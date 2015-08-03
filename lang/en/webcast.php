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
 * Version information
 *
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @package   mod_webcast
 * @copyright 2015 MoodleFreak.com
 * @author    Luuk Verhoeven
 **/
$string['modulename'] = 'MoodleFreak Webcast';
$string['modulenameplural'] = 'MoodleFreak Webcast';
$string['modulename_help'] = 'Webcast activity:<br/>
<ul>
    <li>Chat</li>
    <li>Live Broadcasting</li>
    <li>Active log users</li>
    <li>File sharing</li>
    <li>Reminder messages</li>
    <li>Completion based on viewed</li>
</ul>
';
$string['modulename_link'] = 'mod/webcast/view';
$string['pluginadministration'] = 'MoodleFreak Webcast administration';
$string['pluginname'] = 'MoodleFreak Webcast';
$string['webcastfieldset'] = 'Custom example fieldset';
$string['webcastname'] = 'MoodleFreak Webcast';
$string['webcastname_help'] = 'The name of the webcast';
$string['webcast'] = 'MoodleFreak Webcast';

// ACCESS
$string['webcast:history'] = 'Allow viewing webcast history';
$string['webcast:view'] = 'Allow viewing webcast';
$string['webcast:addinstance'] = 'Add a webcast';
$string['webcast:manager'] = 'Webcast manager';
$string['webcast:teacher'] = 'Webcast teacher';

// ERRORS
$string['error:webcast_notfound'] = 'Error: We can\'t get the correct webcast!';
$string['error:time_passed'] = 'Error:  Starttime has already been passed!';

// SETTINGS
$string['setting:heading_server'] = 'Communication settings';
$string['setting:heading_instance_features'] = 'Features enabled or disabled (can be override in each webcast activity)';
$string['setting:streaming_server'] = 'Streaming url';
$string['setting:streaming_server_desc'] = 'The location of your streaming server';
$string['setting:chat_server'] = 'Chat/socket url';
$string['setting:chat_server_desc'] = 'The location of your chat server';
$string['setting:shared_secret'] = 'Shared secret';
$string['setting:shared_secret_desc'] = 'A unique key that is shared with the chat/streaming server';
$string['setting:heading_instance_defaults'] = 'Reminder default values (can be override in each webcast activity)';
$string['setting:reminder_1'] = 'Reminder 1';
$string['setting:reminder_1_desc'] = 'Notification moment before the start of the webcast.<br> Set to 0 to disable the notification';
$string['setting:reminder_2'] = 'Reminder 2';
$string['setting:reminder_2_desc'] = 'Notification moment before the start of the webcast.<br> Set to 0 to disable the notification';
$string['setting:reminder_3'] = 'Reminder 1';
$string['setting:reminder_3_desc'] = 'Notification moment before the start of the webcast.<br> Set to 0 to disable the notification';
$string['setting:stream'] = 'Streaming enabled';
$string['setting:stream_desc'] = 'If disabled the webcast don\'t shows a video player';
$string['setting:chat'] = 'Chat enabled';
$string['setting:chat_desc'] = 'If disabled the webcast don\'t shows a chat room';
$string['setting:filesharing'] = 'Filesharing enabled';
$string['setting:filesharing_desc'] = 'If disabled nobody can share files in the file drop zone.';
$string['setting:filesharing_student'] = 'Allow student filesharing';
$string['setting:filesharing_student_desc'] = 'Allow students to share there files';
$string['setting:showuserpicture'] = 'Show user avatar';
$string['setting:showuserpicture_desc'] = 'Show a avatar of user in the chat';
$string['setting:userlist'] = 'Show userlist';
$string['setting:userlist_desc'] = 'Show active users to the student';

// Mod settings
$string['mod_setting:settings'] = 'Webcast features';
$string['mod_setting:timing'] = 'Webcast time';
$string['mod_setting:timeopen'] = 'Start';
$string['mod_setting:timeopenhelp'] = 'Start';
$string['mod_setting:timeopenhelp_help'] = 'Start';

$string['mod_setting:make_a_selection'] = 'Select a user';
$string['mod_setting:broadcaster'] = 'Webcast broadcaster';
$string['mod_setting:reminders'] = 'Webcast reminder messages';

$string['mod_setting:duration'] = 'Duration';
$string['mod_setting:durationhelp'] = 'Duration';
$string['mod_setting:durationhelp_help'] = 'Duration';
$string['mod_setting:broadcastkey'] = 'Broadcastkey';
$string['mod_setting:broadcastkey_desc'] = '<b>{$a->broadcastkey}</b>';

// Text
$string['text:live_webcast'] = 'The webcast is open from: <b>{$a->timeopen}</b><br> You can enter the webcast by pressing the button below';
$string['text:broadcaster_help'] = '<h3>You are the broadcaster</h3><br>Broadcastkey: <b>{$a->broadcastkey}</b><br><br>The broadcast guide can be found <a href="">here</a>.<br>
Make sure you install the streaming software: <a href="https://obsproject.com/" target="_blank">Open Broadcaster Software</a>';
// Buttons
$string['btn:enter_live_webcast'] = 'Enter webcast';

// Helper strings
$string['dateformat'] = 'd-m-Y H:i';
$string['users'] = 'Users';
$string['chat'] = 'Chat';
$string['menu'] = 'Menu';
$string['broadcaster'] = 'Broadcaster';
$string['student'] = 'Student';
$string['guest'] = 'Guest';
$string['Teacher'] = 'Teacher';
$string['message_placeholder'] = 'Type a message here....';

// Js
$string['js:send'] = 'Send';
$string['js:wait_on_connection'] = 'Waiting';
$string['js:joined'] = 'Welcome to chatroom.';
$string['js:disconnect'] = 'You are disconnected.';
$string['js:reconnected'] = 'You are reconnected.';
$string['js:script_user'] = 'System message';
$string['js:system_user'] = 'Chat server';
$string['js:connecting'] = 'Connecting to the chat server, please be patient.';
$string['js:warning_message_closing_window'] = 'Are you sure you want to do exit the webcast?';