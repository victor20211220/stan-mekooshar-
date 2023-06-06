<?php

/**
 * Admin Template Controller.
 *
 * @version  $Id: template.php 2 2009-10-02 23:06:43Z perfilev $
 * @package  Application
 */

abstract class Controller_User extends Controller_Common
{
	protected $resource = 'profile';

	public function before()
	{
		parent::before();

// NOT DELETE -----------
//		Model_User::generateViewsForAllUser();
//		Model_Connections::fixBagForFirstVirtualConnection();

//		Model_User::updateAllUsersCountConnections();

//		Model_User_Friends::checkAndCreateEmptyFriendsForAllUser();
//		Model_User_Friends::checkAndFixFriendsForAllUser();

//		exit();
// ----------------------

		if($this->user->isBlocked) {
			$this->message('You are blocked by Administrator!');
			Auth::getInstance()->clearIdentity();
			$this->response->redirect(Request::generateUri('home'));
		}

		$f_findpanel = new Form_FindPanel();
		$f_findpanel->setFindType(Request::$action);
		$f_findpanel->form->validate();
		$this->view->f_findpanel = $f_findpanel->form;

		if(isset($_SESSION['search']['last_open'])) {
			if($_SESSION['search']['last_open'] < (time() - 60*15)) {
				unset($_SESSION['search']);
			}
		}

		if(isset($_COOKIE['viewProfile'])) {
			$id = $_COOKIE['viewProfile'];
			setcookie('viewProfile', null, -1, '/');
			$this->response->redirect(Request::generateUri('profile', $id));
		}

	 	if($this->user->accountType == ACCOUNT_TYPE_GOLD) {
//			if(strtotime($this->user->updateExp) < time()) { //Disable check updateExp
			if(false) {
				Model_User::update(array(
					'accountType' => ACCOUNT_TYPE_BASIC,
				), $this->user->id);

				Auth::getInstance()->updateIdentity($this->user->id);
			}
		}
		Model_User::addUserId($this->user->id);
//		dump($this->user, 1);
	}

	public function after()
	{
		$countNewMessages = Model_Messages::getCountNewReceived($this->user->id);
		$countNewConnections = Model_Connections::getCountNewReceived($this->user->id);

		$notifications = Model_Notifications::getListByUserid($this->user->id);
		$countNotifications = Model_Notifications::getCountNewNotification($this->user->id);
		$countNewJobApplicant = Model_Job_Apply::getCountAllNewApplicant($this->user->id);

		$countGroupNewMember = Model_Group_Members::getCountAllNewMember($this->user->id);
		$countGroupNewContent = Model_Posts::getCountAllNewGroupContent($this->user->id);

		$countSchoolNewStaffMember = Model_Universities::getCountNewStaffMember($this->user->id);


		$this->view->user_panel = new View('parts/user_panel', array(
			'subactive' => $this->subactive,
			'countNewMessages' => $countNewMessages,
			'countNewConnections' => $countNewConnections,
			'countNotifications' => $countNotifications->countItems,
			'countNewJobApplicant' => $countNewJobApplicant,
			'countGroupNewMember' => $countGroupNewMember,
			'countGroupNewContent' => $countGroupNewContent,
			'countSchoolNewStaffMember' => $countSchoolNewStaffMember
		));
		$this->view->notifications = $notifications;
		$this->view->active = 'profile';

		parent::after();
	}
}
