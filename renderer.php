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
 * @package   mod_webcast
 * @copyright 2015 MoodleFreak.com
 * @author    Luuk Verhoeven
 */
defined('MOODLE_INTERNAL') || die();
require_once $CFG->dirroot . '/mod/webcast/lib/uaparser/vendor/autoload.php';
use UAParser\Parser;

/**
 * The renderer for the webcast module.
 */
class mod_webcast_renderer extends plugin_renderer_base {

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
     * @return string
     * @throws coding_exception
     */
    public function view_page_live_webcast($id = 0, $webcast) {

        $obj = new stdClass();
        $obj->timeopen = date(get_string('dateformat', 'webcast'), $webcast->timeopen);

        $message = html_writer::tag('p', get_string('text:live_webcast', 'webcast', $obj), array('class' => 'webcast-message'));
        $url = new moodle_url('/mod/webcast/view_webcast.php', array('id' => $id));


        // button
        $output = html_writer::empty_tag('input', array('name' => 'id', 'type' => 'hidden', 'value' => $id));
        $output .= html_writer::tag('div', html_writer::empty_tag('input', array(
            'type' => 'submit',
            'value' => get_string('btn:enter_live_webcast', 'webcast'),
            'id' => 'id_submitbutton',
        )), array('class' => 'buttons'));

        // output
        return $this->output->container($message . html_writer::tag('form', $output, array(
                'action' => $url->out(),
                'method' => 'get'
            )) . '<hr/>', 'generalbox attwidth webcast-center');
    }

    /**
     * Show help info for broadcaster
     *
     * @param $webcast
     *
     * @return string
     */
    public function view_page_broadcaster_help($webcast) {
        return $this->output->container(html_writer::tag('p', get_string('text:broadcaster_help', 'webcast', $webcast), array('class' => 'webcast-message')), 'generalbox attwidth webcast-center');
    }

    /**
     * Show a page when the broadcast will be starting
     */
    public function view_page_not_started_webcast($webcast) {
        $html = __FUNCTION__ . '<br>';


        return $html;
    }

    /**
     * User has access to the history
     *
     * @param int $id
     * @param $webcast
     *
     * @return string
     * @throws coding_exception
     */
    public function view_page_history_webcast($id = 0, $webcast) {

        $obj = new stdClass();
        $obj->timeopen = date(get_string('dateformat', 'webcast'), $webcast->timeopen);
        $content = html_writer::tag('p', get_string('text:history', 'webcast', $obj), array('class' => 'webcast-message'));

        // add link to the room
        $url = new moodle_url('/mod/webcast/view_webcast.php', array('id' => $id));

        // extra data
        $output = html_writer::empty_tag('input', array('name' => 'id', 'type' => 'hidden', 'value' => $id));
        $output .= html_writer::tag('div', html_writer::empty_tag('input', array(
            'type' => 'submit',
            'value' => get_string('btn:enter_offline_webcast', 'webcast'),
            'id' => 'id_submitbutton',
        )));
        ///
        $content .= html_writer::tag('form', $output, array(
            'class' => 'buttons',
            'action' => $url->out(),
            'method' => 'get'
        ));

        return $this->output->container($content, 'generalbox attwidth webcast-center');
    }

    /**
     * Webcast has ended and user doesn't have access to history
     *
     * @param $webcast
     *
     * @return string
     */
    public function view_page_ended_message($webcast) {
        $html = '';

        return $html;
    }

    /**
     * get overview of all user activities in given webcast
     *
     * @param $webcast
     *
     * @return string
     */
    public function view_user_activity_all($webcast) {
        global $OUTPUT, $PAGE, $CFG, $DB;

        require_once($CFG->libdir . '/tablelib.php');

        $table = new \mod_webcast\table\useractivity('outstation-list-table', $webcast);
        echo $OUTPUT->heading(get_string('text:useractivity', 'webcast'));

        echo '<hr/>';

        $table->set_attribute('cellspacing', '0');
        $table->set_attribute('class', 'admintable generaltable');
        $table->initialbars(true); // always initial bars
        $table->define_columns(array(
            'picture',
            'firstname',
            'lastname',
            'email',
            'present',
            'action'
        ));

        $table->define_headers(array(
            get_string('heading:picture', 'webcast'),
            get_string('heading:firstname', 'webcast'),
            get_string('heading:lastname', 'webcast'),
            get_string('heading:email', 'webcast'),
            get_string('heading:present', 'webcast'),
            get_string('heading:action', 'webcast'),
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
     * @param object|false $webcast
     * @param int $userid
     */
    public function view_user_chattime($webcast = false, $userid = 0) {
        global $OUTPUT, $DB, $PAGE, $CFG;
        $backurl = new \moodle_url('/mod/webcast/user_activity.php', $PAGE->url->params());
        $btn = new single_button($backurl, get_string('btn:back', 'webcast'));
        $btn->class = 'webcast_back';
        echo $this->render($btn);
        echo '<hr>';
        $user = $DB->get_record('user', array('id' => $userid), '*', MUST_EXIST);
        $user->fullname = fullname($user);
        echo $OUTPUT->heading(get_string('heading:chattime', 'webcast', $user));

        echo $OUTPUT->box_start('generalbox');

        $status = $DB->get_record('webcast_userstatus', array('webcast_id' => $webcast->id, 'userid' => $user->id));

        if ($status) {
            // user info

            // Parse user agent for more readable format
            $parser = Parser::create();
            $browser = $parser->parse($status->useragent);

            $table = new html_table();
            $table->size = array('120px', '');
            $table->head = array(get_string('heading:name', 'webcast'), get_string('heading:value', 'webcast'));
            $table->data = array();
            $table->data[] = array(
                get_string('browser', 'webcast'),
                $browser->toString(),
            );
            $table->data[] = array(
                get_string('ip_address', 'webcast'),
                $status->ip_address,
            );
            $table->data[] = array('<b>' . get_string('time', 'webcast') . '</b>' , '');

            $table->data[] = array(get_string('starttime', 'webcast'), date('d-m-Y H:i:s' , $status->starttime));
            $table->data[] = array(get_string('online_time', 'webcast'),  ($status->timer_seconds == 0) ? '-' : gmdate("H:i:s", $status->timer_seconds));
            $table->data[] = array(get_string('endtime', 'webcast'), ($status->endtime == 0) ? '-' : date('d-m-Y H:i:s' , $status->endtime));

            echo html_writer::table($table);

            // add time table

        }

        echo $OUTPUT->box_end();
    }

    /**
     * Load chat log of a user
     *
     * @param object|false $webcast
     * @param int $userid
     */
    public function view_user_chatlog($webcast = false, $userid = 0) {
        global $OUTPUT, $DB, $PAGE;
        $backurl = new \moodle_url('/mod/webcast/user_activity.php', $PAGE->url->params());
        $btn = new single_button($backurl, get_string('btn:back', 'webcast'));
        $btn->class = 'webcast_back';
        echo $this->render($btn);
        echo '<hr>';
        $user = $DB->get_record('user', array('id' => $userid), '*', MUST_EXIST);
        $user->fullname = fullname($user);
        echo $OUTPUT->heading(get_string('heading:chatlog', 'webcast', $user));

        echo $OUTPUT->box_start('generalbox');

        $table = new html_table();
        $table->size = array('120px', '');
        $table->head = array(get_string('heading:time', 'webcast'), get_string('heading:message', 'webcast'));
        $table->data = array();

        $qr = $DB->get_recordset('webcast_messages', array(
            'userid' => $userid,
            'webcast_id' => $webcast->id
        ), 'id ASC');

        foreach ($qr as $record) {
            $table->data[] = array(
                date('d-m-Y H:i:s', $record->timestamp),
                $this->convertMessageToReadable($record->message)
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
     *
     * @return string
     */
    protected function convertMessageToReadable($message = '') {
        if (strpos($message, '[') === 0) {
            $message = '[shortcode]';
        }
        //@todo convert emoticons in chatlog
        return $message;
    }
}