<?php

class Form_ProfilePlans_Plan extends Form_Main
{
	public function __construct()
	{
		$form = new Form('plan');

		$form->fieldset('field');

		$form->text('name', 'Title')
			->attribute('maxlength', '255')
			->attribute('required', 'required')
			->rule('required');

		$form->text('price', 'Price $')
			->attribute('maxlength', '16')
			->attribute('required', 'required')
			->rule('required');

		$form->text('countMonth', 'Count left month')
			->attribute('maxlength', '2')
			->attribute('required', 'required')
			->rule('required');

		$form->submit('submit', 'Add')
			->attribute('eva-content', 'Add new plan')
			->attribute('class', 'btn btn-ok');

		$this->form = $form;
		return $this;
	}

	public function edit($plan)
	{
		$form =& $this->form;
		$form->elements['submit']->value = 'Save';
		$form->elements['submit']->attribute('eva-content', 'Save changes');
		$item = $plan->getValues();
		$item['countMonth'] = $item['countDays'] / 30;
		$form->loadValues($item);
	}
}