<?php

class Form_Profile_EditEducation extends Form_Main
{
	public $university = FALSE;

	public function __construct($id = false, $school_name = '')
	{

		$form = new Form('editeducation', false, Request::generateUri('profile', 'editEducation', $id));
		$form->attribute('onsubmit', "return box.submit(this);");
		$this->form =& $form;


		// Get list for autocomplete
		$universities = Model_Universities::getList_OrderCountUsed();



		$form->fieldset('fields', false, array('class' => 'customform on-white customform-label'));

		$obj = $this->generateAutocomplete('university', 'Please select university or create new', 'University name', false, $universities, 'getListUniversities')
			->attribute('required', 'required')
			->attribute('tabindex', '1')
			->attribute('maxlength', '128')
			->rule('maxLength', 128)
			->required();
//			->rule(function ($field) use($school_name) {
//				$user = Auth::getInstance()->getIdentity();
//				if (Model_Profile_Education::checkUniversityByIdUserid_WithoutName($field->value, $user->id, $school_name)) {
//					return 'This university has been added!';
//				}
//			});

//		$form->text('university', 'University name')
////			->attribute('placeholder', 'UNIVERSITY NAME')
//			->attribute('required', 'required')
//			->attribute('tabindex', '1')
//			->attribute('maxlength', '128')
//			->rule('maxLength', 128)
//			->required()
//			->rule(function ($field) use($school_name) {
//				$user = Auth::getInstance()->getIdentity();
//				if (Model_Profile_Education::checkUniversityByNameUserid_WithoutName($field->value, $user->id, $school_name)) {
//					return 'This university has been added!';
//				}
//			});

		$form->html('label_from', false, 'From:')
			->attribute('class', 'form-html');

		for($i = 0; $i<= 70; $i++) $years[date('Y') - $i] = date('Y') - $i;

		$form->select('yearFrom', array('' => ' ') + $years, '')
//			->attribute('placeholder', 'YEAR')
			->attribute('class', 'bootstripe')
			->attribute('tabindex', '2')
			->attribute('maxlength', '4')
//			->attribute('required', 'required')
			->rule('maxLength', 4);
//			->required();

		$form->html('label_to', false, 'To:')
			->attribute('class', 'form-html');

		unset($years);
		$years = array();
		for($i = -10; $i<= 70; $i++) $years[date('Y') - $i] = date('Y') - $i;

		$form->select('yearTo', array('' => ' ') + $years, '')
//			->attribute('placeholder', 'YEAR')
			->attribute('class', 'bootstripe')
			->attribute('tabindex', '3')
			->attribute('maxlength', '4')
//			->attribute('required', 'required')
			->rule('maxLength', 4)
			->rule(function ($field){
				$form_values = $this->form->getValues();
				if( $form_values['yearFrom'] > $form_values['yearTo'] ) {
					return 'Wrong date';
				}
			});
//			->required();

		$form->text('fieldOfStudy', 'Field of study')
//			->attribute('placeholder', 'FIELD OF STUDY')
//			->attribute('required', 'required')
			->attribute('tabindex', '4')
			->attribute('maxlength', '160')
			->rule('maxLength', 160);
//			->required();

		$form->text('degree', 'Degree')
//			->attribute('placeholder', 'DEGREE')
			->attribute('tabindex', '5')
			->attribute('maxlength', '160')
			->rule('maxLength', 160);

		$form->text('grade', 'Grade')
//			->attribute('placeholder', 'GRADE')
			->attribute('tabindex', '6')
			->attribute('maxlength', '128')
			->rule('maxLength', 128);

		$form->textarea('activitiesAndSocieties', 'Activities and societies')
//			->attribute('placeholder', 'ACTIVITIES AND SOCIETIES')
			->attribute('rows', '5')
			->attribute('tabindex', '7')
			->attribute('maxlength', '10000')
			->rule('maxLength', 10000);
//          todo uncomment description
//		$form->textarea('description', 'Description')
////			->attribute('placeholder', 'DESCRIPTION')
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
		$values = array(
			'university' => $item->university_id,
			'yearFrom' => $item->yearFrom,
			'yearTo' => $item->yearTo,
			'fieldOfStudy' => $item->fieldOfStudy,
			'degree' => $item->degree,
			'grade' => $item->grade,
			'description' => $item->description,
			'activitiesAndSocieties' => $item->activitiesAndSocieties
		);

		$this->form->loadValues($values);
	}


	/**
	 * Get form values and check university name. If name does not isset in database, created it.
	 *
	 * @return array
	 */
	public function getPost()
	{
		$values = $this->form->getValues();

		if(substr($values['university'], 0, 4) == 'new%') {
			$name = substr($values['university'], 4);
			$check = Model_Universities::checkItemByName($name);
			if(!$check){
				$element = Model_Universities::create(array(
					'name' => $name
				));
				$this->university = $element;
				$this->university->countUsed = 0;
				$id = $element->id;
			} else {
				$id = $check->id;
				$this->project = $check;
			}
		} else {
			$name = $values['university'];
			$check = Model_Universities::checkItemById($name);
			if(!$check){
				$element = Model_Universities::create(array(
					'name' => $name
				));
				$this->university = $element;
				$this->university->countUsed = 0;
				$id = $element->id;
			} else {
				$id = $check->id;
				$this->university = $check;
			}
		}

		$values['university'] = $id;

		return $values;
	}

}