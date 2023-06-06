<?php

class Messages_Controller extends Controller_User
{

	protected $subactive = 'messages';

	public function  before() {
		parent::before();

	}

	public function actionIndex()
	{
		$this->view->title = 'Messages';

		$messages_received = Model_Messages::getListReceivedByUser($this->user->id);

		if(Request::$isAjax){
			$this->autoRender = false;
			$this->response->setHeader('Content-Type', 'text/json');

			$view = '';
			foreach($messages_received['data'] as $message_received) {
				$view .=  View::factory('pages/messages/item-received', array(
					'typeListReceived' => true,
					'item' => $message_received
				));
			}

			$view .= '<li>' . View::factory('common/default-pages', array(
						'isBand' => TRUE,
						'autoScroll' => TRUE
					) + $messages_received['paginator']) . '</li>';

			$this->response->body = json_encode(array(
				'status' => true,
				'function_name' => 'changeContent',
				'data' => array(
					'content' => (string)$view,
					'target' => '.messages-list_received > .list-items > li:last-child'
				)
			));
			return;
		}

		$messages_countnew = Model_Messages::getCountNewReceived($this->user->id);

		$f_Messages_FilterMessage = new Form_Messages_FilterMessage();

		$view = View::factory('parts/parts-right_big', array(
			'left' => View::factory('pages/messages/menu', array(
					'active' => 'received',
					'messages_countnew' => $messages_countnew
				)),
			'right' => View::factory('pages/messages/list-received', array(
					'messages_received' => $messages_received,
					'f_Messages_FilterMessage' => $f_Messages_FilterMessage
				))
		));
		$this->view->content = $view;
	}

	public function actionSent()
	{
		$this->view->title = 'Sent messages';

		$messages_sent = Model_Messages::getListSentByUser($this->user->id);

		if(Request::$isAjax){
			$this->autoRender = false;
			$this->response->setHeader('Content-Type', 'text/json');

			$view = '';
			foreach($messages_sent['data'] as $message_sent) {
				$view .=  View::factory('pages/messages/item-received', array(
					'typeListSent' => true,
					'item' => $message_sent
				));
			}

			$view .= '<li>' . View::factory('common/default-pages', array(
						'isBand' => TRUE,
						'autoScroll' => TRUE
					) + $messages_sent['paginator']) . '</li>';

			$this->response->body = json_encode(array(
				'status' => true,
				'function_name' => 'changeContent',
				'data' => array(
					'content' => (string)$view,
					'target' => '.messages-list_sent > .list-items > li:last-child'
				)
			));
			return;
		}

		$messages_countnew = Model_Messages::getCountNewReceived($this->user->id);

		$view = View::factory('parts/parts-right_big', array(
			'left' => View::factory('pages/messages/menu', array(
					'active' => 'sent',
					'messages_countnew' => $messages_countnew
				)),
			'right' => View::factory('pages/messages/list-sent', array(
					'messages_sent' => $messages_sent
				))
		));
		$this->view->content = $view;
	}


	public function actionArchive()
	{
		$this->view->title = 'Messages archive';

		$messages_archive = Model_Messages::getListArchiveByUser($this->user->id);

		if(Request::$isAjax){
			$this->autoRender = false;
			$this->response->setHeader('Content-Type', 'text/json');

			$view = '';
			foreach($messages_archive['data'] as $message_archive) {
				$view .=  View::factory('pages/messages/item-received', array(
					'typeListArchive' => true,
					'item' => $message_archive
				));
			}

			$view .= '<li>' . View::factory('common/default-pages', array(
						'isBand' => TRUE,
						'autoScroll' => TRUE
					) + $messages_archive['paginator']) . '</li>';

			$this->response->body = json_encode(array(
				'status' => true,
				'function_name' => 'changeContent',
				'data' => array(
					'content' => (string)$view,
					'target' => '.messages-list_archive > .list-items > li:last-child'
				)
			));
			return;
		}

		$messages_countnew = Model_Messages::getCountNewReceived($this->user->id);

		$f_Messages_FilterMessage = new Form_Messages_FilterMessage();

		$view = View::factory('parts/parts-right_big', array(
			'left' => View::factory('pages/messages/menu', array(
					'active' => 'archive',
					'messages_countnew' => $messages_countnew
				)),
			'right' => View::factory('pages/messages/list-archive', array(
					'messages_archive' => $messages_archive,
					'f_Messages_FilterMessage' => $f_Messages_FilterMessage
				))
		));
		$this->view->content = $view;
	}

	public function actionTrash()
	{
		$this->view->title = 'Trash messages';

		$messages_trash = Model_Messages::getListTrashByUser($this->user->id);

		if(Request::$isAjax){
			$this->autoRender = false;
			$this->response->setHeader('Content-Type', 'text/json');

			$view = '';
			foreach($messages_trash['data'] as $message_trash) {
				$view .=  View::factory('pages/messages/item-received', array(
					'typeListTrash' => true,
					'item' => $message_trash
				));
			}

			$view .= '<li>' . View::factory('common/default-pages', array(
						'isBand' => TRUE,
						'autoScroll' => TRUE
					) + $messages_trash['paginator']) . '</li>';

			$this->response->body = json_encode(array(
				'status' => true,
				'function_name' => 'changeContent',
				'data' => array(
					'content' => (string)$view,
					'target' => '.messages-list_trash > .list-items > li:last-child'
				)
			));
			return;
		}

		$messages_countnew = Model_Messages::getCountNewReceived($this->user->id);

		$f_Messages_FilterMessage = new Form_Messages_FilterMessage();

		$view = View::factory('parts/parts-right_big', array(
			'left' => View::factory('pages/messages/menu', array(
					'active' => 'trash',
					'messages_countnew' => $messages_countnew
				)),
			'right' => View::factory('pages/messages/list-trash', array(
					'messages_trash' => $messages_trash,
					'f_Messages_FilterMessage' => $f_Messages_FilterMessage
				))
		));
		$this->view->content = $view;
	}

	public function actionMessage($message_id)
	{
		$this->view->title = 'View messages';

		$message = Model_Messages::getItemReplayByMessageId($message_id, $this->user->id);

		if($message->friend_id == $this->user->id) {
			$friend_id = $message->user_id;
			$message->isFriendView = 1;
			$message->save();
		} else {
			$friend_id = $message->friend_id;
		}

		$messages_countnew = Model_Messages::getCountNewReceived($this->user->id);

		$messages = Model_Messages::getListHistoryByUserId($friend_id, $this->user->id);
		$contentHistory = View::factory('pages/messages/history-message', array(
			'messages' => $messages,
			'friend_id' => $message->user_id,
			'isVisible' => false
		));

		$view = View::factory('parts/parts-right_big', array(
			'left' => View::factory('pages/messages/menu', array(
					'active' => 'received',
					'messages_countnew' => $messages_countnew
				)),
			'right' => View::factory('pages/messages/view-message', array(
					'message' => $message,
					'contentHistory' => isset($contentHistory) ? $contentHistory : false
				))
		));
		$this->view->content = $view;
	}

	public function actionReply($message_id)
	{
		$this->actionNew($message_id);
	}

	public function actionSentMessageFromUserAvaBlock($friend_id)
	{
		$this->actionNew(false, $friend_id);
	}

	public function actionSentMessageFromProfile($friend_id)
	{
		$this->actionNew(false, $friend_id);
	}


	public function actionNew($message_id = false, $friend_id = false, $from = false)
	{
		// When I is in block list user's
		if($friend_id) {
			$isBlocked = Model_Profile_Blocked::checkIsIInBlockListUser($friend_id);
			if($isBlocked) {
				$message = 'You cannot send invitations to this user. You are in block list.';

				if(Request::isAjax() && Request::get('fromBox', false)) {
					$this->autoRender = false;
					$this->response->setHeader('Content-Type', 'text/json');

					$content = View::factory('parts/pbox-form', array(
						'title'   => 'Message',
						'content' => View::factory('popups/message', array(
							'message' => $message
						))
					));

					$this->response->body = json_encode(array(
						'status'  => true,
						'content' => (string)$content
					));
					return;
				} else {
					$this->message($message);
					$this->response->redirect(Request::generateUri('messages', 'new'));
				}
			}
		}

		// Check if connections is 1 or 2 level
		if($friend_id) {
            $levelConnection = Model_User::checkCanSendToUser($friend_id);
			if( ! $levelConnection && $this->user->accountType != ACCOUNT_TYPE_GOLD && $friend_id != $this->user->id) {
				$url = Request::generateUri('profile', 'upgrade');

				if(Request::isAjax() && Request::get('fromBox', false)) {
					$this->autoRender = false;
					$this->response->setHeader('Content-Type', 'text/json');

					$this->response->body = json_encode(array(
						'status'  => true,
						'function_name' => 'redirect',
						'data' => array(
							'url' => $url
						)
					));
					return;
				} else {
					$this->response->redirect($url);
				}
			}
		}




		if($message_id){
			$this->view->title = 'Reply message';
			$messageFromUser = Model_Messages::getItemByMessageidFriendid($message_id, $this->user->id);
		} else {
			$this->view->title = 'Create message';
		}

		$connections = Model_Connections::getListConnectionsByUser($this->user->id);
		array_push(  $connections, ['userId' => $friend_id] );
        $messages_countnew = Model_Messages::getCountNewReceived($this->user->id);
		$f_Messages_NewMessage = new Form_Messages_NewMessage($connections);

		if($friend_id) {
			Model_User::addUserId($friend_id);
            $levelConnection = Model_User::checkCanSendToUser($friend_id);
			if($levelConnection && $friend_id){
				if(isset($connections['data'][$friend_id])) {
					$f_Messages_NewMessage->setSentTo($connections['data'][$friend_id]);
				}
			} elseif($levelConnection || $this->user->accountType == ACCOUNT_TYPE_GOLD) {
				$profile = Model_User::getItemByUserid($friend_id);
				$connections['data'][$friend_id] = $profile;
				$f_Messages_NewMessage->generateList($connections);
				$f_Messages_NewMessage->setSentTo($connections['data'][$friend_id]);
			}
		}



		if($message_id){
			$f_Messages_NewMessage->setReplay($message_id);
			if($f_Messages_NewMessage->message->user_id == $this->user->id){
				$friend_id = $f_Messages_NewMessage->message->friend_id;
			} else {
				$friend_id = $f_Messages_NewMessage->message->user_id;
			}
			$history = Model_Messages::getListHistoryByUserId($friend_id, $this->user->id);

			Model_Messages::update(array(
				'isFriendView' => 1
			), $messageFromUser->id);

			$viewHistory = View::factory('pages/messages/history-message', array(
				'messages' => $history,
				'friend_id' => $friend_id,
				'isVisible' => false
			));
		}

		$isError = false;
		if(Request::isPost()){
            if($f_Messages_NewMessage->form->validate()) {
                $values = $f_Messages_NewMessage->form->getValues();
				Model_Messages::create(array(
					'user_id' => $this->user->id,
					'friend_id' => $values['selectedConnection'],
					'subject' => $values['subject'],
					'message' => $values['message']
				));
				$f_Messages_NewMessage->form->clearValues();

//				$item = $connections['data'][$values['selectedConnection']];
				$message = 'Message has been successfully sent';
			} else {
				$isError = true;
			}
		}

		if(Request::isAjax() && Request::get('fromBox', false)){
			$this->autoRender = false;
			$this->response->setHeader('Content-Type', 'text/json');

			$f_Messages_NewMessage->fromBox();
			if(($isError && Request::isPost()) || (!$isError && !Request::isPost())) {
				$f_Messages_NewMessage->hideLabel();
				$content = View::factory('parts/pbox-form', array(
					'title' => 'Send message',
					'content' => View::factory('popups/messages/sendmessage', array(
							'f_Messages_NewMessage' => $f_Messages_NewMessage->form
						))
				));

				$this->response->body = json_encode(array(
					'status' => (!$isError),
					'content' => (string)$content
				));
				return;
			} else {

				$this->response->body = json_encode(array(
					'status' => true,
					'content' => '',
					'function_name' => 'popupShow',
					'data' => array(
						'title' => 'Sent message',
						'content' => $message
					)
				));
				return;
			}
		}
		$messages_countnew = Model_Messages::getCountNewReceived($this->user->id);

		$view = View::factory('parts/parts-right_big', array(
			'left' => View::factory('pages/messages/menu', array(
					'active' => 'newmessage',
					'messages_countnew' => $messages_countnew
				)),
			'right' => View::factory('pages/messages/new-message', array(
					'f_Messages_NewMessage' => $f_Messages_NewMessage,
					'message' => (isset($message)) ? $message : false,
					'viewHistory' => (isset($viewHistory)) ? $viewHistory : ''
				))
		));
		$this->view->content = $view;
	}

	public function actionHistory($friend_id)
	{
		if(Request::$isAjax){
			$this->autoRender = false;
			$this->response->setHeader('Content-Type', 'text/json');
			$view = '';
			$messages = Model_Messages::getListHistoryByUserId($friend_id, $this->user->id);
			foreach($messages['data'] as $message) {
				$view .=  View::factory('pages/messages/item-received', array(
					'item' => $message,
					'typeListHistory' => TRUE,
					'avasize' => 'avasize_52'
				));
			}

			$view .= '<li>' . View::factory('common/default-pages', array(
				'controller' => Request::generateUri(false, 'history', $friend_id),
				'isBand' => TRUE,
				'autoScroll' => TRUE
			) + $messages['paginator']) . '</li>';

			$this->response->body = json_encode(array(
				'status' => true,
				'function_name' => 'changeContent',
				'data' => array(
					'content' => (string)$view,
					'target' => '.messages-history > .list-items > li:last-child'
				)
			));
			return;
		} else {
			$this->response->redirect(Request::generateUri('messages', 'index'));
		}
	}

	public function actionArchiveReceived($message_id)
	{
		$this->actionChangeType(MESSAGE_ARCHIVE, $message_id, 'index');
	}
	public function actionArchiveHistory($message_id)
	{
		$this->actionChangeType(MESSAGE_ARCHIVE, $message_id, 'message', $message_id);
	}
	public function actionArchiveSent($message_id)
	{
		$this->actionChangeType(MESSAGE_ARCHIVE, $message_id, 'sent');
	}
	public function actionArchiveMessages($message_id)
	{
		$this->actionChangeType(MESSAGE_ARCHIVE, $message_id, 'index');
	}




	public function actionTrashReceived($message_id)
	{
		$this->actionChangeType(MESSAGE_TRASH, $message_id, 'index');
	}
	public function actionTrashHistory($message_id)
	{
		$this->actionChangeType(MESSAGE_TRASH, $message_id, 'message', $message_id);
	}
	public function actionTrashSent($message_id)
	{
		$this->actionChangeType(MESSAGE_TRASH, $message_id, 'sent');
	}
	public function actionTrashArchive($message_id)
	{
		$this->actionChangeType(MESSAGE_TRASH, $message_id, 'archive');
	}
	public function actionTrashMessage($message_id)
	{
		$this->actionChangeType(MESSAGE_TRASH, $message_id, 'index');
	}





	public function actionDelete($message_id)
	{
		$this->actionChangeType(MESSAGE_DELETE, $message_id, 'trash');
	}



	public function actionRestoreReceived($message_id)
	{
		$this->actionRestoreType(MESSAGE_INBOX, $message_id, 'index');
	}
	public function actionRestoreHistory($message_id)
	{
		$this->actionChangeType(MESSAGE_INBOX, $message_id, 'message', $message_id);
	}
	public function actionRestoreSent($message_id)
	{
		$this->actionChangeType(MESSAGE_INBOX, $message_id, 'sent');
	}
	public function actionRestoreArchive($message_id)
	{
		$this->actionChangeType(MESSAGE_INBOX, $message_id, 'archive');
	}
	public function actionRestoreTrash($message_id)
	{
		$this->actionChangeType(MESSAGE_INBOX, $message_id, 'trash');
	}

	protected function actionChangeType($type, $message_id, $fromAction = false, $params = array())
	{
		$ids = explode(',', $message_id);

		foreach ($ids as $id){
			$message = new Model_Messages($id);
			if($message->user_id == $this->user->id) {
				$message->typeForUser = $type;
				$friend_id = $message->friend_id;
			} elseif($message->friend_id == $this->user->id) {
				$message->typeForFriend	 = $type;
				$friend_id = $message->user_id;
			} else {
				$this->response->redirect(Request::generateUri('messages', $fromAction, $params));
			}
			$message->save();
		}


		$messages_countnew = Model_Messages::getCountNewReceived($this->user->id);
		$f_Messages_FilterMessage = new Form_Messages_FilterMessage();

		if(Request::$isAjax){
			$this->autoRender = false;
			$this->response->setHeader('Content-Type', 'text/json');

			$target = array();
			foreach($ids as $id){
				$target[] = '.list-items > li[data-id="' . $id . '"]';
			}

			$this->response->body = json_encode(array(
				'status' => true,
				'function_name' => 'removeItem',
				'data' => array(
					'target' => $target,
					'function_name' => 'setCount',
					'data' => array(
						'target' => '.messages-countreceived > span',
						'content' => $messages_countnew,
						'function_name' => 'afterUpdatePage',
						'data' => array()
					)
				)
			));
//			switch($fromAction){
//				case 'index':
//					$messages_received = Model_Messages::getListReceivedByUser($this->user->id);
//					$content = View::factory('pages/messages/list-received', array(
//						'messages_received' => $messages_received,
//						'f_Messages_FilterMessage' => $f_Messages_FilterMessage,
//						'messages_countnew' => $messages_countnew
//					));
//					$targetBlock = '.messages-list_received';
//					break;
//				case 'sent':
//					$messages_sent = Model_Messages::getListSentByUser($this->user->id);
//					$content = View::factory('pages/messages/list-sent', array(
//						'messages_sent' => $messages_sent,
//						'messages_countnew' => $messages_countnew
//					));
//					$targetBlock = '.messages-list_sent';
//					break;
//				case 'archive':
//					$messages_archive = Model_Messages::getListArchiveByUser($this->user->id);
//					$content = View::factory('pages/messages/list-archive', array(
//						'messages_archive' => $messages_archive,
//						'f_Messages_FilterMessage' => $f_Messages_FilterMessage,
//						'messages_countnew' => $messages_countnew
//					));
//					$targetBlock = '.messages-list_archive';
//					break;
//				case 'trash':
//					$messages_trash = Model_Messages::getListTrashByUser($this->user->id);
//					$content = View::factory('pages/messages/list-trash', array(
//						'messages_trash' => $messages_trash,
//						'f_Messages_FilterMessage' => $f_Messages_FilterMessage,
//						'messages_countnew' => $messages_countnew
//					));
//					$targetBlock = '.messages-list_trash';
//					break;
//				case 'message':
//					$messages = Model_Messages::getListHistoryByUserId($friend_id, $this->user->id);
//					$content = View::factory('pages/messages/history-message', array(
//						'messages' => $messages,
//						'messages_countnew' => $messages_countnew
//					));
//					$targetBlock = '.messages-history';
//					break;
//			}

//			$this->response->body = json_encode(array(
//				'status' => true,
//				'function_name' => 'changeContent',
//				'data' => array(
//					'content' => (string)$content,
//					'target' => $targetBlock
//				)
//			));
			return;
		} else {
			$this->response->redirect(Request::generateUri('messages', $fromAction, $params));
		}
	}



}