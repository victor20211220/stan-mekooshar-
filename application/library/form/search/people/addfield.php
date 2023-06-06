<?php

class Form_Search_People_AddField extends Form_Main
{
	public function __construct()
	{
		$form = new Form('addfield', false, Request::generateUri('search', 'addSearchCompany'));
		$form->attribute('onsubmit', "return box.submit(this);");

		$form->fieldset('submit', false, array('class' => 'submit'));
		$form->html('cancel', '<a class="btn-roundbrown" href="' . Request::generateUri('profile', 'edit') . '" onclick="return box.close();">Cancel</a>');
		$form->html('save', '<a class="btn-roundblue" href="#" onclick="$(this).closest(\'form\').find(\'input:submit\').click(); return false;">Add</a>');
		$form->submit('submit', 'Submit')
			->visible(false);

		$this->form = $form;

		return $this;
	}

	public function generateCompany($without_company = array(), $action = FALSE)
	{
		$form =& $this->form;
		$form->fieldset('field', false, array('class' => 'customform on-white customform-label'));

		$form->text('company', 'Company name')
//			->attribute('placeholder', 'COMPANY NAME')
			->attribute('tabindex', '1')
			->attribute('maxlength', '128')
			->rule('maxLength', 128)
			->rule(function($field) use ($without_company) {
				if (in_array($field->value, $without_company)) {
					return 'The company is in filter!';
				}
			});

		if(!$action) {
			$form->attributes['action'] = Request::generateUri('search', 'addSearchCompany');
		} else {
			$form->attributes['action'] = $action;
		}

		return true;
	}

	public function generateRegion($without_region = array(), $action = FALSE)
	{
		$form =& $this->form;
		$form->fieldset('field', false, array('class' => 'customform on-white customform-label'));

		$regions = t('countries');
		foreach($without_region as $keys) {
			unset($regions[$keys]);
		}

		if(empty($regions)) {
			return false;
		}

		$form->select('region', array('' => '') + $regions, 'Region name')
//			->attribute('placeholder', 'REGION NAME')
			->attribute('class', 'bootstripe')
			->attribute('tabindex', '1')
			->attribute('maxlength', '128')
			->rule('maxLength', 128)
			->rule(function ($field) use($regions) {
				if (!isset($regions[$field->value])) {
					return 'Please select region!';
				}
			});

		if(!$action) {
			$form->attributes['action'] = Request::generateUri('search', 'addSearchRegion');
		} else {
			$form->attributes['action'] = $action;
		}

		return true;
	}

	public function generateIndustry($without_industry = array(), $action = FALSE)
	{
		$form =& $this->form;
		$form->fieldset('field', false, array('class' => 'customform on-white customform-label'));

		$industries = t('industries');
		foreach($without_industry as $keys) {
			unset($industries[$keys]);
		}

		if(empty($industries)) {
			return false;
		}

		$form->select('industry', array('' => '') + $industries, 'Industry')
//			->attribute('placeholder', 'INDUSTRY')
			->attribute('class', 'bootstripe')
			->attribute('tabindex', '1')
			->attribute('maxlength', '128')
			->rule('maxLength', 128)
			->rule(function ($field) use($industries) {
				if (!isset($industries[$field->value])) {
					return 'Please select industry!';
				}
			});

		if(!$action) {
			$form->attributes['action'] = Request::generateUri('search', 'addSearchIndustry');
		} else {
			$form->attributes['action'] = $action;
		}

		return true;
	}

	public function generateCompanyType($without_type = array(), $action)
	{
		$form =& $this->form;
		$form->fieldset('field', false, array('class' => 'customform on-white customform-label'));

		$types = t('company_type');
		foreach($without_type as $keys) {
			unset($types[$keys]);
		}

		if(empty($types)) {
			return false;
		}

		$form->select('type', array('' => '') + $types, 'Type')
//			->attribute('placeholder', 'TYPE')
			->attribute('class', 'bootstripe')
			->attribute('tabindex', '1')
			->attribute('maxlength', '2')
			->rule('maxLength', 2)
			->rule(function ($field) use($types) {
				if (!isset($types[$field->value])) {
					return 'Please select company type!';
				}
			});

			$form->attributes['action'] = $action;

		return true;
	}


	public function generateCompanyEmployer($without_employer = array(), $action)
	{
		$form =& $this->form;
		$form->fieldset('field', false, array('class' => 'customform on-white customform-label'));

		$employers = t('company_number_of_employer');
		foreach($without_employer as $keys) {
			unset($employers[$keys]);
		}

		if(empty($employers)) {
			return false;
		}

		$form->select('employer', array('' => '') + $employers, 'Employers')
//			->attribute('placeholder', 'EMPLOYER')
			->attribute('class', 'bootstripe')
			->attribute('tabindex', '1')
			->attribute('maxlength', '2')
			->rule('maxLength', 2)
			->rule(function ($field) use($employers) {
				if (!isset($employers[$field->value])) {
					return 'Please select company employer!';
				}
			});

		$form->attributes['action'] = $action;

		return true;
	}

	public function generateSchool($without_school = array(), $action = FALSE)
	{
		$form =& $this->form;
		$form->fieldset('field', false, array('class' => 'customform on-white customform-label'));

		$form->text('school', 'School name')
//			->attribute('placeholder', 'SCHOOL NAME')
			->attribute('tabindex', '1')
			->attribute('maxlength', '128')
			->rule('maxLength', 128)
			->rule(function($field) use ($without_school) {
				if (in_array($field->value, $without_school)) {
					return 'The school is in filter!';
				}
			});

		if(!$action) {
			$form->attributes['action'] = Request::generateUri('search', 'addSearchSchool');
		} else {
			$form->attributes['action'] = $action;
		}

		return true;
	}


	public function generateSkill($without_skill = array(), $action)
	{
		$form =& $this->form;
		$form->fieldset('field', false, array('class' => 'customform on-white customform-label'));

		$skills = Model_Skills::getList_OrderCountUsed();
		foreach($without_skill as $keys) {
			unset($skills['data'][$keys]);
		}

//		if(empty($skills)) {
//			return false;
//		}


		$this->generateAutocomplete('skill', '', 'Skills', FALSE, $skills, 'getListSkills', FALSE);
//		$form->select('skill', array('' => '') + $skills, 'Skills')
//			->attribute('class', 'bootstripe')
//			->attribute('tabindex', '1')
//			->attribute('maxlength', '2')
//			->rule('maxLength', 2)
//			->rule(function ($field) use($skills) {
//				if (!isset($skills[$field->value])) {
//					return 'Please select skill!';
//				}
//			});

		$form->attributes['action'] = $action;

		return true;
	}
}

