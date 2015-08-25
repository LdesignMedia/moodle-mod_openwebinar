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
 * Question open
 *
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @package   mod_webcast
 * @copyright 2015 MoodleFreak.com
 * @author    Luuk Verhoeven
 **/
namespace mod_webcast\questiontypes;

use mod_webcast\questiontypes;

defined('MOODLE_INTERNAL') || die();

class open extends questiontypes {

    /**
     * Question type
     * @var string
     */
    private $type = 'open';

    /**
     * Return the question type
     *
     * @return mixed
     */
    function get_question_type() {
        return $this->type;
    }

    /**
     * Display the question to the user
     *
     * @return mixed
     */
    function render() {
        // TODO: Implement render() method.
    }

    /**
     * Add a validation function to your question type
     *
     * @return mixed
     */
    function validation() {
        // TODO: Implement validation() method.
    }

    /**
     * Need to be implemented when creating the question
     *
     * @return mixed
     */
    function create() {
        // TODO: Implement create() method.
    }}