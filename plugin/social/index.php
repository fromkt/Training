<?php
/**
* HybridAuth
* http://hybridauth.sourceforge.net | http://github.com/hybridauth/hybridauth
* (c) 2009-2015, HybridAuth authors | http://hybridauth.sourceforge.net/licenses.html
*/

// ------------------------------------------------------------------------
//	HybridAuth End Point
// ------------------------------------------------------------------------

include_once('_common.php');

if( ! $config['cf_social_login_use']){
    die(__('Disable social login.'));
}

require_once( "./includes/gml_endpoint.php" );

error_reporting(0); // Turn off all error reporting

GML_Hybrid_Authentication::hybridauth_endpoint();
