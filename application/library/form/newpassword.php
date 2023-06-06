<?php

class Form_NewPassword extends Form_Main
{
	public $user = FALSE;

	public function __construct()
	{
		$form = new Form('new_password', false, Request::generateUri('auth', 'newPassword'));
		$form->attribute('onsubmit', "return box.submit(this);");

		$form->fieldset('fields', false, array('class' => 'customform on-white customform-label'));

		$form->password('new', 'New password')
			->attribute('tabindex', '11')
			->attribute('maxlength', '32')
			->attribute('minlength', '5')
			->attribute('required', 'required')
			->rule('minLength', 5)
			->rule('maxLength', 32)
			->required();


		$form->password('reenternew', 'Re-enter new password:')
			->attribute('tabindex', '12')
			->attribute('maxlength', '32')
			->attribute('minlength', '5')
			->attribute('required', 'required')
			->rule('maxLength', 32)
			->rule('minLength', 5)
			->required()
			->rule(function ($field) {
				if($field->value != $field->fieldset->elements['new']->value) {
					return 'Passwords do not match!';
				}
			});


		$form->fieldset('submit', false, false);
		$form->html('cancel', '<a class="btn-roundbrown" href="' . Request::generateUri('profile', 'edit') . '" onclick="return box.close();">Cancel</a>');
		$form->html('save', '<a class="btn-roundblue" href="#" onclick="$(this).closest(\'form\').find(\'input:submit\').click(); return false;">Save password</a>');
		$form->submit('submit', 'Submit')
			->visible(false);

		$this->form = $form;

		return $this;
	}


}