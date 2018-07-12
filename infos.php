<?php

/*
##############################################
ÜBERSICHTSSEITE FÜR REGELN
Autor: https://github.com/its-sparks-fly 
##############################################
ANLEITUNG ZUM HINZUFÜGEN WEITERER INFOS:
Array $infos (STRG + F > $infos; erstes Ergebnis) für jede neue Seite um einen aussagekräftigen Punkt erweitern.
Die neue Informationsseite ist nun über infos.php?action=neuer_listenpunkt erreichbar.
Neues Template listen_neuer_listenpunkt anlegen (siehe Schema vorhandener Template)
nicht vergessen: Template infos_nav um die neu hinzugefügte Informationsseite erweitern
##############################################
TODO: Templates bauen. Entsprechend:
https://media.discordapp.net/attachments/461123557058150401/466176897404108800/20180710_114005.jpg?width=301&height=402
*/

define("IN_MYBB", 1);
define('THIS_SCRIPT', 'infos.php');

require_once "./global.php";

add_breadcrumb("Hintergrundinformationen", "infos.php");

// Liste verfübbarer Hintergrundinformationen
$infos = array("bildung", "politik");

// Navigation
eval('$infos_nav = "'.$templates->get('infos_nav').'";');

// Hauptseite Hintergrundinformationen
if(!$mybb->input['action']) {
  eval("\$page = \"".$templates->get("infos")."\";");
  output_page($page);
}

// Seiten werden erstellt
foreach($infos as $info) {
    if($mybb->input['action'] == $info) {
        eval("\$page = \"".$templates->get("infos_{$info}")."\";");
        output_page($page);
    }
}