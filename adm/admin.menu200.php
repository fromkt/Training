<?php
$menu['menu200'] = array (
    array('200000', __('Member Manage'), GML_ADMIN_URL.'/member_list.php', 'member'),        //회원관리
    array('200100', __('Member Manage'), GML_ADMIN_URL.'/member_list.php', 'mb_list'),       //회원관리
    array('200300', __('Send Member Email'), GML_ADMIN_URL.'/mail_list.php', 'mb_mail'),     //회원메일발송
    array('200800', __('Visitor report'), GML_ADMIN_URL.'/visit_list.php', 'mb_visit', 1),   //방문자보기
    array('200810', __('Visitor search'), GML_ADMIN_URL.'/visit_search.php', 'mb_search', 1),    //방문자 검색
    array('200820', __('Delete visitor Log'), GML_ADMIN_URL.'/visit_delete.php', 'mb_delete', 1),    //방문자 로그 삭제
    array('200200', __('Point Manage'), GML_ADMIN_URL.'/point_list.php', 'mb_point'),        //포인트 관리
    array('200900', __('Poll Manage'), GML_ADMIN_URL.'/poll_list.php', 'mb_poll')            //설문 관리
);
?>