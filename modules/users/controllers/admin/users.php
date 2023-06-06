<?php

class Admin_Users_Controller extends Controller_Admin_Template
{
	public function before()
	{
		parent::before();
		
		$this->view->active = 'users';
		$this->view->script('/js/libs/ui/jquery-ui.custom.min.js');
	}

	public function actionIndex()
	{

		$users = Model_User::getAll();

		$view = $this->view;
		$view->crumbs('User management');
		$view->content = $content = new View('admin/users/list');
		
		$content->users = $users;
        $view->title = "Users list";
        $view->user = $this->user;
		$this->getMessages();

		$this->view->script('/js/libs/table.filter.min.js');
	}

	public function actionAdd()
	{
		$this->actionEdit();
	}

	public function actionEdit($id = null)
	{
		$this->view->crumbs('User management', Request::$controller);
		
		if ($id) {
			$this->view->crumbs('Edit user profile');
			$form = new Form('user');
			$user = Model_User::getById($id);
		} else {
			$this->view->crumbs('Add user');
			$form = new Form('user');
		}
		
		$form->attribute('class', 'no-borders autoform');
		$form->labelWidth = '150px';
		$form->text('firstName', 'First name')->attribute('size', 15)
			->inline()
			->contentRight('&nbsp;')
			->rule('maxLength', 64)
			->rule('required');
		$form->text('lastName', 'Last name')->attribute('size', 15)
			->rule('maxLength', 64)
			->rule('required');
		$form->select('gender', array('M' => 'Male', 'F' => 'Female'), 'Gender');
		$form->text('email', 'E-mail')
			->attribute('size', 34)
			->rule('maxLength', 64)
			->rule('email')
			->rule('required')
			->rule(function($field) use($id) {
				if (Model_User::exists('email', $field->value, $id)) {
					return t('Value already exist');
				}
			});

		$roles = Acl::getInstance()->rolesList();
		
		$form->select('role', $roles, 'Role')->required();
		if (!$id) {
			$form->html('passwordReq', '', 'Password must be atleast 5 characters long')
				->phantom();
			$form->password('password', 'Password')
				->attribute('size', 24)
				->rule('maxLength', 32)
				->rule('minLength', 5)
				->rule('required')
				->grouped();
			$form->password('password2', 'Confirm Password')
				->attribute('size', 24)
				->rule('required')
				->rule(function($field) {
					if ($field->fieldset->elements['password']->value != $field->value) {
						return 'Passwords are not match';
					}
				})
				->grouped();
//			$form->checkbox('emailUser', '', 'Email new password to user', 1);
		}

		$form->textarea('address', 'Address')->attribute('size', 15)
			->attribute('cols', 40);
		
		$form->submit('submit', 'Add')
			->attribute('eva-content', 'Add new user')
			->attribute('class', 'btn btn-ok');
		
		if ($id) {
			$form->elements['submit']->value = 'Save';
			$form->elements['submit']->attribute('eva-content', 'Save changes');
			$form->loadValues($user->getValues());
		}
		
		if ('POST' == Request::$method) {
			if ($form->validate()) {
				$values = $form->getValues();
				
//				$emailUser = $values['emailUser'];
				unset($values['emailUser'], $values['password2']);
				if (!isset($roles[$values['role']])) {
					throw new ForbiddenException('Role nor exist');
				}
//				if ($emailUser) {
//					Model_User::emailPassword($userId, $password);
//				}

				if ($id) {
					if (!isset($roles[$values['role']])) {
						throw new ForbiddenException('Role does not exist');
					}
 					Model_User::update($values, $id);
					$this->message('User information has been updated');
				} else {
					$password = $values['password'];
					$values['password'] = Model_User::encryptPassword($values['password']);
					
					$userId = Model_User::create($values);
					
					$this->message('User has been created');
				}
				$this->response->redirect(Request::$controller);
			}
		}
		
		$this->view->content = new View('admin/users/form', array('form' => $form));
		
		$this->getMessages();
	}

	public function actionPassword($id)
	{
		$user = Model_User::getById($id);
		
		$this->view
			->crumbs('User management', Request::$controller)
			->crumbs('Change password');
		
		$form = new Form('password');
		$form->attribute('class', 'no-borders autoform');
		
		$form->labelWidth = '150px';
		$form->html('user', 'User', $user->lastName . ' ' . $user->firstName . ' ('.$user->name.')');
		$form->password('password', 'New password')
			->attribute('size', 24)
			->attribute('maxlength', 32)
			->rule('minLength', 5)
			->rule('required');
		$form->password('password2', 'Confirm Password')
			->attribute('size', 24)
			->rule('required')
			->rule(function($field) {
				if ($field->fieldset->elements['password']->value != $field->value) {
					return 'Passwords are not match';
				}
			})
			->grouped();
		function passwordCheck($field) {
			
		};
//		$form->checkbox('emailUser', '', 'Email new password to user', 1)->grouped();
		
		$form->submit('submit', 'Update')
			->attribute('eva-content', 'Save new password')
			->attribute('class', 'btn btn-ok');
		
		if ('POST' == Request::$method) {
			if ($form->validate()) {
				$values = $form->getValues();
				
//				$emailUser = $values['emailUser'];
				unset($values['emailUser'], $values['password2']);

				$password = $values['password'];
				$values['password'] = Model_User::encryptPassword($values['password']);
					
//				if ($emailUser) {
//					Model_User::emailPassword($userId, $password);
//				}
				
				Model_User::update($values, $id);
				$this->message('Password has been changed');
				
				$this->response->redirect(Request::$controller);
			}
		}
		
		$this->view->content = new View('admin/users/form', array('form' => $form));
		
		$this->getMessages();
	}

	public function actionRemove($id)
	{
		$user = Model_User::getById($id);
		if ($user->role != 'root' && $user->id != $this->user->id) {
//			Model_User::remove($id);
			Model_User::update(array(
				'isRemoved' => '1'
			), $id);
		} else {
			throw new Exception('You can`t delete yourself');
		}
		$this->message('User has been deleted');
		
		$this->response->redirect(Request::$controller);
	}


	public function actionComplaints()
	{
		$users = Model_Profile_Complaint::getAll();
		$this->view->active = 'complaints';


		if(Request::get('page', false)) {
			$this->autoRender = false;
			$this->response->setHeader('Content-Type', 'text/json');

			$view = '';
			foreach($users['data'] as $user) {
				$view .= View::factory('admin/complaints/item-allusers-complaints', array(
					'user' => $user
				));
			}
			$view .= '<tr><td colspan="6">' . View::factory('common/default-pages', array(
						'controller' => Request::generateUri('admin', 'users', 'complaints'),
						'isBand' => TRUE
					) + $users['paginator']) . '</td></tr>';

			$this->response->body = json_encode(array(
				'status' => true,
				'function_name' => 'changeContent',
				'data' => array(
					'content' => (string)$view,
					'target' => 'table > tbody > tr:last-child'
				)
			));
			return;
		}


		$view = $this->view;
		$view->crumbs('Complaints management');
		$view->content = $content = new View('admin/complaints/allusers-complaints');

		$content->users = $users;

		$this->getMessages();

		$this->view->script('/js/libs/table.filter.min.js');
		$this->view->script('/js/system.js');
	}

	public function actionShowUserComplaints($user_id)
	{
		$complaints = Model_Profile_Complaint::getAllComplaintsByUser($user_id);
		$this->view->active = 'complaints';

		if(Request::get('page', false)) {
			$this->autoRender = false;
			$this->response->setHeader('Content-Type', 'text/json');

			$view = '';
			foreach($complaints['data'] as $complaint) {
				$view .= View::factory('admin/complaints/item-user-complaints', array(
					'complaint' => $complaint
				));
			}
			$view .= '<tr><td colspan="7">' . View::factory('common/default-pages', array(
						'controller' => Request::generateUri('admin', 'users', 'complaints'),
						'isBand' => TRUE
					) + $complaints['paginator']) . '</td></tr>';

			$this->response->body = json_encode(array(
				'status' => true,
				'function_name' => 'changeContent',
				'data' => array(
					'content' => (string)$view,
					'target' => 'table > tbody > tr:last-child'
				)
			));
			return;
		}

		$view = $this->view;
		$view->crumbs('User complaints');
		$view->content = $content = new View('admin/complaints/user-complaints');

		$content->complaints = $complaints;

		$this->getMessages();
		$this->view->script('/js/libs/table.filter.min.js');
		$this->view->script('/js/system.js');
	}

	public function actionSetComplaintAsViewed($complaint_id)
	{
		if(Request::$isAjax){
			$this->autoRender = false;
			$this->response->setHeader('Content-Type', 'text/json');

			$complaint = Model_Profile_Complaint::getOneComplaintsById($complaint_id);
			Model_Profile_Complaint::update(array(
				'isViewed' => TRUE
			), $complaint->complaintId);
			$complaint->isViewed = TRUE;

			$view = View::factory('admin/complaints/item-user-complaints', array(
				'complaint' => $complaint
			));

			$this->response->body = json_encode(array(
				'status' => TRUE,
				'function_name' => 'changeContent',
				'data' => array(
					'target' => 'tr[data-id="complaint_' . $complaint->complaintId . '"]',
					'content' => (string) $view
				)
			));
			return;
		}

		$this->response->redirect(Request::generateUri('admin', 'complaints'));
	}

	public function actionSetComplaintAsNew($complaint_id)
	{
		if(Request::$isAjax){
			$this->autoRender = false;
			$this->response->setHeader('Content-Type', 'text/json');

			$complaint = Model_Profile_Complaint::getOneComplaintsById($complaint_id);
			Model_Profile_Complaint::update(array(
				'isViewed' => FALSE
			), $complaint->complaintId);
			$complaint->isViewed = FALSE;

			$view = View::factory('admin/complaints/item-user-complaints', array(
				'complaint' => $complaint
			));

			$this->response->body = json_encode(array(
				'status' => TRUE,
				'function_name' => 'changeContent',
				'data' => array(
					'target' => 'tr[data-id="complaint_' . $complaint->complaintId . '"]',
					'content' => (string) $view
				)
			));
			return;
		}

		$this->response->redirect(Request::generateUri('admin', 'complaints'));
	}

	public function actionBlockUserFromUserComplaints($user_id)
	{
		return $this->actionBlockUser($user_id, TRUE);
	}

	public function actionBlockUser($user_id, $isFromUserComplaints = FALSE)
	{
		if(Request::$isAjax){
			$this->autoRender = false;
			$this->response->setHeader('Content-Type', 'text/json');

			$usercomplaint = Model_Profile_Complaint::getOneUserWithComplaintsInfo($user_id);
			Model_User::update(array(
				'isBlocked' => TRUE
			), $usercomplaint->profile_id);
			$usercomplaint->isBlocked = TRUE;

			if($isFromUserComplaints) {
				$this->response->body = json_encode(array(
					'status' => TRUE,
					'function_name' => 'addClass',
					'data' => array(
						'target' => '.user_complaints_block',
						'class' => 'hidden',
						'function_name' => 'removeClass',
						'data' => array(
							'target' => '.user_complaints_unblock',
							'class' => 'hidden'
						)
					)
				));
			} else {
				$view = View::factory('admin/complaints/item-allusers-complaints', array(
					'user' => $usercomplaint
				));

				$this->response->body = json_encode(array(
					'status'        => TRUE,
					'function_name' => 'changeContent',
					'data'          => array(
						'target'  => 'tr[data-id="usercomplaint_' . $usercomplaint->profile_id . '"]',
						'content' => (string)$view
					)
				));
			}

			return;
		}

		$this->response->redirect(Request::generateUri('admin', 'complaints'));
	}


	public function actionUnblockUserFromUserComplaints($user_id)
	{
		return $this->actionUnBlockUser($user_id, TRUE);
	}

	public function actionUnBlockUser($user_id, $isFromUserComplaints = FALSE)
	{
		if(Request::$isAjax){
			$this->autoRender = false;
			$this->response->setHeader('Content-Type', 'text/json');

			$usercomplaint = Model_Profile_Complaint::getOneUserWithComplaintsInfo($user_id);
			Model_User::update(array(
				'isBlocked' => FALSE
			), $usercomplaint->profile_id);
			$usercomplaint->isBlocked = FALSE;


			if($isFromUserComplaints) {
				$this->response->body = json_encode(array(
					'status' => TRUE,
					'function_name' => 'addClass',
					'data' => array(
						'target' => '.user_complaints_unblock',
						'class' => 'hidden',
						'function_name' => 'removeClass',
						'data' => array(
							'target' => '.user_complaints_block',
							'class' => 'hidden'
						)
					)
				));
			} else {
				$view = View::factory('admin/complaints/item-allusers-complaints', array(
					'user' => $usercomplaint
				));

				$this->response->body = json_encode(array(
					'status' => TRUE,
					'function_name' => 'changeContent',
					'data' => array(
						'target' => 'tr[data-id="usercomplaint_' . $usercomplaint->profile_id . '"]',
						'content' => (string) $view
					)
				));
			}

			return;
		}

		$this->response->redirect(Request::generateUri('admin', 'complaints'));
	}

}