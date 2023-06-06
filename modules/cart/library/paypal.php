<?php

/**
 * Paypal class.
 *
 * @version $Id: paypal.php 238 2010-03-30 05:20:13Z perfilev $
 * @package Application
 */

class Paypal
{

	/**
	 * @var  array  Website settings.
	 */
	protected $settings;

	/**
	 * @var  array  Class instance.
	 */
	protected static $instance;

	/**
	 * Returns an instance of Auth class by the given session id.
	 *
	 * @return  Auth
	 */
	public static function getInstance()
	{
		if (null == self::$instance) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Consrtuctor.
	 *
	 * @return  Auth
	 */
	public function __construct()
	{
		$this->settings = System::$global->settings;
	}

	public function request($method, $values)
	{
		$nvpStr = '';
		foreach ($values as $key => $value) {
			$nvpStr .= '&' . strtoupper($key) . '=' . urlencode($value);
		}
		$endpoint = 'https://api-3t.paypal.com/nvp';
		
		if($this->settings['sandbox']) {
			$endpoint = 'https://api-3t.sandbox.paypal.com/nvp';
		}
		$version = urlencode('51.0');

		// Set the curl parameters.
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $endpoint);
		curl_setopt($ch, CURLOPT_VERBOSE, 1);

		// Turn off the server and peer verification (TrustManager Concept).
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
		
		// Set the API operation, version, and API signature in the request.
		$nvpReq = 'METHOD=' . urlencode($method). '&VERSION=' . urlencode($version) .
			'&PWD=' . urlencode($this->settings['paypalPassword']). '&USER=' . urlencode($this->settings['paypalUsername']) .
			'&SIGNATURE=' . urlencode($this->settings['paypalSignature']). $nvpStr;

		// Set the request as a POST FIELD for curl.
		curl_setopt($ch, CURLOPT_POSTFIELDS, $nvpReq);

		// Get response from the server.

		$httpResponse = curl_exec($ch);

		if (!$httpResponse) {
			$error = $method . ' failed: ' . curl_error($ch) . '(' . curl_errno($ch) . ')';
		}

		// Extract the response details.
		$httpResponseArr = explode("&", $httpResponse);

		$httpParsedResponseArr = array();
		foreach ($httpResponseArr as $key => $value) {
			$tmpArr = explode('=', $value);
			if (sizeof($tmpArr) > 1) {
				$httpParsedResponseArr[strtolower($tmpArr[0])] = urldecode($tmpArr[1]);
			}
		}

		if ((0 == count($httpParsedResponseArr)) || !array_key_exists('ACK', $httpParsedResponseArr)) {
			$error = 'Invalid HTTP Response for POST request (' . $nvpReq . ') to ' . $endpoint . '.';
		}

		return $httpParsedResponseArr;
	}

	public function setExpressCheckoutUrl($token)
	{
		$url = 'https://www.paypal.com/webscr&cmd=_express-checkout&token=' . $token;
		if(System::$global->settings['sandbox']) {
			$url = 'https://www.sandbox.paypal.com/webscr&cmd=_express-checkout&token=' . $token;
		}
		return $url;
	}

}