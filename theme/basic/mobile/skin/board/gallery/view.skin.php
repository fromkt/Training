<?php
if (!defined("_GNUBOARD_")) exit; // Unable to access direct pages

// add_stylesheet('css file path', Output order); Smaller numbers printed first
add_stylesheet('<link rel="stylesheet" href="'.$board_skin_url.'/style.css">', 0);
?>

<script src="<?php echo GML_JS_URL; ?>/viewimageresize.js"></script>

<!-- Start reading posts { -->
<article id="bo_v" style="width:<?php echo $width; ?>">
    <header>
        <h2 id="bo_v_title">
            <?php if ($category_name) { ?>
            <span class="bo_v_cate"><?php echo $view['ca_name']; // Category Output End ?></span>
            <?php } ?>
            <span class="bo_v_tit">
            <?php echo $show_wr_subject // Print a Subject for the posts ?></span>
        </h2>
        <div id="bo_v_info">
	        <h2><?php e__('Page Info'); ?></h2>
	        <span class="sound_only"><?php e__('Writer'); ?> </span><?php echo $view['name'] ?><span class="ip"><?php echo $show_ip_view ?></span>
	        <span class="sound_only"><?php e__('Hit'); ?></span><strong><i class="fa fa-eye" aria-hidden="true"></i> <?php echo sprintf(n__('%s Hit', '%s Hits', $show_hit_number), $show_hit_number); ?></strong>
	        <span class="sound_only"><?php e__('Date'); ?></span><i class="fa fa-clock-o" aria-hidden="true"></i> <?php echo $show_wr_datetime ?></span>
	    </div>
	    <div id="bo_v_option">
			<a href="#bo_vc" class="bo_vc_btn"><span class="sound_only"><?php e__('Comment'); ?></span><i class="fa fa-commenting-o" aria-hidden="true"></i> <?php echo $view['wr_comment'] ?><?php e__('Comments'); ?></a>
            <button class="btn_b03 btn_share" id="copy_post"><i class="fa fa-clipboard" aria-hidden="true"></i><span class="sound_only"><?php e__('Copy Post'); ?></span></button>
			<div id="bo_v_share">
    			<?php include_once(GML_SNS_PATH."/view.sns.skin.php"); ?>
	            <?php if ($scrap_href) { ?><a href="<?php echo $scrap_href;  ?>" target="_blank" class="btn_b03 btn_scrap" onclick="win_scrap(this.href); return false;"><i class="fa fa-thumb-tack" aria-hidden="true"></i><span class="sound_only"><?php e__('Scrap'); ?></span></a><?php } ?>
	        </div>
            <?php if($update_href || $delete_href || $copy_href || $move_href || $search_href) { ?>
	        <button class="bo_v_opt"><span class="sound_only">게시물 옵션</span><i class="fa fa-ellipsis-v"></i></button>
			<ul id="bo_v_opt">
	            <?php if ($update_href) { ?><li><a href="<?php echo $update_href ?>"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> <?php e__('Edit'); ?></a></li><?php } ?>
	            <?php if ($delete_href) { ?><li><a href="<?php echo $delete_href ?>" onclick="del(this.href); return false;"><i class="fa fa-trash-o" aria-hidden="true"></i> <?php e__('Delete'); ?></a></li><?php } ?>
	            <?php if ($copy_href) { ?><li><a href="<?php echo $copy_href ?>" onclick="board_move(this.href); return false;"><i class="fa fa-files-o" aria-hidden="true"></i> <?php e__('Copy'); ?></a></li><?php } ?>
	            <?php if ($move_href) { ?><li><a href="<?php echo $move_href ?>" onclick="board_move(this.href); return false;"><i class="fa fa-arrows" aria-hidden="true"></i> <?php e__('Move'); ?></a></li><?php } ?>
	            <?php if ($search_href) { ?><li><a href="<?php echo $search_href ?>"><?php e__('Search'); ?></a></li><?php } ?>
			</ul>
            <?php } ?>
		</div>
	</header>

    <section id="bo_v_atc">
        <h2 id="bo_v_atc_title"><?php e__('Content'); ?></h2>

        <?php if($file_view_thumbnail) { // Output images attached as files ?>
        <div id="bo_v_img">
            <?php echo $file_view_thumbnail ?>
        </div>
        <?php } ?>

        <!-- Start body content { -->
        <div id="bo_v_con"><?php echo get_view_thumbnail($view['content']); ?></div>
        <?php //echo $view['rich_content']; // If you are using the same code as {image:0} ?>
        <!-- } End body content -->

        <?php if ($is_signature) { ?>
            <p><?php echo $signature ?></p>
        <?php } ?>

        <!--  Start of good or bad { -->
        <?php if ( $good_href || $nogood_href) { ?>
        <div id="bo_v_act">
            <?php if ($good_href) { ?>
            <span class="bo_v_act_gng">
                <a href="<?php echo $good_href.'&amp;'.$qstr ?>" id="good_button"  class="bo_v_good"><i class="fa fa-thumbs-o-up" aria-hidden="true"></i> <span class="sound_only"><?php e__('Good'); ?></span><strong><?php echo $show_good_number ?></strong></a>
                <b id="bo_v_act_good"></b>
            </span>
            <?php } ?>
            <?php if ($nogood_href) { ?>
            <span class="bo_v_act_gng">
                <a href="<?php echo $nogood_href.'&amp;'.$qstr ?>" id="nogood_button" class="bo_v_nogood"><i class="fa fa-thumbs-o-down" aria-hidden="true"></i> <span class="sound_only"><?php e__('Bad'); ?></span><strong><?php echo $show_nogood_number ?></strong></a>
                <b id="bo_v_act_nogood"></b>
            </span>
            <?php } ?>
        </div>
        <?php } else {
            if($board['bo_use_good'] || $board['bo_use_nogood']) {
        ?>
        <div id="bo_v_act">
            <?php if($board['bo_use_good']) { ?><span class="bo_v_good"><i class="fa fa-thumbs-o-up" aria-hidden="true"></i> <span class="sound_only"><?php e__('Good'); ?></span><strong><?php echo $show_good_number ?></strong></span><?php } ?>
            <?php if($board['bo_use_nogood']) { ?><span class="bo_v_nogood"><i class="fa fa-thumbs-o-down" aria-hidden="true"></i> <span class="sound_only"><?php e__('Bad'); ?></span> <strong><?php echo $show_nogood_number ?></strong></span><?php } ?>
        </div>
        <?php
            }
        }
        ?>
        <!-- }  End of good or bad -->

        <!-- Start Attachment { -->
	    <?php if($exist_file) { ?>
	    <section id="bo_v_file">
	        <h2><?php e__('Attached file'); ?></h2>
	        <ul>
	        <?php // Variable file
	        for ($i=0; $i<count($view['file']); $i++) {
	            if (isset($view['file'][$i]['source']) && $view['file'][$i]['source'] && !$view['file'][$i]['view']) {
	         ?>
	            <li>
	                <a href="<?php echo $view['file'][$i]['href'];  ?>" class="view_file_download">
	                    <i class="fa fa-download" aria-hidden="true"></i>
	                    <strong><?php echo $view['file'][$i]['source'] ?></strong>
	                    <?php echo $view['file'][$i]['content'] ?> (<?php echo $view['file'][$i]['size'] ?>)
	                </a>
	                <span class="bo_v_file_cnt"><?php echo sprintf(n__('%s download', '%s downloads', $view['file'][$i]['download']), $view['file'][$i]['download']); ?></span> |
	                <span>DATE : <?php echo $view['file'][$i]['datetime'] ?></span>
	            </li>
	        <?php
	            }
	        }
	         ?>
	        </ul>
	    </section>
	    <?php } ?>
	    <!-- } End Attachment -->

	    <!-- Start Related Links { -->
	    <?php if($exist_link) { ?>
	    <section id="bo_v_link">
	        <h2><?php e__('Related Links'); ?></h2>
	        <ul>
	        <?php for ($i=1; $i<=count($view['link']); $i++) { ?>
	            <li>
	                <a href="<?php echo $view['link_href'][$i] ?>" target="_blank">
	                    <i class="fa fa-link" aria-hidden="true"></i>
	                    <strong><?php echo $view['link'][$i] ?></strong>
	                </a>
	                <span class="bo_v_link_cnt"><?php echo sprintf(n__('%s hit', '%s hits', $view['link_hit'][$i]), $view['link_hit'][$i]); ?></span>
	            </li>
	        <?php
	            }
	        }
	        ?>
	        </ul>
	    </section>
	    <!-- } End Related Links -->
    </section>

	<div class="btn_top top">
		<a href="<?php echo $list_href ?>" class="btn_b01"><?php e__('List'); ?></a>
	    <?php if ($reply_href) { ?><a href="<?php echo $reply_href ?>" class="btn_b05 btn"><?php e__('Reply'); ?></a><?php } ?>
	    <?php if ($write_href) { ?><a href="<?php echo $write_href ?>" class="btn_b02 btn"><?php e__('Write'); ?></a><?php } ?>
	</div>

    <?php if ($prev_href || $next_href) { ?>
    <ul class="bo_v_nb">
        <?php if ($prev_href) { ?><li class="bo_v_prev"><a href="<?php echo $prev_href ?>"><i class="fa fa-angle-up"></i> <?php e__('Prev'); ?></a></li><?php } ?>
        <?php if ($next_href) { ?><li class="bo_v_next"><a href="<?php echo $next_href ?>"><i class="fa fa-angle-down"></i> <?php e__('Next'); ?></a></li><?php } ?>
    </ul>
    <?php } ?>
    <?php
    // Display Comment
    include_once(GML_BBS_PATH.'/view_comment.php');
    ?>

</article>
<!-- } End reading posts -->
