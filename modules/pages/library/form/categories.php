<?php

class Form_Categories extends Form_Main
{
	public function __construct()
	{
		$form = new Form('plan');

		$form->fieldset('field');

		$form->text('name', 'Name')
			->attribute('maxlength', '255')
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
		$form->loadValues($plan->getValues());
	}
}