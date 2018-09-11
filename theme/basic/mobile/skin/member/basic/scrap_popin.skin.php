<?php
if (!defined('_GNUBOARD_')) exit; // Unable to access direct pages

// add_stylesheet('css file path', Output order); Smaller numbers printed first
add_stylesheet('<link rel="stylesheet" href="'.$member_skin_url.'/style.css">', 0);
?>

<!-- Start scrap { -->
<div id="scrap_do" class="new_win">
    <h1 id="win_title"><?php e__('Scrap'); ?></h1>

    <form name="f_scrap_popin" action="./scrap_popin_update.php" method="post">
    <input type="hidden" name="bo_table" value="<?php echo $bo_table ?>">
    <input type="hidden" name="wr_id" value="<?php echo $wr_id ?>">
	<div class="new_win_con">
	    <div class="form_01">
	        <h2 class="sound_only"><?php e__('Check Subject and Write Comment'); ?></h2>
	        <ul>
	            <li class="scrap_tit">
	                <span class="sound_only"><?php e__('Subject'); ?></span>
	                <?php echo get_text(cut_str($write['wr_subject'], 255)) ?>
	            </li>
	            <li>
	                <label for="wr_content"><?php e__('Write comments'); ?></label>
	                <textarea name="wr_content" id="wr_content"></textarea>
	            </li>
	        </ul>
	
	        <p class="win_desc">
	            <?php e__('You can clip and leave a comment of appreciation or encouragement.'); ?>
	        </p>
	
	        <div class="win_btn">
	            <button type="submit" class="btn_submit"><?php e__('Confirm scrap'); ?></button>
	        </div>
	    </div>
	</div>
    </form>
</div>
<!-- } End scrap -->
