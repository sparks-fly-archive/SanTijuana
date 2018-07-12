<?php

if(!defined("IN_MYBB"))
{
	die("Direct initialization of this file is not allowed.");
}

$plugins->add_hook("index_start", "quests_index");

function quests_info()
{
	return array(
		"name"			=> "Quest-System",
		"description"	=> "Erweitert MyBB um ein Foren RPG-Questsystem, das es Usern erlaubt, das Inplay mit vom Team versandten oder selbst entworfenen Aufgaben aufzupeppen.",
		"website"		=> "http://www.storming-gates.de",
		"author"		=> "sparks fly",
		"authorsite"	=> "http://www.storming-gates.de",
		"version"		=> "1.0",
		"compatibility" => "*"
	);
}

function quests_install()
{
	global $db, $mybb;
	require_once MYBB_ROOT."/inc/plugins/quests/quests_templates.php";

	// Tabellen erstellen
	$db->query("CREATE TABLE ".TABLE_PREFIX."miniquests ( `qid` INT(11) NOT NULL AUTO_INCREMENT , `taken` INT(11) NOT NULL , `done` INT(11) NOT NULL , `quest` VARCHAR(1200) NOT NULL , `addedby` INT(11) NOT NULL , `doneby` INT(11) NOT NULL , `claimedby` INT(11) NOT NULL , `accepted` TINYINT(1) NOT NULL , `time` VARCHAR(20) NOT NULL , `pid` VARCHAR(11) NOT NULL , `timedone` VARCHAR(20) NOT NULL , PRIMARY KEY (`qid`)) ENGINE = InnoDB CHARSET=utf8 COLLATE utf8_general_ci;");
	$db->query("CREATE TABLE ".TABLE_PREFIX."quests ( `qid` INT(11) NOT NULL AUTO_INCREMENT , `sentby` INT(11) NOT NULL , `name` VARCHAR(155) NOT NULL , `quest` VARCHAR(2000) NOT NULL , `extra` VARCHAR(500) NOT NULL , `maxcount` SMALLINT(2) NOT NULL , `uids` VARCHAR(155) NOT NULL , `tid` INT(11) NOT NULL , `done` TINYINT(1) NOT NULL , `accepted` TINYINT(1) NOT NULL , PRIMARY KEY (`qid`)) ENGINE = InnoDB CHARSET=utf8 COLLATE utf8_general_ci;");

	// Templates erstellen
	quests_templates();
}

function quests_is_installed()
{
  global $db;
  if($db->table_exists("miniquests"))
  {
      return true;
  }
  return false;
}

function quests_uninstall()
{
  global $db;

  // Tabellen entfernen
	if($db->table_exists("miniquests"))
  {
    $db->drop_table("miniquests");
  }
  if($db->table_exists("quests"))
  {
    $db->drop_table("quests");
  }

	$db->delete_query('templates', "title='quests' OR title LIKE '%quests%'");
	$db->delete_query('templategroups', "prefix='quests'");
}

function quests_activate()
{
  global $mybb;
	// Variablen einfügen
  include MYBB_ROOT."/inc/adminfunctions_templates.php";
	find_replace_templatesets("index", "#".preg_quote('{$header}')."#i", '{$header}{$index_accept_quests}');
}

function quests_deactivate()
{
  global $mybb;
  // Variablen entfernen
  include MYBB_ROOT."/inc/adminfunctions_templates.php";
  find_replace_templatesets("index", "#".preg_quote('{$index_accept_quests}')."#i", '', 0);
}

function quests_index()
{
	global $mybb, $db, $lang, $templates, $header, $headerinclude, $footer, $accept_miniquests_alert, $accept_donequests_alert, $index_accept_quests;
	$lang->load("quests");

	// Benachrichtigung fürs Team
	$accept_miniquests_alert = "";
	$accept_donequests_alert = "";
	if($mybb->usergroup['cancp'] == "1") {
		$mini_count = $db->fetch_field($db->query("SELECT COUNT(*) AS mini FROM ".TABLE_PREFIX."miniquests WHERE accepted = '0' AND pid = ''"), "mini");
		if($mini_count > "0") {
			$accept_miniquests_alert = "<div class=\"red_alert\"><a href=\"quests.php?action=acceptquests\" target=\"\">{$lang->new_miniquests}</a></div>";
		}
		$mini_count = $db->fetch_field($db->query("SELECT COUNT(*) AS mini FROM ".TABLE_PREFIX."miniquests WHERE accepted = '0' AND pid != ''"), "mini");
		$market_count = $db->fetch_field($db->query("SELECT COUNT(*) AS market FROM ".TABLE_PREFIX."quests WHERE accepted = '0' AND done != '0'"), "market");
		$quest_count = $mini_count + $market_count;
		if($quest_count > "0" ) {
			$accept_donequests_alert = "<div class=\"red_alert\"><a href=\"quests.php?action=acceptdonequests\" target=\"\">{$lang->new_accept_quests}</a></div>";
		}
		eval('$index_accept_quests = "'.$templates->get('index_accept_quests').'";');
	}
}

?>
