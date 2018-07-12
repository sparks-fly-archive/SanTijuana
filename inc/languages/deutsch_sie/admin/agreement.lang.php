<?php
###################################
# Plugin Agreement for MyBB*#
# (c) 2018 by doylecc    #
# Website: http://mybbplugins.tk #
###################################

$l['ag_name'] = "Einverständnis Nutzungsbedingungen";
$l['ag_name_descr'] = "Erfordert die Zustimmung der Nutzer, speichert diese mit dem Datum ab und ermöglicht die Begrenzung der Speicherungsdauer von IP-Adressen.<br />";
$l['ag_group_title'] = "Einverständnis zu den Nutzungsbedingungen";
$l['ag_group_descr'] = "Einstellungen zum Akzeptieren Ihrer aktuellen Nutzungsbedingungen.";

$l['ag_force_title'] = "Zustimmung bereits registrierter Nutzer.";
$l['ag_force_descr'] = "Müssen bereits registrierte Nutzer den aktuellen Nutzungsbestimmungen erneut zustimmen, um das Forum weiter nutzen zu können?";
$l['ag_repeat_title'] = "Wiederholte Zustimmung erlauben";
$l['ag_repeat_descr'] = "Soll es den Benutzern ermöglicht werden, den Nutzungsbestimmungen zu jeder Zeit wiederholt zuzustimmen und somit selbständig den Zeitpunkt ihrer Zustimmung zu aktualisieren? (Standard: Nein)";
$l['ag_allowedpages_title'] = "Erlaubte Seiten ohne Zustimmung";
$l['ag_allowedpages_descr'] = "Bitte die Seiten eingeben, die Benutzer, welche den Nutzungsbestimmungen noch nicht zugestimmt haben, sehen dürfen. (im Format Dateiname + Parameter. Beispiel: misc.php?action=help&hid=3) Bitte für jede Seite eine neue Zeile verwenden!";
$l['ag_usercp_title'] = "Anzeige der Zustimmung im Benutzer-CP.";
$l['ag_usercp_descr'] = "Soll den Benutzern auf der Übersichtsseite des Benutzer-CP das Datum ihrer Zustimmung zu den Nutzungsbedingungen angezeigt werden?";
$l['ag_profile_title'] = "Anzeige der Zustimmung im Benutzer-Profil.";
$l['ag_profile_descr'] = "Soll den Benutzern und Administratoren auf der jeweiligen Benutzer-Profil-Seite das Datum der Zustimmung zu den Nutzungsbedingungen angezeigt werden?";
$l['ag_lang_title'] = "Verwendung von eigener Sprachdatei";
$l['ag_lang_descr'] = "Wird eine eigene, selbst erstellte Sprachdatei auf der Seite mit den Nutzungsbedingungen verwendet?";
$l['ag_langtitle_title'] = "Name der Sprachdatei";
$l['ag_langtitle_descr'] = "Bitte den Namen der Sprachdatei ohne Endungen eintragen! (Beispiel: für meinesprachdatei.lang.php ist meinesprachdatei einzutragen.)";
$l['ag_ipaddresses_title'] = "Speicherungsdauer der IP-Adressen";
$l['ag_ipaddresses_descr'] = "Wie lange sollen die IP-Adressen in Beiträgen, Privaten Nachrichten, Abstimmungen und Logs gespeichert bleiben?";
$l['ag_ipaddresses_users_title'] = "Regelmäßige Löschung von Registrierungs-IP und letzter IP der Benutzer";
$l['ag_ipaddresses_users_descr'] = "Sollen die Registrierungs-IP und letzte IP von Benutzern entsprechend der eingestellten Höchstspeicherungsdauer gelöscht werden?";
$l['ag_ipaddresses_ratings_title'] = "Regelmäßige Löschung der IP-Adressen von Themenbewertungen";
$l['ag_ipaddresses_ratings_descr'] = "Sollen die IP-Adressen von Themenbewertungen regelmäßig komplett gelöscht werden? (Eine zeitabhängige Löschung ist hier nicht möglich)";
$l['ag_ip_del_user_title'] = "Entfernung der IP-Adressen bei Benutzerlöschung.";
$l['ag_ip_del_user_descr'] = "Sollen die für Beiträge, Private Nachrichten, Abstimmungen und in Logs gespeicherten IP-Adressen eines Benutzers entfernt werden, wenn der Benutzer gelöscht wird?";
$l['ag_guest_post_title'] = "Zustimmung der Nutzungsbedingungen für Gastbeiträge.";
$l['ag_guest_post_descr'] = "Sollen Gäste den Nutzungsbedingungen zustimmen müssen, damit ihre Beiträge gespeichert werden?";
$l['ag_tou_title'] = "Nutzungsbedingungen im Admin-CP verwalten.";
$l['ag_tou_descr'] = "Sollen die Nutzungsbedingungen im <a href=\"index.php?module=config-termsofuse\">Admin-CP</a> erstellt/bearbeitet werden und diese dann die Nutzungsbedingungen aus den Sprachdateien auf der Seite ersetzen?";

$l['ag_never'] = "Niemals";
$l['ag_day'] = "1 Tag";
$l['ag_threedays'] = "3 Tage";
$l['ag_week'] = "1 Woche";
$l['ag_twoweeks'] = "2 Wochen";
$l['ag_month'] = "4 Wochen";
$l['ag_tenweeks'] = "10 Wochen";
$l['ag_halfyear'] = "6 Monate";
$l['ag_year'] = "1 Jahr";
$l['ag_forever'] = "Unbegrenzt";

$l['ag_reset_confirm'] = "Sollen alle Einverständniserklärungen der Benutzer jetzt zurückgesetzt werden?";
$l['ag_reset_success'] = "Einverständniserklärungen aller Benutzer erfolgreich zurückgesetzt!";
$l['ag_reset'] = "Einverständniserklärungen aller Benutzer zurücksetzen!";

$l['ag_task_name'] = "IP Adressen bereinigen!";
$l['ag_task_description'] = "Löscht die IP Adressen von Beiträgen, Privaten Nachrichten, Abstimmungen und Logs, wenn diese die in den Einstellungen festgelegte maximale Speicherungszeit erreicht haben.";
$l['ag_task_run'] = "IP Adressen erfolgreich bereinigt!";

$l['ag_can_edit'] = "Kann Nutzungsbedingungen bearbeiten.";
$l['ag_tou_menu'] = "Nutzungsbedingungen";

$l['ag_tou_manage'] = "Nutzungsbedingungen verwalten";
$l['ag_tou_manage_desc'] = "Die im Forum angezeigten Nutzungsbedingungen bearbeiten";
$l['ag_tou_add'] = "Nutzungsbedingungen hinzufügen";
$l['ag_tou_add_desc'] = "Nutzungsbedingungen für weitere Sprachen hinzufügen";
$l['ag_tou_manage_title'] = "Titel";
$l['ag_tou_manage_title_desc'] = "Bitte den Namen der Nutzungsbedingungen eingeben, der in der Titelleiste angezeigt werden soll";
$l['ag_tou_language'] = "Sprache";
$l['ag_tou_language_desc'] = "Bitte den Namen des neuen Sprachpakets eingeben (z.B. deutsch_du)";
$l['ag_tou_terms'] = "Text der Nutzungsbedingungen";
$l['ag_tou_terms_desc'] = "Bitte die Nutzungsbedingungen eingeben, die im Forum für diese Sprache angezeigt werden sollen. (Kein HTML!)";
$l['ag_tou_options'] = "Optionen";
$l['ag_tou_edit'] = "Bedingungen bearbeiten";
$l['ag_tou_edit_desc'] = "Hier können die Nutzungsbedingungen bearbeitet werden";
$l['ag_tou_delete'] = "Bedingungen löschen";
$l['ag_tou_date'] = "Erstellungsdatum";
$l['ag_tou_date_desc'] = "Bitte das Datum (in der Form DD.MM.JJJJ) eingeben, ab dem diese Bedingungen gültig sind.";
$l['ag_tou_datenow'] = "Heutiges Datum";
$l['ag_tou_datenow_desc'] = "Soll das heutige Datum als Erstellungsdatum gespeichert werden? Damit wird das oben eingegebene Datum nicht berücksichtigt.";
$l['ag_tou_datenow_save'] = "Heutiges Datum verwenden";

$l['ag_tou_add_submit'] = "Hinzufügen";
$l['ag_tou_edit_submit'] = "Speichern";
$l['ag_tou_add_success'] = "Neue Nutzungsbedingungen hinzugefügt";
$l['ag_tou_empty_field'] = "Bitte folgendes Feld ausfüllen: ";
$l['ag_tou_lang_installed'] = "Dieses Sprachpaket ist nicht installiert: ";
$l['ag_tou_lang_exists'] = "Für dieses Sprachpaket sind bereits Nutzungsbedingungen gespeichert: ";
$l['ag_tou_edit_success'] = "Nutzungsbedingungen erfolgreich bearbeitet";
$l['ag_tou_delete_invalid'] = "Gewählte Nutzungsbedingungen nicht gefunden!";
$l['ag_tou_delete_success'] = "Gewählte Nutzungsbedingungen erfolgreich gelöscht!";
$l['ag_tou_delete_confirm'] = "Gewählte Nutzungsbedingungen wirklich löschen?";
$l['ag_tou_deactivated'] = "Die Anzeige dieser Nutzungsbedingungen ist zur Zeit in den <a href=\"index.php?module=config-settings&amp;action=change&amp;search=UserAgreement\" target=\"_blank\" rel=\"noopener\">Einstellungen</a> deaktiviert!";
