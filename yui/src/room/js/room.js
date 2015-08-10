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
/*global  M, Y, videojs, console, io, tinyscrollbar, alert , YUI*/
M.mod_webcast = M.mod_webcast || {};
M.mod_webcast.room = {

    /**
     * Emoticons mapping
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
    emoticons_map  : {},

    /**
     * Webcast variables
     */
    options: {
        debugjs            : true,
        chat               : false,
        duration           : 0,
        timeopen           : 0,
        cmid               : 0,
        courseid           : 0,
        webcastid          : 0,
        filesharing        : false,
        filesharing_student: false,
        viewhistory        : false,
        is_ended           : false,
        showuserpicture    : false,
        stream             : false,
        broadcaster        : -999,
        broadcastkey       : "broadcastkey",
        shared_secret      : "",
        streaming_server   : "",
        chat_server        : "",
        fullname           : "",
        ajax_path          : "",
        usertype           : "",
        userid             : 0,
        userlist           : false,
        ajax_timer         : false,
        enable_emoticons   : true,
        hls                : false
    },

    /**
     * A reference to the scrollview used in this module
     */
    scrollbar_userlist: null,

    /**
     * A reference to the scrollview used in this module
     */
    scrollview_chatlist: null,

    /**
     * Socket
     */
    socket: null,

    /**
     * Files
     */
    files_uploaded_hashes: {},

    /**
     * Shortcode regex
     * Sample [file 19845771897512389057123589027301201]
     */
    shortcode_regex: /\[([\w\-_]+)([^\]]*)?\](?:(.+?)?\[\/\1\])?/g,

    /**
     * Bool to check if we are connected
     */
    socket_is_connected: null,

    /**
     * Videojs player
     */
    player: null,

    /**
     * Chat template
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
        message           : null
    },
    /**
     * Internal logging
     * @param val
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
     * Init
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

        // build room components
        this.build_room();
    },

    /**
     * Build the room and add the components that are enabled
     */
    build_room: function () {
        "use strict";
        this.log('build_room');

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
        }

        // add file sharing
        if ((this.options.filesharing_student && this.options.broadcaster === this.options.userid) ||
            this.options.filesharing_student) {

            this.add_fileshare();
        }

        // scaling listener
        this.add_event(window, "resize", function () {
            M.mod_webcast.room.scale_room();
        });

        // Message before closing
        // this.warning_message_closing_window();

        // first time scale the room
        setTimeout(function () {
            M.mod_webcast.room.scale_room();
        }, 300);

        if (this.options.ajax_timer) {
            setInterval(function () {
                M.mod_webcast.room.ajax_timer_ping();
            }, 60000);
        }

    },

    /**
     * Set value with the switches in control panel
     * @param name
     * @param value
     */
    set_user_setting: function (name, value) {
        "use strict";
        M.mod_webcast.room.log('set_user_setting(' + name + ',' + value + ')');

        switch (name) {

            case 'stream':

                if (value) {
                    M.mod_webcast.room.add_video();
                } else {
                    M.mod_webcast.room.player.dispose();
                    Y.one('#webcast-stream-holder').setHTML("");
                }

                M.mod_webcast.room.options.stream = value;
                M.mod_webcast.room.scale_room();
                break;

            case 'userlist':

                if (value) {
                    Y.one('#webcast-userlist-holder').show();
                } else {
                    Y.one('#webcast-userlist-holder').hide();
                }

                M.mod_webcast.room.options.userlist = value;
                M.mod_webcast.room.scale_room();
                break;

        }

    },

    /**
     * Ping ajax.php for keeping track of the exact user online time
     */
    ajax_timer_ping: function () {
        "use strict";
        M.mod_webcast.room.log('ajax_timer_ping');

        Y.io(M.mod_webcast.room.options.ajax_path, {
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
        // add useragent
        this.chatobject.useragent = navigator.userAgent;
        this.log('connect_to_socket');

        // Connect
        this.socket = io.connect(this.options.chat_server);

        // Nodes
        this.nodeholder.sendbutton = Y.one('#webcast-send');
        this.nodeholder.message = Y.one('#webcast-message');

        this.socket.on('connect', function () {

            if (M.mod_webcast.room.socket_is_connected === false) {

                // we are reconnected
                M.mod_webcast.room.local_message('reconnected');

                // Join the public room again
                this.emit("join", M.mod_webcast.room.chatobject, function (response) {
                    if (!response.status) {
                        M.mod_webcast.room.exception(response.error);
                    } else {
                        M.mod_webcast.room.local_message('joined');
                    }
                });
            }

            M.mod_webcast.room.log('isConnected');
            M.mod_webcast.room.socket_is_connected = true;

            // enable chatinput
            M.mod_webcast.room.nodeholder.message.removeAttribute('disabled');
            M.mod_webcast.room.nodeholder.sendbutton.set('text', M.util.get_string('js:send', 'webcast', {}));
        });

        // connection failed
        this.socket.on('reconnect_failed', function () {
            M.mod_webcast.room.socket_connection_failed('reconnect_failed');
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
     * socket_connection_failed
     * @param message
     */
    socket_connection_failed: function (message) {
        "use strict";
        M.mod_webcast.room.log(message);
        M.mod_webcast.room.socket_is_connected = false;

        // disable chat input
        M.mod_webcast.room.nodeholder.message.setAttribute('disabled', 'disabled');
        M.mod_webcast.room.nodeholder.sendbutton.set('text', M.util.get_string('js:wait_on_connection', 'webcast', {}));

        M.mod_webcast.room.local_message(message);

        // Clear the userlist
        M.mod_webcast.room.reset_userlist();
    },

    /**
     * Commands that can be executed in the chat
     * /command extra1 extra2
     */
    chat_commands: function (string) {
        "use strict";
        this.log('chat_commands: ' + string);
        var args = string.split(' ');

        switch (args[0]) {
            case '/clear':
                this.nodeholder.chatlist.setHTML('');

                // scroll to bottom
                M.mod_webcast.room.scrollbar_chatlist.update('bottom');
                break;
            default :
                this.chat_add_chatrow({
                    usertype: 'local',
                    message : 'chat_commands'
                });
        }

        // Reset the input
        M.mod_webcast.room.chatobject.message = "";
        M.mod_webcast.room.nodeholder.message.set('value', "");
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
     */
    chat_share_file: function (args) {
        "use strict";
        M.mod_webcast.room.chatobject.message = '[file ' + Y.JSON.stringify(args) + ']';
        M.mod_webcast.room.socket.emit("send", M.mod_webcast.room.chatobject, function (response) {
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
                        // Set emoticon mapping
                        this.emoticons_map[this.escape_emoticon(code)] = name;
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
     * @param text
     * @return string
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

    escape_emoticon: function (string) {
        "use strict";
        var entityMap = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#39;',
            '/': '&#x2F;'
        };
        return String(string).replace(/[&<>"'\/]/g, function (s) {
            return entityMap[s];
        });
    },

    /**
     * Add videojs component
     */
    add_video: function () {
        "use strict";
        this.log('add_video');
        var source = {};

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
        if (this.options.hls) {
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

        // Set player settings
        this.player = videojs('room_stream', {
            techOrder: ['hls', 'html5', 'flash'],
            autoplay : true,
            preload  : 'auto',
            sources  : [source]
        });

        // events
        // https://github.com/videojs/video.js/blob/master/docs/api/vjs.Player.md#waiting-event
        // Fired whenever the media begins waiting
        this.player.on('waiting', function (e) {
            M.mod_webcast.room.log('player_event(waiting)');
        });

        // Fired whenever the media has been paused
        this.player.on('pause', function (e) {
            M.mod_webcast.room.log('player_event(pause)');
        });

        // Fired whenever the media begins or resumes playback
        this.player.on('play', function (e) {
            M.mod_webcast.room.log('player_event(play)');
        });

        // Fired when the end of the media resource is reached (currentTime == duration)
        this.player.on('ended', function (e) {
            M.mod_webcast.room.log('player_event(ended)');
        });

        // Fired while the user agent is downloading media data
        this.player.on('progress', function (e) {
            M.mod_webcast.room.log('player_event(progress)');
        });

        // Fired while the user agent is downloading media data
        this.player.on('loadedmetadata', function (e) {
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
        var el = document.getElementById("webcast-chatlist");
        this.scrollbar_chatlist = tinyscrollbar(el);
        this.nodeholder.chatlist = Y.one('#webcast-chatlist ul');
        this.nodeholder.loadhistorybtn = Y.one('#webcast-loadhistory');

        // Add first message to the chat
        M.mod_webcast.room.local_message('connecting');

        // Join the public room
        this.socket.emit("join", this.chatobject, function (response) {
            if (!response.status) {
                M.mod_webcast.room.exception(response.error);
            } else {
                M.mod_webcast.room.local_message('joined');
            }
        });

        // Socket call when getting a message
        this.socket.on("update-chat", function (data) {
            M.mod_webcast.room.chat_add_chatrow(data);
        });

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

        // Workaround for enterkey YUI event not working here..
        this.nodeholder.message.setAttribute('onkeypress', 'return M.mod_webcast.room.chat_enter_listener(event);');
    },

    /**
     * Check if enter is pressed send the message
     * @param e
     * @returns {boolean}
     */
    chat_enter_listener: function (e) {
        "use strict";
        if (e.keyCode === 13) {
            M.mod_webcast.room.chat_send_message();
            return false;
        }
    },

    /**
     *
     */
    chat_add_chatrow: function (data, direction, multiline) {
        "use strict";

        M.mod_webcast.room.log('chat_add_chatrow');

        // Setting vars
        var chatline = '', date = 0, me = false, i;

        if (Y.Object.hasKey(data, 'message')) {

            M.mod_webcast.room.log(data);

            // build the chatline and make sure nothing strange happens XSS!
            if (data.messagetype === 'default') {

                me = (data.userid === M.mod_webcast.room.options.userid);

                // Start
                chatline += '<li class="webcast-chatline webcast-' + M.mod_webcast.room.alpha_numeric(data.usertype) + ' ' + (me ? 'me' : '') + '">' +
                    '<div class="message-container">';

                if (M.mod_webcast.room.options.showuserpicture) {
                    // Add avatar
                    chatline += '<span class="webcast-avatar">' +
                        '<img src="' + M.cfg.wwwroot + '/user/pix.php?file=/' + Number(data.userid) + '/f1.jpg" />' +
                        '</span>';
                }

                chatline += '<span class="webcast-username" data-userid="' + Number(data.userid) + '">' + M.mod_webcast.room.alpha_numeric(data.fullname) + '</span>' +
                    '<span class="webcast-timestamp">' + M.mod_webcast.room.timestamp_to_humanreadable(data.timestamp) + '</span>' +
                    '<span class="webcast-message">' + M.mod_webcast.room.chat_parse_message(data.message) + '</span>' +
                    '</div>' +
                    '</li>';

            } else if (data.messagetype === 'system') {

                // Messages generate by server
                chatline += '<li class="webcast-chatline webcast-socketserver">' +
                    '<div class="message-container">' +
                    '<span class="webcast-username">' + M.util.get_string('js:system_user', 'webcast', {}) + '</span>' +
                    '<span class="webcast-timestamp">' + M.mod_webcast.room.timestamp_to_humanreadable(data.timestamp) + '</span>' +
                    '<span class="webcast-message">' + M.util.get_string('js:' + data.message, 'webcast', {}) + '</span>' +
                    '</div>' +
                    '</li>';

            } else if (data.messagetype === 'local') {

                date = new Date().getTime() / 1000;

                // Messages generate by this script local
                chatline += '<li class="webcast-chatline webcast-local">' +
                    '<div class="message-container">' +
                    '<span class="webcast-username noSelect">' + M.util.get_string('js:script_user', 'webcast', {}) + '</span>' +
                    '<span class="webcast-timestamp noSelect">' + M.mod_webcast.room.timestamp_to_humanreadable(date) + '</span>' +
                    '<span class="webcast-message noSelect">' + M.util.get_string('js:' + data.message, 'webcast', {}) + '</span>' +
                    '</div>' +
                    '</li>';
            }

            if (multiline) {
                return chatline;
            }
        }

        // Check if we using data as a multiline object
        if (multiline) {
            for (i in data) {
                if (data.hasOwnProperty(i)) {
                    chatline += this.chat_add_chatrow(data[i], '', true);
                }
            }
        }

        // Inserts the content as the firstChild of the node.
        if (direction === 'prepend') {
            M.mod_webcast.room.nodeholder.chatlist.prepend(chatline);
        } else {
            M.mod_webcast.room.nodeholder.chatlist.append(chatline);
        }

        // scroll to bottom
        M.mod_webcast.room.scrollbar_chatlist.update('bottom');
    },

    /**
     * Add a local message to chat
     * @param string
     */
    local_message     : function (string) {
        "use strict";
        var message = {
            'messagetype': 'local',
            'message'    : string
        };

        M.mod_webcast.room.chat_add_chatrow(message);
    },
    /**
     * Make sure a message is a valid text
     * @param message
     * @returns string
     */
    chat_parse_message: function (message) {
        "use strict";

        // check if we must replace text by a shortcode
        if (message.charAt(0) === '[' && message.slice(-1) === ']') {
            var newmessage = this.chat_parse_shortcodes(message);
            if (newmessage) {
                return newmessage;
            } else {
                M.mod_webcast.room.log('Error: shortcode not replaced');
            }
        }

        return this.add_emoticons(Y.Node.create("<div/>").setHTML(message).get('text'));
    },

    /**
     * Replace shortcode by special features if they exists
     * @param message
     */
    chat_parse_shortcodes: function (message) {
        "use strict";
        var newmessage = false;

        if (M.mod_webcast.room.shortcode_regex.test(message)) {
            M.mod_webcast.room.log('Has some shortcode:');
            message.replace(M.mod_webcast.room.shortcode_regex, function (a, command, args) {
                M.mod_webcast.room.log(a);
                switch (command) {
                    case 'file':
                        newmessage = M.mod_webcast.room.chat_add_shortcode_file(args);
                        break;
                }
            });
        }

        // trigger the normal functionality
        return newmessage;
    },

    /**
     * get the file details from the server by hash
     */
    chat_add_shortcode_file: function (args) {
        "use strict";
        var message = '';
        M.mod_webcast.room.log('Add file detail to the chat');

        try {
            var obj = Y.JSON.parse(args.slice(1));
            M.mod_webcast.room.log(obj);
            message += '<div class="webcast-file">' +
                '<img src="' + obj.thumbnail + '" alt="" />' +
                '<span class="webcast-filename">' + M.mod_webcast.room.alpha_numeric(obj.filename) + '</span>' +
                '<span class="webcast-filesize">' + M.mod_webcast.room.alpha_numeric(obj.filesize) + '</span>' +
                '<span class="webcast-fileauthor">' + M.mod_webcast.room.alpha_numeric(obj.author) + '</span>' +
                '<a target="_blank" href="' + M.cfg.wwwroot + '/mod/webcast/download.php?' +
                'extra3=' + Number(obj.id) + '&extra2=' + M.mod_webcast.room.options.webcastid + '&extra1=' + M.mod_webcast.room.options.courseid + '&' +
                'sesskey=' + M.cfg.sesskey +
                '" class="webcast-download webcast-button">Download</a>' +
                '</div>';

        } catch (e) {
            M.mod_webcast.room.log(e);
        }
        M.mod_webcast.room.log(message);
        return message;
    },

    /**
     * Convert timestamp
     * @param unix_timestamp
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
        var message = String(M.mod_webcast.room.nodeholder.message.get('value'));

        // Check if the message is a command
        if (message.charAt(0) === '/') {
            this.chat_commands(message);
            return;
        }

        // Prevent html tags [this will not prevent all more security on server side and when adding the message]
        var regex = new RegExp('/(<([^>]+)>)/ig');
        message = message.replace(regex, "");

        M.mod_webcast.room.log('Send: ' + message);
        if (message.length === 0) {
            return;
        }

        M.mod_webcast.room.chatobject.message = message;
        M.mod_webcast.room.socket.emit("send", M.mod_webcast.room.chatobject, function (response) {
            if (!response.status) {
                M.mod_webcast.room.exception(response.error);
            }
        });

        // clear
        M.mod_webcast.room.chatobject.message = "";
        M.mod_webcast.room.nodeholder.message.set('value', "");
    },

    /**
     * Set a exception
     * @param errorstring
     */
    exception: function (errorstring) {
        "use strict";
        M.mod_webcast.room.log('ERROR: ' + errorstring);
    },

    /**
     * Returns a alpha numeric string to prevent xss
     * @param string
     * @returns string
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
        M.mod_webcast.room.log('add_userlist');

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
     * @param data
     */
    update_userlist: function (data) {
        "use strict";
        M.mod_webcast.room.log(data);

        if (!data.status) {
            return;
        }

        // Setting vars
        var htmlbroadcaster = '', htmlteachers = '', htmlstudents = '', htmlguests = '', key, userobject, li;

        for (key in data.users) {

            if (data.users.hasOwnProperty(key)) {

                userobject = data.users[key];

                this.log(userobject);

                li = '<li id="userlist-user-' + Number(userobject.userid) + '" class="webcast-' + M.mod_webcast.room.alpha_numeric(userobject.usertype) + ' noSelect">';

                if (M.mod_webcast.room.options.showuserpicture) {
                    li += '<img src="' + M.cfg.wwwroot + '/user/pix.php?file=/' + Number(userobject.userid) + '/f1.jpg" />';
                }

                li += '<span class="fullname">' + M.mod_webcast.room.alpha_numeric(userobject.fullname) + '</span>' +
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

        M.mod_webcast.room.nodeholder.userlist.setHTML(htmlbroadcaster + htmlteachers + htmlstudents + htmlguests);

        // Update scrollbar
        M.mod_webcast.room.scrollbar_userlist.update();

        // Update the counter
        M.mod_webcast.room.nodeholder.userlist_counter.set('text', ' (' + data.count + ') ');
    },

    /**
     * clear the userlist if connection fail
     */
    reset_userlist: function () {
        "use strict";
        M.mod_webcast.room.log('build_room');
        M.mod_webcast.room.nodeholder.userlist.setHTML('');
        // Update the counter
        M.mod_webcast.room.nodeholder.userlist_counter.set('text', ' (0) ');

        // Update scrollbar
        M.mod_webcast.room.scrollbar_userlist.update();
    },

    /**
     *
     */
    add_fileshare: function () {
        "use strict";

        this.nodeholder.filemanagerdialog = Y.one("#webcast-filemanger-dialog");
        this.nodeholder.fileoverviewdialog = Y.one("#webcast-fileoverview-dialog");

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

        Y.one('#webcast-filemanager-btn').on('click', function () {
            this.log('Filemanager');
            if ((this.nodeholder.filemanagerdialog.get('offsetWidth') === 0 &&
                this.nodeholder.filemanagerdialog.get('offsetHeight') === 0) ||
                this.nodeholder.filemanagerdialog.get('display') === 'none') {
                this.log('Show');
                this.nodeholder.filemanagerdialog.show();
            } else {
                this.log('Hide');
                this.nodeholder.filemanagerdialog.hide();
            }

            this.scale_room();
        }, this);

        Y.one('#webcast-fileoverview-btn').on('click', function () {
            this.log('Filemanager');
            if ((this.nodeholder.filemanagerdialog.get('offsetWidth') === 0 &&
                this.nodeholder.filemanagerdialog.get('offsetHeight') === 0) ||
                this.nodeholder.filemanagerdialog.get('display') === 'none') {
                this.log('Show');
                this.nodeholder.filemanagerdialog.show();
            } else {
                this.log('Hide');
                this.nodeholder.filemanagerdialog.hide();
            }

            this.scale_room();
        }, this);

        this.log('add_fileshare @todo');
    },

    /**
     * Show a warning before closing
     */
    warning_message_closing_window: function () {
        "use strict";
        window.onbeforeunload = function () {
            return M.util.get_string('js:warning_message_closing_window', 'webcast', {});
        };
    },

    /**
     * Scale room
     * Should be executed when the browser window resize
     */
    scale_room: function () {
        "use strict";
        var winWidth = this.nodeholder.body.get("winWidth");
        var winHeight = this.nodeholder.body.get("winHeight");
        var wh;

        // set elements one time
        if (this.nodeholder.userlist_viewport === null) {
            this.nodeholder.userlist_viewport = Y.one('#webcast-userlist .viewport');
            this.nodeholder.chatlist_viewport = Y.one('#webcast-chatlist .viewport');
        }

        M.mod_webcast.room.log('scale_room for : ' + winWidth + 'x' + winHeight);

        if (this.options.stream) {

            // scale video component
            var videowidth = winWidth - 401 - 40;
            var videoheight = Math.round((videowidth / 16) * 9);
            var maxvideoheight = (winHeight - 70 - 80);

            // Make sure everything fits the screen
            if (videoheight > maxvideoheight) {
                M.mod_webcast.room.log('Video to high we set it to: ' + maxvideoheight);
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

        } else {

            // only userlist
            wh = winHeight - ((36) + 50 + 100);
            Y.one('#webcast-userlist .viewport').setStyles({
                height: wh
            });
            this.scrollbar_userlist.update();
        }

        // filesharing
        if (this.options.filesharing) {

            this.nodeholder.filemanagerdialog.setStyles({
                height      : (wh + 25),
                'margin-top': -(wh + 25)
            });
        }

    },

    /**
     * Set options base on listed options
     * @param options
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
     * @param object
     * @param type
     * @param callback
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