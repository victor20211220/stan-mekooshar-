<?php

class Form_Profile_EditExperience extends Form_Main
{
	public $experience = false;

	public function __construct($id = false)
	{
		$form = new Form('editexperience', false, Request::generateUri('profile', 'editExperience', $id));
		$form->attribute('onsubmit', "return box.submit(this, function(content){web.submitForm(content)});");
		$this->form =& $form;

		// Get list for autocomplete
		$companies_schools = Model_Companies::getList_OrderCountUsed();



		$form->fieldset('fields', false, array('class' => 'customform on-white customform-label'));
		$obj = $this->generateAutocomplete('company', 'Please select company/university or create new', 'Company/University name', false, $companies_schools, 'getListExperience')
			->attribute('required', 'required')
			->attribute('tabindex', '1')
			->attribute('maxlength', '128')
			->rule('maxLength', 128)
			->required();


		$form->text('company', 'Company name')
//			->attribute('placeholder', 'COMPANY NAME')
			->attribute('required', 'required')
			->attribute('tabindex', '1')
			->attribute('maxlength', '128')
			->rule('maxLength', 128)
			->required();
		$form->text('title', 'Position title')
//			->attribute('placeholder', 'POSITION TITLE')
			->attribute('required', 'required')
			->attribute('tabindex', '2')
			->attribute('maxlength', '128')
			->rule('maxLength', 128)
			->required();
		$form->text('location', 'Location')
//			->attribute('placeholder', 'LOCATION')
			->attribute('tabindex', '3')
			->attribute('maxlength', '160')
			->rule('maxLength', 160);

		$form->html('label_period', false, 'Period:')
			->attribute('class', 'form-html');
		$form->html('label_from', false, 'From:')
			->attribute('class', 'form-html');

		for($i = 1; $i<= 12; $i++) $months[$i] = $i;

		$form->select('monthFrom', array('' => ' ') + $months, 'Month')
//			->attribute('placeholder', 'MONTH')
			->attribute('class', 'bootstripe')
			->attribute('tabindex', '4')
			->attribute('maxlength', '2')
			->attribute('required', 'required')
			->rule('maxLength', 2)
			->required();

		for($i = 0; $i<= 70; $i++) $years[date('Y') - $i] = date('Y') - $i;

		$form->select('yearFrom', array('' => ' ') + $years, 'Year')
//			->attribute('placeholder', 'YEAR')
			->attribute('class', 'bootstripe')
			->attribute('tabindex', '5')
			->attribute('maxlength', '4')
			->attribute('required', 'required')
			->rule('maxLength', 4)
			->required();

		$form->html('label_to', false, 'To:')
			->attribute('class', 'form-html');

		$form->select('monthTo', array('' => ' ') + $months, 'Month')
//			->attribute('placeholder', 'MONTH')
			->attribute('class', 'bootstripe')
			->attribute('tabindex', '6')
			->attribute('maxlength', '2')
			->attribute('required', 'required')
			->rule('maxLength', 2)
			->before(function ($field) {
				if($field->fieldset->elements['isCurrent']->value == 1){
//					unset($field->attributes);
				} else {
					$field->required();
				}
			});

		$form->select('yearTo', array('' => ' ') + $years, 'Year')
//			->attribute('placeholder', 'YEAR')
			->attribute('class', 'bootstripe')
			->attribute('tabindex', '7')
			->attribute('maxlength', '4')
			->rule('maxLength', 4)
			->before(function ($field) {
				if($field->fieldset->elements['isCurrent']->value == 1){
//					unset($field->attributes);
					$form_values = $this->form->getValues();
					if (!$form_values['monthTo']) {
						$field->attribute('onload', 'web.showHidePeriodToIfCurrentWorkHereTrue(this)');
					}
				} else {
					$field->required();
				}
			});

		$form->checkbox('isCurrent', false, 'I currently work here')
			->attribute('class', 'form-checkbox form-period')
			->attribute('onchange', 'web.showHidePeriodTo(this)')
			->rule(function ($field){
				$form_values = $this->form->getValues();

				if ($form_values['monthFrom'] !== null && $form_values['monthFrom'] > date('n')) {
//					$field->attribute('onload', 'web.showHidePeriodToIfCurrentWorkHereTrue(this)');
					return 'Wrong date';
				}

				if ($form_values['isCurrent'] !== null &&  !$form_values['isCurrent']) {
					if( $form_values['yearFrom'] > $form_values['yearTo'] ) {
						return 'Wrong date';
					}

					if ( ( $form_values['yearFrom'] == $form_values['yearTo'] && $form_values['yearFrom'] == date('Y') ) ) {
						if ($form_values['monthFrom'] > date('n') || $form_values['monthTo'] > date('n')) {
							return 'Wrong date';
						}
						if ($form_values['monthFrom'] > $form_values['monthTo']) {
							return 'Wrong date';
						}
					}
				}
			});

		//todo uncomment work description
//		$form->textarea('description', 'Work description')
////			->attribute('placeholder', 'WORK DESCRIPTION')
//			->attribute('rows', '5')
//			->attribute('tabindex', '8')
//			->attribute('maxlength', '10000')
//			->attribute('class', 'max-10000')
//			->rule('maxLength', 10000);

		$form->fieldset('submit', false, array('class' => 'submit'));
		$form->html('cancel', '<a class="btn-roundbrown" href="' . Request::generateUri('profile', 'edit') . '" onclick="return web.cancelEditBlock(this);">Cancel</a>');
		$form->html('save', '<a class="btn-roundblue" href="#" onclick="$(this).closest(\'form\').find(\'input:submit\').click(); return false;">Save changes</a>');
		$form->submit('submit', 'Submit')
			->visible(false);

		$this->form = $form;

		return $this;
	}

	public function setValue($item)
	{
		if(!empty($item->company_id)) {
			$id = 'c' . $item->company_id;
		} else {
			$id = 'u' . $item->university_id;
		}

		$values = array(
			'company' => $id,
			'title' => $item->title,
			'location' => $item->location,
			'description' => $item->description,
			'monthFrom' => date('m', strtotime($item->dateFrom)),
			'yearFrom' => date('Y', strtotime($item->dateFrom)),
			'isCurrent' => $item->isCurrent
		);

		if($item->isCurrent != 1){
			$values['monthTo'] = date('m', strtotime($item->dateTo));
			$values['yearTo'] = date('Y', strtotime($item->dateTo));
		} else {
//			$values['monthTo'] = date('m', strtotime($item->dateFrom));
//			$values['yearTo'] = date('Y', strtotime($item->dateFrom));
		}

		$this->form->loadValues($values);
	}


	/**
	 * Get form values and check certification name. If name does not isset in database, created it.
	 *
	 * @return array
	 */
	public function getPost()
	{
		$values = $this->form->getValues();

		if(substr($values['company'], 0, 4) == 'new%') {
			$name = substr($values['company'], 4);
			$check = Model_Companies::checkItemByName_withUniversity($name);

			if(!$check){
				if(substr($check->name, 0, 1) == 'c') {
					$element = Model_Companies::create(array(
						'name' => $name
					));
					$id = 'c' . $element->id;
				} else {
					$element = Model_Universities::create(array(
						'name' => $name
					));
					$id = 'u' . $element->id;
				}

				$this->experience = $element;
				$this->experience->countUsed = 0;
			} else {
				$id = $check->id;
				$this->experience = $check;
			}
		} else {
			$name = $values['company'];
			$check = Model_Companies::checkItemById_withUniversity($name);
			if(!$check){
				if(true) {
					$element = Model_Companies::create(array(
						'name' => $name
					));
					$id = 'c' . $element->id;
				} else {
					$element = Model_Universities::create(array(
						'name' => $name
					));
					$id = 'u' . $element->id;
				}

				$this->experience = $element;
				$this->experience->countUsed = 0;
			} else {
				$id = $check->id;
				$this->experience = $check;
			}
		}

		$values['company'] = $id;

		return $values;
	}

}