<?php

class Notifications_Controller extends Controller_User
{
	protected $subactive = 'profile';

// Today not used
//	public function actionDelete($notification_id)
//	{
//		if(Request::$isAjax) {
//			$this->autoRender = false;
//			$this->response->setHeader('Content-Type', 'text/json');
//
//			$notification = Model_Notifications::getItemByIdUserid($this->user->id, $notification_id);
//
//			$notification->isView = 1;
//			$notification->save();
//			$this->response->body = json_encode(array(
//				'status' => true,
//				'function_name' => 'removeItem',
//				'data' => array(
//					'target' => 'li[data-id="notification_' . $notification->id . '"]',
//					'function_name' => 'negativeCount',
//					'data' => array(
//						'target' => '.userpanel-control .notification-btn .userpanel-counter',
//					)
//				)
//			));
//			return;
//		}
//
//		$this->response->redirect(Request::generateUri('profile', $this->user->id));
//	}

	public function actionSetView($notification_ids)
	{
		if(Request::$isAjax) {
			$this->autoRender = false;
			$this->response->setHeader('Content-Type', 'text/json');

			$notification_ids = explode(',', $notification_ids);

			$notifications = Model_Notifications::getItemByIdsFriendid($this->user->id, $notification_ids);

			$ids = array();
			if(!empty($notifications['data'])) {
				$ids = array_keys($notifications['data']);
				Model_Notifications::update(array(
					'isView' => 1
				), array('id in (?) AND friend_id = ?', $ids, $this->user->id));
			}

			$this->response->body = json_encode(array(
				'status' => ((!empty($ids)) ? TRUE : FALSE)
			));
			return;
		}

		$this->response->redirect(Request::generateUri('profile', $this->user->id));
	}

	public function actionIndex()
	{
		if(Request::get('pagedown', false) && Request::$isAjax) {
			$this->autoRender = false;
			$this->response->setHeader('Content-Type', 'text/json');

			$notifications = Model_Notifications::getListByUserid($this->user->id);

			$view = '';
			foreach($notifications['data'] as $notification) {
				$view .= View::factory('pages/item-notifications', array(
					'notification' => $notification
				));
			}
			$view .= '<li>' . View::factory('common/default-pages', array(
						'controller' => Request::generateUri('notifications', 'index'),
						'isBand' => TRUE
					) + $notifications['paginator']) . '</li>';

			$this->response->body = json_encode(array(
				'status' => true,
				'function_name' => 'changeContent',
				'data' => array(
					'content' => (string)$view,
					'target' => '.block-notifications .list-items > li:last-child'
				)
			));
			return;
		}

		$this->response->redirect(Request::generateUri('profile', $this->user->id));
	}
}