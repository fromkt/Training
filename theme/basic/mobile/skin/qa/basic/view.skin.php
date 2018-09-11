<?php
if (!defined("_GNUBOARD_")) exit; // Unable to access direct pages

// add_stylesheet('css file path', Output order); Smaller numbers printed first
add_stylesheet('<link rel="stylesheet" href="'.$qa_skin_url.'/style.css">', 0);
?>

<script src="<?php echo GML_JS_URL; ?>/viewimageresize.js"></script>

<!-- Start Read post { -->
<article id="bo_v">
    <div class="bo_v_wr">
        <header>
            <h2 id="bo_v_title">
                <span><?php echo $view['category']; ?></span>
                <br>
                <?php echo $view['subject'];  ?>
            </h2>
        </header>
        <section id="bo_v_info">
            <h2><?php e__('Page info'); ?></h2>
            <span class="sound_only"><?php e__('Writer'); ?></span><strong><?php echo $view['name'] ?></strong>
            <span class="sound_only"><?php e__('Date'); ?></span><strong class="info_date"><i class="fa fa-clock-o" aria-hidden="true"></i> <?php echo $view['datetime']; ?></strong>
        	
        	<button class="bo_v_opt"><span class="sound_only"><?php e__('Post Options'); ?></span><i class="fa fa-ellipsis-v" aria-hidden="true"></i></button>
        	<ul id="bo_v_opt" class="bo_v_com">
	            <?php if ($update_href) { ?><li><a href="<?php echo $update_href ?>"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> <?php e__('Edit'); ?></a></li><?php } ?>
	            <?php if ($delete_href) { ?><li><a href="<?php echo $delete_href ?>" onclick="del(this.href); return false;"><i class="fa fa-trash-o" aria-hidden="true"></i> <?php e__('Delete'); ?></a></li><?php } ?>
	        </ul>
	        
        	<script>
		    $(".bo_v_opt").click(function(){
		        $("#bo_v_opt").fadeIn();

		    });

		    $(document).mouseup(function (e) {
		        var container = $("#bo_v_opt");
		        if (!container.is(e.target) && container.has(e.target).length === 0){
		        container.css("display","none");
		        }
		    });
			</script>
        </section>

        <?php if($view['email'] || $view['hp']) { ?>
        <section id="bo_v_contact">
            <h2><?php e__('Contact Info') ?></h2>
            <dl>
                <?php if($view['email']) { ?>
                <dt><i class="fa fa-envelope-o" aria-hidden="true"></i><span class="sound_only"><?php e__('E-mail'); ?></span></dt>
                <dd><?php echo $view['email']; ?></dd>
                <?php } ?>
                <?php if($view['hp']) { ?>
                <dt><i class="fa fa fa-phone" aria-hidden="true"></i><span class="sound_only"><?php e__('Mobile phone'); ?></span></dt>
                <dd><?php echo $view['hp']; ?></dd>
                <?php } ?>
            </dl>
        </section>
        <?php } ?>

        <section id="bo_v_atc">
            <h2 id="bo_v_atc_title"><?php e__('Content'); ?></h2>

            <?php if($file_view_thumbnail) { // Output images attached as files ?>
            <div id="bo_v_img">
                <?php echo $file_view_thumbnail ?>
            </div>
            <?php } ?>

            <!-- Start Content { -->
            <div id="bo_v_con"><?php echo get_view_thumbnail($view['content'], $qaconfig['qa_image_width']); ?></div>
            <!-- } End Content -->

            <?php if($view['qa_type']) { ?>
            <div><a href="<?php echo $rewrite_href; ?>" class="btn_b01"><?php e__('Other QA'); ?></a></div>
            <?php } ?>

            <?php if($view['download_count']) { ?>
            <!-- Start attached file { -->
            <section id="bo_v_file">
                <h2><?php e__('Attached file'); ?></h2>
                <ul>
                <?php for ($i=0; $i<$view['download_count']; $i++) { ?>
                    <li>
                        <a href="<?php echo $view['download_href'][$i];  ?>" class="view_file_download">
                            <span class="sound_only"><?php e__('Attached file'); ?></span>
                            <i class="fa fa-download" aria-hidden="true"></i>
                            <strong><?php echo $view['download_source'][$i] ?></strong>
                        </a>
                    </li>
                <?php } ?>
                </ul>
            </section>
            <!-- } End attached file -->
            <?php } ?>
        </section>
    </div>

    <!-- Start top bbs button { -->
    <div class="btn_top top">
        <ul>
             <li><a href="<?php echo $list_href ?>" class="btn_b01"><?php e__('List'); ?></a></li>
            <?php if ($write_href) { ?><li><a href="<?php echo $write_href ?>" class="btn_b02"><?php e__('Write'); ?></a></li><?php } ?>
        </ul>
    </div>
    <div id="bo_v_top">
        <?php if ($prev_href || $next_href) { ?>
        <ul class="bo_v_nb">
            <?php if ($prev_href) { ?><li><a href="<?php echo $prev_href ?>"><i class="fa fa-chevron-up" aria-hidden="true"></i> <?php e__('Prev'); ?></a></li><?php } ?>
            <?php if ($next_href) { ?><li><a href="<?php echo $next_href ?>"><i class="fa fa-chevron-down" aria-hidden="true"></i> <?php e__('Next'); ?></a></li><?php } ?>
        </ul>
        <?php } ?>
    </div>
    <!-- } End top bbs button -->

</article>
<!-- } End Read post -->

<?php
// Display answer if there is an answer in the question article ; print answer registration form if you are an administrator
if(!$view['qa_type']) {
    if($view['qa_status'] && $answer['qa_id'])
        include_once($qa_skin_path.'/view.answer.skin.php');
    else
        include_once($qa_skin_path.'/view.answerform.skin.php');
}

// Display any related QA
if($view['rel_count']) {
    include_once($qa_skin_path.'/rel_list.skin.php');
}
?>
