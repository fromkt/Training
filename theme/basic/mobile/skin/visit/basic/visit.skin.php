<?php
if (!defined("_GNUBOARD_")) exit; // Unable to access direct pages

global $is_admin;

// add_stylesheet('css file path', Output order); Smaller numbers printed first
add_stylesheet('<link rel="stylesheet" href="'.$visit_skin_url.'/style.css">', 0);
?>

<!-- Start Count Vister { -->
<aside id="visit">
    <h2><?php e__('Count Vister'); ?></h2>
    <dl>
        <dt><i class="fa fa-user-circle-o" aria-hidden="true"></i> <?php e__('Today'); ?></dt>
        <dd><span><?php echo number_format($visit[1]) ?></span></dd>
        <dt><i class="fa fa-user-circle-o" aria-hidden="true"></i> <?php e__('Yesterday'); ?></dt>
        <dd><span><?php echo number_format($visit[2]) ?></span></dd>
        <dt><i class="fa fa-user-circle-o" aria-hidden="true"></i> <?php e__('Maximum'); ?></dt>
        <dd><span><?php echo number_format($visit[3]) ?></span></dd>
        <dt><i class="fa fa-user-circle-o" aria-hidden="true"></i> <?php e__('All'); ?></dt>
        <dd><span><?php echo number_format($visit[4]) ?></span></dd>
    </dl>
    <?php if ($is_admin == "super") { ?><a href="<?php echo GML_ADMIN_URL ?>/visit_list.php"><?php e__('View Details'); ?></a><?php } ?>
</aside>
<!-- } End Count Vister -->
