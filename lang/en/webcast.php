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
    <li>Live chat</li>
    <li>Live broadcasting</li>
    <li>Online status log</li>
    <li>Users in room list</li>
    <li>Messages log</li>
    <li>File sharing</li>
    <li>HLS support</li>
    <li>Reminder messages</li>
</ul>';

$string['modulename_link'] = 'mod/webcast/view';
$string['pluginadministration'] = 'MoodleFreak Webcast administration';
$string['pluginname'] = 'MoodleFreak Webcast';
$string['webcastfieldset'] = 'Custom example fieldset';
$string['webcastname'] = 'MoodleFreak Webcast';
$string['webcastname_help'] = 'The name of the webcast';
$string['webcast'] = 'MoodleFreak Webcast';
$string['task:auto_close'] = 'Auto close rooms if they expired and not closed by broadcaster.';

// ACCESS
$string['webcast:history'] = 'Allow viewing webcast history';
$string['webcast:view'] = 'Allow viewing webcast';
$string['webcast:addinstance'] = 'Add a webcast';
$string['webcast:manager'] = 'Webcast manager';
$string['webcast:teacher'] = 'Webcast teacher';

// ERRORS
$string['error:webcast_notfound'] = 'Error: We can\'t get the correct webcast!';
$string['error:time_passed'] = 'Error:  Starttime has already been passed!';
$string['error:file_not_exits'] = 'Error: This file doesn\'t exists or is removed!';
$string['error:file_no_access'] = 'Error: No access to this file!';
$string['error:no_access'] = 'Error: missing capability to do this.';
$string['error:no_result'] = 'No result(s) found';
$string['error:answer_already_saved'] = 'Your answer is already saved!';
$string['error:not_for_guests'] = 'Error: not available for Guests';

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
$string['setting:ajax_timer'] = 'Ajax based timing';
$string['setting:ajax_timer_desc'] = 'This will send every 60 seconds a request to the server to update user his onlinetime. <br/>
Warning with more then 25 users in the room this can give a high server load.';
$string['setting:emoticons'] = 'Emoticons in chat';
$string['setting:emoticons_desc'] = 'Shortcode will be converted to a emoticon. Also a dialog will be added where you can select a emoticon.';
$string['setting:debugjs'] = 'Debug Javascript';
$string['setting:debugjs_desc'] = 'Write js debug message to browser console. ';
$string['setting:hls'] = 'HLS video stream';
$string['setting:hls_desc'] = 'This will add support for mobile devices. Broadcaster also need to stream to server that sends a HLS output. <br/>Warning HLS gives a extra delay of 30 seconds to your stream.';

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
$string['text:live_webcast'] = '<br/>The webcast is open from: <b>{$a->timeopen}</b><br> You can enter the webcast by pressing the button below';
$string['text:broadcaster_help'] = '<h3>You are the broadcaster</h3><br>Broadcastkey:<br><b class="selectable">{$a->broadcastkey}</b><br><br>The broadcast guide can be found <a  target="_blank" href="http://moodlefreak.com/docs/webcast_broadcast_guide_2015_08_20.pdf">here</a>.<br><br>
Make sure you install the streaming software: <a href="https://obsproject.com/" target="_blank">Open Broadcaster Software</a>';
$string['text:history'] = '<h3>Webcast is offline</h3>This webcast was given on <b>{$a->timeopen}</b>. <br/><br/>You can still view the history in this webcast by clicking on the button below.';
$string['text:useractivity'] = 'User activity';

// Buttons
$string['btn:enter_live_webcast'] = 'Enter live webcast';
$string['btn:enter_offline_webcast'] = 'Enter offline webcast';
$string['btn:chattime'] = 'Time in room';
$string['btn:chatlog'] = 'Log';
$string['btn:view'] = 'View';
$string['btn:back'] = 'Back';
$string['btn:addquestion'] = 'Add new question';
$string['btn:open'] = 'Save';

// Helper strings
$string['dateformat'] = 'd-m-Y H:i';
$string['users'] = 'Users';
$string['chat'] = 'Chat';
$string['no'] = 'No';
$string['yes'] = 'Yes';
$string['menu'] = 'Control panel';
$string['live'] = 'Live';
$string['addfile'] = 'Add file';
$string['attachment'] = 'Attachment';
$string['offline'] = 'Offline (ended)';
$string['filemanager'] = 'Uploader';
$string['fileoverview'] = 'Files';
$string['question_overview'] = 'Question';
$string['broadcaster'] = 'Broadcaster';
$string['student'] = 'Student';
$string['guest'] = 'Guest';
$string['teacher'] = 'Teacher';
$string['message_placeholder'] = 'Type a message here....';
$string['user_activity'] = 'Webcast user activity';

// user options
$string['opt:header_broadcaster'] = 'Broadcaster';
$string['opt:header_exit'] = 'Exit';
$string['opt:mute_guests'] = 'Mute guests';
$string['opt:chat_sound'] = 'Chat sound';
$string['opt:stream'] = 'Show stream';
$string['opt:userlist'] = 'Show userlist';
$string['opt:mute_students'] = 'Mute students';
$string['opt:mute_teachers'] = 'Mute teachers';
$string['opt:endwebcast'] = 'Close & end the webcast';
$string['opt:endwebcast_desc'] = 'This will end the live webcast.';
$string['opt:leave'] = 'Leave webcast';
$string['opt:header_general'] = 'General';

// Js
$string['js:send'] = 'Send';
$string['js:answer'] = 'Answer';
$string['js:added_answer'] = '{$a->fullname} added a answer.';
$string['js:ending_webcast'] = 'Are you sure you wan\'t to end and close the webcast?';
$string['js:muted'] = 'The messages are muted by the broadcaster!';
$string['js:wait_on_connection'] = 'Waiting';
$string['js:joined'] = 'Welcome to chatroom.';
$string['js:disconnect'] = 'You are disconnected.';
$string['js:reconnected'] = 'You are reconnected.';
$string['js:script_user'] = 'System message';
$string['js:system_user'] = 'Chat server';
$string['js:connecting'] = 'Connecting to the chat server, please be patient.';
$string['js:warning_message_closing_window'] = 'Are you sure you want to do exit the webcast?';
$string['js:error_logout_or_lostconnection'] = 'Connection lost! Or your session is expired. Please reload the webcast.';
$string['js:dialog_ending_text'] = 'Broadcaster has closed the webcast. You will be redirect to Moodle.';
$string['js:dialog_ending_btn'] = 'Close webcast';
$string['js:ended'] = 'Broadcaster has closed the webcast.';
$string['js:chat_commands'] = '<h4>Error: unknown command</h4>
<p>What are the available chat commands</p>
<b>Users:</b>
<ul class="command">
<li>/clear <span class="note">Empty all messages in your overview</span></li>
</ul>
<br/>
<b>Broadcaster:</b>
<ul class="command">
<li>/send_question_to_all <span class="note">Send a question to the all users. There answers only available for you.</span></li>
</ul>';
$string['js:added_question'] = 'Your question is send to client(s) in the room. You will receive a notice if someone gives an answer.';

// HEADING Tables
$string['heading:picture'] = 'Avatar';
$string['heading:firstname'] = 'Firstname';
$string['heading:lastname'] = 'Lastname';
$string['heading:email'] = 'Email';
$string['heading:present'] = 'Present';
$string['heading:action'] = 'Action';