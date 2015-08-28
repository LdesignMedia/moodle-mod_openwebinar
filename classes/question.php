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
 * Question class
 *
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @package   mod_webcast
 * @copyright 2015 MoodleFreak.com
 * @author    Luuk Verhoeven
 **/
namespace mod_webcast;

defined('MOODLE_INTERNAL') || die();

class question {

    /**
     * Open Question
     *
     * @const QUESTION_TYPE_OPEN
     */
    const QUESTION_TYPE_OPEN = 0;

    /**
     * Multiple choice
     *
     * @const QUESTION_TYPE_MULTIPLE_CHOICE
     */
    const QUESTION_TYPE_MULTIPLE_CHOICE = 1;

    /**
     * True or False
     * the respondent selects from two options: True or False
     *
     * @const QUESTION_TYPE_TRUE_FALSE
     */
    const QUESTION_TYPE_TRUE_FALSE = 2;

    /**
     * Webcast
     *
     * @var object
     */
    protected $webcast = false;

    /**
     * Set webcast
     *
     * @param object $webcast
     */
    public function __construct($webcast) {
        $this->webcast = (object)$webcast;
    }

    /**
     * Create a new question
     *
     * @param int $type
     * @param object $data
     * @param object $users
     *
     * @return bool|int
     */
    public function create($type = question::QUESTION_TYPE_OPEN, $data, $users) {

        global $DB , $USER;
        $obj = new \stdClass();
        $obj->webcast_id = $this->webcast->id;
        $obj->added_on = time();
        $obj->created_by = $USER->id;
        $obj->question_type = (int)$type;
        $obj->question_data = serialize($data);
        $obj->question_users = serialize($users);

        return $DB->insert_record('webcast_question', $obj);
    }

    /**
     * Convert question type to a integer
     *
     * @param string $type
     *
     * @return int
     * @throws \Exception
     */
    public function question_type_string_to_int($type = '') {

        switch ($type) {
            case 'open':
                return self::QUESTION_TYPE_OPEN;
            case 'choice':
                return self::QUESTION_TYPE_MULTIPLE_CHOICE;
            case 'truefalse':
                return self::QUESTION_TYPE_TRUE_FALSE;
        }

        throw new \Exception("Incorrect question type used");
    }

    /**
     * question_type_int_to_string
     *
     * @param int $int
     *
     * @return string
     * @throws \Exception
     */
    public function question_type_int_to_string($int = 0) {
        switch ($int) {
            case self::QUESTION_TYPE_OPEN:
                return 'open';
            case self::QUESTION_TYPE_MULTIPLE_CHOICE:
                return 'choice';
            case self::QUESTION_TYPE_TRUE_FALSE :
                return 'truefalse';
        }
        throw new \Exception("Incorrect question type used");
    }

    /**
     * Save a question answer
     *
     * @param int $questionid
     *
     * @return array
     */
    public function save_answer($questionid = 0) {

        // make sure questiontype is valid
        $question = $this->get_question_by_id($questionid);
        if (!$question) {
            return array('status' => false , 'error' => 'question_not_found');
        }

        $status = $question->validation();
        if ($status['status']) {
            $question->save_user_input();
            $status['created_by'] = $question->get_created_by();
            $status['added_on'] = time();
        }

        return $status;
    }


    /**
     * delete a question and there answers
     *
     * @param int $questionid
     */
    public function delete($questionid = 0) {
        global $DB;
        $DB->delete_records('webcast_question', array('id' => $questionid));
        $DB->delete_records('webcast_question_answer', array('question_id' => $questionid));
    }

    /**
     * Get all questions from this webcast
     *
     * @return bool|array
     */
    public function get_all_question() {
        global $DB;
        $questions = $DB->get_records('webcast_question', array('webcast_id' => $this->webcast->id), 'added_on ASC');

        if ($questions) {
            foreach ($questions as $id => $question) {
                $class = '\mod_webcast\questiontypes\\' . $this->question_type_int_to_string($question->question_type);
                $questions[$id] = new $class($question, false, true);
            }
        }

        return $questions;
    }

    /**
     * Get a single question or return false
     *
     * @param int $questionid
     *
     * @return false|questiontypes
     */
    public function get_question_by_id($questionid = 0) {
        global $DB;
        $question = $DB->get_record('webcast_question', array('id' => $questionid, 'webcast_id' => $this->webcast->id));
        if ($question) {
            $class = '\mod_webcast\questiontypes\\' . $this->question_type_int_to_string($question->question_type);
            $question = new $class($question, false, true);
        }

        return $question;
    }

    /**
     * Get all answers on a question
     *
     * @param int $questionid
     *
     * @return array
     */
    public function get_answers($questionid = 0) {
        global $DB;

        $qr = $DB->get_recordset('webcast_question_answer', array('question_id' => (int)$questionid), 'id DESC');

        $results = array();
        foreach ($qr as $record) {
            $record->answer_data = unserialize($record->answer_data);
            $results[$record->id] = $record;
        }
        $qr->close();

        return $results;
    }


}