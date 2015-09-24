/**
 * Module helper JS function will be listed here
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @package mod_openwebinar
 * @copyright 2015 MoodleFreak.com
 * @author Luuk Verhoeven
 **/
/*jslint browser: true, white: true, vars: true, regexp: true*/
/*global  M, Y, console, YUI , countdown*/
M.mod_openwebinar = M.mod_openwebinar || {};
M.mod_openwebinar.base = {

    /**
     * Openwebinar variables
     * @type Object
     * @protected
     */
    options: {
        debugjs : true,
        duration: 0,
        from    : 0,
        timeopen: 0,
        cmid    : 0,
        is_ended: false
    },

    /**
     * Internal logging
     * @param val
     */
    log: function (val) {
        "use strict";
        // check if we can show the log
        if (!this.options.debugjs) {
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
     * Init the room.
     * @param {Object} options
     */
    init: function (options) {
        "use strict";
        this.set_options(options);
        // log the new options
        this.log(this.options);

        // add a count down if its not started
        Y.on('domready', function () {
            this.add_countdown();
        }, this);
    },

    /**
     * Add countdown clock
     */
    add_countdown: function () {
        "use strict";
        var that = this;
        var start = new Date(this.options.timeopen * 1000);
        // fix date to count from server time instead of local
        var now = new Date(that.options.from * 1000);


        var timerspan = document.getElementById('pageTimer');
        var interval = setInterval(function () {
            // +1 second
            now.setSeconds(now.getSeconds() + 1);
            var ts = countdown(start, now, countdown.HOURS | countdown.MINUTES | countdown.SECONDS, 6, 0);
            that.log(ts.value);
            if ((ts.value > 0)) {
                that.log('clearInterval');
                clearInterval(interval);
                window.location = M.cfg.wwwroot + "/mod/openwebinar/view.php?id=" + that.options.cmid;
            } else {
                timerspan.innerHTML = ts.toHTML("strong");
            }
        }, 1000);
    },

    /**
     * Set options base on listed options
     * @param {object} options
     */
    set_options: function (options) {
        "use strict";
        var key, vartype;
        for (key in this.options) {
            if (this.options.hasOwnProperty(key) && options.hasOwnProperty(key)) {

                // casting to prevent errors
                vartype = typeof this.options[key];
                if (vartype === "boolean") {
                    this.options[key] = Boolean(options[key]);
                }
                else if (vartype === 'number') {
                    this.options[key] = Number(options[key]);
                }
                else if (vartype === 'string') {
                    this.options[key] = String(options[key]);
                }
                // skip all other types
            }
        }
    }
};