<?php

class Form_Schools_EditSchool extends Form_Main
{
	public function __construct($school)
	{
		$form = new Form('editschool', false, Request::generateUri('schools', 'edit', $school->id));

		$form->hidden('notable_alumni');

		$form->fieldset('fields1', false, array('class' => 'customform on-white customform-label'));


//		$form->html('company_logo_label', false, '<b>Logo:</b>')
//			->attribute('class', 'form-html');

		$viewLogo = View::factory('pages/schools/block-ava_logo', array(
			'school' => $school
		));

		$form->html('school_logo', 'Logo', $viewLogo)
			->attribute('class', 'form-html');



//		$form->html('name_label', false, '<b>School name:</b>')
//			->attribute('class', 'form-html');

		$form->text('name', 'School name')
			->attribute('tabindex', '1')
			->attribute('required', 'required')
			->required()
			->setValue($school->name)
			->rule(function ($field) use ($school) {
				$isSchool = Model_Universities::checkIsRegistredByNameWithoutId($field->value, $school->id);

				if($isSchool) {
					return 'This school is registered!';
				}
			});



//		$form->html('industry_label', false, '<b>Company industry:</b>')
//			->attribute('class', 'form-html');
//
//		$form->select('industry', array('' => '') + t('industries'), '<span class="icons i-dot"><span></span></span>')
//			->attribute('class', 'bootstripe')
//			->attribute('tabindex', '2')
//			->attribute('maxlength', '4')
//			->nullable()
//			->rule('maxLength', 4);



//		$form->html('type_label', false, '<b>School type:</b>')
//			->attribute('class', 'form-html');

		$form->select('type', array('' => '') + t('school_type'), 'School type')
			->attribute('class', 'bootstripe')
			->attribute('tabindex', '3')
			->attribute('maxlength', '1')
			->rule('maxLength', 1)
			->setValue(1);



//		$form->html('size_label', false, '<b>Number of employer:</b>')
//			->attribute('class', 'form-html');
//
//		$form->select('size', array('' => '') + t('company_number_of_employer'), '<span class="icons i-dot"><span></span></span>')
//			->attribute('class', 'bootstripe')
//			->attribute('tabindex', '4')
//			->attribute('maxlength', '1')
//			->rule('maxLength', 1)
//			->nullable()
//			->setValue(1);



//		$form->html('year_label', false, '<b>Year founded:</b>')
//			->attribute('class', 'form-html');

		$form->text('year', 'Year founded')
			->attribute('tabindex', '5')
			->attribute('maxlength', '4')
			->nullable()
			->rule('maxLength', 4);



//		$form->html('phone1_label', false, '<b>Phone 1:</b>')
//			->attribute('class', 'form-html');

		$form->text('phone1', 'Phone 1')
			->attribute('tabindex', '6')
			->attribute('maxlength', '32')
			->rule('maxLength', 32);



//		$form->html('phone2_label', false, '<b>Phone 2:</b>')
//			->attribute('class', 'form-html');

		$form->text('phone2', 'hone 2')
			->attribute('tabindex', '6')
			->attribute('maxlength', '32')
			->rule('maxLength', 32);



//		$form->html('address_label', false, '<b>Address:</b>')
//			->attribute('class', 'form-html');

		$form->text('address', 'Address')
			->attribute('tabindex', '7')
			->attribute('maxlength', '255')
			->rule('maxLength', 255);



//		$form->html('url_label', false, '<b>Website link:</b>')
//			->attribute('class', 'form-html');

		$form->text('url', 'Website link')
			->attribute('tabindex', '8')
			->attribute('maxlength', '128')
			->rule('maxLength', 128)
			->rule('url');



//		$form->html('email2_label', false, '<b>School associate email:</b>')
//			->attribute('class', 'form-html');

		$form->text('email2', 'School associate email')
			->attribute('tabindex', '9')
			->attribute('maxlength', '64')
			->rule('maxLength', 64)
			->rule('email');






		$form->fieldset('fields2', false, array('class' => 'customform on-white customform-label'));

//		$form->html('school_cover_label', false, '<b>Cover:</b>')
//			->attribute('class', 'form-html');

		$viewCover = View::factory('pages/schools/block-ava_cover', array(
			'school' => $school
		));

		$form->html('school_cover', 'Cover', $viewCover)
			->attribute('class', 'form-html');



//		$form->html('description_label', false, '<b>School description:</b>')
//			->attribute('class', 'form-html');

		$form->textarea('description', 'School description')
			->attribute('rows', '5')
			->attribute('tabindex', '10')
			->attribute('maxlength', '10000')
			->rule('maxLength', 10000)
			->attribute('class', 'max-10000')
			->attribute('required', 'required')
			->required();




		$form->fieldset('fields3', false, array('class' => 'customform on-white customform-label'));

		if(!$school->isAgree) {
			$form->checkbox('isAgree', false, 'I have read and agree to the User Agreement and Privacy Policy')
				->attribute('class', 'form-checkbox form-period')
				->attribute('required', 'required')
				->required();
			$submit_text = 'Create school';
		} else {
			$submit_text = 'Save changes';
		}

		$form->fieldset('submit', false, array('class' => 'submit'));
		$form->html('delete', '<a class="btn-roundbrown" href="' . Request::generateUri('schools', 'remove', $school->id) . '" onclick="return box.confirm(this);" title="Remove school">Remove</a>');
		$form->html('cancel', '<a class="btn-roundbrown" href="' . Request::generateUri('schools', 'index', $school->id) . '">Cancel</a>');
		$form->html('save', '<a class="btn-roundblue" href="#" onclick="$(this).closest(\'form\').find(\'input:submit\').click(); return false;">' . $submit_text . '</a>');
		$form->submit('submit', 'Submit')
			->visible(false);

		$this->form = $form;

		return $this;
	}


	public function setValues($school)
	{
		$values = $school->getValues();
		$values['year'] = $values['yearFounded'];
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