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
 * helper
 *
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @package   mod_webcast
 * @copyright 2015 MoodleFreak.com
 * @author    Luuk Verhoeven
 **/
namespace mod_webcast;

defined('MOODLE_INTERNAL') || die();

class helper {

    /**
     * The broadcast date has passed
     *
     * @const WEBCAST_BROADCASTED
     */
    const WEBCAST_CLOSED = 3;

    /**
     * The broadcast is been marked as ended
     *
     * @const WEBCAST_BROADCASTED
     */
    const WEBCAST_BROADCASTED = 2;

    /**
     * The activity is open for broadcast
     *
     * @const WEBCAST_LIVE
     */
    const WEBCAST_LIVE = 1;

    /**
     * Date is yet to come
     *
     * @const WEBCAST_NOT_BROADCASTED
     */
    const WEBCAST_NOT_BROADCASTED = 0;

    /**
     * get permission of a user
     *
     * @param $context
     * @param \stdClass $webcast
     * @param bool $user
     *
     * @return stdClass
     * @throws Exception
     * @throws \coding_exception
     */
    static public function get_permissions($context, $webcast, $user = false) {

        global $USER;

        // get correct user object
        if (!is_object($user)) {
            $user = $USER;
        }

        if (empty($webcast)) {
            throw new Exception(get_string('error:webcast_notfound', 'mod_webcast'));
        }

        // build internal caching
        static $obj = array();
        if (!empty($obj[$user->id])) {
            return $obj[$user->id];
        }

        // echo 'query ';

        $access = new \stdClass();

        // is broadcaster
        $access->broadcaster = ($user->id == $webcast->broadcaster) ? true : false;

        // is manager
        $access->manager = has_capability('mod/webcast:manager', $context, $user);

        // is teacher
        $access->teacher = has_capability('mod/webcast:teacher', $context, $user);

        // can upload
        // @todo make this optional
        $access->upload = true;

        // can chat
        // @todo make this optional
        $access->chat = true;

        // reference to scope var
        $obj[$user->id] = &$access;

        return $obj[$user->id];
    }

    /**
     * Get the status
     *
     * @param stdClass $webcast
     *
     * @return int
     * @throws Exception
     * @throws \coding_exception
     */
    public static function get_webcast_status($webcast) {

        // check 
        if (empty($webcast)) {
            throw new Exception(get_string('error:webcast_notfound', 'mod_webcast'));
        }

        $now = time();

        if (!empty($webcast->is_ended)) {
            return self::WEBCAST_BROADCASTED;
        } elseif ($now >= $webcast->timeopen) {
            print_r(date("d-m-Y H:i:s" , $webcast->timeopen));
            return self::WEBCAST_LIVE;
        }

        return self::WEBCAST_NOT_BROADCASTED;

    }

    /**
     * generate unique identifier
     *
     * @return string
     */
    public static function generate_key() {
        return md5(microtime() . rand());
    }

}