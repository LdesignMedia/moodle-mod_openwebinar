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


    protected function get_video_player() {

    }


    protected function get_chatwebcast() {

    }


    protected function get_filedropbox() {


    }

    protected function get_userlist() {


    }
    /*
        protected function get_navbar($activename = '', $id = 0) {

            global $PAGE;

            $html = '<ul class="nav nav-tabs" id="matrixnav">';
            $viewbase = '/mod/webcast/';
            $params = array(
                'id' => $id,
            );

            // Menu Items
            $array = array(
                'webcast_view' => html_writer::link(new moodle_url($viewbase . 'view.php', $params), get_string('link:webcast_view', 'mod_webcast')),
                'webcast_history' => html_writer::link(new moodle_url($viewbase . 'history.php', $params), get_string('link:history', 'mod_webcast')),
            );


            // Access level needed
            $access = array(
                'webcast_view' => true,
                'webcast_history' => $ismanager
            );

            foreach ($array as $name => $url) {
                $html .= '<li role="presentation" class="' . (($activename == $name) ? 'active' : '') . '">' . $url . '</li>';
            }
            $html .= '</ul>';

            return $html;
        }
    */

    /**
     * Show the page with all the component
     *
     * @param int $id
     *
     * @return string
     * @throws coding_exception
     */
    public function view_page_live_webcast($id = 0, $webcast) {

        // @todo add popup support
        $popup = false;

        $obj = new stdClass();
        $obj->timeopen = date(get_string('dateformat', 'webcast'), $webcast->timeopen);

        $message = html_writer::tag('p', get_string('text:live_webcast', 'webcast', $obj), array('class' => 'webcast-message'));
        $url = new moodle_url('/mod/webcast/view_webcast.php');

        // extra data
        $output = html_writer::empty_tag('input', array('name' => 'sesskey', 'type' => 'hidden', 'value' => sesskey()));
        $output .= html_writer::empty_tag('input', array('name' => 'id', 'type' => 'hidden', 'value' => $id));
        $output .= html_writer::empty_tag('input', array('name' => 'returnto', 'type' => 'hidden', 'value' => s(me())));

        // button
        $output .= html_writer::tag('div', html_writer::empty_tag('input', array(
            'type' => 'submit',
            'value' => get_string('btn:enter_live_webcast', 'webcast'),
            'id' => 'id_submitbutton',
            'class' => (($popup) ? 'webcast-openpopup' : '')
        )), array('class' => 'buttons'));

        // output
        return $this->output->container($message . html_writer::tag('form', $output, array(
                'action' => $url->out(),
                'method' => 'post'
            )) . '<hr/>', 'generalbox attwidth webcast-center');
    }

    /**
     * Show help info for broadcaster
     *
     * @param $webcast
     *
     * @return string
     */
    public function view_page_broadcaster_help($webcast){
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
     * Show the page with the history content
     */
    public function view_page_history_webcast($webcast) {
        $html = __FUNCTION__ . '<br>';


        return $html;
    }


}