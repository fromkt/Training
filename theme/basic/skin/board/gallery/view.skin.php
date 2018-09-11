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
            <span class="bo_v_tit"><?php echo $show_wr_subject; // Print a Subject for the posts ?></span>
        </h2>
    </header>

    <section id="bo_v_info">
        <h2><?php e__('Page Info'); ?></h2>
        <div class="bo_v_info_l">
        	<span class="sound_only"><?php e__('Writer'); ?></span> <strong><?php echo $view['name'] ?><?php echo $show_ip_view; ?></strong>
        	<span class="sound_only"><?php e__('Hit'); ?></span><strong><i class="fa fa-eye" aria-hidden="true"></i> <?php echo sprintf(n__('%s Hit', '%s Hits', $show_hit_number), $show_hit_number); ?></strong>
        	<span class="if_date"><span class="sound_only"><?php e__('Date'); ?></span><i class="fa fa-clock-o" aria-hidden="true"></i> <?php echo $show_wr_datetime; ?></span>
		</div>
		<div class="bo_v_info_r">
			<a href="#bo_vc" class="bo_vc_btn"><span class="sound_only"><?php e__('Comment'); ?></span><i class="fa fa-commenting-o" aria-hidden="true"></i> <?php echo sprintf(n__('%s Comment', '%s Comments', $show_cmt_number), $show_cmt_number); ?></a>
			<button class="btn_b03 btn_share" id="copy_post"><i class="fa fa-clipboard" aria-hidden="true"></i><span class="sound_only"><?php e__('Copy Post'); ?></span></button>
			<div id="bo_v_share">
				<?php include_once(GML_SNS_PATH."/view.sns.skin.php"); ?>
		        <?php if ($scrap_href) { ?><a href="<?php echo $scrap_href;  ?>" target="_blank" class="btn_b03 btn_scrap" onclick="win_scrap(this.href); return false;"><i class="fa fa-thumb-tack" aria-hidden="true"></i><span class="sound_only"><?php e__('Scrap'); ?></span></a><?php } ?>
		    </div>
            <?php if($update_href || $delete_href || $copy_href || $move_href || $search_href) { ?>
		    <button class="bo_v_opt"><span class="sound_only"><?php e__('Write Option Button'); ?></span><i class="fa fa-ellipsis-v"></i></button>
			<ul id="bo_v_opt">
	            <?php if ($update_href) { ?><li><a href="<?php echo $update_href ?>"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> <?php e__('Edit'); ?></a></li><?php } ?>
	            <?php if ($delete_href) { ?><li><a href="<?php echo $delete_href ?>" onclick="del(this.href); return false;"><i class="fa fa-trash-o" aria-hidden="true"></i> <?php e__('Delete'); ?></a></li><?php } ?>
	            <?php if ($copy_href) { ?><li><a href="<?php echo $copy_href ?>" onclick="board_move(this.href); return false;"><i class="fa fa-files-o" aria-hidden="true"></i> <?php e__('Copy'); ?></a></li><?php } ?>
	            <?php if ($move_href) { ?><li><a href="<?php echo $move_href ?>" onclick="board_move(this.href); return false;"><i class="fa fa-arrows" aria-hidden="true"></i> <?php e__('Move'); ?></a></li><?php } ?>
	            <?php if ($search_href) { ?><li><a href="<?php echo $search_href ?>"><i class="fa fa-search" aria-hidden="true"></i> <?php e__('Search'); ?></a></li><?php } ?>
	        </ul>
            <?php } ?>
		</div>
    </section>

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
                <a href="<?php echo $good_href.'&amp;'.$qstr ?>" id="good_button" class="bo_v_good"><?php e__('Good'); ?> <strong><?php echo $show_good_number ?></strong></a>
                <b id="bo_v_act_good"></b>
            </span>
            <?php } ?>
            <?php if ($nogood_href) { ?>
            <span class="bo_v_act_gng">
                <a href="<?php echo $nogood_href.'&amp;'.$qstr ?>" id="nogood_button" class="bo_v_nogood"><?php e__('Bad'); ?> <strong><?php echo $show_nogood_number ?></strong></a>
                <b id="bo_v_act_nogood"></b>
            </span>
            <?php } ?>
        </div>
        <?php
            } else {
                if($board['bo_use_good'] || $board['bo_use_nogood']) {
        ?>
        <div id="bo_v_act">
            <?php if($board['bo_use_good']) { ?><span class="bo_v_good"><?php e__('Good'); ?> <strong><?php echo $show_good_number ?></strong></span><?php } ?>
            <?php if($board['bo_use_nogood']) { ?><span class="bo_v_nogood"><?php e__('Bad'); ?> <strong><?php echo $show_nogood_number ?></strong></span><?php } ?>
        </div>
        <?php
                }
            }
        ?>
        <!-- } End of good or bad -->
    </section>

    <?php if($exist_file) { ?>
    <!-- Start Attachment { -->
    <section id="bo_v_file">
        <h2><?php e__('Attached file'); ?></h2>
        <ul>
        <?php // Variable file
        for ($i=0; $i<count($view['file']); $i++) {
            if (isset($view['file'][$i]['source']) && $view['file'][$i]['source'] && !$view['file'][$i]['view']) {
        ?>
            <li>
                <i class="fa fa-download" aria-hidden="true"></i>
                <a href="<?php echo $view['file'][$i]['href'];  ?>" class="view_file_download">
                    <strong><?php echo $view['file'][$i]['source'] ?></strong>
                </a>
                <?php echo $view['file'][$i]['content'] ?> (<?php echo $view['file'][$i]['size'] ?>)
                <span class="bo_v_file_cnt"><?php echo sprintf(n__('%s download', '%s downloads', $view['file'][$i]['download']), $view['file'][$i]['download']); ?> | DATE : <?php echo $view['file'][$i]['datetime']; ?></span>
            </li>
        <?php
            }
        }
         ?>
        </ul>
    </section>
    <!-- } End Attachment -->
    <?php } ?>

    <?php if($exist_link) { ?>
    <!-- Start Related Links { -->
    <section id="bo_v_link">
        <h2><?php e__('Related Links'); ?></h2>
        <ul>
        <?php for ($i=1; $i<=count($view['link']); $i++) { ?>
            <li>
                <i class="fa fa-link" aria-hidden="true"></i> <a href="<?php echo $view['link_href'][$i] ?>" target="_blank">

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

</article>
<!-- } End reading posts -->

<!-- Start Post bottom Button { -->
<div id="bo_v_btm">
    <ul class="bo_v_com">
       <li><a href="<?php echo $list_href ?>" class="btn_b01 btn"><?php e__('List'); ?></a></li>
        <?php if ($reply_href) { ?><li><a href="<?php echo $reply_href ?>" class="btn_b05 btn"><?php e__('Reply'); ?></a></li><?php } ?>
        <?php if ($write_href) { ?><li><a href="<?php echo $write_href ?>" class="btn_b02 btn"><?php e__('Write'); ?></a></li><?php } ?>
    </ul>
</div>
<!-- } End Post bottom Button -->

<?php if ($prev_href || $next_href) { ?>
<div class="bo_v_nb">
    <?php if ($prev_href) { ?>
    	<a href="<?php echo $prev_href ?>" class="btn_prv">
    		<span class="sound_only"><?php e__('Prev'); ?></span>
    		<i class="fa fa-chevron-left fa-2x" aria-hidden="true"></i>
    		<?php echo cut_str($prev_wr_subject, 15); ?>
    		<?php if ($prev_wr_comment) { ?>
	    		<span class="cnt_cmt"><?php echo $prev_wr_comment; ?></span>
			<?php } ?>
    	</a><?php } ?>
    <?php // echo $prev_wr_date ?>

    <?php if ($next_href) { ?>
    	<a href="<?php echo $next_href ?>" class="btn_next">
    		<span class="sound_only"><?php e__('Next'); ?></span>
    		<?php echo cut_str($next_wr_subject, 15); ?>
    		<?php if ($next_wr_comment) { ?>
    			<span class="cnt_cmt"><?php echo $next_wr_comment; ?></span>
    		<?php } ?>
    		<i class="fa fa-chevron-right fa-2x" aria-hidden="true"></i>
    	</a><?php } ?>
    <?php // echo $next_wr_date ?>
</div>
<?php } ?>

<?php
// Display Comment
include_once(GML_BBS_PATH.'/view_comment.php');
?>
