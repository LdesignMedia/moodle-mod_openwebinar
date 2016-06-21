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
 * Filemanager for uploading user/broadcaster files
 *
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @package   mod_openwebinar
 * @copyright 2015 MoodleFreak.com
 * @author    Luuk Verhoeven
 **/
namespace mod_openwebinar;

use mod_openwebinar\helper;

defined('MOODLE_INTERNAL') || die();
require_once($CFG->libdir . '/formslib.php');

class formfilemanager extends \moodleform {

    /**
     * Form definition.
     *
     * @global moodle_database $DB
     */
    protected function definition() {
        $mform = &$this->_form;
        $context = $this->_customdata['context'];
        $mform->addElement('filemanager', 'files_filemanager', get_string('attachment', 'openwebinar'), null,
                helper::get_file_options($context));
    }

}