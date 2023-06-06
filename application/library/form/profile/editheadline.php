<?php

class Form_Profile_EditHeadline extends Form_Main
{
	public function __construct()
	{
		$form = new Form('editheadline', false, Request::generateUri('profile', 'editHeadline'));
		$form->attribute('onsubmit', "return box.submit(this);");
//		$form->attribute('class', "bg-blue");

		$form->fieldset('fields', false, array('class' => 'customform on-white customform-label'));

		$form->text('professionalHeadline', 'Professional headline')
//			->attribute('placeholder', 'PROFESSIONAL HEADLINE')
			->attribute('tabindex', '1')
			->attribute('maxlength', '128')
			->rule('maxLength', 128);
		$form->select('country', array('' => ' ', '0' => 'NONE') + t('countries'), 'Country')
//			->attribute('placeholder', 'COUNTRY')
			->attribute('class', 'bootstripe')
			->attribute('tabindex', '2')
			->attribute('maxlength', '64')
			->rule('maxLength', 64);
		$form->select('industry', array('' => ' ', '0' => 'NONE') + t('industries'), 'Industry')
//			->attribute('placeholder', 'INDUSTRY')
			->attribute('class', 'bootstripe')
			->attribute('tabindex', '3')
			->attribute('maxlength', '4')
			->rule('maxLength', 4);

		$form->fieldset('submit', false, array('class' => 'submit'));
		$form->html('cancel', '<a class="btn-roundbrown" href="' . Request::generateUri('profile', 'edit') . '" onclick="return web.cancelEditBlock(this);">Cancel</a>');
		$form->html('save', '<a class="btn-roundblue" href="#" onclick="$(this).closest(\'form\').find(\'input:submit\').click(); return false;">Save changes</a>');
		$form->submit('submit', 'Submit')
			->visible(false);

		$this->form = $form;

		return $this;
	}
}