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
 * @package   mod_openwebinar
 * @copyright 2015 MoodleFreak.com
 * @author    Luuk Verhoeven
 **/
namespace mod_openwebinar;
defined('MOODLE_INTERNAL') || die();

class cron {

    /**
     * Room open max duration
     *
     * @const MAX_DURATION
     */
    const MAX_DURATION = 28800; // 8 hours.

    /**
     * Debug
     *
     * @var bool
     */
    protected $debug = false;

    /**
     * @return boolean
     */
    public function is_debug() {
        return $this->debug;
    }

    /**
     * @param boolean $debug
     *
     * @return cron
     */
    public function set_debug($debug) {
        $this->debug = (bool) $debug;

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
        $openwebinars = $DB->get_records('openwebinar', array('is_ended' => 0));
        if ($openwebinars) {
            foreach ($openwebinars as $openwebinar) {

                // We must end this openwebinar.
                if ($now > $openwebinar->timeopen + self::MAX_DURATION) {

                    // Set to closed.
                    $obj = new \stdClass();
                    $obj->id = $openwebinar->id;
                    $obj->is_ended = 1;
                    $DB->update_record('openwebinar', $obj);

                    mtrace('Closed -> ' . $openwebinar->name);
                }
            }
        }
    }

    /**
     * Check if we need to send reminders
     */
    public function reminder() {
        global $DB;
        // Get all openwebinar that aren't started.
        mtrace('Check if we need send reminders');
        mtrace('Now: ' . date('d-m-Y H:i:s'));
        $sql = 'SELECT * FROM {openwebinar} WHERE is_ended = 0';
        $results = $DB->get_records_sql($sql, array('now' => time()));
        if ($results) {
            foreach ($results as $result) {
                // Check if we need to.
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
     * @param bool|false $openwebinar
     * @param int $number
     */
    protected function reminder_send_invites($openwebinar = false, $number = 0) {

        global $DB;
        $remindersend = 'reminder_' . $number . '_send';
        $remindertime = 'reminder_' . $number;

        // Skip there is no time set.
        if (empty($openwebinar->$remindertime)) {
            return;
        }

        if ($openwebinar->$remindersend == 0) {

            $sendtime = $openwebinar->timeopen - $openwebinar->$remindertime;
            mtrace('Step ' . $number . ' send on: ' . date('d-m-Y H:i:s', $sendtime));

            if ($sendtime <= time()) {
                mtrace('Send: ' . $remindersend . ' / ' . $remindertime);

                // Get the broadcaster.
                $broadcaster = $DB->get_record('user', array('id' => $openwebinar->broadcaster), '*', MUST_EXIST);

                // Get students in the course.
                $students = helper::get_active_course_users($openwebinar->course);
                $message = get_string('mail:reminder_message', 'openwebinar');

                // Get url.
                $cm = get_coursemodule_from_instance('openwebinar', $openwebinar->id, $openwebinar->course, false, MUST_EXIST);
                $url = new \moodle_url('/mod/openwebinar/view.php', array('id' => $cm->id));

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
                            date('d-m-Y H:i', $openwebinar->timeopen),
                            round($openwebinar->duration / 60),
                            $url,
                            $openwebinar->name,
                            fullname($broadcaster)
                    ), $message);

                    $eventdata = new \stdClass();
                    $eventdata->userfrom = \core_user::get_noreply_user();
                    $eventdata->userto = $student;
                    $eventdata->subject = get_string('mail:reminder_subject', 'openwebinar', $openwebinar);
                    $eventdata->smallmessage = html_to_text($htmlmessage);
                    $eventdata->fullmessage = html_to_text($htmlmessage);
                    $eventdata->fullmessagehtml = $htmlmessage;
                    $eventdata->fullmessageformat = FORMAT_HTML;

                    $eventdata->name = 'reminder';
                    $eventdata->component = 'mod_openwebinar';
                    $eventdata->notification = 1;
                    $eventdata->contexturl = $url->out();
                    $eventdata->contexturlname = $openwebinar->name;
                    message_send($eventdata);
                }

                // Save to DB to prevent sending again.
                $obj = new \stdClass();
                $obj->id = $openwebinar->id;
                $obj->$remindersend = 1;
                $DB->update_record('openwebinar', $obj);

            } else {
                mtrace('....');
            }
        }
    }

}