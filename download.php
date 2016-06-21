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
 * Download file
 *
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @package   mod_openwebinar
 * @copyright 2015 MoodleFreak.com
 * @author    Luuk Verhoeven
 **/

require_once("../../config.php");

// Extra validation.
$sesskey = required_param('sesskey', PARAM_ALPHANUMEXT);

// Parameters for the action.
$extra1 = required_param('extra1', PARAM_TEXT);
$extra2 = required_param('extra2', PARAM_TEXT);
$extra3 = required_param('extra3', PARAM_INT);

$PAGE->set_url('/mod/openwebinar/download.php');

// Get module data and validate access.
list($course, $openwebinar, $cm, $context) = \mod_openwebinar\helper::get_module_data($extra1, $extra2);

// Still here check if we can find the file.
$fs = get_file_storage();
$file = $fs->get_file_by_id($extra3);

if ($file) {
    
    // Make sure it belongs to a openwebinar.
    if ($file->get_component() != 'mod_openwebinar' ||
            $file->get_filearea() != 'attachments' ||
            $file->get_contextid() != $context->id
    ) {

        throw new Exception(get_string('error:file_no_access', 'mod_openwebinar'));
    }

    // We can now send the file back to the browser - in this case with a cache lifetime of 1 day and no filtering.
    send_stored_file($file, 86400, 0, true);

} else {
    throw new Exception(get_string('error:file_not_exits', 'mod_openwebinar'));
}
