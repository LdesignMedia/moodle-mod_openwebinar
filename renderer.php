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

/**
 * The renderer for the webcast module.
 */
class mod_webcast_renderer extends plugin_renderer_base {


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
     */
    public function view_page_ended_message($webcast) {

    }
}