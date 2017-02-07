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
     * Return the question type int
     *
     * @return int
     */
    public function get_question_type_int() {
        return question::QUESTION_TYPE_MULTIPLE_CHOICE;
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

    protected function get_answer_options() {
        $answer = $this->get_my_answer_string();
        $options = $this->get_question_answer_options();

        $html = '';
        foreach ($options as $i => $option) {
            $html .= '<label><input type="checkbox" value="1" ' . (isset($answer[$i]) ? 'checked' : '') . ' name="answer[' . $i .
                    ']" />'
                    . $option . ' </label><br>';
        }

        return $html;
    }

    /**
     * Display the question to the user
     *
     * @return mixed
     */
    public function render() {

        return $this->render_back_link() . '<h2>' . $this->get_question_text() . '</h2>
                <p>' . $this->get_question_summary() . '</p>
                <span id="question-error" style="display:none"></span>
                <form id="question-submit-answer" action="" method="post">
                    <input type="hidden" name="question_id" value="' . $this->get_id() . '"/>
                    ' . $this->get_answer_options() . '
                    <input type="submit" id="id_submitbutton" value="' . get_string('btn:open', 'openwebinar') . '"
                     class="btn-primary"/>
                </form>';
    }

    /**
     * Add a validation function to your question type
     *
     * @return array
     */
    public function validation() {
        $return = array('status' => true, 'error' => '');
        // Make sure we have the data.
        $this->get_post_data();

        // Make sure a value is given.
        if (empty($this->postdata->answer)) {
            $return = array(
                    'status' => false,
                    'error' => get_string('error:empty_not_allowed', 'openwebinar')
            );
        }

        return $return;
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
            $answeroptions = $this->get_question_answer_options();

            // Total counter.
            $totals = [];
            foreach ($answeroptions as $key => $option) {
                $totals[$key] = 0;
            }
            $answerstotal = 0;
            $answerhtml = '';
            foreach ($answers as $answer) {
                $values = $answer->answer_data->answer;

                $answerhtml .= '<div class="question-answer open">
                    <span class="fullname">' . $answer->firstname . ' ' . $answer->lastname . '</span>
                    <p class="answer">';

                foreach ($values as $value => $bool) {
                    $answerhtml .= $answeroptions[$value] . '<br>';
                    $totals[$value]++;
                    $answerstotal++;
                }
                $answerhtml .= '</p>
                </div>';
            }

            // Show stats first.
            foreach ($answeroptions as $key => $option) {
                $percentage = ($totals[$key] > 0) ? (($answerstotal * 100) / $totals[$key]) : 0;
                $return .= '<div class="progress progress-info">
                              <b style="position:absolute;left:20px">' . $option . '  ' . $percentage. '%</b>
                              <div class="bar" style="width: ' . $percentage . '%"></div>
                            </div>';
            }

            $return .= $answerhtml;

        } else {
            $return .= '<span class="openwebinar-no-result">' . get_string('error:no_result', 'openwebinar') . '</span>';
        }

        return $return;
    }

    /**
     * Get the post data from the user
     *
     * @throws \coding_exception
     */
    protected function get_post_data() {
        $this->postdata->answer = optional_param('answer', '', PARAM_TEXT);
    }
}
