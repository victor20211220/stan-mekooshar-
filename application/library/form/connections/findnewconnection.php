<?php

class Form_Connections_FindNewConnection extends Form_Main
{
	public function __construct($email)
	{
		$form = new Form('findnewconnection', false, Request::generateUri('connections', 'addRegistered'));
		$form->attribute('class', 'hidden');

		$form->fieldset('fields', false, array('class' => 'customform on-white customform-label'));

		$form->text('name', '')
//			->attribute('placeholder', 'Email')
			->disabled(true)
			->attribute('disabled', 'disabled')
			->attribute('tabindex', 1)
			->attribute('maxlength', '64')
			->rule('maxLength', 64)
			->setValue($email);

		$form->fieldset('file', false, array('class' => 'customform on-white customform-label'));
		$form->file('file')
			->attribute('onchange', 'return web.inviteFormOnChangeFile(this);');
//			->rule(function ($field) {
//				$ext = strtolower(pathinfo($field->value, PATHINFO_EXTENSION));
//				if(!in_array($ext, array('csv', ''))) {
//					return 'Bad file. File must have *.CSV extension!';
//				}
//			});

		$form->fieldset('submit', false, array('class' => 'submit'));
		$form->html('choose_file', '<a class="btn-roundblue-border icons i-attachhoverwhite invite-chose_file" href="#" onclick="$(this).closest(\'form\').find(\'input:file\').click(); return false;"><span></span>Choose file</a>');
		$form->html('save', '<a class="btn-roundblue invite-next" href="#" onclick="$(this).closest(\'form\').find(\'input:submit\').click(); return false;">Next</a>');
		$form->submit('submit', 'Submit')
			->visible(false);

		$this->form = $form;

		return $this;
	}
}

