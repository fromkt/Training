<?php
if (!defined("_GNUBOARD_")) exit; // Unable to access direct pages

// add_stylesheet('css file path', Output order); Smaller numbers printed first
add_stylesheet('<link rel="stylesheet" href="'.$qa_skin_url.'/style.css">', 0);
?>

<script src="<?php echo GML_JS_URL; ?>/viewimageresize.js"></script>

<!-- Start Read post { -->
<article id="bo_v">
    <header>
        <h2 id="bo_v_title">
            <span class="bo_v_cate"><?php echo $view['category']; ?></span>
            <?php echo $view['subject']; ?>
        </h2>
    </header>

    <section id="bo_v_info">
        <h2><?php e__('Page info'); ?></h2>
        <div class="bo_v_info_l">
        	<span class="sound_only"><?php e__('Writer'); ?></span>
        	<strong><?php echo $view['name'] ?></strong>
        	<span class="sound_only"><?php e__('Date'); ?></span>
        	<strong class="bo_date"><i class="fa fa-clock-o" aria-hidden="true"></i> <?php echo $view['datetime']; ?></strong>
	        <?php if($view['email'] || $view['hp']) { ?>
	            <?php if($view['email']) { ?>
	            <span class="sound_only"><?php e__('E-mail'); ?></span>
	            <strong><i class="fa fa-envelope-o" aria-hidden="true"></i> <?php echo $view['email']; ?></strong>
	            <?php } ?>
	            <?php if($view['hp']) { ?>
	            <span class="sound_only"><?php e__('Mobile phone'); ?></span>
	            <strong><i class="fa fa-phone" aria-hidden="true"></i> <?php echo $view['hp']; ?></strong>
	            <?php } ?>
	        <?php } ?>
	    </div>

	    <div class="bo_v_info_r">
	    	<button class="bo_v_opt_btn"><span class="sound_only"><?php e__('Write Option Button'); ?></span><i class="fa fa-ellipsis-v" aria-hidden="true"></i></button>
	    	<ul class="bo_v_opt_li">
		        <?php if ($update_href) { ?><li><a href="<?php echo $update_href ?>"><?php e__('Edit'); ?><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a></li><?php } ?>
		        <?php if ($delete_href) { ?><li><a href="<?php echo $delete_href ?>" onclick="del(this.href); return false;"><?php e__('Delete'); ?><i class="fa fa-trash-o" aria-hidden="true"></i></a></li><?php } ?>
		    </ul>
	    </div>
    </section>

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
        <div id="bo_v_addq"><a href="<?php echo $rewrite_href; ?>" class="btn_b01"><?php e__('Other QA'); ?></a></div>
        <?php } ?>

        <?php if($view['download_count']) { ?>
        <!-- Start attached file { -->
        <section id="bo_v_file">
            <h2><?php e__('Attached file'); ?></h2>
            <ul>
            <?php for ($i=0; $i<$view['download_count']; $i++) { ?>
                <li>
                    <i class="fa fa-download" aria-hidden="true"></i>
                    <a href="<?php echo $view['download_href'][$i];  ?>" class="view_file_download">
                        <strong><?php echo $view['download_source'][$i] ?></strong>
                    </a>
                </li>
            <?php } ?>
            </ul>
        </section>
        <!-- } End attached file -->
        <?php } ?>
    </section>
</article>
<!-- } End Read post -->

<!-- Start bottom bbs button { -->
<div id="bo_v_btm">
    <ul class="bo_v_com">
    	<li><a href="<?php echo $list_href ?>" class="btn_b01 btn"><?php e__('List'); ?></a></li>
    	<?php if ($write_href) { ?><li><a href="<?php echo $write_href ?>" class="btn_b02 btn"><?php e__('Write'); ?></a></li><?php } ?>
    </ul> 
</div>
<!-- } End bottom bbs button -->
    
<?php if ($prev_href || $next_href) { ?>
<div class="bo_v_nb">
    <?php if ($prev_href) { ?><a href="<?php echo $prev_href ?>" class="btn_prv"><i class="fa fa-chevron-left fa-2x" aria-hidden="true"></i> <?php e__('Prev'); ?></a><?php } ?>
    <?php if ($next_href) { ?><a href="<?php echo $next_href ?>" class="btn_next"><?php e__('Next'); ?> <i class="fa fa-chevron-right fa-2x" aria-hidden="true"></i></a><?php } ?>
</div>
<?php } ?> 

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

<script>
$(function() {
    $("a.view_image").click(function() {
        window.open(this.href, "large_image", "location=yes,links=no,toolbar=no,top=10,left=10,width=10,height=10,resizable=yes,scrollbars=no,status=no");
        return false;
    });

    // image resize
    $("#bo_v_atc").viewimageresize();
});


// Write Option Button
$(".bo_v_opt_btn").click(function(){
    $(".bo_v_opt_li").fadeIn();

    });

    $(document).mouseup(function (e) {
        var container = $(".bo_v_opt_li");
    if (!container.is(e.target) && container.has(e.target).length === 0){
    container.css("display","none");
    }
});
</script>
