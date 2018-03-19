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
 * Overview of user activity in the openwebinar
 *
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @package   mod_openwebinar
 * @copyright 2015 MoodleFreak.com
 * @author    Luuk Verhoeven
 * @var mod_openwebinar_renderer $renderer
 **/
require_once("../../config.php");
require_once(dirname(__FILE__) . '/lib.php');

$id = optional_param('id', 0, PARAM_INT); // Course_module ID, or
$n = optional_param('n', 0, PARAM_INT);  // ... openwebinar instance ID - it should be named as the first character of the module.

$userid = optional_param('user_id', false, PARAM_INT);
$action = optional_param('action', false, PARAM_TEXT);

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

// Get context.
$context = context_module::instance($cm->id);

// Validate access to this part.
if (!has_capability('mod/openwebinar:manager', $PAGE->cm->context)) {
    print_error(get_string('error:no_access', 'openwebinar'));
}

$PAGE->set_url('/mod/openwebinar/user_mail.php', array('id' => $cm->id));
$PAGE->set_title(format_string($openwebinar->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->add_body_class('moodlefreak-openwebinar');

$renderer = $PAGE->get_renderer('mod_openwebinar');

$url = clone $PAGE->url;
$url->param('action' , 'email_all');


switch ($action) {

    case    'email_single':
        // This is still todo item , only works for DSHOP now.
        $SESSION->bulk_users = [];
        $SESSION->bulk_users[$userid] = $userid;
        redirect('/blocks/dshop/admin/crm/message?blockid=349');
        break;

    case    'email_all':
        // This is still todo item , only works for DSHOP now.

        $users = \mod_openwebinar\helper::get_active_course_users($openwebinar->course);

        $SESSION->bulk_users = [];
        foreach ($users as $user) {
            $SESSION->bulk_users[$user->id] = $user->id;
        }
        redirect('/blocks/dshop/admin/crm/message?blockid=349');

        break;

    default:
        // Output starts here.
        echo $OUTPUT->header();

        echo $OUTPUT->heading(get_string('text:usermail', 'openwebinar'));

        echo '<hr/>';
        // Email all users btn.
        echo html_writer::link($url, get_string('btn:email_all' , 'openwebinar') , [
                'class' => 'btn btn-primary'
        ]);

        $renderer->view_mail_users($openwebinar);

        // Finish the page.
        echo $OUTPUT->footer();
}

