<?php

class Form_Schools_CreateSchool extends Form_Main
{
	public function __construct()
	{
		$form = new Form('createschool', false, Request::generateUri('schools', 'createSchool'));
		$form->attribute('onsubmit', "return box.submit(this);");

		$form->fieldset('fields', false, array('class' => 'customform on-white customform-label'));

		$form->text('schoolName', 'School name')
//			->attribute('placeholder', 'School Name')
			->attribute('tabindex', '1')
			->attribute('required', 'required')
			->required()
			->rule(function ($field)  {
				$company = Model_Universities::checkIsRegistredByName($field->value);

				if($company) {
					return 'This university is registered!';
				}
			});

		$form->text('email', 'School e-mail address')
			->required()
			->attribute('required', 'required')
//			->attribute('placeholder', 'School e-mail address')
			->attribute('maxlength', '64')
			->attribute('tabindex', '2')
			->rule('maxLength', 64)
			->rule('email')
			->rule(function ($field)  {
				$email = Model_Universities::checkIsEmailCorporate($field->value);

				if(!$email) {
					return 'This email is not univerity!';
				}

				$isEmail = Model_Universities::checkRegisteredByEmail($field->value);

//				if($isDomain) {
				if($isEmail) {
					return 'This email is registered!';
				}
			});


		$form->fieldset('submit', false, array('class' => 'submit'));
		$form->html('cancel', '<a class="btn-roundbrown" href="' . Request::generateUri('schools', 'updates') . '" onclick="return box.close();">Cancel</a>');
		$form->html('save', '<a class="btn-roundblue" href="#" onclick="$(this).closest(\'form\').find(\'input:submit\').click(); return false;">Create</a>');
		$form->submit('submit', 'Submit')
			->visible(false);

		$this->form = $form;

		return $this;
	}
}