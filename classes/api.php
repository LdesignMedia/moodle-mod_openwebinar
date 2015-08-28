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
 * api class
 *
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @package   mod_webcast
 * @copyright 2015 MoodleFreak.com
 * @author    Luuk Verhoeven
 **/
namespace mod_webcast;

use mod_webcast\event\webcast_ping;
use mod_webcast\helper;
use mod_webcast\question;

defined('MOODLE_INTERNAL') || die();

class api {

    /**
     * user session key
     *
     * @var string
     */
    protected $sesskey = '';

    /**
     * Some extra value
     *
     * @var mixed
     */
    protected $extra1 = false;

    /**
     * Some extra value
     *
     * @var mixed
     */
    protected $extra2 = false;

    /**
     * plugin object
     *
     * @var mixed
     */
    protected $config = false;

    /**
     * Default response
     *
     * @var array
     */
    protected $defaultResponse = array('error' => "", 'status' => false);

    /**
     * Response holder
     *
     * @var array
     */
    protected $response = array();


    /**
     * Container
     *
     * @var bool|object
     */
    protected $jsondata = false;

    /**
     * Container
     *
     * @var bool|object
     */
    protected $course = false;

    /**
     * Container
     *
     * @var bool|object
     */
    protected $webcast = false;

    /**
     * Container
     *
     * @var bool|object
     */
    protected $cm = false;

    /**
     * Container
     *
     * @var bool|object
     */
    protected $context = false;


    /**
     * @return string
     */
    protected function getSesskey() {
        return $this->sesskey;
    }

    /**
     * @param string $sesskey
     */
    public function setSesskey($sesskey) {
        $this->sesskey = $sesskey;
    }

    /**
     * @return mixed
     */
    protected function getExtra1() {
        return $this->extra1;
    }

    /**
     * @param mixed $extra1
     */
    public function setExtra1($extra1) {
        $this->extra1 = $extra1;
    }

    /**
     * @return mixed
     */
    protected function getExtra2() {
        return $this->extra2;
    }

    /**
     * @param mixed $extra2
     */
    public function setExtra2($extra2) {
        $this->extra2 = $extra2;
    }


    /**
     * public __construct
     */
    public function __construct() {

    }

    /**
     * get_module_information
     */
    protected function get_module_information() {

        if ($this->course) {
            return;
        }
        list($this->course, $this->webcast, $this->cm, $this->context) = helper::get_module_data($this->extra1, $this->extra2);
    }

    /**
     * Input json data convert directly to php object
     */
    protected function input_to_json() {
        $this->jsondata = (object)json_decode(file_get_contents('php://input'), true);
    }

    /**
     * Online timer for chat users
     *
     * @throws \Exception
     * @throws \coding_exception
     */
    public function api_call_ping() {

        global $PAGE;

        // Valid sesskey
        $this->has_valid_sesskey();

        // Set information
        $this->get_module_information();

        if (!empty($this->webcast->is_ended)) {
            throw new \Exception("webcast_already_ended");
        }

        $params = array(
            'context' => $this->context,
            'objectid' => $this->cm->id,
        );
        // add new log entry
        $event = webcast_ping::create($params);

        $event->add_record_snapshot('course', $this->course);
        $event->trigger();

        $this->response['online_minutes'] = helper::set_user_online_status($this->webcast->id);
        $this->response['status'] = true;

        $this->output_json();
    }

    /**
     * End webcast
     *
     * @throws \Exception
     */
    public function api_call_endwebcast() {

        global $DB;
        // Valid sesskey
        $this->has_valid_sesskey();

        // Set information
        $this->get_module_information();

        if (!empty($this->webcast->is_ended)) {
            throw new \Exception("webcast_already_ended");
        }
        $obj = new \stdClass();
        $obj->id = $this->webcast->id;
        $obj->is_ended = 1;

        $DB->update_record('webcast', $obj);
        $this->response['status'] = true;

        $this->output_json();
    }

    /**
     * Load history from a webcast room
     *
     * @throws \Exception
     */
    public function api_call_load_public_history() {

        global $DB;

        // Valid sesskey
        $this->has_valid_sesskey();

        // Set information
        $this->get_module_information();

        $this->response['messages'] = $DB->get_records('webcast_messages', array('webcast_id' => $this->webcast->id), 'timestamp ASC');
        $this->response['status'] = true;

        $this->output_json();
    }

    /**
     * Return webcast information to the chatserver
     *
     * @throws \Exception
     */
    public function api_call_broadcastinfo() {

        // Input listener for json data
        $this->input_to_json();

        // Load plugin config
        $this->get_config();

        if (!empty($this->jsondata->shared_secret) && $this->config->shared_secret == $this->jsondata->shared_secret) {
            $this->response['status'] = true;
            $this->response['webcast'] = helper::get_webcast_by_broadcastkey($this->jsondata->broadcastkey);
        }

        $this->output_json();
    }

    /**
     * Add files to a webcast
     *
     * @throws \coding_exception
     */
    public function api_call_add_file() {
        global $DB, $USER;

        // Valid sesskey
        $this->has_valid_sesskey();

        // Set information
        $this->get_module_information();

        $data = new \stdClass();
        $data->files_filemanager = required_param('files_filemanager', PARAM_INT);
        $data = file_postupdate_standard_filemanager($data, 'files', helper::get_file_options($this->context), $this->context, 'mod_webcast', 'attachments', $data->files_filemanager);

        $this->response['status'] = true;
        $this->response['itemid'] = $data->files_filemanager;

        // get files we submit
        $fs = get_file_storage();

        $files = $DB->get_records('files', array(
            'contextid' => $this->context->id,
            'userid' => $USER->id,
            'itemid' => $data->files_filemanager,
            'component' => 'mod_webcast',
            'filearea' => 'attachments'
        ));
        foreach ($files as $file) {

            $file = $fs->get_file_by_id($file->id);

            if ($file && $file->get_filename() !== '.' && !$file->is_directory()) {
                $this->response['files'][] = helper::get_file_info($file, $fs);
            }
        }

        $this->output_json();
    }

    /**
     * List of all files in a webcast
     */
    public function api_call_list_all_files() {

        // Valid sesskey
        $this->has_valid_sesskey();

        // Set information
        $this->get_module_information();

        $fs = get_file_storage();
        $files = $fs->get_area_files($this->context->id, 'mod_webcast', 'attachments');

        foreach ($files as $f) {
            if ($f && $f->get_filename() !== '.' && !$f->is_directory()) {
                $this->response['files'][] = helper::get_file_info($f, $fs);
            }
        }
        $this->response['status'] = true;
        $this->output_json();
    }


    /**
     * Save the chatlog from the chat server
     */
    public function api_call_chatlog() {

        // Input listener for json data
        $this->input_to_json();

        // Load plugin config
        $this->get_config();

        // validate its a valid request
        if (!empty($this->jsondata->shared_secret) && $this->config->shared_secret == $this->jsondata->shared_secret) {
            $status = helper::save_messages($this->jsondata);
            if ($status) {
                $this->response['status'] = true;
            } else {
                $this->response['error'] = 'failed_saving';
            }
        } else {
            $this->response['error'] = 'wrong_shared_secret';
        }

        $this->output_json();
    }

    /**
     * Add new question to the webcast
     */
    public function api_call_add_question() {

        global $USER;

        // Valid sesskey
        $this->has_valid_sesskey();

        // Set information
        $this->get_module_information();

        // class
        $question = new question($this->webcast);

        // get post data
        $questiontype = required_param('questiontype', PARAM_ALPHA);

        $data = new \stdClass();
        $data->question = required_param('question', PARAM_TEXT);
        $data->summary = optional_param('summary', '', PARAM_TEXT);
        $data->questiontype = $question->question_type_string_to_int($questiontype);

        //@todo selectable for which users this question is
        $users = new \stdClass();
        $users->all_enrolled_users = true;
        $users->user_ids = array();

        $returnid = $question->create($data->questiontype, $data, $users);

        if (is_numeric($returnid)) {
            $this->response['status'] = true;
            $this->response['question_id'] = $returnid;
            $this->response['text'] =  $data->question;
            $this->response['type'] =  $questiontype;
            $this->response['user_id'] =  $USER->id;
        }

        $this->output_json();
    }

    /**
     * Get all questions from the DB
     */
    public function api_call_get_questions() {
        // Valid sesskey
        $this->has_valid_sesskey();

        // Set information
        $this->get_module_information();

        // class
        $question = new question($this->webcast);
        // get all questions in this webcast
        $questions = $question->get_all_question();

        /**
         * @var int $id
         * @var \mod_webcast\questiontypes $quest
         */
        foreach ($questions as $id => $quest) {
            $obj = new \stdClass();
            $obj->name = $quest->get_question_text();
            $obj->id = $quest->get_id();
            $obj->answers = $quest->get_answers_count();
            $obj->my_answer = $quest->get_my_answer();
            $this->response['questions'][$id] = $obj;
        }
        $this->response['status'] = true;
        $this->output_json();
    }

    /**
     * Get a single question
     *
     * @throws \Exception
     */
    public function api_call_get_question() {

        global $PAGE;

        // Valid sesskey
        $this->has_valid_sesskey();

        // Set information
        $this->get_module_information();

        // get question id to query
        $questionid = required_param('questionid', PARAM_INT);

        // class
        $question = new question($this->webcast);

        /**
         * @var \mod_webcast\questiontypes $questiontype
         */
        $questiontype = $question->get_question_by_id($questionid);

        $obj = new \stdClass();

        // include answers from the other for the teacher and the broadcaster
        $permissions =helper::get_permissions($PAGE->context, $this->webcast);
        if($permissions->broadcaster || $permissions->teacher){
            $obj->answers = $questiontype->render_answers($question->get_answers($questionid));
        }else{
            // get question form
            $obj->my_answer = $questiontype->get_my_answer();
            $obj->form = $questiontype->render();
        }

        $this->response['item'] = $obj;
        $this->response['status'] = true;
        $this->output_json();
    }

    public function api_call_add_answer(){
        global $USER;

        // Valid sesskey
        $this->has_valid_sesskey();

        if($USER->id <= 1){
            throw new \Exception(get_string('error:not_for_guests' , 'webcast'));
        }

        // Set information
        $this->get_module_information();

        // get question id to query
        $questionid = required_param('question_id', PARAM_INT);

        $question = new question($this->webcast);
        $this->response = $question->save_answer($questionid);
        $this->output_json();
    }


    /**
     * Set the webcast plugin config to this class
     *
     * @throws \Exception
     * @throws \dml_exception
     */
    public function get_config() {

        if ($this->config) {
            return;
        }

        $this->config = get_config('webcast');
    }

    /**
     * Check if user has a valid sesskey
     *
     * @throws \Exception
     */
    protected function has_valid_sesskey() {
        if (!confirm_sesskey($this->sesskey)) {
            throw new \Exception('invalid_sesskey');
        }
    }

    /**
     * Send output to client
     */
    protected function output_json() {

        global $OUTPUT;

        $response = array_merge($this->defaultResponse, $this->response);

        echo $OUTPUT->header();
        echo json_encode($response);
        die();
    }
}