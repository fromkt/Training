<?php
if (!defined('_GNUBOARD_')) exit; // Unable to access direct pages

if (GML_IS_MOBILE) {
    include_once(GML_THEME_MOBILE_PATH.'/head.php');
    return;
}

include_once(GML_THEME_PATH.'/head.sub.php');
include_once(GML_LIB_PATH.'/latest.lib.php');
include_once(GML_LIB_PATH.'/outlogin.lib.php');
include_once(GML_LIB_PATH.'/poll.lib.php');
include_once(GML_LIB_PATH.'/visit.lib.php');
include_once(GML_LIB_PATH.'/connect.lib.php');
include_once(GML_LIB_PATH.'/popular.lib.php');

// don't display header, footer, side menu on login/register and etc.
if (!defined("_DONT_WRAP_IN_CONTAINER_")) {
?>

<!-- header start { -->
<div id="hd">
    <h1 id="hd_h1"><?php echo $gml['title'] ?></h1>

    <div id="skip_to_container"><a href="#container"><?php e__('Go to Body'); ?></a></div>

    <?php
    if(defined('_INDEX_')) { // index에서만 실행, excute index page
        include GML_BBS_PATH.'/newwin.inc.php'; // 팝업레이어, popup layer
    }
    ?>

    <div id="hd_wrapper">
        <div id="logo">
            <a href="<?php echo GML_URL ?>"><img src="<?php echo GML_IMG_URL ?>/logo.png" alt="<?php echo $config['cf_title']; ?>"></a>
        </div>

		<div id="tnb">
	        <ul>
	            <?php if ($is_member) {  ?>
	            <li><a href="<?php echo GML_BBS_URL ?>/member_confirm.php?url=<?php echo GML_BBS_URL ?>/register_form.php"><?php ep__('Profile', 'Edit My Profile'); ?></a></li>
	            <li><a href="<?php echo GML_BBS_URL ?>/logout.php"><?php e__('Logout'); ?></a></li>
	            <?php } else {  ?>
	            <li><a href="<?php echo GML_BBS_URL ?>/register.php"><?php e__('Register'); ?></a></li>
	            <li><a href="<?php echo GML_BBS_URL ?>/login.php"><b><?php e__('Login'); ?></b></a></li>
	            <?php }  ?>

	            <?php if ($is_admin) {  ?>
	            <!-- <li class="tnb_admin"><a href="<?php echo GML_ADMIN_URL ?>"><b><?php e__('Admin'); ?></b></a></li> -->
	            <?php }  ?>
	        </ul>
		</div>

        <div class="hd_sch_wr">
            <fieldset id="hd_sch">
                <legend><?php e__('All Search in Site'); ?></legend>
                <form name="fsearchbox" method="get" action="<?php echo GML_BBS_URL ?>/search.php" onsubmit="return fsearchbox_submit(this);">
                <input type="hidden" name="sfl" value="wr_subject||wr_content">
                <input type="hidden" name="sop" value="and">
                <label for="sch_stx" class="sound_only"><?php e__('Search term required'); ?></label>
                <input type="text" name="stx" id="sch_stx" maxlength="20" placeholder="<?php e__('Enter search term'); ?>">
                <button type="submit" id="sch_submit" value="<?php e__('Search'); ?>"><i class="fa fa-search" aria-hidden="true"></i><span class="sound_only"><?php e__('Search'); ?></span></button>
                </form>

                <?php
                get_localize_script('locale_head',
                array(
                'character_msg'=>__('Please enter at least two characters for search term.'),  // 검색어는 두글자 이상 입력하십시오.
                'gap_msg'=>__('For quick searching, you can only enter one space in the search term.'),    // 빠른 검색을 위하여 검색어에 공백은 한개만 입력할 수 있습니다.
                ),
                true);
                ?>
                <script>
                function fsearchbox_submit(f)
                {
                    if (f.stx.value.length < 2) {
                        alert( locale_head.character_msg );
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
                        alert( locale_head.gap_msg );
                        f.stx.select();
                        f.stx.focus();
                        return false;
                    }

                    return true;
                }
                </script>
            </fieldset>
        </div>
    </div>

    <nav id="gnb">
        <h2><?php e__('Main menu'); ?></h2>
        <div class="gnb_wrap">
            <ul id="gnb_1dul">
                <li class="gnb_1dli gnb_mnal"><button type="button" class="gnb_menu_btn"><i class="fa fa-bars" aria-hidden="true"></i><span class="sound_only"><?php e__('Open the all menu'); ?></span></button></li>
                <?php
                $sql = " select *
                            from {$gml['menu_table']}
                            where me_use = '1'
                              and length(me_code) = '2'
                            order by me_order, me_id ";
                $result = sql_query($sql, false);
                $gnb_zindex = 999; // gnb_1dli z-index 값 설정용
                $menu_datas = array();

                for ($i=0; $row=sql_fetch_array($result); $i++) {
                    $menu_datas[$i] = $row;

                    $sql2 = " select *
                                from {$gml['menu_table']}
                                where me_use = '1'
                                  and length(me_code) = '4'
                                  and substring(me_code, 1, 2) = '{$row['me_code']}'
                                order by me_order, me_id ";
                    $result2 = sql_query($sql2);
                    for ($k=0; $row2=sql_fetch_array($result2); $k++) {
                        $menu_datas[$i]['sub'][$k] = $row2;
                    }

                }

                $i = 0;
                foreach( $menu_datas as $row ){
                    if( empty($row) ) continue;
                ?>
                <li class="gnb_1dli" style="z-index:<?php echo $gnb_zindex--; ?>">
                    <a href="<?php echo adjust_url_to_config($row['me_link']); ?>" target="_<?php echo $row['me_target']; ?>" class="gnb_1da"><?php echo $row['me_name'] ?></a>
                    <?php
                    $k = 0;
                    foreach( (array) $row['sub'] as $row2 ){

                        if( empty($row2) ) continue;

                        if($k == 0)
                            echo '<span class="bg">'.__('Subcategory').'</span><ul class="gnb_2dul">'.PHP_EOL;
                    ?>
                        <li class="gnb_2dli"><a href="<?php echo adjust_url_to_config($row2['me_link']); ?>" target="_<?php echo $row2['me_target']; ?>" class="gnb_2da"><?php echo $row2['me_name'] ?></a></li>
                    <?php
                    $k++;
                    }   //end foreach $row2

                    if($k > 0)
                        echo '</ul>'.PHP_EOL;
                    ?>
                </li>
                <?php
                $i++;
                }   //end foreach $row

                if ($i == 0) {  ?>
                    <li class="gnb_empty"><?php e__('Preparing menu.');  // Preparing menu ?>
                    <?php if ($is_admin) {
                        echo sprintf(__('You can set it in %s'), '<a href="'.GML_ADMIN_URL.'/menu_list.php">'.__('Admin mode &gt; Configuration &gt; Menu settings').'</a>');
                    }
                    ?>
                    </li>
                <?php } ?>
            </ul>
            <div id="gnb_all">
                <h2><?php e__('All menu'); ?></h2>
                <ul class="gnb_al_ul">
                    <?php

                    $i = 0;
                    foreach( $menu_datas as $row ){
                    ?>
                    <li class="gnb_al_li">
                        <a href="<?php echo adjust_url_to_config($row['me_link']); ?>" target="_<?php echo $row['me_target']; ?>" class="gnb_al_a"><?php echo $row['me_name'] ?></a>
                        <?php
                        $k = 0;
                        foreach( (array) $row['sub'] as $row2 ){
                            if($k == 0)
                                echo '<ul>'.PHP_EOL;
                        ?>
                            <li><a href="<?php echo $row2['me_link']; ?>" target="_<?php echo $row2['me_target']; ?>"><?php echo $row2['me_name'] ?></a></li>
                        <?php
                        $k++;
                        }   //end foreach $row2

                        if($k > 0)
                            echo '</ul>'.PHP_EOL;
                        ?>
                    </li>
                    <?php
                    $i++;
                    }   //end foreach $row

                    if ($i == 0) {  ?>
                        <li class="gnb_empty">
                        <?php e__('Preparing menu.'); ?>
                        <?php if ($is_admin) {
                            echo sprintf(__('You can set it in %s'), '<a href="'.GML_ADMIN_URL.'/menu_list.php">'.__('Admin mode &gt; Configuration &gt; Menu settings').'</a>');
                        }
                        ?>
                        </li>
                    <?php } ?>
                </ul>
                <button class="gnb_close_btn"><span class="sound_only"><?php e__('close'); ?></span><i class="fa fa-times" aria-hidden="true"></i></button>
            </div>
        </div>
    </nav>
    <script>

    jQuery(function($){
        $(".gnb_menu_btn").click(function(){
            $("#gnb_all").show();
        });
        $(".gnb_close_btn").click(function(){
            $("#gnb_all").hide();
        });
    });

    </script>
</div>
<!-- } end header -->

<!-- start contents { -->
<div id="wrapper">
    <div id="container_wr">

	<div id="aside">
        <?php start_event('aside_start'); ?>
		<?php //공지 echo latest('notice', 'notice', 4, 13); ?>
    	<?php echo outlogin('basic'); // 외부 로그인, 테마의 스킨을 사용하려면 스킨을 basic 과 같이 지정 ?>
    	<ul id="hd_qnb">
            <li class="hd_qnb_faq"><a href="<?php echo GML_BBS_URL ?>/faq.php"><i class="fa fa-question" aria-hidden="true"></i><span>FAQ</span></a></li>
            <li class="hd_qnb_qalist"><a href="<?php echo GML_BBS_URL ?>/qalist.php"><i class="fa fa-comments" aria-hidden="true"></i><span><?php ep__('1:1', '1:1 inquiry'); ?></span></a></li>
            <li class="hd_qnb_new"><a href="<?php echo GML_BBS_URL ?>/current_connect.php" class="visit"><i class="fa fa-users" aria-hidden="true"></i><span><?php e__('Visitor'); ?></span><strong class="visit-num"><?php echo connect('basic'); // 현재 접속자수, 테마의 스킨을 사용하려면 스킨을 basic 과 같이 지정  ?></strong></a></li>
            <li class="hd_qnb_cc"><a href="<?php echo GML_BBS_URL ?>/new.php"><i class="fa fa-history" aria-hidden="true"></i><span><?php ep__('New', 'New posts'); ?></span></a></li>
        </ul>

		<?php echo poll('basic'); // 설문조사, 테마의 스킨을 사용하려면 스킨을 basic 과 같이 지정 ?>
		<?php echo popular('basic'); // 인기검색어, 테마의 스킨을 사용하려면 스킨을 basic 과 같이 지정  ?>
		<?php echo visit('basic'); // 접속자집계, 테마의 스킨을 사용하려면 스킨을 basic 과 같이 지정 ?>
        <?php start_event('aside_end'); ?>
    </div>
    <div id="container">
	<?php } // end of if (!defined("_DONT_WRAP_IN_CONTAINER_")) ?>
    	<?php if (!defined("_INDEX_") && !defined("_DONT_WRAP_IN_CONTAINER_")) {?>
        	<h2 id="container_title"><span title="<?php echo get_text($gml['title']); ?>"><?php echo get_head_title($gml['title']); ?></span></h2>
        <?php } ?>
    <?php start_event('container_start'); ?>