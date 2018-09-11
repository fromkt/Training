<?php
if (!defined("_GNUBOARD_")) exit; // Unable to access direct pages

$select_subject_or_content = get_selected($_GET['sfl'], "wr_subject||wr_content");
$select_wr_subject = get_selected($_GET['sfl'], "wr_subject");
$select_wr_content = get_selected($_GET['sfl'], "wr_content");
$select_mb_id = get_selected($_GET['sfl'], "mb_id");
$select_wr_name = get_selected($_GET['sfl'], "wr_name");

$sop_or = ($sop == "or") ? "checked" : "";
$sop_and = ($sop == "and") ? "checked" : "";

$exist_search_result = $stx && $board_count ? : false;
if($exist_search_result) {
    $show_total_count = number_format($total_count);
    $show_page = number_format($page);
    $show_total_page = number_format($total_page);
}

$more_result_href = array();
for ($idx=$table_index, $k=0; $idx<count($search_table) && $k<$rows; $idx++) {
    $more_result_href[$idx] = get_pretty_url($search_table[$idx], '', $search_query);
    for ($i=0; $i<count($list[$idx]) && $k<$rows; $i++, $k++) {
        if ($list[$idx][$i]['wr_is_comment'])
        {
            $list[$idx][$i]['comment_def'] = '<span class="cmt_def"><i class="fa fa-commenting-o" aria-hidden="true"></i><span class="sound_only">'.__('Comment').'</span></span> ';
            $list[$idx][$i]['comment_href'] = '#c_'.$list[$idx][$i]['wr_id'];
        }
        else
        {
            $list[$idx][$i]['comment_def'] = '';
            $list[$idx][$i]['comment_href'] = '';
        }
    }
}

// add_stylesheet('css file path', Output order); Smaller numbers printed first
add_stylesheet('<link rel="stylesheet" href="'.$search_skin_url.'/style.css">', 0);
?>

<!-- Start all search { -->
<form name="fsearch" onsubmit="return fsearch_submit(this);" method="get">
<input type="hidden" name="srows" value="<?php echo $srows ?>">
<fieldset id="sch_res_detail">
    <legend><?php e__('Search Details'); ?></legend>
    <?php echo $group_select ?>

    <label for="sfl" class="sound_only"><?php e__('Search options'); ?></label>
    <select name="sfl" id="sfl">
        <option value="wr_subject||wr_content"<?php echo $select_subject_or_content ?>><?php e__('Subject+Content'); ?></option>
        <option value="wr_subject"<?php echo $select_wr_subject ?>><?php e__('Subject'); ?></option>
        <option value="wr_content"<?php echo $select_wr_content ?>><?php e__('Content'); ?></option>
        <option value="mb_id"<?php echo $select_mb_id ?>><?php e__('Member ID'); ?></option>
        <option value="wr_name"<?php echo $select_wr_name ?>><?php e__('Name'); ?></option>
    </select>

    <label for="stx" class="sound_only"><?php e__('Search terms'); ?><strong class="sound_only"> <?php e__('Required'); ?></strong></label>
    <span class="sch_wr">
        <input type="text" name="stx" value="<?php echo $text_stx ?>" id="stx" required class="frm_input" size="40">
        <button type="submit" class="btn_submit"><i class="fa fa-search" aria-hidden="true"></i> <?php e__('Search'); ?></button>
    </span>

    <span class="sch_rd">
        <input type="radio" value="or" <?php echo $sop_or ?> id="sop_or" name="sop">
        <label for="sop_or">OR</label>
        <input type="radio" value="and" <?php echo $sop_and ?> id="sop_and" name="sop">
        <label for="sop_and">AND</label>
    </span>
</fieldset>
</form>

<div id="sch_result">
    <?php if ($exist_search_result) { ?>
    <section id="sch_res_ov">
        <h2><strong><?php echo $stx ?></strong> <?php e__('All Search Results'); ?></h2>
        <dl>
            <dt><?php e__('Boards'); ?></dt>
            <dd><strong class="sch_word"><?php echo sprintf(n__('%s total', '%s totals', $board_count), $board_count); ?></strong></dd>
            <dt><?php e__('Posts'); ?></dt>
            <dd><strong class="sch_word"><?php echo sprintf(n__('%s total', '%s totals', $show_total_count), $show_total_count); ?></strong></dd>
        </dl>
    </section>
    <p><?php echo $show_page ?>/<?php echo $show_total_page ?> <?php e__('Reading pages'); ?></p>

    <ul id="sch_res_board">
        <li><a href="?<?php echo $search_query ?>&amp;gr_id=<?php echo $gr_id ?>" <?php echo $sch_all ?>><?php e__('All boards'); ?></a></li>
        <?php echo $str_board_list; ?>
    </ul>
    <?php } else { ?>
    <div class="empty_list"><?php e__('No search results.'); ?></div>
    <?php } ?>

    <?php
    if ($exist_search_result) {
    ?>
    <section class="sch_res_list">
    <?php
        for ($idx=$table_index, $k=0; $idx<count($search_table) && $k<$rows; $idx++) {
    ?>
        <h2><a href="<?php echo $more_result_href[$idx] ?>"><?php echo $bo_subject[$idx] ?> <?php e__('Results within bulletin board'); ?></a></h2>
        <ul>
        <?php
            for ($i=0; $i<count($list[$idx]) && $k<$rows; $i++, $k++) {
        ?>
            <li>
                <div class="sch_tit">
                	<?php echo $list[$idx][$i]['comment_def'] ?>
                    <a href="<?php echo $list[$idx][$i]['href'] ?><?php echo $list[$idx][$i]['comment_href'] ?>" class="sch_res_title"><?php echo $list[$idx][$i]['subject'] ?></a>
                    <a href="<?php echo $list[$idx][$i]['href'] ?><?php echo $list[$idx][$i]['comment_href'] ?>" target="_blank" class="pop_a"><?php e__('New window'); ?></a>
                </div>
                <p><?php echo $list[$idx][$i]['content'] ?></p>
                <div class="sch_info">
                    <?php echo $list[$idx][$i]['name'] ?>
                    <span class="sch_datetime"><i class="fa fa-clock-o" aria-hidden="true"></i> <?php echo $list[$idx][$i]['wr_datetime'] ?></span>
                </div>
            </li>
        <?php
            }
        ?>
        </ul>
        <div class="sch_more"><a href="<?php echo $more_result_href[$idx] ?>"><strong><?php echo $bo_subject[$idx] ?></strong> <?php e__('more'); ?></a></div>
    <?php
        }
    ?>
    </section>
    <?php
    }
    ?>

    <?php echo $write_pages ?>
</div>

<?php
get_localize_script('search_skin',
array(
'check_msg1'=>__('Please enter at least two characters for search term.'),  // 검색어는 두글자 이상 입력하십시오.
'check_msg2'=>__('For quick searching, you can only enter one space in the search term.'),    // 빠른 검색을 위하여 검색어에 공백은 한개만 입력할 수 있습니다.
),
true);
?>
<script>
function fsearch_submit(f)
{
    if (f.stx.value.length < 2) {
        alert( search_skin.check_msg1 );
        f.stx.select();
        f.stx.focus();
        return false;
    }

    var cnt = 0;
    for (var i=0; i<f.stx.value.length; i++) {
        if (f.stx.value.charAt(i) == ' ')
            cnt++;
    }

    if (cnt > 1) {
        alert( search_skin.check_msg2 );
        f.stx.select();
        f.stx.focus();
        return false;
    }

    f.action = "";
    return true;
}
</script>
<!-- } End all search -->
