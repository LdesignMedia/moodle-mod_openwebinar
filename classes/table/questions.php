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
 * Question overview table
 *
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @package   mod_openwebinar
 * @copyright 2015 MoodleFreak.com
 * @author    Luuk Verhoeven
 **/
namespace mod_openwebinar\table;

use mod_openwebinar\question;

defined('MOODLE_INTERNAL') || die();

/**
 * Simple subclass of {@link table_sql} that provides
 * some custom formatters for various columns, in order
 * to make the main outstations list nicer
 */
class questions extends \table_sql {

    /**
     * Openwebinar object
     *
     * @var bool|object
     */
    public $openwebinar = false;

    /**
     * Build the table and sql parts
     *
     * @param string $uniqueid
     * @param $openwebinar
     *
     * @throws \coding_exception
     * @throws \dml_exception
     */
    public function __construct($uniqueid, \stdClass $openwebinar) {

        parent::__construct($uniqueid);

        // Set the openwebinar.
        $this->openwebinar = $openwebinar;

        // Get extra fields.

        // Params.
        $params = ['openwebinar_id' => $openwebinar->id];

        $this->sql = new \stdClass();
        $this->sql->fields = 'q.* , q.question_data as question_name, q.question_data as question_summary';
        $this->sql->from = '{openwebinar_question} q';
        $this->sql->where = 'q.openwebinar_id = :openwebinar_id AND q.grouptype = "template"';
        $this->sql->params = $params;

        // Set count sql.
        $this->countsql = 'SELECT COUNT(*) FROM ' . $this->sql->from . ' WHERE ' . $this->sql->where;
        $this->countparams = $params;
    }

    /**
     * @param $row
     *
     * @return string
     */
    protected function col_question_type($row){
        switch ($row->question_type){
            case question::QUESTION_TYPE_OPEN:
                return get_string('type:open' , 'openwebinar');

            case question::QUESTION_TYPE_TRUE_FALSE:
                return  get_string('type:true_false' , 'openwebinar');

            case question::QUESTION_TYPE_MULTIPLE_CHOICE:
                return  get_string('type:multiple_choice' , 'openwebinar');
        }
        return '';
    }

    /**
     * Get unserialized data
     *
     * @param $row
     *
     * @return mixed
     */
    private function get_unserialize_data($row){
        static $array;

        if(!isset($array[$row->id])){
            $array[$row->id] = unserialize($row->question_data);
        }

        return $array[$row->id];
    }

    /**
     * Render name
     *
     * @param $row
     *
     * @return mixed
     */
    protected function col_question_name($row){
        $data = $this->get_unserialize_data($row);
        return $data->question;
    }

    /**
     * Render comment
     *
     * @param $row
     *
     * @return mixed
     */
    protected function col_question_comment($row){
        $data = $this->get_unserialize_data($row);
        return !empty($data->comment) ? $data->comment : '';
    }

    /**
     * Render actions
     *
     * @param $row
     *
     * @return string
     */
    protected function col_action($row) {

        global $PAGE;

        $edit = new \moodle_url('/mod/openwebinar/offline_questions.php', array(
                'id' => $PAGE->cm->id,
                'questionid' => $row->id,
                'action' => 'edit',
        ));

        $delete = new \moodle_url('/mod/openwebinar/offline_questions.php', array(
                'id' => $PAGE->cm->id,
                'questionid' => $row->id,
                'action' => 'delete',
        ));

        return \html_writer::link($edit, get_string('btn:edit', 'openwebinar'), array(
                'class' => 'btn',
        )) . ' ' . \html_writer::link($delete, get_string('btn:delete', 'openwebinar'), array(
                'class' => 'btn btn-danger',
        ));
    }

}