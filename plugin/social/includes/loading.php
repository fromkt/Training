<?php
if (!defined('_GNUBOARD_')) exit;
?>
<!DOCTYPE html>
	<head>
		<meta name="robots" content="NOINDEX, NOFOLLOW">
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <meta name="viewport" content="width=device-width,initial-scale=1, minimum-scale=1, maximum-scale=1, user-scalable=yes">
		<title><?php e__('Social login'); ?> - <?php echo $provider; ?></title>
	</head>
	<body>
        <table width="100%" border="0">
          <tr>
            <td align="center" height="190px" valign="middle"><img src="<?php echo $img_url;?>loading_icon.gif" /></td>
          </tr>
          <tr>
            <td align="center"><br /><h3>Loading...</h3><br /></td> 
          </tr>
          <tr>
            <td align="center"><?php echo sprintf(__('Connecting to %s. Please wait a moment.'), '<b>'.ucfirst(strtolower(strip_tags($provider))).'</b>'); ?></td> 
          </tr> 
        </table>

        <?php if( (defined('GML_SOCIAL_IS_LOADING') && GML_SOCIAL_IS_LOADING ) || (GML_SOCIAL_USE_POPUP && empty($login_action_url)) ){ ?>
        <script>
            window.location.href = window.location.href + "&redirect_to_idp=1";
        </script>
        <?php } else { ?>
		<form name="loginform" method="post" action="<?php echo $login_action_url; ?>">
			<input type="hidden" id="url" name="url" value="<?php echo $url ?>">
			<input type="hidden" id="provider" name="provider" value="<?php echo $provider ?>">
            <input type="hidden" id="mb_id" name="mb_id" value="<?php echo $mb_id ?>">
            <input type="hidden" id="mb_password" name="mb_password" value="<?php echo $mb_password ?>">
		</form>
		<script>
			function init()
			{
				<?php
					if( $use_popup == 1 || ! $use_popup ){
						?>
							if( window.opener )
							{
								window.opener.name = "social_login";
                                document.loginform.target = window.opener.name;
                                document.loginform.submit();
								window.close();
							}
							else
							{
								document.loginform.submit();
							}
						<?php
					}
					elseif( $use_popup == 2 ){
						?>
							document.loginform.submit();
						<?php
					}
				?>
			}
            init();
		</script>
        <?php } //end if ?>
	</body>
</html>
<?php
die();
?>