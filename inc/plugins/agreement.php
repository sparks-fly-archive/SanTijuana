<?php
/**
 * agreement.php
 *
 * User Agreement plugin for MyBB 1.8
 * Copyright (c) 2018 doylecc. All rights reserved.
 * Website: http://mybbplugins.tk
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
 * IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM,
 * DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE,
 * ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 *
 */

// Disallow direct access to this file for security reasons
if (!defined("IN_MYBB")) {
    die("Direct initialization of this file is not allowed.<br /><br />Please make sure IN_MYBB is defined.");
}

// Add the front end hooks


// Plugin info
function agreement_info()
{
    global $mybb, $lang;

    $lang->load('agreement');

    $agreement_info = array(
        "name"          => $lang->ag_name,
        "description"   => $lang->ag_name_descr,
        "website"       => "http://mybbplugins.tk",
        "author"        => "doylecc",
        "authorsite"    => "http://mybbplugins.tk",
        "version"       => "1.5",
        "codename"      => "dcc_agreement",
        "compatibility" => "18*"
    );

    $agreement_info['description'] .= '<ul><li style="list-style: none;"><img src="styles/default/images/icons/';
    $agreement_info['description'] .= 'run_task.png" alt="" style="margin-left: 10px;" /><a href="index.php?module=';
    $agreement_info['description'] .= 'tools-agreement&amp;action=reset&amp;my_post_key=';
    $agreement_info['description'] .= $mybb->post_code.'" style="margin: 10px;">';
    $agreement_info['description'] .= $lang->ag_reset.'</a></li></ul>';

    return $agreement_info;
}

################################################################################

// Install function
function agreement_install()
{
    global $db, $lang;

    // Load language file
    $lang->load('agreement');

    // Avoid database errors due to duplicates
    if ($db->field_exists("new_terms", "users")) {
        $db->write_query("ALTER TABLE `".TABLE_PREFIX."users` DROP COLUMN `new_terms`");
    }
    if ($db->field_exists("new_terms_date", "users")) {
        $db->write_query("ALTER TABLE `".TABLE_PREFIX."users` DROP COLUMN `new_terms_date`");
    }
    // Add database columns
    switch ($db->type) {
        case "pgsql":
            $db->write_query(
                "ALTER TABLE `".TABLE_PREFIX."users` ADD COLUMN `new_terms` smallint NOT NULL DEFAULT '0'"
            );
            $db->write_query(
                "ALTER TABLE `".TABLE_PREFIX."users` ADD COLUMN `new_terms_date` int NOT NULL default '0'"
            );
            break;
        default:
            $db->write_query(
                "ALTER TABLE `".TABLE_PREFIX."users` ADD COLUMN `new_terms` tinyint(1) NOT NULL DEFAULT '0'"
            );
            $db->write_query(
                "ALTER TABLE `".TABLE_PREFIX."users` ADD COLUMN `new_terms_date` int unsigned NOT NULL default '0'"
            );
            break;
    }

    // Add a new database table for terms of use
    if ($db->table_exists('termsofuse')) {
        $db->drop_table('termsofuse');
    }
    $collation = $db->build_create_table_collation();

    // Create table
    $db->write_query("
        CREATE TABLE ".TABLE_PREFIX."termsofuse (
            `touid` int(10) unsigned NOT NULL auto_increment,
            `name` varchar(100) NOT NULL DEFAULT '',
            `title` varchar(255) NOT NULL DEFAULT '',
            `language` varchar(100) NOT NULL DEFAULT '',
            `terms` TEXT NOT NULL,
            `dateline` int unsigned NOT NULL DEFAULT '0',
            PRIMARY KEY (touid)
        ) ENGINE=MyISAM{$collation};
    ");

    // Insert values into table
    $tou_1 = array(
        "name" => "tou_english",
        "title" => "Agreement - Terms Of Use",
        "language" => "english",
        "terms" => $db->escape_string("Whilst we attempt to edit or remove any messages containing inappropriate, sexually orientated, abusive, hateful, slanderous, or threatening material that could be considered invasive of a person's privacy, or which otherwise violate any kind of law, it is impossible for us to review every message posted on this discussion system. For this reason you acknowledge that all messages posted on this discussion system express the views and opinions of the original message author and not necessarily the views of this bulletin board. Therefore we take no responsibility and cannot be held liable for any messages posted. We do not vouch for or warrant the accuracy and completeness of every message.

By registering on this discussion system you agree that you will not post any material which is knowingly false, inaccurate, abusive, hateful, harassing, sexually orientated, threatening or invasive of a person's privacy, or any other material which may violate any applicable laws.

Failure to comply with these rules may result in the termination of your account, account suspension, or permanent ban of access to these forums. Your IP Address is recorded with each post you make on this discussion system and is retrievable by the forum staff if need-be. You agree that we have the ability and right to remove, edit, or lock any account or message at any time should it be seen fit. You also agree that any information you enter on this discussion system is stored in a database, and that \"cookies\" are stored on your computer to save your login information.

Any information you provide on these forums will not be disclosed to any third party without your complete consent, although the staff cannot be held liable for any hacking attempt in which your data is compromised.

[b]By continuing with the sign up process you agree to the above rules and any others that the Administrator specifies.[/b]"),
        "dateline" => TIME_NOW
        );
    $db->insert_query("termsofuse", $tou_1);

    $tou_2 = array(
        "name" => "tou_deutsch_du",
        "title" => "Einverständniserklärung Nutzungsbedingungen",
        "language" => "deutsch_du",
        "terms" => $db->escape_string("Die Administratoren und Moderatoren dieses Forums bemühen sich, Beiträge mit fragwürdigem Inhalt so schnell wie möglich zu bearbeiten oder ganz zu löschen; aber es ist nicht möglich, jede einzelne Nachricht zu überprüfen. Du bestätigst mit dem Absenden dieser Einverständniserklärung, dass du akzeptierst, dass jeder Beitrag in diesem Forum die Meinung des Urhebers wiedergibt und dass die Administratoren, Moderatoren und Betreiber dieses Forums nur für ihre eigenen Beiträge verantwortlich sind.

Du verpflichtest dich, keine beleidigenden, obszönen, vulgären, verleumdenden, gewaltverherrlichenden oder aus anderen Gründen strafbaren Inhalte in diesem Forum zu veröffentlichen. Verstöße gegen diese Regel führen zu sofortiger und permanenter Sperrung. Wir behalten uns vor, Verbindungsdaten u. Ä. an die strafverfolgenden Behörden weiterzugeben. Du räumst den Betreibern, Administratoren und Moderatoren dieses Forums das Recht ein, Beiträge nach eigenem Ermessen zu entfernen, zu bearbeiten, zu verschieben oder zu sperren. Du stimmst zu, dass die im Rahmen der Registrierung erhobenen Daten in einer Datenbank gespeichert werden.

Dieses System verwendet Cookies, um Informationen auf deinem Computer zu speichern. Diese Cookies enthalten keine der oben angegebenen Informationen, sondern dienen ausschließlich deinem Komfort. Deine E-Mail-Adresse wird nur zur Bestätigung der Registrierung und ggf. zum Versand eines neuen Passwortes verwendet.

Durch das Abschließen der Registrierung stimmst du diesen Nutzungsbedingungen zu.

[b]Wenn du auf 'Ich stimme zu' klickst, erklärst du dich mit den Regeln einverstanden.[/b]"),
        "dateline" => TIME_NOW
        );
    $db->insert_query("termsofuse", $tou_2);

    $tou_3 = array(
        "name" => "tou_deutsch_sie",
        "title" => "Einverständniserklärung Nutzungsbedingungen",
        "language" => "deutsch_sie",
        "terms" => $db->escape_string("Die Administratoren und Moderatoren dieses Forums bemühen sich, Beiträge mit fragwürdigem Inhalt so schnell wie möglich zu bearbeiten oder ganz zu löschen; aber es ist nicht möglich, jede einzelne Nachricht zu überprüfen. Sie bestätigen mit dem Absenden dieser Einverständniserklärung, dass Sie akzeptieren, dass jeder Beitrag in diesem Forum die Meinung des Urhebers wiedergibt und dass die Administratoren, Moderatoren und Betreiber dieses Forums nur für ihre eigenen Beiträge verantwortlich sind.

Sie verpflichten sich, keine beleidigenden, obszönen, vulgären, verleumdenden, gewaltverherrlichenden oder aus anderen Gründen strafbaren Inhalte in diesem Forum zu veröffentlichen. Verstöße gegen diese Regel führen zu sofortiger und permanenter Sperrung. Wir behalten uns vor, Verbindungsdaten u. Ä. an die strafverfolgenden Behörden weiterzugeben. Sie räumen den Betreibern, Administratoren und Moderatoren dieses Forums das Recht ein, Beiträge nach eigenem Ermessen zu entfernen, zu bearbeiten, zu verschieben oder zu sperren. Sie stimmen zu, dass die im Rahmen der Registrierung erhobenen Daten in einer Datenbank gespeichert werden.

Dieses System verwendet Cookies, um Informationen auf Ihrem Computer zu speichern. Diese Cookies enthalten keine der oben angegebenen Informationen, sondern dienen ausschließlich Ihrem Komfort. Ihre E-Mail-Adresse wird nur zur Bestätigung der Registrierung und ggf. zum Versand eines neuen Passwortes verwendet.

Durch das Abschließen der Registrierung stimmen Sie diesen Nutzungsbedingungen zu.

[b]Wenn Sie auf 'Ich stimme zu' klicken, erklären Sie sich mit den Regeln einverstanden.[/b]"),
        "dateline" => TIME_NOW
        );
    $db->insert_query("termsofuse", $tou_3);

    // Delete former settings to avoid duplicates
    $query = $db->simple_select('settinggroups', 'gid', 'name="UserAgreement"');
    $ag = $db->fetch_array($query);
    $db->delete_query('settinggroups', "gid='".$ag['gid']."'");
    $db->delete_query('settings', "gid='".$ag['gid']."'");

    // Add settings
    $query_rows = $db->simple_select("settinggroups", "COUNT(*) as agrows");
    $rows = $db->fetch_field($query_rows, "agrows");

    // Add Settinggroup
    $agreement_group = array(
        "name" => "UserAgreement",
        "title" => $db->escape_string($lang->ag_group_title),
        "description" => $db->escape_string($lang->ag_group_descr),
        "disporder" => $rows+1,
        "isdefault" => 0
    );
    $db->insert_query("settinggroups", $agreement_group);
    $gid = $db->insert_id();

    // Add settings for settinggroup
    $agreement_1 = array(
        "name" => "ag_force",
        "title" => $db->escape_string($lang->ag_force_title),
        "description" => $db->escape_string($lang->ag_force_descr),
        "optionscode" => "yesno",
        "value" => 1,
        "disporder" => 1,
        "gid" => (int)$gid
        );
    $db->insert_query("settings", $agreement_1);

    $agreement_2 = array(
        "name" => "ag_repeat",
        "title" => $db->escape_string($lang->ag_repeat_title),
        "description" => $db->escape_string($lang->ag_repeat_descr),
        "optionscode" => "yesno",
        "value" => 0,
        "disporder" => 2,
        "gid" => (int)$gid
        );
    $db->insert_query("settings", $agreement_2);

    $agreement_3 = array(
        "name" => "ag_allowedpages",
        "title" => $db->escape_string($lang->ag_allowedpages_title),
        "description" => $db->escape_string($lang->ag_allowedpages_descr),
        "optionscode" => "textarea",
        "value" => '',
        "disporder" => 3,
        "gid" => (int)$gid
        );
    $db->insert_query("settings", $agreement_3);

    $agreement_4 = array(
        "name" => "ag_usercp",
        "title" => $db->escape_string($lang->ag_usercp_title),
        "description" => $db->escape_string($lang->ag_usercp_descr),
        "optionscode" => "yesno",
        "value" => 1,
        "disporder" => 4,
        "gid" => (int)$gid
        );
    $db->insert_query("settings", $agreement_4);

    $agreement_5 = array(
        "name" => "ag_profile",
        "title" => $db->escape_string($lang->ag_profile_title),
        "description" => $db->escape_string($lang->ag_profile_descr),
        "optionscode" => "yesno",
        "value" => 1,
        "disporder" => 5,
        "gid" => (int)$gid
        );
    $db->insert_query("settings", $agreement_5);

    $agreement_6 = array(
        "name" => "ag_lang",
        "title" => $db->escape_string($lang->ag_lang_title),
        "description" => $db->escape_string($lang->ag_lang_descr),
        "optionscode" => "yesno",
        "value" => 0,
        "disporder" => 6,
        "gid" => (int)$gid
        );
    $db->insert_query("settings", $agreement_6);

    $agreement_7 = array(
        "name" => "ag_langtitle",
        "title" => $db->escape_string($lang->ag_langtitle_title),
        "description" => $db->escape_string($lang->ag_langtitle_descr),
        "optionscode" => "text",
        "value" => '',
        "disporder" => 7,
        "gid" => (int)$gid
        );
    $db->insert_query("settings", $agreement_7);

    $agreement_8 = array(
        "name" => "ag_ipaddresses",
        "title" => $db->escape_string($lang->ag_ipaddresses_title),
        "description" => $db->escape_string($lang->ag_ipaddresses_descr),
        "optionscode" => "select\n
never=".$db->escape_string($lang->ag_never)."\n
day=".$db->escape_string($lang->ag_day)."\n
threedays=".$db->escape_string($lang->ag_threedays)."\n
week=".$db->escape_string($lang->ag_week)."\n
twoweeks=".$db->escape_string($lang->ag_twoweeks)."\n
month=".$db->escape_string($lang->ag_month)."\n
tenweeks=".$db->escape_string($lang->ag_tenweeks)."\n
halfyear=".$db->escape_string($lang->ag_halfyear)."\n
year=".$db->escape_string($lang->ag_year)."\n
forever=".$db->escape_string($lang->ag_forever)."\n",
        "value" => "forever",
        "disporder" => 8,
        "gid" => (int)$gid
        );
    $db->insert_query("settings", $agreement_8);

    $agreement_9 = array(
        "name" => "ag_ipaddresses_users",
        "title" => $db->escape_string($lang->ag_ipaddresses_users_title),
        "description" => $db->escape_string($lang->ag_ipaddresses_users_descr),
        "optionscode" => "yesno",
        "value" => 0,
        "disporder" => 9,
        "gid" => (int)$gid
        );
    $db->insert_query("settings", $agreement_9);

    $agreement_10 = array(
        "name" => "ag_ipaddresses_ratings",
        "title" => $db->escape_string($lang->ag_ipaddresses_ratings_title),
        "description" => $db->escape_string($lang->ag_ipaddresses_ratings_descr),
        "optionscode" => "yesno",
        "value" => 0,
        "disporder" => 10,
        "gid" => (int)$gid
        );
    $db->insert_query("settings", $agreement_10);

    $agreement_11 = array(
        "name" => "ag_ip_del_user",
        "title" => $db->escape_string($lang->ag_ip_del_user_title),
        "description" => $db->escape_string($lang->ag_ip_del_user_descr),
        "optionscode" => "yesno",
        "value" => 1,
        "disporder" => 11,
        "gid" => (int)$gid
        );
    $db->insert_query("settings", $agreement_11);

    $agreement_12 = array(
        "name" => "ag_guest_post",
        "title" => $db->escape_string($lang->ag_guest_post_title),
        "description" => $db->escape_string($lang->ag_guest_post_descr),
        "optionscode" => "yesno",
        "value" => 1,
        "disporder" => 12,
        "gid" => (int)$gid
        );
    $db->insert_query("settings", $agreement_12);

    $agreement_13 = array(
        "name" => "ag_tou",
        "title" => $db->escape_string($lang->ag_tou_title),
        "description" => $db->escape_string($lang->ag_tou_descr),
        "optionscode" => "yesno",
        "value" => 0,
        "disporder" => 13,
        "gid" => (int)$gid
        );
    $db->insert_query("settings", $agreement_13);

    // Refresh settings.php
    rebuild_settings();

    // Add templates
    agreement_templates_add();
}

// Check if plugin is installed
function agreement_is_installed()
{
    global $db;

    if ($db->field_exists("new_terms", "users") && $db->field_exists("new_terms_date", "users")) {
        return true;
    } else {
        return false;
    }
}

// The uninstall function
function agreement_uninstall()
{
    global $db;

    // Delete database columns
    if ($db->field_exists("new_terms", "users")) {
        $db->write_query("ALTER TABLE `".TABLE_PREFIX."users` DROP COLUMN `new_terms`");
    }
    if ($db->field_exists("new_terms_date", "users")) {
        $db->write_query("ALTER TABLE `".TABLE_PREFIX."users` DROP COLUMN `new_terms_date`");
    }

    // Delete database table for terms of use
    if ($db->table_exists('termsofuse')) {
        $db->drop_table('termsofuse');
    }

    // Delete the settings
    $query = $db->simple_select('settinggroups', 'gid', 'name="UserAgreement"');
    $ag = $db->fetch_array($query);
    $db->delete_query('settinggroups', "gid='".$ag['gid']."'");
    $db->delete_query('settings', "gid='".$ag['gid']."'");
    // Refresh settings.php
    rebuild_settings();

    // Remove the task entry
    $db->delete_query('tasks', "file='agreement_ip'");

    // Delete master templates
    $db->delete_query('templates', "title LIKE 'member_termsofuse_%' AND sid='-2'");
}

// Activate the plugin
function agreement_activate()
{
    global $db, $lang;

    change_admin_permission('config', 'termsofuse', 1);

    // Add templates
    $db->delete_query('templates', "title LIKE 'member_termsofuse_%' AND sid='-2'");
    agreement_templates_add();

    // Template edits
    require_once MYBB_ROOT.'/inc/adminfunctions_templates.php';
    // Remove first to avoid duplicates
    find_replace_templatesets(
        "member_profile",
        "#".preg_quote('{$newterms}')."#i",
        '',
        0
    );
    find_replace_templatesets(
        "usercp",
        "#".preg_quote('{$newterms}')."#i",
        '',
        0
    );
    find_replace_templatesets(
        "newreply",
        "#".preg_quote('{$guestterms}')."#i",
        '',
        0
    );
    find_replace_templatesets(
        "newthread",
        "#".preg_quote('{$guestterms}')."#i",
        '',
        0
    );
    find_replace_templatesets(
        "showthread_quickreply",
        "#".preg_quote('{$guestterms}')."#i",
        '',
        0
    );

    // Now insert the variables
    find_replace_templatesets(
        "member_profile",
        "#".preg_quote('{$memregdate}')."#i",
        ' {$memregdate}{$newterms}'
    );
    find_replace_templatesets(
        "usercp",
        "#".preg_quote('{$regdate}')."#i",
        '{$regdate}{$newterms}'
    );
    find_replace_templatesets(
        "newreply",
        "#".preg_quote('{$attachbox}')."#i",
        '{$attachbox}{$guestterms}'
    );
    find_replace_templatesets(
        "newthread",
        "#".preg_quote('{$attachbox}')."#i",
        '{$attachbox}{$guestterms}'
    );
    find_replace_templatesets(
        "showthread_quickreply",
        "#".preg_quote('</form>')."#i",
        '{$guestterms}</form>'
    );

    // Add a new database table for terms of use
    if (!$db->table_exists('termsofuse')) {
        $collation = $db->build_create_table_collation();

        // Create table
        $db->write_query("
            CREATE TABLE ".TABLE_PREFIX."termsofuse (
                `touid` int(10) unsigned NOT NULL auto_increment,
                `name` varchar(100) NOT NULL DEFAULT '',
                `title` varchar(255) NOT NULL DEFAULT '',
                `language` varchar(100) NOT NULL DEFAULT '',
                `terms` TEXT NOT NULL,
                `dateline` int unsigned NOT NULL DEFAULT '0',
                PRIMARY KEY (touid)
            ) ENGINE=MyISAM{$collation};
        ");

        // Insert values into table
        $tou_1 = array(
            "name" => "tou_english",
            "title" => "Agreement - Terms Of Use",
            "language" => "english",
            "terms" => $db->escape_string("Whilst we attempt to edit or remove any messages containing inappropriate, sexually orientated, abusive, hateful, slanderous, or threatening material that could be considered invasive of a person's privacy, or which otherwise violate any kind of law, it is impossible for us to review every message posted on this discussion system. For this reason you acknowledge that all messages posted on this discussion system express the views and opinions of the original message author and not necessarily the views of this bulletin board. Therefore we take no responsibility and cannot be held liable for any messages posted. We do not vouch for or warrant the accuracy and completeness of every message.

By registering on this discussion system you agree that you will not post any material which is knowingly false, inaccurate, abusive, hateful, harassing, sexually orientated, threatening or invasive of a person's privacy, or any other material which may violate any applicable laws.

Failure to comply with these rules may result in the termination of your account, account suspension, or permanent ban of access to these forums. Your IP Address is recorded with each post you make on this discussion system and is retrievable by the forum staff if need-be. You agree that we have the ability and right to remove, edit, or lock any account or message at any time should it be seen fit. You also agree that any information you enter on this discussion system is stored in a database, and that \"cookies\" are stored on your computer to save your login information.

Any information you provide on these forums will not be disclosed to any third party without your complete consent, although the staff cannot be held liable for any hacking attempt in which your data is compromised.

[b]By continuing with the sign up process you agree to the above rules and any others that the Administrator specifies.[/b]"),
            "dateline" => TIME_NOW
            );
        $db->insert_query("termsofuse", $tou_1);

        $tou_2 = array(
            "name" => "tou_deutsch_du",
            "title" => "Einverständniserklärung Nutzungsbedingungen",
            "language" => "deutsch_du",
            "terms" => $db->escape_string("Die Administratoren und Moderatoren dieses Forums bemühen sich, Beiträge mit fragwürdigem Inhalt so schnell wie möglich zu bearbeiten oder ganz zu löschen; aber es ist nicht möglich, jede einzelne Nachricht zu überprüfen. Du bestätigst mit dem Absenden dieser Einverständniserklärung, dass du akzeptierst, dass jeder Beitrag in diesem Forum die Meinung des Urhebers wiedergibt und dass die Administratoren, Moderatoren und Betreiber dieses Forums nur für ihre eigenen Beiträge verantwortlich sind.

Du verpflichtest dich, keine beleidigenden, obszönen, vulgären, verleumdenden, gewaltverherrlichenden oder aus anderen Gründen strafbaren Inhalte in diesem Forum zu veröffentlichen. Verstöße gegen diese Regel führen zu sofortiger und permanenter Sperrung. Wir behalten uns vor, Verbindungsdaten u. Ä. an die strafverfolgenden Behörden weiterzugeben. Du räumst den Betreibern, Administratoren und Moderatoren dieses Forums das Recht ein, Beiträge nach eigenem Ermessen zu entfernen, zu bearbeiten, zu verschieben oder zu sperren. Du stimmst zu, dass die im Rahmen der Registrierung erhobenen Daten in einer Datenbank gespeichert werden.

Dieses System verwendet Cookies, um Informationen auf deinem Computer zu speichern. Diese Cookies enthalten keine der oben angegebenen Informationen, sondern dienen ausschließlich deinem Komfort. Deine E-Mail-Adresse wird nur zur Bestätigung der Registrierung und ggf. zum Versand eines neuen Passwortes verwendet.

Durch das Abschließen der Registrierung stimmst du diesen Nutzungsbedingungen zu.

[b]Wenn du auf 'Ich stimme zu' klickst, erklärst du dich mit den Regeln einverstanden.[/b]"),
            "dateline" => TIME_NOW
            );
        $db->insert_query("termsofuse", $tou_2);

        $tou_3 = array(
            "name" => "tou_deutsch_sie",
            "title" => "Einverständniserklärung Nutzungsbedingungen",
            "language" => "deutsch_sie",
            "terms" => $db->escape_string("Die Administratoren und Moderatoren dieses Forums bemühen sich, Beiträge mit fragwürdigem Inhalt so schnell wie möglich zu bearbeiten oder ganz zu löschen; aber es ist nicht möglich, jede einzelne Nachricht zu überprüfen. Sie bestätigen mit dem Absenden dieser Einverständniserklärung, dass Sie akzeptieren, dass jeder Beitrag in diesem Forum die Meinung des Urhebers wiedergibt und dass die Administratoren, Moderatoren und Betreiber dieses Forums nur für ihre eigenen Beiträge verantwortlich sind.

Sie verpflichten sich, keine beleidigenden, obszönen, vulgären, verleumdenden, gewaltverherrlichenden oder aus anderen Gründen strafbaren Inhalte in diesem Forum zu veröffentlichen. Verstöße gegen diese Regel führen zu sofortiger und permanenter Sperrung. Wir behalten uns vor, Verbindungsdaten u. Ä. an die strafverfolgenden Behörden weiterzugeben. Sie räumen den Betreibern, Administratoren und Moderatoren dieses Forums das Recht ein, Beiträge nach eigenem Ermessen zu entfernen, zu bearbeiten, zu verschieben oder zu sperren. Sie stimmen zu, dass die im Rahmen der Registrierung erhobenen Daten in einer Datenbank gespeichert werden.

Dieses System verwendet Cookies, um Informationen auf Ihrem Computer zu speichern. Diese Cookies enthalten keine der oben angegebenen Informationen, sondern dienen ausschließlich Ihrem Komfort. Ihre E-Mail-Adresse wird nur zur Bestätigung der Registrierung und ggf. zum Versand eines neuen Passwortes verwendet.

Durch das Abschließen der Registrierung stimmen Sie diesen Nutzungsbedingungen zu.

[b]Wenn Sie auf 'Ich stimme zu' klicken, erklären Sie sich mit den Regeln einverstanden.[/b]"),
            "dateline" => TIME_NOW
            );
        $db->insert_query("termsofuse", $tou_3);
}

    // Load language file
    $lang->load('agreement');

    // Check if settings are installed, if not - do it
    $query_ag = $db->simple_select("settings", "*", "name='ag_force'");
    $result = $db->num_rows($query_ag);

    if (!$result) {
        // Add settings
        $query = $db->simple_select("settinggroups", "COUNT(*) as agrows");
        $rows = $db->fetch_field($query, "agrows");

        // Add Settinggroup
        $agreement_group = array(
            "name" => "UserAgreement",
            "title" => $db->escape_string($lang->ag_group_title),
            "description" => $db->escape_string($lang->ag_group_descr),
            "disporder" => $rows+1,
            "isdefault" => 0
        );
        $db->insert_query("settinggroups", $agreement_group);
        $gid = $db->insert_id();

        // Add settings for settinggroup
        $agreement_1 = array(
            "name" => "ag_force",
            "title" => $db->escape_string($lang->ag_force_title),
            "description" => $db->escape_string($lang->ag_force_descr),
            "optionscode" => "yesno",
            "value" => 1,
            "disporder" => 1,
            "gid" => (int)$gid
            );
        $db->insert_query("settings", $agreement_1);

        $agreement_2 = array(
            "name" => "ag_repeat",
            "title" => $db->escape_string($lang->ag_repeat_title),
            "description" => $db->escape_string($lang->ag_repeat_descr),
            "optionscode" => "yesno",
            "value" => 0,
            "disporder" => 2,
            "gid" => (int)$gid
            );
        $db->insert_query("settings", $agreement_2);

        $agreement_3 = array(
            "name" => "ag_allowedpages",
            "title" => $db->escape_string($lang->ag_allowedpages_title),
            "description" => $db->escape_string($lang->ag_allowedpages_descr),
            "optionscode" => "textarea",
            "value" => '',
            "disporder" => 3,
            "gid" => (int)$gid
            );
        $db->insert_query("settings", $agreement_3);

        $agreement_4 = array(
            "name" => "ag_usercp",
            "title" => $db->escape_string($lang->ag_usercp_title),
            "description" => $db->escape_string($lang->ag_usercp_descr),
            "optionscode" => "yesno",
            "value" => 1,
            "disporder" => 4,
            "gid" => (int)$gid
            );
        $db->insert_query("settings", $agreement_4);

        $agreement_5 = array(
            "name" => "ag_profile",
            "title" => $db->escape_string($lang->ag_profile_title),
            "description" => $db->escape_string($lang->ag_profile_descr),
            "optionscode" => "yesno",
            "value" => 1,
            "disporder" => 5,
            "gid" => (int)$gid
            );
        $db->insert_query("settings", $agreement_5);

        $agreement_6 = array(
            "name" => "ag_lang",
            "title" => $db->escape_string($lang->ag_lang_title),
            "description" => $db->escape_string($lang->ag_lang_descr),
            "optionscode" => "yesno",
            "value" => 0,
            "disporder" => 6,
            "gid" => (int)$gid
            );
        $db->insert_query("settings", $agreement_6);

        $agreement_7 = array(
            "name" => "ag_langtitle",
            "title" => $db->escape_string($lang->ag_langtitle_title),
            "description" => $db->escape_string($lang->ag_langtitle_descr),
            "optionscode" => "text",
            "value" => '',
            "disporder" => 7,
            "gid" => (int)$gid
            );
        $db->insert_query("settings", $agreement_7);
    }

    // Modify the language file field
    $query_lang = $db->simple_select("settings", "*", "name='ag_langtitle'");
    $results_lang = $db->fetch_array($query_lang);

    if (isset($results_lang['optionscode']) && $results_lang['optionscode'] != "text") {
        $update_l = array(
            "optionscode" => "text",
            "value" => ''
            );
        $db->update_query("settings", $update_l, "name='ag_langtitle'");
    }

    // Get the ID of the settings group
    $query_gid = $db->simple_select("settinggroups", "gid", "name='UserAgreement'", array("limit" => 1));
    $gid = $db->fetch_field($query_gid, "gid");

    // Check if ip settings are installed, if not - do it
    $query_ip = $db->simple_select("settings", "*", "name='ag_ipaddresses'");
    $result_ip = $db->num_rows($query_ip);

    if (!$result_ip) {
        $agreement_8 = array(
            "name" => "ag_ipaddresses",
            "title" => $db->escape_string($lang->ag_ipaddresses_title),
            "description" => $db->escape_string($lang->ag_ipaddresses_descr),
            "optionscode" => "select\n
never=".$db->escape_string($lang->ag_never)."\n
day=".$db->escape_string($lang->ag_day)."\n
threedays=".$db->escape_string($lang->ag_threedays)."\n
week=".$db->escape_string($lang->ag_week)."\n
twoweeks=".$db->escape_string($lang->ag_twoweeks)."\n
month=".$db->escape_string($lang->ag_month)."\n
tenweeks=".$db->escape_string($lang->ag_tenweeks)."\n
halfyear=".$db->escape_string($lang->ag_halfyear)."\n
year=".$db->escape_string($lang->ag_year)."\n
forever=".$db->escape_string($lang->ag_forever)."\n",
            "value" => "forever",
            "disporder" => 8,
            "gid" => (int)$gid
            );
        $db->insert_query("settings", $agreement_8);

        $agreement_11 = array(
            "name" => "ag_ip_del_user",
            "title" => $db->escape_string($lang->ag_ip_del_user_title),
            "description" => $db->escape_string($lang->ag_ip_del_user_descr),
            "optionscode" => "yesno",
            "value" => 1,
            "disporder" => 11,
            "gid" => (int)$gid
            );
        $db->insert_query("settings", $agreement_11);
    }

    // Check if regip-lastip settings are installed, if not - do it
    $query_regip = $db->simple_select("settings", "*", "name='ag_ipaddresses_users'");
    $result_regip = $db->num_rows($query_regip);

    if (!$result_regip) {
        $agreement_9 = array(
            "name" => "ag_ipaddresses_users",
            "title" => $db->escape_string($lang->ag_ipaddresses_users_title),
            "description" => $db->escape_string($lang->ag_ipaddresses_users_descr),
            "optionscode" => "yesno",
            "value" => 0,
            "disporder" => 9,
            "gid" => (int)$gid
            );
        $db->insert_query("settings", $agreement_9);

        $agreement_10 = array(
            "name" => "ag_ipaddresses_ratings",
            "title" => $db->escape_string($lang->ag_ipaddresses_ratings_title),
            "description" => $db->escape_string($lang->ag_ipaddresses_ratings_descr),
            "optionscode" => "yesno",
            "value" => 0,
            "disporder" => 10,
            "gid" => (int)$gid
            );
        $db->insert_query("settings", $agreement_10);
    }

    // Check if guest post settings are installed, if not - do it
    $query_gp = $db->simple_select("settings", "*", "name='ag_guest_post'");
    $result_gp = $db->num_rows($query_gp);

    if (!$result_gp) {
        $agreement_12 = array(
            "name" => "ag_guest_post",
            "title" => $db->escape_string($lang->ag_guest_post_title),
            "description" => $db->escape_string($lang->ag_guest_post_descr),
            "optionscode" => "yesno",
            "value" => 1,
            "disporder" => 12,
            "gid" => (int)$gid
            );
        $db->insert_query("settings", $agreement_12);
    }

    // Check if terms of use settings are installed, if not - do it
    $query_tou = $db->simple_select("settings", "*", "name='ag_tou'");
    $result_tou = $db->num_rows($query_tou);

    if (!$result_tou) {
        $agreement_13 = array(
            "name" => "ag_tou",
            "title" => $db->escape_string($lang->ag_tou_title),
            "description" => $db->escape_string($lang->ag_tou_descr),
            "optionscode" => "yesno",
            "value" => 0,
            "disporder" => 13,
            "gid" => (int)$gid
            );
        $db->insert_query("settings", $agreement_13);
    }

    // Refresh the settings language strings
    agreement_admin_settings_lang();

    // Refresh settings.php
    rebuild_settings();

    // Have we already added our IP purge task?
    $query = $db->simple_select('tasks', 'tid', "file='agreement_ip'", array('limit' => '1'));
    if ($db->num_rows($query) == 0) {
        // If not then do so
        require_once MYBB_ROOT.'/inc/functions_task.php';

        $ip_task = array(
            "title" => $lang->ag_task_name,
            "file" => 'agreement_ip',
            "description" => $lang->ag_task_description,
            "minute" => 0,
            "hour" => 0,
            "day" => '*',
            "weekday" => '*',
            "month" => '*',
            "nextrun" => TIME_NOW + 3600,
            "lastrun" => 0,
            "enabled" => 1,
            "logging" => 1,
            "locked" => 0,
        );

        $ip_task_id = (int)$db->insert_query('tasks', $ip_task);
        $nextrun = fetch_next_run($ip_task);
        $db->update_query('tasks', "nextrun='{$nextrun}', tid='{$ip_task_id}'");
    }

    // Enable the task
    $db->update_query('tasks', array('enabled' => 1), "file = 'agreement_ip'");
}

// Deactivate the plugin
function agreement_deactivate()
{
    global $db;

    change_admin_permission('config', 'termsofuse', -1);

    // Remove variables from profile and usercp templates
    require_once MYBB_ROOT.'/inc/adminfunctions_templates.php';
    find_replace_templatesets(
        "member_profile",
        "#".preg_quote('{$newterms}')."#i",
        '',
        0
    );
    find_replace_templatesets(
        "usercp",
        "#".preg_quote('{$newterms}')."#i",
        '',
        0
    );
    find_replace_templatesets(
        "newreply",
        "#".preg_quote('{$guestterms}')."#i",
        '',
        0
    );
    find_replace_templatesets(
        "newthread",
        "#".preg_quote('{$guestterms}')."#i",
        '',
        0
    );
    find_replace_templatesets(
        "showthread_quickreply",
        "#".preg_quote('{$guestterms}')."#i",
        '',
        0
    );

    // Disable the task
    $db->update_query('tasks', array('enabled' => 0), "file = 'agreement_ip'");
}

// Add templates
function agreement_templates_add()
{
    global $mybb, $db;

    // Add new templates
    $ag_template[1] = array(
        "title"     => "member_termsofuse_register",
        "template"  => $db->escape_string('<html>
<head>
<title>{$mybb->settings[\'bbname\']} - {$terms_of_use_title}</title>
{$headerinclude}
</head>
<body>
{$header}
<br />
<form action="member.php" method="post">
<table border="0" cellspacing="{$theme[\'borderwidth\']}" cellpadding="{$theme[\'tablespace\']}" class="tborder">
<tr>
<td class="thead"><strong>{$mybb->settings[\'bbname\']} - {$terms_of_use_title}</strong></td>
</tr>
{$coppa_agreement}
<tr>
<td class="trow1">
{$terms_of_use}
</td>
</tr>
<tr>
<td class="trow1">
<span class="smalltext">{$lang->ag_tou_date}{$terms_of_use_date}</span>
</td>
</tr>
</table>

<br />
<div align="center">
<input type="hidden" name="step" value="agreement" />
<input type="hidden" name="action" value="register" />
<input type="submit" class="button" name="agree" value="{$lang->i_agree}" />
</div>
</form>
{$footer}
</body>
</html>'),
        "sid"       => -2,
        "version"   => $mybb->version_code,
        "dateline"  => TIME_NOW
    );

    $ag_template[2] = array(
        "title"     => "member_termsofuse_force",
        "template"  => $db->escape_string('<html>
<head>
<title>{$mybb->settings[\'bbname\']} - {$terms_of_use_title}</title>
{$headerinclude}
</head>
<body>
{$header}
<br />
<form action="member.php" method="post">
<table border="0" cellspacing="{$theme[\'borderwidth\']}" cellpadding="{$theme[\'tablespace\']}" class="tborder">
<tr>
<td>{$lang->ag_tou_forced}</td>
</tr>
<tr>
<td class="thead"><strong>{$mybb->settings[\'bbname\']} - {$terms_of_use_title}</strong></td>
</tr>
{$coppa_agreement}
<tr>
<td class="trow1">
{$terms_of_use}
</td>
</tr>
<tr>
<td class="trow1">
<span class="smalltext">{$lang->ag_tou_date}{$terms_of_use_date}</span>
</td>
</tr>
</table>

<br />
<div align="center">
<input type="hidden" name="step" value="agreement" />
<input type="hidden" name="action" value="profile" />
<input type="submit" class="button" name="agree" value="{$lang->i_agree}" />
</div>
</form>
{$footer}
</body>
</html>'),
        "sid"       => -2,
        "version"   => $mybb->version_code,
        "dateline"  => TIME_NOW
    );

    $ag_template[3] = array(
        "title"     => "member_termsofuse_guest",
        "template"  => $db->escape_string('<html>
<head>
<title>{$mybb->settings[\'bbname\']} - {$terms_of_use_title}</title>
{$headerinclude}
</head>
<body>
{$header}
<br />
<table border="0" cellspacing="{$theme[\'borderwidth\']}" cellpadding="{$theme[\'tablespace\']}" class="tborder">
<tr>
<td class="thead"><strong>{$mybb->settings[\'bbname\']} - {$terms_of_use_title}</strong></td>
</tr>
{$coppa_agreement}
<tr>
<td class="trow1">
{$terms_of_use}
</td>
</tr>
<tr>
<td class="trow1">
<span class="smalltext">{$lang->ag_tou_date}{$terms_of_use_date}</span>
</td>
</tr>
</table>

<br />
<div align="center">
<input type="submit" class="button" name="agree" value="{$lang->i_agree}" onclick="alert(\'{$lang->ag_thanks_guest}\');" />
</div>
<br />
{$footer}
</body>
</html>'),
        "sid"       => -2,
        "version"   => $mybb->version_code,
        "dateline"  => TIME_NOW
    );

    foreach ($ag_template as $row) {
        $db->insert_query('templates', $row);
    }
}


################################################################################
// Front end functions


$plugins->add_hook("member_profile_start", "agreement_save");
// Save the agreement
function agreement_save()
{
    global $mybb, $db, $lang, $plugins;

    // Save the agreement and the timestamp if user has clicked to agree
    if ($mybb->user['uid'] != 0 && !empty($mybb->input['agree']) && $mybb->request_method == "post") {
        // Create a hook
        $plugins->run_hooks('agreement_save');

        $uid = (int)$mybb->user['uid'];
        $updated_record = array(
            "new_terms" => 1,
            "new_terms_date" => TIME_NOW
        );
        $db->update_query("users", $updated_record, "uid='".$uid."'");
        if (!isset($lang->ag_success_accept)) {
            $lang->load('agreement');
        }
        redirect("index.php", $lang->ag_success_accept);
    }
}

$plugins->add_hook("global_end", "agreement_force");
// Force existing users to accept the agreement
function agreement_force()
{
    global $mybb, $lang;

    // Forcing members disabled?
    if (isset($mybb->settings['ag_force']) && $mybb->settings['ag_force'] != 1) {
        return;
    }
    // Allow users to log in and out
    if (THIS_SCRIPT == "member.php" && $mybb->input['action'] == "login"
        || THIS_SCRIPT == "member.php" && $mybb->input['action'] == "logout"
    ) {
        return;
    }

    // Get the allowed pages from the setting
    $allowed_pages = explode(PHP_EOL, $mybb->settings['ag_allowedpages']);
    $redir = 0;

    if ($mybb->user['uid'] != 0
        && $mybb->user['new_terms'] != 1
        && $mybb->input['agreement'] != "do"
        && empty($mybb->input['agree'])
    ) {
        if (!isset($lang->ag_force_accept)) {
            $lang->load('agreement');
        }
        // No redirection from allowed pages
        foreach ($allowed_pages as $allowed_page) {
            $allowed_page = trim($allowed_page);
            if (my_strpos($_SERVER['REQUEST_URI'], $allowed_page) !== false) {
                ++$redir;
            }
        }
        if ($redir == 0) {
            redirect(
                "member.php?action=profile&agreement=do",
                $lang->ag_force_accept
            );
        }
    }
}

$plugins->add_hook("member_profile_start", "agreement_show");
// Show the agreement to existing users
function agreement_show()
{
    global $mybb, $db, $templates, $theme, $header, $footer, $headerinclude, $lang, $memprofile, $newterms;
    global $coppa_agreement, $terms_of_use, $terms_of_use_title, $terms_of_use_date;

    if (THIS_SCRIPT == "member.php"
        && $mybb->input['action'] == "profile"
        && $mybb->input['agreement'] == "do"
    ) {
        $terms_of_use = $terms_of_use_title = $terms_of_use_date = '';

        if (isset($mybb->settings['ag_tou']) && $mybb->settings['ag_tou'] == 1) {
            // Get the current used language
            $current_lang = $db->escape_string($mybb->settings['bblanguage']);

            $query = $db->simple_select("termsofuse", "*", "language='{$current_lang}'");
            $terms = $db->fetch_array($query);

            // MyCode Formating stuff
            require_once MYBB_ROOT . "inc/class_parser.php";
            $parser = new postParser;

            // Set up MyCode parser options
            $parser_options = array();
            $parser_options['allow_html'] = 0;
            $parser_options['allow_mycode'] = 1;
            $parser_options['allow_smilies'] = 1;
            $parser_options['allow_imgcode'] = 1;
            $parser_options['allow_videocode'] = 1;
            $parser_options['filter_badwords'] = 1;

            // Parse the text
            $terms_of_use = $parser->parse_message($terms['terms'], $parser_options);
            $terms_of_use_title = htmlspecialchars_uni($terms['title']);
            $terms_of_use_date = my_date($mybb->settings['dateformat'], (int)$terms['dateline'], '', 0);
        }

        // Loag language file
        if (!isset($lang->ag_thanks_guest)) {
            $lang->load('agreement');
        }

        // Show this page only to users who have not accepted yet or if repeated accepting is allowed or to admins
        if ($mybb->user['uid']
            && ($mybb->user['new_terms'] != 1 || $mybb->settings['ag_repeat'] != 0 || $mybb->usergroup['cancp'] == 1)
        ) {
            // If we have terms in the database
            if (!empty($terms_of_use)) {
                // Load the terms of use template
                eval("\$agreement = \"".$templates->get("member_termsofuse_force")."\";");
            } else {
                // Load the default agreement template
                eval("\$agreement = \"".$templates->get("member_register_agreement")."\";");
                $agreement = str_replace('value="register"', 'value="profile"', $agreement);
            }
            output_page($agreement);
            exit;
        } elseif (!$mybb->user['uid']) {
            // Show this page to guests, but without the button
            if (!empty($terms_of_use)) {
                eval("\$agreement = \"".$templates->get("member_termsofuse_guest")."\";");
            } else {
                eval("\$agreement = \"".$templates->get("member_register_agreement")."\";");
                $agreement = str_replace($lang->agreement_4, '', $agreement);
                $agreement = str_replace($lang->agreement_5, '', $agreement);
                $agreement = preg_replace('#<input type=\"submit(.*?)\/>#Ui', '', $agreement);
            }
            output_page($agreement);
            exit;
        } else {
            if (!isset($lang->ag_already_accepted)) {
                $lang->load('agreement');
            }
            redirect("index.php", $lang->ag_already_accepted);
        }
    }
}

$plugins->add_hook("global_start", "agreement_lang");
// Load custom language file
function agreement_lang()
{
    global $mybb, $lang;

    // Load custom language file globally if there is one defined in the settings
    if ($mybb->settings['ag_lang'] != 0 && !empty($mybb->settings['ag_langtitle'])) {
        $langfile = trim($mybb->settings['ag_langtitle']);
        $langfile = htmlspecialchars_uni($mybb->settings['ag_langtitle']);
        if (file_exists('inc/languages/'.$mybb->settings['bblanguage'].'/'.$langfile.'.lang.php')
        ) {
            $lang->load($langfile);
        }
    }
}

$plugins->add_hook("datahandler_user_insert", "agreement_date");
// Set agreement and date during registration
function agreement_date(&$user)
{
    $user->user_insert_data['new_terms'] = 1;
    $user->user_insert_data['new_terms_date'] = TIME_NOW;
}

$plugins->add_hook("usercp_start", "agreement_usercp");
// Display date of accepting agreement in User-CP for the user
function agreement_usercp()
{
    global $mybb, $lang, $newterms;

    if ($mybb->settings['ag_usercp'] != 0 && $mybb->user['new_terms'] != 0) {
        if (!isset($lang->ag_accepted)) {
            $lang->load('agreement');
        }
        $newtermsdate = my_date($mybb->settings['dateformat'], $mybb->user['new_terms_date'], '', 0);
        $newterms = '<br />'.$lang->ag_accepted.$newtermsdate;
    }
}

$plugins->add_hook("member_profile_end", "agreement_profile");
// Display date of accepting agreement in profile for the user and admins
function agreement_profile()
{
    global $mybb, $memprofile, $lang, $newterms;

    if ($mybb->settings['ag_profile'] != 0
        && $memprofile['new_terms'] != 0
        && ($mybb->usergroup['cancp'] == 1 || $mybb->user['uid'] == $memprofile['uid'])
    ) {
        if (!isset($lang->ag_accepted)) {
            $lang->load('agreement');
        }
        $newtermsdate = my_date($mybb->settings['dateformat'], $memprofile['new_terms_date'], '', 0);
        $newterms = '<br />'.$lang->ag_accepted.$newtermsdate;
    }
}

$plugins->add_hook("datahandler_user_delete_start", "agreement_user_delete");
// Delete all IP's of a user when deleting the user account
function agreement_user_delete(&$dh)
{
    global $mybb, $db;

    if (isset($mybb->settings['ag_ip_del_user']) && $mybb->settings['ag_ip_del_user'] == 1) {
        // Get the uid's of the deleted users
        $delete_uids = implode(',', $dh->delete_uids);

        // Purge all stored IP addresses of the deleted users
        $db->update_query('posts', array('ipaddress' => ''), "uid IN({$delete_uids})");
        $db->update_query('privatemessages', array('ipaddress' => ''), "fromid IN({$delete_uids})");
        $db->update_query('moderatorlog', array('ipaddress' => ''), "uid IN({$delete_uids})");
        $db->update_query('adminlog', array('ipaddress' => ''), "uid IN({$delete_uids})");
        $db->update_query('maillogs', array('ipaddress' => '','fromemail' => ''), "fromuid IN({$delete_uids})");
        $db->update_query('threadratings', array('ipaddress' => ''), "uid IN({$delete_uids})");
        $db->update_query('searchlog', array('ipaddress' => ''), "uid IN({$delete_uids})");
        // Delete poll IP's only in MyBB 1.8.15 and later
        if ($mybb->version_code >= 1815) {
            $db->update_query('pollvotes', array('ipaddress' => ''), "uid IN({$delete_uids})");
        }
    }
}

$plugins->add_hook("datahandler_post_insert_post", "agreement_post_ip");
$plugins->add_hook("datahandler_post_insert_thread_post", "agreement_post_ip");
// Don't store IP's for Posts
function agreement_post_ip(&$dh)
{
    global $mybb;

    if (isset($mybb->settings['ag_ipaddresses']) && $mybb->settings['ag_ipaddresses'] == 'never') {
        $dh->post_insert_data['ipaddress'] = '';
    }
}

$plugins->add_hook("datahandler_pm_insert_updatedraft", "agreement_pm_ip");
$plugins->add_hook("datahandler_pm_insert", "agreement_pm_ip");
$plugins->add_hook("datahandler_pm_insert_savedcopy", "agreement_pm_ip");
// Don't store IP's for PM's
function agreement_pm_ip(&$dh)
{
    global $mybb;

    if (isset($mybb->settings['ag_ipaddresses']) && $mybb->settings['ag_ipaddresses'] == 'never') {
        $dh->pm_insert_data['ipaddress'] = '';
    }
}

$plugins->add_hook("polls_vote_process", "agreement_poll_ip");
// Don't store IP's for Poll Votes
function agreement_poll_ip()
{
    global $mybb, $db, $poll;

    if (isset($mybb->settings['ag_ipaddresses']) && $mybb->settings['ag_ipaddresses'] == 'never') {
        $db->update_query("polls", array('ipaddress' => ''), "pid='".$poll['pid']."'");
    }
}

$plugins->add_hook("newreply_start", "agreement_guest_box");
$plugins->add_hook("newthread_start", "agreement_guest_box");
$plugins->add_hook("showthread_start", "agreement_guest_box");
// Display checkbox for guest posts
function agreement_guest_box()
{
    global $mybb, $lang, $guestterms;

    // Function disabled?
    if (isset($mybb->settings['ag_guest_post']) && $mybb->settings['ag_guest_post'] != 1) {
        return;
    }

    $guestterms = '';
    if (!isset($lang->ag_guestterms)) {
        $lang->load('agreement');
    }

    // Display checkbox for guests only
    if (!$mybb->user['uid']) {
        $guestterms = '<br />
<div align="center">
    <label><input type="checkbox" class="checkbox" name="guestterms" value="1" /> '.$lang->ag_guestterms.'</label>
<br /></div>';
    }
}

$plugins->add_hook("datahandler_post_validate_thread", "agreement_guest_post", 50);
$plugins->add_hook("datahandler_post_validate_post", "agreement_guest_post", 50);
// Allow guest posts and threads only if he agreed to the terms of use
function agreement_guest_post(&$dh)
{
    global $mybb, $lang;

    // Function enabled?
    if (isset($mybb->settings['ag_guest_post']) && $mybb->settings['ag_guest_post'] == 1) {
        // Check if we have a guest
        if ($mybb->user['uid'] == 0 && $dh->method == "insert") {
           $terms_agreed = $mybb->get_input('guestterms', MyBB::INPUT_INT);
            // Box was not checked, display error
            if ($terms_agreed != 1) {
                if (!isset($lang->ag_guestterms_notaccepted)) {
                    $lang->load('agreement');
                }
                $dh->set_error($lang->ag_guestterms_notaccepted);
            }
        }
    }
}

$plugins->add_hook("member_register_agreement", "agreement_tou");
// Replace terms of use at registration
function agreement_tou()
{
    global $mybb, $templates, $theme, $db, $lang, $header, $footer, $headerinclude;
    global $coppa_agreement, $terms_of_use, $terms_of_use_title, $terms_of_use_date;

    $terms_of_use = $terms_of_use_title = $terms_of_use_date = '';

    if (isset($mybb->settings['ag_tou']) && $mybb->settings['ag_tou'] == 1) {
        // Get the current used language
        $current_lang = $db->escape_string($mybb->settings['bblanguage']);
        $query = $db->simple_select("termsofuse", "*", "language='{$current_lang}'");
        $terms = $db->fetch_array($query);

        // MyCode Formating stuff
        require_once MYBB_ROOT . "inc/class_parser.php";
        $parser = new postParser;

        // Set up MyCode parser options
        $parser_options = array();
        $parser_options['allow_html'] = 0;
        $parser_options['allow_mycode'] = 1;
        $parser_options['allow_smilies'] = 1;
        $parser_options['allow_imgcode'] = 1;
        $parser_options['allow_videocode'] = 1;
        $parser_options['filter_badwords'] = 1;

        // parse the description
        $terms_of_use = $parser->parse_message($terms['terms'], $parser_options);
        $terms_of_use_title = htmlspecialchars_uni($terms['title']);
        $terms_of_use_date = my_date($mybb->settings['dateformat'], (int)$terms['dateline'], '', 0);
    }

    if (!empty($terms_of_use)) {
        if (!isset($lang->ag_tou_date)) {
            $lang->load('agreement');
        }
        // Load the terms of use template
        eval("\$agreement = \"".$templates->get("member_termsofuse_register")."\";");
        output_page($agreement);
        exit;
    }

}

##############################################################################
// Admin CP functions

$plugins->add_hook("admin_config_settings_change", "agreement_settings_change");
// Set peeker in ACP
function agreement_settings_change()
{
    global $db, $mybb, $agreement_settings_peeker;

    $result = $db->simple_select('settinggroups', 'gid', "name='UserAgreement'", array("limit" => 1));
    $group = $db->fetch_array($result);
    $agreement_settings_peeker = ($mybb->input['gid'] == $group['gid']) && ($mybb->request_method != 'post');
}

$plugins->add_hook("admin_settings_print_peekers", "agreement_settings_peek");
// Add peeker in ACP
function agreement_settings_peek(&$peekers)
{
    global $agreement_settings_peeker;

    if ($agreement_settings_peeker) {
        // Peeker for allowed pages settings
        $peekers[] = 'new Peeker($(".setting_ag_repeat"), $("#row_setting_ag_allowedpages"),/0/,true)';
        // Peeker for language file settings
        $peekers[] = 'new Peeker($(".setting_ag_lang"), $("#row_setting_ag_langtitle"),/1/,true)';
    }
}

$plugins->add_hook("admin_tools_action_handler", "agreement_admin_tools_action_handler");
// Set action handler for reset agreements
function agreement_admin_tools_action_handler(&$actions)
{
    $actions['agreement'] = array('active' => 'agreement', 'file' => 'agreement');
}

$plugins->add_hook("admin_load", "agreement_reset");
// Removes the entries of the users agreement to the terms of use
function agreement_reset()
{
    global $mybb, $db, $lang, $page, $run_module, $action_file;

    if ($page->active_action != 'agreement') {
        return false;
    }

    if ($run_module == 'tools' && $action_file == 'agreement') {
        if ($mybb->input['action'] == 'reset') {
            if (!verify_post_check($mybb->get_input('my_post_key'))) {
                flash_message($lang->invalid_post_verify_key2, 'error');
                admin_redirect("index.php?module=config-plugins");
            } else {
                $lang->load('agreement');
                if ($mybb->request_method == "post") {
                    if (empty($mybb->input['no'])) {
                        // Clear accepted terms und date of acceptance
                        $updated_record = array(
                            "new_terms" => 0
                        );
                        $db->update_query('users', $updated_record);

                        log_admin_action(htmlspecialchars_uni($lang->ag_reset));
                        flash_message($lang->ag_reset_success, 'success');
                    }
                    admin_redirect("index.php?module=config-plugins");
                } else {
                    $page->output_confirm_action(
                    "index.php?module=tools-agreement&amp;action=reset",
                    $lang->ag_reset_confirm
                    );
                }
            }
        }
        exit;
    }
}

$plugins->add_hook("admin_config_action_handler", "agreement_admin_config_action_handler");
// Set action handler for edit terms
function agreement_admin_config_action_handler(&$actions)
{
    $actions['termsofuse'] = array('active' => 'termsofuse', 'file' => 'termsofuse');
}

$plugins->add_hook("admin_config_permissions", "agreement_admin_config_permissions");
// Admin permissions
function agreement_admin_config_permissions(&$admin_permissions)
{
    global $lang;

    // Load language strings in plugin function
    if (!isset($lang->ag_can_edit)) {
        $lang->load("agreement");
    }
    $admin_permissions['termsofuse'] = $lang->ag_can_edit;
}

$plugins->add_hook("admin_config_menu", "agreement_admin_config_menu");
// ACP menu entry
function agreement_admin_config_menu(&$sub_menu)
{
    global $mybb, $lang;

    // Load language strings in plugin function
    if (!isset($lang->ag_tou_menu)) {
        $lang->load("agreement");
    }

    $sub_menu[] = array(
        'id' => 'termsofuse',
        'title' => $lang->ag_tou_menu,
        'link' => 'index.php?module=config-termsofuse'
    );
}

$plugins->add_hook("admin_load", "agreement_edit_terms");
// Removes the entries of the users agreement to the terms of use
function agreement_edit_terms()
{
    global $mybb, $db, $lang, $page, $run_module, $action_file;

    if ($page->active_action != 'termsofuse') {
        return false;
    }

    if ($run_module == 'config' && $action_file == 'termsofuse') {
        // Load language strings in plugin function
        if (!isset($lang->ag_tou_manage)) {
            $lang->load("agreement");
        }

        // Display info if terms setting is deactivated
        if ($mybb->settings['ag_tou'] != 1) {
            flash_message($lang->ag_tou_deactivated, 'error');
        }

        // Show overview
        if ($mybb->input['action'] == "" || !isset($mybb->input['action'])) {
            $page->add_breadcrumb_item($lang->ag_tou_manage);
            $page->output_header($lang->ag_tou_menu.' - '.$lang->ag_tou_manage);
            $sub_tabs['termsofuse'] = array(
                'title' => $lang->ag_tou_manage,
                'link' => 'index.php?module=config-termsofuse',
                'description' => $lang->ag_tou_manage_desc
            );
            $sub_tabs['termsofuse_add'] = array(
                'title' => $lang->ag_tou_add,
                'link' =>'index.php?module=config-termsofuse&amp;action=add',
                'description' => $lang->ag_tou_add_desc
            );
            $page->output_nav_tabs($sub_tabs, 'termsofuse');

            // Show errors
            if (isset($errors)) {
                $page->output_inline_error($errors);
            }

            // Build the overview
            $form = new Form("index.php?module=config-termsofuse", "post");

            $form_container = new FormContainer($lang->ag_tou_manage);
            $form_container->output_row_header('#');
            $form_container->output_row_header($lang->ag_tou_manage_title);
            $form_container->output_row_header($lang->ag_tou_language);
            $form_container->output_row_header('<div style="text-align: center;">'.$lang->ag_tou_options.'</div>');

            // Get the terms
            $i = 1;
            $query = $db->simple_select("termsofuse", "*");
            while ($terms_of_use = $db->fetch_array($query)) {
                $form_container->output_cell($i);
                $form_container->output_cell('<strong>'.htmlspecialchars_uni($terms_of_use['title']).'</strong>');
                $form_container->output_cell(htmlspecialchars_uni($terms_of_use['language']));
                $popup = new PopupMenu("termsofuse_{$terms_of_use['touid']}", $lang->ag_tou_options);
                $popup->add_item(
                    $lang->ag_tou_edit,
                    "index.php?module=config-termsofuse&amp;action=edit&amp;touid={$terms_of_use['touid']}"
                );
                $popup->add_item(
                    $lang->ag_tou_delete,
                    "index.php?module=config-termsofuse&amp;action=delete&amp;touid={$terms_of_use['touid']}"
                    ."&amp;my_post_key={$mybb->post_code}"
                );
                $form_container->output_cell($popup->fetch(), array("class" => "align_center"));
                $form_container->construct_row();
                ++$i;
            }

            $form_container->end();
            $form->end();
            $page->output_footer();

            exit;
        }

        // Add new terms
        if ($mybb->input['action'] == "add") {
            if ($mybb->request_method == "post") {
                // Check if required fields are not empty
                if (empty($mybb->input['title'])) {
                    $errors[] = $lang->ag_tou_empty_field.$lang->ag_tou_manage_title;
                }
                if (empty($mybb->input['language'])) {
                    $errors[] = $lang->ag_tou_empty_field.$lang->ag_tou_language;
                }
                if (empty($mybb->input['terms'])) {
                    $errors[] = $lang->ag_tou_empty_field.$lang->ag_tou_terms;
                }
                // Check if language is installed
                if (!$lang->language_exists($mybb->input['language'])) {
                    $errors[] = $lang->ag_tou_lang_installed.htmlspecialchars_uni($mybb->input['language']);
                }
                // Check if there are already terms for this language
                $newlang = $db->escape_string($mybb->input['language']);
                $query_lang = $db->simple_select("termsofuse", "*", "language='{$newlang}'");
                $langcheck = $db->num_rows($query_lang);
                if ($langcheck) {
                    $errors[] = $lang->ag_tou_lang_exists.htmlspecialchars_uni($mybb->input['language']);
                }

                // No errors - insert the terms of use
                if (empty($errors)) {
                    // Get the date
                    $new_date = TIME_NOW;
                    if ($mybb->input['update_now'] != 1) {
                        $termsdate = strtotime($mybb->input['updated']);
                        if ($termsdate !== false) {
                            $new_date = $termsdate;
                        }
                    }

                    $new_terms = array(
                        "name" => "tou_".$db->escape_string($mybb->input['language']),
                        "title" => $db->escape_string($mybb->input['title']),
                        "language" => $db->escape_string($mybb->input['language']),
                        "terms" => $db->escape_string($mybb->input['terms']),
                        "dateline" => (int)$new_date
                    );

                    $db->insert_query("termsofuse", $new_terms);

                    $mybb->input['module'] = $lang->ag_tou_menu;
                    $mybb->input['action'] = $lang->ag_tou_add_success." ";
                    log_admin_action(htmlspecialchars_uni($mybb->input['title']));

                    flash_message($lang->ag_tou_add_success, 'success');
                    admin_redirect("index.php?module=config-termsofuse");
                }

            }
            $page->add_breadcrumb_item($lang->ag_tou_add);

            // Editor scripts
            $page->extra_header .= <<<EOF

<link rel="stylesheet" href="../jscripts/sceditor/editor_themes/mybb.css" type="text/css" media="all" />
<script type="text/javascript" src="../jscripts/sceditor/jquery.sceditor.bbcode.min.js?ver=1805"></script>
<script type="text/javascript" src="../jscripts/bbcodes_sceditor.js?ver=1808"></script>
<script type="text/javascript" src="../jscripts/sceditor/editor_plugins/undo.js?ver=1805"></script>
EOF;

            $page->output_header($lang->ag_tou_menu.' - '.$lang->ag_tou_add);
            $sub_tabs['termsofuse'] = array(
                'title' => $lang->ag_tou_manage,
                'link' => 'index.php?module=config-termsofuse',
                'description' => $lang->ag_tou_manage_desc
            );
            $sub_tabs['termsofuse_add'] = array(
                'title' => $lang->ag_tou_add,
                'link' =>'index.php?module=config-termsofuse&amp;action=add',
                'description' => $lang->ag_tou_add_desc
            );
            $page->output_nav_tabs($sub_tabs, 'termsofuse_add');

            // Show errors
            if (isset($errors)) {
                $page->output_inline_error($errors);
            }

            // Build the form
            $form = new Form("index.php?module=config-termsofuse&amp;action=add", "post", "", 1);

            $form_container = new FormContainer($lang->ag_tou_add);
            $form_container->output_row(
                $lang->ag_tou_manage_title.' <em>*</em>',
                $lang->ag_tou_manage_title_desc,
                $form->generate_text_box('title', $mybb->input['title'])
            );
            $form_container->output_row(
                $lang->ag_tou_language.' <em>*</em>',
                $lang->ag_tou_language_desc,
                $form->generate_text_box('language', $mybb->input['language'])
            );
            $terms_editor = $form->generate_text_area('terms', $mybb->input['terms'], array(
                    'id' => 'terms',
                    'rows' => '25',
                    'cols' => '70',
                    'style' => 'height: 450px; width: 75%'
                )
            );
            $terms_editor .= build_mycode_inserter('terms');
            $form_container->output_row(
                $lang->ag_tou_terms.' <em>*</em>',
                $lang->ag_tou_terms_desc,
                $terms_editor,
                'terms'
            );
            $form_container->output_row(
                $lang->ag_tou_date,
                $lang->ag_tou_date_desc,
                $form->generate_text_box('updated', $mybb->input['updated'])
            );
            $form_container->output_row(
                $lang->ag_tou_datenow,
                $lang->ag_tou_datenow_desc,
                $form->generate_check_box(
                    'update_now',
                    1,
                    $lang->ag_tou_datenow_save,
                    array('checked' => $mybb->input['update_now'])),
                'update_now'
            );
            $form_container->end();
            $buttons[] = $form->generate_submit_button($lang->ag_tou_add_submit);
            $form->output_submit_wrapper($buttons);
            $form->end();
            $page->output_footer();

            exit;
        }

        // Edit terms
        if ($mybb->input['action'] == "edit") {
            if ($mybb->request_method == "post") {
                // Check if required fields are not empty
                if (empty($mybb->input['title'])) {
                    $errors[] = $lang->ag_tou_empty_field.$lang->ag_tou_manage_title;
                }
                if (empty($mybb->input['terms'])) {
                    $errors[] = $lang->ag_tou_empty_field.$lang->ag_tou_terms;
                }

                // No errors - insert the terms of use
                if (empty($errors)) {
                    $touid = $mybb->get_input('touid', MyBB::INPUT_INT);
                    // Get the date
                    $edit_date = TIME_NOW;
                    if ($mybb->input['update_now'] != 1) {
                        $termsdate = strtotime($mybb->input['updated']);
                        if ($termsdate !== false) {
                            $edit_date = $termsdate;
                        }
                    }

                    $edited_terms = array(
                        "name" => "tou_".$db->escape_string($mybb->input['language']),
                        "title" => $db->escape_string($mybb->input['title']),
                        "language" => $db->escape_string($mybb->input['language']),
                        "terms" => $db->escape_string($mybb->input['terms']),
                        "dateline" => (int)$edit_date
                    );

                    $db->update_query("termsofuse", $edited_terms, "touid='{$touid}'");

                    $mybb->input['module'] = $lang->ag_tou_menu;
                    $mybb->input['action'] = $lang->ag_tou_edit_success." ";
                    log_admin_action(htmlspecialchars_uni($mybb->input['title']));

                    flash_message($lang->ag_tou_edit_success, 'success');
                    admin_redirect("index.php?module=config-termsofuse");
                }

            }
            $page->add_breadcrumb_item($lang->ag_tou_edit);

            // Editor scripts
            $page->extra_header .= <<<EOF

<link rel="stylesheet" href="../jscripts/sceditor/editor_themes/mybb.css" type="text/css" media="all" />
<script type="text/javascript" src="../jscripts/sceditor/jquery.sceditor.bbcode.min.js?ver=1805"></script>
<script type="text/javascript" src="../jscripts/bbcodes_sceditor.js?ver=1808"></script>
<script type="text/javascript" src="../jscripts/sceditor/editor_plugins/undo.js?ver=1805"></script>
EOF;

            $page->output_header($lang->ag_tou_menu.' - '.$lang->ag_tou_edit);
            $sub_tabs['termsofuse'] = array(
                'title' => $lang->ag_tou_manage,
                'link' => 'index.php?module=config-termsofuse',
                'description' => $lang->ag_tou_manage_desc
            );
            $sub_tabs['termsofuse_add'] = array(
                'title' => $lang->ag_tou_add,
                'link' =>'index.php?module=config-termsofuse&amp;action=add',
                'description' => $lang->ag_tou_add_desc
            );
            $sub_tabs['termsofuse_edit'] = array(
                'title' => $lang->ag_tou_edit,
                'link' =>'index.php?module=config-termsofuse&amp;action=edit',
                'description' => $lang->ag_tou_edit_desc
            );
            $page->output_nav_tabs($sub_tabs, 'termsofuse_edit');

            // Show errors
            if (isset($errors)) {
                $page->output_inline_error($errors);
            }

            // Get the data
            $touid = $mybb->get_input('touid', MyBB::INPUT_INT);
            $query_edit = $db->simple_select("termsofuse", "*", "touid={$touid}");
            $edit_terms = $db->fetch_array($query_edit);

            // Build the form
            $form = new Form("index.php?module=config-termsofuse&amp;action=edit", "post", "", 1);
            echo $form->generate_hidden_field('touid', $touid);
            echo $form->generate_hidden_field('language', htmlspecialchars_uni($edit_terms['language']));

            $form_container = new FormContainer($lang->ag_tou_edit);
            $form_container->output_row(
                $lang->ag_tou_manage_title,
                $lang->ag_tou_manage_title_desc,
                $form->generate_text_box('title', htmlspecialchars_uni($edit_terms['title']))
            );
            $form_container->output_row(
                $lang->ag_tou_language.':',
                '<span style="font-size:1.2em;margin:10px;">'.htmlspecialchars_uni($edit_terms['language']).'</span>'
            );
            $terms_editor = $form->generate_text_area('terms', $edit_terms['terms'], array(
                    'id' => 'terms',
                    'rows' => '25',
                    'cols' => '70',
                    'style' => 'height: 450px; width: 75%'
                )
            );
            $terms_editor .= build_mycode_inserter('terms');
            $form_container->output_row(
                $lang->ag_tou_terms,
                $lang->ag_tou_terms_desc,
                $terms_editor,
                'terms'
            );
            $terms_dateline = my_date($mybb->settings['dateformat'], (int)$edit_terms['dateline'], '', 0);
            $form_container->output_row(
                $lang->ag_tou_date,
                $lang->ag_tou_date_desc,
                $form->generate_text_box('updated', htmlspecialchars_uni($terms_dateline))
            );
            $form_container->output_row(
                $lang->ag_tou_datenow,
                $lang->ag_tou_datenow_desc,
                $form->generate_check_box(
                    'update_now',
                    1,
                    $lang->ag_tou_datenow_save,
                    array('checked' => $mybb->input['update_now'])),
                'update_now'
            );
            $form_container->end();
            $buttons[] = $form->generate_submit_button($lang->ag_tou_edit_submit);
            $form->output_submit_wrapper($buttons);
            $form->end();
            $page->output_footer();

            exit;
        }

        // Remove Terms of use
        if ($mybb->input['action'] == "delete") {
            // Get terms data
            $touid = $mybb->get_input('touid', MyBB::INPUT_INT);
            $query_del = $db->simple_select("termsofuse", "*", "touid={$touid}");
            $del_terms = $db->fetch_array($query_del);

            if (empty($touid)) {
                flash_message($lang->ag_tou_delete_invalid, 'error');
                admin_redirect('index.php?module=config-termsofuse');
            }
            // Cancel button pressed?
            if (isset($mybb->input['no']) && $mybb->input['no']) {
                admin_redirect('index.php?module=config-termsofuse');
            }

            if (!verify_post_check($mybb->input['my_post_key'])) {
                flash_message($lang->invalid_post_verify_key2, 'error');
                admin_redirect("index.php?module=config-termsofuse");
            } else {
                if ($mybb->request_method == "post") {
                    // Delete terms entry
                    $db->delete_query("termsofuse", "touid='{$touid}'");

                    $mybb->input['module'] = $lang->ag_tou_menu;
                    $mybb->input['action'] = $lang->ag_tou_delete_success." ";
                    log_admin_action(htmlspecialchars_uni($del_terms['title']));

                    flash_message($lang->ag_tou_delete_success, 'success');
                    admin_redirect('index.php?module=config-termsofuse');
                } else {
                    $page->output_confirm_action(
                        "index.php?module=config-termsofuse&amp;action=delete&amp;touid={$touid}",
                        $lang->ag_tou_delete_confirm
                    );
                }
            }
            exit;
        }
    }
}

$plugins->add_hook("admin_config_settings_start", "agreement_admin_language_change");
// Change settings language strings after switching ACP language
function agreement_admin_language_change()
{
    global $mybb, $db, $lang;
    // Load language strings in plugin function
    if (!isset($lang->ag_group_descr)) {
        $lang->load('agreement');
    }

    // Get settings language string
    $query = $db->simple_select('settinggroups', '*', "name='UserAgreement'");
    $aggroup = $db->fetch_array($query);

    if ($aggroup['description'] != $db->escape_string($lang->ag_group_descr)) {
        agreement_admin_settings_lang();
    }
}

// Update language strings in settings
function agreement_admin_settings_lang()
{
    global $mybb, $db, $lang;

    // Load language strings in plugin function
    if (!isset($lang->ag_group_descr)) {
        $lang->load('agreement');
    }

    // Update setting group
    $updated_record_gr = array(
        "title" => $db->escape_string($lang->ag_group_title),
        "description" => $db->escape_string($lang->ag_group_descr)
            );
    $db->update_query('settinggroups', $updated_record_gr, "name='UserAgreement'");

    // Update settings
    $updated_record1 = array(
        "title" => $db->escape_string($lang->ag_force_title),
        "description" => $db->escape_string($lang->ag_force_descr)
            );
    $db->update_query('settings', $updated_record1, "name='ag_force'");

    $updated_record2 = array(
        "title" => $db->escape_string($lang->ag_repeat_title),
        "description" => $db->escape_string($lang->ag_repeat_descr)
            );
    $db->update_query('settings', $updated_record2, "name='ag_repeat'");

    $updated_record3 = array(
        "title" => $db->escape_string($lang->ag_allowedpages_title),
        "description" => $db->escape_string($lang->ag_allowedpages_descr)
            );
    $db->update_query('settings', $updated_record3, "name='ag_allowedpages'");

    $updated_record4 = array(
        "title" => $db->escape_string($lang->ag_usercp_title),
        "description" => $db->escape_string($lang->ag_usercp_descr)
            );
    $db->update_query('settings', $updated_record4, "name='ag_usercp'");

    $updated_record5 = array(
        "title" => $db->escape_string($lang->ag_profile_title),
        "description" => $db->escape_string($lang->ag_profile_descr)
            );
    $db->update_query('settings', $updated_record5, "name='ag_profile'");

    $updated_record6 = array(
        "title" => $db->escape_string($lang->ag_lang_title),
        "description" => $db->escape_string($lang->ag_lang_descr)
            );
    $db->update_query('settings', $updated_record6, "name='ag_lang'");

    $updated_record7 = array(
        "title" => $db->escape_string($lang->ag_langtitle_title),
        "description" => $db->escape_string($lang->ag_langtitle_descr)
            );
    $db->update_query('settings', $updated_record7, "name='ag_langtitle'");

    $updated_record8 = array(
        "title" => $db->escape_string($lang->ag_ipaddresses_title),
        "description" => $db->escape_string($lang->ag_ipaddresses_descr)
            );
    $db->update_query('settings', $updated_record8, "name='ag_ipaddresses'");

    $updated_record9 = array(
        "title" => $db->escape_string($lang->ag_ipaddresses_users_title),
        "description" => $db->escape_string($lang->ag_ipaddresses_users_descr)
            );
    $db->update_query('settings', $updated_record9, "name='ag_ipaddresses_users'");

    $updated_record10 = array(
        "title" => $db->escape_string($lang->ag_ipaddresses_ratings_title),
        "description" => $db->escape_string($lang->ag_ipaddresses_ratings_descr)
            );
    $db->update_query('settings', $updated_record10, "name='ag_ipaddresses_ratings'");

    $updated_record11 = array(
        "title" => $db->escape_string($lang->ag_ip_del_user_title),
        "description" => $db->escape_string($lang->ag_ip_del_user_descr),
        "disporder" => 11
            );
    $db->update_query('settings', $updated_record11, "name='ag_ip_del_user'");

    $updated_record12 = array(
        "title" => $db->escape_string($lang->ag_guest_post_title),
        "description" => $db->escape_string($lang->ag_guest_post_descr),
        "disporder" => 12
            );
    $db->update_query('settings', $updated_record12, "name='ag_guest_post'");

    $updated_record13 = array(
        "title" => $db->escape_string($lang->ag_tou_title),
        "description" => $db->escape_string($lang->ag_tou_descr),
        "disporder" => 13
            );
    $db->update_query('settings', $updated_record13, "name='ag_tou'");

    rebuild_settings();
}
