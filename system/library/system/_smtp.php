<?php

/**
 * Kit.
 *
 * SMTP library.
 *
 * @version $Id: smtp.php 35 2010-06-13 23:52:13Z eprev $
 * @package System
 */

class System_Smtp
{
	/**
	 * @var string  <LF>
	 */
	const LF   = "\n";

	/**
	 * @var string  <CR><LF>.
	 */
	const CRLF = "\r\n";

	/**
	 * @var array  Class instances.
	 */
	protected static $instances = array();

	/**
	 * Returns an instance of class.
	 *
	 * @param string $profile  Config profile.
	 * @return object
	 * @throws InvalidArgumentError
	 */
	public static function getInstance($profile = 'default')
	{
		if (false == isset(self::$instances[$profile])) {
			$config = Config::getInstance()->smtp->$profile;
			self::$instances[$profile] = new static($config);
		}
		return self::$instances[$profile];
	}

	/**
	 * @var System_Config SMTP config.
	 */
	protected $config;

	/**
	 * @var Log  Logger.
	 */
	protected $log;

	/**
	 * @var resource  Connection.
	 */
	private $socket;

	/**
	 * Class constructor.
	 *
	 * @param array $config  SMTP options.
	 * @return this
	 */
	public function __construct(System_Config $config)
	{
		$this->config = $config->cloneWith(array(
			/**
			 * @var string SMTP hostname.
			 */
			'host'     => 'localhost',

			/**
			 * @var integer  SMTP port.
			 */
			'port'     => 25,

			/**
			 * @var integer  The connection timeout, in seconds.
			 */
			'timeout'  => null,

			/**
			 * @var string  Log profile name.
			 */
			'log'     => null,

			/**
			 * @var string  Default email priority (1 - Hight, 3 - Normal, 5 - Low).
			 */
			'priority'  => 3,

			/**
			 * @var string  Default From email address.
			 */
			'from'     => null,

			/**
			 * @var string  From name for the default From email.
			 */
			'fromName' => null,

			/**
			 * @var string  Charset of the message.
			 */
			'charset' => 'utf-8'
		));
		if ($this->config->log) {
			$this->log = Log::getInstance($this->config->log);
		}
	}

	/**
	 * Sends mail.
	 *
	 * @param string $to       Receivers of the mail.
	 * @param string $subject  Subject of the mail.
	 * @param string $message  Message to send.
	 * @param string $headers  Additional headers.
	 * @return boolean
	 */
	public function send($to, $subject, $message, $headers = null)
	{
		$headers .= 'To: ' . $to . self::CRLF;
		if (preg_match('/(^|\n)From:\s*(.*?)\s*($|\r|\n)/ui', $headers, $match)) {
			$from = $match[2];
		} else {
			$from = $this->config->from;
			$headers .= 'From: ' . $from . self::CRLF;
		}
		$from = trim($from);
		// Search for address in "Name <user@domain>"
		if (preg_match('/<(.*?)>/', $from, $matches)) {
			$from = trim($matches[1]);
		}
		$headers .= 'Subject: ' . $subject . self::CRLF;
		$headers .= 'X-Priority: ' . $this->config->priority . self::CRLF;

		try {
			$this->connect($this->config->host, $this->config->port, $this->config->timeout);
			$this->hello();
			$this->mail($from);
			$this->recipient($to);
			$this->data($headers . $message);
			$this->quit();
		} catch (Exception $e) {
			if ($this->log) {
				$this->log->write($e, __METHOD__);
			}
			return false;
		}
		return true;
	}

	/**
	 * Socket initalization.
	 *
	 * @return void
	 * @throws RuntimeException
	 */
	protected function connect($host, $port, $timeout)
	{
		if (false === ($this->socket = fsockopen($host, $port, $errno, $errstr, $timeout))) {
			throw new RuntimeException($errstr, $errno);
		}
		$this->read();
	}

	/**
	 * Closes the socket.
	 *
	 * @return void
	 */
	protected function close()
	{
		fclose($this->socket);
	}

	/**
	 * Puts data to the socket.
	 *
	 * @return void
	 * @throws RuntimeException
	 */
	protected function write($data)
	{
		if ($this->log) {
			$this->log->write($data, __METHOD__);
		}
		if (false === fputs($this->socket, $data)) {
			throw new RuntimeException('Failure of sending data to the server.');
		}
	}

	/**
	 * Reads data from the socket.
	 *
	 * Returns response code and message in array.
	 *
	 * @return array
	 */
	protected function read()
	{
		$code = null;
		$message = '';
		while ($line = @fgets($this->socket, 515)) {
			$message .= $line;
			if (' ' === substr($line, 3, 1)) {
				if ($this->log) {
					$this->log->write($message, __METHOD__);
				}
				$code = intval(substr($line, 0, 3));
				$message = substr($line, 4);
				break;
			}
		}
		return array($code, $message);
	}

	/**
	 * Implements RFC 821: DATA <CRLF>.
	 *
	 * @return void
	 * @throws RuntimeException
	 */
	protected function data($data)
	{
		$this->write('DATA' . self::CRLF);

		list($code, $message) = $this->read();

		if ($code != 354) {
			throw new RuntimeException($message, $code);
		}

		// Split data into lines
		$data  = str_replace("\r", "\n", str_replace("\r\n", "\n", $data));
		$lines = explode("\n", $data);

		// Maximum line length in DATA is 1000 including trailing CRLF.
		$maxLength = 998;

		// Headers first?
		$headers = preg_match('/^\S+\:/', $lines[0]);

		foreach ($lines as $line) {

			$s = array();

			if ($line == '' && $headers) {
				$headers = false;
			}

			while (strlen($line) > $maxLength) {
				$pos = strrpos($line, ' ', $mexLength - strlen($line));
				if(false === $pos) {
					$pos  = $maxLength - 1;
					$s[]  = substr($line, 0, $pos);
					$line = substr($line, $pos);
				} else {
					$s[]  = substr($line, 0, $pos);
					$line = substr($line, $pos + 1);
				}
				if ($headers) {
					$line = "\t" . $line; // Use LWSP in front of new lines for long headers
				}
			}
			$s[] = $line;

			foreach($s as $l) {
				if ('' != $l && $l[0] == '.') {
					$l = '.' . $l;
				}
				$this->write($l . self::CRLF);
			}
		}

		$this->write('.' . self::CRLF);

		list($code, $message) = $this->read();

		if($code != 250) {
			throw new RuntimeException($message, $code);
		}
	}

	/**
	 * Implements RFC 821: HELO <SP> <domain> <CRLF>
	 *
	 * @return void
	 * @throws RuntimeException
	 */
	public function hello()
	{
		try {
			$this->sayHello('EHLO');
		} catch (Excetion $e) {
			$this->sayHello('HELO');
		}
	}

	/**
	 * Helper for hello().
	 *
	 * @return void
	 * @throws RuntimeException
	 */
	private function sayHello($hello)
	{
		$this->write($hello . ' ' . $this->config->host . self::CRLF);
		list($code, $message) = $this->read();
		if($code != 250) {
			throw new RuntimeException($message, $code);
		}
	}

	/**
	 * Implements RFC 821: MAIL <SP> FROM: <reverse-path> <CRLF>
	 *
	 * @return void
	 * @throws RuntimeException
	 */
	public function mail($from)
	{
		$this->write('MAIL FROM: ' . $from . self::CRLF);
		list($code, $message) = $this->read();
		if($code != 250) {
			throw new RuntimeException($message, $code);
		}
	}

	/**
	 * Implements RFC 821: QUIT <CRLF>
	 *
	 * @return void
	 * @throws RuntimeException
	 */
	public function quit()
	{
		$this->write('QUIT' . self::CRLF);
		list($code, $message) = $this->read();
		if($code != 221) {
			throw new RuntimeException($message, $code);
		}
		$this->close();
	}

	/**
	 * Implements RFC 821: RCPT <SP> TO: <forward-path> <CRLF>
	 *
	 * @return void
	 * @throws RuntimeException
	 */
	public function recipient($to)
	{
		$this->write('RCPT TO: ' . $to . self::CRLF);
		list($code, $message) = $this->read();
		if($code != 250 && $code != 251) {
			throw new RuntimeException($message, $code);
		}
	}

	/**
	 * Implements RFC 821: RSET <CRLF>
	 *
	 * @return void
	 * @throws RuntimeException
	 */
	public function reset()
	{
		$this->write('RSET' . self::CRLF);
		list($code, $message) = $this->read();
		if($code != 250) {
			throw new RuntimeExcepton($message, $code);
		}
	}
}

/* Local Variables:	   */
/* tab-width: 4		   */
/* indent-tabs-mode: t */
/* End:                */
