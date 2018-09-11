<?php
if (!defined("_GNUBOARD_")) exit; // Unable to access direct pages
?>

<section id="bo_v_ans">
    <h2><span class="bo_v_reply"><?php e__('Reply'); ?></span> <?php echo get_text($answer['qa_subject']); ?></h2>
    <div id="bo_v_ans_info">
    	<div class="bo_v_ans_r">
			<button class="ans_opt_btn"><span class="sound_only"><?php e__('Write Option Button'); ?></span><i class="fa fa-ellipsis-v" aria-hidden="true"></i></button>  
			<div class="ans_add">
				<?php if($answer_update_href) { ?>
		        <a href="<?php echo $answer_update_href; ?>"><?php e__('Edit'); ?><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
		        <?php } ?>
		        <?php if($answer_delete_href) { ?>
		        <a href="<?php echo $answer_delete_href; ?>" onclick="del(this.href); return false;"><?php e__('Delete'); ?><i class="fa fa-trash-o" aria-hidden="true"></i></a>
		        <?php } ?>	  
			</div>
		</div>
    	<div id="ans_datetime">
	        <i class="fa fa-clock-o" aria-hidden="true"></i> <?php echo $answer['qa_datetime']; ?>
	    </div>
    </div>
    
    <div id="ans_con">
        <?php echo get_view_thumbnail(conv_content($answer['qa_content'], $answer['qa_html']), $qaconfig['qa_image_width']); ?>
    </div>
</section>

<div class="bo_v_btm">
	<a href="<?php echo $rewrite_href; ?>" class="btn add_qa"><i class="fa fa-plus" aria-hidden="true"></i> <?php e__('Additional QA'); ?></a>  
</div>

<script>
// Writer Options
$(".ans_opt_btn").click(function(){
    $(".ans_add").fadeIn();
});

$(document).mouseup(function (e) {
	var container = $(".ans_add");
    if (!container.is(e.target) && container.has(e.target).length === 0){
    container.css("display","none");
    }
});
</script>    
