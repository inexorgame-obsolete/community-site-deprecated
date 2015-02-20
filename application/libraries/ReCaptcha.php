<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class ReCaptcha
{

	private $_CI;
	private $_keys;
	private $_servers;
	private $_theme;

	// Private variables allowed to read from outside.
	private $_whitelist = array('is_valid', 'error');

	// Readonly's with magic method __get
	private $is_valid = false;
	private $error = false;

	/**
	 * Magic method
	 * Constructor
	 */
	public function __construct() {
		$this->_CI =& get_instance();
		$this->_CI->load->config('recaptcha');
		$this->_keys = $this->_CI->config->item('keys');
		$this->_CI->config->set_item('keys', false);
		$this->_servers = $this->_CI->config->item('servers');
		$this->_theme = $this->_CI->config->item('theme');
		if($this->_keys === false) {
			throw new Exception('Recaptcha Library cannot be loaded multiple times.');
		}
	}


	/**
	 * Magic method
	 * Allowes to read private & protected variables in the $this->_whitelist -array.
	 * @var string $variable name of the in-accessible variable.
	 * @return mixed Returns NULL if not set or not set in whitelist-array else returns (normally) in-accessible variable.
	 */
	public function __get($variable) {
		if(in_array($variable, $this->_whitelist) && isset($this->$variable)) {
			return $this->$variable;
		}
		return NULL;
	}


	/**
	 * Gets the challenge HTML (javascript and non-javascript version).
	 * This is called from the browser, and the resulting reCAPTCHA HTML widget
	 * is embedded within the HTML form it was called from.
	 * @param string $pubkey A public key for reCAPTCHA
	 * @param string $error The error given by reCAPTCHA (optional, default is null)
	 * @param boolean $use_ssl Should the request be made over ssl? (optional, default is true)
	 * @return string - The HTML to be embedded in the user's form.
	 */
	public function get_html ($error = null, $use_ssl = true)
	{
		$pubkey = $this->_keys['public'];
		if ($pubkey == null || $pubkey == '') {
			die ("To use reCAPTCHA you must get an API key from <a href='https://www.google.com/recaptcha/admin/create'>https://www.google.com/recaptcha/admin/create</a>");
		}

		if ($use_ssl) {
			$server = $this->_servers['api_secure'];
		} else {
			$server = $this->_servers['api'];
		}

		$errorpart = "";
		if ($error) {
			$errorpart = "&amp;error=" . $error;
		}
		if($this->_theme['theme'] != false && (!isset($this->_theme['custom_widget']) || $this->_theme['custom_widget'] == false || $this->_theme['custom_widget'] == null ))
			$theme = "<script type=\"text/javascript\">var RecaptchaOptions = { theme : '" . $this->_theme['theme'] . "' };</script>";
		elseif($this->_theme['theme'] != false && isset($this->_theme['custom_widget']))
			$theme = "<script type=\"text/javascript\">var RecaptchaOptions = { theme : 'custom', custom_theme_widget: '" . $this->_theme['custom_widget'] . "' };</script>";
		else
			$theme = '';
		return $theme . '<script type="text/javascript" src="'. $server . '/challenge?k=' . $pubkey . $errorpart . '"></script>

		<noscript>
		<iframe src="'. $server . '/noscript?k=' . $pubkey . $errorpart . '" height="300" width="500" frameborder="0"></iframe><br/>
		<textarea name="recaptcha_challenge_field" rows="3" cols="40"></textarea>
		<input type="hidden" name="recaptcha_response_field" value="manual_challenge"/>
	</noscript>';
	}


	/**
	 * Calls an HTTP POST function to verify if the user's guess was correct
	 * @param string $privkey
	 * @param string $remoteip
	 * @param string $challenge
	 * @param string $response
	 * @param array $extra_params an array of extra variables to post to the server
	 * @return ReCaptchaResponse
	 */
	public function check_answer($challenge = FALSE, $response = FALSE, $remoteip = FALSE, $extra_params = array())
	{
		if($challenge === FALSE || $challenge === null) $challenge = $_POST["recaptcha_challenge_field"];
		if($response  === FALSE || $response  === null) $response  = $_POST["recaptcha_response_field"];
		$privkey = $this->_keys['private'];
		if($remoteip === null || $remoteip === false) $remoteip = $_SERVER["REMOTE_ADDR"];

		if ($privkey == null || $privkey == '') {
			die ("To use reCAPTCHA you must get an API key from <a href='https://www.google.com/recaptcha/admin/create'>https://www.google.com/recaptcha/admin/create</a>");
		}


		if ($remoteip == null || $remoteip == '') {
			die ("For security reasons, you must pass the remote ip to reCAPTCHA");
		}



		//discard spam submissions
		if ($challenge == null || strlen($challenge) == 0 || $response == null || strlen($response) == 0) {
			$this->is_valid = false;
			$this->error = 'incorrect-captcha-sol';
		}

		$response = $this->_http_post($this->_servers['verify'], "/recaptcha/api/verify",
			array (
				'privatekey' => $privkey,
				'remoteip' => $remoteip,
				'challenge' => $challenge,
				'response' => $response
			) + $extra_params
		);

		$answers = explode ("\n", $response [1]);

		if (trim ($answers [0]) == 'true') {
			$this->is_valid = true;
		} else {
			$this->is_valid = false;
			$this->error = $answers [1];
		}

	}


	/**
	 * gets a URL where the user can sign up for reCAPTCHA. If your application
	 * has a configuration page where you enter a key, you should provide a link
	 * using this function.
	 * @param string $domain The domain where the page is hosted
	 * @param string $appname The name of your application
	 */
	public function get_signup_url ($domain = null, $appname = null) {
		return "https://www.google.com/recaptcha/admin/create?" .  $this->_qsencode (array ('domains' => $domain, 'app' => $appname));
	}


	/**
	 * Encodes the given data into a query string format
	 * @param $data - array of string elements to be encoded
	 * @return string - encoded request
	 */
	private function _qsencode ($data) {
		$req = "";
		foreach ( $data as $key => $value )
			$req .= $key . '=' . urlencode( stripslashes($value) ) . '&';

		// Cut the last '&'
		$req=substr($req,0,strlen($req)-1);
		return $req;
	}


	/**
	 * Submits an HTTP POST to a reCAPTCHA server
	 * @param string $host
	 * @param string $path
	 * @param array $data
	 * @param int port
	 * @return array response
	 */
	private function _http_post($host, $path, $data, $port = 80) {

		$req = $this->_qsencode ($data);

		$http_request  = "POST $path HTTP/1.0\r\n";
		$http_request .= "Host: $host\r\n";
		$http_request .= "Content-Type: application/x-www-form-urlencoded;\r\n";
		$http_request .= "Content-Length: " . strlen($req) . "\r\n";
		$http_request .= "User-Agent: reCAPTCHA/PHP\r\n";
		$http_request .= "\r\n";
		$http_request .= $req;

		$response = '';
		if( false == ( $fs = @fsockopen($host, $port, $errno, $errstr, 10) ) ) {
			die ('Could not open socket');
		}

		fwrite($fs, $http_request);

		while ( !feof($fs) )
			$response .= fgets($fs, 1160); // One TCP-IP packet
		fclose($fs);
		$response = explode("\r\n\r\n", $response, 2);

		return $response;
	}


	private function _aes_pad($val) {
		$block_size = 16;
		$numpad = $block_size - (strlen ($val) % $block_size);
		return str_pad($val, strlen ($val) + $numpad, chr($numpad));
	}

	/* Mailhide related code */


	/* gets the reCAPTCHA Mailhide url for a given email, public key and private key */
	public function mailhide_url($email) {
		$pubkey  = $this->_keys['public'];
		$privkey = $this->_keys['private'];
		if ($pubkey == '' || $pubkey == null || $privkey == "" || $privkey == null) {
			die ("To use reCAPTCHA Mailhide, you have to sign up for a public and private key, " .
				 "you can do so at <a href='http://www.google.com/recaptcha/mailhide/apikey'>http://www.google.com/recaptcha/mailhide/apikey</a>");
		}
		

		$ky = pack('H*', $privkey);
		$cryptmail = $this->_aes_encrypt ($email, $ky);
		
		return "http://www.google.com/recaptcha/mailhide/d?k=" . $pubkey . "&c=" . $this->_mailhide_urlbase64 ($cryptmail);
	}


	/**
	 * Gets html to display an email address given a public an private key.
	 * to get a key, go to:
	 *
	 * http://www.google.com/recaptcha/mailhide/apikey
	 */
	function mailhide_html($email) {
		$pubkey  = $this->_keys['public'];
		$privkey = $this->_keys['private'];
		$emailparts = $this->_mailhide_email_parts ($email);
		$url = $this->mailhide_url ($email);
		
		return htmlentities($emailparts[0]) . "<a href='" . htmlentities ($url) .
			"' onclick=\"window.open('" . htmlentities ($url) . "', '', 'toolbar=0,scrollbars=0,location=0,statusbar=0,menubar=0,resizable=0,width=500,height=300'); return false;\" title=\"Reveal this e-mail address\">...</a>@" . htmlentities ($emailparts [1]);

	}

	private function _aes_encrypt($val,$ky) {
		if (! function_exists ("mcrypt_encrypt")) {
			die ("To use reCAPTCHA Mailhide, you need to have the mcrypt php module installed.");
		}
		$mode=MCRYPT_MODE_CBC;   
		$enc=MCRYPT_RIJNDAEL_128;
		$val=$this->_aes_pad($val);
		return mcrypt_encrypt($enc, $ky, $val, $mode, "\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0");
	}


	private function _mailhide_urlbase64 ($x) {
		return strtr(base64_encode ($x), '+/', '-_');
	}


	/**
	 * gets the parts of the email to expose to the user.
	 * eg, given johndoe@example,com return ["john", "example.com"].
	 * the email is then displayed as john...@example.com
	 */
	private function _mailhide_email_parts ($email) {
		$arr = preg_split("/@/", $email );

		if (strlen ($arr[0]) <= 4) {
			$arr[0] = substr ($arr[0], 0, 1);
		} else if (strlen ($arr[0]) <= 6) {
			$arr[0] = substr ($arr[0], 0, 3);
		} else {
			$arr[0] = substr ($arr[0], 0, 4);
		}
		return $arr;
	}
}