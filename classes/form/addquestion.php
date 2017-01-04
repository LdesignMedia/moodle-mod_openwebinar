<?php
/**
 * File: addquestion.php
 * Encoding: UTF8
 *
 * @package: moodle_mod_openwebinar
 *
 * @Version: 1.0.0
 * @Since  4-1-2017
 * @Author : MoodleFreak.com | Ldesign.nl - Luuk Verhoeven
 **/
namespace mod_openwebinar\form;

use mod_openwebinar\question;

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden!');
}

global $CFG;
require_once($CFG->libdir . '/formslib.php');

class addquestion extends \moodleform {

    protected function definition() {
        $mform = &$this->_form;
        $mform->addElement('header', 'header1', get_string('form:addquestion', 'openwebinar'));

        $mform->addElement('text', 'question', get_string('form:question', 'openwebinar'),
                array('size' => '48',));
        $mform->setType('question', PARAM_TEXT);

        $mform->addElement('textarea', 'summary', get_string('form:summary', 'openwebinar'),
                [
                        'rows' => 10,
                        'cols' => 100
                ]
        );
        $mform->setType('summary', PARAM_TEXT);

        $mform->addElement('select', 'question_type', get_string('form:question_type', 'openwebinar'),
                [
                        question::QUESTION_TYPE_OPEN => get_string('type:open', 'openwebinar'),
                        question::QUESTION_TYPE_TRUE_FALSE => get_string('type:true_false', 'openwebinar'),
                ]
        );

        $mform->addRule('question', null, 'required', null, 'client');
        $mform->addRule('question_type', null, 'required', null, 'client');

        $this->add_action_buttons(true, get_string('form:save', 'openwebinar'));
    }

    /**
     * If there are errors return array of errors ("fieldname"=>"error message"),
     * otherwise true if ok.
     *
     * Server side rules do not work for uploaded files, implement serverside rules here if needed.
     *
     * @param array $data  array of ("fieldname"=>value) of submitted data
     * @param array $files array of uploaded files "element_name"=>tmp_file_path
     *
     * @return array of "element_name"=>"error_description" if there are errors,
     *         or an empty array if everything is OK (true allowed for backwards compatibility too).
     */
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        return $errors;
    }
}