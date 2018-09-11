<?php
if (!defined('_GNUBOARD_')) exit;

add_stylesheet('<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.13/css/all.css">', 1);

$gml_debug['begin_time'] = $begin_time = get_microtime();

include_once(GML_PATH.'/head.sub.php');

function print_menu1($key, $no='')
{
    global $menu;

    $str = print_menu2($key, $no);

    return $str;
}

function print_menu2($key, $no='')
{
    global $menu, $auth_menu, $is_admin, $auth, $gml, $sub_menu;

    $str .= "<ul>";
    for($i=1; $i<count($menu[$key]); $i++)
    {
        if ($is_admin != 'super' && (!array_key_exists($menu[$key][$i][0],$auth) || !strstr($auth[$menu[$key][$i][0]], 'r')))
            continue;

        if (($menu[$key][$i][4] == 1 && $gnb_grp_style == false) || ($menu[$key][$i][4] != 1 && $gnb_grp_style == true)) $gnb_grp_div = 'gnb_grp_div';
        else $gnb_grp_div = '';

        if ($menu[$key][$i][4] == 1) $gnb_grp_style = 'gnb_grp_style';
        else $gnb_grp_style = '';

        $current_class = '';

        if ($menu[$key][$i][0] == $sub_menu){
            $current_class = ' on';
        }

        $str .= '<li class="gnb_2dli" data-menu="'.$menu[$key][$i][0].'"><i class="fa fa-caret-right"></i> <a href="'.$menu[$key][$i][2].'" class="gnb_2da '.$gnb_grp_style.' '.$gnb_grp_div.$current_class.'">'.$menu[$key][$i][1].'</a></li>';

        $auth_menu[$menu[$key][$i][0]] = $menu[$key][$i][1];
    }
    $str .= "</ul>";

    return $str;
}

$adm_menu_cookie = array(
'container' => '',
'gnb'       => '',
'btn_gnb'   => '',
);

if( ! empty($_COOKIE['gml_admin_btn_gnb']) ){
    $adm_menu_cookie['container'] = 'container-small';
    $adm_menu_cookie['gnb'] = 'gnb_small';
    $adm_menu_cookie['btn_gnb'] = 'btn_gnb_open';
}
?>

<script>
var tempX = 0;
var tempY = 0;

function imageview(id, w, h)
{

    menu(id);

    var el_id = document.getElementById(id);

    //submenu = eval(name+".style");
    submenu = el_id.style;
    submenu.left = tempX - ( w + 11 );
    submenu.top  = tempY - ( h / 2 );

    selectBoxVisible();

    if (el_id.style.display != 'none')
        selectBoxHidden(id);
}
</script>

<div id="to_content"><a href="#container"><?php e__('Go to Body'); ?></a></div>

<header id="hd">
        <h1><?php echo $config['cf_title'] ?></h1>

        <div id="hd_top">

           <button type="button" id="btn_gnb" class="btn_gnb_close <?php echo $adm_menu_cookie['btn_gnb'];?>"><i class="fa fa-bars"></i><span class="sound_only"><?php e__('Toggle Menu'); ?></span></button>
           <div id="logo"><a href="<?php echo GML_ADMIN_URL ?>"><img src="<?php echo GML_ADMIN_URL ?>/img/logo.png" alt="<?php echo sprintf(__('%s Admin'), $config['cf_title']); ?>"></a></div>

            <div id="tnb">
                <ul>
                    <li class="tnb_li"><a href="<?php echo GML_URL ?>" class=" tnb_community" target="_blank"><i class="fa fa-home" aria-hidden="true"></i><span class="sound_only"><?php e__('Go to Community'); ?></span></a></li>
                    <li class="tnb_li m_no"><button type="button" class="tnb_mb_btn"><?php echo e__('Admin'); ?><span class="./img/btn_gnb.png"><?php e__('Open Menu'); ?></span></button>
                        <ul class="tnb_mb_area">
                            <li><a href="<?php echo GML_ADMIN_URL ?>/member_form.php?w=u&amp;mb_id=<?php echo $member['mb_id'] ?>"><?php e__('Edit Profile'); ?></a></li>
                            <li id="tnb_logout"><a href="<?php echo GML_BBS_URL ?>/logout.php"><?php e__('Logout'); ?></a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
        <nav id="gnb" class="<?php echo $adm_menu_cookie['gnb']; ?>">
            <h2><?php e__('Admin Main Menu');   //관리자 주메뉴 ?></h2>
            <ul class="gnb_ul">
                <?php
                $jj = 1;
                foreach($amenu as $key=>$value) {
                    $href1 = $href2 = '';

                    if ($menu['menu'.$key][0][2]) {
                        $href1 = '<a href="'.$menu['menu'.$key][0][2].'" class="gnb_1da">';
                        $href2 = '</a>';
                    } else {
                        continue;
                    }

                    $current_class = "";
                    if (isset($sub_menu) && (substr($sub_menu, 0, 3) == substr($menu['menu'.$key][0][0], 0, 3)))
                        $current_class = " on";

                    $button_title = $menu['menu'.$key][0][1];
                ?>
                <li class="gnb_li<?php echo $current_class;?>  gnb_li<?php echo $jj; ?>">
                    <button type="button" class="btn_op menu-<?php echo $jj; ?>" title="<?php echo $button_title;?>"><?php echo $button_title;?></button>
                    <div class="gnb_oparea_wr">
                        <div class="gnb_oparea">
                            <h3><?php echo $menu['menu'.$key][0][1];?></h3>
                            <?php echo print_menu1('menu'.$key, 1); ?>
                        </div>
                    </div>
                </li>
                <?php
                $jj++;
                }     //end foreach
                ?>
            </ul>
        </nav>

</header>
<script>
jQuery(function($){

    var menu_cookie_key = 'gml_admin_btn_gnb',
        is_gnb_resize = false,
        current_window_width = 0;

    $(".gnb_ul li .btn_op" ).click(function() {
        $(this).parent().addClass("on").siblings().removeClass("on");
    });

    $(".tnb_mb_btn").click(function(){
        $(".tnb_mb_area").toggle();
    });

    $("#btn_gnb").click(function(){

        var $this = $(this);
        
        current_window_width = $(window).width();

        try {
            if( ! $this.hasClass("btn_gnb_open") ){
                set_cookie(menu_cookie_key, 1, 60*60*24*365);
            } else {
                delete_cookie(menu_cookie_key);
            }
        }
        catch(err) {
        }

        $("#container").toggleClass("container-small");
        $("#gnb").toggleClass("gnb_small");
        $this.toggleClass("btn_gnb_open");
    });

    function btn_gnb_resize_check(){
        
        var window_width = $(window).width();

        if ( window_width < 768 ) {
            if( current_window_width != window_width ){
                if ( ! $("#btn_gnb").hasClass("btn_gnb_open") ){
                    $("#btn_gnb").trigger("click");
                    is_gnb_resize = true;
                    current_window_width = window_width;
                }
            }
        } else {
            if( is_gnb_resize ){
                $("#btn_gnb").trigger("click");
                is_gnb_resize = false;
            }
        }
    }

    btn_gnb_resize_check();

    $(window).on("resize", function(event){
        btn_gnb_resize_check();
    });
    
    $(".gnb_ul").on("click", "a", function(e){
        if ( $(window).width() < 768 ) {
            set_cookie(menu_cookie_key, 1, 60*60*24*365);
        }
    });

    $(".gnb_ul li .btn_op" ).click(function() {
        $(this).parent().addClass("on").siblings().removeClass("on");
    });

    $(".m_btn_gnb").click(function(){
        //$("#gnb").toggle();
    });


});
</script>

<div id="wrapper">

    <div id="container" class="<?php echo $adm_menu_cookie['container']; ?>">

        <h1 id="container_title"><?php echo $gml['title'] ?></h1>
        <div class="container_wr">
