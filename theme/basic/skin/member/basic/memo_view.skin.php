<?php
if (!defined('_GNUBOARD_')) exit; // Unable to access direct pages
$nick = get_sideview($mb['mb_id'], $mb['mb_nick'], $mb['mb_email'], $mb['mb_homepage']);
if($kind == "recv") {
    $kind_str = __('Sent');
    $kind_date = __('Received');
}
else {
    $kind_str = __('Received');
    $kind_date = __('Sent');
}

// add_stylesheet('css file path', Output order); Smaller numbers printed first
add_stylesheet('<link rel="stylesheet" href="'.$member_skin_url.'/style.css">', 0);
?>

<!-- Start Memo View { -->
<div id="memo_view" class="new_win">
    <h1 id="win_title"><?php echo $gml['title'] ?></h1>
    <div class="new_win_con">
        <!-- Start select Memo { -->
        <ul class="win_ul">
            <li class="<?php if ($kind == 'recv') {  ?>selected<?php } ?>"><a href="./memo.php?kind=recv"><?php e__('Received Memo'); ?></a></li>
            <li class="<?php if ($kind == 'send') {  ?>selected<?php } ?>"><a href="./memo.php?kind=send"><?php e__('Sent Memo'); ?></a></li>
            <li><a href="./memo_form.php"><?php e__('Write Memo'); ?></a></li>
        </ul>
        <!-- } End select Memo -->

        <article id="memo_view_contents">
            <header>
                <h2><?php e__('Memo Content'); ?></h2>
            </header>
            <ul id="memo_view_ul">
                <li class="memo_view_li memo_view_name">
                    <span class="memo_view_subj"><?php echo $kind_str; ?><?php e__('User'); ?></span>
                    <strong><?php echo $nick ?></strong>
                </li>
                <li class="memo_view_li memo_view_date">
                    <span class="sound_only"><?php echo $kind_date; ?><?php e__('Date'); ?></span>
                    <strong><i class="fa fa-clock-o" aria-hidden="true"></i> <?php echo $memo['me_send_datetime']; ?></strong>
                </li>
            </ul>
            <p>
                <?php echo conv_content($memo['me_memo'], 0); ?>
            </p>
        </article>


        <div class="win_btn memo_view_opt">
            <?php if($prev_link) {  ?>
            <a href="<?php echo $prev_link ?>" class="btn btn_b01"><?php e__('Prev'); ?></a>
            <?php }  ?>
            <?php if($next_link) {  ?>
            <a href="<?php echo $next_link ?>" class="btn btn_b01"><?php e__('Next'); ?></a>
            <?php }  ?>

            <a href="<?php echo $list_link ?>" class="btn btn_b01 btn_right"><?php e__('List'); ?></a>
            <?php if ($kind == 'recv') {  ?><a href="./memo_form.php?mb_hash=<?php echo get_string_encrypt($mb['mb_id']); ?>&amp;me_id=<?php echo $memo['me_id'] ?>" class="btn btn_b02 reply_btn"><?php e__('Reply'); ?></a><?php }  ?>
        </div>
        
        <div class="memo_view_btn">
        	<button type="button" onclick="window.close();" class="btn_close"><?php e__('Close window'); ?></button>  
        </div>
    </div>
</div>
<!-- } End Memo View -->