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
 * @package   mod_webcast
 * @copyright 2015 MoodleFreak.com
 * @author    Luuk Verhoeven
 **/
namespace mod_webcast;

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
     * @param \stdClass $webcast
     * @param bool $user
     *
     * @return stdClass
     * @throws Exception
     * @throws \coding_exception
     */
    static public function get_permissions($context, $webcast, $user = false) {

        global $USER;

        // get correct user object
        if (!is_object($user)) {
            $user = $USER;
        }

        if (empty($webcast)) {
            throw new Exception(get_string('error:webcast_notfound', 'mod_webcast'));
        }

        // build internal caching
        static $obj = array();
        if (!empty($obj[$user->id])) {
            return $obj[$user->id];
        }

        // echo 'query ';

        $access = new \stdClass();

        // is broadcaster
        $access->broadcaster = ($user->id == $webcast->broadcaster) ? true : false;

        // is manager
        $access->manager = has_capability('mod/webcast:manager', $context, $user);

        // is teacher
        $access->teacher = has_capability('mod/webcast:teacher', $context, $user);

        // can upload
        // @todo make this optional
        $access->upload = true;

        // can chat
        // @todo make this optional
        $access->chat = true;

        // reference to scope var
        $obj[$user->id] = &$access;

        return $obj[$user->id];
    }

    /**
     * Get the status
     *
     * @param stdClass $webcast
     *
     * @return int
     * @throws Exception
     * @throws \coding_exception
     */
    public static function get_webcast_status($webcast) {

        // check 
        if (empty($webcast)) {
            throw new Exception(get_string('error:webcast_notfound', 'mod_webcast'));
        }

        $now = time();

        if (!empty($webcast->is_ended)) {
            return self::WEBCAST_BROADCASTED;
        } elseif ($now >= $webcast->timeopen) {
            return self::WEBCAST_LIVE;
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
            // windows based
            return com_create_guid();
        }

        // Linux
        mt_srand((double)microtime() * 10000);
        $charid = strtoupper(md5(uniqid(rand(), true)));

        return substr($charid, 0, 8) . '-' . substr($charid, 8, 4) . '-' . substr($charid, 12, 4) . '-' . substr($charid, 16, 4) . '-' . substr($charid, 20, 12);
    }

    /**
     * save a  messages to the database
     * this will be done by the API that is called through the socket server
     *
     * @param bool|false $data
     *
     * @return bool
     */
    public static function save_messages($data = false) {
        global $DB;
        $webcast = $DB->get_record('webcast', array('broadcastkey' => str_replace('_public', '', $data->broadcastkey)), '*', MUST_EXIST);

        $now = time();
        foreach ($data->messages as $message) {

            $message = (object)$message;

            $obj = new \stdClass();
            $obj->userid = (int)$message->userid;
            $obj->fullname = $message->fullname;
            $obj->messagetype = $message->messagetype;
            $obj->usertype = $message->usertype;
            $obj->webcast_id = $webcast->id;
            $obj->course_id = $webcast->course;
            $obj->message = $message->message;
            $obj->timestamp = (int)$message->timestamp;
            $obj->addedon = $now;

            $DB->insert_record('webcast_messages', $obj);
        }

        return true;
    }

    /**
     * set_user_online_status in the room
     *
     * @param int $webcastid
     *
     * @return int the seconds active
     */
    public static function set_user_online_status($webcastid = 0) {
        global $DB, $USER;

        //  Guest no status will be saved for unregistered users
        if ($USER->id <= 1) {
            return 0;
        }

        $object = new \stdClass();

        // check if record already exists
        $row = $DB->get_record('webcast_userstatus', array('webcast_id' => $webcastid, 'userid' => $USER->id));

        if (!$row) {
            // set extra data
            $object->webcast_id = $webcastid;
            $object->userid = $USER->id;
            $object->starttime = time();
            $object->timer_seconds = 0;
            $object->endtime = 0;
            $object->useragent = filter_input(INPUT_SERVER, 'HTTP_USER_AGENT', FILTER_SANITIZE_STRING);
            $object->ip_address = filter_input(INPUT_SERVER, 'REMOTE_ADDR', FILTER_VALIDATE_IP);

            $DB->insert_record('webcast_userstatus', $object);

            return 0;
        }

        $newtime = $row->timer_seconds + 60;

        $object->id = $row->id;
        $object->timer_seconds = $newtime;
        $DB->update_record('webcast_userstatus', $object);

        return $newtime;
    }
}