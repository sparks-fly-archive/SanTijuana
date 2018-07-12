<?php

/*
##############################################
ÜBERSICHTSSEITE FÜR REGELN
Autor: https://github.com/its-sparks-fly 
##############################################
ANLEITUNG ZUM HINZUFÜGEN WEITERER REGELN:
Array $regeln (STRG + F > $regeln; erstes Ergebnis) für jede neue Seite um einen aussagekräftigen Punkt erweitern.
Die neue Informationsseite ist nun über regeln.php?action=neue_regeln erreichbar.
Neues Template listen_neue_regeln anlegen (siehe Schema vorhandener Template)
nicht vergessen: Template regeln_nav um die neu hinzugefügte Informationsseite erweitern
##############################################
TODO: Templates bauen. Entsprechend:
https://media.discordapp.net/attachments/461123557058150401/466176897404108800/20180710_114005.jpg?width=301&height=402
*/

define("IN_MYBB", 1);
define('THIS_SCRIPT', 'regeln.php');

require_once "./global.php";

add_breadcrumb("Rollenspielregeln", "regeln.php");

// Liste verfübbarer Hintergrundinformationen
$regeln = array("registrierung", "rating");

// Navigation
eval('$regeln_nav = "'.$templates->get('regeln_nav').'";');

// Hauptseite Hintergrundinformationen
if(!$mybb->input['action']) {
  eval("\$page = \"".$templates->get("regeln")."\";");
  output_page($page);
}

// Seiten werden erstellt
foreach($regeln as $regel) {
    if($mybb->input['action'] == $regel) {
        eval("\$page = \"".$templates->get("regeln_{$regel}")."\";");
        output_page($page);
    }
}