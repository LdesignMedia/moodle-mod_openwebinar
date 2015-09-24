Moodle openwebinar - Activity
====================
This plugin allows you to give a live webinar inside moodle with allot of features. All services/plugins/libs that are used are open source. 

Author
====================
![MoodleFreak.com](http://moodlefreak.com/logo_small.png)

Author: Luuk Verhoeven, [MoodleFreak.com](http://www.moodlefreak.com/)

Requires at least: Moodle 2.6+

Description
====================
MoodleFreak Openwebinar provides the functionality to give a live stream and chat in moodle. You can schedule a meeting and give your live openwebinar. There is one public chat room.
Most preference of the features can be globally set in settings. Settings can be overridden on instance level to be extra customisable. 

Everything we are using is open source no need for Flash media server, Adobe Connect, Gotomeeting or other paid services. You can setup this in your own environment see the how to on moodlefreak here. 

List of features
====================

#### Room
  - Auto room scaling
  - Live and offline mode 
  - max 3 fully customisable reminder e-mail message (notify users about the openwebinar)
  - User activity pinger to measure online time in the room
  - YUI 3 javascript module
  - Completion triggered on entering the live openwebinar
  - Auto closing live rooms
  
#### Chat
  - Live socket chat
  - Saving chat log to moodle DB
  - Emoticons in chat
  - Loading previous message in the chat
  - Users in the room list sorted by status
  - Users in room list include browser and OS version
  - New message sound
  - Support chat commands in message box 
    - /clear  clear all messages in your chat overview

#### Broadcaster
  - Broadcaster can mute chat messages from guests, students and teachers separately
  - Broadcaster can close / end a openwebinar

#### Users
  - Chat control panel for user in the room
  - Chat in the room

#### Video
  - View live and offline video stream
  - Stream RTMP (Real Time Messaging Protocol) viewer
  - Stream HLS (allow almost all devices) with fallback support
  - Modern video api videojs included for more playback support
  - Adding offline or already given video to a openwebinar
  - Fullscreen video mode

#### File sharing
  - File sharing adding files to the chat
  - User friendly file overview

#### User activity in openwebinar overview
  - See how long users where in the room
  - See who visited the openwebinar
  - See chatlog of 1 specific user

Installation
====================
1.  copy this plugin to the mod\openwebinar folder on the server
2.  login as administrator
3.  go to Site Administrator > Notification
4.  install the plugin

Installation broadcaster
====================
The openwebinar it self is all webbased. But to stream a openwebinar as broadcaster you will need some software to stream a RTMP stream. 
We used open broadcaster for this what is also a open source project. The manual to use it for this plugin can be found here.

Installation dependenties
====================
This won't run without a Node.js socket.io / chat server. The chat server is developed by MoodleFreak and can be found on ...

Also for live openwebinar you need a rtmp streaming server. We build this speciality for nginx-rtmp because its free to setup. Chances are high that another RTMP server will also work. 

Keep in mind streaming will cost lot of bandwidth. 

Changelog
====================

See version control for the complete history, major changes in this versions will be list below.