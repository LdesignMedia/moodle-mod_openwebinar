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
 * Activity overview table
 *
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @package   mod_openwebinar
 * @copyright 2015 MoodleFreak.com
 * @author    Luuk Verhoeven
 **/
namespace mod_openwebinar\table;

defined('MOODLE_INTERNAL') || die();

/**
 * Simple subclass of {@link table_sql} that provides
 * some custom formatters for various columns, in order
 * to make the main outstations list nicer
 */
class useractivity extends \table_sql {

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
    function __construct($uniqueid, $openwebinar) {

        global $DB;
        parent::__construct($uniqueid);

        // Set the openwebinar.
        $this->openwebinar = $openwebinar;

        // Get extra fields.
        $extrafields = get_extra_user_fields(\context_course::instance($openwebinar->course));
        $extrafields[] = 'lastaccess';
        $dbfields = \user_picture::fields('u', $extrafields);

        // Params.
        $params = array(
                'openwebinar_id' => $openwebinar->id
        );

        $this->sql = new \stdClass();
        $this->sql->fields = 'DISTINCT ' . $dbfields . ', p.available';
        $this->sql->from = '{user} u
                            JOIN {openwebinar_presence} p ON (p.user_id = u.id)';
        $this->sql->where = 'u.deleted = 0 AND p.openwebinar_id = :openwebinar_id';

        $this->sql->params = $params;

        // Set count sql.
        $this->countsql = 'SELECT COUNT(*) FROM ' . $this->sql->from . ' WHERE ' . $this->sql->where;
        $this->countparams = $params;
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

        if (empty($row->available)) {
            return '';
        }

        $chattime = new \moodle_url('/mod/openwebinar/user_activity.php', array(
                'user_id' => $row->id,
                'id' => $PAGE->cm->id,
                'action' => 'user_chattime',
        ));

        $chatlog = new \moodle_url('/mod/openwebinar/user_activity.php', array(
                'user_id' => $row->id,
                'id' => $PAGE->cm->id,
                'action' => 'user_chatlog',
        ));

        return \html_writer::link($chattime, get_string('btn:chattime', 'openwebinar'), array(
                'class' => 'btn',
        )) . ' ' . \html_writer::link($chatlog, get_string('btn:chatlog', 'openwebinar'), array(
                'class' => 'btn',
        ));
    }

    /**
     * Render user picture
     *
     * @param $row
     *
     * @return string
     */
    protected function col_picture($row) {
        global $OUTPUT;

        return $OUTPUT->user_picture($row, array('link' => true));
    }

    /**
     * Render field
     *
     * @param $row
     *
     * @return string
     */
    protected function col_present($row) {
        return (!empty($row->available)) ? get_string('yes', 'openwebinar') : get_string('no', 'openwebinar');
    }
}