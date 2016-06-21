<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * helper
 *
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @package   mod_openwebinar
 * @copyright 2015 MoodleFreak.com
 * @author    Luuk Verhoeven
 **/
namespace mod_openwebinar;

defined('MOODLE_INTERNAL') || die();

class helper {

    /**
     * The broadcast date has passed
     *
     * @const WEBCAST_BROADCASTED
     */
    const WEBCAST_CLOSED = 3;

    /**
     * The broadcast is been marked as ended
     *
     * @const WEBCAST_BROADCASTED
     */
    const WEBCAST_BROADCASTED = 2;

    /**
     * The activity is open for broadcast
     *
     * @const WEBCAST_LIVE
     */
    const WEBCAST_LIVE = 1;

    /**
     * Date is yet to come
     *
     * @const WEBCAST_NOT_BROADCASTED
     */
    const WEBCAST_NOT_BROADCASTED = 0;

    /**
     * get permission of a user
     *
     * @param $context
     * @param \stdClass $openwebinar
     * @param bool $user
     *
     * @return stdClass
     * @throws Exception
     * @throws \coding_exception
     */
    static public function get_permissions($context, $openwebinar, $user = false) {

        global $USER;

        // Get correct user object.
        if (!is_object($user)) {
            $user = $USER;
        }

        if (empty($openwebinar)) {
            throw new Exception(get_string('error:openwebinar_notfound', 'mod_openwebinar'));
        }

        // Build internal caching.
        static $obj = array();
        if (!empty($obj[$user->id])) {
            return $obj[$user->id];
        }

        $access = new \stdClass();

        // Is broadcaster.
        $access->broadcaster = ($user->id == $openwebinar->broadcaster) ? true : false;

        // Is manager.
        $access->manager = has_capability('mod/openwebinar:manager', $context, $user);

        // Is teacher.
        $access->teacher = has_capability('mod/openwebinar:teacher', $context, $user);

        // View history.
        $access->history = has_capability('mod/openwebinar:history', $context, $user);

        // Reference to scope var.
        $obj[$user->id] = $access;

        return $obj[$user->id];
    }

    /**
     * Get the status
     *
     * @param stdClass $openwebinar
     *
     * @return int
     * @throws Exception
     * @throws \coding_exception
     */
    public static function get_openwebinar_status($openwebinar) {

        // Check.
        if (empty($openwebinar)) {
            throw new Exception(get_string('error:openwebinar_notfound', 'mod_openwebinar'));
        }

        $now = time();
        if (!empty($openwebinar->is_ended)) {
            return self::WEBCAST_BROADCASTED;
        } else {
            if ($now >= $openwebinar->timeopen) {
                return self::WEBCAST_LIVE;
            }
        }

        return self::WEBCAST_NOT_BROADCASTED;

    }

    /**
     * get the user type of a user
     *
     * @param bool|false $user
     * @param $permissions
     *
     * @return string
     */
    public static function get_usertype($user = false, $permissions) {

        if ($permissions->broadcaster) {
            return 'broadcaster';
        }

        if ($permissions->teacher) {
            return 'teacher';
        }

        if ($user->id > 1) {
            return 'student';
        }

        return 'guest';
    }

    /**
     * generate unique identifier GUID
     *
     * @return string
     */
    public static function generate_key() {

        if (function_exists('com_create_guid')) {
            // Windows based.
            return com_create_guid();
        }

        // Linux.
        mt_srand((double) microtime() * 10000);
        $charid = strtoupper(md5(uniqid(rand(), true)));

        return substr($charid, 0, 8) . '-' . substr($charid, 8, 4) . '-' . substr($charid, 12, 4) . '-' . substr($charid, 16, 4) .
        '-' . substr($charid, 20, 12);
    }

    /**
     * Save a messages to the database
     * this will be done by the API that is called through the socket server
     *
     * @param bool|false $data
     *
     * @return bool
     */
    public static function save_messages($data = false) {
        global $DB;
        $openwebinar = $DB->get_record('openwebinar', array('broadcastkey' => str_replace('_public', '', $data->broadcastkey)), '*',
                MUST_EXIST);

        $now = time();
        foreach ($data->messages as $message) {

            $message = (object) $message;

            $obj = new \stdClass();
            $obj->userid = (int) $message->userid;
            $obj->fullname = $message->fullname;
            $obj->messagetype = $message->messagetype;
            $obj->usertype = $message->usertype;
            $obj->openwebinar_id = $openwebinar->id;
            $obj->course_id = $openwebinar->course;
            $obj->message = $message->message;
            $obj->timestamp = (int) $message->timestamp;
            $obj->addedon = $now;

            $DB->insert_record('openwebinar_messages', $obj);
        }

        return true;
    }

    /**
     * set_user_online_status in the room
     *
     * @param int $openwebinarid
     *
     * @return int the seconds active
     */
    public static function set_user_online_status($openwebinarid = 0) {
        global $DB, $USER;

        // Guest no status will be saved for unregistered users.
        if ($USER->id <= 1) {
            return 0;
        }

        $object = new \stdClass();

        // Check if record already exists.
        $row = $DB->get_record('openwebinar_userstatus', array('openwebinar_id' => $openwebinarid, 'userid' => $USER->id));

        if (!$row) {
            // Set extra data.
            $object->openwebinar_id = $openwebinarid;
            $object->userid = $USER->id;
            $object->starttime = time();
            $object->timer_seconds = 0;
            $object->endtime = 0;
            $object->useragent = filter_input(INPUT_SERVER, 'HTTP_USER_AGENT', FILTER_SANITIZE_STRING);
            $object->ip_address = filter_input(INPUT_SERVER, 'REMOTE_ADDR', FILTER_VALIDATE_IP);

            $DB->insert_record('openwebinar_userstatus', $object);

            return 0;
        }

        $newtime = $row->timer_seconds + 60;

        $object->id = $row->id;
        $object->timer_seconds = $newtime;
        $DB->update_record('openwebinar_userstatus', $object);

        return $newtime;
    }

    /**
     * get file options
     *
     * @param $context
     *
     * @return array
     */
    static public function get_file_options($context) {
        global $CFG;

        return array(
                'subdirs' => 0,
                'maxfiles' => 50,
                'maxbytes' => $CFG->maxbytes,
                'accepted_types' => '*',
                'context' => $context,
                'return_types' => 2 | 1
        );
    }

    /**
     * get_module_data
     *
     * @param int $courseid
     * @param int $openwebinarid
     *
     * @return array array($course , $openwebinar , $cm , $context)
     * @throws \coding_exception
     */
    static public function get_module_data($courseid = 0, $openwebinarid = 0) {
        global $DB;

        $course = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);
        require_course_login($course);

        // Get the openwebinar.
        $openwebinar = $DB->get_record('openwebinar', array('id' => $openwebinarid), '*', MUST_EXIST);

        // Get course module.
        $cm = get_coursemodule_from_instance('openwebinar', $openwebinar->id, $course->id, false, MUST_EXIST);

        // Get context.
        $context = \context_module::instance($cm->id);

        return array($course, $openwebinar, $cm, $context);
    }

    /**
     * get information about the file for sharing
     *
     * @param \stored_file $file
     * @param \file_storage $fs
     *
     * @return \stdClass
     * @throws \coding_exception
     * @throws \file_exception
     */
    static public function get_file_info(\stored_file $file, \file_storage $fs) {

        global $OUTPUT;

        $item = new \stdClass();
        $item->filename = $file->get_filename();
        $filesize = $file->get_filesize();
        $item->filesize = $filesize ? display_size($filesize) : '';

        $item->author = (string) $file->get_author();
        $item->hash = (string) $file->get_contenthash();
        $item->id = $file->get_id();

        $item->mimetype = get_mimetype_description($file);
        $item->thumbnail = $OUTPUT->pix_url(file_file_icon($file, 90))->out(false);

        return $item;
    }

    /**
     * get_openwebinar_by_broadcastkey
     *
     * @param string $broadcastkey
     *
     * @return mixed
     */
    static public function get_openwebinar_by_broadcastkey($broadcastkey = '') {
        global $DB;

        return $DB->get_record('openwebinar', array('broadcastkey' => str_replace('_public', '', $broadcastkey)), '*', MUST_EXIST);
    }

    /**
     * Get enrolled user in course
     *
     * @param int $courseid
     *
     * @return array
     * @throws \coding_exception
     * @throws \dml_exception
     */
    static public function get_active_course_users($courseid = 0) {

        global $DB;

        list($instancessql, $params) = $DB->get_in_or_equal(array_keys(enrol_get_instances($courseid, false)), SQL_PARAMS_NAMED);

        // Get extra fields.
        $extrafields = get_extra_user_fields(\context_course::instance($courseid));
        $extrafields[] = 'lastaccess';
        $dbfields = \user_picture::fields('u', $extrafields);

        // Params.
        $now = round(time(), -2); // Rounding helps caching in DB.
        $params += array(
                'enabled' => ENROL_INSTANCE_ENABLED,
                'active' => ENROL_USER_ACTIVE,
                'now1' => $now,
                'now2' => $now,
        );

        $sql = 'SELECT DISTINCT ' . $dbfields . '
                FROM {user} u
                      JOIN {user_enrolments} ue ON (ue.userid = u.id  AND ue.enrolid ' . $instancessql . ')
                      JOIN {enrol} e ON (e.id = ue.enrolid)
                 LEFT JOIN {groups_members} gm ON u.id = gm.userid
                 WHERE
                 ue.status = :active AND e.status = :enabled AND ue.timestart < :now1
                    AND (ue.timeend = 0 OR ue.timeend > :now2)';

        return $DB->get_records_sql($sql, $params);
    }

    /**
     * Save the users presence for stats and overviews
     *
     * @param bool|\stdClass $webcast
     * @param bool|\stdClass $user
     *
     * @return bool
     */
    static public function update_user_presence($webcast = false, $user = false) {
        global $DB;

        // We can't update if this is the case.
        if (empty($webcast) || $webcast->is_ended == 1 || !$user) {
            return false;
        }

        // Get users that can enter this webinar.
        $courseusers = self::get_active_course_users($webcast->course);

        // User already added.
        $webcastusers = $DB->get_records('openwebinar_presence',
                ['openwebinar_id' => $webcast->id], '', 'user_id, id , available');

        // Make sure all users are added first.
        foreach ($courseusers as $cuser) {
            if (!isset($webcastusers[$cuser->id])) {

                $obj = new \stdClass();
                // Is this user is active.
                $obj->available = ($cuser->id === $user->id) ? 1 : 0;

                $obj->user_id = $cuser->id;
                $obj->openwebinar_id = $webcast->id;
                $obj->added_on = time();
                $obj->id = $DB->insert_record('openwebinar_presence', $obj);

                $webcastusers[$cuser->id] = $obj;
            }
        }

        // Check if I exists.
        if (!isset($webcastusers[$user->id])) {
            return false;
        }

        // Check my presence is set correctly.
        if ($webcastusers[$user->id]->available !== 1) {
            $obj = new \stdClass();
            $obj->id = $webcastusers[$user->id]->id;
            $obj->available = 1;
            $DB->update_record('openwebinar_presence', $obj);
        }

        return true;
    }

}