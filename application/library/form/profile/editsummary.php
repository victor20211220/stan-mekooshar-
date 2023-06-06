<?php

class Form_Profile_EditSummary extends Form_Main
{
	public function __construct()
	{
		$form = new Form('editsummary', false, Request::generateUri('profile', 'editSummary'));
		$form->attribute('onsubmit', "return box.submit(this);");

		$form->fieldset('fields', false, array('class' => 'customform on-white customform-label'));

		$form->textarea('summaryText', '')
//			->attribute('placeholder', 'SUMMARY')
			->attribute('rows', '5')
			->attribute('tabindex', '1')
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
}