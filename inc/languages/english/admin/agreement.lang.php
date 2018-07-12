<?php
###################################
# Plugin Agreement for MyBB*#
# (c) 2018 by doylecc    #
# Website: http://mybbplugins.tk #
###################################

$l['ag_name'] = "Terms of Use Agreement";
$l['ag_name_descr'] = "Requires user consent to the current Terms of Use and manages the duration of storing IP addresses.<br />";
$l['ag_group_title'] = "Agreement to Terms of Use";
$l['ag_group_descr'] = "Settings for accepting the current Terms of Use.";

$l['ag_force_title'] = "Approval of already registered users.";
$l['ag_force_descr'] = "Do already registered users have to agree to the current terms of use in order to continue using the forum?";
$l['ag_repeat_title'] = "Repeated Agreement";
$l['ag_repeat_descr'] = "Should users be allowed to repeatedly agree to the terms of use at any time on their own? (Default: no)";
$l['ag_allowedpages_title'] = "Allowed pages to view";
$l['ag_allowedpages_descr'] = "Please enter the pages that users who have not accepted the terms of use yet will be able to access without being redirected. (Insert as filename + parameters. Example: misc.php?action=help&hid=3) Please use a new line for every entry!";
$l['ag_usercp_title'] = "Display of approval in User-CP.";
$l['ag_usercp_descr'] = "Should the date of the agreement to the terms of use be displayed on the summary page of the User-CP?";
$l['ag_profile_title'] = "Display of approval on profile page.";
$l['ag_profile_descr'] = "Display the date of the agreement to the terms of use for the corresponding user and for administrators on the profile page?";
$l['ag_lang_title'] = "Using own language file";
$l['ag_lang_descr'] = "Is a custom, self-created language file used on the terms of use page?";
$l['ag_langtitle_title'] = "Title of the language file";
$l['ag_langtitle_descr'] = "Please enter the name of the language file without endings. (Example: for mylanguagefile.lang.php you have to insert mylanguagefile.)";
$l['ag_ipaddresses_title'] = "Duration of storing IP Addresses";
$l['ag_ipaddresses_descr'] = "How long should the IP addresses be stored in posts, private messages, polls and logs?";
$l['ag_ipaddresses_users_title'] = "Duration of storing Registration IP Addresses and last IP Addresses";
$l['ag_ipaddresses_users_descr'] = "Should the registration IP and last IP of users be deleted according to the maximum storage duration?";
$l['ag_ipaddresses_ratings_title'] = "Removal of IP Addressen of Thread Ratings";
$l['ag_ipaddresses_ratings_descr'] = "Should the IP addresses of thread ratings be deleted regularly? (A time-dependent deletion is not possible here)";
$l['ag_ip_del_user_title'] = "Removal of IP addresses in case of user deletion.";
$l['ag_ip_del_user_descr'] = "Should the user's IP addresses stored for posts, private messages, polls, and logs be removed when the user is deleted?";
$l['ag_guest_post_title'] = "Accept terms of use for guest posts";
$l['ag_guest_post_descr'] = "Should guests have to agree to the Terms of Use to be able to send posts?";
$l['ag_tou_title'] = "Manage Terms of Use in Admin CP.";
$l['ag_tou_descr'] = "Do you want to create/edit the Terms of Service in the <a href=\"index.php?module=config-termsofuse\">Admin CP</a>, and then replace the terms from the language files on the page?";

$l['ag_never'] = "Never";
$l['ag_day'] = "1 Day";
$l['ag_threedays'] = "3 Days";
$l['ag_week'] = "1 Week";
$l['ag_twoweeks'] = "2 Weeks";
$l['ag_month'] = "4 Weeks";
$l['ag_tenweeks'] = "10 Weeks";
$l['ag_halfyear'] = "6 Months";
$l['ag_year'] = "1 Year";
$l['ag_forever'] = "Unlimited";

$l['ag_reset_confirm'] = "Reset all user agreements to Terms of Use now?";
$l['ag_reset_success'] = "All user agreements to Terms of Use reset successfully!";
$l['ag_reset'] = "Reset all user agreements!";

$l['ag_task_name'] = "Purge IP Addresses!";
$l['ag_task_description'] = "Clears the IP addresses of posts, private messages, polls, and logs when they reach the maximum storage time set in the preferences.";
$l['ag_task_run'] = "IP addresses purged successfully!";

$l['ag_can_edit'] = "Can edit Terms of Use.";
$l['ag_tou_menu'] = "Terms of Use";

$l['ag_tou_manage'] = "Manage Terms of Use";
$l['ag_tou_manage_desc'] = "Edit the Terms of Use displayed in the forum";
$l['ag_tou_add'] = "Add Terms of Use";
$l['ag_tou_add_desc'] = "Add Terms of Use for another language pack";
$l['ag_tou_manage_title'] = "Title";
$l['ag_tou_manage_title_desc'] = "Please enter the title of the Terms of Use to be displayed in the title";
$l['ag_tou_language'] = "Language";
$l['ag_tou_language_desc'] = "Please enter the title of the language pack (e.g. french)";
$l['ag_tou_terms'] = "Text of the Terms of Use";
$l['ag_tou_terms_desc'] = "Please enter the Terms of Use to be displayed in the forum for this language. (No HTML!)";
$l['ag_tou_options'] = "Options";
$l['ag_tou_edit'] = "Edit Terms";
$l['ag_tou_edit_desc'] = "Here you can edit the Terms of Use";
$l['ag_tou_delete'] = "Delete Terms";
$l['ag_tou_date'] = "Creation Date";
$l['ag_tou_date_desc'] = "Please enter the date (in the form DD.MM.YYYY) from which these terms are valid.";
$l['ag_tou_datenow'] = "Today's Date";
$l['ag_tou_datenow_desc'] = "Should today's date be saved as creation date? The date entered above will be ignored.";
$l['ag_tou_datenow_save'] = "Use Today's Date";

$l['ag_tou_add_submit'] = "Add Terms";
$l['ag_tou_edit_submit'] = "Save Terms";
$l['ag_tou_add_success'] = "New Terms of Use added!";
$l['ag_tou_empty_field'] = "Please fill in the following field: ";
$l['ag_tou_lang_installed'] = "This language pack is not installed: ";
$l['ag_tou_lang_exists'] = "Terms of use are already stored for this language pack: ";
$l['ag_tou_edit_success'] = "Terms of Use successfully edited";
$l['ag_tou_delete_invalid'] = "Selected Terms of Use not found!";
$l['ag_tou_delete_success'] = "Selected Terms of Use successfully deleted!";
$l['ag_tou_delete_confirm'] = "Delete selected terms of use?";
$l['ag_tou_deactivated'] = "The display of these Terms of Use is currently deactivated in the <a href=\"index.php?module=config-settings&amp;action=change&amp;search=UserAgreement\" target=\"_blank\" rel=\"noopener\">settings</a>!";
