/**
 * The broadcast room
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @package mod_webcast
 * @copyright 2015 MoodleFreak.com
 * @author Luuk Verhoeven
 **/
/*jslint browser: true*/
/*global  M, Y, videojs, console*/
M.mod_webcast = M.mod_webcast || {};
M.mod_webcast.room = {

    /**
     * Webcast variables
     */
    options: {
        'debugjs'            : true,
        'chat'               : false,
        'duration'           : 0,
        'timeopen'           : 0,
        'cmid'               : 0,
        'courseid'           : 0,
        'webcastid'          : 0,
        'filesharing'        : false,
        'filesharing_student': false,
        'is_ended'           : false,
        'showuserpicture'    : false,
        'stream'             : false,
        'broadcaster'        : -999,
        'broadcastkey'       : "broadcastkey",
        'shared_secret'      : "",
        'streaming_server'   : "",
        'chat_server'        : "",
        'fullname'           : "",
        'usertype'           : "",
        'userid'             : 0,
        'userlist'           : false
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
    socket_is_connected: false,

    /**
     * Videojs player
     */
    player: null,

    /**
     * Chat template
     */
    chatobject: {
        'cmid'         : 0,
        'courseid'     : 0,
        'webcastid'    : 0,
        'userid'       : 0,
        'fullname'     : "",
        'broadcastkey' : "",
        'room'         : "_public",
        'shared_secret': "",
        'hostname'     : "",
        'message'      : "",
        'usertype'     : "guest"
    },

    nodeholder: {
        'chatlist'        : null,
        'userlist'        : null,
        'userlist_counter': null,
        'sendbutton'      : null,
        'message'         : null
    },
    /**
     * Internal logging
     * @param val
     */
    log       : function (val) {
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
     * Init
     */
    init: function (options) {
        "use strict";

        // Make sure its loaded
        if (typeof videojs.options === 'undefined') {
            this.log('wait..');
            setTimeout(function () {
                this.init(options);
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
        this.log('connect_to_socket');

        // Connect
        this.socket = io.connect(this.options.chat_server);

        // Nodes
        this.nodeholder.sendbutton = Y.one('#webcast-send');
        this.nodeholder.message = Y.one('#webcast-message');

        this.socket.on('connect', function () {
            M.mod_webcast.room.log('isConnected');
            M.mod_webcast.room.socket_is_connected = true;

            // enable chatinput
            M.mod_webcast.room.nodeholder.message.removeAttribute('disabled');
            M.mod_webcast.room.nodeholder.sendbutton.set('text', M.util.get_string('javascript:send', 'webcast'));
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

        // Set the template
        for (var k in this.chatobject) {
            if (typeof  this.options[k] !== 'undefined') {
                this.chatobject[k] = this.options[k];
            }
        }

        // add hostname
        this.chatobject.hostname = window.location.hostname;
    },

    /**
     * socket_connection_failed
     * @param message
     */
    socket_connection_failed: function (message) {
        M.mod_webcast.room.log(message);
        M.mod_webcast.room.socket_is_connected = false;

        // disable chat input
        M.mod_webcast.room.nodeholder.message.setAttribute('disabled', 'disabled');
        M.mod_webcast.room.nodeholder.sendbutton.set('text', M.util.get_string('javascript:wait_on_connection', 'webcast', {}));

        M.mod_webcast.room.local_message(message);
    },

    /**
     * Build the room and add the components that are enabled
     */
    build_room: function () {

        this.log('build_room');

        // Connect to socket
        this.connect_to_socket();

        // add stream component
        if (this.options.stream) {
            this.add_video();
        }

        // add the userlist
        if (this.options.userlist) {
            this.add_userlist();
        }

        // add the chat
        if (this.options.chat) {
            this.add_chat();
        }

        // add file sharing
        if ((this.options.filesharing_student && this.options.broadcaster == this.options.userid) ||
            this.options.filesharing_student) {

            this.add_fileshare();
        }

        // scaling listener
        this.add_event(window, "resize", function () {
            M.mod_webcast.room.scale_room();
        });

        // first time scale the room
        setTimeout(function () {
            M.mod_webcast.room.scale_room();
        }, 300);
    },

    /**
     * Add videojs component
     */
    add_video: function () {
        this.log('add_video');
        this.log(typeof videojs);

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

        // Set player settings
        this.player = videojs('room_stream', {
            techOrder: ['flash', 'html5'],
            autoplay : true,
            preload  : 'auto',
            sources  : [{
                type: "rtmp/mp4",
                src : "rtmp://" + this.options.streaming_server + '/' + this.options.broadcastkey
            }]
        });

        this.log("rtmp://" + this.options.streaming_server + '/' + this.options.broadcastkey);
    },

    /**
     * Add chat component
     */
    add_chat: function () {

        this.log('add_chat');

        // add tinyscrollbar
        var el = document.getElementById("webcast-chatlist");
        this.scrollbar_chatlist = tinyscrollbar(el);
        this.nodeholder.chatlist = Y.one('#webcast-chatlist ul');

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

        // Workaround for enterkey YUI event not working here..
        this.nodeholder.message.setAttribute('onkeypress', 'return M.mod_webcast.room.chat_enter_listener(event);');
    },

    /**
     * Check if enter is pressed send the message
     * @param e
     * @returns {boolean}
     */
    chat_enter_listener: function (e) {
        if (e.keyCode == 13) {
            M.mod_webcast.room.chat_send_message();
            return false;
        }
    },

    /**
     *
     */
    chat_add_chatrow: function (data) {
        var chatline = '';

        // build the chatline and make sure nothing strange happens XSS!
        if (data.messagetype === 'default') {

            var me = (data.userid == M.mod_webcast.room.options.userid);

            // Start
            chatline += '<li class="webcast-chatline webcast-' + M.mod_webcast.room.alpha_numeric(data.usertype) + ' ' + (me ? 'me' : '') + '">';

            // Add avatar
            if (M.mod_webcast.room.options.showuserpicture) {
                chatline += '<span class="webcast-avatar"><img src="' + M.cfg.wwwroot + '/user/pix.php?file=/' + Number(data.userid) + '/f1.jpg" /></span>';
            }

            // Fullname
            chatline += '<span class="webcast-username" data-userid="' + Number(data.userid) + '">' + (me ? M.util.get_string('javascript:me', 'webcast', {}) : M.mod_webcast.room.alpha_numeric(data.fullname)) + '</span>';

            // Time
            chatline += '<span class="webcast-timestamp">' + M.mod_webcast.room.timestamp_to_humanreadable(data.timestamp) + '</span>';

            // Message
            chatline += '<span class="webcast-message">' + M.mod_webcast.room.escape_message(data.message) + '</span>';

        } else if (data.messagetype === 'system') {

            // Messages generate by server
            chatline += '<li class="webcast-chatline webcast-socketserver">';

            // Time
            chatline += '<span class="webcast-timestamp">' + M.mod_webcast.room.timestamp_to_humanreadable(data.timestamp) + '</span>';

            // Message
            chatline += '<span class="webcast-message">' + M.util.get_string('javascript:' + data.message, 'webcast' , {}) + '</span>';
        } else if (data.messagetype === 'local') {

            // Messages generate by this script local
            chatline += '<li class="webcast-chatline webcast-local">';

            // Time
            var date = new Date().getTime() / 1000;
            chatline += '<span class="webcast-timestamp">' + M.mod_webcast.room.timestamp_to_humanreadable(date) + '</span>';

            // Message
            chatline += '<span class="webcast-message">' + M.util.get_string('javascript:' + data.message, 'webcast', {}) + '</span>';

        }

        // Ending
        chatline += '</li>';

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
        return Y.Node.create("<div/>").setHTML(message).get('text');

    },

    /**
     * Convert timestamp
     * @param unix_timestamp
     * @returns {string}
     */
    timestamp_to_humanreadable: function (unix_timestamp) {

        var date = new Date(unix_timestamp * 1000);
        var hours = date.getHours();
        var minutes = "0" + date.getMinutes();
        var seconds = "0" + date.getSeconds();

        return hours + ':' + minutes.substr(-2) + ':' + seconds.substr(-2);
    },

    /**
     * Send a message to chat server
     */
    chat_send_message: function () {

        var message = String(M.mod_webcast.room.nodeholder.message.get('value'));

        // Prevent html tags [this will not prevent all more security on server side and when adding the message]
        message = message.replace(/(<([^>]+)>)/ig, "");

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
     * @todo make something nicer here write to a div
     * @param errorstring
     */
    exception: function (errorstring) {
        M.mod_webcast.room.log('ERROR: ' + errorstring);
    },

    /**
     * Returns a alpha numeric string to prevent xss
     * @param string
     * @returns string
     */
    alpha_numeric: function (string) {
        return string.replace(/^[a-z0-9]+$/i, "");
    },

    /**
     * Show the userlist
     */
    add_userlist: function () {

        M.mod_webcast.room.log('add_userlist');

        // set userlist node prevent searching the dom again
        this.nodeholder.userlist = Y.one('#webcast-userlist ul');
        this.nodeholder.userlist_counter = Y.one('#webcast-usercounter');

        // add tinyscrollbar
        var el = document.getElementById("webcast-userlist");
        this.scrollbar_userlist = tinyscrollbar(el);

        // Userlist listener
        this.socket.on("update-user-list", function (data) {

            M.mod_webcast.room.log(data);

            // rebuild the list
            var htmlbroadcaster = '';
            var htmlteachers = '';
            var htmlstudents = '';
            var htmlguests = '';

            for (var i in data.users) {

                if (data.users.hasOwnProperty(i)) {

                    var user = data.users[i];
                    var element = '<li id="userlist-user-' + Number(user.userid) + '" class="webcast-' + M.mod_webcast.room.alpha_numeric(user.usertype) + ' noSelect">';

                    if (M.mod_webcast.room.options.showuserpicture) {
                        element += '<img src="' + M.cfg.wwwroot + '/user/pix.php?file=/' + Number(user.userid) + '/f1.jpg" />';
                    }

                    element += '<span>' + M.mod_webcast.room.alpha_numeric(user.fullname) + '</span>';
                    element += '</li>';

                    switch (user.usertype) {
                        case 'broadcaster':
                            htmlbroadcaster += element;
                            break;
                        case 'teacher':
                            htmlteachers += element;
                            break;
                        case 'student':
                            htmlstudents += element;
                            break;
                        default:
                            htmlguests += element;
                    }
                }
            }

            M.mod_webcast.room.nodeholder.userlist.setHTML(htmlbroadcaster + htmlteachers + htmlstudents + htmlguests);

            // Update scrollbar
            M.mod_webcast.room.scrollbar_userlist.update();

            // Update the counter
            M.mod_webcast.room.nodeholder.userlist_counter.set('text', ' (' + data.count + ') ');
        });
    },

    /**
     *
     */
    add_fileshare: function () {

        this.log('add_fileshare @todo');
    },

    /**
     * Scale room
     * Should be executed when the browser window resize
     */
    scale_room: function () {

        var winWidth = Y.one("body").get("winWidth");
        var winHeight = Y.one("body").get("winHeight");
        var wh;

        M.mod_webcast.room.log('scale_room for : ' + winWidth + 'x' + winHeight);

        if (this.options.stream) {

            // scale video component
            var videowidth = parseInt(Y.one('#webcast-left').getComputedStyle('width')) - 40;
            var videoheight = Math.round((videowidth / 16) * 9);

            Y.one('#room_stream').setStyles({
                height: videoheight,
                width : videowidth
            });
        }

        // scale userlist and chat
        if (this.options.stream && this.options.chat) {
            // 30% 70% - 200 for the other components
            wh = winHeight - 200;

            Y.one('#webcast-userlist .viewport').setStyles({
                height: wh * 0.3
            });
            this.scrollbar_userlist.update();

            Y.one('#webcast-chatlist .viewport').setStyles({
                height: wh * 0.7
            });
            this.scrollbar_chatlist.update();

        } else if (this.options.stream && !this.options.chat) {

            // only chat
            wh = winHeight - 150;
            Y.one('#webcast-chatlist .viewport').setStyles({
                height: wh
            });
            this.scrollbar_chatlist.update();

        } else {

            // only userlist
            wh = winHeight - 150;
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

        for (var key in this.options) {

            if (this.options.hasOwnProperty(key) && options.hasOwnProperty(key)) {

                // casting to prevent errors
                var vartype = typeof this.options[key];
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
        if (object === null || typeof(object) === 'undefined') {
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