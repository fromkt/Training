<?php
if (!defined('_GNUBOARD_')) exit;

include_once(GML_LIB_PATH.'/visit.lib.php');
include_once('./admin.head.php');
include_once(GML_PLUGIN_PATH.'/jquery-ui/datepicker.php');

if (empty($fr_date) || ! preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $fr_date) ) $fr_date = GML_TIME_YMD;
if (empty($to_date) || ! preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $to_date) ) $to_date = GML_TIME_YMD;

$qstr = "fr_date=".$fr_date."&amp;to_date=".$to_date;
$query_string = $qstr ? '?'.$qstr : '';
add_javascript('<script src="'.GML_ADMIN_URL.'/js/horizon-swiper.min.js"></script>', 10);
add_stylesheet('<link rel="stylesheet" href="'.GML_ADMIN_URL.'/js/horizon-swiper.min.css">', 1);
?>


<form name="fvisit" id="fvisit" class="local_sch03" method="get">
    <strong class="sch_tit"><?php e__('Search by Period');   //기간별검색 ?></strong>
    <input type="text" name="fr_date" value="<?php echo $fr_date ?>" id="fr_date" class="frm_input" size="6" maxlength="10">
    <label for="fr_date" class="sound_only"><?php e__('Start date'); ?></label>
    ~
    <input type="text" name="to_date" value="<?php echo $to_date ?>" id="to_date" class="frm_input" size="6" maxlength="10">
    <label for="to_date" class="sound_only"><?php e__('End Date'); ?></label>
    <input type="submit" value="<?php e__('Search'); ?>" class="btn_submit ">
</form>

<div class="anchor horizon-swiper">
    <div class="horizon-item"><a href="./visit_list.php<?php echo $query_string ?>"><?php e__('Visitor'); ?></a></div><div class="horizon-item"><a href="./visit_domain.php<?php echo $query_string ?>"><?php e__('Domain'); ?></a></div><div class="horizon-item"><a href="./visit_browser.php<?php echo $query_string ?>"><?php e__('Browser'); ?></a></div><div class="horizon-item"><a href="./visit_os.php<?php echo $query_string ?>"><?php e__('OS'); ?></a></div><?php if(version_compare(phpversion(), '5.3.0', '>=') && defined('GML_BROWSCAP_USE') && GML_BROWSCAP_USE) { ?><div class="horizon-item"><a href="./visit_device.php<?php echo $query_string ?>"><?php e__('User agent'); ?></a></div><?php } ?><div class="horizon-item"><a href="./visit_hour.php<?php echo $query_string ?>"><?php e__('Hours'); ?></a></div><div class="horizon-item"><a href="./visit_week.php<?php echo $query_string ?>"><?php e__('Week'); ?></a></div><div class="horizon-item"><a href="./visit_date.php<?php echo $query_string ?>"><?php e__('Day'); ?></a></div><div class="horizon-item"><a href="./visit_month.php<?php echo $query_string ?>"><?php e__('Month'); ?></a></div><div class="horizon-item"><a href="./visit_year.php<?php echo $query_string ?>"><?php e__('Year'); ?></a></div>
</div>

<script>
jQuery(function(){
    $('.horizon-swiper').horizonSwiper();

    $("#fr_date, #to_date").datepicker({ changeMonth: true, changeYear: true, dateFormat: "yy-mm-dd", showButtonPanel: true, yearRange: "c-99:c+99", maxDate: "+0d" });
});

function fvisit_submit(act)
{
    var f = document.fvisit;
    f.action = act;
    f.submit();
}
</script>
