<?php

class Form_Profile_ChangeGpaphStatistic extends Form_Main
{
	public function __construct()
	{
		$form = new Form('changegpaphstatistic', false, Request::generateUri('profile', 'statistic'));
		$form->attribute('onsubmit', "return box.submit(this);");

		$form->fieldset('fields', false, array('class' => 'customform on-white customform-label'));

		$form->select('type', array(
				'' => '',
				'1' => 'View by month',
				'2' => 'View by week',
				'3' => 'View by days'), '')
			->attribute('class', 'bootstripe')
			->attribute('tabindex', '1')
			->attribute('maxlength', '1')
			->attribute('required', 'required')
			->attribute('onchange', 'web.changeProfileStatistic(this)')
			->rule('maxLength', 1)
			->setValue('1')
			->required();

		$this->form = $form;

		return $this;
	}

}