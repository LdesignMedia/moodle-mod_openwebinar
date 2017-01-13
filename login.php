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
 * Login from e-mail else we are redirecting
 * Prevent strange things from happening and make a simple login form if needed
 *
 *
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @package   mod_openwebinar
 * @copyright 2016 MoodleFreak.com
 * @author    Luuk Verhoeven
 * @var mod_openwebinar_renderer $renderer
 */

require_once("../../config.php");
require_once(dirname(__FILE__) . '/lib.php');

$id = required_param('id', PARAM_INT); // Course_module ID, or
$url = required_param('url', PARAM_RAW);

//$username = optional_param('username', '', PARAM_USERNAME);
//$password = optional_param('password', '', PARAM_RAW);

$cm = get_coursemodule_from_id('openwebinar', $id, 0, false, MUST_EXIST);
$course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
$openwebinar = $DB->get_record('openwebinar', array('id' => $cm->instance), '*', MUST_EXIST);

// Print the page header.
$PAGE->set_url('/mod/openwebinar/login.php', [
        'id' => $cm->id,
        'url' => $url
]);
$PAGE->set_context(context_system::instance());
$PAGE->set_title(format_string($openwebinar->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->add_body_class('moodlefreak-openwebinar');

// check if you are logged..
if (!empty($USER) && $USER->id > 1) {
    $url = base64_decode($url);
    redirect($url);
} else {
    // First we need to show the login window..
    $form = new \mod_openwebinar\form\login($PAGE->url);

    if (($data = $form->get_data()) != false) {

        $data->username = trim(core_text::strtolower($data->username));

        if (($data->username == 'guest') and empty($CFG->guestloginbutton)) {
            $user = false;    /// Can't log in as guest if guest button is disabled
            $frm = false;
        } else {
            if (empty($errormsg)) {
                $user = authenticate_user_login($data->username, $data->password, false, $errorcode);
            }
        }

        if($user){

            /// Let's get them all set up.
            complete_user_login($user);
            set_moodle_cookie($USER->username);

            //user already supplied by aut plugin prelogin hook
            $url = base64_decode($url);
            redirect($url);
        }
    }

    echo $OUTPUT->header();
    echo $form->display();
    echo $OUTPUT->footer();
}

