Moodle openwebinar - Activity
====================
This plugin allows you to give a live webinar inside Moodle with a lot of features. All services/plugins/libs which are used are open source.

Author
====================

![MFreak.nl](https://mfreak.nl/logo_small.png)

Author: Luuk Verhoeven, [MFreak.nl](http://MFreak.nl/)

Min. required: Moodle 2.6+

### Project Status
[![Build Status](https://travis-ci.org/MFreakNL/moodle_mod_openwebinar.svg?branch=master)](https://travis-ci.org/MFreakNL/moodle_mod_openwebinar/)

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
## Debian
https://wiki.debian.org/DhyanNataraj/RtmpVideoStreamingViaNginx

Files or folders that doesn't exists must be created!

```
Error debuild - try
dpkg-buildpackage -uc -us
```

## Ubuntu - not working on debian
```bash

apt-get -y update; \
apt-get -y install software-properties-common dpkg-dev git; \
add-apt-repository -y ppa:nginx/stable; \
sed -i '/^#.* deb-src /s/^#//' /etc/apt/sources.list.d/nginx-ubuntu-stable-xenial.list; \
apt-get -y update; \
apt-get -y source nginx; \
cd $(find . -maxdepth 1 -type d -name "nginx*") && \
ls -ahl && \
git clone https://github.com/arut/nginx-rtmp-module.git && \
sed -i "s|common_configure_flags := \\\|common_configure_flags := \\\--add-module=$(cd  nginx-rtmp-module && pwd) \\\|" debian/rules && \
cat debian/rules && echo "^^" && \
apt-get -y build-dep nginx && \
dpkg-buildpackage -b && \
cd .. && ls -ahl && \
dpkg --install $(find . -maxdepth 1 -type f -name "nginx-common*") && \
dpkg --install $(find . -maxdepth 1 -type f -name "libnginx*") && \
dpkg --install $(find . -maxdepth 1 -type f -name "nginx-full*"); \
apt-get -y remove software-properties-common dpkg-dev git; \
apt-get -y install aptitude; \
aptitude -y markauto $(apt-cache showsrc nginx | sed -e '/Build-Depends/!d;s/Build-Depends: \|,\|([^)]*),*\|\[[^]]*\]//g'); \
apt-get -y autoremove; \
apt-get -y remove aptitude; \
apt-get -y autoremove; \
rm -rf ./*nginx*
```

```apacheconfig
#https://www.digitalocean.com/community/tutorials/how-to-optimize-nginx-configuration
worker_processes  1;

events {
    worker_connections  65536;
}

http {
    include             mime.types;
    default_type        application/octet-stream;
    access_log          off;
    keepalive_timeout   65;


    #ADMIN controll/stats
    server {

        listen          8080;

        location /hls {
                # Serve HLS fragments

                types {
                        application/vnd.apple.mpegurl m3u8;
                        video/mp2t ts;
                }
                root /tmp;
                add_header Cache-Control no-cache;
                add_header Access-Control-Allow-Origin *;
       }
    }
}

#START THE RTMP PART
rtmp {
        server {
                listen 1935;
                chunk_size 4096;
                log_format new '$remote_addr $msec  $command "$app" "$name" "$args" $bytes_received $bytes_sent "$pageurl" "$flashver" ($session_readable_time)';
                access_log logs/rtmp_access.log new;

                application live {
                        live on;
                        publish_notify on;
                        record off;
                }
                application hls {
                       live on;
                       hls on;
                       #hls_playlist_length 8s;
                       #hls_fragment 2s;
                       hls_path /tmp/hls;
               }
        }
}

```


Changelog
====================

See version control for the complete history. Major changes in this version will be listed below.

- Adding travis
