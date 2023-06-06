<?php

class Admin_Maillist_Controller extends Controller_Admin_Template {

	private $pathAttachments = 'content/maillist/attachments/';
	private $maxSize = 5242880;

	public function before() {
		parent::before();
		$this->view->active = 'maillist';
	}

	public function actionIndex() {
		$view = $this->view;
		$view->title = 'Mail messages';
		$view->crumbs('Mail messages');
		$view->content = $content = new View('admin/maillist/messages/list');

		$content->messages = Model_Maillistmessages::getMessages();
		$content->count = Model_User::count("role != 'admin'");
		$content->recipients = Model_Maillistrecipients::getUserResipient();
	}

	public function actionMessage($id = null) {
		$view = $this->view;
		if ($id) {
			$message = Model_Maillistmessages::getMessages($id);
		}

		$view->title = 'Compose a message';
		$view->content = $content = new View('admin/maillist/messages/compose');

		$form = new Form('message');

		$form->labelWidth = '120px';
		$form->attribute('class', 'no-stripes autoform');

		$form->text('name', 'Title')->attribute('size', 40)->rule('required');
		$form->text('subject', '<b>Mail subject</b>')->attribute('size', 40)->rule('required');
		$form->textarea('message', '<b>Mail message</b>')->
			attribute('cols', 80)->
			attribute('rows', 15)->
			attribute('class', 'elrte')->
			rule('required')->
			callback('clearBr')->
			bunched();
		if ($id) {
			$this->view->content->messageId = $id;
			$form->loadValues($message->getValues());
			$submitLabel = 'Save';
		} else {
			$submitLabel = 'Save and go to attachments';
		}

		$form->submit('submit', $submitLabel)
			->attribute('class', 'awesome');

		function clearBr($field) {
			if ($field->value == '<br>') {
				$field->errors[] = 'This field is required';
			}
		}

		$content->form = $form;
		if (Request::isPost()) {
			if ($form->validate()) {
				if ($id) {
					$values = $form->getModified();
					Model_Maillistmessages::update($values, (int) $id);

					$this->response->redirect(Request::$controller);
				} else {
					$values = $form->getValues();
					$values['dateTime'] = date('Y-m-d H:i');

					$message = Model_Maillistmessages::create($values);

					$this->response->redirect(Request::$controller . 'attachments/' . $message->id . '/');
				}
			}
		}

		$this->view->script('/js/jquery/jquery-ui.js');
		$this->view->script('/js/libs/elrte/elrte.min.js');
		$this->view->style('/css/jquery-ui/ui-custom/jquery-ui.css');
		$this->view->style('/css/libs/elrte/elrte.full.css');
	}

	public function actionAttachments($id) {
//		$message = Model_Maillistmessages::getMessages($id);
		$view = $this->view;
		$attachments = Model_Maillistattachments::getAttachments($id);
		$totalSize = 0;
		$maxSize = $this->maxSize;
		foreach ($attachments as $attachment) {
			$totalSize += $attachment->filesize;
		}

		$form = new Form('file');
		$form->file('file', 'Attachment')
			->rule(function($field) use ($totalSize, $maxSize) {
					if (isset($field->files[0])) {
						$file = $field->files[0];
						if (($totalSize + $file['size']) > $maxSize) {
							return 'Total size of your attachments must not exceed ' . number_format(($maxSize / (1024 * 1024)), 0, '.', '') . 'Mb<br />' .
								'With this attachment it will be ' . number_format((($totalSize + $file['size']) / (1024 * 1024)), 1, '.', '') . 'Mb';
						}
					}
				});

		$form->submit('submit', 'Upload')
			->attribute('class', 'awesome');

		if ('POST' == Request::$method) {
			if ($form->validate()) {
				$cnt = 0;

				while (true === Model_Maillistattachments::exists('alias', ($alias = Text::random('alphanuml', 3))) && $cnt < 5) {
					$cnt++;
				}
				if ($cnt == 5) {
					throw new ForbiddenException('Can\'t generate alias for attachment');
				}

				if (isset($form->elements['file']->files[0])) {
					$files = $form->elements['file']->files;
					$file = $files[0];
					$umask = umask(0);
					$dir = $this->attachmentDir($id, $alias);

					if (!file_exists($dir)) {
						mkdir($dir, 0777, true);
					}
					$tmpFilename = $this->attachmentDir($id, $alias) . $file['name'];
					if (false === move_uploaded_file($file['tmp_name'], $tmpFilename)) {
						throw new Exception('File %s cannot be moved.');
					}
					umask($umask);
					$filename = $file['name'];
					$filesize = filesize($tmpFilename);
					$values = $form->getValues();
					$values['alias'] = $alias;
					$values['messageId'] = $id;
					$values['filesize'] = $filesize;
					$values['filename'] = $filename;

					Model_Maillistattachments::create($values);
				} else {
					$form->error = 'File has not been uploaded.';
				}

				$this->response->redirect(Request::$controller . 'attachments/' . $id . '/');
			}
		}

		$view->title = 'Attachments';
		$view->content = $content = new View('admin/maillist/messages/attachments');
		$content->pathAttachments = '/' . $this->pathAttachments;
		$content->totalSize = $totalSize;
		$content->maxSize = $maxSize;
		$content->messageId = $id;
		$content->form = $form;
		$content->attachments = $attachments;
		$content->active = 'attachments';
	}

	public function actionPreview($id) {
		$view = $this->view;
		$message = Model_Maillistmessages::getMessages($id);
		$recipients = Model_Maillistrecipients::getRecipientsMessageId($id);

		$view->title = 'Preview and send';
		$view->content = $content = new View('admin/maillist/messages/preview');
		$content->messageId = $id;
		$content->recipients = $recipients;

		$v = new View('mailer/message');
		$v->subject = $message->subject;
		$v->message = $message->message;
//		dump($v,1);

		$content->active = 'preview';
		$content->message = $v;
	}

	public function actionRecipients($id) {
		$message = Model_Maillistmessages::getMessages($id);
		$recipients = Model_Maillistrecipients::getRecipients($id);
		$view = $this->view;
		$view->title = 'Recipients';
		$view->content = $content = new View('admin/maillist/messages/recipients');
		$content->messageId = $id;

		$form = new Form('recipients');
		foreach (($subscribers = Model_User::getSubscribedUsers()) as $subscriber) {
			$form->checkbox('recipient-' . $subscriber->id, '', $subscriber->name/* , (in_array($subscriber['id'], $recipients)) */);
		}
		$form->submit('submit', 'Save')
			->attribute('class', 'awesome');

//		$roles = Acl::getInstance()->roles();
//		unset($roles['root']);

		$content->message = $message;
		$content->form = $form;
		$content->subscribers = $subscribers;
		$content->active = 'recipients';
		$content->recipients = $recipients;

		if (Request::isPost()) {
			if ($form->validate()) {
				$values = $form->getValues();
				Model_Maillistrecipients::remove(array('messageId = ?', $id));

				foreach ($values as $recipientId => $value) {
					if ($value) {
						$recipient = array(
						    'messageId' => $id,
						    'subscriberId' => substr($recipientId, strpos($recipientId, '-') + 1)
						);
						Model_Maillistrecipients::create($recipient);
					}
				}

				$this->response->redirect(Request::$controller . 'preview/' . $id . '/');
			}
		}
	}

	public function actionSend($id) {
		$message = Model_Maillistmessages::getMessages($id);
		$subscribers = Model_User::getSubscribedUsers();


		$mail = new Mailer('message');
		$mail->subject = $message->subject;
		$mail->message = $message->message;

		foreach (($attachments = Model_Maillistattachments::getAttachments($message->id)) as $attachment) {
			$mail->attachment($this->pathAttachments . $message->id . '/' . $attachment->alias . '/' . $attachment->filename, $attachment->filename);
		}

		foreach (Model_Maillistrecipients::getByMessage($message->id) as $recipient) {
			if (isset($subscribers[$recipient->subscriberId])) {
				$receiverMail = $subscribers[$recipient->subscriberId]->email;
			} else {
				$this->log('Subscriber with id="' . $recipient['subscriberId'] . '" not found.', false);
				continue;
			}

			$mail->userid = $recipient->subscriberId;
			$mail->usermail = $receiverMail;
			$mail->send($receiverMail, 1);

			Model_Maillistrecipients::update(array('sent' => 1), array('`subscriberId` = ? AND `messageId` = ?', $recipient->subscriberId,$message->id));
		}
		Model_Maillistmessages::update(array('date' => date('Y-m-d H:i:s')), $id);

		$this->response->redirect(Request::$controller);
	}

	public function actionRemoveAttachment($attachmentId, $internal = false) {
		$attachment = Model_Maillistattachments::getAttachment($attachmentId);
		$fileName = $this->attachmentPath($attachment->messageId, $attachment->alias, $attachment->filename);
		if (file_exists($fileName)) {
			unlink($fileName);
			if (FileSystem::isDirEmpty($attDir = $this->attachmentDir($attachment->messageId, $attachment->alias))) {
				rmdir($attDir);
			}
			if (FileSystem::isDirEmpty($msgDir = $this->messageDir($attachment->messageId))) {
				rmdir($msgDir);
			}
		}

		Model_Maillistattachments::remove((int) $attachment->id);

		if (!$internal) {
//			if($attachment->type == 'user') {
//				$this->response->redirect(Request::$controller . 'edit/' . $attachment->messageId . '/');
//			} else {
			$this->response->redirect(Request::$controller . 'attachments/' . $attachment->messageId . '/');
//			}
		}
	}

	public function actionRemoveMessage($messageId, $internal = false) {
		$message = Model_Maillistmessages::getMessages($messageId);

		foreach (($attachments = Model_Maillistattachments::getAttachments($message->id)) as $attachment) {
			$this->actionRemoveAttachment($attachment->id, true);
		}

		Model_Maillistrecipients::remove(array('`messageId` = ?', $messageId));
		Model_Maillistmessages::remove($messageId);

		if (!$internal) {
			$this->response->redirect(Request::$controller);
		}
	}

	private function attachmentPath($messageId, $alias, $filename) {
		return $this->pathAttachments . $messageId . '/' . $alias . '/' . $filename;
	}

	private function attachmentDir($messageId, $alias) {
		return $this->pathAttachments . $messageId . '/' . $alias . '/';
	}

	private function messageDir($messageId) {
		return $this->pathAttachments . $messageId . '/';
	}

}

//	public function actionAdd()
//	{
//		$this->view->title = 'Add subscriber';
//		$this->actionEdit();
//	}
//
//	public function actionEdit($id = null)
//	{
//		if ($id) {
//			$this->view->title = 'Edit subscriber profile';
//			$attachments = $this->model->getAttachments($id, 'user');
//		} else {
//			$attachments = null;
//		}
//		
//		$types	    = $this->model->getTypes() + array('' => '___custom___');
//		$categories = $this->model->getCategories() + array('' => '___custom___');
//		
//		
//		
//		$form = new Form('subscriber');
//		$form->attributes['autocomplete'] = 'off';
//		$form->labelWidth = '150px';
//		$form->text('name', 'Full name')->attribute('size', 32)
//			->rule('maxLength', 64)->rule('required');
//		$form->select('type', $types, 'Type')
//			->attribute('type', 'type');
//		$form->text('otherType', ' other type')->attribute('size', 32)
//			->attribute('type', 'otherType')
//			->attribute('size', 16)
//			->rule('maxLength', 32)
//			->callback('typeCheck');
//		function typeCheck($field) {
//			if ($field->form->elements['type']->value == '' and !$field->value) {
//				$field->error('This field is required');
//			}
//		}
//		$form->select('category', $categories, 'Category')
//			->attribute('type', 'category');
//		$form->text('otherCategory', ' other category')->attribute('size', 32)
//			->attribute('type', 'otherCategory')
//			->attribute('size', 16)
//			->rule('maxLength', 32)
//			->callback('categoryCheck');
//		function categoryCheck($field) {
//			if ($field->form->elements['category']->value == '' and !$field->value) {
//				$field->error('This field is required');
//			}
//		}
//		
//		$form->text('company', 'Company name')->attribute('size', 32)
//			->rule('maxLength', 128);
//		$form->text('appointment', 'Position in company')->attribute('size', 32)
//			->rule('maxLength', 128);
//		$form->text('phone', 'Phone')->attribute('size', 32)
//			->rule('maxLength', 128);
//		$form->text('email', 'E-mail')->attribute('size', 32)
//			->rule('maxLength', 64)->rule('email')->rule('required')
//			->callback('emailCheck', array('id' => $id));
//		function emailCheck($field, $args) {
//			extract($args);
//			Model_Table::instance('mailList')->keys['string'] = 'email';
//			if (Model_Table::instance('mailList')->exists($field->value, $id)) {
//				$field->error(Text::DUPLICATE_KEY);
//			}
//		}
//		$form->text('url', 'URL')->attribute('size', 32)
//			->rule('maxLength', 255)->rule('url');
//		$form->checkbox('emailSubscriber', '', 'Send subscriber confirmation email');
//		$form->checkbox('confirmed', '', 'Confirmed', 1);
//		$form->submit('submit', 'Add');
//		if ($id) {
//			$form->elements['submit']->value = 'Save';
//			$subscriber = $this->model->getSubscriber($id);
//			$form->loadValues($subscriber);
//		}
//		if ('POST' == Request::$method) {
//			if ($form->validate()) {
//				$values = $form->getValues();
//				
//				if($values['type'] === '') {
//					$values['type'] = strtolower($values['otherType']);
//				}
//				if($values['category'] === '') {
//					$values['category'] = strtolower($values['otherCategory']);
//				}
//				
//				unset($values['otherType']);
//				unset($values['otherCategory']);
//				$confirmation = false;
//				if ($values['emailSubscriber'] == 1) {
//					$confirmation = true;
//				}
//				
//				unset($values['emailSubscriber']);
//				
//				if ($id) {
//					if ($confirmation) {
//						MailList::getInstance()->sendConfirmation($id);
//					}
//					Model_Table::instance('mailList')->update($values, $id);
//				} else {
//					MailList::getInstance()->subscribe($values, $confirmation);
//				}
//				$this->response->redirect(Request::$controller . 'subscribers/');
//			} else {
//				$form->legend = '<span class="red">Form have validation errors.</span>';
//			}
//		}
//		
//		$this->template->content = $c = new View('admin/maillist/subscribers/form');
//		$c->form = $form;
//		$c->pathAttachments = '/' . $this->pathAttachments;
//		$c->id = $id;
//		$c->attachments = $attachments;
//		
//		$this->addScript('/scripts/jquery/jquery.js');
//		$this->addScript('/scripts/admin_dashboard.js');
//	}
//	public function actionAddAttachment($id)
//	{
//		if (!($subscriber = $this->model->getSubscriber($id))) {
//			throw new ForbiddenException('Subscriber not found');
//		}
//		$this->template->title = 'Attachments';
//		$this->template->content = $c = new View('admin/maillist/subscribers/attachment');
//		//$c->pathAttachments = '/' . $this->pathAttachments;
//		$c->subscriber = $subscriber;
//		$form = new Form('file');
//		$form->file('file', 'Attachment');
//		$form->submit('submit', 'Upload')
//			->attribute('class', 'awesome');
//		
//		$c->form = $form;
//		
//		if ('POST' == Request::$method) {
//			if ($form->validate()) {
//				$cnt = 0;
//				while (true === Model_Table::instance('mailListAttachments')->exists(($alias = System::random('alphanuml', 3))) && $cnt < 3) {
//					$cnt++;
//				}
//				if ($cnt == 7) {
//					throw new ForbiddenException('Can\'t generate alias for attachment');
//				}
//				if (isset($form->elements['file']->files[0])) {
//					$files = $form->elements['file']->files;
//					$file = $files[0];
//					$umask = umask(0);
//					$dir = $this->attachmentDir($id, $alias);
//					if (!file_exists($dir)) {
//						mkdir($dir, 0777, true);
//					}
//					$tmpFilename = $this->attachmentDir($id, $alias) . $file['name'];
//					if (false === move_uploaded_file($file['tmp_name'], $tmpFilename)) {
//						throw new Exception('File %s cannot be moved.');
//					}
//					umask($umask);
//					$filename = $file['name'];
//					$filesize = filesize($tmpFilename);
//					$values = $form->getValues();
//					$values['alias'] = $alias;
//					$values['type'] = 'user';
//					$values['parentId'] = $id;
//					$values['filesize'] = $filesize;
//					$values['filename'] = $filename;
//					Model_Table::instance('mailListAttachments')->insert($values);
//				} else {
//					$form->error = 'File has not been uploaded.';
//				}
//				$this->response->redirect(Request::$controller . 'edit/' . $id  . '/');
//			}
//		}
//
//	}
//
//	public function actionRemove($id)
//	{
//		if (!($user = $this->model->getSubscriber($id))) {
//			throw new ForbiddenException('Subscriber not found');
//		}
//		foreach (($attachments = $this->model->getAttachments($user['id'], 'user')) as $attachment) {
//			$this->actionRemoveAttachment($attachment['id'], true);
//		}
//		
//		Model_Table::instance('mailList')->delete((int)$id);
//		$this->response->redirect(Request::$controller . 'subscribers/');
//	}