<?php
$config['keys']['public']  = '6LeDNfsSAAAAAD5wl5J1ZETfZlY-VEf7xS25WHly';	// ReCaptcha public key;  Generate one at https://www.google.com/recaptcha/admin#list
$config['keys']['private'] = '6LeDNfsSAAAAAErQ9eTawkSyeef70wweK0EzmsUA';	// ReCaptcha private key; Generate one at https://www.google.com/recaptcha/admin#list
$config['theme']['theme'] = 'clean';
// $config['theme']['custom_widget'] = '';	// Only needed if theme is custom


// The reCaptcha servers
$config['servers']['api'] 			= "http://www.google.com/recaptcha/api";
$config['servers']['api_secure'] 	= "https://www.google.com/recaptcha/api";
$config['servers']['verify'] 		= "www.google.com";