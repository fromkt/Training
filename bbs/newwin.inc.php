<?php
if (!defined("_GNUBOARD_")) exit; // Unable to access direct pages

$sql = " select * from {$gml['new_win_table']}
          where '".GML_TIME_YMDHIS."' between nw_begin_time and nw_end_time
            and nw_device IN ( 'both', 'pc' )
          order by nw_id asc ";
$result = sql_query($sql, false);
?>

<!-- Start Popup-layer { -->
<div id="hd_pop">
    <h2><?php e__('Popup Layer Notification'); ?></h2>

<?php
for ($i=0; $nw=sql_fetch_array($result); $i++)
{
    // 이미 체크 되었다면 Continue
    if ($_COOKIE["hd_pops_{$nw['nw_id']}"])
        continue;
?>

    <div id="hd_pops_<?php echo $nw['nw_id'] ?>" class="hd_pops" style="top:<?php echo $nw['nw_top']?>px;left:<?php echo $nw['nw_left']?>px">
        <div class="hd_pops_con" style="width:<?php echo $nw['nw_width'] ?>px;height:<?php echo $nw['nw_height'] ?>px">
            <?php echo conv_content($nw['nw_content'], 1); ?>
        </div>
        <div class="hd_pops_footer">
            <button class="hd_pops_reject hd_pops_<?php echo $nw['nw_id']; ?> <?php echo $nw['nw_disable_hours']; ?>"><?php echo sprintf(__('Not see it agagin for <strong>%s</strong> hours'), $nw['nw_disable_hours']); ?></button>
            <button class="hd_pops_close hd_pops_<?php echo $nw['nw_id']; ?>"><?php e__('Close'); ?></button>
        </div>
    </div>
<?php }
if ($i == 0) echo '<span class="sound_only">'.__('No pop-up layer notifications.').'</span>';
?>
</div>

<script>
jQuery(function($) {
    $(".hd_pops_reject").click(function() {
        var id = $(this).attr('class').split(' ');
        var ck_name = id[1];
        var exp_time = parseInt(id[2]);
        $("#"+id[1]).css("display", "none");
        set_cookie(ck_name, 1, exp_time, gml_cookie_domain);
    });
    $('.hd_pops_close').click(function() {
        var idb = $(this).attr('class').split(' ');
        $('#'+idb[1]).css('display','none');
    });
    $("#hd").css("z-index", 1000);
});
</script>
<!-- } End Popup-layer -->