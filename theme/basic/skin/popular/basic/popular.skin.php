<?php
if (!defined("_GNUBOARD_")) exit; // Unable to access direct pages

// add_stylesheet('css file path', Output order); Smaller numbers printed first
add_stylesheet('<link rel="stylesheet" href="'.$popular_skin_url.'/style.css">', 0);
?>

<!-- Start top Search { -->
<section id="popular">
    <div>
        <h2><?php e__('Top search terms'); ?></h2>
        <ul>
        <?php
        if( isset($list) && is_array($list) ){
            for ($i=0; $i<count($list); $i++) {
            ?>
            <li><a href="<?php echo GML_BBS_URL ?>/search.php?sfl=wr_subject&amp;sop=and&amp;stx=<?php echo urlencode($list[$i]['pp_word']) ?>"><?php echo get_text($list[$i]['pp_word']); ?></a></li>
            <?php
            }   //end for
        }   //end if
        ?>
        <?php if (!is_array($list) || count($list) == 0) { ?>
            <li class="empty_li"><?php e__('No popular search terms.'); ?></li>
        <?php } ?>
        </ul>

    </div>
</section>
<!-- } End top Search -->