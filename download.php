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
 * @package   mod_webcast
 * @copyright 2015 MoodleFreak.com
 * @author    Luuk Verhoeven
 **/

require_once("../../config.php");

// Extra validation
$sesskey = required_param('sesskey', PARAM_ALPHANUMEXT);

// Parameters for the action
$extra1 = required_param('extra1', PARAM_TEXT);
$extra2 = required_param('extra2', PARAM_TEXT);
$extra3 = required_param('extra3', PARAM_TEXT);

$PAGE->set_url('/mod/webcast/download.php');


// Get module data and validate access
list($course, $webcast, $cm, $context) = \mod_webcast\helper::get_module_data($extra1, $extra2);

// still here check if we can find the file


