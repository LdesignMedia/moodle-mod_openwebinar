<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Strings for component 'mod_openwebinar', language 'nl'
 *
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @package   mod_openwebinar
 * @copyright 2015 MoodleFreak.com
 * @author    Luuk Verhoeven
 **/
$string['modulename'] = 'MoodleFreak Openwebinar';
$string['modulenameplural'] = 'MoodleFreak Openwebinar';
$string['modulename_help'] = 'Openwebinar activiteit:<br/>
<b>Room</b>
<ul>
	<li>De chat is schaalbaar (responsive)</li>
	<li>Live en offline modus</li>
	<li>Herinnering emails</li>
	<li>Online tijd in de room meten</li>
	<li>YUI 3 javascript modulen</li>
	<li>Activiteit completion</li>
	<li>Niet afgesloten chats worden automatisch gesloten</li>
</ul>
<b>Chat</b>
<ul>
	<li>Live socket chat</li>
	<li>Chat geschiedenis word opgeslagen voor later</li>
	<li>Emoticons zijn te gebruiken in de chat</li>
	<li>Inladen van chat geschiedenis mogelijk</li>
	<li>Gebruikers lijst , sorteert op type</li>
	<li>Gebruikers lijst met browser en OS version</li>
	<li>Geluid bij een nieuwe bericht</li>
	<li>Ondersteuning voor de volgende chat commandos:
	<ul>
		<li>/clear Wis alle berichten uit het overzicht (lokaal)</li>
	</ul>
	</li>
</ul>
<b>Uitzender / Presentator</b>
<ul>
	<li>Berichten los blokkeren per status: gast, students en leraar</li>
	<li>Openwebinar afsluiten na voltooiing</li>
	<li>Vragen uitzetten naar de andere gebruikers in de chatruimte</li>
</ul>
<b>Gebruikers</b>
<ul>
	<li>Chatruimte kan aangepast worden via het configuratiescherm</li>
	<li>Berichten sturen in de chatruimte</li>
	<li>Vragen beantwoorde</li>
	<li>Stream bekijken</li>
</ul>
<b>Video</b>
<ul>
	<li>Bekijken van live en offline video stream</li>
	<li>Streamen via RTMP (Real Time Messaging Protocol)</li>
	<li>Stream in HLS (ondersteunt op de meeste apparaten) </li>
	<li>Videojs gebruikt als video component</li>
	<li>Toevoegen van een eerder gemaakte video</li>
	<li>Video kan in volledig scherm afgespeeld worden</li>
</ul>
<b>Bestanden delen</b>
<ul>
	<li>Delen van bestanden in een openwebinar</li>
	<li>Los overzicht van bestanden die in de chat toegevoegd zijn</li>
</ul>
<b>Gebruikers logboek</b>
<ul>
	<li>Tijd dat een gebruiker in de openwebinar is geweest</li>
	<li>Bekijk wie de openwebinar heeft bekeken</li>
	<li>Bekijk de chat geschiedenis van een specifieke gebruiker.</li>
</ul>';

$string['modulename_link'] = 'mod/openwebinar/view';
$string['pluginadministration'] = 'MoodleFreak openwebinar administrator';
$string['pluginname'] = 'MoodleFreak openwebinar';
$string['openwebinarname'] = 'MoodleFreak openwebinar';
$string['openwebinarname_help'] = 'De naam van de openwebinar';
$string['openwebinar'] = 'MoodleFreak openwebinar';
$string['task:auto_close'] = 'Sluit chatruimte wanneer deze is afgelopen en niet is afgesloten door de presentator.';
$string['task:reminder'] = 'Versturen van herinnering';
$string['messageprovider:reminder'] = 'Openwebinar herinnering melding';

// ACCESS.
$string['openwebinar:history'] = 'Bekijken van chat geschiedenis';
$string['openwebinar:view'] = 'Openwebinar bekijken';
$string['openwebinar:addinstance'] = 'Toevoegen van een openwebinar';
$string['openwebinar:manager'] = 'Openwebinar manager';
$string['openwebinar:teacher'] = 'Openwebinar leraar';

// ERRORS.
$string['error:openwebinar_notfound'] = 'Fout: de juiste openwebinar kan niet gevonden worden!';
$string['error:file_not_exits'] = 'Fout: dit bestand bestaat niet!';
$string['error:file_no_access'] = 'Fout: geen toegang tot dit bestand!';
$string['error:no_access'] = 'Fout: u beschikt niet over de juiste rechten.';
$string['error:no_result'] = 'Geen resultaten gevonden';
$string['error:answer_already_saved'] = 'Uw antwoord is al opgeslagen!';
$string['error:not_for_guests'] = 'Fout: gasten kunnen geen gebruik maken van deze functionaliteit';

// SETTINGS.
$string['setting:heading_server'] = 'Communicatie instellingen';
$string['setting:heading_instance_features'] = 'Functies ingeschakeld of uitgeschakeld (kan overschreven worden door
 iedere openwebinar onafhankelijk)';
$string['setting:streaming_server'] = 'Streaming locatie';
$string['setting:streaming_server_desc'] = 'De locatie van de streaming server';
$string['setting:chat_server'] = 'Chat/socket link';
$string['setting:chat_server_desc'] = 'De locatie van de chatserver';
$string['setting:shared_secret'] = 'Gedeeld Geheim';
$string['setting:shared_secret_desc'] = 'Een unieke sleutel die wordt gedeeld met de chat / streaming server';
$string['setting:heading_instance_defaults'] = 'Herinnering standaardwaarden (kan overschreven worden
 door iedere openwebinar onafhankelijk)';
$string['setting:reminder_1'] = 'Herinnering 1';
$string['setting:reminder_1_desc'] = 'Notificatie moment voor de start van de openwebinar.
<br> Instellen op 0 om dit noficatie moment uit te schakelen.';
$string['setting:reminder_2'] = 'Herinnering 2';
$string['setting:reminder_2_desc'] = 'Notificatie moment voor de start van de openwebinar.
<br> Instellen op 0 om dit noficatie moment uit te schakelen.';
$string['setting:reminder_3'] = 'Herinnering 3';
$string['setting:reminder_3_desc'] = 'Notificatie moment voor de start van de openwebinar.
<br> Instellen op 0 om dit noficatie moment uit te schakelen.';
$string['setting:stream'] = 'Video ingeschakeld';
$string['setting:stream_desc'] = 'Indien uitgeschakeld bevat de openwebinar geen video-speler';
$string['setting:chat'] = 'Chat ingeschakeld';
$string['setting:chat_desc'] = 'Indien uitgeschakeld bevat de openwebinar geen chatruimte';
$string['setting:filesharing'] = 'Bestandsdeling ingeschakeld';
$string['setting:filesharing_desc'] = 'Indien uitgeschakeld kan niemand bestanden delen.';
$string['setting:filesharing_student'] = 'Studenten kunnen bestanden delen';
$string['setting:filesharing_student_desc'] = 'Wanneer ingeschakeld kunnen studenten ook bestanden delen.';
$string['setting:showuserpicture'] = 'Toon profiel foto';
$string['setting:showuserpicture_desc'] = 'Toon profiel foto van gebruikers in de chatruimte';
$string['setting:userlist'] = 'Toon gebruikerslijst';
$string['setting:userlist_desc'] = 'Laat de actuele gebruikers in de chatruimte zien.';
$string['setting:ajax_timer'] = 'AJAX timer';
$string['setting:ajax_timer_desc'] = 'Dit zal elke 60 seconden een verzoek naar de server sturen om de online
tijd van de gebruiker zijn in de chatruimte realtime op te slaan.<br/>
Waarschuwing wanneer meer dan 25 gebruikers in de chatruimte aanwezig zijn kan dit voor een hogere serverload zorgen.';

$string['setting:emoticons'] = 'Emoticons in chatruimte';
$string['setting:emoticons_desc'] = 'Shortcode zal worden omgezet naar een emoticon.
 Ook een dialoog zal worden toegevoegd waar u een emoticon kunt selecteren.';
$string['setting:debugjs'] = 'Debug javascript';
$string['setting:debugjs_desc'] = 'Log javascript berichten naar de browser console.';
$string['setting:hls'] = 'HLS video stream';
$string['setting:hls_desc'] = 'Ondersteuning voor mobiele apparaten. Presentator moet ook streamen naar server met HLS-uitgang.
<br/>Opmerking: HLS zorgt voor een vertraging van 30 seconde in de stream!';

// Mod settings.
$string['mod_setting:settings'] = 'Openwebinar functies';
$string['mod_setting:timing'] = 'Openwebinar tijd';
$string['mod_setting:timeopen'] = 'Start';
$string['mod_setting:timeopenhelp'] = 'Start';
$string['mod_setting:timeopenhelp_help'] = 'Start';
$string['mod_setting:make_a_selection'] = 'Selecteer een gebruiker';
$string['mod_setting:broadcaster'] = 'Openwebinar presentator';
$string['mod_setting:reminders'] = 'Openwebinar herinnering bericht';
$string['mod_setting:duration'] = 'Duur';
$string['mod_setting:durationhelp'] = 'Duur';
$string['mod_setting:durationhelp_help'] = 'Duur';
$string['mod_setting:broadcastkey'] = 'Presentator sleutel';
$string['mod_setting:broadcastkey_desc'] = '<b>{$a->broadcastkey}</b>';

// Text.
$string['text:live_openwebinar'] = '<br/>De openwebinar is open vanaf: <b>{$a->timeopen}</b><br>
 U kunt nu de openwebinar openen via de knop hieronder.';

$string['text:broadcaster_help'] = '<h3>U bent de presentator van deze openwebinar</h3><br>Presentator sleutel:<br>
<b class="selectable">{$a->broadcastkey}</b><br><br>De handleiding kunt u downloaden
<a class="btn" target="_blank" href="http://moodlefreak.com/docs/openwebinar_broadcast_guide_2015_08_20.pdf">hier</a><br><br>
Zorg ervoor dat u de streaming-software installeerd:
<a href="https://obsproject.com/" target="_blank">Open Broadcaster Software</a>';


$string['text:history'] = '<h3>Openwebinar is offline</h3>Deze openwebinar werd gegeven op <b>{$a->timeopen}</b>. <br/><br/>U
 kunt nog steeds de chatgeschiedenis terugzien door te klikken op de onderstaande knop.';
$string['text:useractivity'] = 'Gebruikers activiteit';

// Buttons.
$string['btn:enter_live_openwebinar'] = 'Open de live openwebinar';
$string['btn:enter_offline_openwebinar'] = 'Open de offline openwebinar';
$string['btn:chattime'] = 'Rapport';
$string['btn:chatlog'] = 'Chatgeschiedenis';
$string['btn:view'] = 'Bekijken';
$string['btn:back'] = 'Terug';
$string['btn:addquestion'] = 'Voeg een nieuwe vraag toe';
$string['btn:open'] = 'Opslaan';
$string['btn:broadcast_enter'] = 'Enter openwebinar';

// Helper strings.
$string['dateformat'] = 'd-m-Y H:i';
$string['users'] = 'Gebruikers';
$string['browser'] = 'Browser';
$string['ip_address'] = 'IP-adres';
$string['starttime'] = 'Gestart op';
$string['online_time'] = 'Tijd aanwezig';
$string['time'] = 'Tijd';
$string['chat'] = 'Chat';
$string['no'] = 'Nee';
$string['yes'] = 'Ja';
$string['menu'] = 'Configuratiescherm';
$string['live'] = 'Live';
$string['addfile'] = 'Bestand toevoegen';
$string['attachment'] = 'Bijlagen';
$string['offline'] = 'Offline (beëindigd)';
$string['filemanager'] = 'Uploader';
$string['fileoverview'] = 'Bestanden';
$string['question_overview'] = 'Vraag';
$string['broadcaster'] = 'Presentator';
$string['student'] = 'Student';
$string['guest'] = 'Gast';
$string['teacher'] = 'Leraar';
$string['message_placeholder'] = 'Type een  bericht hier...';
$string['user_activity'] = 'Openwebinar gebruikers activiteit';
$string['starts_at'] = 'Openwebinar begint in:';
$string['starttime'] = 'Gestart op';
$string['Close'] = 'Close';

// User options.
$string['opt:header_broadcaster'] = 'Presentator';
$string['opt:header_exit'] = 'Beëindigd';
$string['opt:mute_guests'] = 'Dempen gasten';
$string['opt:chat_sound'] = 'Chat geluid';
$string['opt:stream'] = 'Toon stream';
$string['opt:userlist'] = 'Toon gebruikerslijst';
$string['opt:mute_students'] = 'Dempen studenten';
$string['opt:mute_teachers'] = 'Dempen leraren';
$string['opt:endopenwebinar'] = 'Sluit & beëindigd de openwebinar';
$string['opt:leave'] = 'Verlaat de openwebinar';
$string['opt:header_general'] = 'Algemeen';

// Js.
$string['js:my_answer_saved'] = 'Uw antwoord is opgeslagen.';
$string['js:send'] = 'Verstuur';
$string['js:answer'] = 'Antwoord';
$string['js:added_answer'] = '{$a->fullname} heeft een antwoord toegevoegd.';
$string['js:ending_openwebinar'] = 'Bent u zeker dat u de openwebinar wilt beëindigen?';
$string['js:muted'] = 'Uw berichten worden gedempt door de presentator.';
$string['js:wait_on_connection'] = 'Wachten..';
$string['js:joined'] = 'Welkom, u bent verbonden met chat-server.';
$string['js:disconnect'] = 'Verbinding is verbroken.';
$string['js:reconnected'] = 'Verbinding is hersteld.';
$string['js:script_user'] = 'Systeem bericht';
$string['js:system_user'] = 'Chat server';
$string['js:connecting'] = 'Verbinding maken met de chat-server, even geduld.';
$string['js:warning_message_closing_window'] = 'Weet u zeker dat u de openwebinar wilt verlaten?';
$string['js:error_logout_or_lostconnection'] = 'Verbinding verbroken! U kunt het beste nu uw venster vernieuwen.';
$string['js:dialog_ending_text'] = 'Presentator heeft de openwebinar beëindigd. Klik op onderstaande knop om terug te keren.';
$string['js:dialog_ending_btn'] = 'Sluit openwebinar';
$string['js:ended'] = 'Presentator heeft de openwebinar beëindigd.';
$string['js:chat_commands'] = '<h4>Fout: onbekend commando</h4>
<p>Onderstaande commandos zijn beschikbaar</p>
<b>Gebruikers:</b>
<ul class="command">
<li>/clear <span class="note">Wis alle berichten in uw overzicht</span></li>
</ul>';
$string['js:added_question'] = 'De vraag is uitgezet in de chatruimte. U ontvangt een bericht als iemand een antwoord geeft.';
$string['js:countdown_line1'] = ' milliseconde | seconde | minute | uur | dag | week | maand | jaar | decennium | eeuw |
 millennium';

$string['js:countdown_line2'] = ' milliseconden | seconden | minuten | uur | dagen | weken | maanden | jaar | decennia | eeuwen |
 millennia';
$string['js:countdown_line3'] = ' en ';

// HEADING Tables.
$string['heading:picture'] = 'Foto';
$string['heading:firstname'] = 'Voornaam';
$string['heading:lastname'] = 'Achternaam';
$string['heading:email'] = 'E-mail';
$string['heading:present'] = 'Aanwezig';
$string['heading:action'] = 'Actie';
$string['heading:chatlog'] = 'Chat geschiedenis: {$a->fullname}';
$string['heading:chattime'] = 'Rapportage: {$a->fullname}';
$string['heading:time'] = 'Tijd';
$string['heading:message'] = 'Bericht';
$string['heading:name'] = 'Naam';
$string['heading:value'] = 'Waarde';

// Email.
$string['mail:reminder_subject'] = 'Openwebinar herinnering: {$a->name}';
$string['mail:reminder_message'] = 'Beste ##fullname##, <br/><br/>

Openwebinar herinnering:<br/><br/>

<table cellpadding="0" cellspacing="0" border="0">
    <tr>
        <td style="width: 200px"><b>Activiteit:</b></td>
        <td>##name##</td>
    </tr>
    <tr>
        <td><b>Startdatum:</b></td>
        <td>##starttime##</td>
    </tr>
    <tr>
        <td><b>Geschatte duur:</b></td>
        <td>##duration## Minutes</td>
    </tr>
    <tr>
        <td><b>Link</b></td>
        <td><a href="##link##">Bekijk de openwebinar</a> </td>
    </tr>
</table>
<br/>
Met vriendelijke groet,<br/>

##broadcaster_fullname##';

$string['fullname'] = 'Volledige naam';
$string['skype'] = 'Skype';