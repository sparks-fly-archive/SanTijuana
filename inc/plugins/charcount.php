<?php
/**
 * Character Count 1.8
 * Copyright © 2011-2017 doylecc
 * http://community.mybb.com/user-14694.html
 *
 This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 the Free Software Foundation, either version 3 of the License, or
 (at your option) any later version.
 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.
 You should have received a copy of the GNU General Public License
 along with this program.  If not, see <http://www.gnu.org/licenses/>
 */

if(!defined("IN_MYBB"))
{
	die("Direct initialization of this file is not allowed.<br /><br />Please make sure IN_MYBB is defined.");
}

//Add hook
$plugins->add_hook('pre_output_page', 'charcount_show');

// Plugin info
function charcount_info()
{
	global $plugins_cache;

	$charcount_info = array(
		"name"			=> "Character Count",
		"description"	=> "Shows the remaining characters in editors textarea in real time.",
		"website"		=> "http://mods.mybb.com/profile/14694",
		"author"		=> "doylecc",
		"authorsite"	=> "http:/mybbplugins.tk",
		"version"		=> "1.9",
		"guid"			=> "8a74eb304882e3a7ea768ea635c7a084",
		"compatibility"	=> "18*"
	);

	if(is_array($plugins_cache) && is_array($plugins_cache['active']) && $plugins_cache['active']['charcount'])
	{
		$charcount_info['description'] .= @charcount_status();
	}
	return $charcount_info;
}

// Show update link in plugin list
function charcount_status()
{
	global $db, $mybb;

	$query1 = $db->simple_select('templates', 'template', 'template like "%counttarget%"');
	$query2 = $db->simple_select('templates', 'template', 'template like "%countstart%"');
	$result1 = $db->num_rows($query1);
	$result2 = $db->num_rows($query2);

	if($result1 || $result2)
	{
		$status =  "<br /><span style=\"float: right; padding-right: 20px;\"><img src=\"styles/default/images/icons/error.png\" alt=\"\" />&nbsp;&nbsp;<a href=\"index.php?module=tools-charcount&amp;action=update&amp;my_post_key=".$mybb->post_code."\">Update Plugin</a></span>";
	}
	else
	{
		$status = '';
	}
	return $status;
}

// Activate the plugin
function charcount_activate()
{
}

// Deactivate the plugin
function charcount_deactivate()
{
}

// Get the max. message length
function charcount_max()
{
	global $mybb, $db;

	// Get the data type of the message field for the max. length
	$result = $db->show_fields_from("posts");
	if(is_array($result))
	{
		foreach ($result as $key => $fields)
		{
			if($fields['Field'] == 'message')
			{
				switch($fields['Type'])
				{
					case 'text':
					{
						$maximum = 65535;
					}
					break;
					case 'mediumtext':
					{
						$maximum = 16777215;
					}
					break;
					case 'longtext':
					{
						$maximum = 4294967295;
					}
					break;
				}
			}
		}
	}

	$maxlength = (int)$mybb->settings['maxmessagelength'];

	if($maxlength && $maxlength > 0 && $maxlength < $maximum)
	{
		$maxchars = $maxlength;
	}
	else
	{
		$maxchars = $maximum;
	}
	return $maxchars;
}

// Get the min. message length
function charcount_min()
{
	global $mybb;

	$minlength = (int)$mybb->settings['minmessagelength'];

	if($minlength && $minlength > 0)
	{
		$minchars = $minlength;
	}
	else
	{
		$minchars = 0;
	}
	return $minchars;
}

// Display character count data below textareas and warnings above textareas
function charcount_show($page)
{
	global $mybb, $lang;

	if (isset($mybb->settings['quickadveditorplus_qedit']) && $mybb->settings['quickadveditorplus_qedit'] != 0 && empty($mybb->user['usemheditor']) && THIS_SCRIPT == "showthread.php") return;

	if(!isset($lang->char_count_min))
	{
		if(isset($lang))
		{
			$lang->load("charcount");
		}
		else
		{
			$GLOBALS['lang']->load("charcount");
			$lang = $GLOBALS['lang'];
		}
	}
	$maxchars = charcount_max();
	$minchars = charcount_min();
	$lang->char_count_max_warn = $lang->sprintf($lang->char_count_max_warn, $maxchars);

	$countset_warn = '';
	if($minchars > 0 && $maxchars > 0 && $minchars >= $maxchars)
	{
		$lang->char_count_set_warn = $lang->sprintf($lang->char_count_set_warn, $minchars, $maxchars);
		$countset_warn = '<div class="warningSet">'.$lang->char_count_set_warn.'</div>';
	}

	// Header code insert
	$replace_head = array('</head>' => '<!-- start: character count css-->
	<style type="text/css">
		.normalClass {
			margin-top: 10px;
			font-size: 12px;
			font-family: Tahoma, sans-serif;
			float:right: right;
		}

		.warningClass {
			margin-top: 10px;
			font-size: 12px;
			font-weight: bold;
			color: #FF0000;
			font-family: Tahoma, sans-serif;
			text-align: right
		}
		.warningSet {
			margin-top: 10px;
			font-size: 12px;
			font-weight: bold;
			color: #FF0000;
			font-family: Tahoma, sans-serif;
			text-align: left
		}

		.showData {
			height: 20px;
			margin: 10px;
			text-align: right;
		}
	</style>
	<!-- end: character count css -->
	</head>');

	// Footer code insert
	$replace_foot = array('</body>' => '<!-- start: character count script -->
	<script type="text/javascript">
	if (typeof jQuery == \'undefined\')
	{
		document.write(unescape("%3Cscript src=\'http://code.jquery.com/jquery-latest.min.js\' type=\'text/javascript\'%3E%3C/script%3E"));
	}
	</script>
	<script type="text/javascript">
	<!--
	var msg_min = "'.$lang->char_count_min.'";
	var msg_max = "'.$lang->char_count_max.'";
	var msg_input = "'.$lang->char_count_input.'";
	var msg_left = "'.$lang->char_count_left.'";
	var msg_words = "'.$lang->char_count_words.'";
	var msg_max_warn = "'.$lang->char_count_max_warn.'";

	var info;
	jQuery(document).ready(function($)
	{
		$("#showData").html("'.$lang->char_count_button.'");

		// Word count function part 1
		function getCleanedWordStrings(content){
			var fullStr = content + " ";
			var initial_whitespace_rExp = /^[^A-Za-z0-9]+/gi;
			var left_trimmedStr = fullStr.replace(initial_whitespace_rExp, "");
			var non_alphanumerics_rExp = rExp = /[^A-Za-z0-9]+/gi;
			var cleanedStr = left_trimmedStr.replace(non_alphanumerics_rExp, " ");
			var splitString = cleanedStr.split(" ");
			return splitString;
		}

		// Word count function part 2
		function countWords(cleanedWordStrings){
			var word_count = cleanedWordStrings.length-1;
			return word_count;
		}

		// Observe textarea actions an show warning message
		$("#message").bind("keyup mouseover paste", function() {
			$(".showData").css("min-width", "500px");
			var all_length = $("#message").val().length;
			// Do not count whitespaces and line breaks
			if($("#message").val().match(/\s/g,"")) {
				var space = $("#message").val().match(/\s/g,"").length;
				var length = all_length - space;
			} else {
				var length = all_length;
			}
			var num_left = '.$maxchars.' - length;
			var numWords = countWords(getCleanedWordStrings($("#message").val()));

			if(length >= '.$maxchars.') {
				$("#showWarn").html("<span class=\"ShowWarn warningClass\">'.$lang->char_count_max_warn.'</span>");
				$("#showData").html("");
			} else if(length < '.$minchars.') {
				$("#showData").addClass("warningClass");
				$("#showData").html(length + msg_min + "'.$minchars.'" + msg_input);
				$("#showWarn").html("");
			} else {
				$("#showData").removeClass("warningClass");
				$("#showData").html(length + msg_max + "'.$maxchars.'" + msg_input + "(" + num_left + msg_left + ") / " + numWords + msg_words);
				$("#showWarn").html("");
			}
			if(length >= '.$maxchars.' - 20) {
				$("#showData").addClass("warningClass");
			}
		});

		// Observe CKEDITOR actions and display warning message & character/word count
		if(typeof CKEDITOR !== \'undefined\') {
			$("#showData").bind("mouseenter touchstart", function() {
				var all_length = CKEDITOR.instances[\'message\'].getData().length;
				// Do not count whitespaces and line breaks
				if(CKEDITOR.instances[\'message\'].getData().match(/\s/g,"")) {
					var space = CKEDITOR.instances[\'message\'].getData().match(/\s/g,"").length;
					var length = all_length - space;
				} else {
					var length = all_length;
				}
				var num_left = '.$maxchars.' - length;
				var numWords = countWords(getCleanedWordStrings(CKEDITOR.instances[\'message\'].getData()));

				if(length >= '.$maxchars.') {
					$("#showWarn").html("<span class=\"ShowWarn warningClass\">'.$lang->char_count_max_warn.'</span>");
					$("#showData").html("");
				} else if(length < '.$minchars.') {
					$("#showData").addClass("warningClass");
					$("#showData").html(length + msg_min + "'.$minchars.'" + msg_input);
					$("#showWarn").html("");
				} else {
					$("#showData").removeClass("warningClass");
					$("#showData").html(length + msg_max + "'.$maxchars.'" + msg_input + "(" + num_left + msg_left + ") / " + numWords + msg_words);
					$("#showWarn").html("");
				}
				if(length >= '.$maxchars.' - 20) {
					$("#showData").addClass("warningClass");
				}
			});

			$("#showData").on("mouseleave touchleave touchend", function() {
				$("#showData").html("'.$lang->char_count_button.'");
			});
			CKEDITOR.instances[\'message\'].on("focus", function() {
				$("#showData").html("'.$lang->char_count_button.'");
			});
		}

		// Observe Sceditor actions and display warning message & character/word count
		else if(MyBBEditor) {
			$("#showData").bind("mouseenter touchstart", function() {
				var all_length = MyBBEditor.val().length;
				// Do not count whitespaces and line breaks
				if(MyBBEditor.val().match(/\s/g,"")) {
					var space = MyBBEditor.val().match(/\s/g,"").length;
					var length = all_length - space;
				} else {
					var length = all_length;
				}
				var num_left = '.$maxchars.' - length;
				var numWords = countWords(getCleanedWordStrings(MyBBEditor.val()));

				if(length >= '.$maxchars.') {
					$("#showWarn").html("<span class=\"ShowWarn warningClass\">'.$lang->char_count_max_warn.'</span>");
					$("#showData").html("");
				} else if(length < '.$minchars.') {
					$("#showData").addClass("warningClass");
					$("#showData").html(length + msg_min + "'.$minchars.'" + msg_input);
					$("#showWarn").html("");
				} else {
					$("#showData").removeClass("warningClass");
					$("#showData").html(length + msg_max + "'.$maxchars.'" + msg_input + "(" + num_left + msg_left + ") / " + numWords + msg_words);
					$("#showWarn").html("");
				}
				if(length >= '.$maxchars.' - 20) {
					$("#showData").addClass("warningClass");
				}
			});

			$("#showData").bind("mouseleave touchleave touchend", function() {
				$("#showData").html("'.$lang->char_count_button.'");
			});
			MyBBEditor.bind("focus", function() {
				$("#showData").html("'.$lang->char_count_button.'");
			});
		}
	});
	-->
	</script>
	<!-- end: character count script -->
	</body>');

	// PM Code insert
	$replace_pm = array('</head>' => '<!-- start: character count pm script -->
	<script type="text/javascript">
	<!--
	jQuery(document).ready(function($)
	{
		// Insert character count element after message textarea only
		$("<div id=\"showData\" class=\"showData\">&nbsp;</div>").insertAfter("#message");
	});
	-->
	</script>
	<!-- end: character count pm script -->
	</head>');

	/**
	*
	* Replace the code
	*
	**/
	if(THIS_SCRIPT == "newthread.php" || THIS_SCRIPT == "newreply.php" || THIS_SCRIPT == "editpost.php")
	{
		// Body code insert
		$replace_body1 = array('<textarea' => ''.$countset_warn.'<div id="showWarn"></div>
		<textarea');
		$replace_body2 = array('</textarea>' => '</textarea>
		<div id="showData" class="showData">&nbsp;</div>');

		// Replace code
		$page = strtr($page, $replace_head);
		$page = strtr($page, $replace_foot);
		$page = strtr($page, $replace_body1);
		$page = strtr($page, $replace_body2);
	}
	if(THIS_SCRIPT == "showthread.php" && $mybb->settings['quickreply'] == 1)
	{
		// Body code insert
		$replace_body1 = array('<textarea' => ''.$countset_warn.'<div id="showWarn"></div>
		<textarea');
		$replace_body2 = array('</textarea>' => '</textarea>
		<div id="showData" class="showData">&nbsp;</div>');

		// Replace code
		$page = strtr($page, $replace_head);
		$page = strtr($page, $replace_foot);
		$page = strtr($page, $replace_body1);
		$page = strtr($page, $replace_body2);
	}
	if(THIS_SCRIPT == "private.php" && $mybb->input['action'] == "send")
	{
		// Body code insert
		$replace_body1 = array('<textarea name="message"' => ''.$countset_warn.'<div id="showWarn">&nbsp;</div>
		<textarea name="message"');

		// Replace code
		$page = strtr($page, $replace_head);
		$page = strtr($page, $replace_foot);
		$page = strtr($page, $replace_body1);
		$page = strtr($page, $replace_pm);
	}
	return $page;
}

/**
 *
 * Admin CP part for the update function
 *
 **/

// Add hooks
$plugins->add_hook("admin_load", "charcount_admin");
$plugins->add_hook("admin_tools_action_handler", "charcount_admin_tools_action_handler");

// ACP action handler
function charcount_admin_tools_action_handler(&$actions)
{
	$actions['charcount'] = array('active' => 'charcount', 'file' => 'charcount');
}

// Update function from old plugin versions < 1.4
function charcount_admin()
{
	global $db, $lang, $mybb, $run_module, $action_file;

	if($run_module == 'tools' && $action_file == 'charcount')
	{
		if($mybb->input['action'] == "update")
		{
			if(!verify_post_check($mybb->input['my_post_key']))
			{
				flash_message($lang->invalid_post_verify_key2, 'error');
				admin_redirect("index.php?module=config-plugins");
			}
		charcount_templates_reset();

		$query = $db->simple_select('templates', 'template', 'template like "%counttarget%"');
		$result = $db->num_rows($query);

		if($result)
		{
			flash_message("Not able to revert all older Character Count template edits!", 'error');
		}
		else
		{
			flash_message("Character Count Plugin updated successfully!", 'success');
		}
		admin_redirect("index.php?module=config-plugins");
		exit();
		}
	}
}

// Revert old template edits
function charcount_templates_reset()
{
	// Remove template elements
	include MYBB_ROOT."/inc/adminfunctions_templates.php";

	find_replace_templatesets("showthread", "#".preg_quote('<script type="text/javascript" src="jscripts/charcount.js"></script>
<script type="text/javascript">
<!--
	var maxchars = "{$maxchars}";
	var minchars = "{$minchars}";
	var alert_one = "{$lang->char_alert_one}";
	var alert_two = "{$lang->char_alert_two}";
	var max_msg = "{$lang->char_count}";
	var minmsg1 = "{$lang->char_minmsg_one}";
	var minmsg2 = "{$lang->char_minmsg_two}";
-->
</script>
')."#i", '', 0);
	find_replace_templatesets("showthread_quickreply", "#".preg_quote('<div class="smalltext"><span style="font-weight:bold;" class="countstart"></span>&nbsp;<span class="counttarget"></span></div>
')."#i", '', 0);

	find_replace_templatesets("newreply", "#".preg_quote('<script type="text/javascript" src="jscripts/charcount.js"></script>
<script type="text/javascript">
<!--
	var maxchars = "{$maxchars}";
	var minchars = "{$minchars}";
	var alert_one = "{$lang->char_alert_one}";
	var alert_two = "{$lang->char_alert_two}";
	var max_msg = "{$lang->char_count}";
	var minmsg1 = "{$lang->char_minmsg_one}";
	var minmsg2 = "{$lang->char_minmsg_two}";
-->
</script>
')."#i", '', false);
	find_replace_templatesets("newreply", "#".preg_quote('<div class="smalltext"><span style="font-weight:bold;" class="countstart"></span>&nbsp;<span class="counttarget"></span></div>
')."#i", '', false);

	find_replace_templatesets("newthread", "#".preg_quote('<script type="text/javascript" src="jscripts/charcount.js"></script>
<script type="text/javascript">
<!--
	var maxchars = "{$maxchars}";
	var minchars = "{$minchars}";
	var alert_one = "{$lang->char_alert_one}";
	var alert_two = "{$lang->char_alert_two}";
	var max_msg = "{$lang->char_count}";
	var minmsg1 = "{$lang->char_minmsg_one}";
	var minmsg2 = "{$lang->char_minmsg_two}";
-->
</script>
')."#i", '', false);
	find_replace_templatesets("newthread", "#".preg_quote('<div class="smalltext"><span style="font-weight:bold;" class="countstart"></span>&nbsp;<span class="counttarget"></span></div>
')."#i", '', false);

	find_replace_templatesets("editpost", "#".preg_quote('<script type="text/javascript" src="jscripts/charcount.js"></script>
<script type="text/javascript">
<!--
	var maxchars = "{$maxchars}";
	var minchars = "{$minchars}";
	var alert_one = "{$lang->char_alert_one}";
	var alert_two = "{$lang->char_alert_two}";
	var max_msg = "{$lang->char_count}";
	var minmsg1 = "{$lang->char_minmsg_one}";
	var minmsg2 = "{$lang->char_minmsg_two}";
-->
</script>
')."#i", '', false);
	find_replace_templatesets("editpost", "#".preg_quote('<div class="smalltext"><span style="font-weight:bold;" class="countstart"></span>&nbsp;<span class="counttarget"></span></div>
')."#i", '', false);

	find_replace_templatesets("private_send", "#".preg_quote('<script type="text/javascript" src="jscripts/charcount.js"></script>
<script type="text/javascript">
<!--
	var maxchars = "{$maxchars}";
	var minchars = "{$minchars}";
	var alert_one = "{$lang->char_alert_one}";
	var alert_two = "{$lang->char_alert_two}";
	var max_msg = "{$lang->char_count}";
	var minmsg1 = "{$lang->char_minmsg_one}";
	var minmsg2 = "{$lang->char_minmsg_two}";
-->
</script>
')."#i", '', false);
	find_replace_templatesets("private_send", "#".preg_quote('<div class="smalltext"><span style="font-weight:bold;" class="countstart"></span>&nbsp;<span class="counttarget"></span></div>
')."#i", '', false);

}
