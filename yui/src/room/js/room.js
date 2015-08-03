/**
 * The broadcast room
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @package mod_webcast
 * @copyright 2015 MoodleFreak.com
 * @author Luuk Verhoeven
 **/
/*jslint browser: true, white: true, vars:true, regexp: true*/
/*global  M, Y, videojs, console, io, tinyscrollbar*/
M.mod_webcast = M.mod_webcast || {};
M.mod_webcast.room = {

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
        is_ended           : false,
        showuserpicture    : false,
        stream             : false,
        broadcaster        : -999,
        broadcastkey       : "broadcastkey",
        shared_secret      : "",
        streaming_server   : "",
        chat_server        : "",
        fullname           : "",
        usertype           : "",
        userid             : 0,
        userlist           : false,
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
        chatlist         : null,
        userlist         : null,
        topmenu         : null,
        loadhistorybtn   : null,
        userlist_counter : null,
        sendbutton       : null,
        body             : null,
        userlist_viewport: null,
        chatlist_viewport: null,
        message          : null
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
     * Build the room and add the components that are enabled
     */
    build_room: function () {
        "use strict";
        this.log('build_room');

        // Set body node
        this.nodeholder.body = Y.one("body");
        this.nodeholder.topmenu = Y.one("#webcast-topbar-left");

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
            M.mod_webcast.room.chat_send_message();
        });

        this.nodeholder.loadhistorybtn.on('click', function () {
            M.mod_webcast.room.loadhistory();
        });

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
    chat_add_chatrow: function (data) {
        "use strict";

        M.mod_webcast.room.log('chat_add_chatrow');
        M.mod_webcast.room.log(data);
        // Setting vars
        var chatline = '', date = 0, me = false;

        // build the chatline and make sure nothing strange happens XSS!
        if (data.messagetype === 'default') {

            me = (data.userid === M.mod_webcast.room.options.userid);

            // Start
            chatline += '<li class="webcast-chatline webcast-' + M.mod_webcast.room.alpha_numeric(data.usertype) + ' ' + (me ? 'me' : '') + '">';

            // Add avatar

            // Fullname
            chatline += '<div class="message-container">';

            if (M.mod_webcast.room.options.showuserpicture) {
                chatline += '<span class="webcast-avatar"><img src="' + M.cfg.wwwroot + '/user/pix.php?file=/' + Number(data.userid) + '/f1.jpg" /></span>';
            }

            chatline += '<span class="webcast-username" data-userid="' + Number(data.userid) + '">' + M.mod_webcast.room.alpha_numeric(data.fullname) + '</span>' +
                '<span class="webcast-timestamp">' + M.mod_webcast.room.timestamp_to_humanreadable(data.timestamp) + '</span>' +
                '<span class="webcast-message">' + M.mod_webcast.room.escape_message(data.message) + '</span>' +
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

        // add row
        M.mod_webcast.room.nodeholder.chatlist.append(chatline);

        // scroll to bottom
        M.mod_webcast.room.scrollbar_chatlist.update('bottom');
    },

    /**
     * Add a local message to chat
     * @param string
     */
    local_message : function (string) {
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
    escape_message: function (message) {
        "use strict";
        return Y.Node.create("<div/>").setHTML(message).get('text');

    },

    /**
     * Convert timestamp
     * @param unix_timestamp
     * @returns {string}
     */
    timestamp_to_humanreadable: function (unix_timestamp) {
        "use strict";
        var date = new Date(unix_timestamp * 1000);
        var hours = date.getHours();
        var minutes = "0" + date.getMinutes();
        var seconds = "0" + date.getSeconds();

        return ((hours > 12) ? 'PM ' : 'AM ') + hours + ':' + minutes.substr(-2) + ':' + seconds.substr(-2);
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
        return string.replace(/^[a-z0-9]+$/i, "");
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
                li = '<li id="userlist-user-' + Number(userobject.userid) + '" class="webcast-' + M.mod_webcast.room.alpha_numeric(userobject.usertype) + ' noSelect">';

                if (M.mod_webcast.room.options.showuserpicture) {
                    li += '<img src="' + M.cfg.wwwroot + '/user/pix.php?file=/' + Number(userobject.userid) + '/f1.jpg" />';
                }

                li += '<span>' + M.mod_webcast.room.alpha_numeric(userobject.fullname) + '</span>';
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

            Y.one('#webcast-chatlist .viewport').setStyles({
                height: wh * 0.7
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
            wh = winHeight  - ((36) + 50 + 100);
            Y.one('#webcast-userlist .viewport').setStyles({
                height: wh
            });
            this.scrollbar_userlist.update();
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