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
 * Overview of user activity in the webcast
 *
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @package   mod_webcast
 * @copyright 2015 MoodleFreak.com
 * @author    Luuk Verhoeven
 **/
require_once("../../config.php");
require_once(dirname(__FILE__) . '/lib.php');

$id = optional_param('id', 0, PARAM_INT); // Course_module ID, or
$n = optional_param('n', 0, PARAM_INT);  // ... webcast instance ID - it should be named as the first character of the module.

$userid = optional_param('userid', false, PARAM_INT);
$action = optional_param('action', false, PARAM_TEXT);

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

// get context
$context = context_module::instance($cm->id);

// validate access to this part
if (!has_capability('mod/webcast:manager', $PAGE->cm->context)) {
    error(get_string('error:no_access', 'webcast'));
}

$PAGE->set_url('/mod/webcast/user_activity.php', array('id' => $cm->id));
$PAGE->set_title(format_string($webcast->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->add_body_class('moodlefreak-webcast');

/**
 * Renderer
 *
 * @var mod_webcast_renderer $renderer
 */
$renderer = $PAGE->get_renderer('mod_webcast');

// Output starts here.
echo $OUTPUT->header();

switch ($action) {

    case 'user_chattime':
        $renderer->view_user_chattime($webcast, $userid);
        break;

    case 'user_chatlog':
        $renderer->view_user_chatlog($webcast, $userid);
        break;

    default:
        $renderer->view_user_activity_all($webcast);
}

// Finish the page.
echo $OUTPUT->footer();