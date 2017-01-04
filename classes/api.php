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
 * @package   mod_openwebinar
 * @copyright 2015 MoodleFreak.com
 * @author    Luuk Verhoeven
 **/
namespace mod_openwebinar;

use mod_openwebinar\event\openwebinar_ping;

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
     * Some extra value
     *
     * @var mixed
     */
    protected $extra3 = false;

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
    protected $defaultresponse = array('error' => "",
                                       'status' => false);

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
    protected $openwebinar = false;

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
    protected function get_sesskey() {
        return $this->sesskey;
    }

    /**
     * @param string $sesskey
     */
    public function set_sesskey($sesskey) {
        $this->sesskey = $sesskey;
    }

    /**
     * @return mixed
     */
    protected function get_extra1() {
        return $this->extra1;
    }

    /**
     * @param mixed $extra1
     */
    public function set_extra1($extra1) {
        $this->extra1 = $extra1;
    }

    /**
     * @return mixed
     */
    protected function get_extra2() {
        return $this->extra2;
    }

    /**
     * @param mixed $extra2
     */
    public function set_extra2($extra2) {
        $this->extra2 = $extra2;
    }

    /**
     *
     * @return mixed
     */
    protected function get_extra3() {
        return $this->extra3;
    }

    /**
     * @param mixed $extra3
     */
    public function set_extra3($extra3) {
        $this->extra3 = $extra3;
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
        list($this->course, $this->openwebinar, $this->cm, $this->context) = helper::get_module_data($this->extra1, $this->extra2);
    }

    /**
     * Input json data convert directly to php object
     */
    protected function input_to_json() {
        $this->jsondata = (object) json_decode(file_get_contents('php://input'), true);
    }

    /**
     * Online timer for chat users
     *
     * @throws \Exception
     * @throws \coding_exception
     */
    public function api_call_ping() {

        // Valid sesskey.
        $this->has_valid_sesskey();

        // Set information.
        $this->get_module_information();

        // Protect updating time when openwebinar is already ended.
        if (!empty($this->openwebinar->is_ended)) {
            $this->response['status'] = false;
            $this->response['is_ended'] = true;
            $this->output_json();

            return;
        }

        $params = [
                'context' => $this->context,
                'objectid' => $this->cm->id,
        ];
        // Add new log entry.
        $event = openwebinar_ping::create($params);

        $event->add_record_snapshot('course', $this->course);
        $event->trigger();

        $this->response['online_minutes'] = helper::set_user_online_status($this->openwebinar->id);
        $this->response['status'] = true;
        $this->response['is_ended'] = false;
        $this->output_json();

    }

    /**
     * End openwebinar
     *
     * @throws \Exception
     */
    public function api_call_endopenwebinar() {

        global $DB;
        // Valid sesskey.
        $this->has_valid_sesskey();

        // Set information.
        $this->get_module_information();

        if (!empty($this->openwebinar->is_ended)) {
            throw new \Exception("openwebinar_already_ended");
        }
        $obj = new \stdClass();
        $obj->id = $this->openwebinar->id;
        $obj->is_ended = 1;

        $DB->update_record('openwebinar', $obj);
        $this->response['status'] = true;

        $this->output_json();
    }

    /**
     * Load history from a openwebinar room
     *
     * @throws \Exception
     */
    public function api_call_load_public_history() {

        global $DB;

        // Valid sesskey.
        $this->has_valid_sesskey();

        // Set information.
        $this->get_module_information();

        $this->response['messages'] = $DB->get_records('openwebinar_messages', array(
                'openwebinar_id' => $this->openwebinar->id,
                'roomtype' => '_public'),
                'timestamp ASC');

        $this->response['status'] = true;

        $this->output_json();
    }

    /**
     * Return openwebinar information to the chatserver
     *
     * @throws \Exception
     */
    public function api_call_broadcastinfo() {

        // Input listener for json data.
        $this->input_to_json();

        // Load plugin config.
        $this->get_config();

        if (!empty($this->jsondata->shared_secret) && $this->config->shared_secret == $this->jsondata->shared_secret) {
            $this->response['status'] = true;
            $this->response['openwebinar'] = helper::get_openwebinar_by_broadcastkey($this->jsondata->broadcastkey);
        }

        $this->output_json();
    }

    /**
     * Add files to a openwebinar
     *
     * @throws \coding_exception
     */
    public function api_call_add_file() {
        global $DB, $USER;

        // Valid sesskey.
        $this->has_valid_sesskey();

        // Set information.
        $this->get_module_information();

        $data = new \stdClass();
        $data->files_filemanager = required_param('files_filemanager', PARAM_INT);
        $data = file_postupdate_standard_filemanager($data, 'files', helper::get_file_options($this->context), $this->context,
                'mod_openwebinar', 'attachments', $data->files_filemanager);

        $this->response['status'] = true;
        $this->response['itemid'] = $data->files_filemanager;

        // Get files we submit.
        $fs = get_file_storage();

        $files = $DB->get_records('files', array(
                'contextid' => $this->context->id,
                'userid' => $USER->id,
                'itemid' => $data->files_filemanager,
                'component' => 'mod_openwebinar',
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
     * List of all files in a openwebinar
     */
    public function api_call_list_all_files() {

        // Valid sesskey.
        $this->has_valid_sesskey();

        // Set information.
        $this->get_module_information();

        $fs = get_file_storage();
        $files = $fs->get_area_files($this->context->id, 'mod_openwebinar', 'attachments');

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

        // Input listener for json data.
        $this->input_to_json();

        // Load plugin config.
        $this->get_config();

        // Validate its a valid request.
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
     * Use a question template
     */
    public function api_call_select_question_template() {
        global $USER;

        // Valid sesskey.
        $this->has_valid_sesskey();

        // Set information.
        $this->get_module_information();

        // Class.
        $question = new question($this->openwebinar);
        $question = $question->get_question_by_id($this->extra3);

        if ($question) {
            $this->response['status'] = true;
            $this->response['question_id'] = $question->get_id();
            $this->response['text'] = $question->get_question_text();
            $this->response['type'] = $question->get_question_type_int();
            $this->response['user_id'] = $USER->id;
        }

        $this->output_json();
    }

    /**
     * Add new question to the openwebinar
     */
    public function api_call_add_question() {

        global $USER;

        // Valid sesskey.
        $this->has_valid_sesskey();

        // Set information.
        $this->get_module_information();

        // Class.
        $question = new question($this->openwebinar);

        // Get post data.
        $questiontype = required_param('questiontype', PARAM_ALPHA);

        $data = new \stdClass();
        $data->question = required_param('question', PARAM_TEXT);
        $data->summary = optional_param('summary', '', PARAM_TEXT);
        $data->questiontype = $question->question_type_string_to_int($questiontype);

        // TODO: selectable for which users this question is.
        $users = new \stdClass();
        $users->all_enrolled_users = true;
        $users->user_ids = array();

        $returnid = $question->create($data->questiontype, $data, $users);

        if (is_numeric($returnid)) {
            $this->response['status'] = true;
            $this->response['question_id'] = $returnid;
            $this->response['text'] = $data->question;
            $this->response['type'] = $questiontype;
            $this->response['user_id'] = $USER->id;
        }

        $this->output_json();
    }

    /**
     * Get all questions from the DB
     */
    public function api_call_get_questions() {
        global $PAGE;

        // Valid sesskey.
        $this->has_valid_sesskey();

        // Set information.
        $this->get_module_information();

        // Class.
        $question = new question($this->openwebinar);
        // Get all questions in this openwebinar.
        $questions = $question->get_all_question();

        foreach ($questions as $id => $quest) {
            $obj = new \stdClass();
            $obj->name = $quest->get_question_text();
            $obj->id = $quest->get_id();
            $obj->answers = $quest->get_answers_count();
            $obj->my_answer = $quest->get_my_answer();
            $this->response['questions'][$id] = $obj;
        }
        $this->response['status'] = true;

        $permissions = helper::get_permissions($PAGE->context, $this->openwebinar);
        $this->response['manager'] = ($permissions->broadcaster || $permissions->teacher) ? true : false;
        $this->output_json();
    }

    /**
     * Get all questions templates from the DB
     */
    public function api_call_get_questions_templates() {
        global $PAGE;

        // Valid sesskey.
        $this->has_valid_sesskey();

        // Set information.
        $this->get_module_information();

        // Class.
        $question = new question($this->openwebinar);

        // Get all questions in this openwebinar.
        $questions = $question->get_all_template_question();

        foreach ($questions as $id => $quest) {
            $obj = new \stdClass();
            $obj->name = $quest->get_question_text();
            $obj->id = $quest->get_id();
            $this->response['questions'][$id] = $obj;
        }
        $this->response['status'] = true;

        $permissions = helper::get_permissions($PAGE->context, $this->openwebinar);
        $this->response['manager'] = ($permissions->broadcaster || $permissions->teacher) ? true : false;
        $this->output_json();
    }

    /**
     * Get a single question
     *
     * @throws \Exception
     */
    public function api_call_get_question() {

        global $PAGE;

        // Valid sesskey.
        $this->has_valid_sesskey();

        // Set information.
        $this->get_module_information();

        // Get question id to query.
        $questionid = required_param('questionid', PARAM_INT);

        // Class.
        $question = new question($this->openwebinar);
        $questiontype = $question->get_question_by_id($questionid);

        $obj = new \stdClass();

        // Include answers from the other for the teacher and the broadcaster.
        $permissions = helper::get_permissions($PAGE->context, $this->openwebinar);
        if ($permissions->broadcaster || $permissions->teacher) {
            $obj->answers = $questiontype->render_answers($question->get_answers($questionid));
        } else {
            // Get question form.
            $obj->my_answer = $questiontype->get_my_answer();
            $obj->form = $questiontype->render();
        }

        $this->response['item'] = $obj;
        $this->response['status'] = true;
        $this->output_json();
    }

    public function api_call_add_answer() {
        global $USER;

        // Valid sesskey.
        $this->has_valid_sesskey();

        if ($USER->id <= 1) {
            throw new \Exception(get_string('error:not_for_guests', 'openwebinar'));
        }

        // Set information.
        $this->get_module_information();

        // Get question id to query.
        $questionid = required_param('question_id', PARAM_INT);

        $question = new question($this->openwebinar);
        $this->response = $question->save_answer($questionid);
        $this->output_json();
    }

    /**
     * Manual run a task/cron function
     *
     * Sample:
     * /mod/openwebinar/api.php?action=task_test&taskname=reminder
     *
     * @throws \coding_exception
     */
    public function api_call_task_test() {
        $task = required_param('taskname', PARAM_TEXT);

        $cron = new cron();
        if (is_callable(array($cron, $task))) {
            $cron->$task();
        } else {
            echo 'NOT_EXISTS';
        }
        die();
    }

    /**
     * Set the openwebinar plugin config to this class
     *
     * @throws \Exception
     * @throws \dml_exception
     */
    public function get_config() {

        if ($this->config) {
            return;
        }

        $this->config = get_config('openwebinar');
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

        $response = array_merge($this->defaultresponse, $this->response);

        echo $OUTPUT->header();
        echo json_encode($response);
        die();
    }
}