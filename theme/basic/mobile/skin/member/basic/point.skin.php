<?php
if (!defined('_GNUBOARD_')) exit; // Unable to access direct pages

// add_stylesheet('css file path', Output order); Smaller numbers printed first
add_stylesheet('<link rel="stylesheet" href="'.$member_skin_url.'/style.css">', 0);
?>

<!-- Start point list { -->
<div id="point" class="new_win">
    <h1 id="win_title"><?php echo $gml['title'] ?></h1>

    <div class="new_win_con">
    	<p class="point_all">
    		<span class="point_all_tit"><?php e__('MY Point'); ?></span>
    		<span class="point_all_cnt"><i class="fa fa-1x fa-product-hunt" aria-hidden="true"></i> <?php echo $show_total_point ?></span>
    	</p>

        <ul id="point_ul">
            <?php for ($i=0; $i<count($list); $i++) { ?>
            <li>
                <div class="point_cnt">
                    <span class="point_num<?php echo ($list[$i]['po_point'] > 0 ? '' : ' point_num_sbt') ?>"><?php echo $list[$i]['show_point'] ?></span>
                    <span class="point_log"><?php echo $list[$i]['po_content'] ?></span>
                </div>
                <div class="point_date">
                	<i class="fa fa-clock-o" aria-hidden="true"></i> <?php echo conv_date_format('y-m-d H시', $list[$i]['po_datetime']); ?>
                    <span class="point_expdate<?php echo $list[$i]['expr'] ?>"><?php echo $list[$i]['show_expired_date'] ?></span>
                </div>
            </li>
            <?php } ?>
            <?php echo $no_list ?>
        </ul>

        <div class="point_status">
            <p class="point_status_add"><span><?php e__('Points Recieved'); ?></span><b class="sum_val"><?php echo $sum_point1; ?></b></p>
			<p class="point_status_sbt"><span><?php e__('Points Used'); ?></span><b class="sum_val"><?php echo $sum_point2; ?></b></p>
        </div>

        <?php echo $point_paging ?>

        <div class="win_btn">
        	<button type="button" onclick="javascript:window.close();" class="btn_close"><?php e__('Close Window'); ?></button>
        </div>
    </div>
</div>
<!-- } End point list -->
