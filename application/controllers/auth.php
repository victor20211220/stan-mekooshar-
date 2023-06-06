<?php

/**
 * Auth controller.
 *
 * @version $Id: auth.php $
 * @package Application
 */

class Auth_Controller extends Controller
{
	protected $view = 'auth/template';
	
	public function before() {
		parent::before();

		if (!isset($_SESSION)) {
			session_start();
		}
	}
	
	public function actionLogin()
	{
		$v = $this->view;
		
		$v->title = 'Login';

		$form = new Form('signin', 'SIGN IN');
		$form->attribute('class', 'autoform no-stripes');
		$form->labelWidth = '95px';
		$form->text('username', 'Name')
			->attribute('size', 28)
			->rule('maxLength', 26)
			->rule('required');
		$form->password('password', 'Password')
			->attribute('size', 28)
			->rule('maxLength', 32)
			->rule('required');
		$form->checkbox('persistent', 'Stay signed in');
		$form->submit('submit', 'Sign In')
			->attribute('class', '');
		
		if ($form->validate()) {
			if ($form->validate()) {
				$values = $form->getValues();
				
				if (Auth::getInstance()->authenticate($values['username'], $values['password'], $values['persistent'])) {
					$this->response->redirect('/admin/');
				} else {
					$v->error = t('login_failed');
				}
			}
		} else {
			if(Request::$method == 'POST' && isset($_POST['login']['submit'])) {
				$v->error = t('validation_error');
			}
		}
		
		$v->content = $form;
		
		$this->getMessages();
	}
	
	public function actionLogout()
	{
		Auth::getInstance()->clearIdentity();
		unset($_SESSION['socials']);
//		dump($_SESSION); exit;
		$this->response->redirect('http://' . Request::$host . '/');
	}


	public function actionResetPassword()
	{
		if(Request::$isAjax) {
			$this->autoRender = false;
			$this->response->setHeader('Content-Type', 'text/json');

			$f_Reset = new Form_Reset();

			$isError = false;
			if(Request::isPost()) {
				if($f_Reset->form->validate()) {
					$values = $f_Reset->form->getValues();

					$user = $f_Reset->user;
					$code = Confirmations::generate($user->id, Confirmations::USER, Confirmations::PASSWORD, $values['email']);

					$mail = new Mailer('reset-password');
					$mail->code = $code;
					$mail->firstName = $user->firstName;
					$mail->send($values['email']);

					$this->response->body = json_encode(array(
						'status'        => TRUE,
						'function_name' => 'popupShow',
						'data'          => array(
							'content' => 'Please check your email<br>We\'ve sent you an email that will allow you to reset your password quickly and easily.',
							'title'  => 'Reset password'
						)
					));

					return;
				} else {
					$isError = true;
				}
			}

			$content = View::factory('parts/pbox-form', array(
				'title' => 'Reset password ',
				'content' => View::factory('popups/reset_password', array(
					'f_Reset' => $f_Reset->form
				))
			));

			$this->response->body = json_encode(array(
				'status' => (!$isError),
				'content' => (string)$content
			));
			return;
		}

		$this->response->redirect(Request::generateUri('index', 'index'));
	}

	public function actionNewPassword()
	{
		if(Request::$isAjax && (isset($_SESSION['resetPassword']) || ($_SESSION['newPassword'] && Request::isPost()))) {
			$this->autoRender = false;
			$this->response->setHeader('Content-Type', 'text/json');

			$f_NewPassword = new Form_NewPassword();

			$isError = false;
			if(Request::isPost()) {
				if($f_NewPassword->form->validate()) {
					$values = $f_NewPassword->form->getValues();

					$user = new Model_User($_SESSION['newPassword']);

					unset($_SESSION['newPassword']);
					$password = Model_User::encryptPassword($values['new']);
					Model_User::update(array(
						'password' => $password
					), $user->id);

					$mail = new Mailer('change-password');
					$mail->firstName = $user->firstName;
					$mail->send($user->email);

					$this->response->body = json_encode(array(
						'status'        => TRUE,
						'function_name' => 'popupShow',
						'data'          => array(
							'content' => 'Success! Password has been changed. Please login.',
							'title'  => 'Reset password'
						)
					));

					return;
				} else {
					$isError = true;
				}
			} else {
				$_SESSION['newPassword'] = $_SESSION['resetPassword'];
				unset($_SESSION['resetPassword']);
			}

			$content = View::factory('parts/pbox-form', array(
				'title' => 'Reset password ',
				'content' => View::factory('popups/new_password', array(
					'f_NewPassword' => $f_NewPassword->form
				))
			));

			$this->response->body = json_encode(array(
				'status' => (!$isError),
				'content' => (string)$content
			));
			return;
		}

		$this->response->redirect(Request::generateUri('index', 'index'));
	}

	
	public function actionConfirm()
	{
		$code = trim(Request::get('code', false));

		$this->view->title  = t('confirmation');
		$c = $this->view->content = new View('auth/confirm');

		if (false === ($res = Confirmations::confirm($code))) {
			$message = 'Code is expired.';
		} else {
			switch ($res->type) {
				case Confirmations::EMAIL:
					Model_User::update(
						array('email' => $res->value, 'isConfirmed' => 1),
						$res->sender
					);
					$auth = Auth::getInstance();
					$auth->authenticateWithoutPassword($res->value, false, true);
					Model_User::createViews($res->sender);
					Model_User_Friends::createEmptyFriends($res->sender);
					Model_Connections::createFirstVirtualConnection($res->sender);
					$message = 'Thank you, your email has been successfully confirmed. You can now log in with your email and password. ';
					$this->message($message, 1);
					$this->response->redirect(Request::generateUri('profile', 'edit'));
					return;
					break;
				case Confirmations::PASSWORD:
					if (!isset($_SESSION)) {
						session_start();
					}
					if ($user = new Model_User($res->sender)) {
						if ($user->email == $res->value) {
							$user->isConfirmed = 1;
							$user->save();
						}
					}
					$_SESSION['resetPassword'] = $res->sender;
					$this->response->redirect(Request::generateUri('index', 'index'));
					break;
				case Confirmations::CREATECOMPANY:
					$confirm = unserialize($res->value);

					$tmp = explode('@', $confirm['email']);
					$domain = (isset($tmp[1])) ? $tmp[1] : '';

					Model_Companies::update(
						array(
							'user_id' => $res->sender,
							'email' => $confirm['email'],
							'domain' => $domain,
							'createDate' => CURRENT_DATETIME,
							'followers' => 1
						),	$confirm['company_id']
					);

					Model_Company_Follow::create(array(
						'company_id' => $confirm['company_id'],
						'user_id' => $res->sender
					));


					$message = 'Thank you, your corporate email has been successfully confirmed. You can edit company info now.';
					$this->message($message, 1);
					$this->response->redirect(Request::generateUri('companies', 'edit', $confirm['company_id']));
					break;
				case Confirmations::CREATESCHOOL:
					$confirm = unserialize($res->value);

					$tmp = explode('@', $confirm['email']);
					$domain = (isset($tmp[1])) ? $tmp[1] : '';

					Model_Universities::update(
						array(
							'user_id' => $res->sender,
							'email' => $confirm['email'],
							'domain' => $domain,
							'createDate' => CURRENT_DATETIME,
							'countFollowers' => 1,
							'isRegistered' => 1
						),	$confirm['school_id']
					);

					Model_University_Follow::create(array(
						'univercity_id' => $confirm['school_id'],
						'user_id' => $res->sender
					));


					$message = 'Thank you, your university email has been successfully confirmed. You can edit university info now.';
					$this->message($message, 1);
					$this->response->redirect(Request::generateUri('schools', 'edit', $confirm['school_id']));
					break;
				default:
					$message = 'Code';
			}
		}

		$this->message($message);
		$this->response->redirect('/');
	}


}