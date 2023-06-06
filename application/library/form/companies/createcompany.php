<?php

class Form_Companies_CreateCompany extends Form_Main
{
	public function __construct()
	{
		$form = new Form('createcompany', false, Request::generateUri('companies', 'createCompany'));
		$form->attribute('onsubmit', "return box.submit(this);");

		$form->fieldset('fields', false, array('class' => 'customform on-white customform-label'));

		$form->text('companyName', 'Company name')
//			->attribute('placeholder', 'Company Name')
			->attribute('tabindex', '1')
			->attribute('required', 'required')
			->required()
			->rule(function ($field)  {
				$company = Model_Companies::checkIsRegistredByName($field->value);

				if($company) {
					return 'This company is registered!';
				}
			});

		$form->text('email', 'Corporate e-mail address')
			->required()
			->attribute('required', 'required')
//			->attribute('placeholder', 'Corporate e-mail address')
			->attribute('maxlength', '64')
			->attribute('tabindex', '2')
			->rule('maxLength', 64)
			->rule('email')
			->rule(function ($field)  {
				$email = Model_Companies::checkIsEmailCorporate($field->value);

				if(!$email) {
					return 'This email is not corporate!';
				}

//				$isDomain = Model_Companies::checkRegisteredByDomain($field->value);
				$isEmail = Model_Companies::checkRegisteredByEmail($field->value);

//				if($isDomain) {
				if($isEmail) {
					return 'This email is registered!';
				}
			});


		$form->fieldset('submit', false, array('class' => 'submit'));
		$form->html('cancel', '<a class="btn-roundbrown" href="' . Request::generateUri('companies', 'index') . '" onclick="return box.close();">Cancel</a>');
		$form->html('save', '<a class="btn-roundblue" href="#" onclick="$(this).closest(\'form\').find(\'input:submit\').click(); return false;">Create</a>');
		$form->submit('submit', 'Submit')
			->visible(false);

		$this->form = $form;

		return $this;
	}
}