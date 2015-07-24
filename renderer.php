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


    protected function get_chatroom() {

    }


    protected function get_filedropbox() {


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
     */
    public function view_page_live_room($webcast) {

    }

    /**
     * Show a page when the broadcast will be starting
     */
    public function view_page_not_started_room($webcast) {


    }

    /**
     * Show the page with the history content
     */
    public function view_page_history_room($webcast) {


    }


}