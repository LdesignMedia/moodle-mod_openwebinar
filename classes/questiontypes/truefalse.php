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
        $answer =  $this->get_my_answer_string();
        return $this->render_back_link() . '<h2>' . $this->get_question_text() . '</h2>
                <p>' . $this->get_question_summary() . '</p>
                <span id="question-error" style="display:none"></span>
                <form id="question-submit-answer" action="" method="post">
                    <input type="hidden" name="question_id" value="' . $this->get_id() . '"/>
                    <select name="answer">
                        <option value="yes" '.($answer == 'yes' ? 'selected' : '').'>'.get_string('yes' , 'webcast').'</option>
                        <option value="no" '.($answer == 'no' ? 'selected' : '').'>'.get_string('no' , 'webcast').'</option>
                    </select>
                    <input type="submit" id="id_submitbutton" value="' . get_string('btn:open', 'webcast') . '" class="btn-primary"/>
                </form>';
    }

    /**
     * Get my personal answer value
     * prevent against xss and code injection
     *
     * @return string
     */
    protected function get_my_answer_string() {
        $answer = $this->get_my_answer();

        return !empty($answer->answer_data->answer) ? $answer->answer_data->answer : '';
    }


    /**
     * Add a validation function to your question type
     *
     * @return array
     */
    public function validation() {
        $return = array('status' => true, 'error' => '');
        // make sure we have the data
        $this->get_post_data();

        // make sure a value is given
        if (empty($this->postdata->answer) && ($this->postdata->answer !== 'yes' && $this->postdata->answer !== 'no')) {
            $return = array(
                'status' => false,
                'error' => get_string('error:empty_not_allowed', 'webcast')
            );
        }

        return $return;
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
     * @return mixed
     */
    public function render_answers($answers) {
        $return = $this->render_back_link() . '<h2>' . $this->get_question_text() . '</h2>
                <p>' . $this->get_question_summary() . '</p><hr/>';
        if (!empty($answers)) {
            foreach ($answers as $answer) {
                    $return .= '<div class="question-answer open">
                    <span class="fullname">' . $answer->firstname . ' ' . $answer->lastname . '</span>
                    <p class="answer">' . get_string($answer->answer_data->answer, 'webcast') . '</p>
                </div>';
            }
        } else {
            $return .= '<span class="webcast-no-result">' . get_string('error:no_result', 'webcast') . '</span>';
        }

        return $return;
    }

    /**
     * Get the post data from the user
     * @throws \coding_exception
     */
    protected function get_post_data() {
        $this->postdata->answer = optional_param('answer', '', PARAM_ALPHA);
    }
}