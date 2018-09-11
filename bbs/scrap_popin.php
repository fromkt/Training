<?php
include_once('./_common.php');

include_once(GML_PATH.'/head.sub.php');

if ($is_guest) {
    $href = './login.php?'.$qstr.'&amp;url='.urlencode(get_pretty_url($bo_table, $wr_id));
    $href2 = str_replace('&amp;', '&', $href);

echo '
    <script>
        alert("'.__('Only members can access it.').'");
        opener.location.href = "'.$href2.'";
        window.close();
    </script>
    <noscript>
    <p>'.__('Only members can access it.').'</p>
    <a href="'.$href.'">'.__('Login').'</a>
    </noscript>
';
    exit;
}

echo '
<script>
    if (window.name != "win_scrap") {
        alert("'.__('Please use it in the correct way.').'");
        window.close();
    }
</script>
';

if ($write['wr_is_comment'])
    alert_close(__('Comments can not be Scrap.'));

$sql = " select count(*) as cnt from {$gml['scrap_table']}
            where mb_id = '{$member['mb_id']}'
            and bo_table = '$bo_table'
            and wr_id = '$wr_id' ";
$row = sql_fetch($sql);
if ($row['cnt']) {
    $back_url = get_pretty_url($bo_table, $wr_id);

    echo '
    <script>
    if (confirm(\''.__('You already been Scrap.').'\n\n'.__('Do you want to confirm the scrap now?').'\'))
        document.location.href = \'./scrap.php\';
    else
        window.close();
    </script>
    <noscript>
    <p>'.__('You already been Scrap.').'</p>
    <a href="./scrap.php">'.__('Confirm Scrap').'</a>
    <a href="'.$back_url.'">'.__('Back').'</a>
    </noscript>';
    exit;
}

// load language for l10n
bind_lang_domain( 'default', get_path_lang_dir('skin', $member_skin_path.'/'.GML_LANG_DIR) );

include_once($member_skin_path.'/scrap_popin.skin.php');

include_once(GML_PATH.'/tail.sub.php');
?>
