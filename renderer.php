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
 * Render class responsible for html parts
 *
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @package   mod_openwebinar
 * @copyright 2015 MoodleFreak.com
 * @author    Luuk Verhoeven
 */
defined('MOODLE_INTERNAL') || die();
require_once($CFG->dirroot . '/mod/openwebinar/lib/uaparser/vendor/autoload.php');
use UAParser\Parser;

/**
 * The renderer for the openwebinar module.
 */
class mod_openwebinar_renderer extends plugin_renderer_base {

    /**
     * Number of rows will be shown foreach page
     *
     * @const int DEFAULT_TABLE_ROW_COUNT
     */
    const DEFAULT_TABLE_ROW_COUNT = 20;

    /**
     * Show the page with all the component
     *
     * @param int $id
     *
     * @param $openwebinar
     *
     * @return string
     * @throws coding_exception
     */
    public function view_page_live_openwebinar($id = 0, $openwebinar) {

        $obj = new stdClass();
        $obj->timeopen = date(get_string('dateformat', 'openwebinar'), $openwebinar->timeopen);

        $message = html_writer::tag('p', get_string('text:live_openwebinar', 'openwebinar', $obj),
                array('class' => 'openwebinar-message'));
        $url = new moodle_url('/mod/openwebinar/view_openwebinar.php', array('id' => $id));

        // Button.
        $output = html_writer::link($url, get_string('btn:enter_live_openwebinar', 'openwebinar'), array(
                'class' => 'btn btn-primary',
                'target' => '_blank'
        ));

        // Output.
        return $this->output->container($message . $output . '<hr/>', 'generalbox attwidth openwebinar-center');
    }

    /**
     * Show help info for broadcaster
     *
     * @param int $id
     * @param \stdClass $openwebinar
     *
     * @return string
     */
    public function view_page_broadcaster_help($id = 0, stdClass $openwebinar) {

        $url = new moodle_url('/mod/openwebinar/view_openwebinar.php', array('id' => $id));
        $link = html_writer::link($url, get_string('btn:broadcast_enter', 'openwebinar'), array(
                'class' => 'btn btn-primary',
                'target' => '_blank'
        ));

        return $this->output->container(html_writer::tag('p',
                get_string('text:broadcaster_help', 'openwebinar', $openwebinar) . '<hr/>' . $link,
                array('class' => 'openwebinar-message')), 'generalbox attwidth openwebinar-center');
    }

    /**
     * Show a page when the broadcast will be starting
     *
     * @param $openwebinar
     *
     * @return string
     */
    public function view_page_not_started_openwebinar($openwebinar) {
        global $PAGE;
        $html = '';
        // Language strings.
        $PAGE->requires->strings_for_js(array(
                'js:countdown_line1',
                'js:countdown_line2',
                'js:countdown_line3',
        ), 'openwebinar');

        $PAGE->requires->js('/mod/openwebinar/javascript/countdown.js');

        // Get cm.
        $cm = get_coursemodule_from_instance('openwebinar', $openwebinar->id, $openwebinar->course, false, MUST_EXIST);

        // Load js for countdown.
        $opts = array();
        $opts['timeopen'] = $openwebinar->timeopen;
        $opts['cmid'] = $cm->id;
        $opts['from'] = time();
        $PAGE->requires->yui_module('moodle-mod_openwebinar-base', 'M.mod_openwebinar.base.init', array($opts));

        // Add id for the countdown.
        $html .= html_writer::tag('h3', get_string('starts_at', 'openwebinar'), array(
                'class' => 'openwebinar-center'
        ));

        $html .= html_writer::tag('h3', '', array(
                'id' => 'pageTimer',
                'class' => 'openwebinar-center'
        ));

        // Show a count down.
        // Reload page when it starts.

        return $html . '<hr/>';
    }

    /**
     * User has access to the history
     *
     * @param int $id
     * @param $openwebinar
     *
     * @return string
     * @throws coding_exception
     */
    public function view_page_history_openwebinar($id = 0, $openwebinar) {

        $obj = new stdClass();
        $obj->timeopen = date(get_string('dateformat', 'openwebinar'), $openwebinar->timeopen);
        $content = html_writer::tag('p', get_string('text:history', 'openwebinar', $obj), array('class' => 'openwebinar-message'));

        // Add link to the room.
        $url = new moodle_url('/mod/openwebinar/view_openwebinar.php', array('id' => $id));
        $content .= html_writer::link($url, get_string('btn:enter_offline_openwebinar', 'openwebinar'), array(
                'class' => 'btn btn-primary',
                'target' => '_blank'
        ));

        return $this->output->container($content, 'generalbox attwidth openwebinar-center');
    }

    /**
     * Openwebinar has ended and user doesn't have access to history
     *
     * @param $openwebinar
     *
     * @return string
     */
    public function view_page_ended_message($openwebinar) {
        $html = '';

        return $html;
    }

    /**
     * get overview of all user activities in given openwebinar
     *
     * @param $openwebinar
     *
     * @return string
     */
    public function view_user_activity_all($openwebinar) {
        global $OUTPUT, $PAGE, $CFG;

        require_once($CFG->libdir . '/tablelib.php');

        $table = new \mod_openwebinar\table\useractivity('outstation-list-table', $openwebinar);
        echo $OUTPUT->heading(get_string('text:useractivity', 'openwebinar'));

        echo '<hr/>';

        $table->set_attribute('cellspacing', '0');
        $table->set_attribute('class', 'admintable generaltable');
        $table->initialbars(true); // Always initial bars.
        $table->define_columns(array(
                'picture',
                'firstname',
                'lastname',
                'email',
                'present',
                'action'
        ));

        $table->define_headers(array(
                get_string('heading:picture', 'openwebinar'),
                get_string('heading:firstname', 'openwebinar'),
                get_string('heading:lastname', 'openwebinar'),
                get_string('heading:email', 'openwebinar'),
                get_string('heading:present', 'openwebinar'),
                get_string('heading:action', 'openwebinar'),
        ));

        $table->no_sorting('action');
        $table->sortable(true, 'name', SORT_DESC);
        $table->define_baseurl(new moodle_url($PAGE->url, $PAGE->url->params()));
        $table->collapsible(false);
        $table->out(self::DEFAULT_TABLE_ROW_COUNT, true);
    }

    /**
     * Load online time of the user
     *
     * @param bool|\stdClass $openwebinar
     * @param int $userid
     *
     * @throws coding_exception
     */
    public function view_user_chattime($openwebinar = false, $userid = 0) {
        global $OUTPUT, $DB, $PAGE;
        $backurl = new \moodle_url('/mod/openwebinar/user_activity.php', $PAGE->url->params());
        $btn = new single_button($backurl, get_string('btn:back', 'openwebinar'));
        $btn->class = 'openwebinar_back';
        echo $this->render($btn);
        echo '<hr>';
        $user = $DB->get_record('user', array('id' => $userid), '*', MUST_EXIST);
        $user->fullname = fullname($user);
        echo $OUTPUT->heading(get_string('heading:chattime', 'openwebinar', $user));

        echo $OUTPUT->box_start('generalbox');

        $status = $DB->get_record('openwebinar_userstatus', array(
                'openwebinar_id' => $openwebinar->id,
                'userid' => $user->id
        ));

        if ($status) {
            // User info.

            // Parse user agent for more readable format.
            $parser = Parser::create();
            $browser = $parser->parse($status->useragent);

            $table = new html_table();
            $table->size = array('120px', '');
            $table->head = array(get_string('heading:name', 'openwebinar'), get_string('heading:value', 'openwebinar'));
            $table->data = array();
            $table->data[] = array(
                    get_string('browser', 'openwebinar'),
                    $browser->toString(),
            );
            $table->data[] = array(
                    get_string('ip_address', 'openwebinar'),
                    $status->ip_address,
            );
            $table->data[] = array('<b>' . get_string('time', 'openwebinar') . '</b>', '');

            $table->data[] = array(get_string('starttime', 'openwebinar'), date('d-m-Y H:i:s', $status->starttime));
            $table->data[] = array(
                    get_string('online_time', 'openwebinar'),
                    ($status->timer_seconds == 0) ? '-' : gmdate("H:i:s", $status->timer_seconds)
            );

            echo html_writer::table($table);
            // Add time table.
        }

        echo $OUTPUT->box_end();
    }

    /**
     * Load chat log of a user
     *
     * @param bool|stdClass $openwebinar
     * @param int $userid
     *
     * @throws coding_exception
     */
    public function view_user_chatlog($openwebinar = false, $userid = 0) {
        global $OUTPUT, $DB, $PAGE;
        $backurl = new \moodle_url('/mod/openwebinar/user_activity.php', $PAGE->url->params());
        $btn = new single_button($backurl, get_string('btn:back', 'openwebinar'));
        $btn->class = 'openwebinar_back';
        echo $this->render($btn);
        echo '<hr>';
        $user = $DB->get_record('user', array('id' => $userid), '*', MUST_EXIST);
        $user->fullname = fullname($user);
        echo $OUTPUT->heading(get_string('heading:chatlog', 'openwebinar', $user));

        echo $OUTPUT->box_start('generalbox');

        $table = new html_table();
        $table->size = array('120px', '');
        $table->head = array(get_string('heading:time', 'openwebinar'), get_string('heading:message', 'openwebinar'));
        $table->data = array();

        $qr = $DB->get_recordset('openwebinar_messages', array(
                'userid' => $userid,
                'openwebinar_id' => $openwebinar->id
        ), 'id ASC');

        foreach ($qr as $record) {
            $table->data[] = array(
                    date('d-m-Y H:i:s', $record->timestamp),
                    $this->convertmessagetoreadable($record->message, $openwebinar)
            );
        }

        $qr->close();
        echo html_writer::table($table);
        echo $OUTPUT->box_end();
    }

    /**
     * Internal parsing message text for log overview
     *
     * @param string $message
     * @param stdClass $openwebinar
     *
     * @return string
     */
    public function convertmessagetoreadable($message = '', stdClass $openwebinar) {
        global $DB;

        if (strpos($message, '[') === 0) {

            $re = "/\\[([\\w\\-_]+)([^\\]]*)?\\](?:(.+?)?\\[\\/\\1\\])?/";
            preg_match_all($re, $message, $matches);

            // Convert shortcode.
            if (!empty($matches[1][0])) {
                $args = !empty($matches[2][0]) ? json_decode($matches[2][0]) : false;
                switch ($matches[1][0]) {

                    case 'answer' :
                        $answer = $DB->get_field('openwebinar_question_answer', 'answer_data',
                                ['id' => $args->answerid]);
                        $question = $DB->get_field('openwebinar_question', 'question_data',
                                ['id' => $args->questionid]);

                        if ($answer) {
                            $message = '';
                            $answer = unserialize($answer);
                            $question = unserialize($question);
                            $message .= \html_writer::tag('h4', $question->question, []);
                            $message .= \html_writer::tag('p', $question->summary, []);
                            $message .= \html_writer::tag('b', $answer->answer, []);
                        }

                        break;
                    case 'file' :
                        $message = '';
                        $message .= \html_writer::tag('h4', $args->filename, []);
                        $message .= html_writer::link(new moodle_url('/mod/openwebinar/download.php', [
                                'extra3' => $args->id,
                                'extra2' => $openwebinar->id,
                                'extra1' => $openwebinar->course,
                                'sesskey' => sesskey(),
                        ]), '<img src="'
                                . $args->thumbnail . '" />');
                        break;
                    case 'question' :
                        $message = '[question]';
                        break;
                    default:
                        $message = '[shortcode unable_to_convert]';
                }
            }
        }

        // TODO: convert emoticons in chatlog.
        return $message;
    }
}