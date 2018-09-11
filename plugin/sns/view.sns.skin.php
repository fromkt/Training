<?php
if (!defined('_GNUBOARD_')) exit; // Unable to access direct pages

if (!$board['bo_use_sns']) return;

$sns_msg = urlencode(str_replace('\"', '"', $view['subject']));
//$sns_url = googl_short_url('http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
//$msg_url = $sns_msg.' : '.$sns_url;

/*
$facebook_url  = 'http://www.facebook.com/sharer/sharer.php?s=100&p[url]='.$sns_url.'&p[title]='.$sns_msg;
$twitter_url   = 'http://twitter.com/home?status='.$msg_url;
$gplus_url     = 'https://plus.google.com/share?url='.$sns_url;
*/

$sns_send  = GML_BBS_URL.'/sns_send.php?longurl='.urlencode('http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
//$sns_send .= '&amp;title='.urlencode(utf8_strcut(get_text($view['subject']),140));
$sns_send .= '&amp;title='.$sns_msg;

$facebook_url = $sns_send.'&amp;sns=facebook';
$twitter_url  = $sns_send.'&amp;sns=twitter';
$gplus_url    = $sns_send.'&amp;sns=gplus';
?>

<?php if(GML_IS_MOBILE && $config['cf_kakao_js_apikey']) { ?>
<script src="https://developers.kakao.com/sdk/js/kakao.min.js"></script>
<script src="<?php echo GML_JS_URL; ?>/kakaolink.js"></script>
<script>
    // 사용할 앱의 Javascript 키를 설정해 주세요.
    Kakao.init("<?php echo $config['cf_kakao_js_apikey']; ?>");
</script>
<?php } ?>

<div class="bo_v_snswr">
	<button type="button" class="btn_b03 btn_share" id="btn_share"><i class="fa fa-share-alt" aria-hidden="true"></i><span class="sound_only"><?php e__('SNS sharing'); ?></span></button>
	<div id="bo_v_sns">
		<h3><?php e__('SNS sharing'); ?></h3>
		<ul>
		    <li><a href="<?php echo $twitter_url; ?>" target="_blank" class="sns_t"><img src="<?php echo GML_SNS_URL; ?>/icon/twitter.png" alt="<?php e__('Send to Twitter'); ?>" width="20"></a></li>
		    <li><a href="<?php echo $facebook_url; ?>" target="_blank" class="sns_f"><img src="<?php echo GML_SNS_URL; ?>/icon/facebook.png" alt="<?php e__('Send to Facebook'); ?>" width="20"></a></li>
		    <li><a href="<?php echo $gplus_url; ?>" target="_blank" class="sns_g"><img src="<?php echo GML_SNS_URL; ?>/icon/gplus.png" alt="<?php e__('Send to Google+'); ?>" width="20"></a></li>
		    <?php if(GML_IS_MOBILE && $config['cf_kakao_js_apikey']) { ?>
		    <li><a href="javascript:kakaolink_send('<?php echo $sns_msg; ?>', '<?php echo urlencode('http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']); ?>');" class="sns_k"><img src="<?php echo GML_SNS_URL; ?>/icon/kakaotalk.png" alt="<?php e__('Send to KakaoTalk'); ?>" width="20"></a></li>
		    <?php } ?>
	    </ul>
	</div>
</div>
