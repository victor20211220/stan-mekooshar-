<?php

class Form_Messages_FilterMessage extends Form_Main
{
	public function __construct()
	{
		$filter = Request::get('filter', false);

		$form = new Form('filtermessage', false, Request::generateUri('messages', false) . Request::getQueryWithoutKeys(false, false, array('filter')));

		$form->fieldset('fields', false, array('class' => 'customform on-white customform-label'));




		$form->select('filter', array('' => '', 'all' => 'ALL MESSAGES', 'unread' => 'UNREAD MESSAGES'), '')
//			->attribute('placeholder', 'FILTER')
			->attribute('class', 'bootstripe')
			->attribute('tabindex', '1')
			->attribute('maxlength', '1')
			->attribute('onchange', 'web.changeMessagesFilter(this);')
			->rule('maxLength', 1)
			->setValue(($filter) ? $filter : 'all');

		$form->fieldset('submit', false, array('class' => 'submit'));
		$form->submit('submit', 'Submit')
			->visible(false);
		$this->form = $form;

		return $this;
	}
}