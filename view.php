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
 * Prints a particular instance of webcast
 *
 * You can have a rather longer description of the file as well,
 * if you like, and it can span multiple lines.
 *
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @package   mod_webcast
 * @copyright 2015 MoodleFreak.com
 * @author    Luuk Verhoeven
 */

require_once("../../config.php");
require_once(dirname(__FILE__) . '/lib.php');

$id = optional_param('id', 0, PARAM_INT); // Course_module ID, or
$n = optional_param('n', 0, PARAM_INT);  // ... webcast instance ID - it should be named as the first character of the module.

if ($id) {
    $cm = get_coursemodule_from_id('webcast', $id, 0, false, MUST_EXIST);
    $course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
    $webcast = $DB->get_record('webcast', array('id' => $cm->instance), '*', MUST_EXIST);
} else if ($n) {
    $webcast = $DB->get_record('webcast', array('id' => $n), '*', MUST_EXIST);
    $course = $DB->get_record('course', array('id' => $webcast->course), '*', MUST_EXIST);
    $cm = get_coursemodule_from_instance('webcast', $webcast->id, $course->id, false, MUST_EXIST);
} else {
    error('You must specify a course_module ID or an instance ID');
}

require_login($course, true, $cm);

$event = \mod_webcast\event\course_module_viewed::create(array(
    'objectid' => $PAGE->cm->instance,
    'context' => $PAGE->context,
));
$event->add_record_snapshot('course', $PAGE->course);
$event->add_record_snapshot($PAGE->cm->modname, $webcast);
$event->trigger();

// Print the page header.
$PAGE->set_url('/mod/webcast/view.php', array('id' => $cm->id));
$PAGE->set_title(format_string($webcast->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->add_body_class('moodlefreak-webcast');

// Convert webcast data to JS
$opts = (array)$webcast;
unset($opts['intro'], $opts['broadcastkey']);

// Load JS base
$PAGE->requires->yui_module('moodle-mod_webcast-base', 'M.mod_webcast.base.init', array($opts));

// Permissions
$permissions = \mod_webcast\helper::get_permissions($PAGE->context, $webcast);

// Renderer
$renderer = $PAGE->get_renderer('mod_webcast');

// Get status
$status = \mod_webcast\helper::get_webcast_status($webcast);

// Output starts here.
echo $OUTPUT->header();

// Conditions to show the intro can change to look for own settings or whatever.
// if ($webcast->intro) {
// echo $OUTPUT->box(format_module_intro('webcast', $webcast, $cm->id), 'generalbox mod_introbox', 'webcastintro');
//}

echo $OUTPUT->heading(format_string($webcast->name), 1, 'webcast-center');

// echo \mod_webcast\helper::generate_key();

/**
 * $completion=new completion_info($course);
 * $completion->set_module_viewed($cm);
 */
switch ($status) {

    case \mod_webcast\helper::WEBCAST_LIVE:
        echo $renderer->view_page_live_webcast($id, $webcast);

        if ($webcast->broadcaster == $USER->id) {
            echo $renderer->view_page_broadcaster_help($webcast);
        }
        break;

    case \mod_webcast\helper::WEBCAST_CLOSED:
    case \mod_webcast\helper::WEBCAST_BROADCASTED:

        if ($permissions->history) {
            echo $renderer->view_page_history_webcast($webcast);
        } else {
            echo $renderer->view_page_ended_message($webcast);
        }
        break;


    default:
        echo $renderer->view_page_not_started_webcast($webcast);

        if ($webcast->broadcaster == $USER->id) {
            echo $renderer->view_page_broadcaster_help($webcast);
        }
        break;

}

// Finish the page.
echo $OUTPUT->footer();