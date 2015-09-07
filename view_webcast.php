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
require_once("../../config.php");
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

// get context
$context = context_module::instance($cm->id);

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

// Set user online status
\mod_webcast\helper::set_user_online_status($webcast->id);

// Convert webcast data to JS
$opts = (array)$webcast;
$opts['userid'] = $USER->id;
$opts['is_broadcaster'] = $permissions->broadcaster;

// Set booleans  / convert int to bool
$opts['userlist'] = ($webcast->userlist == 1);
$opts['filesharing'] = ($webcast->filesharing == 1);
$opts['filesharing_student'] = ($webcast->filesharing_student == 1);
$opts['stream'] = ($webcast->stream == 1);
$opts['showuserpicture'] = ($webcast->showuserpicture == 1);
$opts['chat'] = ($webcast->chat == 1);
$opts['is_ended'] = ($webcast->is_ended == 1);
$opts['hls'] = ($webcast->hls == 1);
$opts['ajax_timer'] = ($webcast->ajax_timer == 1);
$opts['emoticons'] = ($webcast->emoticons == 1);
$opts['viewhistory'] = $permissions->history;
$opts['questions'] = true; // @todo make this optional to use the question manager

$opts['fullname'] = fullname($USER);
$opts['debugjs'] = ($config->debugjs == 1);
$opts['cmid'] = $cm->id;
$opts['courseid'] = $course->id;
$opts['webcastid'] = $webcast->id;
$opts['streaming_server'] = $config->streaming_server;
$opts['chat_server'] = $config->chat_server;
$opts['shared_secret'] = $config->shared_secret;
$opts['usertype'] = \mod_webcast\helper::get_usertype($USER, $permissions);
$opts['ajax_path'] = $CFG->wwwroot . '/mod/webcast/api.php';
unset($opts['intro']);

if (!$opts['is_broadcaster']) {
    unset($opts['broadcaster_identifier']);
}

// VIDEO Player
$PAGE->requires->js('/mod/webcast/javascript/video-js/video.js', true);

if ($opts['hls']) {
    // Only needed for fully support HLS
    $PAGE->requires->js('/mod/webcast/javascript/video-js/videojs-media-sources.js', true);
    $PAGE->requires->js('/mod/webcast/javascript/video-js/videojs.hls.min.js', true);
}
// Base videoJS to accept the rtmp stream
$PAGE->requires->css('/mod/webcast/javascript/video-js/video-js.min.css');

// Emoticons
$PAGE->requires->css('/mod/webcast/stylesheet/emoticons.css');

// Custom scrollbar
$PAGE->requires->js('/mod/webcast/javascript/tinyscrollbar.min.js', true);

// Socket.io script
$PAGE->requires->js('/mod/webcast/javascript/socket.io-1.3.5.js', true);

// Room js, most of logic is here
$PAGE->requires->yui_module('moodle-mod_webcast-room', 'M.mod_webcast.room.init', array($opts));

// Language strings
$PAGE->requires->strings_for_js(array(
    'js:send',
    'js:wait_on_connection',
    'js:joined',
    'js:connecting',
    'js:disconnect',
    'js:reconnected',
    'js:script_user',
    'js:system_user',
    'js:warning_message_closing_window',
    'js:error_logout_or_lostconnection',
    'js:ending_webcast',
    'js:dialog_ending_text',
    'js:dialog_ending_btn',
    'js:ended',
    'js:chat_commands',
    'js:added_question',
    'btn:view',
    'js:muted',
    'js:answer',
    'js:added_answer',
    'js:my_answer_saved',
), 'webcast');

/**
 * Renderer
 *
 * @var mod_webcast_renderer $renderer
 */
$renderer = $PAGE->get_renderer('mod_webcast');

if (($opts['filesharing'] && $permissions->broadcaster || $opts['filesharing_student']) && $USER->id > 1) {
    $form = new \mod_webcast\formfilemanager("", array('context' => $context));
    $data = new stdClass();
    file_prepare_standard_filemanager($data, 'files', \mod_webcast\helper::get_file_options($context), $context, 'mod_webcast', 'attachments');
}

// still here? we should mark it for course completion if possible
$completion = new completion_info($COURSE);
if ($completion->is_enabled($cm)) {
    $completion->set_module_viewed($cm);
    $completion->update_state($cm, COMPLETION_COMPLETE);
}

// Output starts here.
echo $OUTPUT->header();
?>
    <div id="webcast-loading"></div>
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
            <ul>
                <li class="header"><?php echo get_string('opt:header_general', 'webcast') ?></li>
                <ul>
                    <li>
                        <div class="question">
                            <?php echo get_string('opt:stream', 'webcast') ?>
                        </div>
                        <div class="switch">
                            <input id="stream" class="webcast-toggle" type="checkbox" checked>
                            <label for="stream"></label>
                        </div>
                    </li>
                    <?php if ($opts['userlist'] && !$opts['is_ended']): ?>
                        <li>
                            <div class="question">
                                <?php echo get_string('opt:userlist', 'webcast') ?>
                            </div>
                            <div class="switch">
                                <input id="userlist" class="webcast-toggle" type="checkbox" checked>
                                <label for="userlist"></label>
                            </div>
                        </li>
                    <?php endif ?>
                    <?php if (!$opts['is_ended']): ?>
                        <li>
                            <div class="question">
                                <?php echo get_string('opt:chat_sound', 'webcast') ?>
                            </div>
                            <div class="switch">
                                <input id="sound" class="webcast-toggle" type="checkbox" checked>
                                <label for="sound"></label>
                            </div>
                        </li>
                    <?php endif; ?>
                </ul>
                <?php if ($permissions->broadcaster && !$opts['is_ended']): ?>
                    <li class="header"><?php echo get_string('opt:header_broadcaster', 'webcast') ?></li>
                    <ul>
                        <li class="text">
                            <?php echo get_string('text:broadcaster_help', 'webcast', $webcast) ?>
                        </li>
                        <li>
                            <div class="question">
                                <?php echo get_string('opt:mute_guests', 'webcast') ?>
                            </div>
                            <div class="switch">
                                <input id="mute_guest" class="webcast-toggle" type="checkbox" checked>
                                <label for="mute_guest"></label>
                            </div>
                        </li>
                        <li>
                            <div class="question">
                                <?php echo get_string('opt:mute_students', 'webcast') ?>
                            </div>
                            <div class="switch">
                                <input id="mute_student" class="webcast-toggle" type="checkbox">
                                <label for="mute_student"></label>
                            </div>
                        </li>
                        <li>
                            <div class="question">
                                <?php echo get_string('opt:mute_teachers', 'webcast') ?>
                            </div>
                            <div class="switch">
                                <input id="mute_teacher" class="webcast-toggle" type="checkbox">
                                <label for="mute_teacher"></label>
                            </div>
                        </li>
                        <li>
                            <p><?php echo get_string('opt:endwebcast_desc', 'webcast') ?></p>
                            <span class="webcast-button red" id="webcast-leave"><?php echo get_string('opt:endwebcast', 'webcast') ?></span>
                        </li>
                    </ul>
                <?php else: ?>
                    <li class="header"><?php echo get_string('opt:header_exit', 'webcast') ?></li>
                    <ul>
                        <li>
                            <span class="webcast-button red" id="webcast-leave"><?php echo get_string('opt:leave', 'webcast') ?></span>
                        </li>
                    </ul>
                <?php endif ?>
            </ul>
        </section>
        <section id="webcast-left">
            <div id="webcast-stream-holder"></div>
            <header>
                <?php if ($webcast->is_ended == 0): ?>
                    <span id="webcast-status" class="online"><?php echo get_string('live', 'webcast') ?></span>
                <?php else: ?>
                    <span id="webcast-status" class="offline"><?php echo get_string('offline', 'webcast') ?></span>
                <?php endif ?>
                <h1><?php echo format_string($webcast->name) ?>
                    <small><?php echo format_string($course->fullname) ?></small>
                </h1>
            </header>
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
                    <span id="webcast-loadhistory" class="webcast-button" style="display: none">Load previous messages</span>
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
                    <div id="webcast-noticebar" style="display: none">
                        alert message here
                    </div>
                    <div id="webcast-fileoverview-dialog" class="webcast-dialog" style="display: none">
                        <header>
                            <span>Close</span>
                            <span class="webcast-close-sign">X</span>
                        </header>
                        <div id="webcast-fileoverview" class="scroll">
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
                    <div id="webcast-filemanager-dialog" class="webcast-dialog" style="display: none">
                        <header>
                            <span>Close</span>
                            <span class="webcast-close-sign">X</span>
                        </header>
                        <?php if (!empty($form)): ?>
                            <?php echo $form->render() ?>
                            <span id="add-file-btn" class="webcast-button"><?php echo get_string('addfile', 'webcast') ?></span>
                        <?php endif ?>
                    </div>
                    <div id="webcast-emoticons-dialog" class="webcast-dialog" style="display: none">
                        <header>
                            <span>Close</span>
                            <span class="webcast-close-sign">X</span>
                        </header>
                        <div id="emoticons-overview">
                            <!-- Holder -->
                        </div>
                    </div>
                    <div id="webcast-toolbar">
                        <?php if ($opts['filesharing']): ?>
                            <span id="webcast-filemanager-btn"><?php echo get_string('filemanager', 'webcast') ?></span>
                            <span id="webcast-fileoverview-btn"><?php echo get_string('fileoverview', 'webcast') ?></span>
                        <?php endif ?>
                        <?php if ($opts['questions']): ?>
                            <span id="webcast-viewquestion-btn"><?php echo get_string('question_overview', 'webcast') ?></span>
                        <?php endif ?>
                    </div>
                    <span id="webcast-emoticon-icon"></span>
                    <input autocomplete="off" type="text" disabled placeholder="<?php echo get_string('message_placeholder', 'webcast') ?>" name="message" id="webcast-message"/>
                    <span id="webcast-send"><?php echo get_string('js:wait_on_connection', 'webcast') ?></span>
                </div>
            </div>
        </section>
    </div>
    <div id="webcast-question-manager">
        <div class="yui3-widget-bd">
            <div id="all-questions">
                <?php if (($permissions->broadcaster || $permissions->teacher) && $webcast->is_ended == 0): ?>
                    <span class="webcast-button" id="addquestion"><?php echo get_string('btn:addquestion', 'webcast') ?></span>
                <?php endif ?>
                <div class="overview">
                    <ul>
                        <!-- Holder -->
                    </ul>
                </div>
            </div>
            <div id="question-answer" style="display: none">
                <!-- Holder -->
            </div>
            <?php if (($permissions->broadcaster || $permissions->teacher) && $webcast->is_ended == 0): ?>
                <div id="question-type-selector" style="display: none">
                    <span id="webcast-button-previous-step1" class="webcast-button previous">Previous</span>
                    <span id="webcast-button-next-step1" class="webcast-button next">Next</span>
                    <h3>Select a question type</h3>
                    <label for="question-type">
                        Question type
                    </label>
                    <select name="question-type" id="question-type">
                        <option value="open">Open</option>
                        <option value="truefalse">True / False</option>
                        <!-- @todo building this option
                        <option value="choice">Choice</option>
                        -->
                    </select>
                </div>
                <div id="question-type-open" style="display: none">
                    <span class="webcast-button previous  webcast-button-previous-step2">Previous</span>
                    <span class="webcast-button next disabled" id="open-add-btn">Create</span>
                    <h3>Create your open question</h3>

                    <form>
                        <label for="question-open">
                            Question:
                        </label>
                        <input name="question" type="text" id="question-open" placeholder="Enter some question for your clients..."/>
                        <label for="question-open-summary">
                            Summary (optional):
                        </label>
                        <textarea name="summary" id="question-open-summary"></textarea>
                    </form>
                </div>
                <div id="question-type-truefalse" style="display: none">
                    <span class="webcast-button previous webcast-button-previous-step2">Previous</span>
                    <span class="webcast-button next disabled" id="truefalse-add-btn">Create</span>
                    <h3>Create your true or false question</h3>

                    <form>
                        <label for="question-truefalse">
                            Question:
                        </label>
                        <input name="question" type="text" id="question-truefalse" placeholder="Enter some question for your clients..."/>
                        <label for="question-truefalse-summary">
                            Summary (optional):
                        </label>
                        <textarea name="summary" id="question-truefalse-summary"></textarea>
                    </form>
                </div>
                <div id="question-type-choice" style="display: none">
                    <span class="webcast-button previous webcast-button-previous-step2">Previous</span>
                    <span class="webcast-button next disabled">Create</span>
                    @TODO
                </div>
            <?php endif ?>
        </div>
    </div>
<?php
// Finish the page.
echo $OUTPUT->footer();