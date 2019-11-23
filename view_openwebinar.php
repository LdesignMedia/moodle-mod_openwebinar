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
 * If openwebinar is available this will be the page you load
 *
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @package   mod_openwebinar
 * @copyright 2015 MoodleFreak.com
 * @author    Luuk Verhoeven
 * @var mod_openwebinar_renderer $renderer
 */
require_once("../../config.php");
require_once(dirname(__FILE__) . '/lib.php');

$id = optional_param('id', 0, PARAM_INT); // Course_module ID, or
$n = optional_param('n', 0, PARAM_INT);  // ... openwebinar instance ID - it should be named as the first character of the module.

if ($id) {
    $cm = get_coursemodule_from_id('openwebinar', $id, 0, false, MUST_EXIST);
    $course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
    $openwebinar = $DB->get_record('openwebinar', array('id' => $cm->instance), '*', MUST_EXIST);
} else {
    if ($n) {
        $openwebinar = $DB->get_record('openwebinar', array('id' => $n), '*', MUST_EXIST);
        $course = $DB->get_record('course', array('id' => $openwebinar->course), '*', MUST_EXIST);
        $cm = get_coursemodule_from_instance('openwebinar', $openwebinar->id, $course->id, false, MUST_EXIST);
    } else {
        error('You must specify a course_module ID or an instance ID');
    }
}

// Get context.
$context = context_module::instance($cm->id);

// Print the page header.
$PAGE->set_context($context);
$PAGE->set_url('/mod/openwebinar/view_openwebinar.php', array('id' => $cm->id));

require_login($course, true, $cm);

$PAGE->set_title(format_string($openwebinar->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->add_body_class('moodlefreak-openwebinar-room');
$PAGE->set_pagelayout('embedded');

// Completion.
$completion = new completion_info($course);
$completion->set_module_viewed($cm);

$event = \mod_openwebinar\event\course_module_viewed::create(array(
        'objectid' => $cm->instance,
        'context' => $context,
));
$event->add_record_snapshot('course', $PAGE->course);
$event->add_record_snapshot($cm->modname, $openwebinar);
$event->trigger();

// Load plugin config.
$config = get_config('openwebinar');

// Permissions.
$permissions = \mod_openwebinar\helper::get_permissions($PAGE->context, $openwebinar);

// Set user online status.
\mod_openwebinar\helper::set_user_online_status($openwebinar->id);

// Set user presence.
\mod_openwebinar\helper::update_user_presence($openwebinar, $USER);

// Convert openwebinar data to JS.
$opts = (array) $openwebinar;
$opts['userid'] = $USER->id;
$opts['is_broadcaster'] = $permissions->broadcaster;

// Set booleans  / convert int to bool.
$opts['userlist'] = ($openwebinar->userlist == 1);
$opts['filesharing'] = ($openwebinar->filesharing == 1);
$opts['filesharing_student'] = ($openwebinar->filesharing_student == 1);
$opts['stream'] = ($openwebinar->stream == 1);
$opts['showuserpicture'] = ($openwebinar->showuserpicture == 1);
$opts['chat'] = ($openwebinar->chat == 1);
$opts['is_ended'] = ($openwebinar->is_ended == 1);
$opts['hls'] = ($openwebinar->hls == 1);
$opts['ajax_timer'] = ($openwebinar->ajax_timer == 1);
$opts['emoticons'] = ($openwebinar->emoticons == 1);
$opts['viewhistory'] = $permissions->history;
$opts['questions'] = true; // TODO: make this optional to use the question manager.

$opts['fullname'] = fullname($USER);
$opts['skype'] = $USER->skype;
$opts['debugjs'] = ($config->debugjs == 1);
$opts['cmid'] = $cm->id;
$opts['courseid'] = $course->id;
$opts['openwebinarid'] = $openwebinar->id;
$opts['streaming_server'] = $config->streaming_server;
$opts['chat_server'] = $config->chat_server;
$opts['multi_domain_hostname'] = $config->multi_domain_hostname;
$opts['shared_secret'] = $config->shared_secret;
$opts['usertype'] = \mod_openwebinar\helper::get_usertype($USER, $permissions);
$opts['ajax_path'] = $CFG->wwwroot . '/mod/openwebinar/api.php';
unset($opts['intro']);

if (!$opts['is_broadcaster']) {
    unset($opts['broadcaster_identifier']);
}

// VIDEO Player.
$PAGE->requires->js('/mod/openwebinar/javascript/video-js/video.min.js', true);
$PAGE->requires->css('/mod/openwebinar/javascript/video-js/video-js.min.css');

// Emoticons.
$PAGE->requires->css('/mod/openwebinar/stylesheet/emoticons.css');

// Custom scrollbar.
$PAGE->requires->js('/mod/openwebinar/javascript/tinyscrollbar.min.js', true);

// Socket.io script.
$PAGE->requires->js('/mod/openwebinar/javascript/socket.io-2.1.1.js', true);

// Room js, most of logic is here.
$PAGE->requires->yui_module('moodle-mod_openwebinar-room', 'M.mod_openwebinar.room.init', array($opts));

// Language strings.
$PAGE->requires->strings_for_js(array(
        'js:send',
        'js:new_incoming_message',
        'js:wait_on_connection',
        'js:joined',
        'js:connecting',
        'js:disconnect',
        'js:reconnected',
        'js:script_user',
        'js:system_user',
        'js:warning_message_closing_window',
        'js:error_logout_or_lostconnection',
        'js:ending_openwebinar',
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
), 'openwebinar');

$renderer = $PAGE->get_renderer('mod_openwebinar');

if (($opts['filesharing'] && $permissions->broadcaster || $opts['filesharing_student']) && $USER->id > 1) {
    $form = new \mod_openwebinar\formfilemanager("", array('context' => $context));
    $data = new stdClass();
    file_prepare_standard_filemanager($data, 'files', \mod_openwebinar\helper::get_file_options($context), $context,
            'mod_openwebinar', 'attachments');
}

// Still here? we should mark it for course completion if possible.
if ($openwebinar->is_ended === 0) {

    $completion = new completion_info($COURSE);
    if ($completion->is_enabled($cm)) {
        $completion->set_module_viewed($cm);
        $completion->update_state($cm, COMPLETION_COMPLETE);
    }
}

// Output starts here.
echo $OUTPUT->header();
?>
    <div id="openwebinar-loading"></div>
    <div id="openwebinar-holder" class="noSelect">
        <section id="openwebinar-topbar">
            <div id="openwebinar-topbar-left">
                <div id="openwebinar-menu">&nbsp;
                    <?php print_string('menu', 'openwebinar') ?>
                </div>
            </div>
            <ul id="incoming-bar">
            </ul>
            <div id="openwebinar-topbar-right">
                <?php if ($opts['showuserpicture']): ?>
                    <img src="<?php echo $CFG->wwwroot ?>/user/pix.php?file=/<?php echo $USER->id ?>/f1.jpg"/>
                <?php endif ?>
                <span class="fullname"><?php echo fullname($USER) ?> </span>
                <span class="usertype"><?php print_string($opts['usertype'], 'openwebinar') ?></span>
            </div>
        </section>
        <section id="openwebinar-left-menu" style="display: none">
            <ul>
                <li class="header"><?php print_string('opt:header_general', 'openwebinar') ?></li>
                <ul>
                    <li>
                        <div class="question">
                            <?php print_string('opt:stream', 'openwebinar') ?>
                        </div>
                        <div class="switch">
                            <input id="stream" class="openwebinar-toggle" type="checkbox" checked>
                            <label for="stream"></label>
                        </div>
                    </li>
                    <?php if ($opts['userlist'] && !$opts['is_ended']): ?>
                        <li>
                            <div class="question">
                                <?php print_string('opt:userlist', 'openwebinar') ?>
                            </div>
                            <div class="switch">
                                <input id="userlist" class="openwebinar-toggle" type="checkbox" checked>
                                <label for="userlist"></label>
                            </div>
                        </li>
                    <?php endif ?>
                    <?php if (!$opts['is_ended']): ?>
                        <li>
                            <div class="question">
                                <?php print_string('opt:chat_sound', 'openwebinar') ?>
                            </div>
                            <div class="switch">
                                <input id="sound" class="openwebinar-toggle" type="checkbox" checked>
                                <label for="sound"></label>
                            </div>
                        </li>
                    <?php endif; ?>
                </ul>
                <?php if ($permissions->broadcaster && !$opts['is_ended']): ?>
                    <li class="header"><?php print_string('opt:header_broadcaster', 'openwebinar') ?></li>
                    <ul>
                        <li class="text">
                            <?php print_string('text:broadcaster_help', 'openwebinar', $openwebinar) ?>
                        </li>
                        <li>
                            <div class="question">
                                <?php print_string('opt:mute_guests', 'openwebinar') ?>
                            </div>
                            <div class="switch">
                                <input id="mute_guest" class="openwebinar-toggle" type="checkbox" checked>
                                <label for="mute_guest"></label>
                            </div>
                        </li>
                        <li>
                            <div class="question">
                                <?php print_string('opt:mute_students', 'openwebinar') ?>
                            </div>
                            <div class="switch">
                                <input id="mute_student" class="openwebinar-toggle" type="checkbox">
                                <label for="mute_student"></label>
                            </div>
                        </li>
                        <!--
                        <li>
                            <div class="question">
                                <?php print_string('opt:mute_teachers', 'openwebinar') ?>
                            </div>
                            <div class="switch">
                                <input id="mute_teacher" class="openwebinar-toggle" type="checkbox">
                                <label for="mute_teacher"></label>
                            </div>
                        </li>
                        -->
                    </ul>
                <?php else: ?>
                    <li class="header"><?php print_string('opt:header_exit', 'openwebinar') ?></li>
                    <ul>
                        <li>
                            <span class="openwebinar-button red" id="openwebinar-leave"><?php print_string('opt:leave',
                                        'openwebinar') ?></span>
                        </li>
                    </ul>
                <?php endif ?>
            </ul>
        </section>
        <section id="openwebinar-left">
            <div id="openwebinar-stream-holder"></div>
            <header>
                <?php if ($openwebinar->is_ended == 0): ?>
                    <?php if ($permissions->broadcaster): ?>
                        <span class="openwebinar-button red" id="openwebinar-leave"><?php print_string('opt:endopenwebinar',
                                    'openwebinar') ?></span>
                    <?php else: ?>
                        <span id="openwebinar-status" class="online"><?php print_string('live', 'openwebinar') ?></span>
                    <?php endif ?>
                <?php else: ?>
                    <span id="openwebinar-status" class="offline"><?php print_string('offline', 'openwebinar') ?></span>
                <?php endif ?>
                <h1><?php echo format_string($openwebinar->name) ?>
                    <small><?php echo format_string($course->fullname) ?></small>
                </h1>
            </header>
        </section>
        <section id="openwebinar-right">
            <div id="openwebinar-userlist-holder">
                <div class="openwebinar-header">
                    <h2><?php print_string('users', 'openwebinar') ?> <span id="openwebinar-usercounter">(0)</span>
                    </h2>
                </div>
                <div id="openwebinar-userlist" class="scroll">
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
            <div id="openwebinar-chat-holder">
                <div class="openwebinar-header">
                    <span id="openwebinar-loadhistory" class="openwebinar-button" style="display: none">
                      <?php print_string('load_history', 'openwebinar') ?></span>
                    <h2><?php print_string('chat', 'openwebinar') ?></h2>
                </div>
                <div id="openwebinar-chatlist" class="scroll openwebinar-chatlist">
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
                <div id="openwebinar-chatinput">
                    <div id="openwebinar-noticebar" style="display: none">
                        alert message here
                    </div>
                    <div id="openwebinar-fileoverview-dialog" class="openwebinar-dialog" style="display: none">
                        <header>
                            <span><?php print_string('Close', 'openwebinar') ?></span>
                            <span class="openwebinar-close-sign">X</span>
                        </header>
                        <div id="openwebinar-fileoverview" class="scroll">
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
                    <div id="openwebinar-filemanager-dialog" class="openwebinar-dialog" style="display: none">
                        <header>
                            <span><?php print_string('Close', 'openwebinar') ?></span>
                            <span class="openwebinar-close-sign">X</span>
                        </header>
                        <?php if (!empty($form)): ?>
                            <?php echo $form->render() ?>
                            <span id="add-file-btn" class="openwebinar-button">
                                <?php print_string('addfile', 'openwebinar') ?></span>
                        <?php endif ?>
                    </div>
                    <div id="openwebinar-emoticons-dialog" class="openwebinar-dialog" style="display: none">
                        <header>
                            <span><?php print_string('Close', 'openwebinar') ?></span>
                            <span class="openwebinar-close-sign">X</span>
                        </header>
                        <div id="emoticons-overview">
                            <!-- Holder -->
                        </div>
                    </div>
                    <div id="openwebinar-toolbar">
                        <?php if ($opts['filesharing']): ?>
                            <span id="openwebinar-filemanager-btn"><?php print_string('filemanager', 'openwebinar') ?></span>
                            <span id="openwebinar-fileoverview-btn"><?php print_string('fileoverview', 'openwebinar') ?></span>
                        <?php endif ?>
                        <?php if ($opts['questions']): ?>
                            <span id="openwebinar-viewquestion-btn"><?php print_string('question_overview',
                                        'openwebinar') ?></span>
                        <?php endif ?>
                    </div>
                    <span id="openwebinar-emoticon-icon"></span>
                    <input autocomplete="off" type="text" disabled placeholder="<?php print_string('message_placeholder',
                            'openwebinar') ?>" name="message" class="openwebinar-message-input" id="openwebinar-message"/>
                    <span id="openwebinar-send" class="openwebinar-send-btn">
                        <?php print_string('js:wait_on_connection', 'openwebinar') ?></span>
                </div>
            </div>
        </section>
    </div>
    <div id="openwebinar-shortprofile" class="yui3-widget-loading">
        <div class="yui3-widget-bd">
            <table class="table table-bordered">
                <tr>
                    <td colspan="2" id="shortprofile-avatar" style="text-align: center"></td>
                </tr>
                <tr>
                    <td><?php print_string('fullname', 'openwebinar') ?></td>
                    <td><span id="shortprofile-fullname">-</span></td>
                </tr>
                <tr>
                    <td><?php print_string('skype', 'openwebinar') ?></td>
                    <td><span id="shortprofile-skype">-</span></td>
                </tr>
            </table>
            <div id="openwebinar-chatlist-pm" class="scroll openwebinar-chatlist">
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
            <input autocomplete="off" type="text" disabled placeholder="<?php print_string('message_placeholder',
                    'openwebinar') ?>" name="message-pm" class="openwebinar-message-input" id="openwebinar-message-pm"/>
            <span id="openwebinar-send-pm" class="openwebinar-send-btn">
                <?php print_string('js:wait_on_connection', 'openwebinar') ?></span>
        </div>
    </div>
    <div id="openwebinar-question-manager" class="yui3-widget-loading">
        <div class="yui3-widget-bd">
            <div id="all-questions">
                <?php if (($permissions->broadcaster || $permissions->teacher) && $openwebinar->is_ended == 0): ?>
                    <ul class="nav nav-pills">
                        <li class="active">
                            <a href="#">
                              <span id="addquestion">
                                    <?php print_string('btn:addquestion', 'openwebinar') ?>
                              </span>
                            </a>
                        </li>
                        <li class="active">
                            <a href="#">
                             <span id="selectquestiontemplate">
                                <?php print_string('btn:select_template_question', 'openwebinar') ?>
                             </span>
                            </a>
                        </li>
                    </ul>
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
            <?php if (($permissions->broadcaster || $permissions->teacher) && $openwebinar->is_ended == 0): ?>
                <div id="question-template-selector" style="display: none">
                    <span class="openwebinar-button-previous-step1 openwebinar-button previous">
                        <?php print_string('room:previous', 'openwebinar') ?>
                    </span>
                    <span class="openwebinar-button next" id="insert-template">
                        <?php print_string('room:select', 'openwebinar') ?>
                    </span>
                    <form>
                        <label for="question-template">
                            <?php print_string('room:question', 'openwebinar') ?>:
                        </label>
                        <select name="question-template" id="question-template">
                            <!-- Will be added by YUI -->
                        </select>
                    </form>
                </div>
                <div id="question-type-selector" style="display: none">
                    <span class="openwebinar-button-previous-step1 openwebinar-button previous">
                        <?php print_string('room:previous', 'openwebinar') ?>
                    </span>
                    <span id="openwebinar-button-next-step1" class="openwebinar-button next">
                        <?php print_string('room:next', 'openwebinar') ?>
                    </span>
                    <h3><?php print_string('room:select_question_type', 'openwebinar') ?></h3>
                    <label for="question-type">
                        <?php print_string('room:question_type', 'openwebinar') ?>
                    </label>
                    <select name="question-type" id="question-type">
                        <option value="open"><?php print_string('room:question_open', 'openwebinar') ?></option>
                        <option value="truefalse"><?php print_string('room:question_true_false', 'openwebinar') ?></option>
                        <!-- @todo building this option
                        <option value="choice">Choice</option>
                        -->
                    </select>
                </div>
                <div id="question-type-open" style="display: none">
                    <span class="openwebinar-button previous  openwebinar-button-previous-step2">
                        <?php print_string('room:previous', 'openwebinar') ?>
                    </span>
                    <span class="openwebinar-button next disabled" id="open-add-btn">
                        <?php print_string('room:create', 'openwebinar') ?>
                    </span>
                    <h3><?php print_string('room:create_open', 'openwebinar') ?></h3>
                    <form>
                        <label for="question-open">
                            <?php print_string('room:question', 'openwebinar') ?>:
                        </label>
                        <input name="question" type="text" id="question-open"
                               placeholder=""/>
                        <label for="question-open-summary">
                            <?php print_string('room:summary', 'openwebinar') ?>
                        </label>
                        <textarea name="summary" id="question-open-summary"></textarea>
                    </form>
                </div>
                <div id="question-type-truefalse" style="display: none">
                    <span class="openwebinar-button previous openwebinar-button-previous-step2">
                        <?php print_string('room:previous', 'openwebinar') ?>
                    </span>
                    <span class="openwebinar-button next disabled" id="truefalse-add-btn">
                        <?php print_string('room:create', 'openwebinar') ?>
                    </span>
                    <h3><?php print_string('room:create_true_false', 'openwebinar') ?></h3>
                    <form>
                        <label for="question-truefalse">
                            <?php print_string('room:question', 'openwebinar') ?>:
                        </label>
                        <input name="question" type="text" id="question-truefalse"
                               placeholder=""/>
                        <label for="question-truefalse-summary">
                            <?php print_string('room:summary', 'openwebinar') ?>
                        </label>
                        <textarea name="summary" id="question-truefalse-summary"></textarea>
                    </form>
                </div>
                <div id="question-type-choice" style="display: none">
                    <span class="openwebinar-button previous openwebinar-button-previous-step2">
                         <?php print_string('room:previous', 'openwebinar') ?>
                    </span>
                    <span class="openwebinar-button next disabled">
                        <?php print_string('room:create', 'openwebinar') ?>
                    </span>
                </div>
            <?php endif ?>
        </div>
    </div>
<?php
// Finish the page.
echo $OUTPUT->footer();