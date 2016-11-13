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
 * Strings for component 'mod_openwebinar', language 'en'
 *
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @package   mod_openwebinar
 * @copyright 2015 MoodleFreak.com
 * @author    Luuk Verhoeven
 **/
$string['modulename'] = 'MoodleFreak Openwebinar';
$string['modulenameplural'] = 'MoodleFreak Openwebinar';
$string['modulename_help'] = 'Openwebinar activity:<br/>
<b>Room</b>
<ul>
	<li>The chatroom is responsive</li>
	<li>Live and offline modus</li>
	<li>Reminder mail</li>
	<li>Measure the time a user is in the chat </li>
	<li>YUI 3 javascript engine for the room</li>
	<li>Activity completion</li>
	<li>Not closed rooms will be ended automatic when the cron is running.</li>
</ul>
<b>Chat</b>
<ul>
	<li>Live socket chat</li>
	<li>Chat history will be saved</li>
	<li>Emoticons are supported</li>
	<li>You can load the history of the chat if you enter to late</li>
	<li>User list , sort on usertype</li>
	<li>User in the room will be shown in the user list</li>
	<li>Sound when there is a new message</li>
	<li>Support for the chat commands:
	<ul>
		<li>/clear Remove the message in the chat window in your browser.</li>
	</ul>
	</li>
</ul>
<b>Broadcaster</b>
<ul>
	<li>Can mute chat message by usertype</li>
	<li>Closing a openwebinar</li>
	<li>Create questions inside the chat</li>
</ul>
<b>Users</b>
<ul>
	<li>Chat room can be configured by each user</li>
	<li>Send messages</li>
	<li>Answer questions</li>
	<li>View the stream if there is one</li>
</ul>
<b>Video</b>
<ul>
	<li>View live and offline streams</li>
	<li>Streaming with RTMP (Real Time Messaging Protocol)</li>
	<li>Stream in HLS (supported by most of the devices) </li>
	<li>Videojs used as video component</li>
	<li>Add offline videos</li>
	<li>Play video in fullscreen</li>
</ul>
<b>File sharing</b>
<ul>
	<li>Share files in chat if you have the permission for it</li>
	<li>A window where you can view all shared files</li>
</ul>
<b>User log</b>
<ul>
	<li>Time a user was available in the openwebinar</li>
	<li>See who viewed the openwebinar</li>
	<li>You can view chat history of a user.</li>
</ul>';

$string['modulename_link'] = 'mod/openwebinar/view';
$string['pluginadministration'] = 'MoodleFreak Openwebinar administration';
$string['pluginname'] = 'MoodleFreak Openwebinar';
$string['openwebinarfieldset'] = 'Custom example fieldset';
$string['openwebinarname'] = 'MoodleFreak Openwebinar';
$string['openwebinarname_help'] = 'The name of the openwebinar';
$string['openwebinar'] = 'MoodleFreak Openwebinar';
$string['task:auto_close'] = 'Auto close rooms if they expired and not closed by broadcaster.';
$string['task:reminder'] = 'Send reminders';
$string['messageprovider:reminder'] = 'Openwebinar reminder notifications';

// ACCESS.
$string['openwebinar:history'] = 'Allow viewing openwebinar history';
$string['openwebinar:view'] = 'Allow viewing openwebinar';
$string['openwebinar:addinstance'] = 'Add an openwebinar';
$string['openwebinar:manager'] = 'Openwebinar manager';
$string['openwebinar:teacher'] = 'Openwebinar teacher';

// ERRORS.
$string['error:openwebinar_notfound'] = 'Error: We can\'t get the correct openwebinar!';
$string['error:file_not_exits'] = 'Error: This file doesn\'t exist or has been removed!';
$string['error:file_no_access'] = 'Error: No access to this file!';
$string['error:no_access'] = 'Error: missing capability to fulfill task.';
$string['error:no_result'] = 'No result(s) found';
$string['error:answer_already_saved'] = 'Your answer has been saved already!';
$string['error:not_for_guests'] = 'Error: not available for Guests';

// SETTINGS.
$string['setting:heading_server'] = 'Communication settings';
$string['setting:heading_instance_features'] = 'Features enabled or disabled (can be overridden in each openwebinar activity)';
$string['setting:streaming_server'] = 'Streaming url';
$string['setting:streaming_server_desc'] = 'The location of your streaming server';
$string['setting:chat_server'] = 'Chat/socket url';
$string['setting:chat_server_desc'] = 'The location of your chat server';
$string['setting:shared_secret'] = 'Shared secret';
$string['setting:shared_secret_desc'] = 'A unique key that is shared with the chat/streaming server';
$string['setting:heading_instance_defaults'] = 'Reminder default values (can be overridden in each openwebinar activity)';
$string['setting:reminder_1'] = 'Reminder 1';
$string['setting:reminder_1_desc'] = 'Notification moment before the start of the openwebinar.<br> Set to 0 to disable the
 notification';
$string['setting:reminder_2'] = 'Reminder 2';
$string['setting:reminder_2_desc'] = 'Notification moment before the start of the openwebinar.<br> Set to 0 to disable the
 notification';
$string['setting:reminder_3'] = 'Reminder 3';
$string['setting:reminder_3_desc'] = 'Notification moment before the start of the openwebinar.<br> Set to 0 to disable the
 notification';
$string['setting:reminder_4'] = 'Reminder 4';
$string['setting:reminder_4_desc'] = 'Notification moment before the start of the openwebinar.<br> Set to 0 to disable the
 notification';
$string['setting:stream'] = 'Streaming enabled';
$string['setting:stream_desc'] = 'If disabled the openwebinar don\'t show a video player';
$string['setting:chat'] = 'Chat enabled';
$string['setting:chat_desc'] = 'If disabled the openwebinar don\'t show a chat room';
$string['setting:filesharing'] = 'Filesharing enabled';
$string['setting:filesharing_desc'] = 'If disabled, sharing of files in the file drop zone not possible.';
$string['setting:filesharing_student'] = 'Allow students to share their files';
$string['setting:filesharing_student_desc'] = 'Allow students to share their files';
$string['setting:showuserpicture'] = 'Show user avatar';
$string['setting:showuserpicture_desc'] = 'Show an avatar of user in the chat';
$string['setting:userlist'] = 'Show userlist';
$string['setting:userlist_desc'] = 'Show active users to the student';
$string['setting:ajax_timer'] = 'Ajax based timing';
$string['setting:ajax_timer_desc'] = 'Every 60 seconds a request will be send to the server to update the user’s online time. <br/>
Warning: more than 25 users in the room can cause a high server load.';
$string['setting:emoticons'] = 'Emoticons in chat';
$string['setting:emoticons_desc'] = 'Shortcode will be converted to a emoticon. Also a dialog will be added where you can select
 a emoticon.';
$string['setting:debugjs'] = 'Debug Javascript';
$string['setting:debugjs_desc'] = 'Write js debug message to browser console. ';
$string['setting:hls'] = 'HLS video stream';
$string['setting:hls_desc'] = 'This will add support for mobile devices. Broadcaster also need to stream to server that sends a
 HLS output. <br/>Warning HLS gives a extra delay of 30 seconds to your stream.';

$string['setting:show_skype_popup'] = 'Show skype profile dialog';

// Mod settings.
$string['mod_setting:settings'] = 'Openwebinar features';
$string['mod_setting:timing'] = 'Openwebinar time';
$string['mod_setting:timeopen'] = 'Start';
$string['mod_setting:timeopenhelp'] = 'Start';
$string['mod_setting:timeopenhelp_help'] = 'Start';
$string['mod_setting:make_a_selection'] = 'Select a user';
$string['mod_setting:broadcaster'] = 'Openwebinar broadcaster';
$string['mod_setting:reminders'] = 'Openwebinar reminder messages';
$string['mod_setting:duration'] = 'Duration';
$string['mod_setting:durationhelp'] = 'Duration';
$string['mod_setting:durationhelp_help'] = 'Duration';
$string['mod_setting:broadcastkey'] = 'Broadcastkey';
$string['mod_setting:broadcastkey_desc'] = '<b>{$a->broadcastkey}</b>';

// Text.
$string['text:live_openwebinar'] = '<br/>The openwebinar is open from: <b>{$a->timeopen}</b><br> You can enter the openwebinar by
 pressing the button below';
$string['text:broadcaster_help'] = '<h3>You are the broadcaster</h3><br>Broadcastkey:<br>
<b class="selectable">{$a->broadcastkey}</b>
<br><br>
Make sure you install the streaming software: <br/><a href="https://obsproject.com/" target="_blank">Open Broadcaster Software</a>';
$string['text:history'] = '<h3>Openwebinar is offline</h3>This openwebinar was given on <b>{$a->timeopen}</b>. <br/><br/>You can
still view the history in this openwebinar by clicking on the button below.';
$string['text:useractivity'] = 'User activity';

// Buttons.
$string['btn:enter_live_openwebinar'] = 'Enter live openwebinar';
$string['btn:enter_offline_openwebinar'] = 'Enter offline openwebinar';
$string['btn:chattime'] = 'Report';
$string['btn:chatlog'] = 'Chatlog';
$string['btn:view'] = 'View';
$string['btn:back'] = 'Back';
$string['btn:addquestion'] = 'Add a new question';
$string['btn:open'] = 'Save';
$string['btn:broadcast_enter'] = 'Enter openwebinar';

// Helper strings.
$string['dateformat'] = 'd-m-Y H:i';
$string['users'] = 'Users';
$string['browser'] = 'Browser';
$string['ip_address'] = 'Ip address';
$string['starttime'] = 'Start time';
$string['online_time'] = 'Time in room';
$string['time'] = 'Time';
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
$string['user_activity'] = 'Openwebinar user activity';
$string['starts_at'] = 'Openwebinar starts in:';
$string['starttime'] = 'start time';
$string['Close'] = 'Close';

// User options.
$string['opt:header_broadcaster'] = 'Broadcaster';
$string['opt:header_exit'] = 'Exit';
$string['opt:mute_guests'] = 'Mute guests';
$string['opt:chat_sound'] = 'Chat sound';
$string['opt:stream'] = 'Show stream';
$string['opt:userlist'] = 'Show userlist';
$string['opt:mute_students'] = 'Mute students';
$string['opt:mute_teachers'] = 'Mute teachers';
$string['opt:endopenwebinar'] = 'Close & end the openwebinar';
$string['opt:leave'] = 'Leave openwebinar';
$string['opt:header_general'] = 'General';

// Js.
$string['js:new_incoming_message'] = 'New message';
$string['js:my_answer_saved'] = 'Your answer has been saved.';
$string['js:send'] = 'Send';
$string['js:answer'] = 'Answer';
$string['js:added_answer'] = '{$a->fullname} added an answer.';
$string['js:ending_openwebinar'] = 'Are you sure you wan\'t to end and close the openwebinar?';
$string['js:muted'] = 'The messages are muted by the broadcaster!';
$string['js:wait_on_connection'] = 'Waiting';
$string['js:joined'] = 'Welcome to the chatroom.';
$string['js:disconnect'] = 'You are disconnected.';
$string['js:reconnected'] = 'You are reconnected.';
$string['js:script_user'] = 'System message';
$string['js:system_user'] = 'Chat server';
$string['js:connecting'] = 'Connecting to the chat server, please be patient.';
$string['js:warning_message_closing_window'] = 'Are you sure you want to exit the openwebinar?';
$string['js:error_logout_or_lostconnection'] = 'Connection lost! Or your session is expired. Please reload the openwebinar.';
$string['js:dialog_ending_text'] = 'Broadcaster has closed the openwebinar. You will be redirect to Moodle.';
$string['js:dialog_ending_btn'] = 'Close openwebinar';
$string['js:ended'] = 'Broadcaster has closed the openwebinar.';
$string['js:chat_commands'] = '<h4>Error: unknown command</h4>
<p>What are the available chat commands</p>
<b>Users:</b>
<ul class="command">
<li>/clear <span class="note">Empty all messages in your overview</span></li>
</ul>';
$string['js:added_question'] = 'Your question has been send to client(s) in the room. You will receive a notice if someone gives
 an answer.';
$string['js:countdown_line1'] = ' millisecond| second| minute| hour| day| week| month| year| decade| century| millennium';
$string['js:countdown_line2'] = ' milliseconds| seconds| minutes| hours| days| weeks| months| years| decades| centuries| millennia';
$string['js:countdown_line3'] = ' and ';
$string['js:new_incoming_message'] = 'New message';
// HEADING Tables.
$string['heading:picture'] = 'Avatar';
$string['heading:firstname'] = 'Firstname';
$string['heading:lastname'] = 'Lastname';
$string['heading:email'] = 'Email';
$string['heading:present'] = 'Present';
$string['heading:action'] = 'Action';
$string['heading:chatlog'] = 'Chatlog: {$a->fullname}';
$string['heading:chattime'] = 'Report: {$a->fullname}';
$string['heading:time'] = 'Time';
$string['heading:message'] = 'Message';
$string['heading:name'] = 'Name';
$string['heading:value'] = 'Value';

// Email.
$string['mail:reminder_subject'] = 'Openwebinar reminder: {$a->name}';
$string['mail:reminder_message'] = 'Dear ##fullname##, <br/><br/>

Openwebinar Reminder for:<br/><br/>

<table cellpadding="0" cellspacing="0" border="0">
    <tr>
        <td style="width: 200px"><b>Activity:</b></td>
        <td>##name##</td>
    </tr>
    <tr>
        <td><b>Start date:</b></td>
        <td>##starttime##</td>
    </tr>
    <tr>
        <td><b>Estimate duration:</b></td>
        <td>##duration## Minutes</td>
    </tr>
    <tr>
        <td><b>Link</b></td>
        <td><a href="##link##">Enter openwebinar</a> </td>
    </tr>
</table>
<br/>
Kind regards,<br/>

##broadcaster_fullname##';

$string['fullname'] = 'Fullname';
$string['skype'] = 'Skype';
$string['load_history'] = 'Load previous messages';