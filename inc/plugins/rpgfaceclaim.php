<?php

/**
 *        "AVATARLISTE FÜR FOREN-RPGS" VON CHAN (MELANCHOLIA) © 2018
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

#########################
##### TEMPLATELISTS #####
#########################
$templatelist .= "
";

#################
##### HOOKS #####
#################
$plugins->add_hook('misc_start', 'rpgfaceclaim');
$plugins->add_hook("fetch_wol_activity_end", "rpgfaceclaim_online_activity");
$plugins->add_hook("build_friendly_wol_location_end", "rpgfaceclaim_online_location");

#################################
##### PLUGIN INFOS ADMIN-CP #####
#################################
function rpgfaceclaim_info() {
global $mybb, $lang, $db, $plugins_cache;
$lang->load("melancholia_rpgfaceclaim_confic");
	
	$plugininfo = array(
		"name"				=> $lang->rpgfaceclaim_title,
		"description"		=> $lang->rpgfaceclaim_desc,
		"website"			=> "",
		"author"			=> "Chan (melancholia) — {$lang->rpgfaceclaim_contact} sadzoo@outlook.com",
		"authorsite"		=> "",
		"version"			=> "1.0",
		"codename"			=> "rpgfaceclaim",
		"compatibility"		=> "18*"
	);

	if (rpgfaceclaim_is_installed() && is_array($plugins_cache) && is_array($plugins_cache['active']) && $plugins_cache['active']['rpgfaceclaim']) {			
		$result = $db->simple_select('settinggroups', 'gid', "name = 'settings_rpgfaceclaim'");
		$set = $db->fetch_array($result);
		if (!empty($set)) {
            $desc = $plugininfo['description'];
            $plugininfo['description'] = "".$desc."
				<div style=\"float:right;\">
					<img src=\"styles/default/images/icons/custom.png\" alt=\"\" style=\"margin: 0 5px 0 0;\">
					<a href=\"index.php?module=config-settings&amp;action=change&amp;gid=".(int)$set['gid']."\">".$lang->rpgfaceclaim_settings."</a>
					<hr style=\"margin-bottom: 5px;\">
				</div>";
		}
	}	
	
return $plugininfo;
}

###########################
##### INSTALL PLUGIN  #####
###########################
function rpgfaceclaim_install() {
global $db, $mybb, $cache, $lang;
$lang->load("melancholia_rpgfaceclaim_confic");

	// ADD SETTINGS //
	##################
	
		// SETTINGS GROUP
		$rpgfaceclaim_setting_group = array(
			'gid'				=> NULL,
			'name'				=> 'settings_rpgfaceclaim',
			'title'				=> $lang->rpgfaceclaim_title,
			'description'		=> $lang->rpgfaceclaim_settingsgroup_desc,
			'disporder'			=> 1,
			'isdefault'			=> 0,
		); 
		$db->insert_query('settinggroups', $rpgfaceclaim_setting_group);
		$gid = $db->insert_id();
		
		// INDIVIDUEL SETTINGS
		$rpgfaceclaim_setting_array = array(
			'rpgfaceclaim_activate' => array(
				'title'			=> $lang->rpgfaceclaim_activate,
				'description'	=> $lang->rpgfaceclaim_activate_desc,
				'optionscode'	=> 'yesno',
				'value'			=> 0,
				'disporder'		=> 1,
				'gid'           => intval($gid),
			),
			'rpgfaceclaim_fid' => array(
				'title'			=> $lang->rpgfaceclaim_fid,
				'description'	=> $lang->rpgfaceclaim_fid_desc,
				'optionscode'	=> 'text',
				'value'			=> '',
				'disporder'		=> 2,
				'gid'           => intval($gid),
			),
			'rpgfaceclaim_gender' => array(
				'title'			=> $lang->rpgfaceclaim_gender,
				'description'	=> $lang->rpgfaceclaim_gender_desc,
				'optionscode'	=> 'yesno',
				'value'			=> 0,
				'disporder'		=> 3,
				'gid'           => intval($gid),
			),
			'rpgfaceclaim_genderfid' => array(
				'title'			=> $lang->rpgfaceclaim_genderfid,
				'description'	=> $lang->rpgfaceclaim_genderfid_desc,
				'optionscode'	=> 'text',
				'value'			=> '',
				'disporder'		=> 4,
				'gid'           => intval($gid),
			),
		);
		
		foreach($rpgfaceclaim_setting_array as $name => $setting) {
			$setting['name'] = $name;
			$setting['gid'] = $gid;

			$db->insert_query('settings', $setting);
		}
	
rebuild_settings();	
}

function rpgfaceclaim_is_installed()
{
    global $mybb;
	if(isset($mybb->settings['rpgfaceclaim_activate'])) {
        return true;
    }
    return false;
}

###########################
##### ADMIN-CP PEEKER #####
###########################
$plugins->add_hook('admin_config_settings_change', 'rpgfaceclaim_settings_change');
$plugins->add_hook('admin_settings_print_peekers', 'rpgfaceclaim_settings_peek');
function rpgfaceclaim_settings_change()
{
    global $db, $mybb, $rpgfaceclaim_settings_peeker;

    $result = $db->simple_select('settinggroups', 'gid', "name='settings_rpgfaceclaim'", array("limit" => 1));
    $group = $db->fetch_array($result);
    $rpgfaceclaim_settings_peeker = ($mybb->input['gid'] == $group['gid']) && ($mybb->request_method != 'post');
}
function rpgfaceclaim_settings_peek(&$peekers)
{
    global $mybb, $rpgfaceclaim_settings_peeker;

    if ($rpgfaceclaim_settings_peeker) {
       $peekers[] = 'new Peeker($(".setting_rpgfaceclaim_gender"), $("#row_setting_rpgfaceclaim_genderfid"),/1/,true)';
    }
}

############################
##### DEINSTALL PLUGIN #####
############################
function rpgfaceclaim_uninstall() {
global $db;

	// DELETE SETTINGS //
	#####################
	
		$db->delete_query('settings', "name LIKE 'rpgfaceclaim_%'");
		$db->delete_query('settinggroups', "name = 'settings_rpgfaceclaim'");
		
rebuild_settings();
}

###########################
##### ACTIVATE PLUGIN #####
###########################
function rpgfaceclaim_activate() {
global $db, $lang;
require MYBB_ROOT."/inc/adminfunctions_templates.php";

	// ADD TEMPLATE GROUP //
	########################

		$rpgfaceclaim_templategroup = array(
            "prefix"		=> "rpgfaceclaim",
            "title"			=> $db->escape_string($lang->rpgfaceclaim_templategroup),
        );
        $db->insert_query("templategroups", $rpgfaceclaim_templategroup);
	
	// ADD TEMPLATES //
	###################
	
		// LIST
		$rpgfaceclaim_list = '<html>
<head>
<title>{$mybb->settings[\'bbname\']} - {$lang->rpgfaceclaim_breadcrumb}</title>
{$headerinclude}
</head>
<body>
{$header}
<table border="0" cellspacing="{$theme[\'borderwidth\']}" cellpadding="{$theme[\'tablespace\']}" class="tborder">
	<tr>
		<td class="thead">
			<strong>{$lang->rpgfaceclaim_breadcrumb}</strong><br />
		</td>
	</tr>	
	<tr>
		<td class="tcat smalltext"><strong>A B C D</strong></td>
	</tr>
	<tr>
		<td class="{$altbg}">{$faceclaim_ad}</td>
	</tr>
	<tr>
		<td class="tcat smalltext"><strong>E F G H</strong></td>
	</tr>
	<tr>
		<td class="{$altbg}">{$faceclaim_eh}</td>
	</tr>
	<tr>
		<td class="tcat smalltext"><strong>I J K L</strong></td>
	</tr>
	<tr>
		<td class="{$altbg}">{$faceclaim_il}</td>
	</tr>
	<tr>
		<td class="tcat smalltext"><strong>M N O P</strong></td>
	</tr>
	<tr>
		<td class="{$altbg}">{$faceclaim_mp}</td>
	</tr>
	<tr>
		<td class="tcat smalltext"><strong>Q R S T U</strong></td>
	</tr>
	<tr>
		<td class="{$altbg}">{$faceclaim_qu}</td>
	</tr>
	<tr>
		<td class="tcat smalltext"><strong>V W X Y Z</strong></td>
	</tr>
	<tr>
		<td class="{$altbg}">{$faceclaim_vz}</td>
	</tr>
</table>
{$footer}
</body>
</html>';
		
		$insert_array = array(
			'title'			=> 'rpgfaceclaim_list',
			'template'		=> $db->escape_string($rpgfaceclaim_list),
			'sid'			=> -2,
			'version'       => $mybb->version,
			'dateline'		=> time()
		);
		$db->insert_query('templates', $insert_array);
		
		// LIST: GENDER
		$rpgfaceclaim_list_gender = '<html>
<head>
<title>{$mybb->settings[\'bbname\']} - {$lang->rpgfaceclaim_breadcrumb}</title>
{$headerinclude}
</head>
<body>
{$header}
<table border="0" cellspacing="{$theme[\'borderwidth\']}" cellpadding="{$theme[\'tablespace\']}" class="tborder">
	<tr>
		<td class="thead" colspan="2">
			<strong>{$lang->rpgfaceclaim_breadcrumb}</strong><br />
		</td>
	</tr>	
	<tr>
		<td class="tcat smalltext" align="center" width="50%"><strong>{$lang->rpgfaceclaim_male}</strong></td>
		<td class="tcat smalltext" align="center" width="50%"><strong>{$lang->rpgfaceclaim_female}</strong></td>
	</tr>
	<tr>
		<td class="trow_sep smalltext" colspan="2"><strong>A B C D</strong></td>
	</tr>
	<tr>
		<td class="{$altbg}">{$faceclaim_ad_male}</td>
		<td class="{$altbg}">{$faceclaim_ad_female}</td>
	</tr>
	<tr>
		<td class="trow_sep smalltext" colspan="2"><strong>E F G H</strong></td>
	</tr>
	<tr>
		<td class="{$altbg}">{$faceclaim_eh_male}</td>
		<td class="{$altbg}">{$faceclaim_eh_female}</td>
	</tr>
	<tr>
		<td class="trow_sep smalltext" colspan="2"><strong>I J K L</strong></td>
	</tr>
	<tr>
		<td class="{$altbg}">{$faceclaim_il_male}</td>
		<td class="{$altbg}">{$faceclaim_il_female}</td>
	</tr>
	<tr>
		<td class="trow_sep smalltext" colspan="2"><strong>M N O P</strong></td>
	</tr>
	<tr>
		<td class="{$altbg}">{$faceclaim_mp_male}</td>
		<td class="{$altbg}">{$faceclaim_mp_female}</td>
	</tr>
	<tr>
		<td class="trow_sep smalltext" colspan="2"><strong>Q R S T U</strong></td>
	</tr>
	<tr>
		<td class="{$altbg}">{$faceclaim_qu_male}</td>
		<td class="{$altbg}">{$faceclaim_qu_female}</td>
	</tr>
	<tr>
		<td class="trow_sep smalltext" colspan="2"><strong>V W X Y Z</strong></td>
	</tr>
	<tr>
		<td class="{$altbg}">{$faceclaim_vz_male}</td>
		<td class="{$altbg}">{$faceclaim_vz_female}</td>
	</tr>
</table>
{$footer}
</body>
</html>';
		
		$insert_array = array(
			'title'			=> 'rpgfaceclaim_list_gender',
			'template'		=> $db->escape_string($rpgfaceclaim_list_gender),
			'sid'			=> -2,
			'version'       => $mybb->version,
			'dateline'		=> time()
		);
		$db->insert_query('templates', $insert_array);
		
		// LIST: BIT
		$rpgfaceclaim_list_bit = '<div><strong>{$faceclaim[\'face\']}</strong> - {$faceclaim[\'username\']}</div>';
		
		$insert_array = array(
			'title'			=> 'rpgfaceclaim_list_bit',
			'template'		=> $db->escape_string($rpgfaceclaim_list_bit),
			'sid'			=> -2,
			'version'       => $mybb->version,
			'dateline'		=> time()
		);
		$db->insert_query('templates', $insert_array);
	
}

#############################
##### DEACTIVATE PLUGIN #####
#############################
function rpgfaceclaim_deactivate() {
global $db;
require MYBB_ROOT."/inc/adminfunctions_templates.php";

	// DELETE TEMPLATES //
	######################
	
		$db->delete_query("templategroups", "prefix = 'rpgfaceclaim'");	
		$db->delete_query("templates", "title LIKE 'rpgfaceclaim%' AND sid='-2'");
	
}	

###########################
##### ONLINE LOCATION #####
###########################
function rpgfaceclaim_online_activity($user_activity) {
global $parameters;

	$split_loc = explode(".php", $user_activity['location']);
    if($split_loc[0] == $user['location']) {
        $filename = '';
    } else {
        $filename = my_substr($split_loc[0], -my_strpos(strrev($split_loc[0]), "/"));
    }

    switch ($filename) {
        case 'misc':
		if($parameters['action'] == "rpgfaceclaim" && empty($parameters['site'])) {
			$user_activity['activity'] = "rpgfaceclaim";
		}
		break;
    }
	  
return $user_activity;
}

function rpgfaceclaim_online_location($plugin_array) {
global $mybb, $lang;
$lang->load("melancholia_rpgfaceclaim");

	// CONFIC
	$is_ticker_activated = intval($mybb->settings['rpgfaceclaim_ticker']);

	if($plugin_array['user_activity']['activity'] == "rpgfaceclaim") {
		$plugin_array['location_name'] = $lang->rpgfaceclaim_viewing;
	}

return $plugin_array;
}

##########################
##### rpgfaceclaim #######
##########################
function rpgfaceclaim() {
global $db, $mybb, $lang, $templates, $theme, $headerinclude, $header, $footer;
$lang->load("melancholia_rpgfaceclaim");

	// CONFIC
	$is_activated = (int)$mybb->settings['rpgfaceclaim_activate'];
	$get_fid = (int)$mybb->settings['rpgfaceclaim_fid'];
	$is_gender = (int)$mybb->settings['rpgfaceclaim_gender'];
	$get_genderfid = (int)$mybb->settings['rpgfaceclaim_genderfid'];
	$altbg = alt_trow();
	
	// GENDER
	if($is_gender == 1) {
		$sql_select_gender = ", LOWER(uf.fid".$get_genderfid.") AS genderfid";
		$sql_where_gender = "AND uf.fid".$get_genderfid." != ''";
	}
		
	// THE MAGIC //
	###############
	
		// SITES
		if($is_activated == 1 && $mybb->get_input('action') == 'rpgfaceclaim') {
			
			add_breadcrumb($lang->rpgfaceclaim_breadcrumb, "{$mybb->settings['bburl']}/isc.php?action=rpgfaceclaim");
			
			$query = $db->query("
				SELECT u.uid, u.username, u.usergroup, u.displaygroup, uf.fid".$get_fid." AS face".$sql_select_gender."
				FROM ".TABLE_PREFIX."users u
				LEFT JOIN ".TABLE_PREFIX."userfields uf ON (u.uid = uf.ufid)
				WHERE uf.fid".$get_fid." != '' '".$sql_where_gender."'
				ORDER BY uf.fid".$get_fid." ASC
			 ");
			
			if($is_gender == 0) {			
				while($faceclaim = $db->fetch_array($query)) {
					
					$faceclaim['face'] = htmlspecialchars($faceclaim['face']);					
					$faceclaim['username'] = build_profile_link(format_name($faceclaim['username'], $faceclaim['usergroup'], $faceclaim['displaygroup']), $faceclaim['uid']);
						
					if(preg_match("/^(A|a|B|b|C|c|D|d)/", $faceclaim['face'])) {
					eval("\$faceclaim_ad .= \"".$templates->get("rpgfaceclaim_list_bit")."\";");		
					}
					if(preg_match("/^(E|e|F|f|G|g|H|h)/", $faceclaim['face'])) {	
					eval("\$faceclaim_eh .= \"".$templates->get("rpgfaceclaim_list_bit")."\";");	
					}
					if(preg_match("/^(I|i|J|j|K|k|L|l)/", $faceclaim['face'])) {	
					eval("\$faceclaim_il .= \"".$templates->get("rpgfaceclaim_list_bit")."\";");	
					}
					if(preg_match("/^(M|m|N|n|O|o|P|p)/", $faceclaim['face'])) {	
					eval("\$faceclaim_mp .= \"".$templates->get("rpgfaceclaim_list_bit")."\";");	
					}
					if(preg_match("/^(Q|q|R|r|S|s|T|t|U|u)/", $faceclaim['face'])) {		
					eval("\$faceclaim_qu .= \"".$templates->get("rpgfaceclaim_list_bit")."\";");	
					}
					if(preg_match("/^(V|v|W|w|X|x|Y|y|Z|z)/", $faceclaim['face'])) {
					eval("\$faceclaim_vz .= \"".$templates->get("rpgfaceclaim_list_bit")."\";");		
					}

				}
				
			eval("\$page  = \"".$templates->get("rpgfaceclaim_list")."\";");
			output_page($page );		
			}
			else {
				
				while($faceclaim = $db->fetch_array($query)) {
					
					$faceclaim['face'] = htmlspecialchars($faceclaim['face']);					
					$faceclaim['username'] = build_profile_link(format_name($faceclaim['username'], $faceclaim['usergroup'], $faceclaim['displaygroup']), $faceclaim['uid']);
						
					if(preg_match("/^(A|a|B|b|C|c|D|d)/", $faceclaim['face']) && ($faceclaim['genderfid'] == "männlich" || $faceclaim['genderfid'] == "male")) {
					eval("\$faceclaim_ad_male .= \"".$templates->get("rpgfaceclaim_list_bit")."\";");		
					}
					elseif(preg_match("/^(A|a|B|b|C|c|D|d)/", $faceclaim['face']) && ($faceclaim['genderfid'] == "weiblich" || $faceclaim['genderfid'] == "female")) {
					eval("\$faceclaim_ad_female .= \"".$templates->get("rpgfaceclaim_list_bit")."\";");		
					}
					elseif(preg_match("/^(E|e|F|f|G|g|H|h)/", $faceclaim['face']) && ($faceclaim['genderfid'] == "männlich" || $faceclaim['genderfid'] == "male")) {
					eval("\$faceclaim_eh_male .= \"".$templates->get("rpgfaceclaim_list_bit")."\";");	
					}
					elseif(preg_match("/^(E|e|F|f|G|g|H|h)/", $faceclaim['face']) && ($faceclaim['genderfid'] == "weiblich" || $faceclaim['genderfid'] == "female")) {
					eval("\$faceclaim_eh_female .= \"".$templates->get("rpgfaceclaim_list_bit")."\";");	
					}
					elseif(preg_match("/^(I|i|J|j|K|k|L|l)/", $faceclaim['face']) && ($faceclaim['genderfid'] == "männlich" || $faceclaim['genderfid'] == "male")) {
					eval("\$faceclaim_il_male .= \"".$templates->get("rpgfaceclaim_list_bit")."\";");	
					}
					elseif(preg_match("/^(I|i|J|j|K|k|L|l)/", $faceclaim['face']) && ($faceclaim['genderfid'] == "weiblich" || $faceclaim['genderfid'] == "female")) {
					eval("\$faceclaim_il_female .= \"".$templates->get("rpgfaceclaim_list_bit")."\";");	
					}
					elseif(preg_match("/^(M|m|N|n|O|o|P|p)/", $faceclaim['face']) && ($faceclaim['genderfid'] == "männlich" || $faceclaim['genderfid'] == "male")) {
					eval("\$faceclaim_mp_male .= \"".$templates->get("rpgfaceclaim_list_bit")."\";");	
					}
					elseif(preg_match("/^(M|m|N|n|O|o|P|p)/", $faceclaim['face']) && ($faceclaim['genderfid'] == "weiblich" || $faceclaim['genderfid'] == "female")) {
					eval("\$faceclaim_mp_female .= \"".$templates->get("rpgfaceclaim_list_bit")."\";");	
					}
					elseif(preg_match("/^(Q|q|R|r|S|s|T|t|U|u)/", $faceclaim['face']) && ($faceclaim['genderfid'] == "männlich" || $faceclaim['genderfid'] == "male")) {
					eval("\$faceclaim_qu_male .= \"".$templates->get("rpgfaceclaim_list_bit")."\";");	
					}
					elseif(preg_match("/^(Q|q|R|r|S|s|T|t|U|u)/", $faceclaim['face']) && ($faceclaim['genderfid'] == "weiblich" || $faceclaim['genderfid'] == "female")) {
					eval("\$faceclaim_qu_female .= \"".$templates->get("rpgfaceclaim_list_bit")."\";");	
					}
					elseif(preg_match("/^(V|v|W|w|X|x|Y|y|Z|z)/", $faceclaim['face']) && ($faceclaim['genderfid'] == "männlich" || $faceclaim['genderfid'] == "male")) {
					eval("\$faceclaim_vz_male .= \"".$templates->get("rpgfaceclaim_list_bit")."\";");		
					}
					elseif(preg_match("/^(V|v|W|w|X|x|Y|y|Z|z)/", $faceclaim['face']) && ($faceclaim['genderfid'] == "weiblich" || $faceclaim['genderfid'] == "female")) {
					eval("\$faceclaim_vz_female .= \"".$templates->get("rpgfaceclaim_list_bit")."\";");		
					}
					
				}
				
			eval("\$page  = \"".$templates->get("rpgfaceclaim_list_gender")."\";");
			output_page($page );		
			}
			
		}
}
?>