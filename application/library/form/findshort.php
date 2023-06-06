<?php

class Form_FindShort extends Form_Main
{
	public function __construct()
	{
		$form = new Form('findshort', false, Request::generateUri('searchPeople', 'index') . Request::getQuery());
		$form->attribute('onsubmit', 'return web.submitPublicFindPanel(this);');

		if(isset($_GET['searchpeople'])) {
			$fullName = $_GET['searchpeople'];
			$tmp = explode(' ', $fullName);
			$firstName = $tmp[0];
			unset($tmp[0]);
			$lastName = implode(' ', $tmp);
		} else {
			$firstName = '';
			$lastName = '';
		}


		$form->fieldset('fields', false, array('class' => 'customform'));
		$form->text('firstName', 'First name')
			->attribute('placeholder', 'First Name')
			->setValue($firstName);
//			->before(function($field){
//				$value = trim($field->fieldset->elements['lastName']->value);
//				if(empty($value)) {
//					$field->attribute('required', 'required');
//					$field->required();
//				}
//			});


		$form->text('lastName', 'Last name')
			->attribute('placeholder', 'Last Name')
			->setValue($lastName);
//			->before(function($field){
//				$value = trim($field->fieldset->elements['firstName']->value);
//				if(empty($value)) {
//					$field->attribute('required', 'required');
//					$field->required();
//				}
//			});


		$form->fieldset('submit', false, false);
		$form->html('submit', '<a class=" search-btn" href="#" onclick="$(this).closest(\'form\').submit(); return false;">Search</a>');


		$this->form = $form;

		return $this;
	}

	public function onFindPage()
	{
		$form =& $this->form;

		$form->fieldsets['fields']->attribute('class', 'customform on-white');
	}
}