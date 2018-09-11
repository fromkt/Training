<?php
if (!defined('_GNUBOARD_')) exit; // Unable to access direct pages

// add_stylesheet('css file path', Output order); Smaller numbers printed first
add_stylesheet('<link rel="stylesheet" href="'.$member_skin_url.'/style.css">', 0);
?>

<!-- Start verifying member password { -->
<div id="mb_confirm" class="mbskin">
    <h1><?php echo $gml['title'] ?></h1>

    <p>
        <strong><?php e__('Please enter your password again.'); ?></strong>
        <?php if ($url == 'member_leave.php') { ?>
        <?php e__('Enter your password and your membership withdrawal will be completed.'); ?>
        <?php } else { ?>
        <?php e__('To ensure your information is secure, we confirm your password once more.'); ?>
        <?php } ?>
    </p>

    <form name="fmemberconfirm" action="<?php echo $url ?>" onsubmit="return fmemberconfirm_submit(this);" method="post">
    <input type="hidden" name="mb_id" value="<?php echo $member['mb_id'] ?>">
    <input type="hidden" name="w" value="u">

    <fieldset>
        <?php e__('Member ID'); ?>
        <span id="mb_confirm_id"><?php echo $member['mb_id'] ?></span>
        <input type="password" name="mb_password" id="mb_confirm_pw" placeholder="<?php e__('Password'); ?>" required class="frm_input" size="15" maxLength="20">
        <input type="submit" value="<?php e__('Confirm'); ?>" id="btn_submit" class="btn_submit">
    </fieldset>

    </form>

</div>

<script>
function fmemberconfirm_submit(f)
{
    document.getElementById("btn_submit").disabled = true;

    return true;
}
</script>
<!-- } End verifying member password -->
