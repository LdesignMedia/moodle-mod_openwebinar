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
 * Library of interface functions and constants for module openwebinar
 *
 * All the core Moodle functions, needed to allow the module to work
 * integrated in Moodle should be placed here.
 *
 * All the openwebinar specific functions, needed to implement all the module
 * logic, should go to locallib.php. This will help to save some memory when
 * Moodle is performing actions across all modules.
 *
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @package   mod_openwebinar
 * @copyright 2015 MoodleFreak.com
 * @author    Luuk Verhoeven
 **/

defined('MOODLE_INTERNAL') || die();

/* Moodle core API */

/**
 * Returns the information on whether the module supports a feature
 *
 * See {@link plugin_supports()} for more info.
 *
 * @param string $feature FEATURE_xx constant for requested feature
 *
 * @return mixed true if the feature is supported, null if unknown
 */
function openwebinar_supports($feature) {

    switch ($feature) {
        case FEATURE_MOD_INTRO:
            return true;
        case FEATURE_SHOW_DESCRIPTION:
            return true;
        case FEATURE_GRADE_HAS_GRADE:
            return false;
        case FEATURE_COMPLETION_TRACKS_VIEWS:
            return true;

        case FEATURE_BACKUP_MOODLE2:
            return false;// TODO: implement later...
        default:
            return null;
    }
}

/**
 * Given a course_module object, this function returns any
 * "extra" information that may be needed when printing
 * this activity in a course listing.
 * See get_array_of_activities() in course/lib.php
 *
 * @global object
 *
 * @param object $coursemodule
 *
 * @return cached_cm_info|null
 */
function openwebinar_get_coursemodule_info($coursemodule) {
    global $DB;

    if (($openwebinar = $DB->get_record('openwebinar', array('id' => $coursemodule->instance))) !== false) {
        $info = new cached_cm_info();
        $info->name = $openwebinar->name . ' - ' . date('d-m-Y ', $openwebinar->timeopen) . get_string('starttime', 'openwebinar') .
                date(' H:i', $openwebinar->timeopen);
        $info->content = format_module_intro('openwebinar', $openwebinar, $coursemodule->id, false);

        return $info;
    } else {
        return null;
    }
}

/**
 * Saves a new instance of the openwebinar into the database
 *
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will create a new instance and return the id number
 * of the new instance.
 *
 * @param stdClass $openwebinar Submitted data from the form in mod_form.php
 *
 * @return int The id of the newly inserted openwebinar record
 */
function openwebinar_add_instance(stdClass $openwebinar) {
    global $DB;

    $openwebinar->timecreated = time();
    $openwebinar->broadcaster_identifier = md5(\mod_openwebinar\helper::generate_key());
    $openwebinar->id = $DB->insert_record('openwebinar', $openwebinar);

    $event = new stdClass();
    $event->name = $openwebinar->name;
    $event->description = format_module_intro('openwebinar', $openwebinar, $openwebinar->coursemodule);
    $event->courseid = $openwebinar->course;
    $event->groupid = 0;
    $event->userid = 0;
    $event->modulename = 'openwebinar';
    $event->instance = $openwebinar->id;
    $event->eventtype = 'openwebinartime';
    $event->timestart = $openwebinar->timeopen;
    $event->timeduration = $openwebinar->duration;

    calendar_event::create($event);

    return $openwebinar->id;
}

/**
 * Updates an instance of the openwebinar in the database
 *
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will update an existing instance with new data.
 *
 * @param stdClass $openwebinar An object from the form in mod_form.php
 *
 * @return boolean Success/Fail
 */
function openwebinar_update_instance(stdClass $openwebinar) {
    global $DB;

    $openwebinar->timemodified = time();
    $openwebinar->id = $openwebinar->instance;

    // You may have to add extra stuff in here.

    $result = $DB->update_record('openwebinar', $openwebinar);

    $event = new stdClass();

    if ($event->id = $DB->get_field('event', 'id', array(
            'modulename' => 'openwebinar',
            'instance' => $openwebinar->id
    ))
    ) {

        $event->name = $openwebinar->name;
        $event->description = format_module_intro('openwebinar', $openwebinar, $openwebinar->coursemodule);
        $event->timestart = $openwebinar->timeopen;
        $event->timeduration = $openwebinar->duration;

        $calendarevent = calendar_event::load($event->id);
        $calendarevent->update($event);
    }

    return $result;
}

/**
 * Removes an instance of the openwebinar from the database
 *
 * Given an ID of an instance of this module,
 * this function will permanently delete the instance
 * and any data that depends on it.
 *
 * @param int $id Id of the module instance
 *
 * @return boolean Success/Failure
 */
function openwebinar_delete_instance($id) {
    global $DB;

    if (!$openwebinar = $DB->get_record('openwebinar', array('id' => $id))) {
        return false;
    }

    // Delete any dependent records here.
    $DB->delete_records('openwebinar', array('id' => $openwebinar->id));

    // Remove the event.
    $DB->delete_records('event', array('modulename' => 'openwebinar', 'instance' => $openwebinar->id));

    // TODO: remove chatlogs.
    // TODO: remove useronline status.
    // TODO: remove attachments.
    return true;
}

/**
 * Returns a small object with summary information about what a
 * user has done with a given particular instance of this module
 * Used for user activity reports.
 *
 * $return->time = the time they did it
 * $return->info = a short text description
 *
 * @param stdClass $course      The course record
 * @param stdClass $user        The user record
 * @param cm_info|stdClass $mod The course module info object or record
 * @param stdClass $openwebinar The openwebinar instance record
 *
 * @return stdClass|null
 */
function openwebinar_user_outline($course, $user, $mod, $openwebinar) {
    // TODO: we should use this feature.
    $return = new stdClass();
    $return->time = 0;
    $return->info = '';

    return $return;
}

/**
 * Given a course and a time, this module should find recent activity
 * that has occurred in openwebinar activities and print it out.
 *
 * @param stdClass $course    The course record
 * @param bool $viewfullnames Should we display full names
 * @param int $timestart      Print activity since this timestamp
 *
 * @return boolean True if anything was printed, otherwise false
 */
function openwebinar_print_recent_activity($course, $viewfullnames, $timestart) {
    return false;
}

/**
 * Function to be run periodically according to the moodle cron
 *
 * This function searches for things that need to be done, such
 * as sending out mail, toggling flags etc ...
 *
 * Note that this has been deprecated in favour of scheduled task API.
 *
 * @return boolean
 */
function openwebinar_cron() {
    return true;
}

/**
 * Returns all other caps used in the module
 *
 * For example, this could be array('moodle/site:accessallgroups') if the
 * module uses that capability.
 *
 * @return array
 */
function openwebinar_get_extra_capabilities() {
    return array();
}

/* File API */

/**
 * Returns the lists of all browsable file areas within the given module context
 *
 * The file area 'intro' for the activity introduction field is added automatically
 * by {@link file_browser::get_file_info_context_module()}
 *
 * @param stdClass $course
 * @param stdClass $cm
 * @param stdClass $context
 *
 * @return array of [(string)filearea] => (string)description
 */
function openwebinar_get_file_areas($course, $cm, $context) {
    return array('');
}

/**
 * File browsing support for openwebinar file areas
 *
 * @package  mod_openwebinar
 * @category files
 *
 * @param file_browser $browser
 * @param array $areas
 * @param stdClass $course
 * @param stdClass $cm
 * @param stdClass $context
 * @param string $filearea
 * @param int $itemid
 * @param string $filepath
 * @param string $filename
 *
 * @return file_info instance or null if not found
 */
function openwebinar_get_file_info($browser, $areas, $course, $cm, $context, $filearea, $itemid, $filepath, $filename) {
    return null;
}

/**
 * Serves the files from the openwebinar file areas
 *
 * @note     we use own version in api class
 *
 * @package  mod_openwebinar
 * @category files
 *
 * @param stdClass $course    the course object
 * @param stdClass $cm        the course module object
 * @param stdClass $context   the openwebinar's context
 * @param string $filearea    the name of the file area
 * @param array $args         extra arguments (itemid, path)
 * @param bool $forcedownload whether or not force download
 * @param array $options      additional options affecting the file serving
 */
function openwebinar_pluginfile($course, $cm, $context, $filearea, array $args, $forcedownload, array $options = array()) {
    send_file_not_found();
}

/**
 * This function extends the settings navigation block for the site.
 *
 * It is safe to rely on PAGE here as we will only ever be within the module
 * context when this is called
 *
 * @param settings_navigation $settings
 * @param navigation_node $openwebinarnode
 *
 * @return void
 */
function openwebinar_extend_settings_navigation(settings_navigation $settings, navigation_node $openwebinarnode) {
    global $PAGE, $CFG;

    // We want to add these new nodes after the Edit settings node, and before the
    // Locally assigned roles node. Of course, both of those are controlled by capabilities.
    $keys = $openwebinarnode->get_children_key_list();
    $beforekey = null;
    $i = array_search('modedit', $keys);
    if ($i === false and array_key_exists(0, $keys)) {
        $beforekey = $keys[0];
    } else {
        if (array_key_exists($i + 1, $keys)) {
            $beforekey = $keys[$i + 1];
        }
    }

    if (has_capability('mod/openwebinar:manager', $PAGE->cm->context)) {
        $url = new moodle_url('/mod/openwebinar/user_activity.php', array('id' => $PAGE->cm->id));
        $node = navigation_node::create(get_string('user_activity', 'openwebinar'), $url, navigation_node::TYPE_SETTING, null,
                'mod_openwebinar_user_activity', new pix_icon('i/preview', ''));
        $openwebinarnode->add_node($node, $beforekey);
    }

    // Included here as we only ever want to include this file if we really need to.
    //require_once($CFG->libdir . '/questionlib.php');
    //question_extend_settings_navigation($openwebinarnode, $PAGE->cm->context)->trim_if_empty();
}