<?php

class Form_Login extends Form_Main
{
	public $checkName = false;

	public function __construct()
	{
		$form = new Form('login', false, Request::generateUri('signin', 'index'));
		$form->attribute('onsubmit', "return box.submit(this, function(content){web.login(content)});");

//		$form->fieldset('fields', false, array('class' => 'modernform fieldicon'));
		$form->fieldset('fields', false, array('class' => 'customform'));

		$obj = $this;
//		$form->text('email', '<span class="icons i-user"><span></span></span>')
		$form->text('email')
			->required()
			->attribute('required', 'required')
			->attribute('placeholder', 'E-mail address')
			->attribute('maxlength', '64')
			->attribute('tabindex', '11')
			->rule('maxLength', 64)
			->before(function ($field) use ($obj) {
				$user = $obj->checkAuth($field->value, $field->fieldset->elements['password']->value);
				if (!$user) {
					$field->rule('email');
				}
			})
			->rule(function($field) use ($obj){
				$obj->checkName = Model_User::checkByName($field->value);

				if(!$obj->checkName || $obj->checkName->isRemoved == 1 || $obj->checkName->isConfirmed == 0) {
					return 'User is not registered!';
				}
			});

//		$form->password('password', '<span class="icons i-key"><span></span></span>')
		$form->password('password')
			->required()
			->attribute('required', 'required')
			->attribute('placeholder', 'Password')
			->attribute('maxlength', '24')
			->attribute('minlength', '5')
			->attribute('tabindex', '12')
			->rule('minLength', 5)
			->rule('maxLength', 24)
			->rule(function ($field) use ($obj) {
				$user = Auth::getInstance()->getIdentity();
				if (!$user) {
					return t('login_failed');
				}
			});
//			->rule(function ($field) use ($this) {
//				$user = $this->checkAuth($field->fieldset->elements['email']->value, $field->value);
//				if (!$user) {
//					return t('login_failed');
//				}
//			});

//		$form->fieldset('submit', false, array('class' => 'modernsubmit fieldicon'));
		$form->fieldset('submit', false, false);
        $form->html('fb', '<a class="facebook" href="/socials/facebookLogin/"></a>');
        $form->html('in', '<a class="in" href="/socials/linkedinLogin/"></a>');
        $form->html('reset', '<a class="reset-btn" href="' . Request::generateUri('auth', 'resetPassword') . '" onclick="return box.load(this);">Reset password</a>');
		$form->html('button', '<a class="btn-roundblue" href="#" onclick="$(this).closest(\'form\').find(\'input:submit\').click(); return false;">Log in</a>');
		$form->submit('submit', 'Submit')
			->visible(false);

		$this->form = $form;

		return $this;
	}

	public function checkAuth($email, $password)
	{
		$auth = Auth::getInstance();
		$user = $auth->authenticate(mb_strtolower($email, 'utf-8'), $password, true, true);
		if(!$user) {
			$user2 = $auth->authenticate(mb_strtolower($email, 'utf-8'), $password, true, false);

			if($user2) {
				$user = $user2;
			}
		}
		return $user;
	}
}