<?php

class Form_Groups_EditGroup extends Form_Main
{
	public function __construct($group)
	{
		$form = new Form('editgroup', false, Request::generateUri('groups', 'settings', $group->id));

		$form->fieldset('fields1', false, array('class' => 'customform on-white customform-label'));


//		$form->html('group_logo_label', false, '<b>Emblem:</b>')
//			->attribute('class', 'form-html');

		$viewEmblem = View::factory('pages/groups/block-ava_emblem', array(
			'group' => $group
		));

		$form->html('group_logo', 'Emblem', $viewEmblem)
			->attribute('class', 'form-html');







		$form->fieldset('fields2', false, array('class' => 'customform on-white customform-label'));

//		$form->html('name_label', false, '<b>Group name:</b>')
//			->attribute('class', 'form-html');

		$form->text('name', 'Group name')
			->attribute('tabindex', '1')
			->attribute('required', 'required')
			->required()
			->setValue($group->name)
			->rule(function ($field) use ($group) {
				$isGroup = Model_Groups::checkIsRegistredByNameWithoutId($field->value, $group->id);

				if($isGroup) {
					return 'This group is created!';
				}
			});




//		$form->html('website_label', false, '<b>Website:</b>')
//			->attribute('class', 'form-html');

		$form->text('website', 'Website')
			->attribute('tabindex', '2')
			->attribute('maxlength', '128')
			->rule('maxLength', 128)
			->rule('url');


//		$form->html('ownerEmail_label', false, '<b>Owner email:</b>')
//			->attribute('class', 'form-html');

		$form->text('ownerEmail', 'Owner email')
			->attribute('tabindex', '3')
			->attribute('maxlength', '64')
			->rule('maxLength', 64)
			->rule('email');






		$form->fieldset('fields3', false, array('class' => 'customform on-white customform-label'));


//		$form->html('group_cover_label', false, '<b>Cover:</b>')
//			->attribute('class', 'form-html');

		$viewCover = View::factory('pages/groups/block-ava_cover', array(
			'group' => $group
		));

		$form->html('group_cover', 'Cover', $viewCover)
			->attribute('class', 'form-html');



//		$form->html('accessType_label', false, '<b>Access:</b>')
//			->attribute('class', 'form-html');

		$form->radio('accessType', t('group_access_type'), 'Access')
			->attribute('class', 'form-radio')
			->attribute('tabindex', '4')
			->attribute('maxlength', '1')
			->rule('maxLength', 1);



//		$form->html('discussionControlType_label', false, '<b>Authorization:</b>')
//			->attribute('class', 'form-html');

		$form->radio('discussionControlType', t('group_discussion_control_type'), 'Authorization')
			->attribute('class', 'form-radio')
			->attribute('tabindex', '5')
			->attribute('maxlength', '1')
			->rule('maxLength', 1);





//		$form->html('descriptionShort_label', false, '<b>Short description:</b>')
//			->attribute('class', 'form-html');
//
//		$form->textarea('descriptionShort', 'Short description')
//			->attribute('rows', '5')
//			->attribute('tabindex', '6')
//			->attribute('maxlength', '255')
//			->rule('maxLength', 255)
//			->attribute('required', 'required')
//			->required();


//		$form->html('description_label', false, '<b>Full description:</b>')
//			->attribute('class', 'form-html');

		$form->textarea('description', 'Description')
			->attribute('rows', '5')
			->attribute('tabindex', '7')
			->attribute('maxlength', '10000')
			->rule('maxLength', 10000)
			->attribute('class', 'max-10000')
			->attribute('required', 'required')
			->required();




		$form->fieldset('fields4', false, array('class' => 'customform on-white customform-label'));

		if(!$group->isAgree) {
			$form->checkbox('isAgree', false, 'I have read and agree to the User Agreement and Privacy Policy')
				->attribute('class', 'form-checkbox form-period')
				->attribute('required', 'required')
				->required();
			$submit_text = 'Create group';
		} else {
			$submit_text = 'Save changes';
		}

		$form->fieldset('submit', false, array('class' => 'submit'));
		$form->html('cancel', '<a class="btn-roundbrown" href="' . Request::generateUri('groups', 'index', $group->id) . '">Cancel</a>');
		$form->html('save', '<a class="btn-roundblue" href="#" onclick="$(this).closest(\'form\').find(\'input:submit\').click(); return false;">' . $submit_text . '<span></span></a>');
		$form->submit('submit', 'Submit')
			->visible(false);

		$this->form = $form;

		return $this;
	}


	public function setValues($group)
	{
		$values = $group->getValues();
		$this->form->loadValues($values);
	}

	public function setUseOfTerms ($useOfTerm_text)
	{
		if($useOfTerm_text) {
			$form =& $this->form;

			$form->fieldset('submit', false, array('class' => 'submit'));
			$form->elements['isAgree']->contentRight('<a href="#" title="Read terms of use" onclick="return web.showTermsOfUse(\'.termsOfUse\');">Read terms of use</a>');
			$form->html('TermsOfUse', false, $useOfTerm_text)
				->attribute('class', 'termsOfUse')
				->visible(FALSE);
		}
	}
}