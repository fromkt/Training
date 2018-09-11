<?php
include_once('./_common.php');

if(defined('GML_THEME_PATH')) {
    $group_file = GML_THEME_PATH.'/group.php';
    if(is_file($group_file)) {
        require_once($group_file);
        return;
    }
    unset($group_file);
}

if (GML_IS_MOBILE) {
    include_once(GML_MOBILE_PATH.'/group.php');
    return;
}

if(!$is_admin && $group['gr_device'] == 'mobile')
    alert($group['gr_subject'].' '.__('Groups are accessible only from Mobile'));

$gml['title'] = $group['gr_subject'];
include_once('./_head.php');
include_once(GML_LIB_PATH.'/latest.lib.php');
?>

<div class="latest_wr">

<!-- Start Display new latest -->
<?php
//  최신글
$sql = " select bo_table, bo_subject
            from {$gml['board_table']}
            where gr_id = '{$gr_id}'
              and bo_list_level <= '{$member['mb_level']}'
              and bo_device <> 'mobile' ";
if(!$is_admin)
    $sql .= " and bo_use_cert = '' ";
$sql .= " order by bo_order ";
$result = sql_query($sql);
for ($i=0; $row=sql_fetch_array($result); $i++) {
    $lt_style = "";
    if ($i%2==1) $lt_style = "margin-left:2%";
    else $lt_style = "";
?>
    <div style="float:left;<?php echo $lt_style ?>"  class="lt_wr">
    <?php
    // 이 함수가 바로 최신글을 추출하는 역할을 합니다.
    // 사용방법 : latest(스킨, 게시판아이디, 출력라인, 글자수);
    echo latest('basic', $row['bo_table'], 6, 25);
    ?>
    </div>
<?php
}
?>
<!-- End Display new latest -->
</div>
<?php
include_once('./_tail.php');
?>
