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
 * Questiontypes main class
 *
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @package   mod_openwebinar
 * @copyright 2015 MoodleFreak.com
 * @author    Luuk Verhoeven
 **/
namespace mod_openwebinar;

defined('MOODLE_INTERNAL') || die();

abstract class questiontypes {

    /**
     * Postdata container
     *
     * @var \stdClass
     */
    protected $postdata;

    /**
     * Question object
     *
     * @var object
     */
    protected $questionrecord = false;

    /**
     * Own Answer object or false
     *
     * @var bool|object
     */
    protected $answer = false;

    /**
     * All Answer array or false
     *
     * @var bool|array
     */
    protected $allanswerrecords = false;

    /**
     * If you can override your previous answer
     *
     * @var bool
     */
    protected $allowoverideanswer = false;

    /**
     * __construct
     *
     * @param mixed $questionrecord The question
     * @param mixed $answer         your own answer object used for editing your response
     * @param bool $loadallanswers  if we should load all the answers
     */
    public function __construct($questionrecord = false, $answer = false, $loadallanswers = false) {

        // Init postdata store.
        $this->postdata = new \stdClass();

        if ($questionrecord) {
            $questionrecord->question_data = unserialize($questionrecord->question_data);
            $questionrecord->question_users = unserialize($questionrecord->question_users);
            if ($loadallanswers) {
                $this->get_all_answers($questionrecord->id);
            }
        }
        $this->questionrecord = $questionrecord;

        // Check if answer is given.
        if ($answer) {
            $answer->answer_data = unserialize($answer->answer_data);
            $this->answer = $answer;
        }

    }

    /**
     * Get the question text
     *
     * @return string
     */
    public function get_question_text() {
        return !empty($this->questionrecord->question_data->question) ? $this->questionrecord->question_data->question : '';
    }

    /**
     * Get the user_id from the creator of the question
     *
     * @return int
     */
    public function get_created_by() {
        return !empty($this->questionrecord->created_by) ? $this->questionrecord->created_by : -1;
    }

    /**
     * The question his summary
     *
     * @return string
     */
    public function get_question_summary() {
        return !empty($this->questionrecord->question_data->summary) ? $this->questionrecord->question_data->summary : '';
    }

    /**
     * The question openwebinar_id
     *
     * @return int
     */
    public function get_openwebinar_id() {
        return !empty($this->questionrecord->openwebinar_id) ? $this->questionrecord->openwebinar_id : 0;
    }

    /**
     * render the back button
     *
     * @return string
     * @throws \coding_exception
     */
    protected function render_back_link() {
        return \html_writer::span(get_string('btn:back', 'openwebinar'), 'btn openwebinar-back-to-questionoverview');
    }

    /**
     * Get question ID
     *
     * @return int
     */
    public function get_id() {
        return !empty($this->questionrecord->id) ? $this->questionrecord->id : 0;
    }

    /**
     * Count the answers that are given
     */
    public function get_answers_count() {
        return !empty($this->allanswerrecords) ? count($this->allanswerrecords) : 0;
    }

    /**
     * Convert all answers to usable data
     *
     * @param int $questionid
     */
    protected function get_all_answers($questionid = 0) {
        global $DB;
        $answers = $DB->get_records('openwebinar_question_answer', array('question_id' => $questionid), 'id DESC');

        // TODO: Recordset is possible a faster method.
        foreach ($answers as &$answer) {
            $answer->answer_data = unserialize($answer->answer_data);
        }
        $this->allanswerrecords = $answers;
    }

    /**
     * Get my personal answer on the question
     *
     * @return false|object
     */
    public function get_my_answer() {
        global $DB, $USER;
        if ($this->answer) {
            return $this->answer;
        }

        // No answers for guests.
        if (empty($USER) || $USER->id <= 1) {
            return false;
        }

        // Check if we have the answer in the DB.
        $answer = $DB->get_record('openwebinar_question_answer', array(
                'user_id' => $USER->id,
                'question_id' => $this->get_id()
        ));
        if ($answer) {
            $answer->answer_data = unserialize($answer->answer_data);
        }

        return $answer;
    }

    /**
     * Return the question type
     *
     * @return string
     */
    abstract public function get_question_type_string();

    /**
     * Return the question type int
     *
     * @return int
     */
    abstract public function get_question_type_int();

    /**
     * Display the question to the user
     *
     * @return string
     */
    abstract public function render();

    /**
     * Display the answers to a user
     *
     * @param $answers
     *
     * @return string
     */
    abstract public function render_answers($answers);

    /**
     * Add a validation function to your question type
     *
     * @return array
     */
    abstract public function validation();

    /**
     * Get the posted answer data
     */
    abstract protected function get_post_data();

    /**
     * Save user data
     */
    public function save_user_input() {
        global $DB, $USER;

        $result = $DB->get_record('openwebinar_question_answer', array(
                'question_id' => $this->get_id(),
                'user_id' => $USER->id
        ), 'id', IGNORE_MULTIPLE);
        if ($result && !$this->allowoverideanswer) {
            throw new \Exception(get_string('error:answer_already_saved', 'openwebinar'));
        }

        // Build answer record.
        $obj = new \stdClass();
        $obj->user_id = $USER->id;
        $obj->openwebinar_id = $this->get_openwebinar_id();
        $obj->question_id = $this->get_id();

        // Serialize form input.
        $obj->answer_data = serialize($this->postdata);

        // If its allowed to override previous answer.
        if ($result) {
            $obj->id = $result->id;
            $DB->update_record('openwebinar_question_answer', $obj);

            return $result->id;
        }

        $obj->added_on = time();

        return $DB->insert_record('openwebinar_question_answer', $obj);
    }
}