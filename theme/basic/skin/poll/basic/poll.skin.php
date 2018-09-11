<?php
if (!defined("_GNUBOARD_")) exit; // Unable to access direct pages

// add_stylesheet('css file path', Output order); Smaller numbers printed first
add_stylesheet('<link rel="stylesheet" href="'.$poll_skin_url.'/style.css">', 0);
?>

<!-- Start poll { -->
<form name="fpoll" action="<?php echo GML_BBS_URL ?>/poll_update.php" onsubmit="return fpoll_submit(this);" method="post">
<input type="hidden" name="po_id" value="<?php echo $po_id ?>">
<input type="hidden" name="skin_dir" value="<?php echo urlencode($skin_dir); ?>">
<section id="poll">
    <header>
        <h2><?php e__('Poll'); ?></h2>
        <?php if ($is_admin == "super") {  ?><a href="<?php echo GML_ADMIN_URL ?>/poll_form.php?w=u&amp;po_id=<?php echo $po_id ?>" class="btn_admin"><?php e__('Manage poll'); ?></a><?php }  ?>
    </header>
    <div class="poll_con">
        <p><?php echo $po['po_subject'] ?></p>
        <ul>
            <?php for ($i=1; $i<=9 && $po["po_poll{$i}"]; $i++) {  ?>
            <li><input type="radio" name="gb_poll" value="<?php echo $i ?>" id="gb_poll_<?php echo $i ?>"> <label for="gb_poll_<?php echo $i ?>"><?php echo $po['po_poll'.$i] ?></label></li>
            <?php }  ?>
        </ul>
        <div id="poll_btn">
            <a href="<?php echo GML_BBS_URL."/poll_result.php?po_id=$po_id&amp;skin_dir=".urlencode($skin_dir); ?>" target="_blank" onclick="poll_result(this.href); return false;" class="btn_result btn_b04"><?php e__('View Results'); ?></a>
            <button type="submit" class="btn_poll btn_b02"><?php e__('Vote'); ?></button>
        </div>
    </div>
</section>
</form>

<?php
get_localize_script('poll_skin',
array(
'check_msg1'=>__('Only Level %s or higher members are eligible to vote.'),  // 레벨 %s 이상의 회원만 투표에 참여하실 수 있습니다.
'check_msg2'=>__('Please select a survey item to vote for.'),    // 투표하실 설문항목을 선택하세요.
'check_msg3'=>__('Only members above level %s can view the results.'),    // 레벨 %s 이상의 회원만 결과를 보실 수 있습니다.
),
true);
?>
<script>
function fpoll_submit(f)
{
    <?php if ($member['mb_level'] < $po['po_level']) { ?>
    alert( js_sprintf(poll_skin.check_msg1, "<?php echo $po['po_level']; ?>") ); return false;
    <?php } ?>

    var chk = false;
    for (i=0; i<f.gb_poll.length;i ++) {
        if (f.gb_poll[i].checked == true) {
            chk = f.gb_poll[i].value;
            break;
        }
    }

    if (!chk) {
        alert(poll_skin.check_msg2);
        return false;
    }

    var new_win = window.open("about:blank", "win_poll", "width=616,height=500,scrollbars=yes,resizable=yes");
    f.target = "win_poll";

    return true;
}

function poll_result(url)
{
    <?php if ($member['mb_level'] < $po['po_level']) { ?>
    alert( js_sprintf(poll_skin.check_msg3, "<?php echo $po['po_level']; ?>") ); return false;
    <?php } ?>

    win_poll(url);
}
</script>
<!-- } End poll -->