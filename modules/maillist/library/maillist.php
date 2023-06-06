<?php

class Maillist
{

	protected $confirmationUrl = 'maillist/confirm/';

	public static function sendConfirmation($id = null, $email = null)
	{
		dump('TODO', 1);
		
		$subscriber = Model_Maillist::getSubscriber($id, $email);
		$mail = new Mailer();
		$mail->to = $subscriber['email'];
		$mail->from = $this->settings['title'] . ' <' . $this->settings['email'] . '>';
		$mail->subject = 'Confirm your subscription from ' . $this->settings['title'];
		$message = '<h1>Confirm your subscription</h1><br>';
		$message .= '<p>Follow this link to confirm your subscription: ' . Html::anchor(Url::site($this->confirmationUrl . $subscriber['token'])) .'</p><br>';
		$mail->message(array('html' => new View('cli/message', array('message' => $message))));
		$mail->send();
	}

	public static function subscribe($values, $confirmation = true)
	{
		
		$cnt = 0;
		while (true === Model_Maillist::exists('token', $token = Text::random('alphanuml', 6)) && $cnt < 5) {
			$cnt++;
		}
		
		$values['token'] = $token;
		$subscriberId = Model_Maillist::create($values);
		
		if ((isset($values['confirmed']) ? !$values['confirmed'] : true) && $confirmation) {
			self::sendConfirmation($subscriberId);
		}
	}
}
