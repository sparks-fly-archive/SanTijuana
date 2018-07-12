<?php
/**
 * agreement_ip.php
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
function task_agreement_ip($task)
{
    global $db, $mybb, $lang;

    // If IP adresses are stored for unlimited time, don't run this task
    if (!isset($mybb->settings['ag_ipaddresses'])
        || $mybb->settings['ag_ipaddresses'] == 'forever'
    ) {
        return;
    }

    // Load language file
    if (!$lang->ag_task_run) {
        $lang->load('agreement');
    }

    $timecut = 0;
    $rep_ids = array();
    $rep_posts = $excl = '';

    // Get the timecuts für deleting the IP's
    switch ($mybb->settings['ag_ipaddresses']) {
        case "never":
            $timecut = TIME_NOW;
            break;
        case "day":
            $timecut = TIME_NOW - 86400;
            break;
        case "threedays":
            $timecut = TIME_NOW - 259200;
            break;
        case "week":
            $timecut = TIME_NOW - 604800;
            break;
        case "twoweeks":
            $timecut = TIME_NOW - 1209600;
            break;
        case "month":
            $timecut = TIME_NOW - 2419200;
            break;
        case "tenweeks":
            $timecut = TIME_NOW - 6048000;
            break;
        case "halfyear":
            $timecut = TIME_NOW - 15768000;
            break;
        case "year":
            $timecut = TIME_NOW - 31536000;
            break;
    }

    // Don't delete IP's of reported posts
    $query = $db->query("
        SELECT DISTINCT id FROM ".TABLE_PREFIX."reportedcontent
        WHERE type = 'post' AND reportstatus = 0"
    );
    while ($reps = $db->fetch_array($query)) {
        $rep_ids[] = (int)$reps['id'];
    }
    $rep_posts = implode(',', $rep_ids);
    if (!empty($rep_posts)) {
        $excl .= "  AND pid NOT IN({$rep_posts})";
    }

    // Delete all IP's older than the timecut
    if ($mybb->version_code >= 1815) {
        $db->update_query('pollvotes', array('ipaddress' => ''), "dateline < {$timecut} AND ipaddress <> ''");
    }
    // Delete only from not reported posts
    $db->update_query('posts', array('ipaddress' => ''), "dateline < {$timecut}{$excl} AND ipaddress <> ''");
    $db->update_query('privatemessages', array('ipaddress' => ''), "dateline < {$timecut} AND ipaddress <> ''");
    $db->update_query('moderatorlog', array('ipaddress' => ''), "dateline < {$timecut} AND ipaddress <> ''");
    $db->update_query('adminlog', array('ipaddress' => ''), "dateline < {$timecut} AND ipaddress <> ''");
    $db->update_query('searchlog', array('ipaddress' => ''), "dateline < {$timecut} AND ipaddress <> ''");
    $db->update_query('spamlog', array('ipaddress' => '', 'email' => ''), "dateline < {$timecut} AND ipaddress <> ''");
    // Delete Reg-IP and Last-IP of Users if they are older than the timecut
    if (isset($mybb->settings['ag_ipaddresses_users']) && $mybb->settings['ag_ipaddresses_users'] == 1) {
        $db->update_query('users', array('regip' => '',), "regdate < {$timecut} AND regip <> ''");
        $db->update_query('users', array('lastip' => '',), "lastvisit < {$timecut} AND lastip <> ''");
    }
    // Since there is no timestamp delete all IP's from thread ratings?
    if (isset($mybb->settings['ag_ipaddresses_ratings']) && $mybb->settings['ag_ipaddresses_ratings'] == 1) {
        $db->update_query('threadratings', array('ipaddress' => ''), "ipaddress <> ''");
    }

    // Add an entry to the log
    add_task_log($task, $lang->ag_task_run);
}
