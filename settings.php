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
 * @package   mod_openwebinar
 * @copyright 2015 MoodleFreak.com
 * @author    Luuk Verhoeven
 **/
defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {

    $settings->add(new admin_setting_heading('openwebinar_server', '',
            get_string('setting:heading_server', 'openwebinar')));

    // Streaming URL.
    $settings->add(new admin_setting_configtext('openwebinar/streaming_server',
            get_string('setting:streaming_server', 'openwebinar'),
            get_string('setting:streaming_server_desc', 'openwebinar'),
            'your_domain.com/live/', PARAM_URL));

    // Socket.io server.
    $settings->add(new admin_setting_configtext('openwebinar/chat_server',
            get_string('setting:chat_server', 'openwebinar'),
            get_string('setting:chat_server_desc', 'openwebinar'),
            '', PARAM_URL));

    // Communication key.
    $settings->add(new admin_setting_configtext('openwebinar/shared_secret',
            get_string('setting:shared_secret', 'openwebinar'),
            get_string('setting:shared_secret_desc', 'openwebinar'),
            '', PARAM_RAW));

    $settings->add(new admin_setting_heading('openwebinar_instance_defaults', '',
            get_string('setting:heading_instance_defaults', 'openwebinar')));

    // Notification moments defaults.
    $settings->add(new admin_setting_configduration('openwebinar/reminder_1',
            get_string('setting:reminder_1', 'openwebinar'),
            get_string('setting:reminder_1_desc', 'openwebinar'), 3600, 3600));

    $settings->add(new admin_setting_configduration('openwebinar/reminder_2',
            get_string('setting:reminder_2', 'openwebinar'),
            get_string('setting:reminder_2_desc', 'openwebinar'), 86400, 86400));

    $settings->add(new admin_setting_configduration('openwebinar/reminder_3',
            get_string('setting:reminder_3', 'openwebinar'),
            get_string('setting:reminder_3_desc', 'openwebinar'), 604800, 604800));

    $settings->add(new admin_setting_configduration('openwebinar/reminder_4',
            get_string('setting:reminder_4', 'openwebinar'),
            get_string('setting:reminder_4_desc', 'openwebinar'), 604800 * 2, 604800 * 2));

    // Switches.
    $settings->add(new admin_setting_heading('openwebinar_instance_features', '',
            get_string('setting:heading_instance_features', 'openwebinar')));

    $settings->add(new admin_setting_configcheckbox('openwebinar/stream',
            get_string('setting:stream', 'openwebinar'),
            get_string('setting:stream_desc', 'openwebinar'), 1));

    $settings->add(new admin_setting_configcheckbox('openwebinar/chat',
            get_string('setting:chat', 'openwebinar'),
            get_string('setting:chat_desc', 'openwebinar'), 1));

    $settings->add(new admin_setting_configcheckbox('openwebinar/filesharing',
            get_string('setting:filesharing', 'openwebinar'),
            get_string('setting:filesharing_desc', 'openwebinar'), 1));

    $settings->add(new admin_setting_configcheckbox('openwebinar/filesharing_student',
            get_string('setting:filesharing_student', 'openwebinar'),
            get_string('setting:filesharing_student_desc', 'openwebinar'), 1));

    $settings->add(new admin_setting_configcheckbox('openwebinar/showuserpicture',
            get_string('setting:showuserpicture', 'openwebinar'),
            get_string('setting:showuserpicture_desc', 'openwebinar'), 1));

    $settings->add(new admin_setting_configcheckbox('openwebinar/userlist',
            get_string('setting:userlist', 'openwebinar'),
            get_string('setting:userlist_desc', 'openwebinar'), 1));

    $settings->add(new admin_setting_configcheckbox('openwebinar/ajax_timer',
            get_string('setting:ajax_timer', 'openwebinar'),
            get_string('setting:ajax_timer_desc', 'openwebinar'), 1));

    $settings->add(new admin_setting_configcheckbox('openwebinar/emoticons',
            get_string('setting:emoticons', 'openwebinar'),
            get_string('setting:emoticons_desc', 'openwebinar'), 1));

    $settings->add(new admin_setting_configcheckbox('openwebinar/debugjs',
            get_string('setting:debugjs', 'openwebinar'),
            get_string('setting:debugjs_desc', 'openwebinar'), 0));

    $settings->add(new admin_setting_configcheckbox('openwebinar/hls',
            get_string('setting:hls', 'openwebinar'),
            get_string('setting:hls_desc', 'openwebinar'), 0));

    $settings->add(new admin_setting_configcheckbox('openwebinar/show_skype_popup',
            get_string('setting:show_skype_popup', 'openwebinar'),
            get_string('setting:show_skype_popup', 'openwebinar'), 1));

}
