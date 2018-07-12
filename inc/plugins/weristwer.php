<?php
/**
 *        "WER IST WER?" VON CHAN (MELANCHOLIA) © 2016
 *
 *        This program is free software: you can redistribute it and/or modify
 *        it under the terms of the GNU General Public License as published by
 *        the Free Software Foundation, either version 3 of the License, or
 *        (at your option) any later version.
 *
 *        This program is distributed in the hope that it will be useful,
 *        but WITHOUT ANY WARRANTY; without even the implied warranty of
 *        MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *        GNU General Public License for more details.
 *
 *        You should have received a copy of the GNU General Public License
 *        along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

if(!defined("IN_MYBB")) {
    die("You Cannot Access This File Directly. Please Make Sure IN_MYBB Is Defined.");
} 

$templatelist .= "header_menu_weristwer,weristwer_accounts,weristwer_avatar,weristwer_accounts_zweit,weristwer_accounts_zweit_bit";
$templatelist .= ",multipage,multipage_end,multipage_jump_page,multipage_nextpage,multipage_page,multipage_page_current,multipage_page_link_current,multipage_prevpage,multipage_start";

$plugins->add_hook('misc_start', 'show_weristwer');
$plugins->add_hook('global_intermediate', 'add_menu_weristwer');
$plugins->add_hook("fetch_wol_activity_end", "weristwer_online_activity");
$plugins->add_hook("build_friendly_wol_location_end", "weristwer_online_location");


function weristwer_info() {
global $mybb, $lang, $db, $plugins_cache;
$lang->load("weristwer");

	$plugininfo = array(
		"name"				=> $lang->weristwer_titel,
		"description"		=> $lang->weristwer_desc,
		"website"			=> "",
		"author"			=> "Chan (melancholia) — {$lang->weristwer_kontakt} sadzoo@outlook.com",
		"authorsite"		=> "",
		"version"			=> "1.1",
		"codename"			=> "weristwer",
		"compatibility"		=> "18*"
	);	
	
	if (weristwer_is_installed() && is_array($plugins_cache) && is_array($plugins_cache['active']) && $plugins_cache['active']['weristwer'])
	{
		$result = $db->simple_select('settinggroups', 'gid', "name = 'weristwersettings'");
		$set = $db->fetch_array($result);
		if (!empty($set))
		{
            $desc = $plugininfo['description'];
            $plugininfo['description'] = "".$desc."<div style=\"float:right;\"><img src=\"styles/default/images/icons/custom.png\" alt=\"\" style=\"margin-left: 10px;\" /><a href=\"index.php?module=config-settings&amp;action=change&amp;gid=".(int)$set['gid']."\" style=\"margin: 10px;\">".$lang->weristwer_settingsgroup_name."</a><hr style=\"margin-bottom: 5px;\"></div>";
		}
	}
	
return $plugininfo;
} 

function weristwer_install() {
global $db, $mybb, $lang;
$lang->load("weristwer");
	
	// SETTINGS GROUP
	$weristwer_setting_group = array(
		'gid'				=> 'NULL',
		'name'				=> 'weristwersettings',
		'title'				=> $lang->weristwer_settingsgroup_name,
		'description'		=> $lang->weristwer_settingsgroup_desc,
		'disporder'			=> "1",
		'isdefault'			=> "0",
	); 
	$db->insert_query('settinggroups', $weristwer_setting_group);
	$gid = $db->insert_id(); 
	
	// SETTINGS
    $weristwer_setting_array = array(
        'weristwer_aktivieren' => array(
            'title'			=> $lang->weristwer_setting_aktivieren,
            'description'	=> $lang->weristwer_setting_aktivieren_desc,
            'optionscode'	=> 'yesno',
            'value'			=> 0,
            'disporder'		=> 1,
			'gid'           => intval($gid),
        ),
        'weristwer_aktivieren_gast' => array(
            'title'			=> $lang->weristwer_setting_aktivieren_gast,
            'description'	=> $lang->weristwer_setting_aktivieren_gast_desc,
            'optionscode'	=> 'yesno',
            'value'			=> 0,
            'disporder'		=> 2,
			'gid'           => intval($gid),
        ),
		'weristwer_usergruppe' => array(
            'title'			=> $lang->weristwer_setting_usergruppe,
            'description'	=> $lang->weristwer_setting_usergruppe_desc,
            'optionscode'	=> 'groupselect',
            'value'			=> '', // Default
            'disporder'		=> 3,
			'gid'           => intval($gid),
        ),
        'weristwer_fid_spieler' => array(
            'title'			=> $lang->weristwer_setting_fid_spieler,
            'description'	=> $lang->weristwer_setting_fid_spieler_desc,
            'optionscode'	=> 'numeric',
            'value'			=> '5', // Default
            'disporder'		=> 4,
			'gid'           => intval($gid),
        ),
        'weristwer_avatarbreite_haupt' => array(
            'title'			=> $lang->weristwer_setting_avatarbreite_haupt,
            'description'	=> $lang->weristwer_setting_avatarbreite_haupt_desc,
            'optionscode'	=> 'numeric',
            'value'			=> '85', // Default
            'disporder'		=> 5,
			'gid'           => intval($gid),
        ),
        'weristwer_avatarbreite_zweit' => array(
            'title'			=> $lang->weristwer_setting_avatarbreite_zweit,
            'description'	=> $lang->weristwer_setting_avatarbreite_zweit_desc,
            'optionscode'	=> 'numeric',
            'value'			=> '35', // Default
            'disporder'		=> 6,
			'gid'           => intval($gid),
        ),
	); 
	
	// INSERT SETTINGS
	foreach($weristwer_setting_array as $name => $setting) {
		$setting['name'] = $name;
		$setting['gid'] = $gid;

		$db->insert_query('settings', $setting);
	}

rebuild_settings();
	
	// INSERT TEMPLATES
	$template_weristwer_menu = '<li>
	<a href="{$mybb->settings[\'bburl\']}/misc.php?action=weristwer" class="usercp">{$lang->nav_weristwer}</a>
</li>';
		
    $insert_array = array(
        'title'			=> 'header_menu_weristwer',
        'template'		=> $db->escape_string($template_weristwer_menu),
        'sid'			=> '-1',
        'version'		=> '',
        'dateline'		=> time()
    );
	$db->insert_query('templates', $insert_array);

	$template_weristwer = '<html>
<head>
<title>{$mybb->settings[\'bbname\']} - {$lang->weristwer_titel}</title>
{$headerinclude}
</head>
<body>
{$header}
<table border="0" cellspacing="{$theme[\'borderwidth\']}" cellpadding="{$theme[\'tablespace\']}" class="tborder" width="100%">
	<tr>
		<td class="thead"><strong>{$lang->weristwer_titel}</strong></td>
	</tr>
	<tr>
		<td class="{$altbg}">
			<div class="weristwer_container">
				{$weristwer_accounts}
			</div>
		</td>
	</tr>
</table>
{$multipage}
{$footer}
</body>
</html>';

    $insert_array = array(
        'title' => 'weristwer',
        'template' => $db->escape_string($template_weristwer),
        'sid' => '-1',
        'version' => '',
        'dateline' => time()
    );
    $db->insert_query('templates', $insert_array);
	
	$template_weristwer_accounts = '<div class="weristwer_accounts">
<table border="0" cellspacing="{$theme[\'borderwidth\']}" cellpadding="{$theme[\'tablespace\']}" class="tborder" width="100%">
	<tr>
		<td class="tcat" colspan="2" valign="top"><span class="smalltext"><strong>{$spieler_name}</strong></span></td>
	</tr>
	<tr>
		<td class="{$altbg}" align="center" valign="top" {$width}><span class="smalltext">{$avatar_haupt}</span></td>
		<td class="{$altbg}" align="center" valign="top">
			<table border="0" cellspacing="{$theme[\'borderwidth\']}" cellpadding="{$theme[\'tablespace\']}" class="tborder" width="100%">
				<tr>
					<td class="trow_sep" align="center" colspan="2"><span class="smalltext"><strong>{$profillink_haupt}</strong></span></td>
				</tr>
				<tr>
					<td class="{$altbg}" width="35%"><span class="smalltext"><strong>{$lang->weristwer_regdatum}</strong></span></td>
					<td class="{$altbg}" width="65%"><span class="smalltext">{$regdatum_haupt}</span></td>
				</tr>
				<tr>
					<td class="{$altbg}"><span class="smalltext"><strong>{$lang->weristwer_letzterbesuch}</strong></span></td>
					<td class="{$altbg}"><span class="smalltext">{$letzterbesuch_haupt}</span></td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td class="trow_sep" colspan="2" align="center"><span class="smalltext"><strong>{$lang->weristwer_accounts_zweit}</strong></span></td>
	</tr>
	<tr>
		<td class="{$altbg}" colspan="2" align="center" valign="top">
			<div class="weristwer_zweit">
				<table border="0" cellspacing="{$theme[\'borderwidth\']}" cellpadding="{$theme[\'tablespace\']}" class="tborder" width="100%">
					{$weristwer_accounts_zweit}
				</table>
			</div>
		</td>
	</tr>
</table>
</div>';

    $insert_array = array(
        'title' => 'weristwer_accounts',
        'template' => $db->escape_string($template_weristwer_accounts),
        'sid' => '-1',
        'version' => '',
        'dateline' => time()
    );
    $db->insert_query('templates', $insert_array);
	
	$template_weristwer_avatar = '<img src="{$avatar}" alt="" {$width} />';

    $insert_array = array(
        'title' => 'weristwer_avatar',
        'template' => $db->escape_string($template_weristwer_avatar),
        'sid' => '-1',
        'version' => '',
        'dateline' => time()
    );
    $db->insert_query('templates', $insert_array);
	
	$template_weristwer_accounts_zweit = '{$weristwer_accounts_zweit_bit}';

    $insert_array = array(
        'title' => 'weristwer_accounts_zweit',
        'template' => $db->escape_string($template_weristwer_accounts_zweit),
        'sid' => '-1',
        'version' => '',
        'dateline' => time()
    );
    $db->insert_query('templates', $insert_array);
	
	$template_weristwer_accounts_zweit_bit = '<tr>
	<td class="{$altbg}" align="center" {$width}>{$avatar_zweit}</td>
	<td class="{$altbg}">
		{$profillink_zweit}<br />
		<span class="smalltext"><strong>{$lang->weristwer_letzterbesuch}</strong> {$letzterbesuch_zweit}</span>
	</td>
</tr>';

    $insert_array = array(
        'title' => 'weristwer_accounts_zweit_bit',
        'template' => $db->escape_string($template_weristwer_accounts_zweit_bit),
        'sid' => '-1',
        'version' => '',
        'dateline' => time()
    );
    $db->insert_query('templates', $insert_array);
	
	// CSS	
	$css = array(
		'name' => 'weristwer.css',
		'tid' => 1,
		'attachedto' => 'misc.php',
		"stylesheet" =>	'.weristwer_container {
	display: flex;
	display: -webkit-flex;
	-moz-display: flex;
	flex-wrap: wrap;
	-moz-flex-wrap: wrap;
	-webkit-flex-wrap: wrap;
	justify-content: flex-start;
	-moz-justify-content: flex-start;
	-webkit-justify-content: flex-start;
}
.weristwer_accounts {
	width: 33%;
	margin: 0 2px 5px 2px;
}
.weristwer_zweit {
	height: 97px;
	overflow: auto;
}
.weristwer_accounts_zweit_kA {
	height: 82px;	
}',
		'cachefile' => $db->escape_string(str_replace('/', '', weristwer.css)),
		'lastmodified' => time()
	);

	require_once MYBB_ADMIN_DIR."inc/functions_themes.php";

	$sid = $db->insert_query("themestylesheets", $css);
	$db->update_query("themestylesheets", array("cachefile" => "css.php?stylesheet=".$sid), "sid = '".$sid."'", 1);

	$tids = $db->simple_select("themes", "tid");
	while($theme = $db->fetch_array($tids)) {
		update_theme_stylesheet_list($theme['tid']);
	}
	
}

function weristwer_is_installed() {
global $mybb;

    if(isset($mybb->settings['weristwer_aktivieren'])) {
        return true;
    }
    return false;
}

function weristwer_uninstall() {
global $db;
    
    $db->delete_query('settings', "name IN ('weristwer_aktivieren','weristwer_aktivieren_gast','weristwer_usergruppe','weristwer_fid_spieler','weristwer_avatarbreite_haupt','weristwer_avatarbreite_zweit')");
    $db->delete_query('settinggroups', "name = 'weristwersettings'");
	
	$db->query("DELETE FROM ".TABLE_PREFIX."templates WHERE title='header_menu_weristwer'");
	$db->query("DELETE FROM ".TABLE_PREFIX."templates WHERE title='weristwer'");
	$db->query("DELETE FROM ".TABLE_PREFIX."templates WHERE title='weristwer_accounts'");
	$db->query("DELETE FROM ".TABLE_PREFIX."templates WHERE title='weristwer_accounts_zweit'");
	$db->query("DELETE FROM ".TABLE_PREFIX."templates WHERE title='weristwer_accounts_zweit_bit'");
	$db->query("DELETE FROM ".TABLE_PREFIX."templates WHERE title='weristwer_avatar'");
	
	require_once MYBB_ADMIN_DIR."inc/functions_themes.php";
	$db->delete_query("themestylesheets", "name = 'weristwer.css'");
	$query = $db->simple_select("themes", "tid");
	while($theme = $db->fetch_array($query)) {
		update_theme_stylesheet_list($theme['tid']);
	}

rebuild_settings();
}

function weristwer_activate() {
global $db;
require MYBB_ROOT."/inc/adminfunctions_templates.php";

	 find_replace_templatesets('header', '#{\$menu_memberlist}#', "{\$menu_memberlist}\n						{\$menu_weristwer}");

} 

function weristwer_deactivate() {
global $db;
require MYBB_ROOT."/inc/adminfunctions_templates.php";

	 find_replace_templatesets('header', '#{\$menu_weristwer}(\n?)#', '', 0);
	
rebuild_settings();
}

function add_menu_weristwer() {
global $db, $mybb, $lang, $templates, $menu_weristwer;
$lang->load("weristwer");

	// EINSTELLUNGEN
	$aktivieren_plugin = intval($mybb->settings['weristwer_aktivieren']);
	$aktivieren_gast = intval($mybb->settings['weristwer_aktivieren_gast']);
	
	if($aktivieren_plugin == 1 || ($aktivieren_gast == 1 && $mybb->user['uid'] == 0)) {
		eval("\$menu_weristwer = \"".$templates->get("header_menu_weristwer")."\";");
	}
	
}

function weristwer_online_activity($user_activity) {
global $user;
    
    if(my_strpos($user['location'], "misc.php?action=weristwer") !== false) {
		$user_activity['activity'] = "weristwer";
    }
    
return $user_activity;
}

function weristwer_online_location($plugin_array) {
global $mybb, $theme, $lang;
$lang->load("weristwer");

	if($plugin_array['user_activity']['activity'] == "weristwer") {
		$plugin_array['location_name'] = $lang->viewing_weristwer;
	}

return $plugin_array;
}

function show_weristwer() { 
global $db, $mybb, $templates, $theme, $headerinclude, $header, $lang, $footer, $weristwer, $groupcache;
$lang->load("weristwer");

	// EINSTELLUNGEN
	$aktivieren_plugin = intval($mybb->settings['weristwer_aktivieren']);
	
	$usergruppeID = explode(',', $mybb->settings['weristwer_usergruppe']);
		if(is_array($usergruppeID))
		{
			foreach($usergruppeID as &$guid)
			{
				$guid = (int)$guid;
			}
			unset($guid);

			$usergruppeID = implode(',', $usergruppeID);
		}
	
	if($aktivieren_plugin != 1 && $mybb->get_input('action') == 'weristwer') {
		error($lang->weristwer_inaktiv);
	}
	elseif($aktivieren_plugin == 1 && $usergruppeID != '-1' && $mybb->get_input('action') == 'weristwer') {

		// NAVIGATION
		add_breadcrumb($lang->weristwer_titel, "misc.php?action=weristwer");
		
		// EINSTELLUNGEN
		$aktivieren_gast = intval($mybb->settings['weristwer_aktivieren_gast']);
		$usergruppeID = explode(',', $mybb->settings['weristwer_usergruppe']);
		if(is_array($usergruppeID)) {
			foreach($usergruppeID as &$guid) {
				$guid = (int)$guid;
			}
			unset($guid);

			$usergruppeID = implode(',', $usergruppeID);
		}

		$spielerID = intval($mybb->settings['weristwer_fid_spieler']);
		
		// FEHLER WENN SPIELERNAME-FID NICHT VORHANDEN
		$query = $db->simple_select("profilefields", "*", "fid = '".$spielerID."'");
        $profile_field = $db->fetch_array($query);
        if(isset($profile_field) != true) {
            error($lang->weristwer_error_spielernamefid);
        }
		
		$avatarbreite_haupt = intval($mybb->settings['weristwer_avatarbreite_haupt']);
		$avatarbreite_zweit = intval($mybb->settings['weristwer_avatarbreite_zweit']);
		
		// KEIN ZUTRITT FÜR GÄSTE?
		if ($aktivieren_gast != 1 && $mybb->user['uid'] == 0) {
			error_no_permission();
		}
		
		// MULTIPAGE
		$query = $db->simple_select("users u", "COUNT(*) AS numusers", "u.as_uid = '0' AND u.usergroup NOT IN ($usergruppeID)");
		$usercount = $db->fetch_field($query, "numusers");
		$perpage = $mybb->settings['membersperpage'];
		$page = intval($mybb->input['page']);
		if($page) {
			$start = ($page-1) *$perpage;
		}
		else {
			$start = 0;
			$page = 1;
		}
		$end = $start + $perpage;
		$lower = $start+1;
		$upper = $end;
		if($upper > $usercount) {
			$upper = $usercount;
		}
		$multipage = multipage($usercount, $perpage, $page, $_SERVER['PHP_SELF']."?action=weristwer");
		
		// HAUPTACCOUNTS
		$spieler = $db->query("
			SELECT *
			FROM ".TABLE_PREFIX."users u
			LEFT JOIN ".TABLE_PREFIX."userfields uf ON (uf.ufid = u.uid)
			WHERE u.as_uid = '0' AND u.usergroup NOT IN ($usergruppeID) 
			ORDER BY uf.fid$spielerID ASC, u.username ASC
			LIMIT $start, $perpage
		");

		while($haupt = $db->fetch_array($spieler)) {
			$altbg = alt_trow();
			$hauptID = (int)$haupt['uid'];
			$username = htmlspecialchars_uni($haupt['username']);
			$spieler_name = $haupt['fid'.intval($mybb->settings['weristwer_fid_spieler']).''];			
			$spieler_name2 = $haupt['fid'.intval($mybb->settings['weristwer_fid_spieler']).''];
			if(empty($spieler_name) || empty($spieler_name2)) {
				$spieler_name = "{$lang->weristwer_spieler_kA}";
				$spieler_name2 = "{$lang->weristwer_spieler_kA_zweit}";
			}
			$width = "width=\"{$avatarbreite_haupt}\"";
			$avatar = htmlspecialchars_uni($haupt['avatar']);
			if(empty($haupt['avatar']) || $mybb->user['uid'] == 0) {
				$avatar = "{$theme['imgdir']}/default_avatar.png";
			}
			$avatar_haupt = eval($templates->render('weristwer_avatar'));
			$profillink_haupt = build_profile_link(format_name($username, $haupt['usergroup'], $haupt['displaygroup']), (int)$haupt['uid']);
			$regdatum_haupt = my_date('relative', $haupt['regdate']);
			if($haupt['lastactive']) {
				$letzterbesuch_haupt = my_date('relative', $haupt['lastactive']);
				$letzterbesuch_haupt_sep = $lang->comma;
				$letzterbesuch_haupt_zeit = my_date('relative', $haupt['lastactive']);
			}
			else {
				$letzterbesuch_haupt = $lang->lastvisit_never;
				$letzterbesuch_haupt_sep = '';
				$letzterbesuch_haupt_zeit = '';
			}			
			
			// ANGEHÄNGTE ACCOUNTS
			$zweitchare = $db->query("
				SELECT *
				FROM ".TABLE_PREFIX."users u
				LEFT JOIN ".TABLE_PREFIX."userfields uf ON (uf.ufid = u.uid)
				WHERE u.as_uid = '$hauptID' AND u.usergroup NOT IN ($usergruppeID) 
				ORDER BY u.username ASC
			");
			
			$weristwer_accounts_zweit_bit = "";
			
			while($zweit = $db->fetch_array($zweitchare)) {
				$altbg = alt_trow();
				$width = "width=\"{$avatarbreite_zweit}\"";
				$avatar = htmlspecialchars_uni($zweit['avatar']);
				if(empty($zweit['avatar']) || $mybb->user['uid'] == 0) {
					$avatar = "{$theme['imgdir']}/default_avatar.png";
				}
				$avatar_zweit = eval($templates->render('weristwer_avatar'));
				$username = htmlspecialchars_uni($zweit['username']);
				$profillink_zweit = build_profile_link(format_name($username, $zweit['usergroup'], $zweit['displaygroup']), (int)$zweit['uid']);				
				if($zweit['lastactive']) {
				$letzterbesuch_zweit = my_date('relative', $zweit['lastactive']);
				$letzterbesuch_zweit_sep = $lang->comma;
				$letzterbesuch_zweit_zeit = my_date('relative', $zweit['lastactive']);
				}
				else {
					$letzterbesuch_zweit = $lang->lastvisit_never;
					$letzterbesuch_zweit_sep = '';
					$letzterbesuch_zweit_zeit = '';
				}	
				eval("\$weristwer_accounts_zweit_bit .= \"".$templates->get("weristwer_accounts_zweit_bit")."\";");
			}
			
			if(!empty($weristwer_accounts_zweit_bit)) {
				eval("\$weristwer_accounts_zweit = \"".$templates->get("weristwer_accounts_zweit")."\";");
			}
			else $weristwer_accounts_zweit = "<tr><td class=\"{$altbg} weristwer_accounts_zweit_kA\" align=\"center\" valign=\"top\">{$spieler_name2} {$lang->weristwer_accounts_zweit_kA}</td></tr>";
			
		eval("\$weristwer_accounts .= \"".$templates->get("weristwer_accounts")."\";");		
		}
		
		eval("\$page  = \"".$templates->get("weristwer")."\";");
		output_page($page );
	
	}
	// ALLE GRUPPEN AUSGESCHLOSSEN
	elseif ($usergruppeID == -1) {
		error($lang->weristwer_keineusergruppen);
	}
}

?>