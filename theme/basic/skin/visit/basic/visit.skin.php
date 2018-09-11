<?php
if (!defined("_GNUBOARD_")) exit; // Unable to access direct pages

global $is_admin;

// add_stylesheet('css file path', Output order); Smaller numbers printed first
add_stylesheet('<link rel="stylesheet" href="'.$visit_skin_url.'/style.css">', 0);
?>

<!-- Start Count Vister { -->
<section id="visit">
    <h2><?php e__('Count Vister'); ?></h2>
    <dl>
        <dt><i class="fa fa-user-circle-o" aria-hidden="true"></i> <?php e__('Today'); ?></dt>
        <dd><strong class="color_1"><?php echo number_format($visit[1]) ?></strong></dd>
        <dt><i class="fa fa-user-circle-o" aria-hidden="true"></i> <?php e__('Yesterday'); ?></dt>
        <dd><strong class="color_2"><?php echo number_format($visit[2]) ?></strong></dd>
        <dt><i class="fa fa-user-circle-o" aria-hidden="true"></i> <?php e__('Maximum'); ?></dt>
        <dd><strong class="color_3"><?php echo number_format($visit[3]) ?></strong></dd>
        <dt><i class="fa fa-user-circle-o" aria-hidden="true"></i> <?php e__('All'); ?></dt>
        <dd><strong class="color_4"><?php echo number_format($visit[4]) ?></strong></dd>
    </dl>
    <?php if ($is_admin == "super") {  ?><a href="<?php echo GML_ADMIN_URL ?>/visit_list.php" class="btn_admin"><?php e__('View Details'); ?></a><?php } ?>
</section>
<!-- } End Count Vister -->