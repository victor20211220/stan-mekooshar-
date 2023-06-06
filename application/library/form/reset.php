<?php

class Form_Reset extends Form_Main
{
	public $user = FALSE;

	public function __construct()
	{
		$form = new Form('reset_password', false, Request::generateUri('auth', 'resetPassword'));
		$form->attribute('onsubmit', "return box.submit(this);");

		$form->fieldset('fields', false, array('class' => 'customform on-white customform-label'));

		$obj =& $this;
		$form->text('email', 'Email')
			->attribute('required', 'required')
			->rule('email')
			->rule(function($field) use ($obj) {
				$user = Model_User::checkByEmail($field->value);
				$obj->user = $user;
				if(!$user) {
					return 'Bad email!';
				}
			})
			->required();


		$form->fieldset('submit', false, false);
		$form->html('cancel', '<a class="btn-roundbrown" href="' . Request::generateUri('profile', 'edit') . '" onclick="return box.close();">Cancel</a>');
		$form->html('save', '<a class="btn-roundblue" href="#" onclick="$(this).closest(\'form\').find(\'input:submit\').click(); return false;">Reset password</a>');
		$form->submit('submit', 'Submit')
			->visible(false);

		$this->form = $form;

		return $this;
	}


}