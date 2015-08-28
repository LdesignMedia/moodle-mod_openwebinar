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
 * Question True|false
 *
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @package   mod_webcast
 * @copyright 2015 MoodleFreak.com
 * @author    Luuk Verhoeven
 **/
namespace mod_webcast\questiontypes;

use mod_webcast\question;
use mod_webcast\questiontypes;

defined('MOODLE_INTERNAL') || die();

class truefalse extends questiontypes {

    /**
     * Question type
     *
     * @var string
     */
    private $type = 'truefalse';

    /**
     * Return the question type
     *
     * @return mixed
     */
    function get_question_type_string() {
        return $this->type;
    }

    /**
     * Display the question to the user
     *
     * @return mixed
     */
    function render() {

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
     * Return the question type int
     *
     * @return int
     */
    function get_question_type_int() {
        return question::QUESTION_TYPE_TRUE_FALSE;
    }

    /**
     * Display the answers to a user
     *
     * @param array $answers
     *
     * @return string
     * @throws \coding_exception
     */
    public function render_answers($answers) {
        $return = $this->render_back_link() . '<h2>' . $this->get_question_text() . '</h2>
                <p>' . $this->get_question_summary() . '</p><hr/>';
        if (!empty($answers)) {
            foreach ($answers as $answer) {

            }
        } else {
            $return .= '<span class="webcast-no-result">' . get_string('error:no_result', 'webcast') . '</span>';
        }

        return $return;
    }

    /**
     * Get the posted answer data
     */
    protected function get_post_data() {
        // TODO: Implement get_post_data() method.
    }
}