<?php
if (!defined("_GNUBOARD_")) exit; // Unable to access direct pages

// add_stylesheet('css file path', Output order); Smaller numbers printed first
add_stylesheet('<link rel="stylesheet" href="'.$popular_skin_url.'/style.css">', 0);
?>

<!-- Start top Search { -->
<aside id="popular">
    <h2><?php e__('Top search term'); ?></h2>
    <div>
    <?php
    if( isset($list) && is_array($list) ){
        for ($i=0; $i<count($list); $i++) {
    ?>
        <a href="<?php echo GML_BBS_URL ?>/search.php?sfl=wr_subject&amp;sop=and&amp;stx=<?php echo urlencode($list[$i]['pp_word']) ?>"><?php echo get_text($list[$i]['pp_word']); ?></a>
    <?php
        }   //end for
    }   //end if
    ?>
    </div>
</aside>
<!-- } End top Search -->
