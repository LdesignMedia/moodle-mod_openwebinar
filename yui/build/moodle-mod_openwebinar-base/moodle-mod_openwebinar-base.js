YUI.add('moodle-mod_openwebinar-base', function (Y, NAME) {

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
     * Timer holder
     */
    timerWebinar : 0,
    /**
     * Openwebinar variables
     * @type Object
     */
    options: {
        debugjs          : true,
        duration         : 0,
        from             : 0,
        timeopen         : 0,
        cmid             : 0,
        is_ended         : false,
    },

    /**
     * Internal logging
     * @param val
     */
    log: function (val) {
        "use strict";
        // Check if we can show the log.
        if (!this.options.debugjs) {
            return;
        }
        try {
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
        var that = this;

        this.set_options(options);
        // Log the new options.
        this.log(this.options);

        // Add a count down if its not started.
        Y.on('domready', function () {
            that.log('domready');
            that.add_countdown();
        });
    },

    /**
     * Add countdown clock
     */
    add_countdown: function () {
        "use strict";

        if (typeof countdown === 'undefined' || this.timerWebinar > 0) {
            return;
        }

        var that = this;

        var start = new Date(that.options.timeopen * 1000);
        // Fix date to count from server time instead of local.
        var now = new Date(that.options.from * 1000);

        that.log('Start:' + start);
        that.log('Now:' + now);

        // Set countdown locals.
        countdown.setLabels(
            M.util.get_string('js:countdown_line1', 'openwebinar', {}),
            M.util.get_string('js:countdown_line2', 'openwebinar', {}),
            M.util.get_string('js:countdown_line3', 'openwebinar', {}),
            ', ',
            '',
            function (n) {
                return n.toString();
            });

        var timerspan = document.getElementById('pageTimer');
        var updateTimerUi = function () {
            // 1 second.
            now.setSeconds(now.getSeconds() + 1);

            var ts = countdown(start, now, countdown.HOURS | countdown.MINUTES | countdown.SECONDS, 6, 0);
            that.log('TS value : ' + ts.value);
            that.log(that.timerWebinar);
            if ((ts.value > 0)) {
                that.log('clearInterval');
                clearInterval(that.timerWebinar);
                window.location = M.cfg.wwwroot + "/mod/openwebinar/view.php?id=" + that.options.cmid;
            } else {
                timerspan.innerHTML = ts.toHTML("strong");
            }
        };

        this.timerWebinar = setInterval(updateTimerUi, 1000);
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

                // Casting to prevent errors.
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
                // Skip all other types.
            }
        }
    }
};

}, '@VERSION@', {"requires": ["base", "node"]});
