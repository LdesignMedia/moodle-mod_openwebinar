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
 * Prints a particular instance of openwebinar
 *
 * You can have a rather longer description of the file as well,
 * if you like, and it can span multiple lines.
 *
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @package   mod_openwebinar
 * @copyright 2015 MoodleFreak.com
 * @author    Luuk Verhoeven
 * @var mod_openwebinar_renderer $renderer
 */

require_once("../../config.php");
require_once(dirname(__FILE__) . '/lib.php');

$id = optional_param('id', 0, PARAM_INT); // Course_module ID, or
$n = optional_param('n', 0, PARAM_INT);  // ... openwebinar instance ID - it should be named as the first character of the module.

if ($id) {
    $cm = get_coursemodule_from_id('openwebinar', $id, 0, false, MUST_EXIST);
    $course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
    $openwebinar = $DB->get_record('openwebinar', array('id' => $cm->instance), '*', MUST_EXIST);
} else {
    if ($n) {
        $openwebinar = $DB->get_record('openwebinar', array('id' => $n), '*', MUST_EXIST);
        $course = $DB->get_record('course', array('id' => $openwebinar->course), '*', MUST_EXIST);
        $cm = get_coursemodule_from_instance('openwebinar', $openwebinar->id, $course->id, false, MUST_EXIST);
    } else {
        error('You must specify a course_module ID or an instance ID');
    }
}

require_login($course, true, $cm);

// Print the page header.
$PAGE->set_url('/mod/openwebinar/view.php', array('id' => $cm->id));
$PAGE->set_title(format_string($openwebinar->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->add_body_class('moodlefreak-openwebinar');

// Convert openwebinar data to JS.
$opts = (array) $openwebinar;
unset($opts['intro'], $opts['broadcastkey']);

// Permissions.
$permissions = \mod_openwebinar\helper::get_permissions($PAGE->context, $openwebinar);

$renderer = $PAGE->get_renderer('mod_openwebinar');

// Get status.
$status = \mod_openwebinar\helper::get_openwebinar_status($openwebinar);

// Output starts here.
echo $OUTPUT->header();

// Conditions to show the intro can change to look for own settings or whatever.
echo $OUTPUT->heading(format_string($openwebinar->name), 1, 'openwebinar-center');

switch ($status) {

    case \mod_openwebinar\helper::WEBCAST_LIVE:
        echo $renderer->view_page_live_openwebinar($id, $openwebinar);

        if ($openwebinar->broadcaster == $USER->id) {
            echo $renderer->view_page_broadcaster_help($id, $openwebinar);
        }
        break;

    case \mod_openwebinar\helper::WEBCAST_CLOSED:
    case \mod_openwebinar\helper::WEBCAST_BROADCASTED:

        if ($permissions->history) {
            echo $renderer->view_page_history_openwebinar($id, $openwebinar);
        } else {
            echo $renderer->view_page_ended_message($openwebinar);
        }
        break;

    default:
        // Load JS base.
        $PAGE->requires->yui_module('moodle-mod_openwebinar-base', 'M.mod_openwebinar.base.init', array($opts));
        echo $renderer->view_page_not_started_openwebinar($openwebinar);

        if ($openwebinar->broadcaster == $USER->id) {
            echo $renderer->view_page_broadcaster_help($id, $openwebinar);
        }
        break;

}

// Finish the page.
echo $OUTPUT->footer();