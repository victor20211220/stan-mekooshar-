<?php

/**
 * Kit.
 *
 * Response library.
 *
 * @version  $Id: response.php 77 2010-07-11 00:01:20Z eprev $
 * @package  System
 */

class System_Response
{
	/**
	 * @var array  HTTP status codes and messages.
	 */
	public static $messages = array(
		// Informational 1xx
		100 => 'Continue',
		101 => 'Switching Protocols',

		// Success 2xx
		200 => 'OK',
		201 => 'Created',
		202 => 'Accepted',
		203 => 'Non-Authoritative Information',
		204 => 'No Content',
		205 => 'Reset Content',
		206 => 'Partial Content',

		// Redirection 3xx
		300 => 'Multiple Choices',
		301 => 'Moved Permanently',
		302 => 'Found', // 1.1
		303 => 'See Other',
		304 => 'Not Modified',
		305 => 'Use Proxy',
		307 => 'Temporary Redirect',

		// Client Error 4xx
		400 => 'Bad Request',
		401 => 'Unauthorized',
		402 => 'Payment Required',
		403 => 'Forbidden',
		404 => 'Not Found',
		405 => 'Method Not Allowed',
		406 => 'Not Acceptable',
		407 => 'Proxy Authentication Required',
		408 => 'Request Timeout',
		409 => 'Conflict',
		410 => 'Gone',
		411 => 'Length Required',
		412 => 'Precondition Failed',
		413 => 'Request Entity Too Large',
		414 => 'Request-URI Too Long',
		415 => 'Unsupported Media Type',
		416 => 'Requested Range Not Satisfiable',
		417 => 'Expectation Failed',

		// Server Error 5xx
		500 => 'Internal Server Error',
		501 => 'Not Implemented',
		502 => 'Bad Gateway',
		503 => 'Service Unavailable',
		504 => 'Gateway Timeout',
		505 => 'HTTP Version Not Supported',
		509 => 'Bandwidth Limit Exceeded'
	);

	/**
	 * @var decimal  HTTP version: 1.0, 1.1, etc.
	 */
	public $version = 1.1;

	/**
	 * @var integer  HTTP response code: 200, 404, 500, etc.
	 */
	public $status;

	/**
	 * @var string  Response content.
	 */
	public $body;

	/**
	 * @var array  Headers to send with the response body.
	 */
	public $headers;

	/**
	 * Constructor.
	 *
	 * @param integer $status   HTTP response code.
	 * @param string  $body     Response content.
	 * @param array   $headers  Response headers.
	 * @return this
	 */
	public function __construct($status = 200, $body = null, $headers = array('content-type' => 'text/html; charset=utf-8'))
	{
		$this->status  = $status;
		$this->body    = $body;
		$this->headers = $headers;
	}

	/**
	 * Gets an header value.
	 *
	 * @param string $name  Header name.
	 * @return string|false
	 */
	public function getHeader($name)
	{
		$name = strtolower($name);
		return isset($this->headers[$name]) ? $this->headers[$name] : false;
	}

	/**
	 * Sets an header. Use null as the header value to remove it from
	 * the header list.
	 *
	 * @param string $name   Header name.
	 * @param string $value  Header value.
	 * @return this
	 */
	public function setHeader($name, $value)
	{
		$name = strtolower($name);
		if ($value === null) {
			unset($this->headers[$name]);
		} else {
			$this->headers[$name] = $value;
		}
		return $this;
	}

	/**
	 * Sets an unamed header. Raw headers cannot be retrieved with getHeader!
	 *
	 * @param string $value  Header string.
	 * @return this
	 */
	public function setRawHeader($value)
	{
		$this->headers[] = $value;
		return $this;
	}

	/**
	 * Sends the response status and all set headers.
	 *
	 * @return this
	 */
	public function sendHeaders()
	{
		if (false == headers_sent()) {
			$message = self::$messages[$this->status];
			header("HTTP/{$this->version} {$this->status} {$message}", true, $this->status);
			foreach ($this->headers as $name => $value) {
				if (is_string($name)) {
					// Convert the header name to Title-Case, to match RFC spec
					$name = str_replace('-', ' ', $name);
					$name = str_replace(' ', '-', ucwords($name));
					$value = "{$name}: {$value}";
				}
				header($value, true);
			}
		}
		return $this;
	}

	/**
	 * Redirects as the request response.
	 *
	 * @param string  $url   Redirect location.
	 * @param integer $code  Status code.
	 * @return void
	 */
	public function redirect($url, $code = 302)
	{
		$this->status = $code;
		$this->setHeader('location', $url);
		$this->sendHeaders();
		exit(0);
	}

	/**
	 * Sends response headers and body.
	 *
	 * @param function $handler  Output handler.
	 * @return void
	 */
	public function send($handler = null)
	{
		if ($handler) {
			$body = call_user_func($handler, $this->body);
		} else {
			$body = $this->body;
		}
//		$this->setHeader('Content-Length', strlen($body));
		$this->sendHeaders();
		echo $body;
	}
}
