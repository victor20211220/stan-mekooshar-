<?php

class Form_Profile_EditAdditional extends Form_Main
{
	public function __construct()
	{
		$form = new Form('editedditional', false, Request::generateUri('profile', 'editAdditional'));
		$form->attribute('onsubmit', "return box.submit(this);");

		$form->fieldset('fields', false, array('class' => 'customform on-white customform-label'));

		$form->text('birthdayDate', 'Birthday')
//			->attribute('placeholder', 'DATE BIRTHDAY')
			->attribute('class', 'datepicker')
			->attribute('tabindex', '1')
			->attribute('maxlength', '10')
			->rule('maxLength', 10);

		$form->select('maritalStatus', array('' => ' ') + t('maritel_status'), 'Marital status')
//			->attribute('placeholder', 'MARITAL STATUS')
			->attribute('class', 'bootstripe')
			->attribute('tabindex', '2')
			->attribute('maxlength', '1')
			->rule('maxLength', 1);

		$form->textarea('interests', 'Interests')
//			->attribute('placeholder', 'INTERESTS')
			->attribute('rows', '5')
			->attribute('tabindex', '3')
			->attribute('maxlength', '10000')
			->attribute('class', 'max-10000')
			->rule('maxLength', 10000);

		$form->fieldset('submit', false, array('class' => 'submit'));
		$form->html('cancel', '<a class="btn-roundbrown" href="' . Request::generateUri('profile', 'edit') . '" onclick="return web.cancelEditBlock(this);">Cancel</a>');
		$form->html('save', '<a class="btn-roundblue" href="#" onclick="$(this).closest(\'form\').find(\'input:submit\').click(); return false;">Save changes</a>');
		$form->submit('submit', 'Submit')
			->visible(false);

		$this->form = $form;

		return $this;
	}

	public function setValue($user)
	{
		$values = array(
			'birthdayDate' => (!empty($user->birthdayDate)) ? date('m/d/Y', strtotime($user->birthdayDate)) : '',
			'maritalStatus' => $user->maritalStatus,
			'interests' => $user->interests,
		);

		$this->form->loadValues($values);
	}

}