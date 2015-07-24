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
 * Global settings
 *
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @package   mod_webcast
 * @copyright 2015 MoodleFreak.com
 * @author    Luuk Verhoeven
 **/
defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {

    $settings->add(new admin_setting_heading('webcast_server', '',
        get_string('setting:heading_server', 'webcast')));

    // Streaming URL
    $settings->add(new admin_setting_configtext('webcast/streaming_server',
        get_string('setting:streaming_server', 'webcast'),
        get_string('setting:streaming_server_desc', 'webcast'),
        '', PARAM_URL));

    // Socket.io server
    $settings->add(new admin_setting_configtext('webcast/chat_server',
        get_string('setting:chat_server', 'webcast'),
        get_string('setting:chat_server_desc', 'webcast'),
        '', PARAM_URL));

    // Communication key
    $settings->add(new admin_setting_configtext('webcast/shared_secret',
        get_string('setting:shared_secret', 'webcast'),
        get_string('setting:shared_secret_desc', 'webcast'),
        '', PARAM_RAW));

    $settings->add(new admin_setting_heading('webcast_instance_defaults', '',
        get_string('setting:heading_instance_defaults', 'webcast')));


    // Notification moments defaults
    $settings->add(new admin_setting_configduration('webcast/reminder_1',
        get_string('setting:reminder_1', 'webcast'),
        get_string('setting:reminder_1_desc', 'webcast'), 3600, 3600));

    $settings->add(new admin_setting_configduration('webcast/reminder_2',
        get_string('setting:reminder_2', 'webcast'),
        get_string('setting:reminder_2_desc', 'webcast'), 86400, 86400));

    $settings->add(new admin_setting_configduration('webcast/reminder_3',
        get_string('setting:reminder_3', 'webcast'),
        get_string('setting:reminder_3_desc', 'webcast'), 604800, 604800));

    // switches
    $settings->add(new admin_setting_heading('webcast_instance_features', '',
        get_string('setting:heading_instance_features', 'webcast')));

    $settings->add(new admin_setting_configcheckbox('webcast/stream',
        get_string('setting:stream', 'webcast'),
        get_string('setting:stream_desc', 'webcast'), 1));

    $settings->add(new admin_setting_configcheckbox('webcast/chat',
        get_string('setting:chat', 'webcast'),
        get_string('setting:chat_desc', 'webcast'), 1));

    $settings->add(new admin_setting_configcheckbox('webcast/filesharing',
        get_string('setting:filesharing', 'webcast'),
        get_string('setting:filesharing_desc', 'webcast'), 1));

    $settings->add(new admin_setting_configcheckbox('webcast/filesharing_student',
        get_string('setting:filesharing_student', 'webcast'),
        get_string('setting:filesharing_student_desc', 'webcast'), 1));

    $settings->add(new admin_setting_configcheckbox('webcast/showuserpicture',
        get_string('setting:showuserpicture', 'webcast'),
        get_string('setting:showuserpicture_desc', 'webcast'), 1));

}
