<?php
/**
 * File: offline_questions.php
 * Encoding: UTF8
 *
 * @package: moodle_mod_openwebinar
 *
 * @Version: 1.0.0
 * @Since  3-1-2017
 * @Author : MoodleFreak.com | Ldesign.nl - Luuk Verhoeven
 **/

require_once("../../config.php");
require_once(dirname(__FILE__) . '/lib.php');

$id = optional_param('id', 0, PARAM_INT); // Course_module ID, or
$n = optional_param('n', 0, PARAM_INT);  // ... openwebinar instance ID - it should be named as the first character of the module.
$action = optional_param('action', false, PARAM_TEXT);

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
$PAGE->set_url('/mod/openwebinar/offline_questions.php', array('id' => $cm->id));

require_login($course, true, $cm);

$PAGE->set_title(format_string($openwebinar->name));
$PAGE->set_heading(format_string($course->fullname));

/** @var mod_openwebinar_renderer $renderer */
$renderer = $PAGE->get_renderer('mod_openwebinar');

switch($action){

    default:
        // Output starts here.
        echo $OUTPUT->header();
        echo $renderer->question_table($openwebinar);
        echo $OUTPUT->footer();
}