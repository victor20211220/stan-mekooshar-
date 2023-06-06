<?php

class Form_Profile_AddComplaint extends Form_Main
{

	public function __construct($profile)
	{
		$form = new Form('addcomplaint', FALSE, Request::generateUri('profile', 'complaint', $profile->id));
		$form->attribute('onsubmit', "return box.submit(this);");

		$this->form =& $form;

		$form->fieldset('fields', false, array('class' => 'customform on-white customform-label'));

		$form->textarea('description', 'You description:')
			->attribute('rows', '5')
			->attribute('tabindex', '1')
			->attribute('maxlength', '1000')
			->attribute('class', 'max-1000')
			->attribute('required', 'required')
			->required()
			->rule('maxLength', 1000);

		$form->fieldset('submit', false, array('class' => 'submit'));
		$url = Model_User::getUrlToProfile($profile);
		$form->html('cancel', '<a class="btn-roundbrown" href="' . $url . '" onclick="return box.close();">Cancel</a>');
		$form->html('add', '<a class="btn-roundblue" href="#" onclick="$(this).closest(\'form\').find(\'input:submit\').click(); return false;">Complaint</a>');
		$form->submit('submit', 'Submit')
			->visible(false);




		return $this;
	}

}