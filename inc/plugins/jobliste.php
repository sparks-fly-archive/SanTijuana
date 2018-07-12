<?php

// Disallow direct access to this file for security reasons
if(!defined("IN_MYBB"))
{
    die("Direct initialization of this file is not allowed.");
}

// Backend Hooks
$plugins->add_hook("admin_formcontainer_end", "jobliste_usergroup_permission");
$plugins->add_hook("admin_user_groups_edit_commit", "jobliste_usergroup_permission_commit");

// Frontend Hooks
$plugins->add_hook("misc_start", "jobliste_misc");
$plugins->add_hook("global_intermediate", "jobliste_global");
$plugins->add_hook("modcp_start", "jobliste_modcp");

if(THIS_SCRIPT == 'modcp.php')
{
    global $templatelist;
    if(isset($templatelist))
    {
        $templatelist .= ',';
    }
    
    if($GLOBALS['mybb']->input['action'] == 'jobliste')
        $templatelist .= 'modcp_jobliste,modcp_jobliste_bit,modcp_jobliste_nav';
    else
        $templatelist .= 'modcp_jobliste_nav';
}

function jobliste_info()
{
    return array(
        "name"            => "Interaktive Jobliste (RPG-Plugin)",
        "description"    => "Erstellt eine automatische Liste, in der Usergruppen nach Wahl Arbeitsstellen hinzufügen können. Benutzer können sich als Mitarbeiter samt Position zu diesen Arbeitsstellen hinzufügen.",
        "website"        => "http://storming-gates.de",
        "author"        => "Storming Gates",
        "authorsite"    => "http://storming-gates.de",
        "version"        => "1.0",
        "codename"        => "jobliste",
        "compatibility" => "18"
    );
}

function jobliste_install()
{
    global $db, $mybb, $cache;

    // Update Datenbank
   $db->add_column("usergroups", "canaddjob", "tinyint NOT NULL default '1'");
   $db->add_column("usergroups", "canjoinjob", "tinyint NOT NULL default '1'");
   $cache->update_usergroups();
  
   $db->add_column("users", "jid", "smallint NOT NULL default '0'");
   $db->add_column("users", "job", "varchar(155) NOT NULL");
  
   $db->query("CREATE TABLE ".TABLE_PREFIX."jobs (
   `jid` int(11) NOT NULL AUTO_INCREMENT,
   `createdby` int(11) NOT NULL,
   `name` varchar(155) NOT NULL,
   `desc` varchar(2500) NOT NULL,
   `type` varchar(155) NOT NULL,
   `accepted` int(11) NOT NULL,
   `datum` int(11) NOT NULL,
   PRIMARY KEY (`jid`),
   KEY `jid` (`jid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1");

// Templates hinzufügen
  $insert_array = array(
        'title'        => 'misc_jobliste',
        'template'    => $db->escape_string('<html>
<head>
<title>{$mybb->settings[\'bbname\']} - {$lang->jobliste}</title>
{$headerinclude}
</head>
<body>
{$header}
<table border="0" cellspacing="{$theme[\'borderwidth\']}" cellpadding="{$theme[\'tablespace\']}" class="tborder">
    <tr>
        <td class="thead"><span class="smalltext"><strong>Jobliste</strong></span></td>
    </tr>
    <tr>
        <td class="trow2"><span>{$lang->jobliste_description}</span></td>
    </tr>
</table>
<br />
{$add_job}
<br />    
{$join_job}
<br />
<table border="0" cellspacing="{$theme[\'borderwidth\']}" cellpadding="{$theme[\'tablespace\']}" class="tborder">
    <tr>
        <td class="thead"><span class="smalltext"><strong>Jobübersicht</strong></span></td>
    </tr>
    <tr>
        <td class="trow2" align="center">
            <div class="float_left_" style="margin:auto" valign="middle">
                <form id="jobs" method="get" action="misc.php?action=jobliste">
                    <input type="hidden" name="action" value="jobliste" />
                    Filtern nach:
                    <select name="type">
                        <option value="">Branche auswählen</option>
                        <option value="Agrarwirtschaft">Agrarwirtschaft</option>
                        <option value="Bildung">Bildungseinrichtungen</option>
                        <option value="Dienstleistung">Dienstleistung</option>
                        <option value="Einzelhandel">Einzelhandel</option>
                        <option value="Gastronomie">Gastronomie</option>
                        <option value="Gesundheit">Gesundheit</option>
                        <option value="Handwerk">Handwerk</option>
                        <option value="Informationstechnik">Informationstechnik</option>
                        <option value="Industrie">Industrie</option>
                        <option value="Tourismus">Tourismus</option>
                        <option value="Verwaltung">öfftl. Einrichtungen / Verwaltung</option>
                    </select>
                    <input type="submit" value="{$lang->jobliste_filter}" class="button" />
                </form>
            </div>
        </td>
    </tr>
    <tr>
        <td class="trow2" width="100%">
            <center>{$multipage}</center><br />
            {$job_bit}
            <center>{$multipage}</center><br />
        </td>
    </tr>
</table>
{$footer}
</body>
</html>'),
        'sid'        => '-1',
        'version'    => '',
        'dateline'    => TIME_NOW
    );
    $db->insert_query("templates", $insert_array);
    
    $insert_array = array(
          'title'        => 'misc_jobliste_add',
          'template'    => $db->escape_string('<form method="post" action="misc.php" id="add_jobs">
    <table border="0" cellspacing="{$theme[\'borderwidth\']}" cellpadding="{$theme[\'tablespace\']}" class="tborder">
        {$job_added}
        <tr>
            <td class="thead" colspan="3">{$lang->jobliste_add}</td>
        </tr>
        <tr>
            <td class="tcat" width="33%" align="center">{$lang->jobliste_name}</td>
            <td class="tcat" width="33%" align="center">{$lang->jobliste_desc}</td>
            <td class="tcat" width="33%" align="center">{$lang->jobliste_branche}</td>
        </tr>
        <tr>
            <td class="trow2" align="center"><textarea name="name" id="name" style="width: 200px; height: 25px;"></textarea></td>
            <td class="trow2" align="center"><textarea name="desc" id="desc" style="width: 200px; height: 50px;"></textarea></td>
            <td class="trow2" align="center">
                <select name="type" id="type">
                    <option value="auswaehlen">Branche auswählen</option>
                    <option value="Agrarwirtschaft">Agrarwirtschaft</option>
                    <option value="Bildung">Bildungseinrichtungen</option>
                    <option value="Dienstleistung">Dienstleistung</option>
                    <option value="Einzelhandel">Einzelhandel</option>
                    <option value="Gastronomie">Gastronomie</option>
                    <option value="Gesundheit">Gesundheit</option>
                    <option value="Handwerk">Handwerk</option>
                    <option value="Informationstechnik">Informationstechnik</option>
                    <option value="Industrie">Industrie</option>
                    <option value="Tourismus">Tourismus</option>
                    <option value="Verwaltung">öfftl. Einrichtungen / Verwaltung</option>
                </select>
            </td>
        </tr>
        <tr>
            <td class="trow2" colspan="3" align="center">
                <input type="hidden" name="action" value="add_job" />
                <input type="submit" value="{$lang->jobliste_add}" name="add_jobs" class="button" />
            </td>
        </tr>
    </table>
</form>'),
          'sid'        => '-1',
          'version'    => '',
          'dateline'    => TIME_NOW
      );
      $db->insert_query("templates", $insert_array);
    
    $insert_array = array(
          'title'        => 'misc_jobliste_bit',
          'template'    => $db->escape_string('<table class="tborder" cellspacing="{$theme[\'borderwidth\']}" cellpadding="{$theme[\'tablespace\']}">
    <tr>
        <td class="tcat" colspan="3">{$job[\'name\']} &raquo; {$job[\'type\']}</td>
    </tr>
    <tr>
        <td class="trow2" width="10%" align="center">
            <img src="images/jobliste/{$job[\'type\']}.png" width="40px" />
        </td>
        <td class="trow2 smalltext" align="justify" width="55%">
            <div style="max-height: 80px; overflow: auto;">
                {$job[\'desc\']}
            </div>
        </td>
        <td class="trow2" width="35%">
            <div style="max-height: 80px; overflow: auto;">
                {$users_bit}
            </div>
        </td>
    </tr>
</table>
<br />'),
          'sid'        => '-1',
          'version'    => '',
          'dateline'    => TIME_NOW
      );
      $db->insert_query("templates", $insert_array);
    
    $insert_array = array(
          'title'        => 'misc_jobliste_bit_users',
          'template'    => $db->escape_string('<div class="trow1" style="padding: 3px; margin-bottom: 3px;">{$user[\'profilelink\']} &raquo; {$user[\'job\']}</div>'),
          'sid'        => '-1',
          'version'    => '',
          'dateline'    => TIME_NOW
      );
      $db->insert_query("templates", $insert_array);
    
    $insert_array = array(
          'title'        => 'misc_jobliste_join',
          'template'    => $db->escape_string('<form method="post" action="misc.php" id="join_job">
    <table border="0" cellspacing="{$theme[\'borderwidth\']}" cellpadding="{$theme[\'tablespace\']}" class="tborder">
        <tr>
            <td class="thead" colspan="2">{$lang->jobliste_join}</td>
        </tr>
        <tr>
            <td class="tcat" width="33%" align="center">{$lang->jobliste_position}</td>
            <td class="tcat" width="33%" align="center">{$lang->jobliste_name}</td>
        </tr>
        <tr>
            <td class="trow2" align="center"><input type="text" name="job" value="{$mybb->user[\'job\']}" /></td>
            <td class="trow2" align="center">
                <select name="jid">
                    <option value="">Betrieb auswählen</option>
                    {$jobs_options_bit}
                </select>
            </td>
        </tr>
        <tr>
            <td class="trow2" colspan="2" align="center">
                <input type="hidden" name="action" value="join_job" />
                <input type="submit" value="{$lang->jobliste_join}" name="join_jobs" class="button" />
            </td>
        </tr>
    </table>
</form>'),
          'sid'        => '-1',
          'version'    => '',
          'dateline'    => TIME_NOW
      );
      $db->insert_query("templates", $insert_array);
    
    $insert_array = array(
          'title'        => 'modcp_jobliste',
          'template'    => $db->escape_string('<html>
<head>
<title>{$mybb->settings[\'bbname\']} -  Jobs freischalten</title>
{$headerinclude}
</head>
<body>
{$header}
<table width="100%" border="0" align="center">
    <tr>
        {$modcp_nav}
        <td valign="top">
            <table border="0" cellspacing="{$theme[\'borderwidth\']}" cellpadding="{$theme[\'tablespace\']}" class="tborder">
                <tr>
                    <td class="thead">
                        <strong>Jobs freischalten</strong>
                    </td>
                </tr>
                <tr>
                    <td class="trow2">{$jobs_bit}</td>
                </tr>
            </table>
        </td>
    </tr>
</table>
{$footer}
</body>
</html>'),
          'sid'        => '-1',
          'version'    => '',
          'dateline'    => TIME_NOW
      );
      $db->insert_query("templates", $insert_array);
    
    $insert_array = array(
          'title'        => 'modcp_jobliste_bit',
          'template'    => $db->escape_string('<table width="100%" border="0">
    <tr>
        <td class="tcat" colspan="2">{$lang->jobliste_by} {$auser}: {$job[\'name\']} &raquo; {$job[\'type\']} | Eingesendet: {$datum}</td>
    </tr>
    <tr>
        <td class="trow2" width="50%" align="justify">
            {$job[\'desc\']}
        </td>
        <td class="trow2" align="center" width="50%">
            <form method="post" name="reason">
                <textarea name="reason" id="reason">{$lang->jobliste_reason}</textarea>
                <input type="hidden" name="rjid" value="{$job[\'jid\']}" />
                <input type="hidden" name="action" value="do_modcp_jobliste" />
                <center><input type="submit" class="button" value="{$lang->jobliste_decline}" /></center>
            </form>
        </td>
    </tr>
    <tr>
        <td class="trow2" align="center" colspan="2">
            <a href="modcp.php?action=do_modcp_jobliste&amp;aid={$job[\'jid\']}">{$lang->jobliste_accept}</a>
        </td>
    </tr>
</table>'),
          'sid'        => '-1',
          'version'    => '',
          'dateline'    => TIME_NOW
      );
      $db->insert_query("templates", $insert_array);
    
    $insert_array = array(
        'title' => 'modcp_jobliste_nav',
        'template' => $db->escape_string('
        <tr><td class="trow1 smalltext"><a href="modcp.php?action=jobliste" class="modcp_nav_item modcp_jobliste">Jobliste</td></tr>'),
        'sid' => "-1",
          'version'    => '',
          'dateline'    => TIME_NOW
    );
    $db->insert_query("templates", $insert_array);
}

function jobliste_is_installed()
{
    global $db;
    if($db->field_exists("canaddjob", "usergroups"))
    {
        return true;
    }
    return false;
}

function jobliste_uninstall()
{
    global $db, $cache;
    
    // Update Datenbank
      if($db->field_exists("canaddjob", "usergroups"))
    {
      $db->drop_column("usergroups", "canaddjob");
    }
    if($db->field_exists("canjoinjob", "usergroups"))
    {
      $db->drop_column("usergroups", "canjoinjob");
    }
    $cache->update_usergroups();
    
    if($db->field_exists("jid", "users"))
    {
      $db->drop_column("users", "jid");
    }
    if($db->field_exists("job", "users"))
    {
      $db->drop_column("users", "job");
    }

    $db->query("DROP TABLE ".TABLE_PREFIX."jobs");
    
    // Templates entfernen
    $db->delete_query("templates", "title LIKE '%jobliste%'");
}

function jobliste_activate()
{
    global $mybb;
    // Variablen einfügen
    include MYBB_ROOT."/inc/adminfunctions_templates.php";
      find_replace_templatesets("header", "#".preg_quote('{$bbclosedwarning}')."#i", '{$bbclosedwarning} {$new_jobs}');
    find_replace_templatesets("modcp_nav_users", "#".preg_quote('{$nav_ipsearch}').'#i', '{$nav_ipsearch} {$nav_jobliste}');
}

function jobliste_deactivate()
{
    global $mybb;
    // Variablen entfernen
    include MYBB_ROOT."/inc/adminfunctions_templates.php";
      find_replace_templatesets("header", "#".preg_quote('{$new_jobs}')."#i", '', 0);
    find_replace_templatesets("modcp_nav_users", "#".preg_quote('{$nav_jobliste}')."#i", '', 0);
}

// Usergruppen-Berechtigungen
function jobliste_usergroup_permission()
{
    global $mybb, $lang, $form, $form_container, $run_module;

    if($run_module == 'user' && !empty($form_container->_title) & !empty($lang->misc) & $form_container->_title == $lang->misc)
    {
        $avatar2go_options = array(
            $form->generate_check_box('canaddjob', 1, "Kann Arbeitsstellen hinzufügen?", array("checked" => $mybb->input['canaddjob'])),
            $form->generate_check_box('canjoinjob', 1, "Kann Arbeitsstellen beitreten?", array("checked" => $mybb->input['canjoinjob'])),
        );
        $form_container->output_row("Interaktive Jobliste", "", "<div class=\"group_settings_bit\">".implode("</div><div class=\"group_settings_bit\">", $avatar2go_options)."</div>");
    }
}

function jobliste_usergroup_permission_commit()
{
    global $db, $mybb, $updated_group;
    $updated_group['canaddjob'] = $mybb->get_input('canaddjob', MyBB::INPUT_INT);
    $updated_group['canjoinjob'] = $mybb->get_input('canjoinjob', MyBB::INPUT_INT);
}

function jobliste_misc() {
    global $mybb, $db, $lang, $templates, $headerinclude, $header, $footer, $add_job, $theme;
    
    $lang->load('jobliste');
    
    $mybb->input['action'] = $mybb->get_input('action');
    $uid = $mybb->user['uid'];
    if($mybb->input['action'] == "jobliste") {
        
        $query = $db->query("SELECT jid FROM ".TABLE_PREFIX."jobs WHERE createdby = '$uid' AND accepted = '0'");
        $check = $db->fetch_field($query, "jid");
        if($check) {
            $job_added = "<tr><td colspan=\"3\"><div class=\"red_alert\">{$lang->jobliste_added_moderation}</div></td></tr>";
        }
        
        if($mybb->usergroup['canaddjob'] == "1") {
            eval("\$add_job = \"".$templates->get("misc_jobliste_add")."\";");
        }
        
        if($mybb->usergroup['canjoinjob'] == "1") {
            
            $query = $db->query("SELECT jid, name FROM ".TABLE_PREFIX."jobs ORDER by name ASC");
            while($names = $db->fetch_array($query)) {
                $checked = "";
                if($names['jid'] == $mybb->user['jid']) {
                    $checked = "selected=\"selected\"";
                }
                $jobs_options_bit .= "<option value=\"{$names[jid]}\" $checked>{$names[name]}</option>";
            }
            
            eval("\$join_job = \"".$templates->get("misc_jobliste_join")."\";");
        }
        
        $type = $db->escape_string($mybb->get_input('type'));
        if(empty($type)) {
            $type = "%";
        }
        
        $limit = 20;
        $jidnum = $db->fetch_field($db->simple_select('jobs', 'COUNT(jid) AS jidnum', "accepted != '0' AND type LIKE '$type' ORDER by name ASC"), 'jidnum');
        $pagenum = (int)$mybb->input['page'];
        $totalpage = $jidnum / $limit;
        $totalpage = ceil($totalpage);
        if($pagenum < 1 || !$pagenum || $pagenum > $totalpage){ $pagenum = 1; }
        $multipage = multipage($jidnum, $limit, $pagenum, $mybb->settings['bburl'].'/misc.php?action=jobliste');
        
        $query = $db->query("SELECT * FROM ".TABLE_PREFIX."jobs
            WHERE accepted != '0'
            AND type LIKE '$type'
            ORDER by name ASC
            LIMIT 0".(($pagenum-1)*$limit).", ".$limit);
        while($job = $db->fetch_array($query)) {
            $users_bit = "";
            $users = $db->query("SELECT * FROM ".TABLE_PREFIX."users
                WHERE jid = '$job[jid]'");
            while($user = $db->fetch_array($users)) {
                $user['username'] = htmlspecialchars_uni($user['username']);
                $user['profilelink'] = build_profile_link($user['username'], $user['uid']);
                $user['job'] = htmlspecialchars_uni($user['job']);
                eval("\$users_bit .= \"".$templates->get("misc_jobliste_bit_users")."\";");
            }
            
            eval("\$job_bit .= \"".$templates->get("misc_jobliste_bit")."\";");
        }
        
        eval("\$page = \"".$templates->get("misc_jobliste")."\";");
        output_page($page);
        
    }
    
    elseif($mybb->input['action'] == "add_job") {
        if($mybb->input['type'] == "auswaehlen")
        {
            error("Es muss eine Branche ausgewählt werden !");
        }
        else if($mybb->input['name'] == "")
        {
            error("Es muss ein Name eingetragen werden !");
        }
        else if($mybb->input['desc'] == "")
        {
            error("Es muss eine Beschreibung eingetragen werden !");
        }else{
            $new_record = array(
                "name" => $db->escape_string($mybb->get_input('name')),
                "desc" => $db->escape_string($mybb->get_input('desc')),
                "type" => $db->escape_string($mybb->get_input('type')),
                "createdby" => (int)$mybb->user['uid'],
                "accepted" => (int) 0
            );
            $db->insert_query("jobs", $new_record);
            redirect("misc.php?action=jobliste", "{$lang->jobliste_added}");
        }
    }
    elseif($mybb->input['action'] == "join_job") {
        $new_record = array(
            "jid" => (int)$mybb->get_input('jid'),
            "job" => $db->escape_string($mybb->get_input('job'))
        );
        $db->update_query("users", $new_record, "uid = '$uid'");
        redirect("misc.php?action=jobliste", "{$lang->jobliste_add_job}");
    }
}

function jobliste_global()
{
    global $mybb, $db, $lang, $new_jobs;
    $lang->load('jobliste');
    
    // Lese neue Jobs aus
    $query = $db->query("SELECT COUNT(*) AS newjobs FROM ".TABLE_PREFIX."jobs WHERE accepted = '0'");
    $newjobs = $db->fetch_field($query, "newjobs");
    if($newjobs && $mybb->usergroup['canmodcp'] == "1") {
        $new_jobs = "<div class=\"red_alert\"><a href=\"modcp.php?action=jobliste\">{$lang->jobliste_new_jobs}</a></div>";
    }
}

$plugins->add_hook('modcp_nav', 'jobliste_modcp_nav');
function jobliste_modcp_nav()
{
    global $db, $mybb, $templates, $theme, $header, $headerinclude, $footer, $modcp_nav, $nav_jobliste;

    eval("\$nav_jobliste= \"".$templates->get ("modcp_jobliste_nav")."\";");
}

function jobliste_modcp() {
    global $mybb, $db, $lang, $templates, $headerinclude, $header, $footer, $modcp_nav, $jobs_bit, $theme;
    
    $mybb->input['action'] = $mybb->get_input('action');
    if($mybb->input['action'] == "jobliste") {

        $query = $db->query("SELECT * FROM ".TABLE_PREFIX."jobs
        WHERE accepted = '0'
        ORDER by name ASC");
        while($job = $db->fetch_array($query)) {
            $datum = my_date("relative", $job['datum']);
            $jid = $job['jid'];
            $job['desc'] = htmlspecialchars_uni($job['desc']);
            $job['createdby'] = htmlspecialchars_uni($job['createdby']);
            $user = get_user($job['createdby']);
            $user['username'] = htmlspecialchars_uni($user['username']);
            $auser = build_profile_link($user['username'], $job['createdby']);
            eval("\$jobs_bit .= \"".$templates->get("modcp_jobliste_bit")."\";");
        }

        eval("\$page = \"".$templates->get("modcp_jobliste")."\";");
        output_page($page);
    }
    
    if($mybb->input['action'] == "do_modcp_jobliste") {
        
        $ajid = $mybb->input['aid'];
        if($ajid) {
            $ajob = $db->fetch_field($db->query("SELECT createdby FROM ".TABLE_PREFIX."jobs WHERE jid = '$ajid'"), "createdby");
            $ownuid = $mybb->user['uid'];
            $subject = "{$lang->jobliste_accepted_subject}";
            $message = "{$lang->jobliste_accepted_message}";
            $fromid = $ownuid;

            require_once MYBB_ROOT . "inc/datahandlers/pm.php";
            $pmhandler = new PMDataHandler();

            $pm = array(
                    "subject" => $subject,
                    "message" => $message,
                    "fromid" => $fromid,
                    "toid" => $ajob
            );

            $pmhandler->set_data($pm);

            // Now let the pm handler do all the hard work.
            if (!$pmhandler->validate_pm()) {
                    $pm_errors = $pmhandler->get_friendly_errors();
                    return $pm_errors;
            }
            else {
                    $pminfo = $pmhandler->insert_pm();
            }

            $new_record = array(
                "accepted" => "1"
            );
            $db->update_query("jobs", $new_record, "jid = '$ajid'");
            redirect("modcp.php?action=jobliste", "{$lang->jobliste_accepted}");
        }

        $rjid = $mybb->get_input('rjid');
        $reason = $mybb->get_input('reason');
        if($rjid) {
            $query = $db->query("SELECT * FROM mybb_jobs WHERE jid = '$rjid'");
            $rjob = $db->fetch_array($query);
            $ownuid = $mybb->user['uid'];
            $subject = $lang->jobliste;
            $message = "Arbeitsplatz/Betrieb hinzufügen wurde Abgelehnt !
            
            Grund: ".$reason."
            
            Job-Name: [b]".$rjob['name']."[/b]
            Arbeitsbranche: [b]".$rjob['type']."[/b]
            Beschreibung: ".$rjob['desc'];
            $fromid = $ownuid;

            require_once MYBB_ROOT . "inc/datahandlers/pm.php";
            $pmhandler = new PMDataHandler();

            $pm = array(
                    "subject" => $subject,
                    "message" => $message,
                    "fromid" => $fromid,
                    "toid" => $rjob['createdby']
            );

            $pmhandler->set_data($pm);

            // Now let the pm handler do all the hard work.
            if (!$pmhandler->validate_pm()) {
                    $pm_errors = $pmhandler->get_friendly_errors();
                    return $pm_errors;
            }
            else{
                    $pminfo = $pmhandler->insert_pm();
            }
            $db->delete_query("jobs", "jid = '$rjid'");
            redirect("modcp.php?action=jobliste", "{$lang->jobliste_deleted}");
        }
    }
}