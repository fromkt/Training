<?php
if (!defined('_GNUBOARD_')) exit; // Unable to access direct pages

// add_stylesheet('css file path', Output order); Smaller numbers printed first
add_stylesheet('<link rel="stylesheet" href="'.$connect_skin_url.'/style.css">', 0);
?>

<!-- Start Concurrent list { -->
<div id="current_connect">
    <ul>
    <?php
    for ($i=0; $i<count($list); $i++) {
        //$location = conv_content($list[$i]['lo_location'], 0);
        $location = $list[$i]['lo_location'];
        // Allow only top administrator
        // Do not make any changes to this statement.
        if ($list[$i]['lo_url'] && $is_admin == 'super') $display_location = "<a href=\"".$list[$i]['lo_url']."\">".$location."</a>";
        else $display_location = $location;

        $classes = array();
        if( $i && ($i % 4 == 0) ){
            $classes[] = 'box_clear';
        }
    ?>
        <li class="<?php echo $list[$i]['classes']; ?>">
            <span class="crt crt_num"><?php echo $list[$i]['num'] ?></span>
            <span class="crt crt_name"><?php echo $list[$i]['name'] ?></span>
            <span class="crt crt_lct"><i class="fa fa-list-alt" aria-hidden="true"></i> <?php echo $display_location ?></span>
        </li>
    <?php
    }
    if ($i == 0)
        echo "<li class=\"empty_li\">".__('Non-Concurrent Users.')."</li>";
    ?>
    </ul>
</div>
<!-- } End Concurrent list -->