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
        show_skype_dialog: false,
        skype            : ""
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
        this.set_options(options);
        // Log the new options.
        this.log(this.options);

        // Add a count down if its not started.
        Y.on('domready', function () {
            this.add_countdown();

            if (this.options.show_skype_dialog && this.options.skype == '') {
                this.log('Show skype dialog');
                YUI().use('event-base', 'node', "panel", "cookie", "io", function (Y) {

                    var dialog = new Y.Panel({
                        contentBox : Y.Node.create('<div id="dialog" />'),
                        bodyContent: '<div class="message"><b>Skype bijwerken.</b><br/><br/><small>U skype gebruikersnaam is' +
                        ' onbekend. Om ons systeem accuraat te houden vragen wij u dit hieronder te wijzigen.' +
                        '<br/><br/><input name="skype" id="skype" placeholder="Skype username"/>' +
                        '</div>',
                        width      : 410,
                        zIndex     : 6,
                        centered   : true,
                        modal      : true, // modal behavior
                        render     : '.example',
                        visible    : false, // make visible explicitly with .show()
                        buttons    : {
                            footer: [
                                {
                                    name  : 'proceed',
                                    label : 'Opslaan',
                                    action: function () {
                                        console.log('save');

                                    //    e.preventDefault();
                                        var skype = Y.one("#skype").get('value');

                                        YUI().use('io-base', function (Y) {
                                            Y.io("/blocks/dshop/api/update_profile", {
                                                method : 'GET',
                                                data   : {
                                                    'skype': skype,
                                                },
                                                on     : {
                                                    success: function (id, o) {
                                                        log('success');
                                                        log(o);
                                                    },
                                                    failure: function (x, o) {
                                                        log('failure');
                                                        log(o);
                                                    }
                                                },
                                                headers: {
                                                    'Content-Type': 'application/json'
                                                }
                                            });
                                        });
                                        // save selection
                                        this.hide();
                                    }
                                }
                            ]
                        }
                    });

                    dialog.show();
                });
            }
        }, this);

    },

    /**
     * Add countdown clock
     */
    add_countdown: function () {
        "use strict";

        if (typeof countdown === 'undefined') {
            return;
        }

        var that = this;
        var start = new Date(this.options.timeopen * 1000);
        // Fix date to count from server time instead of local.
        var now = new Date(that.options.from * 1000);

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
        var interval = setInterval(function () {
            // 1 second.
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
