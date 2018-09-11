<?php
$menu['menu300'] = array (
    array('300000', __('Manage Board'), ''.GML_ADMIN_URL.'/board_list.php', 'board'),
    array('300100', __('Manage Board'), ''.GML_ADMIN_URL.'/board_list.php', 'bbs_board'),
    array('300200', p__('Manage bbs Groups', 'Manage board Groups'), ''.GML_ADMIN_URL.'/boardgroup_list.php', 'bbs_group'),
    array('300300', __('Manage top search'), ''.GML_ADMIN_URL.'/popular_list.php', 'bbs_poplist', 1),
    array('300400', __('Top search ranking'), ''.GML_ADMIN_URL.'/popular_rank.php', 'bbs_poprank', 1),
    array('300500', __('1:1 Contact Settings'), ''.GML_ADMIN_URL.'/qa_config.php', 'qa'),
    array('300600', __('Manage Content'), GML_ADMIN_URL.'/contentlist.php', 'scf_contents', 1),
    array('300700', __('Manage FAQ'), GML_ADMIN_URL.'/faqmasterlist.php', 'scf_faq', 1),
    array('300820', __('Status of boards'), GML_ADMIN_URL.'/write_count.php', 'scf_write_count'),
);

// CKEDITOR Upload images sources
if( $config['cf_editor'] == "ckeditor4" ) {
    include_once(GML_EDITOR_LIB);
    if( class_exists('EditorImage') ) {
        $menu['menu300'][] = array('300900', __('Editor Images'), GML_ADMIN_URL.'/editorimglist.php', 'editor_img');
    }
}
?>