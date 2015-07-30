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
 * cron
 *
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @package   mod_webcast
 * @copyright 2015 MoodleFreak.com
 * @author    Luuk Verhoeven
 **/
namespace mod_webcast;

class cron{

    /**
     * Room open max duration
     * @const MAX_DURATION
     */
    const MAX_DURATION = 28800; // 8 hours

    /**
     * Debug
     * @var bool
     */
    protected $debug = false;

    function __construct(){

    }

    /**
     * c
     * @return boolean
     */
    public function isDebug() {
        return $this->debug;
    }

    /**
     * @param boolean $debug
     *
     * @return cron
     */
    public function setDebug($debug) {
        $this->debug = (bool) $debug;

        return $this;
    }

    /**
     * close unclosed rooms
     * @return void
     */
    public function auto_close(){
        global $DB;
        $now = time();
        $webcasts = $DB->get_records('webcast', array('is_ended' => 0) );
        if($webcasts){
            foreach($webcasts as $webcast){

                // we must end this webcast
                if($now > $webcast->timeopen + self::MAX_DURATION){

                    // set to closed
                    $obj = new \stdClass();
                    $obj->id = $webcast->id;
                    $obj->is_ended = 1;
                    $DB->update_record('webcast' , $obj);

                    mtrace('Closed -> ' . $webcast->name);
                }
            }
        }
    }

}