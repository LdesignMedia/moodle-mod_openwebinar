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
 * API to handle post backs
 *
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @package   mod_webcast
 * @copyright 2015 MoodleFreak.com
 * @author    Luuk Verhoeven
 **/
if (!defined('AJAX_SCRIPT')) {
    define('AJAX_SCRIPT', true);
}
define('NO_DEBUG_DISPLAY', true);

// Strange issue
if (!defined('FILE_REFERENCE')) {
    define('FILE_REFERENCE', 4);
}

require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');

// Action we are performing
$action = optional_param('action', false, PARAM_ALPHANUMEXT);

// Extra validation
$sesskey = optional_param('sesskey', false, PARAM_RAW);

// Parameters for the action
$extra1 = optional_param('extra1', false, PARAM_TEXT);
$extra2 = optional_param('extra2', false, PARAM_TEXT);

// Load plugin config
$config = get_config('webcast');

$PAGE->set_url('/mod/webcast/api.php');

// Response holder
$response = array('error' => "", 'status' => false);

if ($action == 'chatlog') {
    $data = (object)json_decode(file_get_contents('php://input'), true);

    // validate its a valid request
    if (!empty($data->shared_secret) && $config->shared_secret == $data->shared_secret) {
        $status = \mod_webcast\helper::save_messages($data);
        if ($status) {
            $response['status'] = true;
        } else {
            $response['error'] = 'failed saving';
        }
    } else {
        $response['error'] = 'wrong shared_secret';
    }
} elseif ($action == 'load_public_history' && confirm_sesskey($sesskey)) {

    // Get module data and validate access
    list($course, $webcast, $cm, $context) = \mod_webcast\helper::get_module_data($extra1, $extra2);

    // load the messages
    $response['messages'] = $DB->get_records('webcast_messages', array('webcast_id' => $webcast->id), 'timestamp ASC');
    $response['status'] = true;
} elseif ($action == 'ping' && confirm_sesskey($sesskey)) {

    // Check if user can enter the course
    $course = $DB->get_record('course', array('id' => $extra1), '*', MUST_EXIST);
    require_course_login($course);

    // get the webcast
    $webcast = $DB->get_record('webcast', array('id' => $extra2), '*', MUST_EXIST);

    $response['online_minutes'] = \mod_webcast\helper::set_user_online_status($webcast->id);
    $response['status'] = true;

} elseif ($action == 'add_file' && confirm_sesskey($sesskey)) {

    // Get module data and validate access
    list($course, $webcast, $cm, $context) = \mod_webcast\helper::get_module_data($extra1, $extra2);

    // we need to finalize this submit
    $data = new stdClass();
    $data->files_filemanager = required_param('files_filemanager', PARAM_INT);
    $data = file_postupdate_standard_filemanager($data, 'files', \mod_webcast\helper::get_file_options($context), $context, 'mod_webcast', 'attachments', $data->files_filemanager);

    $response['status'] = true;
    $response['itemid'] = $data->files_filemanager;

    // get files we submit
    $fs = get_file_storage();

    $files = $DB->get_records('files' , array('contextid' => $context->id, 'userid' => $USER->id, 'itemid' => $data->files_filemanager , 'component' => 'mod_webcast' , 'filearea' => 'attachments'));
    foreach ($files as $file) {

        $file = $fs->get_file_by_id($file->id);

        if($file && $file->get_filename() !== '.'){

            $fileobj = new stdClass();
            $fileobj->filename = $file->get_filename();
            $fileobj->author = $file->get_author();
            $fileobj->userid = $file->get_userid();
            $fileobj->fileid = $file->get_id();
            $fileobj->hash = $file->get_contenthash();
            $fileobj->filesize = $file->get_filesize();
            $fileobj->timecreated = $file->get_timecreated();
            $fileobj->mimetype = $file->get_mimetype();

            $response['files'][] = $fileobj;
        }
    }

} else if ($action == 'list_all_files' && confirm_sesskey($sesskey)) {

    // Get module data and validate access
    list($course, $webcast, $cm, $context) = \mod_webcast\helper::get_module_data($extra1, $extra2);

    $fs = get_file_storage();
    $files = $fs->get_area_files($context->id, 'mod_webcast', 'attachments');

    foreach ($files as $f) {

        $file = new stdClass();
        $file->filename = $f->get_filename();
        $file->author = $f->get_author();
        $file->userid = $f->get_userid();
        $file->fileid = $f->get_id();

        $response['files'][] = $file;
    }
}

// Send headers.
echo $OUTPUT->header();
echo json_encode($response);
