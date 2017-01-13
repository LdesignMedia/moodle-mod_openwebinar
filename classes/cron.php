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
        $results = $DB->get_records_sql($sql);
        if ($results) {
            foreach ($results as $result) {
                $file = $this->get_ical_file($result);
                // Check if we need to.
                mtrace(PHP_EOL . $result->name);
                mtrace('Timeopen: ' . date('d-m-Y H:i:s', $result->timeopen) . PHP_EOL);
                $this->reminder_send_invites($result, 1, $file);
                $this->reminder_send_invites($result, 2, $file);
                $this->reminder_send_invites($result, 3, $file);
                $this->reminder_send_invites($result, 4, $file);
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
    protected function reminder_send_invites($openwebinar = false, $number = 0, \stored_file $file) {

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

                // This message needed to be send within 2 hours of the original reminder moment.
                if (time() - $sendtime > (3600 * 2)) {
                    mtrace('We skip sending to late');
                    $obj = new \stdClass();
                    $obj->id = $openwebinar->id;
                    $obj->$remindersend = 1;
                    $DB->update_record('openwebinar', $obj);

                    return;
                }

                mtrace('Send: ' . $remindersend . ' / ' . $remindertime);

                // Get the broadcaster.
                $broadcaster = $DB->get_record('user', array('id' => $openwebinar->broadcaster), '*', MUST_EXIST);

                // Get students in the course.
                $students = helper::get_active_course_users($openwebinar->course);

                // Get url.
                $cm = get_coursemodule_from_instance('openwebinar', $openwebinar->id, $openwebinar->course, false, MUST_EXIST);
                $urlwebinar = new \moodle_url('/mod/openwebinar/view.php', array('id' => $cm->id));

                // Direct login or redirect link
                $urllogin = new \moodle_url('/mod/openwebinar/login.php', [
                        'id' => $cm->id,
                        'url' => base64_encode($urlwebinar->out(false))
                ]);

                foreach ($students as $student) {

                    // Fix issue when mails not in correct language
                    force_current_language($student->lang);

                    $message = get_string('mail:reminder_message', 'openwebinar');
                    $htmlmessage = str_replace(array(
                            '##fullname##',
                            '##firstname##',
                            '##description##',
                            '##starttime##',
                            '##duration##',
                            '##link##',
                            '##name##',
                            '##broadcaster_fullname##',
                    ), array(
                            fullname($student),
                            $student->firstname,
                            (!empty($openwebinar->intro) ? $openwebinar->intro : '-'),
                            date('d-m-Y H:i', $openwebinar->timeopen),
                            round($openwebinar->duration / 60),
                            $urllogin->out(false),
                            $openwebinar->name,
                            fullname($broadcaster)
                    ), $message);

                    $eventdata = new \stdClass();
                    $eventdata->userfrom = \core_user::get_noreply_user();
                    $eventdata->userto = \core_user::get_user($student->id);;
                    $eventdata->subject = get_string('mail:reminder_subject', 'openwebinar', $openwebinar);
                    $eventdata->smallmessage = html_to_text($htmlmessage);
                    $eventdata->fullmessage = html_to_text($htmlmessage);
                    $eventdata->fullmessagehtml = $htmlmessage;
                    $eventdata->fullmessageformat = FORMAT_HTML;
                    $eventdata->name = 'reminder';
                    $eventdata->component = 'mod_openwebinar';
                    $eventdata->notification = 1;
                    $eventdata->contexturl = $urlwebinar->out();
                    $eventdata->contexturlname = $openwebinar->name;
                    $eventdata->attachment = $file;
                    $eventdata->attachname = 'cal.ics';
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

    /**
     * get_ical_file
     *
     * @param $openwebinar
     *
     * @return \stored_file
     */
    protected function get_ical_file(\stdClass $openwebinar) {
        global $DB;

        $broadcaster = $DB->get_record('user', array('id' => $openwebinar->broadcaster), '*', MUST_EXIST);

        $cm = get_coursemodule_from_instance('openwebinar', $openwebinar->id, $openwebinar->course, false,
                MUST_EXIST);
        $fs = get_file_storage();
        $context = \context_module::instance($cm->id);

        // Remove previous.
        $files = $fs->get_area_files($context->id, 'mod_openwebinar', 'cal', $openwebinar->id, "", false);
        foreach ($files as $file) {
            try {
                $file->delete();
            } catch (\Exception $exception) {

            }
        }

        $url = new \moodle_url('/mod/openwebinar/view.php', array('id' => $cm->id));
        $file = $fs->create_file_from_string([
                'contextid' => $context->id,
                'component' => 'mod_openwebinar',
                'filearea' => 'cal',
                'filepath' => '/',
                'filename' => 'cal.ics',
                'itemid' => $openwebinar->id
        ], 'BEGIN:VCALENDAR
VERSION:2.0
ORGANIZER:MAILTO:' . $broadcaster->email . '
PRODID:-//moodlefreack.com/iCal MoodleFreak
X-WR-CALNAME:Test
CALSCALE:GREGORIAN
BEGIN:VTIMEZONE
TZID:Europe/Berlin
TZURL:http://tzurl.org/zoneinfo-outlook/Europe/Berlin
X-LIC-LOCATION:Europe/Berlin
BEGIN:DAYLIGHT
TZOFFSETFROM:+0100
TZOFFSETTO:+0200
TZNAME:CEST
DTSTART:19700329T020000
RRULE:FREQ=YEARLY;BYMONTH=3;BYDAY=-1SU
END:DAYLIGHT
BEGIN:STANDARD
TZOFFSETFROM:+0200
TZOFFSETTO:+0100
TZNAME:CET
DTSTART:19701025T030000
RRULE:FREQ=YEARLY;BYMONTH=10;BYDAY=-1SU
END:STANDARD
END:VTIMEZONE
BEGIN:VEVENT
DTSTAMP:20161128T163615Z
DTSTART;TZID="Europe/Berlin":' . date('Ymd', $openwebinar->timeopen) . 'T' . date("Hi", $openwebinar->timeopen) . '00
DTEND;TZID="Europe/Berlin":' . date('Ymd', $openwebinar->timeopen + $openwebinar->duration) . 'T' .
                date("Hi", $openwebinar->timeopen + $openwebinar->duration) . '00
SUMMARY:' . $openwebinar->name . '
URL:' . $url->out(true) . '
DESCRIPTION:' . preg_replace('/\s+/', ' ', trim(html_to_text($openwebinar->intro, 0))) . '
END:VEVENT
END:VCALENDAR');

        return $file;
    }

    /**
     * Send feedback mail to users that visit the webinar
     */
    public function feedback() {
        global $DB;
        $sql = 'SELECT * FROM {openwebinar} 
                WHERE is_ended = 1
                AND feedback_id > 0
                AND feedback_send = 0';
        $results = $DB->get_records_sql($sql);

        foreach ($results as $openwebinar) {

            $feedback = $DB->get_record('feedback', ['id' => $openwebinar->feedback_id]);

            // Url to feedback
            $cm = get_coursemodule_from_instance('feedback', $feedback->id, $feedback->course, false,
                    MUST_EXIST);

            $cmopenwebinar = get_coursemodule_from_instance('openwebinar', $openwebinar->id, $openwebinar->course, false,
                    MUST_EXIST);

            // Feedback link
            $url = new \moodle_url('/mod/feedback/complete.php', array(
                    'id' => $cm->id,
                    'gopage' => 0
            ));

            // Direct login or redirect link
            $urllogin = new \moodle_url('/mod/openwebinar/login.php', [
                    'id' => $cmopenwebinar->id,
                    'url' => base64_encode($url->out(false))
            ]);

            // Get the broadcaster.
            $broadcaster = $DB->get_record('user', array('id' => $openwebinar->broadcaster), '*', MUST_EXIST);

            // Get all users that visit the webinar.
            $users = $DB->get_records('openwebinar_presence', [
                    'openwebinar_id' => $openwebinar->id,
                    'available' => 1
            ]);

            foreach ($users as $user) {

                $user = \core_user::get_user($user->user_id);

                // Fix issue when mails not in correct language
                force_current_language($user->lang);

                $obj = new \stdClass();
                $obj->name = $openwebinar->name;
                $obj->firstname = $user->firstname;
                $obj->fullname = fullname($user);
                $obj->broadcaster_fullname = fullname($broadcaster);
                $obj->url = $urllogin->out(false);
                $message = get_string('mail:feedback_message', 'openwebinar', $obj);

                $eventdata = new \stdClass();
                $eventdata->userfrom = \core_user::get_noreply_user();
                $eventdata->userto = $user;;
                $eventdata->subject = get_string('mail:feedback_subject', 'openwebinar', $obj);
                $eventdata->smallmessage = html_to_text($message);
                $eventdata->fullmessage = html_to_text($message);
                $eventdata->fullmessagehtml = $message;
                $eventdata->fullmessageformat = FORMAT_HTML;
                $eventdata->name = 'feedback';
                $eventdata->component = 'mod_openwebinar';
                $eventdata->notification = 1;
                $eventdata->contexturl = $url->out();
                $eventdata->contexturlname = $openwebinar->name;
                message_send($eventdata);
            }

            // sended..
            $obj = new \stdClass();
            $obj->id = $openwebinar->id;
            $obj->feedback_send = 1;
            $DB->update_record('openwebinar', $obj);
        }
    }

}