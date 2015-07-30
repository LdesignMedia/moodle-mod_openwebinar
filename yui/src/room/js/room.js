/**
 * The broadcast room
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @package mod_webcast
 * @copyright 2015 MoodleFreak.com
 * @author Luuk Verhoeven
 **/

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
        userlist           : false
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
        'usertype'     : "guest",
    },

    nodeholder: {
        'userlist'        : null,
        'userlist_counter': null,
        'sendbutton': null,
        'message': null,
    },
    /**
     * Internal logging
     * @param val
     */
    log       : function (val) {

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
            var timeout = setTimeout(function () {
                this.init(options);
            }, 100);
        }

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

        this.socket.on('connect', function() {
            M.mod_webcast.room.log('isConnected');
            M.mod_webcast.room.socket_is_connected = true;

            // enable chatinput
            M.mod_webcast.room.nodeholder.message.removeAttribute('disabled');
            M.mod_webcast.room.nodeholder.sendbutton.set('text' , M.util.get_string('javascript:send', 'webcast'));
        });

        //
        this.socket.on('reconnect_failed', function() {
            M.mod_webcast.room.socket_connection_failed('reconnect_failed');
        });

        //
        this.socket.on('disconnect', function() {
            M.mod_webcast.room.socket_connection_failed('disconnect');
        });


        // Set the template
        for (var k in this.chatobject) {

            if (typeof  this.options[k] !== 'undefined') {
                this.chatobject[k] = this.options[k]
            }
        }

        // add hostname
        this.chatobject.hostname = window.location.hostname;

    },

    /**
     * socket_connection_failed
     * @param message
     */
    socket_connection_failed : function(message){
        M.mod_webcast.room.log(message);
        M.mod_webcast.room.socket_is_connected = false;

        // disable chat input
        M.mod_webcast.room.nodeholder.message.setAttribute('disabled' , 'disabled');
        M.mod_webcast.room.nodeholder.sendbutton.set('text' , M.util.get_string('javascript:wait_on_connection', 'webcast'));
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
        this.add_event(window, "resize", function (event) {
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
     * add chat
     */
    add_chat: function () {

        this.log('add_chat');

        // add tinyscrollbar
        var el = document.getElementById("webcast-chatlist");
        this.scrollbar_chatlist = tinyscrollbar(el);

        // Join the public room
        this.socket.emit("join", this.chatobject, function (response) {
            if (!response.status) {
                M.mod_webcast.room.exception(response.error);
            }
        });
    },

    /**
     * @todo make something nicer here write to a div
     * @param errorstring
     */
    exception   : function (errorstring) {
        M.mod_webcast.room.log('ERROR: ' + errorstring);
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

                var user = data.users[i];
                var element = '<li id="userlist-user-' + user.userid + '" class="webcast-' + user.usertype + ' noSelect">';

                if (M.mod_webcast.room.options.showuserpicture) {
                    element += '<img src="' + M.cfg.wwwroot + '/user/pix.php?file=/' + user.userid + '/f1.jpg" />';
                }

                element += '<span>' + user.fullname + '</span>';
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

        this.log('add_fileshare');
    },

    /**
     * Scale
     */
    scale_room: function () {

        var winWidth = Y.one("body").get("winWidth");
        ;
        var winHeight = Y.one("body").get("winHeight");

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
            var wh = winHeight - 200;

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
            var wh = winHeight - 150;
            Y.one('#webcast-chatlist .viewport').setStyles({
                height: wh
            });
            this.scrollbar_chatlist.update();

        } else {

            // only userlist
            var wh = winHeight - 150;
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

            if (typeof options[key] !== "undefined") {

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
        if (object == null || typeof(object) == 'undefined') {
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
}