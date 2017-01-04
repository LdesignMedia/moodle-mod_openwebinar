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
$questionid = optional_param('questionid', 0, PARAM_INT);

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

$baseurl = new moodle_url('/mod/openwebinar/offline_questions.php', [
        'id' => $cm->id,
        'questionid' => $questionid,
        'action' => $action,
]);
$PAGE->set_url($baseurl);

require_login($course, true, $cm);

$PAGE->set_title(format_string($openwebinar->name));
$PAGE->set_heading(format_string($course->fullname));

/** @var mod_openwebinar_renderer $renderer */
$renderer = $PAGE->get_renderer('mod_openwebinar');

switch ($action) {

    case 'edit':
        $form = new \mod_openwebinar\form\addquestion($PAGE->url);

        // Set data.
        if ($questionid > 0) {
            $question = $DB->get_record('openwebinar_question', ['id' => $questionid], '*', MUST_EXIST);

            $question->question_data = unserialize($question->question_data);

            $data = new stdClass();
            $data->question = $question->question_data->question;
            $data->summary = $question->question_data->summary;
            $data->question_type = $question->question_type;

            $form->set_data($data);
        }

        // When cancel btn is pressed.
        if ($form->is_cancelled()) {
            $baseurl->param('action' , '');

            redirect($baseurl);
        }

        // Set data.
        if (($data = $form->get_data()) != false) {

            // Serializing question data.
            $questionrow = new stdClass();
            $questionrow->question = $data->question;
            $questionrow->summary = $data->summary;
            $data->question_data = serialize($questionrow);

            // Serializing user data.
            $users = new stdClass();
            $users->all_enrolled_users = true;
            $users->user_ids = array();
            $data->question_users = serialize($users);

            if ($questionid > 0) {
                $data->id = $questionid;
                $DB->update_record('openwebinar_question', $data);
            } else {

                // We will add a new question.
                $data->created_by = $USER->id;
                $data->openwebinar_id = $openwebinar->id;
                $data->grouptype = 'template';
                $data->added_on = time();

                $DB->insert_record('openwebinar_question', $data);
            }
            $baseurl->param('action' , '');
            redirect($baseurl);
        }

        // Output starts here.
        echo $OUTPUT->header();
        echo $form->display();
        echo $OUTPUT->footer();

        break;

    case 'delete':
        $DB->delete_records('openwebinar_question', ['id' => $questionid]);
        $baseurl->param('action' , '');
        redirect($baseurl);
        break;

    default:

        // Output starts here.
        echo $OUTPUT->header();

        echo \html_writer::link(new moodle_url('/mod/openwebinar/offline_questions.php', [
                'questionid' => 0,
                'id' => $cm->id,
                'action' => 'edit'
        ]), get_string('btn:new', 'openwebinar'), [
                'class' => 'btn btn-primary'
        ]);

        echo '<hr/>';

        echo $renderer->question_table($openwebinar);
        echo $OUTPUT->footer();
}