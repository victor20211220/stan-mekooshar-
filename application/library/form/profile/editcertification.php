<?php

class Form_Profile_EditCertification extends Form_Main
{
	public $certification = false;

	public function __construct($id = false)
	{
		$form = new Form('editcertification', false, Request::generateUri('profile', 'editCertification', $id));
		$form->attribute('onsubmit', "return box.submit(this);");
		$this->form =& $form;

		// Get list for autocomplete
		$certifications = Model_Certifications::getList_OrderCountUsed();





		$form->fieldset('fields', false, array('class' => 'customform on-white customform-label'));
		$obj = $this->generateAutocomplete('certification', 'Please select certificat or create new', 'Certification name', false, $certifications, 'getListCertifications')
			->attribute('required', 'required')
			->attribute('tabindex', '1')
			->attribute('maxlength', '128')
			->rule('maxLength', 128)
			->required();

//		$form->text('certification', 'Certification name')
//			->attribute('required', 'required')
//			->attribute('tabindex', '1')
//			->attribute('maxlength', '128')
//			->rule('maxLength', 128)
//			->required();


		$form->text('authority', 'Certification authority')
			->attribute('tabindex', '2')
			->attribute('maxlength', '255')
			->rule('maxLength', 255);


//		$form->text('number', 'Licence number')
//			->attribute('tabindex', '38');

		$form->html('label_period', false, 'Period:')
			->attribute('class', 'form-html');
		$form->html('label_from', false, 'From:')
			->attribute('class', 'form-html');


		for($i = 1; $i<= 12; $i++) $months[$i] = $i;


		$form->select('monthFrom', array('' => ' ') + $months, 'Month')
			->attribute('class', 'bootstripe')
			->attribute('tabindex', '4')
			->attribute('maxlength', '2')
			->rule('maxLength', 2);


		unset($years);
		$years = array();
		for($i = 0; $i<= 70; $i++) $years[date('Y') - $i] = date('Y') - $i;


		$form->select('yearFrom', array('' => ' ') + $years, 'Year')
			->attribute('class', 'bootstripe')
			->attribute('tabindex', '5')
			->attribute('maxlength', '4')
			->rule('maxLength', 4);


		$form->html('label_to', false, 'To:')
			->attribute('class', 'form-html');


		$form->select('monthTo', array('' => ' ') + $months, 'Month')
			->attribute('class', 'bootstripe no-required')
			->attribute('tabindex', '6')
			->attribute('maxlength', '2')
			->rule('maxLength', 2)
			->before(function ($field) {
				if($field->fieldset->elements['isCurrent']->value == 1){
					unset($field->attributes);
				}
			});


		unset($years);
		$years = array();
		for($i = -70; $i<= 70; $i++) $years[date('Y') - $i] = date('Y') - $i;


		$form->select('yearTo', array('' => ' ') + $years, 'Year')
			->attribute('class', 'bootstripe no-required')
			->attribute('tabindex', '7')
			->attribute('maxlength', '4')
			->rule('maxLength', 4)
			->before(function ($field) {
				if($field->fieldset->elements['isCurrent']->value == 1){
					unset($field->attributes);
				}
			});


		$form->checkbox('isCurrent', false, 'This certificate does not expire')
			->attribute('class', 'form-checkbox form-period')
			->attribute('tabindex', '8')
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


		$form->text('url', 'Url')
			->attribute('tabindex', '9')
			->attribute('maxlength', '160')
			->rule('maxLength', 160)
			->rule('url');


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
		$values = array(
			'certification' => $item->certification_id,
			'authority' => $item->authorityName,
			'number' => $item->number,
			'isCurrent' => $item->isCurrent,
			'url' => $item->url,
		);

		if(strtotime($item->dateFrom) > 1) {
			$values['monthFrom'] = date('m', strtotime($item->dateFrom));
			$values['yearFrom'] = date('Y', strtotime($item->dateFrom));
		}
		if($item->isCurrent != 1 && strtotime($item->dateTo) > 1){
			$values['monthTo'] = date('m', strtotime($item->dateTo));
			$values['yearTo'] = date('Y', strtotime($item->dateTo));
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

		if(substr($values['certification'], 0, 4) == 'new%') {
			$name = substr($values['certification'], 4);
			$check = Model_Certifications::checkItemByName($name);
			if(!$check){
				$element = Model_Certifications::create(array(
					'name' => $name
				));
				$this->certification = $element;
				$this->certification->countUsed = 0;
				$id = $element->id;
			} else {
				$id = $check->id;
				$this->certification = $check;
			}
		} else {
			$name = $values['certification'];
			$check = Model_Certifications::checkItemById($name);
			if(!$check){
				$element = Model_Certifications::create(array(
					'name' => $name
				));
				$this->certification = $element;
				$this->certification->countUsed = 0;
				$id = $element->id;
			} else {
				$id = $check->id;
				$this->certification = $check;
			}
		}

		$values['certification'] = $id;

		return $values;
	}

}