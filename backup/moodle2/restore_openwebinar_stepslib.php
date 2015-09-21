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
 * Structure step to restore one openwebinar activity
 *
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @package   mod_openwebinar
 * @copyright 2015 MoodleFreak.com
 * @author    Luuk Verhoeven
 */
class restore_openwebinar_activity_structure_step extends restore_activity_structure_step {

    /**
     * Defines structure of path elements to be processed during the restore
     *
     * @return array of {@link restore_path_element}
     */
    protected function define_structure() {

//        $paths = array();
//        $paths[] = new restore_path_element('openwebinar', '/activity/openwebinar');
//
//        // Return the paths wrapped into standard activity structure.
//        return $this->prepare_activity_structure($paths);
    }

    /**
     * Process the given restore path element data
     *
     * @param array $data parsed element data
     */
    protected function process_openwebinar($data) {
//        global $DB;
//
//        $data = (object)$data;
//        $oldid = $data->id;
//        $data->course = $this->get_courseid();
//
//        if (empty($data->timecreated)) {
//            $data->timecreated = time();
//        }
//
//        if (empty($data->timemodified)) {
//            $data->timemodified = time();
//        }
//
//        if ($data->grade < 0) {
//            // Scale found, get mapping.
//            $data->grade = -($this->get_mappingid('scale', abs($data->grade)));
//        }
//
//        // Create the openwebinar instance.
//        $newitemid = $DB->insert_record('openwebinar', $data);
//        $this->apply_activity_instance($newitemid);
    }

    /**
     * Post-execution actions
     */
    protected function after_execute() {
        // Add openwebinar related files, no need to match by itemname (just internally handled context).
//        $this->add_related_files('mod_openwebinar', 'intro', null);
    }
}
