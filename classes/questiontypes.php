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
 * @package   mod_webcast
 * @copyright 2015 MoodleFreak.com
 * @author    Luuk Verhoeven
 **/
namespace mod_webcast;

defined('MOODLE_INTERNAL') || die();

abstract class questiontypes{

    /**
     * Question object
     * @var object
     */
    protected $questionrecord = false;

    /**
     * Answer object or false
     * @var bool|object
     */
    protected $answer = false;

    /**
     * __construct
     * @param mixed $questionrecord
     * @param mixed $answer
     */
    public function __construct($questionrecord = false , $answer = false){

        $this->questionrecord = (object) $questionrecord;
        $this->$answer = $answer;
    }

    /**
     * Return the question type
     * @return mixed
     */
    abstract function get_question_type();

    /**
     * Display the question to the user
     * @return mixed
     */
    abstract function render();

    /**
     * Add a validation function to your question type
     * @return mixed
     */
    abstract function validation();

    /**
     * Need to be implemented when creating the question
     * @return mixed
     */
    abstract function create();

    /**
     * Save user data
     */
    public function save_user_input(){

    }
}