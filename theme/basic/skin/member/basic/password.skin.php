<?php
if (!defined('_GNUBOARD_')) exit; // Unable to access direct pages
if ($w == 'd' || $w == 'x') $gml['title'] = ($w == 'x') ? __('Delete Comment') : __('Delete Post');
else $gml['title'] = $gml['title'];

// add_stylesheet('css file path', Output order); Smaller numbers printed first
add_stylesheet('<link rel="stylesheet" href="'.$member_skin_url.'/style.css">', 0);
?>

<!-- Start enter password { -->
<div id="pw_confirm" class="mbskin">
    <h1><?php echo $gml['title'] ?></h1>
    <p>
        <?php if ($w == 'u') { ?>
        <strong><?php e__('Only the author can modify the post.'); ?></strong>
        <?php e__('If you are the author, you can modify the text by entering the password you entered at the time of writing.'); ?>
        <?php } else if ($w == 'd' || $w == 'x') {  ?>
        <strong><?php e__('Only the author can modify the post.'); ?></strong>
        <?php e__('If you are the author, you can delete the post by entering the password you entered at the time of writing.'); ?>
        <?php } else { ?>
        <strong><?php e__('This is a secret post.'); ?></strong>
        <?php e__('Only authors and administrators can access it.'); ?><br> <?php e__('If you are, please enter your password.'); ?>
        <?php }  ?>
    </p>

    <form name="fboardpassword" action="<?php echo $action; ?>" method="post">
    <input type="hidden" name="w" value="<?php echo $w ?>">
    <input type="hidden" name="bo_table" value="<?php echo $bo_table ?>">
    <input type="hidden" name="wr_id" value="<?php echo $wr_id ?>">
    <input type="hidden" name="comment_id" value="<?php echo $comment_id ?>">
    <input type="hidden" name="sfl" value="<?php echo $sfl ?>">
    <input type="hidden" name="stx" value="<?php echo $stx ?>">
    <input type="hidden" name="page" value="<?php echo $page ?>">

    <fieldset>
        <label for="pw_wr_password" class="sound_only"><?php e__('Password'); ?><strong><?php e__('Required'); ?></strong></label>
        <input type="password" name="wr_password" id="password_wr_password" required class="frm_input required" size="15" maxLength="20" placeholder="<?php e__('Password'); ?>">
        <button type="submit" class="btn_submit"><?php e__('Confirm'); ?></button>
    </fieldset>
    </form>

</div>
<!-- } End enter password -->