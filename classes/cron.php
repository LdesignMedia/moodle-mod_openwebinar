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
 * cron
 *
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @package   mod_webcast
 * @copyright 2015 MoodleFreak.com
 * @author    Luuk Verhoeven
 **/
namespace mod_webcast;
defined('MOODLE_INTERNAL') || die();

final class cron {

    /**
     * Room open max duration
     *
     * @const MAX_DURATION
     */
    const MAX_DURATION = 28800; // 8 hours

    /**
     * Debug
     *
     * @var bool
     */
    protected $debug = false;

    function __construct() {

    }

    /**
     * c
     *
     * @return boolean
     */
    public function isDebug() {
        return $this->debug;
    }

    /**
     * @param boolean $debug
     *
     * @return cron
     */
    public function setDebug($debug) {
        $this->debug = (bool)$debug;

        return $this;
    }

    /**
     * close unclosed rooms
     *
     * @return void
     */
    public function auto_close() {
        global $DB;
        $now = time();
        $webcasts = $DB->get_records('webcast', array('is_ended' => 0));
        if ($webcasts) {
            foreach ($webcasts as $webcast) {

                // we must end this webcast
                if ($now > $webcast->timeopen + self::MAX_DURATION) {

                    // set to closed
                    $obj = new \stdClass();
                    $obj->id = $webcast->id;
                    $obj->is_ended = 1;
                    $DB->update_record('webcast', $obj);

                    mtrace('Closed -> ' . $webcast->name);
                }
            }
        }
    }

    /**
     * Check if we need to send reminders
     */
    public function reminder() {
        global $DB;
        // get all webcast that aren't started
        mtrace('Check if we need send reminders');
        mtrace('Now: ' . date('d-m-Y H:i:s'));
        $sql = 'SELECT * FROM {webcast} WHERE timeopen > :now';
        $results = $DB->get_records_sql($sql, array('now' => time()));
        if ($results) {
            foreach ($results as $result) {
                // check if we need to
                mtrace(PHP_EOL . $result->name);
                mtrace('Timeopen: ' . date('d-m-Y H:i:s', $result->timeopen) . PHP_EOL);
                $this->reminder_send_invites($result, 1);
                $this->reminder_send_invites($result, 2);
                $this->reminder_send_invites($result, 3);
                mtrace(' ');
            }
        }
    }

    /**
     * Send a reminder
     *
     * @param bool|false $webcast
     * @param int $number
     */
    protected function reminder_send_invites($webcast = false, $number = 0) {

        global $DB;
        $remindersend = 'reminder_' . $number . '_send';
        $remindertime = 'reminder_' . $number;

        // skip there is no time set
        if (empty($webcast->$remindertime)) {
            return;
        }

        if ($webcast->$remindersend == 0) {

            $sendtime = $webcast->timeopen - $webcast->$remindertime;
            mtrace('Step ' . $number . ' send on: ' . date('d-m-Y H:i:s', $sendtime));

            if ($sendtime <= time()) {
                mtrace('Send: ' . $remindersend . ' / ' . $remindertime);

                // get the broadcaster
                $broadcaster = $DB->get_record('user', array('id' => $webcast->broadcaster), '*', MUST_EXIST);

                // get students in the course
                $students = helper::get_active_course_users($webcast->course);

                // get message
//                $message = $DB->get_record('webcast_tpl', array(
//                    'webcast_id' => $webcast->id,
//                    'name' => 'reminder'
//                ), '*', IGNORE_MULTIPLE);
//                if (!$message) {
                    // get the global message if there is no tpl
                    $message = get_string('mail:reminder_message', 'webcast');
//                }

                // get url
                $cm = get_coursemodule_from_instance('webcast', $webcast->id, $webcast->course, false, MUST_EXIST);
                $url = new \moodle_url('/mod/webcast/view.php', array('id' => $cm->id));

                foreach ($students as $student) {

                    $htmlmessage = str_replace(array(
                        '##fullname##',
                        '##starttime##',
                        '##duration##',
                        '##link##',
                        '##name##',
                        '##broadcaster_fullname##',
                    ), array(
                        fullname($student),
                        date('d-m-Y H:i', $webcast->timeopen),
                        round($webcast->duration / 60),
                        $url,
                        $webcast->name,
                        fullname($broadcaster)
                    ), $message);

                    $eventdata = new \stdClass();
                    $eventdata->userfrom = $broadcaster;
                    $eventdata->userto = $student;
                    $eventdata->subject = get_string('mail:reminder_subject', 'webcast', $webcast);
                    $eventdata->smallmessage = html_to_text($htmlmessage);
                    $eventdata->fullmessage = html_to_text($htmlmessage);
                    $eventdata->fullmessagehtml = $htmlmessage;
                    $eventdata->fullmessageformat = FORMAT_HTML;

                    $eventdata->name = 'reminder';
                    $eventdata->component = 'mod_webcast';
                    $eventdata->notification = 1;
                    $eventdata->contexturl = $url->out();
                    $eventdata->contexturlname = $webcast->name;
                    message_send($eventdata);
                }

                // save to DB to prevent sending again
                $obj = new \stdClass();
                $obj->id = $webcast->id;
                $obj->$remindersend = 1;
                $DB->update_record('webcast', $obj);

            } else {
                mtrace('....');
            }
        }
    }

}