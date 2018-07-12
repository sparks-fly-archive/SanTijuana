<?php

function quests_templates() {
  global $db, $templates, $mybb;
  $insert_array = array(
    'title'		=> 'quests',
    'template'	=> $db->escape_string('<html>
<head>
<title>{$mybb->settings[\'bbname\']} - {$lang->quests}</title>
{$headerinclude}
<script src="https://use.fontawesome.com/5cd804e2c7.js"></script>
</head>
<body>
{$header}
<table width="100%" border="0" align="center">
<tr>
<td width="23%" valign="top">
{$quests_nav}
</td>
<td valign="top">
<table border="0" cellspacing="{$theme[\'borderwidth\']}" cellpadding="{$theme[\'tablespace\']}" class="tborder">
<tr>
<td class="thead" colspan="{$colspan}"><strong>{$lang->quests}</strong></td>
</tr>
<tr>
<td class="trow2" style="padding: 10px; text-align: justify;">
<div style="width: 95%; margin: auto; padding: 8px;  font-size: 12px; line-height: 1.5em;" class="trow1">
	<i class="fa fa-question-circle" aria-hidden="true" style="float: left; font-size: 80px; margin: 5px;"></i> {$lang->quest_description} <br /><br />

</div>
</td>
</tr>
</table>
</td>
</tr>
</table>
{$footer}
</body>
</html>'),
    'sid'		=> '-1',
    'version'	=> '',
    'dateline'	=> TIME_NOW
  );
  $db->insert_query("templates", $insert_array);
}

$insert_array = array(
  'title'		=> 'quests_accept',
  'template'	=> $db->escape_string('<html>
<head>
<title>{$mybb->settings[\'bbname\']} - {$lang->quests_accept}</title>
{$headerinclude}
<script src="https://use.fontawesome.com/5cd804e2c7.js"></script>
</head>
<body>
{$header}
<table width="100%" border="0" align="center">
<tr>
<td width="23%" valign="top">
{$quests_nav}
</td>
<td valign="top">
<table border="0" cellspacing="{$theme[\'borderwidth\']}" cellpadding="{$theme[\'tablespace\']}" class="tborder">
<tr>
<td class="thead" colspan="{$colspan}"><strong>{$lang->quests_accept}</strong></td>
</tr>
<tr>
<td class="trow2" style="padding: 10px; text-align: justify;">
<div style="width: 95%; margin: auto; padding: 8px;  font-size: 12px; line-height: 1.5em;" class="trow1">
	<i class="fa fa-question-circle" aria-hidden="true" style="float: left; font-size: 50px; margin: 5px;"></i> {$lang->quests_accept_description} <br /><br />

	<center>
		<table style="width: 100%;" cellpadding="5" cellspacing="5">
			<tr>
				<td class="tcat" style="width: 400px;">{$lang->quest}</td>
				<td class="tcat">{$lang->addedby}</td>
				<td class="tcat">{$lang->accept_quest}</td>
				<td class="tcat">{$lang->decline_quest}</td>
			</tr>
			{$quests_accept_bit}
		</table>
    </center>

</div>
</td>
</tr>
</table>
</td>
</tr>
</table>
{$footer}
</body>
</html>'),
  'sid'		=> '-1',
  'version'	=> '',
  'dateline'	=> TIME_NOW
);
$db->insert_query("templates", $insert_array);

$insert_array = array(
  'title'		=> 'quests_accept_bit',
  'template'	=> $db->escape_string('<tr class="trow2">
	<td>
		{$quest[\'quest\']}
	</td>
	<td>
		<center>
			{$quest[\'addedby\']}
		</center>
	</td>
	<td>
		<center>
			<form action="quests.php?action=acceptquests" method="post">
				<input type="hidden" name="addedby" value="{$addedby}" />
				<input type="hidden" name="id" value="{$quest[\'qid\']}" />
				<input type="submit" name="accept" value="{$lang->accept_quest}" />
			</form>
		</center>
	</td>
	<td>
		<center>
			<form action="quests.php?action=acceptquests" method="post">
				<input type="hidden" name="id" value="{$quest[\'qid\']}" />
				<input type="submit" name="decline" value="{$lang->decline_quest}" />
			</form>
		</center>
	</td>
</tr>'),
  'sid'		=> '-1',
  'version'	=> '',
  'dateline'	=> TIME_NOW
);
$db->insert_query("templates", $insert_array);

$insert_array = array(
  'title'		=> 'quests_accept_done',
  'template'	=> $db->escape_string('<html>
<head>
<title>{$mybb->settings[\'bbname\']} - {$lang->quests_accept}</title>
{$headerinclude}
<script src="https://use.fontawesome.com/5cd804e2c7.js"></script>
</head>
<body>
{$header}
<table width="100%" border="0" align="center">
<tr>
<td width="23%" valign="top">
{$quests_nav}
</td>
<td valign="top">
<table border="0" cellspacing="{$theme[\'borderwidth\']}" cellpadding="{$theme[\'tablespace\']}" class="tborder">
<tr>
<td class="thead" colspan="{$colspan}"><strong>{$lang->quests_accept}</strong></td>
</tr>
<tr>
<td class="trow2" style="padding: 10px; text-align: justify;">
<div style="width: 95%; margin: auto; padding: 8px;  font-size: 12px; line-height: 1.5em;" class="trow1">
	<h1><i class="fa fa-gift" aria-hidden="true"></i> {$lang->miniquests}</h1>
	<i class="fa fa-question-circle" aria-hidden="true" style="float: left; font-size: 50px; margin: 5px;"></i> {$lang->miniquests_accept_description}<br /><br />

	<table style="width: 100%;" cellpadding="5" cellspacing="5">
		<tr>
			<td class="tcat" style="width: 400px;"><i class="fa fa-question" aria-hidden="true"></i> {$lang->miniquest}</td>
			<td class="tcat"><i class="fa fa-link" aria-hidden="true"></i> {$lang->read_post}</td>
			<td class="tcat"><i class="fa fa-thumbs-up" aria-hidden="true"></i> <i class="fa fa-thumbs-down" aria-hidden="true"></i> {$lang->accept_quest} / {$lang->decline_quest}</td>
		</tr>
{$quests_accept_done_bit}</table>

	<h1><i class="fa fa-shopping-bag" aria-hidden="true"></i>{$lang->quest_sent}</h1>
	<i class="fa fa-question-circle" aria-hidden="true" style="float: left; font-size: 20px; margin: 5px;"></i> {$lang->quests_accept_description} <br /><br />


		<table style="width: 100%;" cellpadding="5" cellspacing="5">
		<tr>
			<td class="tcat" style="width: 700px;"><i class="fa fa-question" aria-hidden="true"></i> {$lang->quest}</td>
			<td class="tcat"><i class="fa fa-link" aria-hidden="true"></i> {$lang->read_topic}</td>
			<td class="tcat"><i class="fa fa-thumbs-up" aria-hidden="true"></i> <i class="fa fa-thumbs-down" aria-hidden="true"></i> {$lang->accept_quest} / {$lang->decline_quest}</td>
		</tr>
{$quests_accept_done_market_bit}</table>

</div>
</td>
</tr>
</table>
</td>
</tr>
</table>
{$footer}
</body>
</html>'),
  'sid'		=> '-1',
  'version'	=> '',
  'dateline'	=> TIME_NOW
);
$db->insert_query("templates", $insert_array);

$insert_array = array(
  'title'		=> 'quests_accept_done_bit',
  'template'	=> $db->escape_string('<tr class="trow2">
	<td>
		{$quest[\'quest\']}
	</td>
	<td>
		<center>
			{$quest[\'pid\']}
		</center>
	</td>
	<td>
		<center>
			<form action="quests.php?action=acceptdonequests" method="post">
				<input type="hidden" name="id" value="{$quest[\'qid\']}" />
				<input type="hidden" name="doneby" value="{$bydone}" />
				<input type="hidden" name="pid" value="{$postid}" />
				<input type="hidden" name="quest" value="{$quest[\'quest\']}" />
				<input type="hidden" name="addedby" value="{$byadded}" />
				<input type="submit" name="accept" value="{$lang->accept_quest}" />
				<input type="submit" name="decline" value="{$lang->decline_quest}" />
			</form>
		</center>
	</td>
</tr>'),
  'sid'		=> '-1',
  'version'	=> '',
  'dateline'	=> TIME_NOW
);
$db->insert_query("templates", $insert_array);

$insert_array = array(
  'title'		=> 'quests_accept_done_market_bit',
  'template'	=> $db->escape_string('<tr class="trow2">
	<td>
		{$mquest[\'quest\']}
	</td>
	<td>
		<center>
			{$mquest[\'tid\']}
		</center>
	</td>
	<td>
		<center>
			<form action="quests.php?action=acceptdonequests" method="post">
				<input type="hidden" name="id" value="{$mquest[\'qid\']}" />
				<input type="hidden" name="addedby" value="{$byadded}" />
				<input type="hidden" name="uids" value="{$mquest[\'uids\']}" />
				<input type="submit" name="accept_market" value="{$lang->accept_quest}" />
				<input type="submit" name="decline_market" value="{$lang->decline_quest}" />
			</form>
		</center>
	</td>
</tr>
<tr>
	<td colspan="3" class="trow2"><strong><i class="fa fa-address-card" aria-hidden="true"></i> {$lang->players}:</strong> &raquo; {$username_list} <i class="fa fa-diamond" aria-hidden="true"></i> <strong>{$lang->extras}:</strong> {$mquest[\'extra\']}</td>
</tr>
<tr>
	<td colspan="3" class="tcat"></td>
</tr>'),
  'sid'		=> '-1',
  'version'	=> '',
  'dateline'	=> TIME_NOW
);
$db->insert_query("templates", $insert_array);

$insert_array = array(
  'title'		=> 'quests_active',
  'template'	=> $db->escape_string('<html>
<head>
<title>{$mybb->settings[\'bbname\']} - {$lang->quests_active}</title>
{$headerinclude}
<script src="https://use.fontawesome.com/5cd804e2c7.js"></script>
</head>
<body>
{$header}
<table width="100%" border="0" align="center">
<tr>
<td width="23%" valign="top">
{$quests_nav}
</td>
<td valign="top">
<table border="0" cellspacing="{$theme[\'borderwidth\']}" cellpadding="{$theme[\'tablespace\']}" class="tborder">
<tr>
<td class="thead" colspan="{$colspan}"><strong>{$lang->quests_active}</strong></td>
</tr>
<tr>
<td class="trow2" style="padding: 10px; text-align: justify;">
<div style="width: 95%; margin: auto; padding: 8px;  font-size: 12px; line-height: 1.5em;" class="trow1">

	<h1><i class="fa fa-gift" aria-hidden="true"></i> {$lang->miniquests}</h1>
		<i class="fa fa-question-circle" aria-hidden="true" style="float: left; font-size: 30px; margin: 5px;"></i> {$lang->active_miniquests_description}<br /><br />
	<table style="width: 100%;" cellpadding="5" cellspacing="5">
		<tr>
			<td class="tcat" style="width: 85%;"><i class="fa fa-hourglass-start" aria-hidden="true"></i> {$lang->pending_quests} ({$mini_count})</td>
			<td class="tcat"><i class="fa fa-arrow-right" aria-hidden="true"></i> {$lang->submit_quest}</td>
		</tr>
		{$quests_active_bit}
	</table><br />
	<h1><i class="fa fa-shopping-bag" aria-hidden="true"></i>{$lang->quest_sent}</h1>
		<i class="fa fa-question-circle" aria-hidden="true" style="float: left; font-size: 30px; margin: 5px;"></i> {$lang->active_quests_description} <br /><br />
	<table style="width: 100%;" cellpadding="5" cellspacing="5">
		<tr>
			<td class="tcat" style="width: 85%;"><i class="fa fa-hourglass-start" aria-hidden="true"></i> {$lang->pending_quests} ({$market_count})</td>
			<td class="tcat"><i class="fa fa-arrow-right" aria-hidden="true"></i> {$lang->submit_quest}</td>
		</tr>
		{$quests_active_market_bit}
	</table>
</div>
</td>
</tr>
</table>
</td>
</tr>
</table>
{$footer}
</body>
</html>'),
  'sid'		=> '-1',
  'version'	=> '',
  'dateline'	=> TIME_NOW
);
$db->insert_query("templates", $insert_array);

$insert_array = array(
  'title'		=> 'quests_active_bit',
  'template'	=> $db->escape_string('<tr>
	<td  class="trow2">
		{$quests[\'quest\']}
	</td>
	<td  class="trow2">
		<center>
			<form action="quests.php?action=active" method="post">
				<input name="pid" id="pid" style="width: 75px;" />
				<input type="hidden" name="id" value="{$quests[\'qid\']}" />
				<input type="submit" name="pid_mini" value="{$lang->submit_quest}"  />
			</form>
		</center>
	</td>
</tr>'),
  'sid'		=> '-1',
  'version'	=> '',
  'dateline'	=> TIME_NOW
);
$db->insert_query("templates", $insert_array);

$insert_array = array(
  'title'		=> 'quests_active_market_bit',
  'template'	=> $db->escape_string('<tr>
	<td  class="trow2">
		{$mquests[\'quest\']}
	</td>
	<td  class="trow2">
		<center>
			<form action="quests.php?action=active" method="post">
				<input name="tid" id="tid" style="width: 75px;" value="{$mquests[\'tid\']}" />
				<input type="hidden" name="id" value="{$mquests[\'qid\']}" /><br />
				<input type="checkbox" name="done" unchecked> <span class="smalltext">{$lang->is_quest_done}</span>
				<input type="submit" name="tid_market" value="{$lang->submit_quest}"  />
			</form>
		</center>
	</td>
</tr>
<tr>
	<td colspan="2" class="trow2"><strong><i class="fa fa-address-card" aria-hidden="true"></i> {$lang->players}:</strong> &raquo; {$username_list} <i class="fa fa-diamond" aria-hidden="true"></i> <strong>{$lang->extras}:</strong> {$mquests[\'extra\']}</td>
</tr>
<tr>
	<td colspan="2" class="tcat"></td>
</tr>'),
  'sid'		=> '-1',
  'version'	=> '',
  'dateline'	=> TIME_NOW
);
$db->insert_query("templates", $insert_array);

$insert_array = array(
  'title'		=> 'quests_add',
  'template'	=> $db->escape_string('<html>
<head>
<title>{$mybb->settings[\'bbname\']} - {$lang->miniquest} {$lang->submit_quest}</title>
{$headerinclude}
<script src="https://use.fontawesome.com/5cd804e2c7.js"></script>
</head>
<body>
{$header}
<table width="100%" border="0" align="center">
<tr>
<td width="23%" valign="top">
{$quests_nav}
</td>
<td valign="top">
<table border="0" cellspacing="{$theme[\'borderwidth\']}" cellpadding="{$theme[\'tablespace\']}" class="tborder">
<tr>
<td class="thead" colspan="{$colspan}"><strong>{$lang->miniquest} {$lang->submit_quest}</strong></td>
</tr>
<tr>
<td class="trow2" style="padding: 10px; text-align: justify;">
<div style="width: 95%; margin: auto; padding: 8px;  font-size: 12px; line-height: 1.5em;" class="trow1">
	<i class="fa fa-question-circle" aria-hidden="true" style="float: left; font-size: 50px; margin-right: 10px;"></i>  {$lang->add_miniquest_description} <br /><br />

	  <center>
	  <form id="quest" method="post" action="quests.php?action=add">
            <p>
                <textarea name="quest" id="quest" style="width: 300px; height: 100px;"></textarea>
            </p>
            <p>
               <input type="submit" name="submit" value="Hinzufügen" id="submit" class="button">
            </p>
        </form>
        </center>

</div>
</td>
</tr>
</table>
</td>
</tr>
</table>
{$footer}
</body>
</html>'),
  'sid'		=> '-1',
  'version'	=> '',
  'dateline'	=> TIME_NOW
);
$db->insert_query("templates", $insert_array);

$insert_array = array(
  'title'		=> 'quests_done',
  'template'	=> $db->escape_string('<html>
<head>
<title>{$mybb->settings[\'bbname\']} - {$lang->quests_done}</title>
{$headerinclude}
<script src="https://use.fontawesome.com/5cd804e2c7.js"></script>
</head>
<body>
{$header}
<table width="100%" border="0" align="center">
<tr>
<td width="23%" valign="top">
{$quests_nav}
</td>
<td valign="top">
<table border="0" cellspacing="{$theme[\'borderwidth\']}" cellpadding="{$theme[\'tablespace\']}" class="tborder">
<tr>
<td class="thead" colspan="{$colspan}"><strong>{$lang->quests_done}</strong></td>
</tr>
<tr>
<td class="trow2" style="padding: 10px; text-align: justify;">
<div style="width: 95%; margin: auto; padding: 8px;  font-size: 12px; line-height: 1.5em;" class="trow1">

	<h1><i class="fa fa-gift" aria-hidden="true"></i> {$lang->miniquests}</h1>
	<table style="width: 100%;" cellpadding="5" cellspacing="5">
		<tr>
			<td class="tcat" style="width: 85%;"><i class="fa fa-check-square-o" aria-hidden="true"></i> {$lang->quests_done} ({$mini_count})</td>
			<td class="tcat"><i class="fa fa-book" aria-hidden="true"></i> {$lang->read_post}</td>
		</tr>
		{$quests_done_bit}
	</table>
	<br />
	<h1><i class="fa fa-shopping-bag" aria-hidden="true"></i> {$lang->quest_market} / {$lang->quest_sent}</h1>
	<table style="width: 100%;" cellpadding="5" cellspacing="5">
		<tr>
			<td class="tcat" style="width: 85%;"><i class="fa fa-check-square-o" aria-hidden="true"></i> {$lang->quests_done} ({$market_count})</td>
			<td class="tcat"><i class="fa fa-book" aria-hidden="true"></i> {$lang->read_topic}</td>
		</tr>
		{$quests_done_market_bit}
	</table>
</div>
</td>
</tr>
</table>
</td>
</tr>
</table>
{$footer}
</body>
</html>'),
  'sid'		=> '-1',
  'version'	=> '',
  'dateline'	=> TIME_NOW
);
$db->insert_query("templates", $insert_array);

$insert_array = array(
  'title'		=> 'quests_done_bit',
  'template'	=> $db->escape_string('<tr>
	<td  class="trow2">{$quests[\'quest\']}</td>
	<td  class="trow2"><center>{$quests[\'pid\']}</td>
</tr>'),
  'sid'		=> '-1',
  'version'	=> '',
  'dateline'	=> TIME_NOW
);
$db->insert_query("templates", $insert_array);

$insert_array = array(
  'title'		=> 'quests_done_market_bit',
  'template'	=> $db->escape_string('<tr>
	<td  class="trow2">
		{$mquests[\'quest\']}
	</td>
	<td  class="trow2">
		<center>
			{$mquests[\'tid\']}
		</center>
	</td>
</tr>
<tr>
	<td colspan="2" class="trow2"><strong><i class="fa fa-address-card" aria-hidden="true"></i> {$lang->players}:</strong> &raquo; {$username_list} <i class="fa fa-diamond" aria-hidden="true"></i> <strong>{$lang->extras}:</strong> {$mquests[\'extra\']}</td>
</tr>
<tr>
	<td colspan="2" class="tcat"></td>
</tr>'),
  'sid'		=> '-1',
  'version'	=> '',
  'dateline'	=> TIME_NOW
);
$db->insert_query("templates", $insert_array);

$insert_array = array(
  'title'		=> 'quests_mine',
  'template'	=> $db->escape_string('<html>
<head>
<title>{$mybb->settings[\'bbname\']} - {$lang->quests_mine}</title>
{$headerinclude}
<script src="https://use.fontawesome.com/5cd804e2c7.js"></script>
</head>
<body>
{$header}
<table width="100%" border="0" align="center">
<tr>
<td width="23%" valign="top">
{$quests_nav}
</td>
<td valign="top">
<table border="0" cellspacing="{$theme[\'borderwidth\']}" cellpadding="{$theme[\'tablespace\']}" class="tborder">
<tr>
<td class="thead" colspan="{$colspan}"><strong>{$lang->quests_mine}</strong></td>
</tr>
<tr>
<td class="trow2" style="padding: 10px; text-align: justify;">
<div style="width: 95%; margin: auto; padding: 8px;  font-size: 12px; line-height: 1.5em;" class="trow1">
	<i class="fa fa-question-circle" aria-hidden="true" style="float: left; font-size: 15px; margin: 5px;"></i> {$lang->quests_mine_description}<br /><br />

	<table style="width: 100%;" cellpadding="5" cellspacing="5">
		<tr>
			<td class="tcat" style="width: 85%;">{$lang->quests_mine} ({$mini_count})</td>
			<td class="tcat">{$lang->read_post}</td>
		</tr>
		{$quests_mine_bit}
	</table>
	<br />
</div>
</td>
</tr>
</table>
</td>
</tr>
</table>
{$footer}
</body>
</html>'),
  'sid'		=> '-1',
  'version'	=> '',
  'dateline'	=> TIME_NOW
);
$db->insert_query("templates", $insert_array);

$insert_array = array(
  'title'		=> 'quests_mine_bit',
  'template'	=> $db->escape_string('<tr>
	<td  class="trow2">{$quests[\'quest\']}</td>
	<td  class="trow2"><center>{$quests[\'pid\']}</td>
</tr>'),
  'sid'		=> '-1',
  'version'	=> '',
  'dateline'	=> TIME_NOW
);
$db->insert_query("templates", $insert_array);

$insert_array = array(
  'title'		=> 'quests_miniquests',
  'template'	=> $db->escape_string('<html>
<head>
<title>{$mybb->settings[\'bbname\']} - {$lang->miniquests}</title>
{$headerinclude}
<script src="https://use.fontawesome.com/5cd804e2c7.js"></script>
</head>
<body>
{$header}
<table width="100%" border="0" align="center">
<tr>
<td width="23%" valign="top">
{$quests_nav}
</td>
<td valign="top">
<table border="0" cellspacing="{$theme[\'borderwidth\']}" cellpadding="{$theme[\'tablespace\']}" class="tborder">
<tr>
<td class="thead" colspan="{$colspan}"><strong>{$lang->miniquests}</strong></td>
</tr>
<tr>
<td class="trow2" style="padding: 10px; text-align: justify;">
<div style="width: 95%; margin: auto; padding: 8px;  font-size: 12px; line-height: 1.5em;" class="trow1">
	<i class="fa fa-question-circle" aria-hidden="true" style="float: left; font-size: 50px; margin: 5px;"></i> {$lang->miniquests_accept_description}<br /><br />

	<center>
		<p style="font-size: 16px; width: 50%; padding: 30px;" class="trow2">
			<i class="fa fa-quote-left" aria-hidden="true"></i>  {$quest} <i class="fa fa-quote-right" aria-hidden="true"></i>
			<form action="quests.php?action=active" method="post">
			<input type="hidden" name="id" value="{$questid}" />
			<input type="submit" name="claim" value="{$lang->accept_quest}"  class="button"/>
			</form>
		</p>
    </center>
	<br />

{$lang->roll_miniquests}

</div>
</td>
</tr>
</table>
</td>
</tr>
</table>
{$footer}
</body>
</html>'),
  'sid'		=> '-1',
  'version'	=> '',
  'dateline'	=> TIME_NOW
);
$db->insert_query("templates", $insert_array);

$insert_array = array(
  'title'		=> 'quests_nav',
  'template'	=> $db->escape_string('<table border="0" cellspacing="{$theme[\'borderwidth\']}" cellpadding="{$theme[\'tablespace\']}" class="tborder con-nav">
<tbody>
	<tr>
		<td class="thead"><strong>Navigation</strong></td>
	</tr>
	<tr>
		<td class="trow1 smalltext"><i class="fa fa-home" aria-hidden="true"></i> <a href="quests.php">{$lang->quests}</a></td>
	</tr>
	<tr>
		<td class="trow2 smalltext"><i class="fa fa-gift" aria-hidden="true"></i> <a href="quests.php?action=miniquests">{$lang->quest_nav_dice}</a></td>
	</tr>
	<tr>
		<td class="trow2 smalltext"><i class="fa fa-paper-plane-o" aria-hidden="true"></i> <a href="quests.php?action=add">{$lang->quest_nav_send}</a></td>
	</tr>
	<tr>
		<td class="tcat">User-Optionen</td>
	</tr>
	<tr>
		<td class="trow1 smalltext"><i class="fa fa-exclamation-circle" aria-hidden="true"></i>  <a href="quests.php?action=active">{$lang->quests_active}</a></td>
	</tr>
	<tr>
		<td class="trow2 smalltext"><i class="fa fa-check-circle" aria-hidden="true"></i>  <a href="quests.php?action=done">{$lang->quests_done}</a></td>
	</tr>
	<tr>
		<td class="trow1 smalltext"><i class="fa fa-paper-plane" aria-hidden="true"></i>  <a href="quests.php?action=mine">{$lang->quests_mine}</a></td>
	</tr>
	{$quests_nav_team}
</tbody>
</table>'),
  'sid'		=> '-1',
  'version'	=> '',
  'dateline'	=> TIME_NOW
);
$db->insert_query("templates", $insert_array);

$insert_array = array(
  'title'		=> 'quests_nav_team',
  'template'	=> $db->escape_string('	<tr>
		<td class="tcat">{$lang->quests_nav_team}</td>
	</tr>
	<tr>
		<td class="trow1 smalltext"><i class="fa fa-envelope-open" aria-hidden="true"></i>  <a href="quests.php?action=senduser">{$lang->quests_nav_senduser}</a></td>
	</tr>
	<tr>
		<td class="trow1 smalltext"><i class="fa fa-handshake-o" aria-hidden="true"></i>  <a href="quests.php?action=acceptquests">{$lang->quests_nav_acceptquests}</a></td>
	</tr>
	<tr>
		<td class="trow2 smalltext"><i class="fa fa-bars" aria-hidden="true"></i>  <a href="quests.php?action=acceptdonequests">{$lang->quests_nav_acceptdonequests}</a></td>
	</tr>'),
  'sid'		=> '-1',
  'version'	=> '',
  'dateline'	=> TIME_NOW
);
$db->insert_query("templates", $insert_array);

$insert_array = array(
  'title'		=> 'quests_senduser',
  'template'	=> $db->escape_string('<html>
<head>
<title>{$mybb->settings[\'bbname\']} - {$lang->quests_nav_senduser}</title>
{$headerinclude}
<script src="https://use.fontawesome.com/5cd804e2c7.js"></script>
</head>
<body>
{$header}
<table width="100%" border="0" align="center">
<tr>
<td width="23%" valign="top">
{$quests_nav}
</td>
<td valign="top">
<table border="0" cellspacing="{$theme[\'borderwidth\']}" cellpadding="{$theme[\'tablespace\']}" class="tborder">
<tr>
<td class="thead" colspan="{$colspan}"><strong>{$lang->quests_nav_senduser}</strong></td>
</tr>
<tr>
<td class="trow2" style="padding: 10px; text-align: justify;">
<div style="width: 95%; margin: auto; padding: 8px;  font-size: 12px; line-height: 1.5em;" class="trow1">
	<i class="fa fa-question-circle" aria-hidden="true" style="float: left; font-size: 80px; margin: 5px;"></i> {$lang->quests_senduser_description}<br /><br />

	<form method="post" action="quests.php?action=senduser" id="senduser">
	<table border="0" cellspacing="{$theme[\'borderwidth\']}" cellpadding="{$theme[\'tablespace\']}" class="tborder" style="width: 65%;">
		<tr>
			<td class="thead" colspan="2"><strong>{$lang->quests_nav_senduser}</strong></td>
		</tr>
		<tr>
			<td class="tcat" colspan="2">{$lang->quest}</td>
		</tr>
		<tr class="trow2">
			<td class="smalltext" style="text-align: justify; width: 25%;">
				<strong>Quest</strong>
				&raquo; {$lang->quests_quest_description}
			</td>
			<td align="center"><textarea name="quest" id="quest" cols="75" rows="8"></textarea>
			</td>
		</tr>
		<tr class="trow1">
			<td class="smalltext" style="text-align: justify; width: 25%;">
				<strong>{$lang->extra}</strong>
				&raquo; {$lang->quests_extra_description}
			</td>
			<td align="center"><input type="text" id="extra" name="extra" style="width: 90%;">
			</td>
		</tr>
		<tr>
			<td class="tcat" colspan="2">{$lang->players}</td>
		</tr>
		<tr class="trow2">
			<td class="smalltext" style="text-align: justify; width: 25%">
				<strong>{$lang->players}</strong>
				&raquo; {$lang->quests_sendplayers_description}
			</td>
			<td align="center">
			    <select name="uids[]" size="5" style="width: 95%" multiple="multiple">
					{$senduser_bit}
    			</select>
			</td>
		</tr>
		<tr>
			<td class="tcat" colspan="2">{$lang->submit_quest}</td>
		</tr>
		<tr>
			<td colspan="2" align="center">
			<input type="submit" name="senduser" value="{$lang->quest} {$lang->submit_quest}!" /></td>
		</tr>
	</table>
	</form>

</div>
</td>
</tr>
</table>
</td>
</tr>
</table>
{$footer}
</body>
</html>'),
  'sid'		=> '-1',
  'version'	=> '',
  'dateline'	=> TIME_NOW
);
$db->insert_query("templates", $insert_array);

$insert_array = array(
  'title'		=> 'index_accept_quests',
  'template'	=> $db->escape_string('{$accept_miniquests_alert}
  {$accept_donequests_alert}'),
  'sid'		=> '-1',
  'version'	=> '',
  'dateline'	=> TIME_NOW
);

$db->insert_query("templates", $insert_array);

?>
