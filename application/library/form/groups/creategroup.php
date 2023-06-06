<?php

class Form_Groups_CreateGroup extends Form_Main
{
	public function __construct()
	{
		$form = new Form('creategroup', false, Request::generateUri('groups', 'createGroup'));
		$form->attribute('onsubmit', "return box.submit(this);");

		$form->fieldset('fields', false, array('class' => 'customform on-white customform-label'));

		$form->text('groupName', 'Group name')
//			->attribute('placeholder', 'Group name')
			->attribute('tabindex', '1')
			->attribute('required', 'required')
			->required()
			->rule(function ($field)  {
				$group = Model_Groups::checkIsRegistredByName($field->value);

				if($group) {
					return 'This group is registered!';
				}
			});

		$form->fieldset('submit', false, array('class' => 'submit'));
		$form->html('cancel', '<a class="btn-roundbrown" href="' . Request::generateUri('companies', 'index') . '" onclick="return box.close();">Cancel</a>');
		$form->html('save', '<a class="btn-roundblue" href="#" onclick="$(this).closest(\'form\').find(\'input:submit\').click(); return false;">Create</a>');
		$form->submit('submit', 'Submit')
			->visible(false);

		$this->form = $form;

		return $this;
	}
}