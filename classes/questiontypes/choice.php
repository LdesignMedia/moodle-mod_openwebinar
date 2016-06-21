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
 * Question choice
 *
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @package   mod_openwebinar
 * @copyright 2015 MoodleFreak.com
 * @author    Luuk Verhoeven
 **/
namespace mod_openwebinar\questiontypes;

use mod_openwebinar\question;
use mod_openwebinar\questiontypes;

defined('MOODLE_INTERNAL') || die();

class choice extends questiontypes {

    /**
     * Question type
     *
     * @var string
     */
    private $type = 'choice';

    /**
     * Return the question type
     *
     * @return mixed
     */
    public function get_question_type_string() {
        return $this->type;
    }

    /**
     * Display the question to the user
     *
     * @return mixed
     */
    public function render() {
        // TODO: Implement render() method.
    }

    /**
     * Add a validation function to your question type
     *
     * @return mixed
     */
    public function validation() {
        // TODO: Implement validation() method.
    }

    /**
     * Return the question type int
     *
     * @return int
     */
    public function get_question_type_int() {
        return question::QUESTION_TYPE_MULTIPLE_CHOICE;
    }

    /**
     * Display the answers to a user
     *
     * @return mixed
     */
    public function render_answers($answers) {
        // TODO: Implement render_answers() method.
    }

    /**
     * Get the posted answer data
     */
    protected function get_post_data() {
        // TODO: Implement get_post_data() method.
    }
}
