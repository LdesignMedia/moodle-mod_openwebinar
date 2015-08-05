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
 * If webcast is available this will be the page you load
 *
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @package   mod_webcast
 * @copyright 2015 MoodleFreak.com
 * @author    Luuk Verhoeven
 */

require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once(dirname(__FILE__) . '/lib.php');

$id = optional_param('id', 0, PARAM_INT); // Course_module ID, or
$n = optional_param('n', 0, PARAM_INT);  // ... webcast instance ID - it should be named as the first character of the module.

if ($id) {
    $cm = get_coursemodule_from_id('webcast', $id, 0, false, MUST_EXIST);
    $course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
    $webcast = $DB->get_record('webcast', array('id' => $cm->instance), '*', MUST_EXIST);
} else if ($n) {
    $webcast = $DB->get_record('webcast', array('id' => $n), '*', MUST_EXIST);
    $course = $DB->get_record('course', array('id' => $webcast->course), '*', MUST_EXIST);
    $cm = get_coursemodule_from_instance('webcast', $webcast->id, $course->id, false, MUST_EXIST);
} else {
    error('You must specify a course_module ID or an instance ID');
}

require_login($course, true, $cm);

$event = \mod_webcast\event\course_module_viewed::create(array(
    'objectid' => $PAGE->cm->instance,
    'context' => $PAGE->context,
));
$event->add_record_snapshot('course', $PAGE->course);
$event->add_record_snapshot($PAGE->cm->modname, $webcast);
$event->trigger();

// Print the page header.
$PAGE->set_url('/mod/webcast/view_webcast.php', array('id' => $cm->id));
$PAGE->set_title(format_string($webcast->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->add_body_class('moodlefreak-webcast-room');
$PAGE->set_pagelayout('embedded');

// Load plugin config
$config = get_config('webcast');

// Permissions
$permissions = \mod_webcast\helper::get_permissions($PAGE->context, $webcast);

// Convert webcast data to JS
$opts = (array)$webcast;
$opts['userid'] = $USER->id;

// Set booleans
$opts['userlist'] = ($webcast->userlist == 1);
$opts['filesharing'] = ($webcast->filesharing == 1);
$opts['stream'] = ($webcast->stream == 1);
$opts['showuserpicture'] = ($webcast->showuserpicture == 1);
$opts['chat'] = ($webcast->chat == 1);

$opts['fullname'] = fullname($USER);
$opts['cmid'] = $cm->id;
$opts['courseid'] = $course->id;
$opts['webcastid'] = $webcast->id;
$opts['streaming_server'] = $config->streaming_server;
$opts['chat_server'] = $config->chat_server;
$opts['shared_secret'] = $config->shared_secret;
$opts['usertype'] = \mod_webcast\helper::get_usertype($USER, $permissions);
$opts['ajax_path'] = $CFG->wwwroot . '/mod/webcast/api.php';
$opts['sesskey'] =  sesskey();
unset($opts['intro']);

// Load JS base

// VIDEO Player
$PAGE->requires->js('/mod/webcast/javascript/video-js/video.js', true);

// @todo Add config for this HLS mode
//$PAGE->requires->js('/mod/webcast/javascript/video-js/videojs-media-sources.js', true);
//$PAGE->requires->js('/mod/webcast/javascript/video-js/videojs.hls.min.js', true);

$PAGE->requires->css('/mod/webcast/javascript/video-js/video-js.min.css');

// Emoticons
$PAGE->requires->css('/mod/webcast/stylesheet/emoticons.css');

// Custom scrollbar
$PAGE->requires->js('/mod/webcast/javascript/tinyscrollbar.min.js', true);

// Socket.io script
$PAGE->requires->js('/mod/webcast/javascript/socket.io-1.3.5.js', true);

//
$PAGE->requires->yui_module('moodle-mod_webcast-room', 'M.mod_webcast.room.init', array($opts));

// Language strings
$PAGE->requires->string_for_js('js:send', 'webcast');
$PAGE->requires->string_for_js('js:wait_on_connection', 'webcast');
$PAGE->requires->string_for_js('js:joined', 'webcast');
$PAGE->requires->string_for_js('js:connecting', 'webcast');
$PAGE->requires->string_for_js('js:disconnect', 'webcast');
$PAGE->requires->string_for_js('js:reconnected', 'webcast');
$PAGE->requires->string_for_js('js:script_user', 'webcast');
$PAGE->requires->string_for_js('js:system_user', 'webcast');
$PAGE->requires->string_for_js('js:warning_message_closing_window', 'webcast');

// Renderer
$renderer = $PAGE->get_renderer('mod_webcast');

// Output starts here.
echo $OUTPUT->header();
?>
    <div id="webcast-holder" class="noSelect">
        <section id="webcast-topbar">
            <div id="webcast-topbar-left">
                <div id="webcast-menu">
                    <span class="arrow">&#x25BA;</span>
                    <?php echo get_string('menu', 'webcast') ?>
                </div>
            </div>
            <div id="webcast-topbar-right">
                <?php if ($opts['showuserpicture']): ?>
                    <img src="<?php echo $CFG->wwwroot ?>/user/pix.php?file=/<?php echo $USER->id ?>/f1.jpg"/>
                <?php endif ?>
                <span class="fullname"><?php echo fullname($USER) ?> </span>
                <span class="usertype"><?php echo get_string($opts['usertype'], 'webcast') ?></span>
            </div>
        </section>
        <section id="webcast-left-menu" style="display: none">
        </section>
        <section id="webcast-left">
            <div id="webcast-stream-holder"></div>
            <header>
                <h1><?php echo format_string($webcast->name) ?>
                    <small><?php echo format_string($course->fullname) ?></small>
                </h1>
            </header>
            <div id="webcast-fileshare-holder"></div>
        </section>
        <section id="webcast-right">
            <div id="webcast-userlist-holder">
                <div class="webcast-header">
                    <h2><?php echo get_string('users', 'webcast') ?> <span id="webcast-usercounter">(0)</span></h2>
                </div>
                <div id="webcast-userlist" class="scroll">
                    <div class="scrollbar">
                        <div class="track">
                            <div class="thumb">
                                <div class="end"></div>
                            </div>
                        </div>
                    </div>
                    <div class="viewport">
                        <div class="overview">
                            <ul>
                                <!-- Holder -->
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div id="webcast-chat-holder">
                <div class="webcast-header">
                    <span id="webcast-loadhistory" class="webcast-hidden">Load previous messages</span>
                    <h2><?php echo get_string('chat', 'webcast') ?></h2>
                </div>
                <div id="webcast-chatlist" class="scroll">
                    <div class="scrollbar">
                        <div class="track">
                            <div class="thumb">
                                <div class="end"></div>
                            </div>
                        </div>
                    </div>
                    <div class="viewport">
                        <div class="overview">
                            <ul>
                                <!-- Holder -->
                            </ul>
                        </div>
                    </div>
                </div>
                <div id="webcast-chatinput">
                    <div class="webcast-emoticons-dialoge"></div>
                    <?php if ($opts['filesharing']): ?>
                        <span id="webcast-filemanager-btn"><?php echo get_string('filemanager', 'webcast') ?></span>
                    <?php endif ?>
                    <span id="webcast-emoticon-icon"></span>
                    <input autocomplete="off" type="text" disabled placeholder="<?php echo get_string('message_placeholder', 'webcast') ?>" name="message" id="webcast-message"/>
                    <span id="webcast-send"><?php echo get_string('js:wait_on_connection', 'webcast') ?></span>
                </div>
            </div>
        </section>
    </div>
<?php
// Finish the page.
echo $OUTPUT->footer();