<?php
if (!defined("_GNUBOARD_")) exit; // Unable to access direct pages

for($i=0; $i<$view['rel_count']; $i++) {
    $rel_list[$i]['qa_status_class'] = $rel_list[$i]['qa_status'] ? 'txt_done' : 'txt_rdy';
    $rel_list[$i]['qa_status_icon'] = $rel_list[$i]['qa_status'] ? __('Answer completed') : __('Answer waiting');
}
?>

<section id="bo_v_rel">
    <h2><?php e__('Other QA'); ?></h2>

    <div class="list_01">
        <ul>
        <?php for($i=0; $i<$view['rel_count']; $i++) { ?>
            <li>
                <div class="li_title">
                    <strong><?php echo get_text($rel_list[$i]['category']); ?></strong>

                    <a href="<?php echo $rel_list[$i]['view_href']; ?>" class="li_sbj">
                        <?php echo $rel_list[$i]['subject']; ?>
                    </a>
                </div>
                <div class="li_info">
                    <span class="li_stat <?php echo $rel_list[$i]['qa_status_class'] ?>"><?php echo $rel_list[$i]['qa_status_icon'] ?></span>
                    <span class="li_date"><?php echo $rel_list[$i]['date']; ?></span>
                </div>
            </li>
        <?php } ?>
        </ul>
    </div>
</section>
