<?php
$sub_menu = "100290";
include_once('./_common.php');

if ($is_admin != 'super')
    alert_close(__('Only the Super administrator can access it.'));

$gml['title'] = __('Add Menu');
include_once(GML_PATH.'/head.sub.php');

$code = isset($code) ? preg_replace('/[^0-9a-zA-Z]/', '', strip_tags($code)) : '';

// 코드
if($new == 'new' || !$code) {
    $code = base_convert(substr($code,0, 2), 36, 10);
    $code += 36;
    $code = base_convert($code, 10, 36);
}
?>

<div id="menu_frm" class="new_win">
    <h1><?php echo $gml['title']; ?></h1>

    <form name="fmenuform" id="fmenuform">
    <div class="new_win_con">
        <div class="new_win_desc">
            <label for="me_type" class="sch_tit"><?php e__('Select Target'); ?></label>
            <select name="me_type" id="me_type">
                <option value=""><?php e__('Enter'); ?></option>
                <option value="group"><?php e__('Board Group'); ?></option>
                <option value="board"><?php e__('Board'); ?></option>
                <option value="content"><?php e__('Manage Content'); ?></option>
            </select>
        </div>

        <div id="menu_result"></div>
    </div>
    </form>

</div>

<?php
get_localize_script('menu_form',
array(
'menu_txt'=>__('Menu'),   //메뉴
'req_txt'=>__('Required'),   //필수
'url_txt'=>p__('link URL', 'link url'),   //URL
'win_txt'=>__('Window open'),   //새창
'order_txt'=>__('Order'),   //순서
'pc_txt'=>__('Print PC'),   //PC출력
'mo_txt'=>__('Print Mobile'),   //모바일출력
'add_txt'=>__('Add'),   //추가
'del_txt'=>__('Delete'),    //삭제
'en_txt'=>__('Enable'),    //활성화
'dis_txt'=>__('Disabled'),    //비활성화
),
true);
?>
<script>
jQuery(function($) {
    $("#menu_result").load(
        "./menu_form_search.php"
    );

    function link_checks_all_chage(){

        var $links = $(opener.document).find("#menulist input[name='me_link[]']"),
            $o_link = $(".td_mngsmall input[name='link[]']"),
            hrefs = [],
            menu_exist = false;

        if( $links.length ){
            $links.each(function( index ) {
                hrefs.push( $(this).val() );
            });

            $o_link.each(function( index ) {
                if( $.inArray( $(this).val(), hrefs ) != -1 ){
                    $(this).closest("tr").find("td:eq( 0 )").addClass("exist_menu_link");
                    menu_exist = true;
                }
            });
        }

        if( menu_exist ){
            $(".menu_exists_tip").show();
        } else {
            $(".menu_exists_tip").hide();
        }
    }

    function menu_result_change( type ){

        var dfd = new $.Deferred();

        $("#menu_result").empty().load(
            "./menu_form_search.php",
            { type : type },
            function(){
                dfd.resolve('Finished');
            }
        );

        return dfd.promise();
    }

    $("#me_type").on("change", function() {
        var type = $(this).val();

        var promise = menu_result_change( type );

        promise.done(function(message) {
            link_checks_all_chage(type);
        });

    });

    $(document).on("click", "#add_manual", function() {
        var me_name = $.trim($("#me_name").val());
        var me_link = $.trim($("#me_link").val());

        add_menu_list(me_name, me_link, "<?php echo $code; ?>");
    });

    $(document).on("click", ".add_select", function() {
        var me_name = $.trim($(this).siblings("input[name='subject[]']").val());
        var me_link = $.trim($(this).siblings("input[name='link[]']").val());

        add_menu_list(me_name, me_link, "<?php echo $code; ?>");
    });

    //tooltip
    $(document).on("click", ".tooltip_btn", function(e){
        $(this).next(".tooltip").toggle();
    });
    //tooltip
    $(".tooltip_btn").click(function(){
        $(this).next(".tooltip").toggle();
    })
    $(document).mouseup(function (e) {
        var container = $(".tooltip");
        if (!container.is(e.target) && container.has(e.target).length === 0){
        container.css("display","none");
        }
    });

});

function add_menu_list(name, link, code)
{
    var $menulist = $("#menulist", opener.document);
    var ms = new Date().getTime();
    var sub_menu_class;
    var ie_edge = /Edge/.test(navigator.userAgent);         // 엣지브라우저에서 <tr></tr><tr></tr> 두번이 안되는 버그가 있음

    <?php if($new == 'new') { ?>
    sub_menu_class = "";
    <?php } else { ?>
    sub_menu_class = " class=\" sub_menu_class\"";
    <?php } ?>

    var list = "<tr class=\"menu_list menu_group_<?php echo $code; ?>\">";
    list += "<td"+sub_menu_class+">";
    list += "<label for=\"me_name_"+ms+"\"  class=\"sound_only\">"+menu_form.menu_txt+"<strong class=\"sound_only\"> "+menu_form.req_txt+"</strong></label>";
    list += "<input type=\"hidden\" name=\"code[]\" value=\"<?php echo $code; ?>\">";
    list += "<input type=\"text\" name=\"me_name[]\" value=\""+name+"\" id=\"me_name_"+ms+"\" required class=\"required frm_input full_input\">";
    list += "</td>";

    list += "<td colspan=\"4\">";
    list += "<div>";
    list += "<label for=\"me_link_"+ms+"\"  class=\"sound_only\">"+menu_form.url_txt+"<strong class=\"sound_only\"> "+menu_form.req_txt+"</strong></label>";
    list += "<input type=\"text\" name=\"me_link[]\" value=\""+link+"\" id=\"me_link_"+ms+"\" required class=\"required frm_input full_input\">";
    list += "</div>";

    if ( ie_edge ) {
        list += "<div class=\"fclear\">";
            list += "<div class=\"w_25p\">";
            list += "<label for=\"me_target_"+ms+"\"  class=\"sound_only\">"+menu_form.win_txt+"</label>";
            list += "<select name=\"me_target[]\" id=\"me_target_"+ms+"\">";
            list += "<option value=\"self\">"+menu_form.en_txt+"</option>";
            list += "<option value=\"blank\">"+menu_form.dis_txt+"</option>";
            list += "</select>";
            list += "</div>";
            list += "<div class=\"w_25p\">";
            list += "<label for=\"me_order_"+ms+"\"  class=\"sound_only\">"+menu_form.order_txt+"<strong class=\"sound_only\"> "+menu_form.req_txt+"</strong></label>";
            list += "<input type=\"text\" name=\"me_order[]\" value=\"0\" id=\"me_order_"+ms+"\" required class=\"required frm_input\" size=\"5\">";
            list += "</div>";
            list += "<div class=\"w_25p\">";
            list += "<label for=\"me_use_"+ms+"\"  class=\"sound_only\">"+menu_form.pc_txt+"</label>";
            list += "<select name=\"me_use[]\" id=\"me_use_"+ms+"\">";
            list += "<option value=\"1\">"+menu_form.en_txt+"</option>";
            list += "<option value=\"0\">"+menu_form.dis_txt+"</option>";
            list += "</select>";
            list += "</div>";
            list += "<div class=\"w_25p\">";
            list += "<label for=\"me_mobile_use_"+ms+"\"  class=\"sound_only\">"+menu_form.mo_txt+"</label>";
            list += "<select name=\"me_mobile_use[]\" id=\"me_mobile_use_"+ms+"\">";
            list += "<option value=\"1\">"+menu_form.en_txt+"</option>";
            list += "<option value=\"0\">"+menu_form.dis_txt+"</option>";
            list += "</select>";
            list += "</div>";
        list += "</div>";
    }

    list += "</td>";
    
    if ( ie_edge ) {
        list += "<td class=\"td_mng td_mng_s\">";
    } else {
        list += "<td class=\"td_mng td_mng_s\" rowspan=\"2\">";
    }
    <?php if($new == 'new') { ?>
    list += "<button type=\"button\" class=\"btn_add_submenu btn_03\">"+menu_form.add_txt+"</button><br>";
    <?php } ?>
    list += "<button type=\"button\" class=\"btn_del_menu btn_02\">"+menu_form.del_txt+"</button>";
    list += "</td>";
    list += "</tr>";

    if ( ! ie_edge ) {
        list += "<tr class=\"menu_list menu_group_<?php echo $code; ?>\">";
        list += "<td></td>";
        list += "<td class=\"td_select\">";
        list += "<label for=\"me_target_"+ms+"\"  class=\"sound_only\">"+menu_form.win_txt+"</label>";
        list += "<select name=\"me_target[]\" id=\"me_target_"+ms+"\">";
        list += "<option value=\"self\">"+menu_form.en_txt+"</option>";
        list += "<option value=\"blank\">"+menu_form.dis_txt+"</option>";
        list += "</select>";
        list += "</td>";
        list += "<td class=\"td_select\">";
        list += "<label for=\"me_order_"+ms+"\"  class=\"sound_only\">"+menu_form.order_txt+"<strong class=\"sound_only\"> "+menu_form.req_txt+"</strong></label>";
        list += "<input type=\"text\" name=\"me_order[]\" value=\"0\" id=\"me_order_"+ms+"\" required class=\"required frm_input\" size=\"5\">";
        list += "</td>";
        list += "<td class=\"td_select\">";
        list += "<label for=\"me_use_"+ms+"\"  class=\"sound_only\">"+menu_form.pc_txt+"</label>";
        list += "<select name=\"me_use[]\" id=\"me_use_"+ms+"\">";
        list += "<option value=\"1\">"+menu_form.en_txt+"</option>";
        list += "<option value=\"0\">"+menu_form.dis_txt+"</option>";
        list += "</select>";
        list += "</td>";
        list += "<td class=\"td_mng\">";
        list += "<label for=\"me_mobile_use_"+ms+"\"  class=\"sound_only\">"+menu_form.mo_txt+"</label>";
        list += "<select name=\"me_mobile_use[]\" id=\"me_mobile_use_"+ms+"\">";
        list += "<option value=\"1\">"+menu_form.en_txt+"</option>";
        list += "<option value=\"0\">"+menu_form.dis_txt+"</option>";
        list += "</select>";
        list += "</td>";
        list += "</tr>";
    }

    var $menu_last = null;

    if(code)
        $menu_last = $menulist.find("tr.menu_group_"+code+":last");
    else
        $menu_last = $menulist.find("tr.menu_list:last");

	if($menu_last.size() > 0) {
        $menu_last.after(list);
    } else {
        if($menulist.find("#empty_menu_list").size() > 0)
            $menulist.find("#empty_menu_list").remove();

        $menulist.find("table tbody").append(list);
    }

    $menulist.find("tr.menu_list").each(function(index) {
        $(this).removeClass("bg0 bg1")
            .addClass("bg"+(index % 2));
    });

    window.close();
}
</script>

<?php
include_once(GML_PATH.'/tail.sub.php');
?>
