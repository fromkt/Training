<?php
$sub_menu = "100550";
include_once('./_common.php');

$gml['title'] = __('Theme Language File');    //관리자메인

if ($is_admin != 'super') {
    alert(__('Only the Super administrator can access it.'));  //최고관리자만 접근 가능합니다.
}

auth_check($auth[$sub_menu], 'r');

$theme_list = get_skin_dir(GML_THEME_DIR, GML_PATH);

include_once ('./admin.head.php');
?>
<style>
.load_theme_lang.tbl_head01 th.txt_left{text-align:left}
.load_theme_lang .inner_theme_lang{margin-top:1em;position:relative;min-height:400px}
.load_theme_lang .inner_theme_lang .tmp_loading{position:absolute;top:0px;left:0px;display:block;}
.load_theme_lang .inner_theme_lang .tmp_loading img{width:100px;height:100px}
.load_theme_lang .inner_theme_lang table{width:100%;table-layout:fixed}
.load_theme_lang .inner_theme_lang th{width:50%;word-wrap:break-word}
.load_theme_lang .print_key{word-break: break-all;white-space:normal}
.load_theme_lang .inner_theme_lang td{width:50%;word-wrap:break-word}
.load_theme_lang .hide_tooltip{display:none}
.ui-tooltip-content li{margin-left:0;list-style:none;word-break: break-all;white-space:normal}
.ui-tooltip{max-width:400px !important}
.ajax_result .download_zipfile {line-height:1.5em}
.ajax_result .download_zipfile {border:2px solid #F73D0E;padding:1em;margin-bottom:1em;background:#fff;font-size:1.25em}
.ajax_result .download_zipfile a{text-decoration: underline;color:#5B0FFF;font-weight:bold}
.print_key .context{margin-right:6px;background-color:#e4f5fc;color:#258dc8}
</style>

<div id="ajax_load_theme_lang" class="tbl_head01 load_theme_lang">
    
    <div id="ajax_result" class="ajax_result"></div>

    <form name="theme_lang_form" id="theme_lang_form" action="./theme_lang_update.php" method="post">
        
        <input type="hidden" id="load_theme_lang" name="load_theme_lang" >
        <input type="hidden" id="load_theme_folder" name="load_theme_folder" >
        <input type="hidden" id="load_theme_param" name="load_theme_param" >
        <input type="hidden" id="load_option_text" name="load_option_text" >

        <div class="form_select_group">
            <?php echo get_lang_select_html('select_theme_lang', $config['cf_lang'], ''); ?>
            <select id="select_theme_folder" name="select_theme_folder">
                <option value=""><?php e__('Select Theme'); ?></option>
                <?php
                foreach((array) $theme_list as $theme_folder){
                ?>
                <option value="<?php echo $theme_folder; ?>"><?php echo $theme_folder; ?></option>
                <?php } ?>
            </select>

            <span class="load_select_theme_lang">
            </span>

        </div>

        <div class="inner_theme_lang">
        </div>

        <div class="btn_fixed_top btn_confirm">
            <input type="submit" value="<?php e__('Save'); ?>" class="btn_submit btn" accesskey="s">
        </div>

    </form>
</div>
<link type="text/css" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.9.2/themes/base/jquery-ui.css" rel="stylesheet" />
<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.9.2/jquery-ui.min.js"></script>
<?php
get_localize_script('theme_lang',
array(
'token_wrong_msg'  =>  __('Token information is invalid.'),    //토큰정보가 올바르지 않습니다.
'load_lang_msg'  =>  __('Please retrieve the language file information first.'),    //언어파일 정보를 먼저 불러와 주세요.
'no_translate_msg' => __('No translation to save.'), // 저장할 번역문이 없습니다.
'lang_confirm_msg1' => __('Language set : %s'), //언어셋 %s
'lang_confirm_msg2' => __('Are you sure you want to save the language file(po, mo files) for %s ?'), // %s 의 언어파일(po, mo files)을 정말 저장하시겠습니까?
),
true);
?>
<script>
var theme_obj = {};

jQuery(function($){
    
    var tooltips = $( document ).tooltip({
      items: ".print_key",
      content: function() {
        var element = $( this );
        if ( element.is( ".print_key" ) ) {
          var ul_el = element.next(".hide_tooltip");
          return ul_el.html();
        }
      },
      close: function (e, o) { },
    });

    theme_obj.loading = function( el, src ){
        if( !el || !src) return;
        $(el).append("<span class='tmp_loading'><img src='"+src+"' title='loading...' ></span>");
    }
    theme_obj.loadingEnd = function( el ){
        $(".tmp_loading", $(el)).remove();
    }
    
    theme_obj.load_theme_folder = function( params ){

        $.ajax({
            url: "./ajax.theme_get_translate.php",
            cache:false,
            timeout : 3000,
            dataType:"json",
            data:params,
            type: 'POST', // GET, PUT
            success: function(HttpRequest) {

                if (HttpRequest.error) {
                    alert(HttpRequest.error);
                    return false;
                } else {
                    var $content_box = $("#ajax_load_theme_lang .load_select_theme_lang");
                    $content_box.html( HttpRequest.content );
                }
            }

        });
    }

    theme_obj.select_page = function( params, option_txt ){

        theme_obj.loading(".load_theme_lang .inner_theme_lang", "./img/ajax_loader.gif" ); //로딩 이미지 보여줌

        $.ajax({
            url: "./ajax.theme_get_translate.php",
            cache:false,
            timeout : 3000,
            dataType:"json",
            data:params,
            type: 'POST', // GET, PUT
            success: function(HttpRequest) {

                if (HttpRequest.error) {
                    alert(HttpRequest.error);
                } else {

                    var $content_box = $("#ajax_load_theme_lang .inner_theme_lang");
                    $("#load_theme_lang").val( HttpRequest.load_theme_lang );
                    $("#load_theme_folder").val( HttpRequest.load_theme_folder );
                    $("#load_theme_param").val( HttpRequest.load_theme_param );
                    $("#load_option_text").val( option_txt );
                    $("#ajax_result").html('');

                    $content_box.html( HttpRequest.content );
                    
                }
                theme_obj.loadingEnd(".load_theme_lang .inner_theme_lang");
            }

        });
    }

    $("select#select_theme_folder").on("change", function(e) {
        
        $("#ajax_load_theme_lang .load_select_theme_lang").html('');

        if( ! this.value ) return false;
        
        var token = get_ajax_token();

        if(!token) {
            alert(theme_lang.token_wrong_msg);
            return false;
        }

        var params = { theme_folder : this.value, token : token, get_theme_info : 1 };

        theme_obj.load_theme_folder(params);
    });

    $(document).on("click", "button.theme_load_lang_info", function(e) {
        e.preventDefault();

        var $selector = $("select#select_theme_lang_file"),
            select_lang_val = $selector.val(),
            theme_folder = $selector.attr("data-theme") ? $selector.attr("data-theme") : $("select#select_theme_folder").val(),
            option_txt = $selector.find("option:selected").text();
        
        if( ! select_lang_val ) return;

        var token = get_ajax_token();
        
        if(!token) {
            alert(theme_lang.token_wrong_msg);
            return false;
        }
        
        var params = { theme_param : select_lang_val, theme_folder : theme_folder , select_theme_lang : $("#select_theme_lang").val(), token : token };

        theme_obj.select_page(params, option_txt);
    });
    
    $("#theme_lang_form").submit(function( e ) {
        e.preventDefault();

        var load_theme_lang = $("#load_theme_lang").val(),
            load_theme_folder = $("#load_theme_folder").val(),
            load_theme_param = $("#load_theme_param").val(),
            load_option_text = $("#load_option_text").val();

        if( ! load_theme_lang || ! load_theme_folder || ! load_theme_param ){
            alert(theme_lang.load_lang_msg);
            return false;
        }

        if( ! $(this).find('input[name="originals[]"]').length ){
            alert(theme_lang.no_translate_msg);
            return false;
        }
        
        if( confirm( js_sprintf(theme_lang.lang_confirm_msg1, load_theme_lang)+"\r"+js_sprintf(theme_lang.lang_confirm_msg2, load_option_text) ) ) {

            var params = $(this).serialize()+ "&is_ajax=1"

            $.ajax({
                url: "./theme_lang_update.php",
                cache:false,
                timeout : 3000,
                dataType:"json",
                data:params,
                type: 'POST', // GET, PUT
                success: function(HttpRequest) {

                    if ((typeof(HttpRequest.msg) != "undefined") && HttpRequest.msg) {
                        alert(HttpRequest.msg);
                        return false;
                    } else if ((typeof(HttpRequest.pre_msg) != "undefined") && HttpRequest.pre_msg) {
                        $("#ajax_result").html(HttpRequest.pre_msg);
                        return false;
                    }


                }
            }); //end ajax

        }   // end if confirm

        return false;
    });

});
</script>

<?php
include_once ('./admin.tail.php');
?>