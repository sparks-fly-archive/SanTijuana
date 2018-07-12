<?php
if(!defined("IN_MYBB"))
{
	die("Direct initialization of this file is not allowed.<br /><br />Please make sure IN_MYBB is defined.");
}
$plugins->add_hook("global_intermediate", "checklist_global");
function checklist_info()
{
	return array(
		"name"		=> "Bewerber-Checklist",
		"description"	=> "Zeigt Charakteren in der Bewerbungsphase eine To Do-Liste im Headerbereich an.",
		"website"	=> "http://storming-gates.de",
		"author"	=> "sparks fly",
		"authorsite"	=> "http://storming-gates.de",
		"version"	=> "1.0",
		"compatibility" => "18*"
	);
}
function checklist_install()
{
  global $db, $cache, $mybb;
  $setting_group = array(
    'name' => 'checklist',
    'title' => 'Bewerber-Checklist',
    'description' => 'Einstellungen für das Bewerber Checklist-Plugin',
    'disporder' => 5, // The order your setting group will display
    'isdefault' => 0
  );
  $gid = $db->insert_query("settinggroups", $setting_group);
  $setting_array = array(
    'checklist_group' => array(
    'title' => 'Benutzergruppe für Bewerber',
    'description' => 'Wie lautet die Gruppen-ID der Bewerber?',
    'optionscode' => 'text',
    'value' => '996', // Default
    'disporder' => 1
     ),
    'checklist_fields' => array(
    'title' => 'Benötigte Profilfelder',
    'description' => 'Gib hier die IDs der benötigten Profilfelder ein. Trennen mit <strong>", "</strong>!',
    'optionscode' => 'text',
    'value' => '997, 998', // Default
    'disporder' => 2
     ),
   'checklist_application' => array(
   'title' => 'Steckbrief voraussetzen?',
   'description' => 'Brauchen Bewerber in deinem Forum einen Steckbrief?',
   'optionscode' => 'yesno',
   'value' => 1,
   'disporder' => 3
    ),
    'checklist_forum' => array(
    'title' => 'Unterforum für Bewerbungen',
    'description' => 'Gib die ID deines Unterforums für Bewerbungen an.',
    'optionscode' => 'text',
    'value' => '999', // Default
    'disporder' => 4
     ),
    'checklist_birthday' => array(
    'title' => 'Geburtstag voraussetzen?',
    'description' => 'Müssen Bewerber in deinem Forum ihr Geburtstag im Profil angeben (mit MyBB-Standardfunktion)?',
    'optionscode' => 'yesno',
    'value' => 1,
    'disporder' => 5
    ),
  );
  foreach($setting_array as $name => $setting)
  {
    $setting['name'] = $name;
    $setting['gid'] = $gid;
     $db->insert_query('settings', $setting);
  }
  $insert_array = array(
    'title'		=> 'checklist',
    'template'	=> $db->escape_string('<center>
	<table border="0" cellspacing="{$theme[\'borderwidth\']}" cellpadding="{$theme[\'tablespace\']}" class="tborder" width="70%">
	<tr><td class="thead" colspan="2">{$lang->checklist}</td></tr>
	{$checklist_check}
</table>
</center>
<br />'),
    'sid'		=> '-1',
    'version'	=> '',
    'dateline'	=> TIME_NOW
  );
  $db->insert_query("templates", $insert_array);
  $insert_array = array(
    'title'		=> 'checklist_application_checked',
    'template'	=> $db->escape_string('<tr>
	<td class="trow1" valign="middle" align="center" width="45px"><img src="images/checklist_checked.png" width="25px" /></td>
	<td class="trow1" valign="middle">{$lang->checklist_application_checked} {$lang->checklist_sep} <a href="showthread.php?tid={$application[\'tid\']}" target="blank">{$lang->checklist_application}</a></td>
</tr>'),
    'sid'		=> '-1',
    'version'	=> '',
    'dateline'	=> TIME_NOW
  );
  $db->insert_query("templates", $insert_array);
  $insert_array = array(
    'title'		=> 'checklist_application_unchecked',
    'template'	=> $db->escape_string('<tr>
  <td class="trow1" valign="middle" align="center" width="45px"><img src="images/checklist_unchecked.png" width="25px" /></td>
  <td class="trow1" valign="middle">{$lang->checklist_application_unchecked} {$lang->checklist_sep} <a href="forumdisplay.php?fid={$mybb->settings[\'checklist_forum\']}" target="blank">{$lang->checklist_forum}</a></td>
</tr>'),
    'sid'		=> '-1',
    'version'	=> '',
    'dateline'	=> TIME_NOW
  );
  $db->insert_query("templates", $insert_array);
  $insert_array = array(
    'title'		=> 'checklist_avatar_checked',
    'template'	=> $db->escape_string('<tr>
  <td class="trow1" valign="middle" align="center" width="45px"><img src="images/checklist_checked.png" width="25px" /></td>
  <td class="trow1" valign="middle">{$lang->checklist_avatar_checked}</td>
</tr>'),
    'sid'		=> '-1',
    'version'	=> '',
    'dateline'	=> TIME_NOW
  );
  $db->insert_query("templates", $insert_array);
  $insert_array = array(
    'title'		=> 'checklist_avatar_unchecked',
    'template'	=> $db->escape_string('<tr>
  <td class="trow1" valign="middle" align="center" width="45px"><img src="images/checklist_unchecked.png" width="25px" /></td>
  <td class="trow1" valign="middle">{$lang->checklist_avatar_unchecked}</td>
</tr>'),
    'sid'		=> '-1',
    'version'	=> '',
    'dateline'	=> TIME_NOW
  );
  $db->insert_query("templates", $insert_array);
  $insert_array = array(
    'title'		=> 'checklist_birthday_checked',
    'template'	=> $db->escape_string('<tr>
  <td class="trow1" valign="middle" align="center" width="45px"><img src="images/checklist_checked.png" width="25px" /></td>
  <td class="trow1" valign="middle">{$lang->checklist_birthday_checked}</td>
</tr>'),
    'sid'		=> '-1',
    'version'	=> '',
    'dateline'	=> TIME_NOW
  );
  $db->insert_query("templates", $insert_array);
  $insert_array = array(
    'title'		=> 'checklist_birthday_unchecked',
    'template'	=> $db->escape_string('<tr>
  <td class="trow1" valign="middle" align="center" width="45px"><img src="images/checklist_unchecked.png" width="25px" /></td>
  <td class="trow1" valign="middle">{$lang->checklist_birthday_unchecked}</td>
</tr>'),
    'sid'		=> '-1',
    'version'	=> '',
    'dateline'	=> TIME_NOW
  );
  $db->insert_query("templates", $insert_array);
  $insert_array = array(
    'title'		=> 'checklist_field_checked',
    'template'	=> $db->escape_string('<tr>
	<td class="trow1" valign="middle" align="center" width="45px"><img src="images/checklist_checked.png" width="25px" /></td>
	<td class="trow1" valign="middle"><strong>{$field[\'name\']}</strong> {$lang->checklist_sep} {$lang->checklist_field_checked}</td>
</tr>'),
    'sid'		=> '-1',
    'version'	=> '',
    'dateline'	=> TIME_NOW
  );
  $db->insert_query("templates", $insert_array);
  $insert_array = array(
    'title'		=> 'checklist_field_unchecked',
    'template'	=> $db->escape_string('<tr>
	<td class="trow1" valign="middle" align="center" width="45px"><img src="images/checklist_unchecked.png" width="25px" /></td>
	<td class="trow1" valign="middle"><strong>{$field[\'name\']}</strong> {$lang->checklist_sep} {$lang->checklist_field_unchecked}</td>
</tr>'),
    'sid'		=> '-1',
    'version'	=> '',
    'dateline'	=> TIME_NOW
  );
  $db->insert_query("templates", $insert_array);
  rebuild_settings();
}
function checklist_is_installed()
{
  global $db, $mybb;
	if(isset($mybb->settings['checklist_birthday'])) {
			return true;
	}
	return false;
}
function checklist_uninstall()
{
  global $db;
  $db->delete_query('settings', "name IN('checklist_group', 'checklist_fields', 'checklist_application', 'checklist_forum', 'checklist_birthday')");
  $db->delete_query('settinggroups', "name = 'checklist'");
    $db->delete_query("templates", "title IN('checklist', 'checklist_application_checked', 'checklist_application_unchecked', 'checklist_avatar_checked', 'checklist_avatar_unchecked', 'checklist_birthday_checked', 'checklist_birthday_unchecked', 'checklist_field_checked', 'checklist_field_unchecked')");
  rebuild_settings();
}
function checklist_activate()
{
  global $db, $mybb;
  include MYBB_ROOT."/inc/adminfunctions_templates.php";
  find_replace_templatesets("header", "#".preg_quote('{$awaitingusers}')."#i", '{$awaitingusers} {$header_checklist}');
}
function checklist_deactivate()
{
  global $db, $mybb;
  include MYBB_ROOT."/inc/adminfunctions_templates.php";
  find_replace_templatesets("header", "#".preg_quote('{$header_checklist}')."#i", '', 0);
}
function checklist_global()
{
  global $db, $mybb, $lang, $templates, $field, $checklist_check, $header_checklist;
  $lang->load('checklist');
  $uid = $mybb->user['uid'];
  $checklist_check = "";
  if($mybb->usergroup['gid'] == $mybb->settings['checklist_group']) {
    if(!empty($mybb->user['avatar'])) {
       eval("\$checklist_check .= \"".$templates->get("checklist_avatar_checked")."\";");
     }
     else {
       eval("\$checklist_check .= \"".$templates->get("checklist_avatar_unchecked")."\";");
     }
    $fields = explode(", ", $db->escape_string($mybb->settings['checklist_fields']));
    foreach($fields as $field) {
      $query = $db->simple_select("profilefields", "fid, name, length", "fid = '".$field."'");
      $field = $db->fetch_array($query);
      $fid = "fid".$field['fid'];
      if(!empty($mybb->user[$fid]) || strlen($mybb->user[$fid]) > $field['length']) {
        eval("\$checklist_check .= \"".$templates->get("checklist_field_checked")."\";");
      }
      else {
        eval("\$checklist_check .= \"".$templates->get("checklist_field_unchecked")."\";");
      }
    }
    if($mybb->settings['checklist_birthday'] == "1") {
      if(!empty($mybb->user['birthday'])) {
        eval("\$checklist_check .= \"".$templates->get("checklist_birthday_checked")."\";");
      }
      else {
        eval("\$checklist_check .= \"".$templates->get("checklist_birthday_unchecked")."\";");
      }
    }
    if($mybb->settings['checklist_application'] == "1") {
      $query = $db->simple_select("threads", "*", "uid = '".$uid."' AND fid = '".$db->escape_string($mybb->settings['checklist_forum'])."'");
      $application = $db->fetch_array($query);
      if(!empty($application)) {
        eval("\$checklist_check .= \"".$templates->get("checklist_application_checked")."\";");
      }
      else {
        eval("\$checklist_check .= \"".$templates->get("checklist_application_unchecked")."\";");
      }
    }
    eval("\$header_checklist .= \"".$templates->get("checklist")."\";");
  }
}
?>
