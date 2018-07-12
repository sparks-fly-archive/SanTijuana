<?php

define("IN_MYBB", 1);
define('THIS_SCRIPT', 'quests.php');

require_once "./global.php";

add_breadcrumb("Quests", "quests.php");

$lang->load("quests");

$action = $db->escape_string($mybb->input['action']);
$team = (int)$mybb->usergroup['cancp'];
$uid = (int)$mybb->user['uid'];

// Navigation
if($team == "1") {
  eval('$quests_nav_team = "'.$templates->get('quests_nav_team').'";');
}
eval('$quests_nav = "'.$templates->get('quests_nav').'";');

if(!$action) {
  eval("\$page = \"".$templates->get("quests")."\";");
  output_page($page);
}

// USER-OPTIONEN

// Quest-Übersicht - Aktive
if($action == "active") {

  // Keine Ansicht für Gäste
  if(!$uid) {
    error_no_permission();
  }

  // Quest aus Würfel ziehen
  if(isset($_POST['claim'])) {
    $questid = $mybb->get_input('id');
    $new_record = array(
      "taken" => "1",
      "claimedby" => (int)$uid,
      "time" => TIME_NOW
    );
    $db->update_query("miniquests", $new_record, "qid = '".(int)$questid."'");
  }

  // Lösung einsenden - Miniquest
  if(isset($_POST['pid_mini'])) {
    $questid = $mybb->get_input('id');
    $questpid = $mybb->get_input('pid');
    if(is_numeric($questpid)) {
      $new_record = array(
        "pid" => (int)$questpid,
        "doneby" => (int)$uid,
        "timedone" => TIME_NOW,
        "accepted" => "0"
      );
      $db->update_query("miniquests", $new_record, "qid = '$questid'");
    }

    // Lösung ist keine Post-ID
    else {
      error("{$lang->quests_numeric_error}");
    }
  }

  // Börsenquest einsenden
  if(isset($_POST['tid_market'])) {
    $questid = $mybb->get_input('id');
    $questtid = $mybb->get_input('tid');
    if(is_numeric($questtid)) {
      $new_record = array(
        "tid" => (int)$questtid
      );
      $db->update_query("quests", $new_record, "qid = '$questid'");
    }

    // Lösung ist keine Post-ID
    else {
      error("{$lang->quests_numeric_error}");
      return false;
    }

    if(isset($_POST['done'])) {
      $new_record = array(
        "tid" => (int)$questtid,
        "done" => (int)"1"
      );
      $db->update_query("quests", $new_record, "qid = '".(int)$questid."'");
    }
  }

  // Aktive Miniquests auslesen
  $mini_count = $db->fetch_field($db->query("
  SELECT COUNT(*) as quest FROM ".TABLE_PREFIX."miniquests
  WHERE claimedby = '".$uid."'
  AND doneby = ''"), "quest");

  $query = $db->query("
  SELECT * FROM ".TABLE_PREFIX."miniquests
  WHERE claimedby = '".$uid."'
  AND doneby = ''");

  while($quests = $db->fetch_array($query)) {
    eval("\$quests_active_bit .= \"".$templates->get("quests_active_bit")."\";");
  }

  // Aktive Börsen-Quests auslesen
  $query = $db->query("
  SELECT * FROM ".TABLE_PREFIX."quests
  WHERE uids != ''
  AND done != '1'
  ORDER BY ".TABLE_PREFIX."quests.qid DESC");

  $market_count = "0";
  while($mquests = $db->fetch_array($query)) {
    $uids = explode(", ", $mquests['uids']);
    $count_uids = count($uids);
    $username_list = "";
    if($mquests['maxcount'] <= $count_uids) {
      foreach($uids as $userid) {
        $username = $db->fetch_field($db->query("SELECT username FROM ".TABLE_PREFIX."users WHERE uid = '".(int)$userid."'"), "username");
        $username = build_profile_link($username, $userid);
        $username_list .= "$username &raquo; ";
      }
      $mquests['uids'] = ", ".$mquests['uids'].", ";
      if(preg_match("/, $uid,/i", $mquests['uids'])) {
        $market_count++;
        eval("\$quests_active_market_bit .= \"".$templates->get("quests_active_market_bit")."\";");
      }
    }
    else { $quests_active_market_bit = " "; }
  }


  eval("\$page = \"".$templates->get("quests_active")."\";");
  output_page($page);
}

// Quest-Übersicht - erledigte
if($action == "done") {

  // Keine Ansicht für Gäste
  if(!$uid) {
    error_no_permission();
  }

  // Erledigte Miniquests auslesen
  $mini_count = $db->fetch_field($db->query("
  SELECT COUNT(*) as quest FROM ".TABLE_PREFIX."miniquests
  WHERE claimedby = '".(int)$uid."'
  AND pid != ''"), "quest");

  $query = $db->query("
  SELECT *, ".TABLE_PREFIX."miniquests.pid FROM ".TABLE_PREFIX."miniquests
  LEFT JOIN ".TABLE_PREFIX."posts ON ".TABLE_PREFIX."posts.pid = ".TABLE_PREFIX."miniquests.pid
  WHERE claimedby = '".(int)$uid."'
  AND ".TABLE_PREFIX."miniquests.pid != ''");

  while($quests = $db->fetch_array($query)) {
    $quests['pid'] = "<a href=\"showthread.php?tid=$quests[tid]&pid=$quests[pid]#pid$quests[pid]\" target=\"blank\">{$lang->read_post}</a>";
    eval("\$quests_done_bit .= \"".$templates->get("quests_done_bit")."\";");
  }

  // Erledigte Börsenquests auslesen
    $query = $db->query("
    SELECT * FROM ".TABLE_PREFIX."quests
    WHERE accepted = '1'
    ORDER BY ".TABLE_PREFIX."quests.qid DESC");

    $market_count = "0";
    while($mquests = $db->fetch_array($query)) {
      $mquests['tid'] = "<a href=\"showthread.php?tid={$mquests['tid']}\" target=\"blank\">{$lang->read_topic}</a>";
      $uids = explode(", ", $mquests['uids']);
      $count_uids = count($uids);
      $username_list = "";
      if($mquests['maxcount'] <= $count_uids) {
        foreach($uids as $userid) {
          $username = $db->fetch_field($db->query("SELECT username FROM ".TABLE_PREFIX."users WHERE uid = '".(int)$userid."'"), "username");
          $username = build_profile_link($username, $userid);
          $username_list .= "$username &raquo; ";
        }
        $mquests['uids'] = ", ".$mquests['uids'].", ";
        if(preg_match("/, $uid,/i", $mquests['uids'])) {
          $market_count++;
          eval("\$quests_done_market_bit .= \"".$templates->get("quests_done_market_bit")."\";");
        }
      }
      else { $quests_active_bit = " "; }
    }

  eval("\$page = \"".$templates->get("quests_done")."\";");
  output_page($page);

}

// Quest-Übersicht - eingesandte
if($action == "mine") {

  // Keine Ansicht für Gäste
  if(!$uid) {
    error_no_permission();
  }

  // Quests auslesen
  $mini_count = $db->fetch_field($db->query("
  SELECT COUNT(*) as quest FROM ".TABLE_PREFIX."miniquests
  WHERE addedby = '".(int)$uid."'"), "quest");

  $query = $db->query("
  SELECT *, ".TABLE_PREFIX."miniquests.pid FROM ".TABLE_PREFIX."miniquests
  LEFT JOIN ".TABLE_PREFIX."posts ON ".TABLE_PREFIX."miniquests.pid = ".TABLE_PREFIX."posts.pid
  WHERE addedby = '".(int)$uid."'
  ORDER BY qid DESC");

  while($quests = $db->fetch_array($query)) {
    // Quest-Status
    if(empty($quests['pid'])) {
      $quests['pid'] = "Nicht gelöst";
    }
    else {
      $quests['pid'] =  "<a href=\"showthread.php?tid=$quests[tid]&pid=$quests[pid]#pid$quests[pid]\" target=\"blank\">{$lang->read_post}</a>";
    }
    eval("\$quests_mine_bit .= \"".$templates->get("quests_mine_bit")."\";");
  }
  eval("\$page = \"".$templates->get("quests_mine")."\";");
  output_page($page);
}

// Miniquests hinzufügen
if($action == "add") {

  // Keine Ansicht für Gäste
  if(!$uid) {
    error_no_permission();
  }

  // Miniquest in die Datenbank eintragen
  if(isset($_POST['submit'])) {
    $quest = $mybb->get_input('quest');
    $new_record = array(
      "taken" => "0",
      "done" => "0",
      "quest" => $db->escape_string($quest),
      "addedby" => $db->escape_string($uid),
      "accepted" => "0"
    );
    $db->insert_query('miniquests', $new_record);
  }

  // Template laden
  eval("\$page = \"".$templates->get("quests_add")."\";");
  output_page($page);
}

// Miniquests annehmen (User)
if($action == "miniquests") {

  // Keine Ansicht für Gäste
  if(empty($uid)) {
    error_no_permission();
  }

  // Zufällige Quest auslesen
  $query = $db->query("
    SELECT * FROM ".TABLE_PREFIX."miniquests
    WHERE taken = '0'
    AND claimedby = ''
    AND accepted = '1'
    ORDER BY rand(), ".TABLE_PREFIX."miniquests.qid
    LIMIT 1
  ");

  while($quests = $db->fetch_array($query)) {
    $quest = $db->escape_string($quests[quest]);
    $questid = (int)$quests[qid];
  }

  eval("\$page = \"".$templates->get("quests_miniquests")."\";");
  output_page($page);
}

// TEAM-OPTIONEN

// Eingesandte Würfel-Quest akzeptieren/ablehnen
if($action == "acceptquests") {

  // Kein Teammitglied? Kein Zugriff!
  if($team != "1") {
    error_no_permission();
  }

  // Quest akzeptieren
	if(isset($_POST['accept'])) {
	$questid = $mybb->get_input('id');
  $new_record = array(
  	"accepted" => "1"
  );
  $db->update_query("miniquests", $new_record, "qid = '".$questid."'");
  }

  // Quest ablehnen
  if(isset($_POST['decline'])) {
	$questid = $mybb->get_input('id');
  $db->delete_query("miniquests", "qid = '".$questid."'");
  }

  // Zu akzeptierende Quests auslesen
	$query = $db->query("
    SELECT * FROM ".TABLE_PREFIX."miniquests
    LEFT JOIN ".TABLE_PREFIX."users ON ".TABLE_PREFIX."users.uid = ".TABLE_PREFIX."miniquests.addedby
	  WHERE accepted = '0' AND claimedby = ''
	  ORDER by qid ASC");

	while( $quest = $db->fetch_array($query)) {
			$addedby = (int)$quest['addedby'];
			$quest['addedby'] = build_profile_link($quest['username'], $quest['addedby']);
			eval("\$quests_accept_bit .= \"".$templates->get("quests_accept_bit")."\";");
		}

	eval("\$page = \"".$templates->get("quests_accept")."\";");
	output_page($page);
}

// Erledigte Würfel-Quest akzeptieren/ablehnen
if($action == "acceptdonequests") {

  // Kein Teammitglied? Kein Zugriff!
  if($team != "1") {
    error_no_permission();
  }

  // Quest ablehnen
  if(isset($_POST['decline'])) {
	   $questid = $mybb->get_input('id');
	    $new_record = array(
		      "pid" => "",
		      "doneby" => "",
	     );
	    $db->update_query("miniquests", $new_record, "qid = '".(int)$questid."'");

      // Automatische PN verschicken
      $subject = "{$lang->quests_pn_declined_subject}";
      $message = "{$lang->quests_pn_declined_text}";
      require_once MYBB_ROOT . "inc/datahandlers/pm.php";
      $pmhandler = new PMDataHandler();

      $pm = array(
        "subject" => $subject,
        "message" => $message,
        "fromid" => $uid,
        "toid" => (int)$mybb->get_input('doneby')
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
  }

  // Börsenquest ablehnen
  if(isset($_POST['decline_market'])) {
     $questid = $mybb->get_input('id');
     $uids = $mybb->get_input('uids');
     $uids_liste = explode(", ", $uids);
     foreach($uids_liste as $userid) {
       $new_record = array(
          "done" => "0",
        );
        $db->update_query("quests", $new_record, "qid = '".(int)$questid."'");

        // Automatische PN verschicken
        $subject = "{$lang->quests_pn_declined_subject}";
        $message = "{$lang->quests_pn_declined_text}";
        require_once MYBB_ROOT . "inc/datahandlers/pm.php";
        $pmhandler = new PMDataHandler();

        $pm = array(
          "subject" => $subject,
          "message" => $message,
          "fromid" => $uid,
          "toid" => (int)$userid
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
     }
  }

  // Quests annehmen
  if(isset($_POST['accept'])) {
    $questid = $mybb->get_input('id');
    $new_record = array(
      "accepted" => "1",
      "done" => "1"
    );
    $db->update_query("miniquests", $new_record, "qid = '".(int)$questid."'");
  }

  if(isset($_POST['accept_market'])) {
    $questid = $mybb->get_input('id');
    $new_record = array(
      "accepted" => "1"
    );
    $db->update_query("quests", $new_record, "qid = '".(int)$questid."'");
  }


  // Quests auslesen
  $query = $db->query("
  SELECT * FROM ".TABLE_PREFIX."miniquests
  LEFT JOIN ".TABLE_PREFIX."posts ON ".TABLE_PREFIX."posts.pid = ".TABLE_PREFIX."miniquests.pid
  WHERE accepted = '0' AND doneby != ''
  ORDER by qid ASC");

  while( $quest = $db->fetch_array($query)) {
    $postid = $quest['pid'];
    $quest['pid'] = "<a href=\"showthread.php?tid=$quest[tid]&pid=$quest[pid]#pid$quest[pid]\" target=\"blank\">{$lang->read_post}</a>";
    $bydone = $quest['doneby'];
    $quest['doneby'] = build_profile_link($quest['username'], $quest['doneby']);
    eval("\$quests_accept_done_bit .= \"".$templates->get("quests_accept_done_bit")."\";");
  }

  // Börsenquests auslesen
  $query = $db->query("
  SELECT * FROM ".TABLE_PREFIX."quests
  WHERE done = '1'
  AND accepted = '0'
  ORDER BY qid DESC");
  while($mquest = $db->fetch_array($query)) {
    $tid = $mquest['tid'];
    $mquest['tid'] = "<a href=\"showthread.php?tid={$tid}\" target=\"blank\">{$lang->read_topic}</a>";
    $uids = explode(", ", $mquest['uids']);
    $username_list = "";
    foreach($uids as $userid) {
      $username = $db->fetch_field($db->query("SELECT username FROM ".TABLE_PREFIX."users WHERE uid = '".(int)$userid."'"), "username");
      $username = build_profile_link($username, $userid);
      $username_list .= "$username &raquo; ";
      }
    eval("\$quests_accept_done_market_bit .= \"".$templates->get("quests_accept_done_market_bit")."\";");
  }

  eval("\$page = \"".$templates->get("quests_accept_done")."\";");
  output_page($page);

}

// Quest an User verschicken
if($action == "senduser") {

  // Kein Teammitglied? Kein Zugriff!
  if($team != "1") {
    error_no_permission();
  }

  // Quest in Datenbank schreiben
  if(isset($_POST['senduser'])) {
    $quest = $mybb->get_input('quest');
    $extra = $mybb->get_input('extra');
    $uids_list = $_POST['uids'];
    $uids = implode(", ", $uids_list);

    $new_record = array(
      "quest" => $db->escape_string($quest),
      "extra" => $db->escape_string($extra),
      "uids" => $db->escape_string($uids),
      "sentby" => (int)$uid
    );
    $db->insert_query("quests", $new_record);

    // Beteiligten Usern eine Benachrichtigung per PN schicken
    foreach($uids_list as $toid) {
      $subject = "{$lang->quests_pn_sent_subject}";
      $message = "{$lang->quests_pn_sent_text}";
      require_once MYBB_ROOT . "inc/datahandlers/pm.php";
      $pmhandler = new PMDataHandler();

      $pm = array(
        "subject" => $db->escape_string($subject),
        "message" => $db->escape_string($message),
        "fromid" => $uid,
        "toid" => (int)$toid
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
    }
  }

  // User-Liste Auswahlmenü
  $query = $db->query("
  SELECT uid, username FROM ".TABLE_PREFIX."users
  ORDER BY ".TABLE_PREFIX."users.username ASC");
  while($users = $db->fetch_array($query)) {
    $senduser_bit .= "<option value=\"{$users['uid']}\">{$users['username']}</option>";
  }

  eval("\$page = \"".$templates->get("quests_senduser")."\";");
  output_page($page);
}

?>
