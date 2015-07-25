
/**
 * Module helper JS function will be listed here
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @package mod_webcast
 * @copyright 2015 MoodleFreak.com
 * @author Luuk Verhoeven
 **/

M.mod_webcast = M.mod_webcast || {};
M.mod_webcast.base = {

    /**
     * Webcast variables
     */
    options      : {
        debugjs : true,
        chat : false,
        duration : 0,
        timeopen : 0,
        filesharing : false,
        filesharing_student : false,
        is_ended : false,
        showuserpicture : false,
        stream : false,
        userlist : false
    },

    /**
     * Internal logging
     * @param val
     */
    log          : function (val) {

        // check if we can show the log
        if(!this.debugjs){
            return;
        }

        try {
            Y.log(val);
        } catch (e) {
            try {
                console.log(val);
            } catch (exc) {
                throw exc;
            }
        }
    },

    /**
     * Init
     */
    init         : function (options) {
        "use strict";
        this.set_options(options);
    },

    /**
     * add countdown clock
     */
    add_countdown: function () {

    },

    /**
     * Set options base on listed options
     * @param options
     */
    set_options  : function (options) {

        for (var key in this.options) {

            if(typeof options[key] !== "undefined"){

                // casting to prevent errors
                var vartype = typeof this.options[key];
                if(vartype === "boolean"){
                    this.options[key] = Boolean(options[key]);
                }else if(vartype === 'number'){
                    this.options[key] = Number(options[key]);
                }
                // skip all other types
            }
        }
        // toggle options
        this.log(this.options);
    }
};