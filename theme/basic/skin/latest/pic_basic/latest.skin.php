<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
include_once(GML_LIB_PATH.'/thumbnail.lib.php');

$thumb_width = 210;
$thumb_height = 150;
for ($i=0; $i<count($list); $i++) {
    $thumb = get_list_thumbnail($bo_table, $list[$i]['wr_id'], $thumb_width, $thumb_height, false, true);

    if($thumb['src']) {
        $img = $thumb['src'];
    } else {
        $img = GML_IMG_URL.'/no_img.png';
        $thumb['alt'] = __('No image found.');
    }
    // 이미지 썸네일
    $list[$i]['img_content'] = '<img src="'.$img.'" alt="'.$thumb['alt'].'" >';
}

// add_stylesheet('css 구문', 출력순서); 숫자가 작을 수록 먼저 출력됨
add_stylesheet('<link rel="stylesheet" href="'.$latest_skin_url.'/style.css">', 0);
?>

<div class="pic_lt">
    <h2 class="lat_title"><a href="<?php echo $see_more_href ?>"><?php echo $bo_subject; ?></a></h2>
    <ul>
    <?php for ($i=0; $i<count($list); $i++) { ?>
        <li>
            <a href="<?php echo $list[$i]['href'] ?>" class="lt_img"><?php echo $list[$i]['img_content'] ?></a>
            
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

        	<div class="lct_info">			
				<span class="lt_nick"><span class="sound_only"><?php e__('Writer'); ?></span><?php echo $list[$i]['name'] ?></span>
				<span class="lt_date"><?php echo $list[$i]['datetime2'] ?></span>
				<?php if ($list[$i]['comment_cnt']) { ?>
	                <span class="lt_comnt"><i class="fa fa-commenting-o" aria-hidden="true"></i> <?php echo $list[$i]['wr_comment'] ?></span>
	            <?php } ?>
			</div>
        </li>
    <?php }  ?>
    <?php echo $show_no_list // 게시물이 없을 때  ?>
    </ul>
    <a href="<?php echo $see_more_href ?>" class="lt_more"><span class="sound_only"> <?php echo $bo_subject ?></span><?php e__('More'); ?></a>
</div>
