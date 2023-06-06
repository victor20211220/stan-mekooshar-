<?php

class Form_Profile_EditUserName extends Form_Main
{
	public function __construct()
	{
		$form = new Form('editusername', false, Request::generateUri('profile', 'editUserName'));
		$form->attribute('onsubmit', "return box.submit(this);");

//		$form->fieldset('fields', false, array('class' => 'modernform fieldicon smalltype'));
		$form->fieldset('fields', false, array('class' => 'customform on-white customform-label'));

		$form->text('firstName', 'First name')
			->attribute('required', 'required')
//			->attribute('placeholder', 'FIRST NAME')
			->attribute('tabindex', '1')
			->attribute('maxlength', '32')
			->rule('maxLength', 32)
			->required();
		$form->text('lastName', 'Last name')
			->attribute('required', 'required')
//			->attribute('placeholder', 'LAST  NAME')
			->attribute('tabindex', '1')
			->attribute('maxlength', '32')
			->rule('maxLength', 32)
			->required();


		$form->fieldset('submit', false, array('class' => 'submit'));
		$form->html('cancel', '<a class="btn-roundbrown" href="' . Request::generateUri('profile', 'edit') . '" onclick="return web.cancelEditBlock(this);">Cancel</a>');
		$form->html('save', '<a class="btn-roundblue" href="#" onclick="$(this).closest(\'form\').find(\'input:submit\').click(); return false;">Save changes</a>');
		$form->submit('submit', 'Submit')
			->visible(false);

		$this->form = $form;

		return $this;
	}
}