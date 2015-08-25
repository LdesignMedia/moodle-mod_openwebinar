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

class question{

    /**
     * Open Question
     * @const QUESTION_TYPE_OPEN
     */
    const QUESTION_TYPE_OPEN = 0;

    /**
     * Multiple choice
     * @const QUESTION_TYPE_MULTIPLE_CHOICE
     */
    const QUESTION_TYPE_MULTIPLE_CHOICE = 1;

    /**
     * True or False
     * the respondent selects from two options: True or False
     * @const QUESTION_TYPE_TRUE_FALSE
     */
    const QUESTION_TYPE_TRUE_FALSE = 2;

    /**
     * Webcast
     * @var object
     */
    protected $webcast = false;

    /**
     * Set webcast
     * @param object $webcast
     */
    public function __construct($webcast){
        $this->webcast = (object) $webcast;
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
    public function create($type = question::QUESTION_TYPE_OPEN , $data  ,$users){

        global $DB;

        $obj = new \stdClass();
        $obj->webcast_id = $this->webcast->id;
        $obj->added_on = time();
        $obj->question_type = (int) $type;
        $obj->question_data = serialize($data);
        $obj->question_users = serialize($users);

        return $DB->insert_record('webcast_question' , $obj);
    }

    /**
     * Convert question type to a integer
     *
     * @param string $type
     *
     * @return int
     * @throws \Exception
     */
    public function question_type_string_to_int($type = ''){

        switch($type){
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
     * delete a question and there answers
     *
     * @param int $questionid
     */
    public function delete($questionid = 0){
        global $DB;
        $DB->delete_records('webcast_question' , array('id' => $questionid));
        $DB->delete_records('webcast_question_answer' , array('question_id' => $questionid));
    }
}