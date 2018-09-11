<?php
if (!defined('_GNUBOARD_')) exit; // Unable to access direct pages
// member totals = $row['mb_cnt'];

// add_stylesheet('css file path', Output order); Smaller numbers printed first
add_stylesheet('<link rel="stylesheet" href="'.$connect_skin_url.'/style.css">', 0);
?>
<?php echo $row['total_cnt'] ?>
