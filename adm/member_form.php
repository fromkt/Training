<?php
$sub_menu = "200100";
include_once('./_common.php');

auth_check($auth[$sub_menu], 'w');

if ($w == '')
{
    $required_mb_id = 'required';
    $required_mb_id_class = 'required alnum_';
    $required_mb_password = 'required';
    $sound_only = '<strong class="sound_only">'.__('required').'</strong>';

    $mb['mb_mailling'] = 1;
    $mb['mb_open'] = 1;
    $mb['mb_level'] = $config['cf_register_level'];
    $html_title = __('Add');
}
else if ($w == 'u')
{
    $mb = get_member($mb_id);
    if (!$mb['mb_id'])
        alert(__('This member data does not exist.'));

    if ($is_admin != 'super' && $mb['mb_level'] >= $member['mb_level'])
        alert(__('You can not modify a member who has more authority than you or is equal to you.'));

    $required_mb_id = 'readonly';
    $required_mb_password = '';
    $html_title = __('Edit');

    $mb['mb_name'] = get_text($mb['mb_name']);
    $mb['mb_nick'] = get_text($mb['mb_nick']);
    $mb['mb_email'] = get_text($mb['mb_email']);
    $mb['mb_homepage'] = get_text($mb['mb_homepage']);
    $mb['mb_birth'] = get_text($mb['mb_birth']);
    $mb['mb_tel'] = get_text($mb['mb_tel']);
    $mb['mb_hp'] = get_text($mb['mb_hp']);
    $mb['mb_addr1'] = get_text($mb['mb_addr1']);
    $mb['mb_addr2'] = get_text($mb['mb_addr2']);
    $mb['mb_addr3'] = get_text($mb['mb_addr3']);
    $mb['mb_signature'] = get_text($mb['mb_signature']);
    $mb['mb_recommend'] = get_text($mb['mb_recommend']);
    $mb['mb_profile'] = get_text($mb['mb_profile']);
    $mb['mb_1'] = get_text($mb['mb_1']);
    $mb['mb_2'] = get_text($mb['mb_2']);
    $mb['mb_3'] = get_text($mb['mb_3']);
    $mb['mb_4'] = get_text($mb['mb_4']);
    $mb['mb_5'] = get_text($mb['mb_5']);
    $mb['mb_6'] = get_text($mb['mb_6']);
    $mb['mb_7'] = get_text($mb['mb_7']);
    $mb['mb_8'] = get_text($mb['mb_8']);
    $mb['mb_9'] = get_text($mb['mb_9']);
    $mb['mb_10'] = get_text($mb['mb_10']);
}
else
    alert(__('The correct value has not been exceeded'));

//메일수신
$mb_mailling_yes    =  $mb['mb_mailling']   ? 'checked="checked"' : '';
$mb_mailling_no     = !$mb['mb_mailling']   ? 'checked="checked"' : '';

// SMS 수신
$mb_sms_yes         =  $mb['mb_sms']        ? 'checked="checked"' : '';
$mb_sms_no          = !$mb['mb_sms']        ? 'checked="checked"' : '';

// 정보 공개
$mb_open_yes        =  $mb['mb_open']       ? 'checked="checked"' : '';
$mb_open_no         = !$mb['mb_open']       ? 'checked="checked"' : '';

// 지번주소 필드추가
if(!isset($mb['mb_zip'])) {
    sql_query(" ALTER TABLE {$gml['member_table']} ADD `mb_zip` char(6) NOT NULL DEFAULT '' AFTER `mb_dupinfo` ", false);
}
if(!isset($mb['mb_country'])) {
    sql_query(" ALTER TABLE {$gml['member_table']} ADD `mb_country` varchar(20) NOT NULL DEFAULT '' AFTER `mb_zip` ", false);
}

if ($mb['mb_intercept_date']) $gml['title'] = __('Blocked').' ';
else $gml['title'] .= "";
$gml['title'] .= __('Member').' '.$html_title;
include_once('./admin.head.php');
?>

<form name="fmember" id="fmember" action="./member_form_update.php" onsubmit="return fmember_submit(this);" method="post" enctype="multipart/form-data">
<input type="hidden" name="w" value="<?php echo $w ?>">
<input type="hidden" name="sfl" value="<?php echo $sfl ?>">
<input type="hidden" name="stx" value="<?php echo $stx ?>">
<input type="hidden" name="sst" value="<?php echo $sst ?>">
<input type="hidden" name="sod" value="<?php echo $sod ?>">
<input type="hidden" name="page" value="<?php echo $page ?>">
<input type="hidden" name="token" value="">

<div class="frm_wr">
    <ul class="frm_ul">
        <li>
            <span class="lb_block"><label for="mb_id"><?php e__('ID'); ?><?php echo $sound_only ?></label></span>
            <input type="text" name="mb_id" value="<?php echo $mb['mb_id'] ?>" id="mb_id" <?php echo $required_mb_id ?> class="frm_input <?php echo $required_mb_id_class ?>" size="15"  maxlength="20">
            <?php if ($w=='u'){ ?><a href="./boardgroupmember_form.php?mb_id=<?php echo $mb['mb_id'] ?>" class="btn_frmline"><?php e__('View accessible groups'); ?></a><?php } ?>
            
            <a href="./point_list.php?sfl=mb_id&amp;stx=<?php echo $mb['mb_id'] ?>" target="_blank" class="btn_frmline"><?php echo sprintf(n__('Point : %s point', 'Point : %s points', $mb['mb_point']), number_format($mb['mb_point'])); ?></a>
            
        </li>
        <li class="li_50">
            <span class="lb_block"><label for="mb_password"><?php e__('Password'); ?><?php echo $sound_only ?></label></span>
            <input type="password" name="mb_password" id="mb_password" <?php echo $required_mb_password ?> class="frm_input frm_input_full <?php echo $required_mb_password ?>" size="15" maxlength="20">
        </li>
        <li class="li_50">
            <span class="lb_block"><label for="mb_name"><?php e__('Name (real name)'); ?><strong class="sound_only"><?php e__('required'); ?></strong></label></span>
            <input type="text" name="mb_name" value="<?php echo $mb['mb_name'] ?>" id="mb_name" required class="required frm_input frm_input_full " size="15" maxlength="20">
        </li>
        <li class="li_50">
            <span class="lb_block"><label for="mb_nick"><?php e__('Nickname'); ?><strong class="sound_only"><?php e__('required'); ?></strong></label></span>
            <input type="text" name="mb_nick" value="<?php echo $mb['mb_nick'] ?>" id="mb_nick" required class="required frm_input frm_input_full " size="15"  maxlength="20">
        </li>
        <li class="li_50">
            <span class="lb_block"><label for="mb_level"><?php e__('Member Level'); ?></label></span>
            <?php echo get_member_level_select('mb_level', 1, $member['mb_level'], $mb['mb_level']) ?>
        </li>
        <li class="li_50">
            <span class="lb_block"><label for="mb_email"><?php e__('E-mail'); ?><strong class="sound_only"><?php e__('required'); ?></strong></label></span>
            <input type="text" name="mb_email" value="<?php echo $mb['mb_email'] ?>" id="mb_email" maxlength="100" required class="required frm_input email frm_input_full " size="30">
        </li>
        <li class="li_50">
            <span class="lb_block"><label for="mb_homepage"><?php e__('Homepage'); ?></label></span>
            <input type="text" name="mb_homepage" value="<?php echo $mb['mb_homepage'] ?>" id="mb_homepage" class="frm_input frm_input_full " maxlength="255" size="15">
        </li>
        <li class="li_50">
            <span class="lb_block"><label for="mb_hp"><?php e__('Mobile number'); ?></label></span>
            <input type="text" name="mb_hp" value="<?php echo $mb['mb_hp'] ?>" id="mb_hp" class="frm_input frm_input_full " size="15" maxlength="20">
        </li>
        <li class="li_50">
            <span class="lb_block"><label for="mb_tel"><?php e__('Phone number'); ?></label></span>
            <input type="text" name="mb_tel" value="<?php echo $mb['mb_tel'] ?>" id="mb_tel" class="frm_input frm_input_full " size="15" maxlength="20">
        </li>
        <li class="li_address li_clear">
            <span class="lb_block"><?php e__('Address'); ?></span>
            <?php echo get_form_address($mb, array(
            'mb_country'=>'class="frm_input"',
            'mb_zip'=>'class="frm_input"',
            'mb_addr1'=>'class="frm_input frm_input_full" size="60"',
            'mb_addr2'=>'class="frm_input frm_input_full" size="60"',
            'mb_addr3'=>'class="frm_input frm_input_full" size="60"'
            ),
            array(
            'mb_country'=>'reg_mb_country',
            'mb_zip'=>'reg_mb_zip',
            'mb_addr1'=>'reg_mb_addr1',
            'mb_addr2'=>'reg_mb_addr2',
            'mb_addr3'=>'reg_mb_addr3'
            )); ?>            
        </li>
        <li>
            <span class="lb_block"><label for="mb_icon"><?php e__('Member icon'); ?></label>
            <?php echo help(sprintf(__('Image size should be %s pixels width and %s pixels height.'), $config['cf_member_icon_width'], $config['cf_member_icon_height'])); ?></span>
            <input type="file" name="mb_icon" id="mb_icon">
            <?php
            $mb_dir = substr($mb['mb_id'],0,2);
            $icon_file = GML_DATA_PATH.'/member/'.$mb_dir.'/'.get_mb_icon_name($mb['mb_id']).'.gif';
            if (file_exists($icon_file)) {
                $icon_url = str_replace(GML_DATA_PATH, GML_DATA_URL, $icon_file);
                echo '<img src="'.$icon_url.'" alt="">';
                echo '<input type="checkbox" id="del_mb_icon" name="del_mb_icon" value="1">'.__('Delete');
            }
            ?>
            
        </li>
        <li>
            <span class="lb_block"><label for="mb_img"><?php e__('Member image'); ?></label>
            <?php echo help(sprintf(__('Image size should be %s pixels width and %s pixels height.'), $config['cf_member_img_width'], $config['cf_member_img_height'])) ?></span>
            <input type="file" name="mb_img" id="mb_img">
            <?php
            $mb_dir = substr($mb['mb_id'],0,2);
            $icon_file = GML_DATA_PATH.'/member_image/'.$mb_dir.'/'.get_mb_icon_name($mb['mb_id']).'.gif';
            if (file_exists($icon_file)) {
                $icon_url = str_replace(GML_DATA_PATH, GML_DATA_URL, $icon_file);
                echo '<img src="'.$icon_url.'" alt="">';
                echo '<input type="checkbox" id="del_mb_img" name="del_mb_img" value="1">'.__('Delete');
            }
            ?>
        </li>
        <li>
            <span class="lb_block"><?php e__('Receiving mail'); ?></span>
            
                <input type="radio" name="mb_mailling" value="1" id="mb_mailling_yes" <?php echo $mb_mailling_yes; ?>>
                <label for="mb_mailling_yes"><?php e__('Yes'); ?></label>
                <input type="radio" name="mb_mailling" value="0" id="mb_mailling_no" <?php echo $mb_mailling_no; ?>>
                <label for="mb_mailling_no"><?php e__('No'); ?></label>
        </li>
        <li class="li_50">
            <span class="lb_block"><label for="mb_sms_yes"><?php e__('Receiving SMS'); ?></label></span>
            
                <input type="radio" name="mb_sms" value="1" id="mb_sms_yes" <?php echo $mb_sms_yes; ?>>
                <label for="mb_sms_yes"><?php e__('Yes'); ?></label>
                <input type="radio" name="mb_sms" value="0" id="mb_sms_no" <?php echo $mb_sms_no; ?>>
                <label for="mb_sms_no"><?php e__('No'); ?></label>
            
        </li>
        <li class="li_50">
            <span class="lb_block"><label for="mb_open"><?php e__('Open profile'); ?></label></span>
            
                <input type="radio" name="mb_open" value="1" id="mb_open_yes" <?php echo $mb_open_yes; ?>>
                <label for="mb_open_yes"><?php e__('Yes'); ?></label>
                <input type="radio" name="mb_open" value="0" id="mb_open_no" <?php echo $mb_open_no; ?>>
                <label for="mb_open_no"><?php e__('No'); ?></label>
            
        </li>
        <li class="li_clear">
            <span class="lb_block"><label for="mb_signature"><?php e__('Signature'); ?></label></span>
            <textarea  name="mb_signature" id="mb_signature"><?php echo $mb['mb_signature'] ?></textarea>
        </li>
        <li>
            <span class="lb_block"><label for="mb_profile"><?php e__('Introduce Myself'); ?></label></span>
            <textarea name="mb_profile" id="mb_profile"><?php echo $mb['mb_profile'] ?></textarea>
        </li>
        <li>
            <span class="lb_block"><label for="mb_memo"><?php e__('Memo'); ?></label></span>
            <textarea name="mb_memo" id="mb_memo"><?php echo $mb['mb_memo'] ?></textarea>
        </li>

        <?php if ($w == 'u') { ?>
        <li class="li_50">
            <span class="lb_block"><?php e__('Registered Date'); ?></span>
            <?php echo $mb['mb_datetime'] ?>
        </li>
        <li class="li_50">
            <span class="lb_block"><?php e__('Last Login Date'); ?></span>
            <?php echo $mb['mb_today_login'] ?>
        </li>
        <li class="li_clear">
            <span class="lb_block">IP</span>
            <?php echo $mb['mb_ip'] ?>
        </li>
        <?php if ($config['cf_use_email_certify']) { ?>
        <li>
            <span class="lb_block"><?php e__('Certification date'); ?></span>
            
            <?php if ($mb['mb_email_certify'] == '0000-00-00 00:00:00') { ?>
            <?php echo help(__('If other member can not receive mail, you can authenticate directly.')); ?>
            <input type="checkbox" name="passive_certify" id="passive_certify">
            <label for="passive_certify"><?php e__('Manual authentication'); ?></label>
            <?php } else { ?>
            <?php echo $mb['mb_email_certify'] ?>
            <?php } ?>
        </li>
        <?php } ?>
        <?php }     //end if w == u ?>
        <?php if ($config['cf_use_recommend']) { // 추천인 사용 ?>
        <li>
            <span class="lb_block"><?php e__('Recommendation'); ?></span>
            <?php echo ($mb['mb_recommend'] ? get_text($mb['mb_recommend']) : __('None')); ?>
        </li>
        <?php } ?>

        <li class="li_50">
            <span class="lb_block"><label for="mb_leave_date"><?php e__('Leave_date'); ?></label></span>
            
            <input type="text" name="mb_leave_date" value="<?php echo $mb['mb_leave_date'] ?>" id="mb_leave_date" class="frm_input date_input" maxlength="8">
            <input type="checkbox" value="<?php echo date("Ymd"); ?>" id="mb_leave_date_set_today" onclick="if (this.form.mb_leave_date.value==this.form.mb_leave_date.defaultValue) {
            this.form.mb_leave_date.value=this.value; } else { this.form.mb_leave_date.value=this.form.mb_leave_date.defaultValue; }">
            <label for="mb_leave_date_set_today"><?php e__('Specify withdrawal date as today'); ?></label>
        </li>
        <li class="li_50">
            <span class="lb_block"><?php e__('Access Block Date'); ?></span>
            
            <input type="text" name="mb_intercept_date" value="<?php echo $mb['mb_intercept_date'] ?>" id="mb_intercept_date" class="frm_input date_input" maxlength="8">
            <input type="checkbox" value="<?php echo date("Ymd"); ?>" id="mb_intercept_date_set_today" onclick="if
            (this.form.mb_intercept_date.value==this.form.mb_intercept_date.defaultValue) { this.form.mb_intercept_date.value=this.value; } else {
            this.form.mb_intercept_date.value=this.form.mb_intercept_date.defaultValue; }">
            <label for="mb_intercept_date_set_today"><?php e__('Specify Access Block Date as of today'); ?></label>
            
        </li>
        
        <?php
        //소셜계정이 있다면
        if(function_exists('social_login_link_account') && $mb['mb_id'] ){
            if( $my_social_accounts = social_login_link_account($mb['mb_id'], false, 'get_data') ){ ?>

        <li class="li_clear">
            
            <span class="lb_block"><?php e__('List of social accounts'); ?></span>

            <ul class="social_link_box">
                <li class="social_login_container">
                    <h4><?php e__('List of linked social accounts'); ?></h4>
                    <?php foreach($my_social_accounts as $account){     //반복문
                        if( empty($account) ) continue;

                        $provider = strtolower($account['provider']);
                        $provider_name = social_get_provider_service_name($provider);
                    ?>
                    <div class="account_provider" data-mpno="social_<?php echo $account['mp_no'];?>" >
                        <div class="sns-wrap-32 sns-wrap-over">
                            <span class="sns-icon sns-<?php echo $provider; ?>" title="<?php echo $provider_name; ?>">
                                <span class="ico"></span>
                                <span class="txt"><?php echo $provider_name; ?></span>
                            </span>

                            <span class="provider_name"><?php echo $provider_name;   // Service Name?> ( <?php echo $account['displayname']; ?> )</span>
                            <span class="account_hidden" style="display:none"><?php echo $account['mb_id']; ?></span>
                        </div>
                        <div class="btn_info"><a href="<?php echo GML_SOCIAL_LOGIN_URL.'/unlink.php?mp_no='.$account['mp_no'] ?>" class="social_unlink" data-provider="<?php echo $account['mp_no'];?>" ><?php e__('Disconnect'); ?></a> <span class="sound_only"><?php echo substr($account['mp_register_day'], 2, 14); ?></span></div>
                    </div>
                    <?php } //end foreach ?>
                </li>
            </ul>

        </li>

        <?php
            }   //end if
        }   //end if

        start_event('admin_member_form_tag', $w, $mb_id);
        ?>
        <?php for ($i=1; $i<=10; $i++) { ?>
        <li class="li_clear">
            <span class="lb_block"><label for="mb_<?php echo $i ?>"><?php echo sprintf(__('Extra field %d'), $i); ?></label></span>
            <input type="text" name="mb_<?php echo $i ?>" value="<?php echo $mb['mb_'.$i] ?>" id="mb_<?php echo $i ?>" class="frm_input frm_input_full" size="30" maxlength="255">
        </li>
        <?php } ?>

    </ul>
</div>

<div class="btn_fixed_top">
    <a href="./member_list.php?<?php echo $qstr ?>" class="btn btn_02"><?php e__('List'); ?></a>
    <input type="submit" value="<?php e__('Save'); ?>" class="btn_submit btn" accesskey='s'>
</div>
</form>

<?php
get_localize_script('member_form',
array(
'delete_link_msg'=>__('Are you sure you want to delete this account association?'),  // 정말 이 계정 연결을 삭제하시겠습니까?
'wrong_mpno_msg'=>__('Invalid request! The mp_no value does not exist.'),    // 잘못된 요청! mp_no 값이 없습니다.
'disconnect_msg'=>__('The connection has been disconnected.'),  // 연결이 해제 되었습니다.
'icon_file_msg'=>__('Icon file can only be a gif file.'),    // 아이콘은 gif 파일만 가능합니다.
),
true);
?>
<script>
jQuery(function($){
    $(".account_provider").on("click", ".social_unlink", function(e){
        e.preventDefault();

        if (!confirm( member_form.delete_link_msg )) {
            return false;
        }

        var ajax_url = "<?php echo GML_SOCIAL_LOGIN_URL.'/unlink.php' ?>";
        var mb_id = '',
            mp_no = $(this).attr("data-provider"),
            $mp_el = $(this).parents(".account_provider");

            mb_id = $mp_el.find(".account_hidden").text();

        if( ! mp_no ){
            alert( member_form.wrong_mpno_msg );
            return;
        }

        $.ajax({
            url: ajax_url,
            type: 'POST',
            data: {
                'mp_no': mp_no,
                'mb_id': mb_id
            },
            dataType: 'json',
            async: false,
            success: function(data, textStatus) {
                if (data.error) {
                    alert(data.error);
                    return false;
                } else {
                    alert(member_form.disconnect_msg);
                    $mp_el.fadeOut("normal", function() {
                        $(this).remove();
                    });
                }
            }
        });

        return;
    });
});

function fmember_submit(f)
{
    <?php start_event('admin_member_form_sumit', $w, $mb_id); ?>

    if (!f.mb_icon.value.match(/\.gif$/i) && f.mb_icon.value) {
        alert( member_form.icon_file_msg );
        return false;
    }

    return true;
}

<?php start_event('admin_member_form_script', $w, $mb_id); ?>

</script>

<?php
include_once('./admin.tail.php');
?>
