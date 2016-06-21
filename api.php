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
 * @package   mod_openwebinar
 * @copyright 2015 MoodleFreak.com
 * @author    Luuk Verhoeven
 **/
if (!defined('AJAX_SCRIPT')) {
    define('AJAX_SCRIPT', true);
}
define('NO_DEBUG_DISPLAY', true);

// Strange issue.
if (!defined('FILE_REFERENCE')) {
    define('FILE_REFERENCE', 4);
}

require_once("../../config.php");

// Action we are performing.
$action = optional_param('action', false, PARAM_ALPHANUMEXT);
$action = 'api_call_' . $action;

// Extra validation.
$sesskey = optional_param('sesskey', false, PARAM_RAW);

// Parameters for the action.
$extra1 = optional_param('extra1', false, PARAM_TEXT);
$extra2 = optional_param('extra2', false, PARAM_TEXT);

// Load plugin config.
$config = get_config('openwebinar');

$PAGE->set_url('/mod/openwebinar/api.php');

// Load the class.
$api = new \mod_openwebinar\api();
$api->set_sesskey($sesskey);
$api->set_extra1($extra1);
$api->set_extra2($extra2);

if (is_callable(array($api, $action))) {
    $api->$action();
} else {
    throw new Exception("not_callable:" . $action);
}
