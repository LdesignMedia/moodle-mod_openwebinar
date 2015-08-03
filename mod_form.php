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
 * The main webcast configuration form
 *
 * It uses the standard core Moodle formslib. For more info about them, please
 * visit: http://docs.moodle.org/en/Development:lib/formslib.php
 *
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @package   mod_webcast
 * @copyright 2015 MoodleFreak.com
 * @author    Luuk Verhoeven
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/course/moodleform_mod.php');

/**
 * Module instance settings form
 *
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @package   mod_webcast
 * @copyright 2015 MoodleFreak.com
 * @author    Luuk Verhoeven
 */
class mod_webcast_mod_form extends moodleform_mod {

    /** @var array options to be used with date_time_selector fields in the webcast. */
    public static $datefieldoptions = array('optional' => false, 'step' => 1);

    /**
     * Defines forms elements
     */
    public function definition() {

        global $CFG;
        $mform = $this->_form;

        // Load default config
        $config = get_config('webcast');

        // Adding the "general" fieldset, where all the common settings are showed.
        $mform->addElement('header', 'general', get_string('general', 'form'));

        // Adding the standard "name" field.
        $mform->addElement('text', 'name', get_string('webcastname', 'webcast'), array('size' => '64'));
        if (!empty($CFG->formatstringstriptags)) {
            $mform->setType('name', PARAM_TEXT);
        } else {
            $mform->setType('name', PARAM_CLEAN);
        }
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');
        $mform->addHelpButton('name', 'webcastname', 'webcast');

        // Adding the standard "intro" and "introformat" fields.
        $this->add_intro_editor();

        $mform->addElement('header', 'timing', get_string('mod_setting:timing', 'webcast'));

        // Open and close dates.
        $mform->addElement('date_time_selector', 'timeopen', get_string('mod_setting:timeopen', 'webcast'), self::$datefieldoptions);
        $mform->addHelpButton('timeopen', 'mod_setting:timeopenhelp', 'webcast');

        $mform->addElement('duration', 'duration', get_string('mod_setting:duration', 'webcast') , array('defaultunit' => 3600 , 'optional' => false));
        $mform->addHelpButton('duration', 'mod_setting:durationhelp', 'webcast');
        $mform->setDefault('duration', 3600);

        $mform->addElement('header', 'settings', get_string('mod_setting:settings', 'webcast'));
        $mform->addElement('selectyesno', 'stream', get_string('setting:stream', 'webcast'));
        $mform->setDefault('stream', $config->stream);
        $mform->addElement('selectyesno', 'chat', get_string('setting:chat', 'webcast'));
        $mform->setDefault('filesharing', $config->chat);
        $mform->addElement('selectyesno', 'filesharing', get_string('setting:filesharing', 'webcast'));
        $mform->setDefault('filesharing', $config->filesharing);
        $mform->addElement('selectyesno', 'filesharing_student', get_string('setting:filesharing_student', 'webcast'));
        $mform->setDefault('filesharing_student', $config->filesharing_student);
        $mform->addElement('selectyesno', 'showuserpicture', get_string('setting:showuserpicture', 'webcast'));
        $mform->setDefault('showuserpicture', $config->showuserpicture);
        $mform->addElement('selectyesno', 'userlist', get_string('setting:userlist', 'webcast'));
        $mform->setDefault('userlist', $config->userlist);

        $mform->addElement('header', 'broadcasterheader', get_string('mod_setting:broadcaster', 'webcast'));
        $this->add_webcast_user_selector();

        // add broadcastkey
        if (empty($this->current->instance)) {

            $key = \mod_webcast\helper::generate_key();

            $obj = new stdClass();
            $obj->broadcastkey = $key;

            $mform->addElement('static', 'html_broadcastkey', get_string('mod_setting:broadcastkey', 'webcast'), get_string('mod_setting:broadcastkey_desc', 'webcast', $obj));

            // add value to the form
            $mform->addElement('hidden', 'broadcastkey');
            $mform->setType('broadcastkey', PARAM_TEXT);
            $mform->setDefault('broadcastkey', $key);

        } else {
            $obj = new stdClass();
            $obj->broadcastkey = $this->current->broadcastkey;
            $mform->addElement('static', 'html_broadcastkey', get_string('mod_setting:broadcastkey', 'webcast'), get_string('mod_setting:broadcastkey_desc', 'webcast', $obj));
        }

        $mform->addElement('header', 'reminders', get_string('mod_setting:reminders', 'webcast'));
        $mform->addElement('duration', 'reminder_1', get_string('setting:reminder_1', 'webcast'));
        $mform->setDefault('reminder_1', $config->reminder_1);
        $mform->addElement('duration', 'reminder_2', get_string('setting:reminder_2', 'webcast'));
        $mform->setDefault('reminder_2', $config->reminder_2);
        $mform->addElement('duration', 'reminder_3', get_string('setting:reminder_3', 'webcast'));
        $mform->setDefault('reminder_3', $config->reminder_3);

        // Add standard grading elements.
        $this->standard_grading_coursemodule_elements();

        // Add standard elements, common to all modules.
        $this->standard_coursemodule_elements();

        // Add standard buttons, common to all modules.
        $this->add_action_buttons();
    }

    /**
     * add a select element for a broadcaster
     */
    protected function add_webcast_user_selector() {
        global $DB;
        $array = array('' => get_string('mod_setting:make_a_selection', 'webcast'));
        $rs = $DB->get_recordset_sql('SELECT {user}.id , {user}.firstname ,{user}.lastname
                                        FROM {user}
                                        WHERE {user}.deleted = 0
                                        ORDER BY {user}.firstname ASC');
        foreach ($rs as $user) {
            $array[$user->id] = $user->firstname . " " . $user->lastname . " ({$user->id})";
        }
        $rs->close();
        $this->_form->addElement('select', 'broadcaster', get_string('mod_setting:broadcaster', 'webcast'), $array);
    }

    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        // Make sure that time is yet to come
//        if(time() > $data['timeopen']){
//            $errors['timeopen'] = get_string('error:time_passed' , 'webcast');
//        }

        return $errors;
    }

}
