<?php
if(!defined("IN_MYBB"))
{
	die("Direct initialization of this file is not allowed.<br /><br />Please make sure IN_MYBB is defined.");
}

$plugins->add_hook("postbit", "inplayquotes_postbit");
$plugins->add_hook("misc_start", "inplayquotes_misc");
$plugins->add_hook("index_start", "inplayquotes_index");
$plugins->add_hook("admin_formcontainer_output_row", "inplayquotes_permission");
$plugins->add_hook("admin_user_groups_edit_commit", "inplayquotes_permission_commit");

function inplayquotes_info()
{
	return array(
		"name"		=> "Zitate auf dem Forenindex",
		"description"	=> "Erlaubt es Mitgliedern, Zitate aus Beiträgen einzufügen, die dann auf dem Index des Forums erscheinen.",
		"website"	=> "http://storming-gates.de",
		"author"	=> "sparks fly",
		"authorsite"	=> "http://storming-gates.de",
		"version"	=> "1.0",
		"compatibility" => "18*"
	);
}

function inplayquotes_install()
{
	global $db, $cache, $mybb;

	$setting_group = array(
	    'name' => 'inplayquotes',
	    'title' => 'Inplayzitate',
	    'description' => 'Einstellungen für die Inplayzitate',
	    'disporder' => 1,
	    'isdefault' => 0
	);

	$gid = $db->insert_query("settinggroups", $setting_group);

	$setting_array = array(
	    // A text setting
	    'inplay_id' => array(
	        'title' => 'ID der Inplay-Kategorie(n)',
	        'description' => 'Gib die ID der Inplay-Kategorie(n) an (Kategorie = das "höchste" Inplayforum, in dem sich alle anderen Inplayforen befinden) - mehrere Kategorien mit "," voneinander trennen!',
	        'optionscode' => 'text',
	        'value' => '', // Default
	        'disporder' => 1
	    ),
	);

	foreach($setting_array as $name => $setting)
	{
	    $setting['name'] = $name;
	    $setting['gid'] = $gid;

	    $db->insert_query('settings', $setting);
	}

	rebuild_settings();

	if(!$db->field_exists("canquoteinplay", "usergroups"))
	{
		switch($db->type)
		{
			case "pgsql":
				$db->add_column("usergroups", "canquoteinplay", "smallint NOT NULL default '1'");
				break;
			default:
				$db->add_column("usergroups", "canquoteinplay", "tinyint(1) NOT NULL default '1'");
				break;

		}

	$cache->update_usergroups();

	$db->query("CREATE TABLE ".TABLE_PREFIX."inplayquotes (
		`qid` int(11) NOT NULL AUTO_INCREMENT,
		`uid` int(11) NOT NULL,
		`tid` int(11) NOT NULL,
		`pid` int(11) NOT NULL,
		`quote` varchar(500) COLLATE utf8_general_ci NOT NULL,
		PRIMARY KEY (`qid`),
		KEY `qid` (`qid`)
		)
		ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1");

	}

	rebuild_settings();

	$insert_array = array(
		'title'		=> 'postbit_inplayquotes',
		'template'	=> $db->escape_string('<a href="misc.php?action=quotes&pid={$post[\'pid\']}" title="Inplayzitat" class="postbit_edit"><span>Zitat hinzufügen</span></a>'),
		'sid'		=> '-1',
		'version'	=> '',
		'dateline'	=> TIME_NOW
	);
	$db->insert_query("templates", $insert_array);

	$insert_array = array(
		'title'		=> 'misc_inplayquotes',
		'template'	=> $db->escape_string('<html>
<head>
<title>{$mybb->settings[\'bbname\']} - Inplayzitat eintragen</title>
{$headerinclude}</head>
<body>
{$header}
    <table style="width: 80%; margin: auto;">
        <tr><td class="thead">Inplayzitat eintragen</td><tr>
        <tr><td class="trow1">
<center>
  <form id="quotes" method="post" action="misc.php?action=quotes&pid={$pid}">
{$insert_quote}
         <p>
                <textarea name="zitat" id="zitat" style="width: 300px; height: 100px;"></textarea>
            </p>
            <p>
               <input type="submit" name="submit" value="Zitat absenden!" id="submit">
            </p>
        </form>
            </center></td></tr></table>

{$footer}
</body>
</html>'),
		'sid'		=> '-1',
		'version'	=> '',
		'dateline'	=> TIME_NOW
	);
	$db->insert_query("templates", $insert_array);

	$insert_array = array(
		'title'		=> 'index_inplayquotes',
		'template'	=> $db->escape_string('<br /><table class="tborder" style="margin: auto;" cellpadding="10" cellspacing="1">
	<tr>
		<td class="thead">Zitat von {$quoted[\'user\']}</td>
	</tr>
	<tr>
		<td align="center" class="trow2">{$quoted[\'quote\']}</td>
	</tr>
	<tr>
		<td><center><span class="smalltext">In: {$quoted[\'scene\']}</span></center></td>
	</tr>
</table>
<br />'),
		'sid'		=> '-1',
		'version'	=> '',
		'dateline'	=> TIME_NOW
	);
	$db->insert_query("templates", $insert_array);
}

function inplayquotes_activate()
{
	include MYBB_ROOT."/inc/adminfunctions_templates.php";
	find_replace_templatesets("postbit_classic", "#".preg_quote('{$post[\'button_edit\']}')."#i", '{$post[\'inplayquotes\']}{$post[\'button_edit\']}');
	find_replace_templatesets("postbit", "#".preg_quote('{$post[\'button_edit\']}')."#i", '{$post[\'inplayquotes\']}{$post[\'button_edit\']}');
	find_replace_templatesets("index", "#".preg_quote('{$footer}')."#i", '{$inplayquotes}{$footer}');
}

function inplayquotes_is_installed()
{
	global $db;
	if($db->table_exists('inplayquotes'))
	{
		return true;
	}
	return false;
}

function inplayquotes_uninstall()
{
	global $db, $cache;

	$db->delete_query('settings', "name IN ('inplay_id')");
	$db->delete_query('settinggroups', "name = 'inplayquotes'");

	rebuild_settings();

	if($db->field_exists("canquoteinplay", "usergroups"))
  {
    $db->drop_column("usergroups", "canquoteinplay");
  }

  $cache->update_usergroups();

	if($db->table_exists("inplayquotes"))
  {
   $db->drop_table("inplayquotes");
  }

	rebuild_settings();

	$db->delete_query("templates", "title IN('postbit_inplayquotes', 'misc_inplayquotes', 'index_inplayquotes')");
}

function inplayquotes_deactivate()
{
	include MYBB_ROOT."/inc/adminfunctions_templates.php";
	find_replace_templatesets("postbit_classic", "#".preg_quote('{$post[\'inplayquotes\']}')."#i", '', 0);
	find_replace_templatesets("postbit", "#".preg_quote('{$post[\'inplayquotes\']}')."#i", '', 0);
	find_replace_templatesets("index", "#".preg_quote('{$inplayquotes}')."#i", '', 0);
}

function inplayquotes_permission($above)
{
	global $mybb, $lang, $form;

	if($above['title'] == $lang->misc && $lang->misc)
	{
		$above['content'] .= "<div class=\"group_settings_bit\">".$form->generate_check_box("canquoteinplay", 1, "Kann aus dem Inplay zitieren?", array("checked" => $mybb->input['canquoteinplay']))."</div>";
	}

	return $above;
}

function inplayquotes_permission_commit()
{
	global $mybb, $updated_group;
	$updated_group['canquoteinplay'] = $mybb->get_input('canquoteinplay', MyBB::INPUT_INT);
}

function inplayquotes_postbit(&$post)
{
	global $templates, $db, $mybb, $forum;
	$quote_forums = $db->fetch_field($db->query("SELECT value FROM ".TABLE_PREFIX."settings WHERE name = 'inplay_id'"), "value");
	$quote_forums = explode(",", $quote_forums);
	foreach($quote_forums as $quote_forum) {
		if(!empty($quote_forum)) {
			if(preg_match("/$quote_forum/i", $forum['parentlist'])) {
		$post['inplayquotes'] = eval($templates->render("postbit_inplayquotes"));
		return $post;
			}
		}
	}
}

function inplayquotes_misc()
{
	global $db, $mybb, $templates, $theme, $headerinclude, $header, $footer;
	$mybb->input['action'] = $mybb->get_input('action');
	if($mybb->input['action'] == "quotes")
	{
		if(isset($_POST['submit'])) {
			$pid = $mybb->input['pid'];
			$tid = $db->fetch_field($db->query("SELECT tid from ".TABLE_PREFIX."posts WHERE pid = '$pid'"), "tid");
			$quote = $_POST['zitat'];
			$uid = $db->fetch_field($db->query("SELECT uid from ".TABLE_PREFIX."posts WHERE pid = '$pid'"), "uid");
			$new_record = array(
				"uid" => $uid,
				"tid" => $tid,
				"pid" => $pid,
				"quote" => $db->escape_string($quote)
			);
			$insert_array = $db->insert_query("inplayquotes", $new_record);
			$insert_quote = "<div class=\"pm_alert\">Das Zitat wurde eingetragen! :)</div>";
		}
		$pid = $mybb->input['pid'];
		$query = $db->query("SELECT username, subject FROM ".TABLE_PREFIX."posts
		WHERE mybb_posts.pid = '$pid'");
		$quoted = $db->fetch_array($query);
		$quotename = $quoted['username'];
		$quotethread = $quoted['subject'];
		if(!isset($_POST['submit'])) {
			$insert_quote = "<center>Du trägst ein Zitat von <strong>{$quotename}</strong> aus der Szene <strong>{$quotethread}</strong> ein!</center>";
		}
		eval("\$inplayquotes = \"".$templates->get("misc_inplayquotes")."\";");
  	output_page($inplayquotes);
	}
}

function inplayquotes_index()
{
	global $db, $mybb, $templates, $inplayquotes, $quoted;
	$query = $db->query("SELECT * FROM mybb_inplayquotes
	LEFT JOIN mybb_posts on mybb_inplayquotes.pid = mybb_posts.pid
	ORDER BY rand()
	LIMIT 1");
	$quoted = $db->fetch_array($query);
	$quoted['user'] = build_profile_link($quoted['username'], $quoted['uid']);
	$quoted['scene']= "<a href=\"showthread.php?tid={$quoted[tid]}&pid={$quoted[pid]}#pid{$quoted[pid]}\">$quoted[subject]</a>";
	if(!empty($quoted['quote'])) {
		eval("\$inplayquotes = \"".$templates->get("index_inplayquotes")."\";");
	}
}

?>
