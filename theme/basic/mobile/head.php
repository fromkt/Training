<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

include_once(GML_THEME_PATH.'/head.sub.php');
include_once(GML_LIB_PATH.'/latest.lib.php');
include_once(GML_LIB_PATH.'/outlogin.lib.php');
include_once(GML_LIB_PATH.'/poll.lib.php');
include_once(GML_LIB_PATH.'/visit.lib.php');
include_once(GML_LIB_PATH.'/connect.lib.php');
include_once(GML_LIB_PATH.'/popular.lib.php');
?>

<header id="hd">
    <h1 id="hd_h1"><?php echo $gml['title'] ?></h1>

    <div class="to_content"><a href="#container"><?php e__('Go to Body'); ?></a></div>

    <?php
    if(defined('_INDEX_')) { // index에서만 실행, excute index page
        include GML_BBS_PATH.'/newwin.inc.php'; // 팝업레이어, popup layer
    } ?>

    <div id="hd_wrapper">
        <div id="logo">
            <a href="<?php echo GML_URL ?>">
            	<span class="sound_only"><?php echo $config['cf_title']; ?></span>
            	<img src="<?php echo GML_THEME_IMG_URL ?>/mobile/m_logo.png" alt="<?php echo $config['cf_title']; ?>">
            </a>
        </div>

        <nav id="gnb_wrap">
        	<button type="button" id="gnb_open" class="hd_opener"><i class="fa fa-bars" aria-hidden="true"></i><span class="sound_only"><?php e__('Open the all menu'); ?></span></button>
			<div id="gnb_side" class="hd_div">
				<div id="gnb_cate">
		            <?php echo outlogin('basic'); // 외부 로그인 ?>
		            <ul id="gnb_1dul">
		            <?php
		            $sql = " select *
		                        from {$gml['menu_table']}
		                        where me_mobile_use = '1'
		                          and length(me_code) = '2'
		                        order by me_order, me_id ";
		            $result = sql_query($sql, false);

		            for($i=0; $row=sql_fetch_array($result); $i++) {
		            ?>
		                <li class="gnb_1dli">
		                    <a href="<?php echo adjust_url_to_config($row['me_link']); ?>" target="_<?php echo $row['me_target']; ?>" class="gnb_1da"><?php echo $row['me_name'] ?></a>
		                    <?php
		                    $sql2 = " select *
		                                from {$gml['menu_table']}
		                                where me_mobile_use = '1'
		                                  and length(me_code) = '4'
		                                  and substring(me_code, 1, 2) = '{$row['me_code']}'
		                                order by me_order, me_id ";
		                    $result2 = sql_query($sql2);

		                    for ($k=0; $row2=sql_fetch_array($result2); $k++) {
		                        if($k == 0)
		                            echo '<button type="button" class="btn_gnb_op">'.__('Subcategory').'</button><ul class="gnb_2dul">'.PHP_EOL;
		                    ?>
		                        <li class="gnb_2dli"><a href="<?php echo adjust_url_to_config($row2['me_link']); ?>" target="_<?php echo $row2['me_target']; ?>" class="gnb_2da"><i class="fa fa-caret-right" aria-hidden="true"></i><?php echo $row2['me_name'] ?></a></li>
		                    <?php
		                    }

		                    if($k > 0)
		                        echo '</ul>'.PHP_EOL;
		                    ?>
		                </li>
		            <?php
		            }

		            if ($i == 0) {  ?>
		                <li id="gnb_empty"><?php e__('Preparing menu.');  // Preparing menu ?>
		                	<?php if ($is_admin) { echo sprintf(__('You can set it in %s'), '<a href="'.GML_ADMIN_URL.'/menu_list.php">'.__('Admin mode &gt; Configuration &gt; Menu settings').'</a>'); } ?>
		                </li>
		            <?php } ?>
		            </ul>

		            <ul id="hd_nb">
		                <li class="hd_nb1"><a href="<?php echo GML_BBS_URL ?>/faq.php" id="snb_faq"><i class="fa fa-question-circle" aria-hidden="true"></i> FAQ</a></li>
		                <li class="hd_nb2"><a href="<?php echo GML_BBS_URL ?>/qalist.php" id="snb_qa"><i class="fa fa-comments" aria-hidden="true"></i> <?php ep__('1:1', '1:1 inquiry'); ?></a></li>
		                <li class="hd_nb3"><a href="<?php echo GML_BBS_URL ?>/current_connect.php" id="snb_cnt"><i class="fa fa-users" aria-hidden="true"></i> <?php e__('Visitor'); ?> <span class="visit-num"><?php echo connect('basic'); // 현재 접속자수 ?></span></a></li>
		                <li class="hd_nb4"><a href="<?php echo GML_BBS_URL ?>/new.php" id="snb_new"><i class="fa fa-history" aria-hidden="true"></i> <?php ep__('New', 'New posts'); ?></a></li>
		            </ul>

		            <div id="text_size">
		            <!-- font_resize('엘리먼트id', '제거할 class', '추가할 class'); -->
		                <button id="size_down" onclick="font_resize('container', 'ts_up ts_up2', '', this);" class="select"><img src="<?php echo GML_URL; ?>/img/ts01.png" width="20" alt="<?php e__('default'); ?>"></button>
		                <button id="size_def" onclick="font_resize('container', 'ts_up ts_up2', 'ts_up', this);"><img src="<?php echo GML_URL; ?>/img/ts02.png" width="20" alt="<?php e__('large'); ?>"></button>
		                <button id="size_up" onclick="font_resize('container', 'ts_up ts_up2', 'ts_up2', this);"><img src="<?php echo GML_URL; ?>/img/ts03.png" width="20" alt="<?php e__('x-large'); ?>"></button>
		            </div>
	        	</div>
				<button type="button" id="gnb_close" class="hd_closer"><i class="fa fa-times fa-2x" aria-hidden="true"></i><span class="sound_only"><?php e__('Menu'); ?><?php e__('close'); ?></span></button>
			</div>

			<div id="gnb">
				<div class="gnb_menu" id="swipe_gnb_menu">
					<ul class="swiper-wrapper">
		            <?php
		            $sql = " select *
		                        from {$gml['menu_table']}
		                        where me_mobile_use = '1'
		                          and length(me_code) = '2'
		                        order by me_order, me_id ";
		            $result = sql_query($sql, false);

		            for($i=0; $row=sql_fetch_array($result); $i++) {
		            ?>
		                <li class="swiper-slide">
		                    <a href="<?php echo $row['me_link']; ?>" target="_<?php echo $row['me_target']; ?>"><?php echo $row['me_name'] ?></a>
		                </li>
		            <?php
		            }

		            if ($i == 0) {  ?>
	                    <li class="gnb_empty"><?php e__('Preparing menu.');  // Preparing menu ?>
	                    <?php if ($is_admin) {
	                        echo sprintf(__('You can set it in %s'), '<a href="'.GML_ADMIN_URL.'/menu_list.php" class="gnb_empty_admin">'.__('Admin mode &gt; Configuration &gt; Menu settings').'</a>');
	                    }
	                    ?>
	                    </li>
	                <?php } ?>
		            </ul>
				</div>
			</div>
		</nav>

		<div id="login_btn">
			<?php if( $is_member ){ ?>
	        <span class="profile_img"><?php echo get_member_profile_img($member['mb_id']); ?></span>
	        <strong><?php echo $member['mb_nick'] ?></strong>
	        <?php } else {  ?>
	        <a href="<?php echo GML_BBS_URL ?>/login.php"><?php e__('Login'); ?></a>
	        <?php }  ?>
        </div>

        <button type="button" id="user_btn" class="hd_opener"><i class="fa fa-search" aria-hidden="true"></i><span class="sound_only"><?php e__('Menu'); ?></span></button>
        <div class="hd_div" id="user_menu">
	        <div id="hd_sch">
	            <h2><?php e__('All Search in Site'); ?></h2>
	            <form name="fsearchbox" action="<?php echo GML_BBS_URL ?>/search.php" onsubmit="return fsearchbox_submit(this);" method="get">
	            <input type="hidden" name="sfl" value="wr_subject||wr_content">
	            <input type="hidden" name="sop" value="and">
	            <input type="text" name="stx" id="sch_stx" placeholder="<?php e__('Enter search term'); ?>" required maxlength="20">
	            <button type="submit" value="<?php e__('Search'); ?>" id="sch_submit"><i class="fa fa-search" aria-hidden="true"></i><span class="sound_only"><?php e__('Search'); ?>"></span></button>
	            <button type="button" id="user_close" class="hd_closer"><span class="sound_only"><?php e__('Menu'); ?></span><?php e__('close'); ?></button>
	            </form>
				<?php
                get_localize_script('locale_head',
                array(
                'character_msg'=>__('Please enter at least two characters for search term.'),  // 검색어는 두글자 이상 입력하십시오.
                'gap_msg'=>__('For quick searching, you can only enter one space in the search term.'),    // 빠른 검색을 위하여 검색어에 공백은 한개만 입력할 수 있습니다.
                'open_msg'=>__('Open'),     //열기
                'close_msg'=>__('Close'),   //닫기
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
	        </div>
	        <?php echo popular('basic'); // 인기검색어 ?>
		</div>

        <script>
        $(function () {
            //폰트 크기 조정 위치 지정
            var font_resize_class = get_cookie("ck_font_resize_add_class");
            if( font_resize_class == 'ts_up' ){
                $("#text_size button").removeClass("select");
                $("#size_def").addClass("select");
            } else if (font_resize_class == 'ts_up2') {
                $("#text_size button").removeClass("select");
                $("#size_up").addClass("select");
            }

            $(".hd_opener").on("click", function() {
                var $this = $(this);
                var $hd_layer = $this.next(".hd_div");

                if($hd_layer.is(":visible")) {
                    $hd_layer.hide();
                    $this.find("span").text(locale_head.open_msg);
                } else {
                    var $hd_layer2 = $(".hd_div:visible");
                    $hd_layer2.prev(".hd_opener").find("span").text(locale_head.open_msg);
                    $hd_layer2.hide();

                    $hd_layer.show();
                    $this.find("span").text(locale_head.close_msg);
                }
            });

            $("#container").on("click", function() {
                $(".hd_div").hide();

            });

            $(".btn_gnb_op").click(function(){
                $(this).toggleClass("btn_gnb_cl").next(".gnb_2dul").slideToggle(300);

            });

            $(".hd_closer").on("click", function() {
                var idx = $(".hd_closer").index($(this));
                $(".hd_div:visible").hide();
                $(".hd_opener:eq("+idx+")").find("span").text(locale_head.open_msg);
            });
            // 현재 선택한 메뉴 탭에 강조표시
            $('.swiper-slide').each(function() {
                var menu_a_tag = $(this).find('a'),
                    menu_href = menu_a_tag.attr('href');

                if(menu_href == window.location.href) {
                    menu_a_tag.addClass('gnb_sl');
                    return false;
                }
            });
        });
        </script>
    </div>
</header>


<div id="wrapper">

    <div id="container">
    <?php // if (!defined("_INDEX_")) { ?>
    <?php if (!defined("_INDEX_") && !(defined("_DONT_WRAP_IN_CONTAINER_") && _DONT_WRAP_IN_CONTAINER_ === true)) {?>
    	<h2 id="container_title" class="top" title="<?php echo get_text($gml['title']); ?>"><a href="javascript:history.back();"><i class="fa fa-angle-left"></i><span class="sound_only"><?php e__('Previous'); ?></span></a><?php echo get_head_title($gml['title']); ?></h2>
    <?php } ?>
