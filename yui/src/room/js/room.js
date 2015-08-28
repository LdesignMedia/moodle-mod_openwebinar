/**
 * The broadcast room
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @package mod_webcast
 * @copyright 2015 MoodleFreak.com
 * @author Luuk Verhoeven
 **/
/*jslint browser: true, white: true, vars: true, regexp: true*/
/*global  M, Y, videojs, console, io, tinyscrollbar, alert, YUI, confirm, Audio */

/**
 * This object is public accessible
 * @type {{}|*}
 */
M.mod_webcast = M.mod_webcast || {};
// @todo Don't allow this to be public accessible
M.mod_webcast.room = {

    /**
     * Emoticons mapping
     * @type Object
     * @protected
     */
    emoticons: {
        "smile"         : {
            "title": "Smile",
            "codes": [":)", ":=)", ":-)"]
        },
        "sad-smile"     : {
            "title": "Sad Smile",
            "codes": [":(", ":=(", ":-("]
        },
        "big-smile"     : {
            "title": "Big Smile",
            "codes": [":D", ":=D", ":-D", ":d", ":=d", ":-d"]
        },
        "cool"          : {
            "title": "Cool",
            "codes": ["8)", "8=)", "8-)", "B)", "B=)", "B-)", "(cool)"]
        },
        "wink"          : {
            "title": "Wink",
            "codes": [":o", ";)", ":=o", ":-o", ":O", ":=O", ":-O"]
        },
        "crying"        : {
            "title": "Crying",
            "codes": [";(", ";-(", ";=("]
        },
        "sweating"      : {
            "title": "Sweating",
            "codes": ["(sweat)", "(:|"]
        },
        "speechless"    : {
            "title": "Speechless",
            "codes": [":|", ":=|", ":-|"]
        },
        "kiss"          : {
            "title": "Kiss",
            "codes": [":*", ":=*", ":-*"]
        },
        "tongue-out"    : {
            "title": "Tongue Out",
            "codes": [":P", ":=P", ":-P", ":p", ":=p", ":-p"]
        },
        "blush"         : {
            "title": "Blush",
            "codes": ["(blush)", ":$", ":-$", ":=$", ":\">"]
        },
        "wondering"     : {
            "title": "Wondering",
            "codes": [":^)"]
        },
        "sleepy"        : {
            "title": "Sleepy",
            "codes": ["|-)", "I-)", "I=)", "(snooze)"]
        },
        "dull"          : {
            "title": "Dull",
            "codes": ["|(", "|-(", "|=("]
        },
        "in-love"       : {
            "title": "In love",
            "codes": ["(inlove)"]
        },
        "evil-grin"     : {
            "title": "Evil grin",
            "codes": ["]:)", ">:)", "(grin)"]
        },
        "talking"       : {
            "title": "Talking",
            "codes": ["(talk)"]
        },
        "yawn"          : {
            "title": "Yawn",
            "codes": ["(yawn)", "|-()"]
        },
        "puke"          : {
            "title": "Puke",
            "codes": ["(puke)", ":&", ":-&", ":=&"]
        },
        "doh!"          : {
            "title": "Doh!",
            "codes": ["(doh)"]
        },
        "angry"         : {
            "title": "Angry",
            "codes": [":@", ":-@", ":=@", "x(", "x-(", "x=(", "X(", "X-(", "X=("]
        },
        "it-wasnt-me"   : {
            "title": "It wasn't me",
            "codes": ["(wasntme)"]
        },
        "party"         : {
            "title": "Party!!!",
            "codes": ["(party)"]
        },
        "worried"       : {
            "title": "Worried",
            "codes": [":S", ":-S", ":=S", ":s", ":-s", ":=s"]
        },
        "mmm"           : {
            "title": "Mmm...",
            "codes": ["(mm)"]
        },
        "nerd"          : {
            "title": "Nerd",
            "codes": ["8-|", "B-|", "8|", "B|", "8=|", "B=|", "(nerd)"]
        },
        "lips-sealed"   : {
            "title": "Lips Sealed",
            "codes": [":x", ":-x", ":X", ":-X", ":#", ":-#", ":=x", ":=X", ":=#"]
        },
        "hi"            : {
            "title": "Hi",
            "codes": ["(hi)"]
        },
        "call"          : {
            "title": "Call",
            "codes": ["(call)"]
        },
        "devil"         : {
            "title": "Devil",
            "codes": ["(devil)"]
        },
        "angel"         : {
            "title": "Angel",
            "codes": ["(angel)"]
        },
        "envy"          : {
            "title": "Envy",
            "codes": ["(envy)"]
        },
        "wait"          : {
            "title": "Wait",
            "codes": ["(wait)"]
        },
        "bear"          : {
            "title": "Bear",
            "codes": ["(bear)", "(hug)"]
        },
        "make-up"       : {
            "title": "Make-up",
            "codes": ["(makeup)", "(kate)"]
        },
        "covered-laugh" : {
            "title": "Covered Laugh",
            "codes": ["(giggle)", "(chuckle)"]
        },
        "clapping-hands": {
            "title": "Clapping Hands",
            "codes": ["(clap)"]
        },
        "thinking"      : {
            "title": "Thinking",
            "codes": ["(think)", ":?", ":-?", ":=?"]
        },
        "bow"           : {
            "title": "Bow",
            "codes": ["(bow)"]
        },
        "rofl"          : {
            "title": "Rolling on the floor laughing",
            "codes": ["(rofl)"]
        },
        "whew"          : {
            "title": "Whew",
            "codes": ["(whew)"]
        },
        "happy"         : {
            "title": "Happy",
            "codes": ["(happy)"]
        },
        "smirking"      : {
            "title": "Smirking",
            "codes": ["(smirk)"]
        },
        "nodding"       : {
            "title": "Nodding",
            "codes": ["(nod)"]
        },
        "shaking"       : {
            "title": "Shaking",
            "codes": ["(shake)"]
        },
        "punch"         : {
            "title": "Punch",
            "codes": ["(punch)"]
        },
        "emo"           : {
            "title": "Emo",
            "codes": ["(emo)"]
        },
        "yes"           : {
            "title": "Yes",
            "codes": ["(y)", "(Y)", "(ok)"]
        },
        "no"            : {
            "title": "No",
            "codes": ["(n)", "(N)"]
        },
        "handshake"     : {
            "title": "Shaking Hands",
            "codes": ["(handshake)"]
        },
        "skype"         : {
            "title": "Skype",
            "codes": ["(skype)", "(ss)"]
        },
        "heart"         : {
            "title": "Heart",
            "codes": ["(h)", "<3", "(H)", "(l)", "(L)"]
        },
        "broken-heart"  : {
            "title": "Broken heart",
            "codes": ["(u)", "(U)"]
        },
        "mail"          : {
            "title": "Mail",
            "codes": ["(e)", "(m)"]
        },
        "flower"        : {
            "title": "Flower",
            "codes": ["(f)", "(F)"]
        },
        "rain"          : {
            "title": "Rain",
            "codes": ["(rain)", "(london)", "(st)"]
        },
        "sun"           : {
            "title": "Sun",
            "codes": ["(sun)"]
        },
        "time"          : {
            "title": "Time",
            "codes": ["(o)", "(O)", "(time)"]
        },
        "music"         : {
            "title": "Music",
            "codes": ["(music)"]
        },
        "movie"         : {
            "title": "Movie",
            "codes": ["(~)", "(film)", "(movie)"]
        },
        "phone"         : {
            "title": "Phone",
            "codes": ["(mp)", "(ph)"]
        },
        "coffee"        : {
            "title": "Coffee",
            "codes": ["(coffee)"]
        },
        "pizza"         : {
            "title": "Pizza",
            "codes": ["(pizza)", "(pi)"]
        },
        "cash"          : {
            "title": "Cash",
            "codes": ["(cash)", "(mo)", "($)"]
        },
        "muscle"        : {
            "title": "Muscle",
            "codes": ["(muscle)", "(flex)"]
        },
        "cake"          : {
            "title": "Cake",
            "codes": ["(^)", "(cake)"]
        },
        "beer"          : {
            "title": "Beer",
            "codes": ["(beer)"]
        },
        "drink"         : {
            "title": "Drink",
            "codes": ["(d)", "(D)"]
        },
        "dance"         : {
            "title": "Dance",
            "codes": ["(dance)", "\\o/", "\\:D/", "\\:d/"]
        },
        "ninja"         : {
            "title": "Ninja",
            "codes": ["(ninja)"]
        },
        "star"          : {
            "title": "Star",
            "codes": ["(*)"]
        },
        "mooning"       : {
            "title": "Mooning",
            "codes": ["(mooning)"]
        },
        "finger"        : {
            "title": "Finger",
            "codes": ["(finger)"]
        },
        "bandit"        : {
            "title": "Bandit",
            "codes": ["(bandit)"]
        },
        "drunk"         : {
            "title": "Drunk",
            "codes": ["(drunk)"]
        },
        "smoking"       : {
            "title": "Smoking",
            "codes": ["(smoking)", "(smoke)", "(ci)"]
        },
        "toivo"         : {
            "title": "Toivo",
            "codes": ["(toivo)"]
        },
        "rock"          : {
            "title": "Rock",
            "codes": ["(rock)"]
        },
        "headbang"      : {
            "title": "Headbang",
            "codes": ["(headbang)", "(banghead)"]
        },
        "bug"           : {
            "title": "Bug",
            "codes": ["(bug)"]
        },
        "fubar"         : {
            "title": "Fubar",
            "codes": ["(fubar)"]
        },
        "poolparty"     : {
            "title": "Poolparty",
            "codes": ["(poolparty)"]
        },
        "swearing"      : {
            "title": "Swearing",
            "codes": ["(swear)"]
        },
        "tmi"           : {
            "title": "TMI",
            "codes": ["(tmi)"]
        },
        "heidy"         : {
            "title": "Heidy",
            "codes": ["(heidy)"]
        },
        "myspace"       : {
            "title": "MySpace",
            "codes": ["(MySpace)"]
        },
        "malthe"        : {
            "title": "Malthe",
            "codes": ["(malthe)"]
        },
        "tauri"         : {
            "title": "Tauri",
            "codes": ["(tauri)"]
        },
        "priidu"        : {
            "title": "Priidu",
            "codes": ["(priidu)"]
        }
    },

    emoticons_regex: null,

    /**
     * Emoticons container
     * @type Object
     * @protected
     */
    emoticons_map: {},

    /**
     * Webcast variables
     * @type Object
     * @protected
     */
    options: {
        debugjs               : false,
        preventclosewaring    : false,
        chat                  : false,
        chatnoticesound       : true,
        duration              : 0,
        timeopen              : 0,
        cmid                  : 0,
        courseid              : 0,
        webcastid             : 0,
        filesharing           : false,
        filesharing_student   : false,
        viewhistory           : false,
        is_ended              : false,
        showuserpicture       : false,
        enable_chat_sound     : true,
        stream                : false,
        broadcaster           : -999,
        is_broadcaster        : false,
        broadcastkey          : "broadcastkey",
        broadcaster_identifier: "",
        shared_secret         : "",
        streaming_server      : "",
        chat_server           : "",
        fullname              : "",
        ajax_path             : "",
        usertype              : "",
        userid                : 0,
        userlist              : false,
        ajax_timer            : false,
        enable_emoticons      : true,
        questions             : true,
        hls                   : false
    },

    /**
     * A reference to the scrollview used in this module
     * @type tinyscrollbar
     * @protected
     */
    scrollbar_userlist: null,

    /**
     * A reference to the scrollview used in this module
     * @type tinyscrollbar
     * @protected
     */
    scrollview_chatlist: null,

    /**
     * A reference to the scrollbar for file overview
     * @type tinyscrollbar
     * @protected
     */
    scrollbar_fileoverview: null,

    /**
     * A reference to the scrollbar for chatlist
     * @type tinyscrollbar
     * @protected
     */
    scrollbar_chatlist: null,

    /**
     * Socket
     */
    socket: null,

    /**
     * Files
     * @type Object
     * @protected
     */
    files_uploaded_hashes: {},

    /**
     * Shortcode regex
     * Sample [file 19845771897512389057123589027301201]
     * @type RegExp
     * @protected
     */
    shortcode_regex: /\[([\w\-_]+)([^\]]*)?\](?:(.+?)?\[\/\1\])?/g,

    /**
     * Bool to check if we are connected
     * @type Boolean
     * @protected
     */
    socket_is_connected: null,

    /**
     * Videojs player
     * @type videojs
     * @protected
     */
    player: null,

    /**
     * New message
     * @type videojs
     * @protected
     */
    audio_newmessage: null,

    /**
     * Chat template
     * @type Object
     * @protected
     */
    chatobject: {
        cmid         : 0,
        useragent    : {},
        courseid     : 0,
        webcastid    : 0,
        userid       : 0,
        fullname     : "",
        broadcastkey : "",
        room         : "_public",
        shared_secret: "",
        hostname     : "",
        message      : "",
        usertype     : "guest"
    },
    /**
     * Node containers
     * @type Object
     * @protected
     */
    nodeholder: {
        chatlist          : null,
        userlist          : null,
        topmenu           : null,
        leftsidemenu      : null,
        loadhistorybtn    : null,
        userlist_counter  : null,
        sendbutton        : null,
        body              : null,
        userlist_viewport : null,
        chatlist_viewport : null,
        filemanagerdialog : null,
        fileoverviewdialog: null,
        fileoverview      : null,
        emoticonsdialog   : null,
        questionmanager   : null,
        addquestionbtn    : null,
        questionoverview  : null,
        noticebar         : null,
        message           : null
    },
    /**
     * Internal logging
     * @param {*} val
     */
    log       : function (val) {
        "use strict";

        // check if we can show the log
        if (!M.mod_webcast.room.options.debugjs) {
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

        // Make sure videojs is loaded
        if (!videojs) {
            M.mod_webcast.room.log('wait..');
            setTimeout(function () {
                M.mod_webcast.room.init(options);
            }, 100);
        }

        // Set the filtered options
        this.set_options(options);

        // log the new options
        this.log(this.options);

        // load message sound
        this.audio_newmessage = new Audio(M.cfg.wwwroot + '/mod/webcast/sound/newmessage.mp3');

        // build room components when the dom is completely loaded
        Y.on('domready', function () {
            this.log('domready');

            this.build_room();

            Y.one('#webcast-loading').hide();
        }, this);
    },

    /**
     * Build the room and add the components that are enabled
     */
    build_room: function () {
        "use strict";
        this.log('build_room');

        // if room is ended prevent some things from happening
        if (this.options.is_ended) {
            this.options.userlist = false;
        }

        // Set some important nodes to this class reference
        this.nodeholder.body = Y.one("body");
        this.nodeholder.topmenu = Y.one("#webcast-topbar-left");
        this.nodeholder.leftsidemenu = Y.one("#webcast-left-menu");

        // Show a menu when clicking on topmenu
        this.nodeholder.topmenu.on('click', function () {
            M.mod_webcast.room.log('Open topmenu');

            // Menu arrow
            if ((M.mod_webcast.room.nodeholder.leftsidemenu.get('offsetWidth') === 0 &&
                M.mod_webcast.room.nodeholder.leftsidemenu.get('offsetHeight') === 0) ||
                M.mod_webcast.room.nodeholder.leftsidemenu.get('display') === 'none') {

                M.mod_webcast.room.log('show');

                YUI().use('anim', function (Y) {
                    var a = new Y.Anim(
                        {
                            node: M.mod_webcast.room.nodeholder.leftsidemenu,
                            from: {
                                left: -200
                            },

                            to      : {
                                left: 0
                            },
                            duration: 0.3
                        }
                    );
                    a.get('node').show();
                    a.on('end', function () {
                        Y.one("#webcast-topbar-left .arrow").setHTML('&#x25C4;');
                    });
                    a.run();
                });

            } else {
                M.mod_webcast.room.log('hide');

                YUI().use('anim', function (Y) {
                    var a = new Y.Anim(
                        {
                            node    : M.mod_webcast.room.nodeholder.leftsidemenu,
                            from    : {
                                left: 0
                            },
                            to      : {
                                left: -200
                            },
                            duration: 0.3
                        }
                    );

                    a.on('end', function () {
                        a.get('node').hide();
                        Y.one("#webcast-topbar-left .arrow").setHTML('&#x25BA;');
                    });
                    a.run();
                });
            }
        });

        // setting helpers
        Y.all('.webcast-toggle').on('click', function (e) {
            this.set_user_setting(e.currentTarget.get('id'), e.currentTarget.get('checked'));
        }, this);

        // Prevent scrollbars
        this.nodeholder.body.setStyle('overflow', 'hidden');

        // Connect to socket
        this.connect_to_socket();

        // add stream component
        if (this.options.stream) {
            this.add_video();
        }

        // add the userlist
        if (this.options.userlist) {
            this.add_userlist();
        } else {
            Y.one('#webcast-userlist-holder').hide();
        }

        if (this.options.enable_emoticons) {
            this.load_emoticons();
        }

        // add the chat
        if (this.options.chat) {
            this.add_chat();
        } else {
            // remove chat components
            Y.all('#webcast-chat-holder .webcast-header, #webcast-message , #webcast-send , #webcast-emoticon-icon').hide();
        }

        // add file sharing
        if ((this.options.filesharing_student && this.options.broadcaster === this.options.userid) ||
            this.options.filesharing_student) {

            this.add_fileshare();
        }

        // add the question manager
        if (this.options.questions) {
            this.add_question_manager();
        }

        // add action on the leave button
        Y.one('#webcast-leave').on('click', function () {

            if (this.options.is_ended) {
                this.options.preventclosewaring = true;
                window.location = M.cfg.wwwroot + "/mod/webcast/view.php?id=" + this.options.cmid;
            } else if (this.options.is_broadcaster) {
                // confirm closing
                var a = confirm(M.util.get_string('js:ending_webcast', 'webcast', {}));
                if (a) {
                    // close chat with a API call
                    Y.io(this.options.ajax_path, {
                        method : 'GET',
                        data   : {
                            'sesskey': M.cfg.sesskey,
                            'action' : "endwebcast",
                            'extra1' : M.mod_webcast.room.options.courseid,
                            'extra2' : M.mod_webcast.room.options.webcastid
                        },
                        on     : {
                            success: function (id, o) {
                                M.mod_webcast.room.log(id);
                                try {
                                    var response = Y.JSON.parse(o.responseText);
                                    M.mod_webcast.room.log(response);
                                    if (response.status) {

                                        // Close the room on chat server // chat server will notice all clients
                                        M.mod_webcast.room.chatobject.broadcaster_identifier = M.mod_webcast.room.options.broadcaster_identifier;
                                        M.mod_webcast.room.socket.emit("ending", M.mod_webcast.room.chatobject, function (response) {
                                            M.mod_webcast.room.log(response);
                                        });
                                    }
                                } catch (e) {
                                    // exception
                                    M.mod_webcast.room.log(e);
                                }
                            }
                        },
                        headers: {
                            'Content-Type': 'application/json'
                        }
                    });
                }
            } else {
                window.location = M.cfg.wwwroot + "/mod/webcast/view.php?id=" + this.options.cmid;
            }
        }, this);

        // scaling listener
        this.add_event(window, "resize", function () {
            M.mod_webcast.room.scale_room();
        });

        // Message before closing
        this.warning_message_closing_window();

        // first time scale the room
        setTimeout(function () {
            M.mod_webcast.room.scale_room();
        }, 300);

        if (this.options.ajax_timer && !this.options.is_ended) {
            setInterval(function () {
                M.mod_webcast.room.ajax_timer_ping();
            }, 60000);
        }

    },

    /**
     * Set value with the switches in control panel
     * @param {string} name
     * @param {string} value
     */
    set_user_setting: function (name, value) {
        "use strict";
        this.log('set_user_setting(' + name + ',' + value + ')');

        switch (name) {

            case 'sound':
                this.options.enable_chat_sound = Boolean(value);
                break;

            case 'stream':

                if (value) {
                    this.add_video();
                } else {
                    this.player.dispose();
                    Y.one('#webcast-stream-holder').setHTML("");
                }

                this.options.stream = value;
                this.scale_room();
                break;

            case 'userlist':

                if (value) {
                    Y.one('#webcast-userlist-holder').show();
                } else {
                    Y.one('#webcast-userlist-holder').hide();
                }

                this.options.userlist = value;
                this.scale_room();
                break;
            case 'mute_guest':
                this.chat_mute_usertype('guest', value);
                break;
            case 'mute_student':
                this.chat_mute_usertype('student', value);
                break;
            case 'mute_teacher':
                this.chat_mute_usertype('teacher', value);
                break;
        }

    },

    /**
     * Mute a usertype as broadcaster
     * @param {string} usertype
     * @param {string} value
     */
    chat_mute_usertype: function (usertype, value) {
        "use strict";
        if (this.options.is_broadcaster) {
            this.log('mute(' + usertype + ',' + value + ')');

            this.chatobject.broadcaster_identifier = this.options.broadcaster_identifier;

            this.socket.emit("mute", this.chatobject, usertype, value, function (response) {
                M.mod_webcast.room.log(response);

                if (!response.status) {
                    M.mod_webcast.room.exception(response.error);
                } else {
                    M.mod_webcast.room.log(response.mute);
                    // @todo make sure the switch not changed by someone else load the status of switch by loading the room
                }
            });
        }
    },

    /**
     * Ping ajax.php for keeping track of the exact user online time
     */
    ajax_timer_ping: function () {
        "use strict";
        this.log('ajax_timer_ping');

        Y.io(this.options.ajax_path, {
            method : 'GET',
            data   : {
                'sesskey': M.cfg.sesskey,
                'action' : "ping",
                'extra1' : M.mod_webcast.room.options.courseid,
                'extra2' : M.mod_webcast.room.options.webcastid
            },
            on     : {
                success: function (id, o) {
                    M.mod_webcast.room.log(id);
                    try {
                        var response = Y.JSON.parse(o.responseText);
                        M.mod_webcast.room.log(response);
                        if (response.status) {
                            // set online somewhere??
                            M.mod_webcast.room.log('You are here for ' + (response.online_minutes / 60) + ' minutes.');
                        } else {
                            // session expired logout etc this bad
                            alert(M.util.get_string('js:error_logout_or_lostconnection', 'webcast', {}));
                        }

                    } catch (e) {
                        // exception
                        M.mod_webcast.room.log(e);
                    }
                }
            },
            headers: {
                'Content-Type': 'application/json'
            }
        });
    },

    /**
     * Setup the main connection to the chat/socket server
     */
    connect_to_socket: function () {
        "use strict";
        var key;
        // Set the template
        for (key in this.chatobject) {
            if (this.chatobject.hasOwnProperty(key)) {
                if (this.options[key] !== undefined) {
                    this.chatobject[key] = this.options[key];
                }
            }
        }

        // add hostname
        this.chatobject.hostname = window.location.hostname;
        // add user agent
        this.chatobject.useragent = navigator.userAgent;
        this.log('connect_to_socket');

        // Nodes
        this.nodeholder.sendbutton = Y.one('#webcast-send');
        this.nodeholder.message = Y.one('#webcast-message');

        // skip if its ended
        if (this.options.is_ended) {
            return;
        }

        // Connect
        this.socket = io.connect(this.options.chat_server);
        this.socket.on('connect', function () {

            if (M.mod_webcast.room.socket_is_connected === false) {

                // we are reconnected
                M.mod_webcast.room.chat_local_message('reconnected');

                // Join the public room again
                this.emit("join", M.mod_webcast.room.chatobject, function (response) {
                    if (!response.status) {
                        M.mod_webcast.room.exception(response.error);
                    } else {
                        M.mod_webcast.room.chat_local_message('joined');
                    }
                });
            }

            M.mod_webcast.room.log('isConnected');
            M.mod_webcast.room.socket_is_connected = true;

            // enable chat input
            M.mod_webcast.room.nodeholder.message.removeAttribute('disabled');
            M.mod_webcast.room.nodeholder.sendbutton.set('text', M.util.get_string('js:send', 'webcast', {}));
        });

        // connection failed
        this.socket.on('reconnect_failed', function () {
            M.mod_webcast.room.socket_connection_failed('reconnect_failed');
        });

        // broadcaster ending webcast called
        this.socket.on('webcast-ended', function () {
            this.chat_ended();
        });

        // disconnect
        this.socket.on('disconnect', function () {
            M.mod_webcast.room.socket_connection_failed('disconnect');
        });

        // generic error
        this.socket.on('error', function () {
            M.mod_webcast.room.log('Socket.io reported a generic error');
        });
    },

    /**
     * Called when the broadcaster end the webcast
     */
    chat_ended: function () {
        "use strict";
        this.chat_local_message('ended');
        var that = this, dialog = new Y.Panel({
            contentBox : Y.Node.create('<div id="dialog" />'),
            bodyContent: '<div class="message"><i class="icon-bubble"></i> ' + M.util.get_string('js:dialog_ending_text', 'webcast', {}) + '</div>',
            width      : 410,
            zIndex     : 6,
            modal      : true, // modal behavior
            render     : true,
            centered   : true,
            visible    : false, // make visible explicitly with .show()
            buttons    : {
                footer: [
                    {
                        name  : 'proceed',
                        label : M.util.get_string('js:dialog_ending_btn', 'webcast', {}),
                        action: 'onOK'
                    }
                ]
            }
        });
        dialog.onOK = function (e) {
            e.preventDefault();
            // redirect to previous page
            window.location = M.cfg.wwwroot + "/mod/webcast/view.php?id=" + that.options.cmid;
        };
        dialog.show();
    },

    /**
     * socket_connection_failed
     * @param {string} message
     */
    socket_connection_failed: function (message) {
        "use strict";
        this.log(message);
        this.socket_is_connected = false;

        // disable chat input
        this.nodeholder.message.setAttribute('disabled', 'disabled');
        this.nodeholder.sendbutton.set('text', M.util.get_string('js:wait_on_connection', 'webcast', {}));

        this.chat_local_message(message);

        // Clear the userlist
        this.reset_userlist();
    },

    /**
     * Commands that can be executed in the chat
     * /command extra1 extra2
     * @param {string} string
     */
    chat_commands: function (string) {
        "use strict";
        this.log('chat_commands: ' + string);
        var args = string.split(' ');

        switch (args[0]) {
            case '/clear':
                this.nodeholder.chatlist.setHTML('');

                // scroll to bottom
                this.scrollbar_chatlist.update('bottom');
                break;
            case '/send_question_to_all':
                // send question to the clients

                break;
            default :
                this.chat_local_message('chat_commands');
        }

        // Reset the input
        this.chatobject.message = "";
        this.nodeholder.message.set('value', "");
    },

    /**
     * Load previous chat data from DB and merge with the socket output
     */
    load_chat_history: function () {
        "use strict";

        Y.io(this.options.ajax_path, {
            method : 'GET',
            data   : {
                'sesskey': M.cfg.sesskey,
                'action' : "load_public_history",
                'extra1' : this.options.courseid,
                'extra2' : this.options.webcastid
            },
            on     : {
                success: function (id, o) {
                    this.log("load_chat_history id:" + id);
                    try {
                        var response = Y.JSON.parse(o.responseText);
                        this.log(response);
                        if (response.status) {
                            // remove the button
                            M.mod_webcast.room.nodeholder.loadhistorybtn.remove();
                            M.mod_webcast.room.chat_add_chatrow(response.messages, 'prepend', true);
                        }

                    } catch (e) {
                        // exception
                        this.log(e);
                    }
                }
            },
            headers: {
                'Content-Type': 'application/json'
            }
        }, this);
    },

    /**
     * Notice other users about the new file
     * @param {object} args
     */
    chat_share_file: function (args) {
        "use strict";
        this.chatobject.message = '[file ' + Y.JSON.stringify(args) + ']';
        this.socket.emit("send", this.chatobject, function (response) {
            if (!response.status) {
                M.mod_webcast.room.exception(response.error);
            } else {
                M.mod_webcast.room.files_uploaded_hashes[args.hash] = true;
            }
        });
    },

    /**
     * load the emoticons in a mapper
     */
    load_emoticons: function () {
        "use strict";
        var name, i, codes, code, patterns = [];
        for (name in this.emoticons) {
            if (this.emoticons.hasOwnProperty(name)) {
                codes = this.emoticons[name].codes;
                for (i in codes) {
                    if (codes.hasOwnProperty(i)) {
                        code = codes[i];
                        this.emoticons_map[code] = name;
                    }
                }
            }
        }

        // Build a regex map
        for (code in this.emoticons_map) {
            if (this.emoticons_map.hasOwnProperty(code)) {
                patterns.push('(' + code.replace(/[\[\]{}()*+?.\\|\^$\-,&#\s]/g, "\\$&") + ')');
            }
        }

        this.emoticons_regex = new RegExp(patterns.join('|'), 'g');
    },

    /**
     * filter text for icon usage
     * can be disabled in the options
     *
     * @param {string} text
     * @return {string}
     */
    add_emoticons: function (text) {
        "use strict";
        if (!this.options.enable_emoticons) {
            return text;
        }

        return text.replace(this.emoticons_regex, function (code) {
            var name = M.mod_webcast.room.emoticons_map[code];
            return ('<span class="emoticon emoticon-' + name + '" title="' + M.mod_webcast.room.emoticons[name].title + '">' + code + '</span>');
        });
    },

    /**
     * Add videojs component
     */
    add_video: function () {
        "use strict";
        this.log('add_video');
        var source = {},  techOrder = ['html5', 'flash'];

        videojs.options.flash.swf = M.cfg.wwwroot + "/mod/webcast/javascript/video-js/video-js.swf";

        var attributes = {
            'id'      : 'room_stream',
            'width'   : '1',
            'height'  : '1',
            'controls': ' ',
            'preload' : 'auto'
        };

        var video = Y.Node.create('<video class="video-js vjs-default-skin"></video>').setAttrs(attributes);
        video.appendTo('#webcast-stream-holder');

        // Note: HLS has about a 30 second delay.
        if (!this.options.is_ended) {
            if (this.options.hls) {
                techOrder = ['hls', 'html5', 'flash'];
                source = {
                    type: "application/x-mpegURL",
                    src : "http://" + this.options.streaming_server + '/' + this.options.broadcastkey + '.m3u8'
                };
            } else {
                // Default rtmp only work on flash based players :(
                source = {
                    type: "rtmp/mp4",
                    src : "rtmp://" + this.options.streaming_server + '/' + this.options.broadcastkey
                };
            }
        } else {
            this.log('Add offline video if there is one');
        }

        // Set player settings
        this.player = videojs('room_stream', {
            'techOrder' : techOrder,
            autoplay : true,
            preload  : 'auto',
            sources  : [source]
        });

        // events
        // https://github.com/videojs/video.js/blob/master/docs/api/vjs.Player.md#waiting-event
        // Fired whenever the media begins waiting
        this.player.on('waiting', function () {
            M.mod_webcast.room.log('player_event(waiting)');
        });

        // Fired whenever the media has been paused
        this.player.on('pause', function () {
            M.mod_webcast.room.log('player_event(pause)');
        });

        // Fired whenever the media begins or resumes playback
        this.player.on('play', function () {
            M.mod_webcast.room.log('player_event(play)');
        });

        // Fired when the end of the media resource is reached (currentTime == duration)
        this.player.on('ended', function () {
            M.mod_webcast.room.log('player_event(ended)');
        });

        // Fired while the user agent is downloading media data
        this.player.on('progress', function () {
            M.mod_webcast.room.log('player_event(progress)');
        });

        // Fired while the user agent is downloading media data
        // Looks like this is called when a stream is really started
        // Also when time no longer gets higher we are stopped
        this.player.on('loadedmetadata', function () {
            M.mod_webcast.room.log('player_event(loadedmetadata)');
        });

        // Fired when an error occurs
        this.player.on('error', function () {
            M.mod_webcast.room.log('player_event(error)');
        });

        this.player.on('loadstart', function () {
            M.mod_webcast.room.log('player_event(loadstart)');
        });

        this.log(source);
    },

    /**
     * Add chat component
     */
    add_chat: function () {
        "use strict";
        this.log('add_chat');

        // add tinyscrollbar
        var that = this, el = document.getElementById("webcast-chatlist");
        this.scrollbar_chatlist = tinyscrollbar(el);
        this.nodeholder.chatlist = Y.one('#webcast-chatlist ul');
        this.nodeholder.loadhistorybtn = Y.one('#webcast-loadhistory');

        if (!this.options.is_ended) {
            // Add first message to the chat
            this.chat_local_message('connecting');
            // Join the public room
            this.socket.emit("join", this.chatobject, function (response) {
                if (!response.status) {
                    M.mod_webcast.room.exception(response.error);
                } else {
                    M.mod_webcast.room.chat_local_message('joined');
                }
            });

            // Socket call when getting a message
            this.socket.on("update-chat", function (data) {
                M.mod_webcast.room.chat_add_chatrow(data);
            });
        }

        // Click on send button
        this.nodeholder.sendbutton.on('click', function () {
            this.chat_send_message();
        }, this);

        // Check if user can view history
        if (this.options.viewhistory) {
            this.nodeholder.loadhistorybtn.show();
            this.nodeholder.loadhistorybtn.on('click', function () {
                this.load_chat_history();
            }, this);
        }

        // show emoticon dialog
        if (this.options.enable_emoticons && !this.options.is_ended) {

            this.log('Emoticons are enabled');
            this.nodeholder.emoticonsdialog = Y.one("#webcast-emoticons-dialog");

            this.nodeholder.emoticonsdialog.delegate('click', function () {
                that.log('click emo');
                that.nodeholder.message.set('value', that.nodeholder.message.get('value') + this.get('text') + ' ');
                that.nodeholder.emoticonsdialog.hide();

                that.nodeholder.message.focus();
            }, 'span.emoticon');

            Y.one('#webcast-emoticon-icon').on('click', function () {
                this.log('click on emoticon icon');

                // validate the emoticons are already build else build them first in a dialog
                if (!Y.one('#webcast-emoticon-content')) {
                    this.chat_build_emoticon_selector();
                }

                if ((this.nodeholder.emoticonsdialog.get('offsetWidth') === 0 &&
                    this.nodeholder.emoticonsdialog.get('offsetHeight') === 0) ||
                    this.nodeholder.emoticonsdialog.get('display') === 'none') {
                    this.log('Show');
                    this.nodeholder.emoticonsdialog.show();
                } else {
                    this.log('Hide');
                    this.nodeholder.emoticonsdialog.hide();
                }
            }, this);
        }

        // Workaround for enter key YUI event not working here..
        this.nodeholder.message.setAttribute('onkeypress', 'return M.mod_webcast.room.chat_enter_listener(event);');
    },

    /**
     * Build emoticons overview
     */
    chat_build_emoticon_selector: function () {
        "use strict";
        var name, items = '';

        // build preview for all emoticons
        for (name in this.emoticons) {
            if (this.emoticons.hasOwnProperty(name)) {
                this.log(this.emoticons[name]);
                items += '<span class="emoticon emoticon-' + name + '" title="' + this.emoticons[name].codes.join(',') + '">' + this.emoticons[name].codes[0] + '</span>';
            }
        }
        var content = Y.Node.create('<div id="webcast-emoticon-content">' + items + '</div>');
        content.appendTo('#webcast-emoticons-dialog div');
    },
    /**
     * Check if enter is pressed send the message
     * @param e
     * @returns {boolean}
     */
    chat_enter_listener         : function (e) {
        "use strict";
        if (e.keyCode === 13) {
            M.mod_webcast.room.chat_send_message();
            return false;
        }
    },

    /**
     * add chat row to the chat
     *
     * @param {object|boolean} data
     * @param {string} direction
     * @param {boolean} multiplelines
     * @returns {string}
     */
    chat_add_chatrow: function (data, direction, multiplelines) {
        "use strict";

        this.log('chat_add_chatrow');

        // Setting vars
        var chatline = '', date = 0, me = false, i, messagetext;

        if (Y.Object.hasKey(data, 'message')) {

            this.log(data);


            // build the chatline and make sure nothing strange happens XSS!
            if (data.messagetype === 'default') {

                me = (data.userid === this.options.userid);
                messagetext = this.chat_parse_message(data);

                // we skip the message
                if (!messagetext) {
                    return '';
                }

                // play sound on new message
                if (this.options.enable_chat_sound &&
                    this.options.userid !== data.userid &&
                    this.audio_newmessage && !multiplelines
                ) {
                    this.log('Bleep sound..');
                    this.audio_newmessage.play();
                }

                // Start
                chatline += '<li class="webcast-chatline webcast-' + this.alpha_numeric(data.usertype) + ' ' + (me ? 'me' : '') + '">' +
                    '<div class="message-container">';

                if (this.options.showuserpicture) {
                    // Add avatar
                    chatline += '<span class="webcast-avatar">' +
                        '<img src="' + M.cfg.wwwroot + '/user/pix.php?file=/' + Number(data.userid) + '/f1.jpg" />' +
                        '</span>';
                }

                chatline += '<span class="webcast-username" data-userid="' + Number(data.userid) + '">' + this.alpha_numeric(data.fullname) + '</span>' +
                    '<span class="webcast-timestamp">' + this.timestamp_to_humanreadable(data.timestamp) + '</span>' +
                    '<span class="webcast-message">' + messagetext + '</span>' +
                    '</div>' +
                    '</li>';

            } else if (data.messagetype === 'system') {

                // Messages generate by server
                chatline += '<li class="webcast-chatline webcast-socketserver">' +
                    '<div class="message-container">' +
                    '<span class="webcast-username">' + M.util.get_string('js:system_user', 'webcast', {}) + '</span>' +
                    '<span class="webcast-timestamp">' + this.timestamp_to_humanreadable(data.timestamp) + '</span>' +
                    '<span class="webcast-message">' + M.util.get_string('js:' + data.message, 'webcast', {}) + '</span>' +
                    '</div>' +
                    '</li>';

            } else if (data.messagetype === 'local') {

                date = new Date().getTime() / 1000;

                // Messages generate by this script local
                chatline += '<li class="webcast-chatline webcast-local">' +
                    '<div class="message-container">' +
                    '<span class="webcast-username noSelect">' + M.util.get_string('js:script_user', 'webcast', {}) + '</span>' +
                    '<span class="webcast-timestamp noSelect">' + this.timestamp_to_humanreadable(date) + '</span>' +
                    '<span class="webcast-message noSelect">' + M.util.get_string('js:' + data.message, 'webcast', {}) + '</span>' +
                    '</div>' +
                    '</li>';
            }

            if (multiplelines) {
                return chatline;
            }
        }

        // Check if we using data as a multiple lines object
        if (multiplelines) {
            for (i in data) {
                if (data.hasOwnProperty(i)) {
                    chatline += this.chat_add_chatrow(data[i], '', true);
                }
            }
        }

        // Inserts the content as the firstChild of the node.
        if (direction === 'prepend') {
            this.nodeholder.chatlist.prepend(chatline);
        } else {
            this.nodeholder.chatlist.append(chatline);
        }

        // scroll to bottom
        this.scrollbar_chatlist.update('bottom');
    },

    /**
     * Add a local message to chat
     * @param {string} string
     */
    chat_local_message: function (string) {
        "use strict";
        var message = {
            'messagetype': 'local',
            'message'    : string
        };

        this.chat_add_chatrow(message);
    },

    /**
     * Make sure a message is a valid text
     * @param {object} data
     * @returns {string|boolean}
     */
    chat_parse_message: function (data) {
        "use strict";

        // check if we must replace text by a shortcode
        if (data.message.charAt(0) === '[' && data.message.slice(-1) === ']') {
            var newmessage = this.chat_parse_shortcodes(data);
            if (newmessage) {
                return newmessage;
            }

            if (!newmessage) {
                // we can skip the message
                this.log('Skip message');
                return false;
            }

            this.log('Error: shortcode not replaced');
        }

        return this.add_emoticons(Y.Node.create("<div/>").setHTML(data.message).get('text'));
    },

    /**
     * Replace shortcode by special features if they exists
     * @param {object} data
     */
    chat_parse_shortcodes: function (data) {
        "use strict";
        var newmessage = false;

        if (this.shortcode_regex.test(data.message)) {
            this.log('Has some shortcode:');
            data.message.replace(this.shortcode_regex, function (a, command, args) {
                M.mod_webcast.room.log(a);
                switch (command) {
                    case 'file':
                        newmessage = M.mod_webcast.room.chat_add_shortcode_file(args);
                        break;
                    case 'question':
                        newmessage = M.mod_webcast.room.chat_add_shortcode_question(args);
                        break;
                    case 'answer':
                        M.mod_webcast.room.chat_add_shortcode_answer(args , data);
                        break;
                }
            });
        }

        // trigger the normal functionality
        return newmessage;
    },

    /**
     * get the file details from the server by hash
     * @param {object} args
     */
    chat_add_shortcode_file: function (args) {
        "use strict";
        var message = '';
        this.log('Add file detail to the chat');

        try {
            var obj = Y.JSON.parse(args.slice(1));
            this.log(obj);
            message += '<div class="webcast-file">' +
                '<img src="' + obj.thumbnail + '" alt="" />' +
                '<span class="webcast-filename">' + this.alpha_numeric(obj.filename) + '</span>' +
                '<span class="webcast-filesize">' + this.alpha_numeric(obj.filesize) + '</span>' +
                '<span class="webcast-fileauthor">' + this.alpha_numeric(obj.author) + '</span>' +
                '<a target="_blank" href="' + M.cfg.wwwroot + '/mod/webcast/download.php?' +
                'extra3=' + Number(obj.id) + '&extra2=' + this.options.webcastid + '&extra1=' + this.options.courseid + '&' +
                'sesskey=' + M.cfg.sesskey +
                '" class="webcast-download webcast-button">Download</a>' +
                '</div>';

        } catch (e) {
            this.log(e);
        }
        this.log(message);
        return message;
    },

    /**
     * Add a question to the chat
     * @param {object} args
     */
    chat_add_shortcode_question: function (args) {
        "use strict";
        var message = '';
        this.log('Add file detail to the chat');

        try {
            var obj = Y.JSON.parse(args.slice(1));
            this.log(obj);
            message += '<div class="webcast-question">' +
                '<span class="text">' + obj.text + '</span>' +
                '<span class="webcast-button answerquestion" data-id="' + obj.question_id + '">' + M.util.get_string('js:answer', 'webcast', {}) + '</span>' +
                '</div>';

        } catch (e) {
            this.log(e);
        }
        this.log(message);
        return message;
    },

    /**
     * Notice the broadcaster or teacher about a new answer
     *
     * @param {object} args
     * @param {object} data
     */
    chat_add_shortcode_answer: function (args , data) {
        "use strict";
        this.log(data);
        try {
            var obj = Y.JSON.parse(args.slice(1));
            if (this.options.userid === Number(obj.created_by)) {
                this.notice_bar_message('added_answer', data);
            }
        } catch (e) {
            this.log(e);
        }
    },

    /**
     * Convert timestamp
     * @param {integer} unix_timestamp
     * @returns {string}
     */
    timestamp_to_humanreadable: function (unix_timestamp) {
        "use strict";
        var now = new Date();
        var date = new Date(unix_timestamp * 1000);
        var minutes = "0" + date.getMinutes();
        var hourstring = date.getHours() + ':' + minutes.substr(-2);

        // IF its not today show complete date
        if (now.toDateString() !== date.toDateString()) {
            return date.getDate() + '-' + date.getMonth() + '-' + date.getFullYear() + '<br/>' + hourstring;
        }

        return hourstring;
    },

    /**
     * Send a message to chat server
     */
    chat_send_message: function () {
        "use strict";
        var message = String(this.nodeholder.message.get('value'));

        // Check if the message is a command
        if (message.charAt(0) === '/') {
            this.chat_commands(message);
            return;
        }

        // Prevent html tags [this will not prevent all more security on server side and when adding the message]
        var regex = new RegExp('/(<([^>]+)>)/ig');
        message = message.replace(regex, "");

        this.log('Send: ' + message);
        if (message.length === 0) {
            return;
        }

        this.chatobject.message = message;
        this.socket.emit("send", this.chatobject, function (response) {
            if (!response.status) {
                M.mod_webcast.room.chat_local_message(response.error);
            }
        });

        // clear
        this.chatobject.message = "";
        this.nodeholder.message.set('value', "");
    },

    /**
     * Set a exception
     * @param {string} errorstring
     */
    exception: function (errorstring) {
        "use strict";
        this.log('ERROR: ' + errorstring);
    },

    /**
     * Returns a alpha numeric string to prevent xss
     * @param {string} string
     * @returns {string}
     */
    alpha_numeric: function (string) {
        "use strict";
        return string.replace(/[^\w\s\.\-\_]/gi, "");
    },

    /**
     * Show the userlist
     */
    add_userlist: function () {
        "use strict";
        this.log('add_userlist');

        // set userlist node prevent searching the dom again
        this.nodeholder.userlist = Y.one('#webcast-userlist ul');
        this.nodeholder.userlist_counter = Y.one('#webcast-usercounter');

        // add tinyscrollbar
        var el = document.getElementById("webcast-userlist");
        this.scrollbar_userlist = tinyscrollbar(el);

        // Userlist listener
        this.socket.on("update-user-list", function (data) {
            M.mod_webcast.room.update_userlist(data);
        });
    },

    /**
     * Update userlist
     * @param {object} data
     */
    update_userlist: function (data) {
        "use strict";
        this.log(data);

        if (!data.status) {
            return;
        }

        // Setting vars
        var htmlbroadcaster = '', htmlteachers = '', htmlstudents = '', htmlguests = '', key, userobject, li;

        for (key in data.users) {

            if (data.users.hasOwnProperty(key)) {

                userobject = data.users[key];

                this.log(userobject);

                li = '<li id="userlist-user-' + Number(userobject.userid) + '" class="webcast-' + this.alpha_numeric(userobject.usertype) + ' noSelect">';

                if (this.options.showuserpicture) {
                    li += '<img src="' + M.cfg.wwwroot + '/user/pix.php?file=/' + Number(userobject.userid) + '/f1.jpg" />';
                }

                li += '<span class="fullname">' + this.alpha_numeric(userobject.fullname) + '</span>' +
                    '<span class="browser">' + userobject.useragent.os.name + ' ' + userobject.useragent.os.version + '<br/>' +
                    userobject.useragent.browser.name + ' ' + userobject.useragent.browser.major + '</span>';

                li += '</li>';

                switch (userobject.usertype) {
                    case 'broadcaster':
                        htmlbroadcaster += li;
                        break;
                    case 'teacher':
                        htmlteachers += li;
                        break;
                    case 'student':
                        htmlstudents += li;
                        break;
                    default:
                        htmlguests += li;
                }
            }
        }

        this.nodeholder.userlist.setHTML(htmlbroadcaster + htmlteachers + htmlstudents + htmlguests);

        // Update scrollbar
        this.scrollbar_userlist.update();

        // Update the counter
        this.nodeholder.userlist_counter.set('text', ' (' + data.count + ') ');
    },

    /**
     * clear the userlist if connection fail
     */
    reset_userlist: function () {
        "use strict";
        this.log('build_room');
        this.nodeholder.userlist.setHTML('');
        // Update the counter
        this.nodeholder.userlist_counter.set('text', ' (0) ');

        // Update scrollbar
        this.scrollbar_userlist.update();
    },

    /**
     * add_fileshare
     */
    add_fileshare: function () {
        "use strict";
        var filelist = '', i, obj;

        this.nodeholder.filemanagerdialog = Y.one("#webcast-filemanager-dialog");
        this.nodeholder.fileoverviewdialog = Y.one("#webcast-fileoverview-dialog");
        var el = document.getElementById("webcast-fileoverview");
        this.scrollbar_fileoverview = tinyscrollbar(el);
        this.nodeholder.fileoverview = Y.one('#webcast-fileoverview ul');

        Y.one('#add-file-btn').on('click', function () {
            this.log('Add files to the room');
            var form = Y.one('#mform1');

            // Start the transaction.
            Y.io(this.options.ajax_path, {
                method: 'POST',
                data  : {
                    'sesskey': M.cfg.sesskey,
                    'action' : "add_file",
                    'extra1' : this.options.courseid,
                    'extra2' : this.options.webcastid
                },
                form  : {
                    id: form
                },
                on    : {
                    success: function (id, o) {
                        M.mod_webcast.room.log(id);
                        try {
                            var response = Y.JSON.parse(o.responseText);
                            M.mod_webcast.room.log(response);
                            if (response.status) {
                                // clear own file overview
                                // Y.all('.fm-content-wrapper .fp-content').setHTML('');

                                // hide the dialog
                                M.mod_webcast.room.nodeholder.filemanagerdialog.hide();
                                var hash;
                                for (var i in response.files) {
                                    if (response.files.hasOwnProperty(i)) {
                                        hash = response.files[i].hash;
                                        if (M.mod_webcast.room.files_uploaded_hashes[hash] !== undefined) {
                                            M.mod_webcast.room.log('already shared skip!');
                                            continue;
                                        }

                                        M.mod_webcast.room.chat_share_file(response.files[i]);
                                    }
                                }
                            }

                        } catch (e) {
                            // exception
                            M.mod_webcast.room.log(e);
                        }
                    }
                }
            });

        }, this);

        if (!this.options.is_ended) {
            Y.one('#webcast-filemanager-btn').on('click', function () {
                this.log('Filemanager');
                if ((this.nodeholder.filemanagerdialog.get('offsetWidth') === 0 &&
                    this.nodeholder.filemanagerdialog.get('offsetHeight') === 0) ||
                    this.nodeholder.filemanagerdialog.get('display') === 'none') {
                    this.log('Show');
                    this.nodeholder.filemanagerdialog.show();
                    this.nodeholder.fileoverviewdialog.hide();
                } else {
                    this.log('Hide');
                    this.nodeholder.filemanagerdialog.hide();
                }

                this.scale_room();
            }, this);
        } else {
            Y.one('#webcast-filemanager-btn').hide();
        }

        Y.one('#webcast-fileoverview-btn').on('click', function () {
            this.log('Filemanager');
            if ((this.nodeholder.fileoverviewdialog.get('offsetWidth') === 0 &&
                this.nodeholder.fileoverviewdialog.get('offsetHeight') === 0) ||
                this.nodeholder.fileoverviewdialog.get('display') === 'none') {
                this.log('Show');
                this.nodeholder.fileoverviewdialog.show();
                this.nodeholder.filemanagerdialog.hide();

                // set the content with a ajax request
                Y.io(this.options.ajax_path, {
                    method: 'POST',
                    data  : {
                        'sesskey': M.cfg.sesskey,
                        'action' : "list_all_files",
                        'extra1' : this.options.courseid,
                        'extra2' : this.options.webcastid
                    },
                    on    : {
                        success: function (id, o) {
                            M.mod_webcast.room.log(id);
                            try {
                                var response = Y.JSON.parse(o.responseText);
                                M.mod_webcast.room.log(response);
                                if (response.status) {

                                    filelist = '';
                                    // clear own file overview
                                    for (i in response.files) {
                                        if (response.files.hasOwnProperty(i)) {
                                            obj = response.files[i];
                                            filelist += '<li class="webcast-file">' +
                                                '<img src="' + obj.thumbnail + '" alt="" />' +
                                                '<span class="webcast-filename">' + M.mod_webcast.room.alpha_numeric(obj.filename) + '</span>' +
                                                '<span class="webcast-filesize">' + M.mod_webcast.room.alpha_numeric(obj.filesize) + '</span>' +
                                                '<span class="webcast-fileauthor">' + M.mod_webcast.room.alpha_numeric(obj.author) + '</span>' +
                                                '<a target="_blank" href="' + M.cfg.wwwroot + '/mod/webcast/download.php?' +
                                                'extra3=' + Number(obj.id) + '&extra2=' + M.mod_webcast.room.options.webcastid + '&extra1=' + M.mod_webcast.room.options.courseid + '&' +
                                                'sesskey=' + M.cfg.sesskey +
                                                '" class="webcast-download webcast-button">Download</a>' +
                                                '</li>';
                                        }
                                    }
                                    M.mod_webcast.room.log(filelist);
                                    // set ul
                                    M.mod_webcast.room.nodeholder.fileoverview.setHTML(filelist);
                                    M.mod_webcast.room.scale_room();
                                    M.mod_webcast.room.scrollbar_fileoverview.update();
                                }

                            } catch (e) {
                                // exception
                                M.mod_webcast.room.log(e);
                            }
                        }
                    }
                });

            } else {
                this.log('Hide');
                this.nodeholder.fileoverviewdialog.hide();
            }

            this.scale_room();
        }, this);

        // Close by clicking the header of dialog
        Y.one('body').delegate('click', function () {
            this.get('parentNode').hide();
        }, '.webcast-dialog header');

        this.log('add_fileshare @todo');
    },

    /**
     * Show a warning before closing
     */
    warning_message_closing_window: function () {
        "use strict";
        window.onbeforeunload = function () {
            if (!this.options.preventclosewaring) {
                return M.util.get_string('js:warning_message_closing_window', 'webcast', {});
            }
        };
    },

    /**
     * Add a question manager that allows the broadcaster to:
     * - Send questions to there clients
     * - Crud question
     * - See answers to the questions
     * - Keep track of all question even on F5 or reentering the room
     */
    add_question_manager    : function () {
        "use strict";
        var that = this;

        this.nodeholder.questionoverview = Y.one('#all-questions ul');
        this.nodeholder.addquestionbtn = Y.one('#addquestion');

        // init manager popup
        this.nodeholder.questionmanager = new Y.Panel({
            width   : 600,
            height  : 400,
            zIndex  : 10,
            centered: true,
            modal   : true,
            visible : false,
            render  : true,
            srcNode : '#webcast-question-manager'
        });

        // add click listener
        Y.one('#webcast-viewquestion-btn').on('click', function () {
            this.nodeholder.questionmanager.show();
            // load the question from the DB
            this.question_load_overview();
        }, this);


        // back button on question detail
        Y.one('body').delegate('click', function () {
            Y.one('#question-answer').hide();
            Y.one('#all-questions').show();
            that.question_load_overview();
        }, '.webcast-back-to-questionoverview');

        // view a question or answer if we aren't a teacher or broadcaster
        this.nodeholder.questionoverview.delegate('click', function () {
            that.question_load_single(this.getData('id'));
        }, '.viewquestionbtn');

        // check if we can still add questions
        if (!this.options.is_ended) {

            // Press on add answer in chat
            Y.one('body').delegate('click', function () {
                that.nodeholder.questionmanager.show();
                that.question_load_single(this.getData('id'));
            }, '.answerquestion');

            // add new question
            if (this.nodeholder.addquestionbtn) {
                // broadcaster or teacher can add questions

                this.nodeholder.addquestionbtn.on('click', function () {
                    Y.one('#all-questions').hide();
                    Y.one('#question-type-selector').show();
                });

                // step 2 back to question type selector
                Y.all('.webcast-button-previous-step2').on('click', function () {
                    Y.all('#question-type-open, #question-type-choice, #question-type-truefalse').hide();
                    Y.one('#question-type-selector').show();
                }, this);

                // show the correct question type create form
                Y.one('#webcast-button-next-step1').on('click', function () {
                    Y.one('#question-type-selector').hide();
                    var value = Y.one('#question-type').get('value');
                    this.log(value);
                    Y.one('#question-type-' + value).show();

                    //  make sure all input is cleared
                    this.question_clear_all_input();
                }, this);

                // back to question overview
                Y.one('#webcast-button-previous-step1').on('click', function () {
                    Y.one('#all-questions').show();
                    Y.one('#question-type-selector').hide();
                }, this);
                ///////////////////////////////////////////////////////////////////////////////////////////////
                var inputtruefalse = Y.one('#question-truefalse');
                var truefalseaddbtn = Y.one('#truefalse-add-btn');
                truefalseaddbtn.on('click', function () {
                    if (!truefalseaddbtn.hasClass('disabled')) {
                        inputtruefalse.setStyles({'border': '1px solid green'});
                        truefalseaddbtn.removeClass('disabled');
                        this.question_save('truefalse');
                    } else {
                        inputtruefalse.setStyles({'border': '1px solid red'});
                    }
                }, this);

                inputtruefalse.on('keyup', function () {
                    if (Y.Lang.trim(inputtruefalse.get('value')) !== "") {
                        inputtruefalse.setStyles({'border': '1px solid green'});
                        truefalseaddbtn.removeClass('disabled');
                    } else {
                        inputtruefalse.setStyles({'border': '1px solid red'});
                        truefalseaddbtn.addClass('disabled');
                    }
                }, this);
                ///////////////////////////////////////////////////////////////////////////////////////////////
                var openaddbtn = Y.one('#open-add-btn');
                var inputopen = Y.one('#question-open');
                openaddbtn.on('click', function () {
                    if (!openaddbtn.hasClass('disabled')) {
                        inputopen.setStyles({'border': '1px solid green'});
                        openaddbtn.removeClass('disabled');
                        this.question_save('open');
                    } else {
                        inputopen.setStyles({'border': '1px solid red'});
                    }
                }, this);

                inputopen.on('keyup', function () {
                    if (Y.Lang.trim(inputopen.get('value')) !== "") {
                        inputopen.setStyles({'border': '1px solid green'});
                        openaddbtn.removeClass('disabled');
                    } else {
                        inputopen.setStyles({'border': '1px solid red'});
                        openaddbtn.addClass('disabled');
                    }
                }, this);

            } else {
                // normal student
            }

            // prevent submits on enter
            Y.all('#webcast-question-manager form').on('submit', function (e) {
                e.preventDefault();
                return false;
            });
        }
    },
    /**
     * clear input to prevent strange thinks from happening
     */
    question_clear_all_input: function () {
        "use strict";
        // empty all input
        Y.all('#webcast-question-manager input[type="text"], #webcast-question-manager textarea').set('value', '');
        // reset the borders
        Y.all('#webcast-question-manager input[type="text"]').setStyles({'border': '1px solid red'});
        // disable the next buttons
        Y.all('#open-add-btn , #truefalse-add-bt').addClass('disabled');
    },

    /**
     * Get all the questions that belong to this webcast and check if its filled
     */
    question_load_overview: function () {
        "use strict";
        var that = this, html = '', i, question;
        Y.io(M.cfg.wwwroot + "/mod/webcast/api.php", {
            method: 'POST',

            data: {
                'sesskey': M.cfg.sesskey,
                'action' : "get_questions",
                'extra1' : that.options.courseid,
                'extra2' : that.options.webcastid
            },
            on  : {
                success: function (id, o) {
                    that.log(o.response);
                    try {
                        var response = Y.JSON.parse(o.response);
                        if (response.status) {
                            html = '';
                            for (i in response.questions) {
                                if (response.questions.hasOwnProperty(i)) {

                                    question = response.questions[i];
                                    html += '<li class="' + ((!question.manager && !question.my_answer) ? 'unanswered' : '') + '">';
                                    html += '<span class="number">#' + i + '</span>';
                                    html += '<span class="name">' + question.name + '</span>';
                                    html += '<span class="webcast-button gray viewquestionbtn" data-id="' + i + '">' + M.util.get_string('btn:view', 'webcast', {}) + '</span>';
                                    html += '</li>';
                                }
                            }

                            that.nodeholder.questionoverview.setHTML(html);
                        }
                    } catch (exc) {
                        that.log(exc);
                    }
                },
                failure: function (x, o) {
                    that.log('failure');
                    that.log(o);
                }
            }
        });

    },

    /**
     * Load a single question by id
     * @param {integer} questionid
     */
    question_load_single: function (questionid) {
        "use strict";
        var that = this;
        //@todo check user type for which api we need to call
        Y.io(M.cfg.wwwroot + "/mod/webcast/api.php", {
            method: 'POST',

            data: {
                'sesskey'   : M.cfg.sesskey,
                'action'    : "get_question",
                'extra1'    : that.options.courseid,
                'extra2'    : that.options.webcastid,
                'questionid': questionid
            },
            on  : {
                success: function (id, o) {
                    that.log(o.response);
                    try {
                        var response = Y.JSON.parse(o.response);
                        if (response.status) {
                            // hide question overview
                            Y.one('#all-questions').hide();

                            // answering a question
                            if (response.item.form) {
                                Y.one('#question-answer').setHTML(response.item.form).show();
                                // listener for a answer on the question
                                that.question_answer();
                            } else {
                                // We need to build total answer overview
                                Y.one('#question-answer').setHTML(response.item.answers).show();
                            }
                        }
                    } catch (exc) {
                        that.log(exc);
                    }
                },
                failure: function (x, o) {
                    that.log('failure');
                    that.log(o);
                }
            }
        });
    },

    /**
     * Answer a question
     */
    question_answer: function () {
        "use strict";
        var answerform = Y.one('#question-submit-answer'), that = this;
        answerform.on('submit', function (e) {
            e.preventDefault();

            Y.io(M.cfg.wwwroot + "/mod/webcast/api.php", {
                method: 'POST',
                form  : {
                    id         : answerform,
                    useDisabled: true
                },
                data  : {
                    'sesskey': M.cfg.sesskey,
                    'action' : "add_answer",
                    'extra1' : that.options.courseid,
                    'extra2' : that.options.webcastid
                },
                on    : {
                    success: function (id, o) {
                        that.log(o.response);
                        try {
                            var response = Y.JSON.parse(o.response);
                            if (response.status) {

                                that.nodeholder.questionmanager.hide();
                                // back to question overview
                                // notice the broadcaster / teacher about the answer
                                that.chatobject.message = '[answer ' + Y.JSON.stringify(response) + ']';
                                that.socket.emit("send", that.chatobject, function (response) {
                                    if (!response.status) {
                                        that.exception(response.error);
                                    }

                                    that.chat_local_message('my_answer_saved');

                                });
                            } else {
                                // we have a error display this to the user
                                that.log('question_answer Error');
                                Y.one('#question-error').setHTML(response.error).show();
                            }
                        } catch (exc) {
                            that.log('question_answer Exception');
                            that.log(exc);
                        }
                    },
                    failure: function (x, o) {
                        that.log('failure');
                        that.log(o);
                    }
                }
            });
        });
    },

    /**
     * Show a notice bar with a text can be triggered remotely
     * @param {string} message
     * @param {object} obj
     */
    notice_bar_message: function (message, obj) {
        "use strict";
        var that = this;
        if (this.nodeholder.noticebar === null) {
            this.nodeholder.noticebar = Y.one('#webcast-noticebar');
        }

        if ((this.nodeholder.noticebar.get('offsetWidth') === 0 && this.nodeholder.noticebar.get('offsetHeight') === 0) ||
            this.nodeholder.noticebar.get('display') === 'none') {
            // not visible we can show it directly
            this.nodeholder.noticebar.setHTML(M.util.get_string('js:' + message, 'webcast', obj)).show();
            setTimeout(function () {
                that.nodeholder.noticebar.hide();
            }, 2000);
        } else {
            // need some kind of queue
            setTimeout(function () {
                that.notice_bar_message(message, obj);
            }, 4000);
        }

    },
    /**
     * Save question and notice the clients
     * @param {string} questiontype
     */
    question_save     : function (questiontype) {
        "use strict";
        var formnode = Y.one('#question-type-' + questiontype + ' form'), that = this;
        Y.io(M.cfg.wwwroot + "/mod/webcast/api.php", {
            method: 'POST',
            form  : {
                id         : formnode,
                useDisabled: true
            },
            data  : {
                'sesskey'     : M.cfg.sesskey,
                'action'      : "add_question",
                'extra1'      : that.options.courseid,
                'extra2'      : that.options.webcastid,
                'questiontype': questiontype
            },
            on    : {
                success: function (id, o) {
                    that.log(o.response);
                    try {
                        var response = Y.JSON.parse(o.response);
                        if (response.status) {
                            that.log('question_save Success');
                            // close the dialog and hide steps
                            Y.all('#question-type-open, #question-type-choice, #question-type-truefalse').hide();
                            Y.one('#all-questions').show();
                            that.nodeholder.questionmanager.hide();
                            that.chat_local_message('added_question');

                            that.chatobject.message = '[question ' + Y.JSON.stringify(response) + ']';
                            that.socket.emit("send", that.chatobject, function (response) {
                                if (!response.status) {
                                    that.exception(response.error);
                                }
                            });
                        }
                    } catch (exc) {
                        that.log('question_save Exception');
                        that.log(exc);
                    }
                },
                failure: function (x, o) {
                    that.log('failure');
                    that.log(o);
                }
            }
        });

    },
    /**
     * Scale room
     * Should be executed when the browser window resize
     */
    scale_room        : function () {
        "use strict";
        var winWidth = this.nodeholder.body.get("winWidth");
        var winHeight = this.nodeholder.body.get("winHeight");
        var wh;

        // set elements one time
        if (this.nodeholder.userlist_viewport === null) {
            this.nodeholder.userlist_viewport = Y.one('#webcast-userlist .viewport');
            this.nodeholder.chatlist_viewport = Y.one('#webcast-chatlist .viewport');
        }

        this.log('scale_room for : ' + winWidth + 'x' + winHeight);

        if (this.options.stream) {

            // scale video component
            var videowidth = winWidth - 401 - 40;
            var videoheight = Math.round((videowidth / 16) * 9);
            var maxvideoheight = (winHeight - 70 - 80);

            // Make sure everything fits the screen
            if (videoheight > maxvideoheight) {
                this.log('Video to high we set it to: ' + maxvideoheight);
                videoheight = maxvideoheight;
            }

            Y.one('#room_stream').setStyles({
                height: videoheight,
                width : videowidth
            });
        }

        // scale userlist and chat
        if (this.options.userlist && this.options.chat) {
            // 30% 70% - 200 for the other components
            wh = winHeight - ((36 * 2) + 50 + 100);

            Y.one('#webcast-userlist .viewport').setStyles({
                height: wh * 0.3
            });
            this.scrollbar_userlist.update();

            wh *= 0.7;
            Y.one('#webcast-chatlist .viewport').setStyles({
                height: wh
            });
            this.scrollbar_chatlist.update('bottom');

        } else if (!this.options.userlist && this.options.chat) {

            // only chat
            wh = winHeight - ((36) + 50 + 100);
            Y.one('#webcast-chatlist .viewport').setStyles({
                height: wh
            });
            this.scrollbar_chatlist.update('bottom');

        } else if (this.options.userlist) {

            // only userlist
            wh = winHeight - ((36) + 50 + 100);
            Y.one('#webcast-userlist .viewport').setStyles({
                height: wh
            });
            this.scrollbar_userlist.update('bottom');
        }

        // file sharing
        if (this.options.filesharing) {

            if (!this.options.is_ended) {
                this.nodeholder.filemanagerdialog.setStyles({
                    height      : (wh + 25),
                    'margin-top': -(wh + 25)
                });

                Y.one('.filemanager .fp-content').setStyles({
                    'max-height': (wh - 200)
                });
            }

            this.nodeholder.fileoverviewdialog.setStyles({
                height      : (wh + 25),
                'margin-top': -(wh + 25)
            });

            Y.one('#webcast-fileoverview .viewport').setStyles({
                height: wh
            });
        }

        // emoticons dialog
        if (this.options.enable_emoticons) {
            if (!this.options.is_ended) {
                this.nodeholder.emoticonsdialog.setStyles({
                    height      : (wh + 25),
                    'margin-top': -(wh + 25)
                });
            }
        }
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
    },

    /**
     * internal event listener
     * @param {object} object
     * @param {string} type
     * @param {function} callback
     */
    add_event: function (object, type, callback) {
        "use strict";
        if (object === null || object === undefined) {
            return;
        }
        if (object.addEventListener) {
            object.addEventListener(type, callback, false);
        }
        else if (object.attachEvent) {
            object.attachEvent("on" + type, callback);
        }
        else {
            object["on" + type] = callback;
        }
    }
};