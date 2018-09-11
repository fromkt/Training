<?php
if (!defined("_GNUBOARD_")) exit; // Unable to access direct pages

for($i=0; $i<$view['rel_count']; $i++) {
    $rel_list[$i]['qa_status_class'] = $rel_list[$i]['qa_status'] ? 'txt_done' : 'txt_rdy';
    $rel_list[$i]['qa_status_icon'] = 
        $rel_list[$i]['qa_status']
        ? ''.__('Answer completed')
        : ''.__('Answer waiting');
}
?>
<section id="bo_v_rel">
    <h2><?php e__('Other QA'); ?></h2>
    <div class="tbl_head01 tbl_wrap">
        <table>
        <thead>
        <tr>
        	<th scope="col"><?php e__('Category'); ?></th>
            <th scope="col"><?php e__('Subject'); ?></th>
            <th scope="col"><?php e__('Date'); ?></th>
            <th scope="col"><?php e__('State'); ?></th>
        </tr>
        </thead>
        <tbody>
        <?php for($i=0; $i<$view['rel_count']; $i++) { ?>
        <tr>
            <td class="td_cate">
                <span><?php echo get_text($rel_list[$i]['category']); ?></span>  
            </td>
            <td class="td_subject">
				<a href="<?php echo $rel_list[$i]['view_href']; ?>" class="bo_tit">
                    <?php echo $rel_list[$i]['subject']; ?>
                </a>
            </td>
            <td class="td_date"><?php echo $rel_list[$i]['date']; ?></td>
            <td class="td_stat"><span class="<?php echo $rel_list[$i]['qa_status_class'] ?>"><?php echo $rel_list[$i]['qa_status_icon'] ?></span></td>
        </tr>
        <?php } ?>
        </tbody>
        </table>
    </div>
</section>
