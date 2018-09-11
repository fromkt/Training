<?php
if (!defined('_GNUBOARD_')) exit; // Unable to access direct pages

// add_stylesheet('css file path', Output order); Smaller numbers printed first
add_stylesheet('<link rel="stylesheet" href="'.$member_skin_url.'/style.css">', 0);
?>

<div id="point" class="new_win">
    <h1 id="win_title"><?php echo $gml['title'] ?></h1>

    <div class="new_win_con list_01">
    	<p class="point_all">
        	<span class="point_all_tit"><?php e__('MY Point'); ?></span>
        	<span class="point_all_cnt"><i class="fa fa-1x fa-product-hunt" aria-hidden="true"></i> <?php echo $show_total_point; ?></span>
		</p>

        <ul>
            <?php for ($i=0; $i<count($list); $i++) { ?>
            <li>
            	<span class="point_num<?php echo ($list[$i]['po_point'] > 0 ? '' : ' point_num_sbt') ?>"><?php echo $list[$i]['show_point'] ?></span>
				<span class="point_tit"><?php echo $list[$i]['po_content'] ?></span>

                <span class="point_date1">
                	<i class="fa fa-clock-o" aria-hidden="true"></i> <?php echo $list[$i]['po_datetime'] ?>
                	<span class="point_date<?php echo $list[$i]['expr_class'] ?>">
                		<?php echo $list[$i]['show_expired_date'] ?>
                	</span>
                </span>
            </li>
            <?php } ?>
            <?php echo $no_list ?>
		</ul>

        <div class="point_status">
            <h2 class="sound_only"><?php e__('Subtotal'); ?></h2>
            <p class="point_status_add"><span>지급포인트</span><b><?php echo $sum_point1; ?></p>
            <p class="point_status_sbt"><span>사용포인트</span><b><?php echo $sum_point2; ?></p>
        </div>
    </div>

    <?php echo $point_paging ?>

	<div class="win_btn">
    	<button type="button" onclick="javascript:window.close();" class="btn_close"><?php e__('Close Window'); ?></button>
	</div>
</div>
