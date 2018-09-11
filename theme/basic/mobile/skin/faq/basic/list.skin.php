<?php
if (!defined('_GNUBOARD_')) exit; // Unable to access direct pages

// add_stylesheet('css file path', Output order); Smaller numbers printed first
add_stylesheet('<link rel="stylesheet" href="'.$faq_skin_url.'/style.css">', 0);
?>

<!-- Start FAQ { -->
<?php if( count($faq_master_list) ){ ?>
	
<!-- HEAD HTML -->
<div id="faq_hhtml"><?php echo $fm_head_html ?></div>
<nav id="bo_cate">
    <h2><?php e__('FAQ Category'); ?></h2>
    <ul id="bo_cate_ul">
        <?php foreach( $faq_master_list as $v ){ ?>
        <li><a href="<?php echo $category_href;?>?fm_id=<?php echo $v['fm_id']?>" <?php echo $v['category_option'];?> ><?php echo $v['category_msg_and_subject'] ?></a></li>
        <?php } ?>
    </ul>
</nav>
<?php } ?>

<div id="faq_sch">
    <form name="faq_search_form" method="get">
    <input type="hidden" name="fm_id" value="<?php echo $fm_id;?>">
    <label for="stx" class="sound_only"><?php e__('Search'); ?><strong class="sound_only"> <?php e__('Required'); ?></strong></label>
    <input type="text" name="stx" value="<?php echo $stx;?>" required id="stx" class="frm_input" size="15" maxlength="15">
    <button type="submit" value="<?php e__('Search'); ?>" class="btn_submit"><i class="fa fa-search" aria-hidden="true"></i><span class="sound_only"><?php e__('Search'); ?></span></button>
    </form>
</div>

<div id="faq_wrap" class="faq_<?php echo $fm_id; ?>">
    <?php // FAQ Content
    if( count($faq_list) ){
    ?>
    <section id="faq_con">
        <h2><?php echo $gml['title']; ?> <?php e__('List'); ?></h2>
        <ol>
            <?php
            foreach($faq_list as $key=>$v){
                if(empty($v))
                    continue;
            ?>
            <li>
                <h3><span class="faq_alp">Q</span><button onclick="return faq_open(this);"><?php echo $v['fa_subject'] ?></button></h3>
                <div class="con_inner">
                    <?php echo $v['fa_content'] ?>
                    <div class="con_closer"><button type="button" class="closer_btn"><?php e__('Close'); ?></button></div>
                </div>
            </li>
            <?php
            }
            ?>
        </ol>
    </section>
    <?php } ?>
    <?php echo $no_faq_list // No FAQ content or search results ?>
</div>

<?php echo $get_pagination ?>

<!-- Footer HTML -->
<div id="faq_thtml"><?php echo $fm_tail_html ?></div>

<!-- } End FAQ -->

<script src="<?php echo GML_JS_URL; ?>/viewimageresize.js"></script>
<script>
$(function() {
    $(".closer_btn").on("click", function() {
        $(this).closest(".con_inner").slideToggle();
    });
});

function faq_open(el)
{
    var $con = $(el).closest("li").find(".con_inner");

    if($con.is(":visible")) {
        $con.slideUp();
    } else {
        $("#faq_con .con_inner:visible").css("display", "none");

        $con.slideDown(
            function() {
                // Resize image
                $con.viewimageresize2();
            }
        );
    }

    return false;
}
</script>
