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
 * This file keeps track of upgrades to the openwebinar module
 *
 * Sometimes, changes between versions involve alterations to database
 * structures and other major things that may break installations. The upgrade
 * function in this file will attempt to perform all the necessary actions to
 * upgrade your older installation to the current version. If there's something
 * it cannot do itself, it will tell you what you need to do.  The commands in
 * here will all be database-neutral, using the functions defined in DLL libraries.
 *
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @package   mod_openwebinar
 * @copyright 2015 MoodleFreak.com
 * @author    Luuk Verhoeven
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Execute openwebinar upgrade from the given old version
 *
 * @param int $oldversion
 *
 * @return bool
 */
function xmldb_openwebinar_upgrade($oldversion) {
    global $CFG, $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2016111300) {

        // Define field reminder_4 to be added to openwebinar.
        $table = new xmldb_table('openwebinar');
        $field = new xmldb_field('reminder_4', XMLDB_TYPE_INTEGER, '9', null, XMLDB_NOTNULL, null, '0', 'reminder_3');

        // Conditionally launch add field reminder_4.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $field = new xmldb_field('reminder_4_send', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '0', 'reminder_3_send');

        // Conditionally launch add field reminder_4_send.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Openwebinar savepoint reached.
        upgrade_mod_savepoint(true, 2016111300, 'openwebinar');
    }

    if ($oldversion < 2016120501) {

        // Define field feedback_id to be added to openwebinar.
        $table = new xmldb_table('openwebinar');
        $field = new xmldb_field('feedback_id', XMLDB_TYPE_INTEGER, '11',
                null, null, null, '0', 'is_ended');

        // Conditionally launch add field feedback_id.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field feedback_send to be added to openwebinar.
        $table = new xmldb_table('openwebinar');
        $field = new xmldb_field('feedback_send', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0', 'feedback_id');

        // Conditionally launch add field feedback_send.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Openwebinar savepoint reached.
        upgrade_mod_savepoint(true, 2016120501, 'openwebinar');
    }

    return true;
}
