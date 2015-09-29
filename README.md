Moodle openwebinar - Activity
====================
This plugin allows you to give a live webinar inside Moodle with a lot of features. All services/plugins/libs which are used are open source.

Author
====================
![MoodleFreak.com](http://moodlefreak.com/logo_small.png)

Author: Luuk Verhoeven, [MoodleFreak.com](http://www.moodlefreak.com/)

Min. required: Moodle 2.6+

Description
====================
MoodleFreak Openwebinar provides the function of live streaming and chatting in Moodle. You are able to schedule a meeting and give a live openwebinar. There is one public chat room. Most preference of the features can be generally set in settings. Settings can be overridden on instance level to be more customizable.

Everything we are using is open source. There is no need for Flash media server, Adobe Connect, Gotomeeting or other paid services. You can set this up in your own environment. For more information on Moodlefreak click here.

List of features
====================

#### Room
  - Auto room scaling
  - Live and offline mode 
  - Max. 3 completely customizable reminder e-mail messages (to notify users about the openwebinar)
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
  - Supported chat commands in message field 
    - `/clear` This removes all messages in your chat overview

#### Broadcaster
  - Broadcaster can mute chat messages from guests, students and teachers separately
  - Broadcaster can close/end an openwebinar

#### Users
  - Chat control panel for user in the room
  - Chat in the room

#### Video
  - View live and offline video stream
  - Stream RTMP (Real Time Messaging Protocol) viewer
  - Stream HLS (allow almost all devices) with fallback support
  - Modern video api videojs included for more playback support
  - Adding offline or already given video to an openwebinar
  - Fullscreen video mode

#### File sharing
  - File sharing adding files to the chat
  - User friendly file overview

#### User activity in openwebinar overview
  - See how long users have been in the room
  - See who visited the openwebinar
  - See chatlog of one specific user

Installation
====================
1.  copy this plugin to the `mod\openwebinar` folder on the server
2.  login as administrator
3.  go to Site Administrator > Notification
4.  install the plugin
5.  you will need to fill out the settings. Please keep in mind you have the options to use a hosted setup or setup all dependencies yourself.

Installation broadcaster
====================
The openwebinar itself is completely webbased. But to stream a openwebinar as broadcaster you will need some software to stream a RTMP stream. 

We’ve used open broadcaster for this which is also an open source project. The users manual for this plugin can be found here.

Installation dependencies
====================
The openwebinar won't run without a **Chat server**. The chat server is developed by **MoodleFreak** and can be found on [openwebinar_chatserver](https://github.com/MoodleFreak/openwebinar_chatserver)

For the openwebinar you will also need a **rtmp streaming server**. We’ve build this especially for [nginx-rtmp](https://github.com/arut/nginx-rtmp-module) because its free and open source. Chances are high that another RTMP server will also work. 

**Note: Keep in mind that streaming will use a lot of bandwidth** 

Installation - nginx-rtmp for `openwebinar`
====================
@todo

Changelog
====================

See version control for the complete history. Major changes in this version will be listed below.