function chk_captcha()
{
	if ( ! jQuery('#g-recaptcha-response').val()) {
		alert("Please check Recaptcha");
		return false;
	}

	return true;
}