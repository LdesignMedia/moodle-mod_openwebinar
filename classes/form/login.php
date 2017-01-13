<?php
/**
 * File: login.php
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

class login extends \moodleform {

    protected function definition() {
        $mform = &$this->_form;
        $mform->addElement('header', 'header1', get_string('login'));

        $mform->addElement('text', 'username', get_string('username'),
                array('style' => 'width:80%',));
        $mform->setType('username', PARAM_USERNAME);

        $mform->addElement('password', 'password', get_string('password'),
                array('style' => 'width:80%',));
        $mform->setType('password', PARAM_RAW);

        $mform->addRule('username', null, 'required', null, 'client');
        $mform->addRule('password', null, 'required', null, 'client');

        $this->add_action_buttons(false, get_string('login'));
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
        global $DB;

        $errors = parent::validation($data, $files);

        $user = $DB->get_record('user', ['username' => $data['username']] , '*' , IGNORE_MULTIPLE);

        if(!$user){
            $errors['username'] = get_string('error:invalid' , 'openwebinar');
        }elseif(!validate_internal_user_password($user, $data['password'])){
            $errors['password'] = get_string('error:invalid' , 'openwebinar');
        }

        return $errors;
    }
}