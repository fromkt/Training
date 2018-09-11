<?php
if (!defined("_GNUBOARD_")) exit; // Unable to access direct pages
?>

<section id="bo_v_ans" class="bo_v_wr">
    <h2><span class="tit_rpl"><?php e__('Reply'); ?></span><span><?php echo get_text($answer['qa_subject']); ?></span></h2>

    <div id="ans_datetime">
        <i class="fa fa-clock-o" aria-hidden="true"></i> <?php echo $answer['qa_datetime']; ?>
		<button class="bo_cv_opt"><span class="sound_only"><?php e__('Post Options'); ?></span><i class="fa fa-ellipsis-v" aria-hidden="true"></i></button>
		<ul id="bo_cv_opt">
			<?php if($answer_update_href) { ?><li><a href="<?php echo $answer_update_href; ?>"><?php e__('Edit'); ?><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a></li><?php } ?>
			<?php if($answer_delete_href) { ?><li><a href="<?php echo $answer_delete_href; ?>" onclick="del(this.href); return false;"><?php e__('Delete'); ?><i class="fa fa-trash-o" aria-hidden="true"></i></a></li><?php } ?>
		</ul>
    </div>
    
    <div id="ans_con">
        <?php echo get_view_thumbnail(conv_content($answer['qa_content'], $answer['qa_html']), $qaconfig['qa_image_width']); ?>
    </div>

    <div id="ans_add">   
        <a href="<?php echo $rewrite_href; ?>" class="btn_b02 btn"><?php e__('Additional QA'); ?></a>
    </div>
</section>

<script>
	$(".bo_cv_opt").click(function(){
	    $("#bo_cv_opt").fadeIn();

	});

	$(document).mouseup(function (e) {
	    var container = $("#bo_cv_opt");
	    if (!container.is(e.target) && container.has(e.target).length === 0){
	    container.css("display","none");
	    }
	});
</script>