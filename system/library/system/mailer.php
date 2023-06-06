<?php

/**
 *
 * Mailer library.
 *
 * @author UkieTech Corporation
 * @copyright Copyright UkieTech Corp. (http://ukietech.com/)
 * @link http://myevasystem.com/
 *
 */

class System_Mailer
{
	/**
	 * Returns a new Mailer object.
	 *
	 * @param   string  $templat  View's template.
	 * @param   array   $data     Array of values.
	 * @return  this
	 */
	public static function factory($template, array $data = array())
	{
		return new static($template, $data);
	}

	/**
	 * @var  string  Template's subject.
	 */
	protected $subject;

	/**
	 * @var  string  Templates's headers.
	 */
	protected $headers = array();

	/**
	 * @var  string  Template.
	 */
	protected $template;

	/**
	 * @var  string  Template's body.
	 */
	protected $body;

	/**
	 * @var  array  Array of local variables.
	 */
	protected $data;

	/**
	 * @var  array  Array of attachments.
	 */
	protected $attachments = array();

	/**
	 * @var  array  Multipart frontier.
	 */
	protected $frontier = 'mail-frontier-12345';

	/**
	 * @var string  <CR><LF>.
	 */
	const CRLF = "\r\n";

	/**
	 * Sets the initial view's templare and local data.
	 *
	 * @param  string  $template  View's template.
	 * @param  array   $data      Array of values.
	 * @return void
	 */
	public function __construct($template, array $data = array())
	{
		$this->data = $data;

		$this->setHeader('MIME-Version', '1.0');
		$this->setHeader('Content-Type', 'text/html; charset=utf-8');

		// Parse the template

		if (null === $path = System::locate("views/mailer/$template.php")) {
			throw new Exception('Template "' . $template . '" does not exist.');
		}

		$this->template = $template;
		$lines = preg_split('/\r?\n/u', file_get_contents($path));

		$emptyLine = false;

		foreach ($lines as $line) {
			if ($emptyLine) {
				$this->body .= (null === $this->body) ? $line : self::CRLF . $line;
			} else {
				if ('' == $line) {
					$emptyLine = true;
				} else {
					if(preg_match('/^(.+?):(.+)$/', $line, $matches)) {
						if(trim($matches[1]) == 'Subject') {
							$this->subject = trim($matches[2]);
						} else {
							$this->setHeader($matches[1], $matches[2]);
						}
					}
				}
			}
		}
	}

	/**
	 * Add header to message
	 *
	 * Sample:
	 * $this->header('MIME-Version', '1.0');
	 *
	 * @param string $title Title ot title and value
	 * @param string $value Value
	 */
	private function setHeader($title, $value)
	{
		$this->headers[trim($title)] = trim($value);
	}

	/**
	 * Add ReplyTo email
	 *
	 * @param string $email Email to reply
	 */
	public function replyTo($email)
	{
		$this->setHeader('Reply-To', $email);
	}

	/**
	 * Add Priority to email. Values 1, 3, 5. 1 - most important
	 *
	 * @param string $email Email to reply
	 */
	public function priority($value)
	{
		$this->setHeader('X-Priority', $value);
		if($value == 1) {
			$this->setHeader('Importance', 'High');
			$this->setHeader('X-MSMail-Priority', 'High');
		}
	}

	/**
	 * Magic method, searches for the given variable and returns its value.
	 * Local variables will be returned before global variables.
	 *
	 * @param   string $key  Variable name.
	 * @return  mixed
	 */
	public function __get($key)
	{
		return array_key_exists($key, $this->data) ? $this->data[$key] : null;
	}

	/**
	 * Magic method, calls set() with the same parameters.
	 *
	 * @param  string  $key    Variable name.
	 * @param  mixed   $value  Value.
	 * @return void
	 */
	public function __set($key, $value)
	{
		$this->set($key, $value);
	}

	/**
	 * Assigns a variable by name.
	 *
	 * @param  string  $key    Variable name or an array of variables.
	 * @param  mixed   $value  Value.
	 * @return this
	 */
	public function set($key, $value = null)
	{
		if (is_array($key)) {
			foreach ($key as $name => $value) {
				$this->data[$name] = $value;
			}
		} else {
			$this->data[$key] = $value;
		}
		return $this;
	}

	/**
	 * Unset local variable.
	 *
	 * @param  string  $key  Variable name.
	 * @return void
	 */
	public function __unset($key)
	{
		unset($this->data[$key]);
	}

	/**
	 * Add attachment
	 *
	 * @param string $file Path to the attachment
	 * @param string $name File name
	 */
	public function attachment($file, $name)
	{
		if (!file_exists($file)) {
			return false;
		}
		if (!is_file($file)) {
			return false;
		}
		if (!is_readable($file)) {
			return false;
		}
		if(!strlen($name)) {
			return false;
		}

		$attachment = file_get_contents(ltrim($file, '/'));
		$this->attachments[] = array($name, $attachment);

		$this->setHeader('Content-Type', 'multipart/mixed; boundary="'.$this->frontier.'"');

		return true;
	}

	/**
	 * Grab attachments for message
	 *
	 * @return string
	 */
	public function getAttachments()
	{
		$attachments = '';
		if(!empty($this->attachments)) {
			foreach($this->attachments as $attach) {
				$attachments .= self::CRLF;
				$attachments .= '--' . $this->frontier . self::CRLF;
				$attachments .= 'Content-Type: application/octet-stream; name="'.$attach[0].'"' . self::CRLF;
				$attachments .= 'Content-Disposition: attachment; filename="'.$attach[0].'"' . self::CRLF;
				$attachments .= 'Content-Transfer-Encoding: base64' . self::CRLF;
				$attachments .= self::CRLF;
				$attachments .= chunk_split(base64_encode($attach[1]));
			}
		}

		return $attachments;
	}

	/**
	 * Renders the view object to a message and sends it to the given recipient.
	 *
	 * @param  string		$to        Recipient address.
	 * @param  boolean		$defer     Delivery message by CRON?
	 * @param  boolean|string	$timeSend  When send message?
	 * @return boolean
	 * @throws Exception
	 */
	public function send($to, $defer = false, $timeSend = false)
	{
		$subject = $this->capture('?>' . $this->subject, $this->data);
		$message = $this->capture('?>' . $this->body,    $this->data);

		$body = self::CRLF;
		if(!empty($this->attachments)) {
			$body .= '--'.$this->frontier . self::CRLF;
			$body .= 'Content-Type: text/html; charset=utf-8' . self::CRLF;
			$body .= 'Content-Transfer-Encoding: base64' . self::CRLF . self::CRLF;
		} else {
			$this->setHeader('Content-Transfer-Encoding', 'base64');
		}

		$body .= chunk_split(base64_encode($message));

		if(!empty($this->attachments)) {
			$body .= $this->getAttachments();
			$body .= self::CRLF . '--'.$this->frontier.'--';
		}

		$headers = array();
		foreach($this->headers as $name => $value) {
			$name = $this->capture('?>' . $name, $this->data);
			$value = $this->capture('?>' . $value, $this->data);

			$headers[] = "$name: $value";
		}

		$headers = implode(self::CRLF, $headers);

		if ($defer) {
			try {
				$mail = Model_Mailer::create(array(
					'recipient' => $to,
					'message' => serialize(array(
						'subject' => $subject,
						'headers' => $headers,
						'body'    => $body
					)),
					'sendAfter' => $timeSend ? $timeSend : date('Y-m-d H:i:s', CURRENT_TIMESTAMP)
				));
			} catch (Exception $e) {
				Log::getInstance()->write('Mail failure: ' . $e, __METHOD__);
				return false;
			}
			return $mail->id;
		} else {
			if (false === Smtp::getInstance()->send($to, $subject, $body, $headers)) {
				Log::getInstance()->write('Mail failure', __METHOD__);
				return false;
			} else {
				Log::getInstance('mailer')->write($subject . " <$to>", $this->template);
				return true;
			}
		}
	}

	/**
	 * Captures the output that is generated when a message body is evaluated.
	 * The view data will be extracted to make local variables. This method
	 * is static to prevent object scope resolution.
	 *
	 * @param string $view  Message view.
	 * @param array  $data  Variables.
	 * @return string
	 */
	protected static function capture($view, array $data)
	{
		extract($data, EXTR_SKIP);
		ob_start();

		try {
			eval($view);
		} catch (Exception $e) {
			ob_end_clean();
			throw $e;
		}

		return ob_get_clean();
	}
}
