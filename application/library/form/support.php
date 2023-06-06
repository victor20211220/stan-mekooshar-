<?php

class Form_Support extends Form_Main
{
	public function __construct()
	{
		$form = new Form('support');

		$form->fieldset('fields-1', false, array('class' => 'customform customform-label'));
		$form->text('name', 'Name')
			->attribute('tabindex', '1')
			->attribute('required', 'required')
			->attribute('maxlength', '64')
			->required()
			->rule('maxLength', 64);

		$form->text('company_name', 'Company name')
			->attribute('tabindex', '2')
			->attribute('required', 'required')
			->attribute('maxlength', '160')
			->required()
			->rule('maxLength', 160);

		$form->text('email', 'E-mail address')
			->attribute('tabindex', '3')
			->attribute('required', 'required')
			->attribute('maxlength', '64')
			->rule('maxLength', 64)
			->rule('email')
			->required();

		$form->fieldset('fields-2', false, array('class' => 'customform customform-label'));
		$form->textarea('message', 'Write your message text ...')
			->attribute('rows', '5')
			->attribute('tabindex', '4')
			->attribute('required', 'required')
			->attribute('maxlength', '10000')
//			->attribute('class', 'max-10000')
			->rule('maxLength', 10000)
			->required();


		$form->fieldset('submit', false, false);
		$form->html('submit', '<a class="btn-roundblue" href="#" onclick="$(this).closest(\'form\').submit(); return false;">Send message</a>');


		$this->form = $form;

		return $this;
	}
}