<?php

class Form_Registration extends Form_Main
{
	public function __construct()
	{
		$form = new Form('registration', false, Request::generateUri('registration', 'index'));
		$form->attribute('onsubmit', "return box.submit(this, function(content){web.registration(content)});");

		$form->fieldset('fields', false, array('class' => 'customform'));
//		$form->text('firstName', '1')
//			->required()
//			->attribute('required', 'required')
//			->attribute('placeholder', 'First Name')
//			->attribute('maxlength', '32')
//			->attribute('tabindex', '1')
//			->rule('maxLength', 32);
//
//		$form->text('lastName', '2')
//			->required()
//			->attribute('required', 'required')
//			->attribute('placeholder', 'Last Name')
//			->attribute('maxlength', '32')
//			->attribute('tabindex', '2')
//			->rule('maxLength', 32);
//
//		$form->text('email', '3')
//			->required()
//			->attribute('required', 'required')
//			->attribute('placeholder', 'Email Address')
//			->attribute('maxlength', '64')
//			->attribute('tabindex', '3')
//			->rule('maxLength', 64)
//			->rule('email')
//			->rule(function ($field) {
//				if (Model_User::exists(array('email = ? AND isRemoved = 0', $field->value))) {
//					return 'This email address is already registered in the system';
//				}
//			});
//
//		$form->password('password', '4')
//			->required()
//			->attribute('required', 'required')
//			->attribute('placeholder', 'Password')
//			->attribute('maxlength', '24')
//			->attribute('minlength', '5')
//			->attribute('tabindex', '4')
//			->inline()
//			->rule('minLength', 5)
//			->rule('maxLength', 24);

//		$form->password('confirm', '5')
//			->phantom()
//			->required()
//			->attribute('required', 'required')
//			->attribute('placeholder', 'Confirm Password')
//			->attribute('maxlength', '24')
//			->attribute('minlength', '5')
//			->attribute('tabindex', '5')
//			->rule('maxLength', 24)
//			->rule('minLength', 5)
//			->rule(function ($field) {
//				if($field->fieldset->elements['password']->value !== $field->value) {
//					return 'Passwords do not match';
//				}
//			});

		$form->fieldset('fields-2', false, array('class' => 'customform register-box'));
		$form->checkbox('isAgree', FALSE, ' ', 1)
			->attribute('class', 'form-checkbox form-period')
			->attribute('required', 'required')
			->attribute('checked', 'checked')
//			->setValue(0)
			->phantom()
			->required();
//			->rule(function ($field) {
//				if($field->value !== 1) {
//					return $field->value;
//				}
//			});

		$form->fieldset('submit', false, false);
//		$form->html('button', '<a class="btn-roundred" href="#" onclick="$(this).closest(\'form\').find(\'input:submit\').click(); return false;">Create Account</a>');
		$form->html('button', '<a class="btn-roundred" href="#" onclick="$(this).closest(\'form\').submit(); return false;">Create Account</a>');
		$form->submit('submit', 'Submit')
			->visible(false);

		$this->form = $form;

		return $this;
	}

	public function setUseOfTerms ($useOfTerm_text)
	{
		if($useOfTerm_text) {
			$form =& $this->form;

			$form->fieldset('fields-2');
			$form->elements['isAgree']->contentRight('<a href="#" title="Read terms of use" onclick="return web.showTermsOfUse(\'.termsOfUse\');">I have read and agree to the User Agreement and Privacy Policy</a>');
			$form->html('TermsOfUse', false, $useOfTerm_text)
				->attribute('class', 'termsOfUse')
				->visible(FALSE);
		}
	}
}