<?php

/**
 * Mailer CLI controller.
 *
 * @version $Id$
 * @package Application
 */
class Mailer_Controller extends Controller_Common {

	public function before() 
	{
		    parent::before();
		    set_time_limit(2000);
	}

	private function log($message, $success = true, $end = false) 
	{
		Log::getInstance('mailer')->write($message, ($success) ? 'SUCCESS: ' : 'FAILURE: ');
		if ($end) {
			Log::getInstance('mailer')->write('------------------------------------------------------------------', 'Maillist');
		}
	}

	public function actionIndex() 
	{
		$this->autoRender = false;

		$cnt = 0;
		
		$subscribers = Model_User::getSubscribedUsers();
		
		//new mailer test
		foreach (Model_Maillistmessages::getPending() as $message) {
			$mail = new Mailer('message');
			$mail->subject = $message->subject;
			$mail->message = $message->message;

			foreach (($attachments = Model_Maillistattachments::getAttachments($message->id)) as $attachment) {
				$mail->attachment('content/maillist/attachments/' . $message->id . '/' . $attachment->alias . '/' . $attachment->filename, $attachment->filename);
			}

			foreach(Model_Maillistrecipients::getByMessage($message->id) as $recipient) {
				if (isset($subscribers[$recipient->subscriberId])) {
					$receiverMail = $subscribers[$recipient->subscriberId]->email;
				} else {
					$this->log('Subscriber with id="' . $recipient['subscriberId'] . '" not found.', false);
					continue;
				}
				
				//$mail->send();
				$mail->userid = $recipient->subscriberId;
				$mail->usermail = $receiverMail;
				$mail->send($receiverMail);
				
				Model_Maillistrecipients::update(array('sent' => 1), array('`subscriberId` = ? AND `messageId` = ?', $message->id, $recipient->subscriberId));
				$cnt++;
				usleep(500000);
			}
			
			Model_Maillistmessages::update(array('status' => Null), (int) $message->id);
		}

		if ($cnt > 0) {
			$this->log('Mailer finished. ' . $cnt . ' messages has been sent.', true, true);
		}
	}

//	public function actionUnsubscribe($email, $id) 
//	{
//		$this->db = Database::instance();
//		if ($id == null OR $email == null) {
//		    $this->response->redirect('/');
//		}
//		$id = (int) $id;
//		$email = $this->db->escapeString($email);
//		$res = $this->db->query("SELECT COUNT(*) as count FROM `users` WHERE `id`={$id} AND `email` = '{$email}'")->fetch();
//		if ($res['count'] == 1) {
//		    $this->db->query("UPDATE `users` SET `subscribed`='0' WHERE `id`={$id} AND `email` = '{$email}'");
//		    $this->autoRender = false;
//		    $t = new View('template');
//		    //parent::before();
//		    $t->title = 'Chicago Web Design – Web Development & Branding in Chicago,IL | Ukietech';
//		    $t->menu = new View('menu', array('active' => 'home', 'home' => true));
//		    $t->quote = Quotes::getQuotes();
//		    $t->keywords = 'Chicago Web Design, branding Chicago, print materials Chicago, web development Chicago, 3d rendering Chicago, web design company Chicago, web design services, web design and development, web site design Chicago, web design animation, corporate branding Chicago, print design Chicago ';
//		    $t->description = 'Professional Web Design and Branding in Chicago. Creative company develops 2D & 3D graphics, quality animation in Chicago, Illinois. ';
//		    $t->content = '<h2 style="text-align: center;">You have successfully unsubscribed.</h2>';
//		    echo $t;
//		} else {
//		    $this->response->redirect('/');
//		}
//	}
}