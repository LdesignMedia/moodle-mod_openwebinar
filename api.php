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

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');

$action = optional_param('action' , false , PARAM_ALPHANUMEXT);
$config = get_config('webcast');

$PAGE->set_url('/mod/webcast/api.php');

// Response holder
$response = array('error' => "", 'status' => false);

// Send headers.
echo $OUTPUT->header();

if($action == 'chatlog'){
    $data = (object) json_decode(file_get_contents('php://input'), true);

    // validate its a valid request
    if(!empty($data->shared_secret) && $config->shared_secret == $data->shared_secret){
        $status = \mod_webcast\helper::save_messages($data);
        if($status){
            $response['status'] = true;
        }else{
            $response['error'] = 'failed saving';
        }
    }else{
        $response['error'] = 'wrong shared_secret';
    }
}

echo json_encode($response);
