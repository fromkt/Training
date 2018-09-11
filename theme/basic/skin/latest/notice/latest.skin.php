<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

// add_stylesheet('css 구문', 출력순서); 숫자가 작을 수록 먼저 출력됨
add_stylesheet('<link rel="stylesheet" href="'.$latest_skin_url.'/style.css">', 0);
add_javascript('<script src="'.GML_JS_URL.'/jquery.bxslider.js"></script>', 10);
?>

<div class="notice">
    <h2>
    	<a href="<?php echo $see_more_href ?>">
    		<i class="fa fa-bullhorn" aria-hidden="true"></i><span class="sound_only"><?php echo $bo_subject; ?></span>
    	</a>
    </h2>
    <ul>
    <?php for ($i=0; $i<count($list); $i++) {  ?>
        <li>            
            <?php if ($list[$i]['icon_secret']) { ?>
	            <i class="fa fa-lock" aria-hidden="true"></i><span class="sound_only"><?php e__('Secret'); ?></span>
	        <?php } ?>
			
			<a href="<?php echo $list[$i]['href'] ?>" class="lt_tit">

            	<?php echo $list[$i]['show_subject'] ?>
		        
		        <?php if ($list[$i]['icon_new']) { ?>
	            <span class="new_icon">N<span class="sound_only"><?php e__('New'); ?></span></span>
		        <?php } ?>
		
		        <?php if ($list[$i]['icon_hot']) { ?>
		            <span class="hot_icon">H<span class="sound_only"><?php e__('Hot'); ?></span></span>
		        <?php } ?>
            </a>
        </li>
    <?php }  ?>
    <?php echo $show_no_list; // No recent posts ?>
    </ul>
</div>

<?php if (count($list)) { // 게시물이 있을 때 스크립트 추가 ?>
<script>
    $('.notice ul').bxSlider({
        hideControlOnEnd: true,
        pager:false,
        nextText: '<i class="fa fa-angle-right" aria-hidden="true"></i>',
        prevText: '<i class="fa fa-angle-left" aria-hidden="true"></i>'
    });
</script>
<?php } ?>
