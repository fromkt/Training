<?php
if (!defined('_GNUBOARD_')) exit;
define('GML_MYSQL_HOST', 'localhost');
define('GML_MYSQL_USER', 'gnum');
define('GML_MYSQL_PASSWORD', 'Zymp8QHQCRYLlAxE');
define('GML_MYSQL_DB', 'gnum');
define('GML_MYSQL_SET_MODE', true);

define('GML_TABLE_PREFIX', 'gml_');

$gml['write_prefix'] = GML_TABLE_PREFIX.'write_'; // Board page name prefix

$gml['auth_table'] = GML_TABLE_PREFIX.'auth'; // Admin Permission Settings Table
$gml['config_table'] = GML_TABLE_PREFIX.'config'; // Config table
$gml['group_table'] = GML_TABLE_PREFIX.'group'; // Bulletin Group Table
$gml['group_member_table'] = GML_TABLE_PREFIX.'group_member'; // Board Group + Member Table
$gml['board_table'] = GML_TABLE_PREFIX.'board'; // Board Settings Table
$gml['board_file_table'] = GML_TABLE_PREFIX.'board_file'; // Board Attachment Table
$gml['board_good_table'] = GML_TABLE_PREFIX.'board_good'; // Good OR BAD table
$gml['board_new_table'] = GML_TABLE_PREFIX.'board_new'; // New Post Table
$gml['login_table'] = GML_TABLE_PREFIX.'login'; // Login table (number of users)
$gml['mail_table'] = GML_TABLE_PREFIX.'mail'; // Member mail Table
$gml['member_table'] = GML_TABLE_PREFIX.'member'; // Member Table
$gml['memo_table'] = GML_TABLE_PREFIX.'memo'; // Memo Table
$gml['poll_table'] = GML_TABLE_PREFIX.'poll'; // Poll Table
$gml['poll_etc_table'] = GML_TABLE_PREFIX.'poll_etc'; // Voting Other Comments Table
$gml['point_table'] = GML_TABLE_PREFIX.'point'; // point Table
$gml['popular_table'] = GML_TABLE_PREFIX.'popular'; // Popular search word table
$gml['scrap_table'] = GML_TABLE_PREFIX.'scrap'; // Post Scrap Table
$gml['visit_table'] = GML_TABLE_PREFIX.'visit'; // Visitors table
$gml['visit_sum_table'] = GML_TABLE_PREFIX.'visit_sum'; // Total Visitors Table
$gml['uniqid_table'] = GML_TABLE_PREFIX.'uniqid'; // Table for creating uniqid values
$gml['autosave_table'] = GML_TABLE_PREFIX.'autosave'; // Board temp post save Table
$gml['cert_history_table'] = GML_TABLE_PREFIX.'cert_history'; // Member Certification History Table
$gml['qa_config_table'] = GML_TABLE_PREFIX.'qa_config'; // 1:1 inquiry setup table
$gml['qa_content_table'] = GML_TABLE_PREFIX.'qa_content'; // 1:1 inquiry table
$gml['content_table'] = GML_TABLE_PREFIX.'content'; // Content Information Table
$gml['faq_table'] = GML_TABLE_PREFIX.'faq'; // Frequently Asked Questions Table
$gml['faq_master_table'] = GML_TABLE_PREFIX.'faq_master'; // Frequently asked questions master table
$gml['new_win_table'] = GML_TABLE_PREFIX.'new_win'; // Manage Popup layer Table
$gml['menu_table'] = GML_TABLE_PREFIX.'menu'; // Manage Menu table
$gml['social_profile_table'] = GML_TABLE_PREFIX.'member_social_profiles'; // Manage Social Login Table
$gml['notice_table'] = GML_TABLE_PREFIX.'notice'; // Notification Table
$gml['multi_lang_data_table'] = GML_TABLE_PREFIX.'multi_lang_data'; // Multi language data Table
?>