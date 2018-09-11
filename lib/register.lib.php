<?php
if (!defined('_GNUBOARD_')) exit;

function empty_mb_id($reg_mb_id)
{
    if (trim($reg_mb_id)=='')
        return __('Enter member ID.');
    else
        return '';
}

function valid_mb_id($reg_mb_id)
{
    if (preg_match("/[^0-9a-z_]+/i", $reg_mb_id))
        return __('Enter only alphabetic, numeric, and _ for member ID.');
    else
        return '';
}

function count_mb_id($reg_mb_id)
{
    if (strlen($reg_mb_id) < 3)
        return __('Please enter at least 3 characters for member ID.');
    else
        return '';
}

function exist_mb_id($reg_mb_id)
{
    global $gml;

    $reg_mb_id = trim($reg_mb_id);
    if ($reg_mb_id == "") return "";

    $sql = " select count(*) as cnt from `{$gml['member_table']}` where mb_id = '$reg_mb_id' ";
    $row = sql_fetch($sql);
    if ($row['cnt'])
        return __('Duplicate ID already exists.');
    else
        return '';
}

function reserve_mb_id($reg_mb_id)
{
    global $config;
    if (preg_match("/[\,]?{$reg_mb_id}/i", $config['cf_prohibit_id']))
        return __('This member ID can not be used as a word already reserved.');
    else
        return '';
}

function empty_mb_nick($reg_mb_nick)
{
    if (!trim($reg_mb_nick))
        return __('Please enter a nickname.');
    else
        return '';
}

function valid_mb_nick($reg_mb_nick)
{
    if (!check_string($reg_mb_nick, GML_HANGUL + GML_ALPHABETIC + GML_NUMERIC))
        return __('You can enter character, and numbers without spaces for nicknames.');
    else
        return '';
}

function count_mb_nick($reg_mb_nick)
{
    if (strlen($reg_mb_nick) < 4)
        return __('You can enter your nickname in more than 2 characters.');
    else
        return '';
}

function exist_mb_nick($reg_mb_nick, $reg_mb_id)
{
    global $gml;
    $row = sql_fetch(" select count(*) as cnt from {$gml['member_table']} where mb_nick = '$reg_mb_nick' and mb_id <> '$reg_mb_id' ");
    if ($row['cnt'])
        return __('The nickname already exists.');
    else
        return '';
}

function reserve_mb_nick($reg_mb_nick)
{
    global $config;
    if (preg_match("/[\,]?{$reg_mb_nick}/i", $config['cf_prohibit_id']))
        return __('Nickname can not be used as a word already reserved.');
    else
        return '';
}

function empty_mb_email($reg_mb_email)
{
    if (!trim($reg_mb_email))
        return __('Please enter your email address.');
    else
        return '';
}

function valid_mb_email($reg_mb_email)
{
    if (!preg_match("/([0-9a-zA-Z_-]+)@([0-9a-zA-Z_-]+)\.([0-9a-zA-Z_-]+)/", $reg_mb_email))
        return __('E-mail address is out of format.');
    else
        return '';
}

// 금지 메일 도메인 검사
function prohibit_mb_email($reg_mb_email)
{
    global $config;

    list($id, $domain) = explode("@", $reg_mb_email);
    $email_domains = explode("\n", trim($config['cf_prohibit_email']));
    $email_domains = array_map('trim', $email_domains);
    $email_domains = array_map('strtolower', $email_domains);
    $email_domain = strtolower($domain);

    if (in_array($email_domain, $email_domains))
        return sprintf(__('Mail %s is not available.'), $domain);

    return "";
}

function exist_mb_email($reg_mb_email, $reg_mb_id)
{
    global $gml;
    $row = sql_fetch(" select count(*) as cnt from `{$gml['member_table']}` where mb_email = '$reg_mb_email' and mb_id <> '$reg_mb_id' ");
    if ($row['cnt'])
        return __('This email address is already in use.');
    else
        return '';
}

function empty_mb_name($reg_mb_name)
{
    if (!trim($reg_mb_name))
        return __('Please enter a name.');
    else
        return '';
}

function valid_mb_name($mb_name)
{
    if (!check_string($mb_name, GML_HANGUL))
        return __('You can enter Korean characters only without spaces.');
    else
        return '';
}

function valid_mb_hp($reg_mb_hp)
{
    $reg_mb_hp = preg_replace("/[^0-9]/", "", $reg_mb_hp);
    if(!$reg_mb_hp)
        return __('Please enter your mobile phone number.');
    else {
        if(preg_match("/^01[0-9]{8,9}$/", $reg_mb_hp))
            return '';
        else
            return __('Please enter a valid mobile phone number.');
    }
}

function exist_mb_hp($reg_mb_hp, $reg_mb_id)
{
    global $gml;

    if (!trim($reg_mb_hp)) return "";

    $reg_mb_hp = hyphen_hp_number($reg_mb_hp);

    $sql = "select count(*) as cnt from {$gml['member_table']} where mb_hp = '$reg_mb_hp' and mb_id <> '$reg_mb_id' ";
    $row = sql_fetch($sql);

    if($row['cnt'])
        return ' '.__('Duplicate phone number already exists.').' '.$reg_mb_hp;
    else
        return '';
}

return;
?>