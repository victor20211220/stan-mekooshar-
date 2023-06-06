<?php

class Form_Profile_EditProject extends Form_Main
{
	public $occupations = array();
	public $project = false;

	public function __construct($id = false)
	{
		$form = new Form('editproject', false, Request::generateUri('profile', 'editProject', $id));
		$form->attribute('onsubmit', "return box.submit(this);");
		$this->form =& $form;

		// Get list for autocomplete
		$projects = Model_Projects::getList_OrderCountUsed();



		$form->fieldset('fields', false, array('class' => 'customform on-white customform-label'));

		$obj = $this->generateAutocomplete('project', 'Please select project or create new', 'Project name', false, $projects, 'getListProjects')
			->attribute('required', 'required')
			->attribute('tabindex', '1')
			->attribute('maxlength', '128')
			->rule('maxLength', 128)
			->required();


//		$form->text('project', 'Project name')
////			->attribute('placeholder', 'PROJECT NAME')
//			->attribute('required', 'required')
//			->attribute('tabindex', '1')
//			->attribute('maxlength', '128')
//			->rule('maxLength', 128)
//			->required();

		$auth = Auth::getInstance();
		$user = $auth->getIdentity();
		$education = Model_Profile_Education::getListByUser($user->id);
		$experience = Model_Profile_Experience::getListByUser($user->id);

		$occupation = array();
		foreach($experience['data'] as $item) {
			if(!empty($item->company_id)) {
				$occupation['ex' . $item->id] = $item->companyName;
			} else {
				$occupation['ex' . $item->id] = $item->universityName;
			}
		}
		foreach($education['data'] as $item) {
			$occupation['ed' . $item->id] = 'Student at ' . $item->universityName;
		}

		$this->occupations = $occupation;


		$form->select('occupation', array('' => ' ') + $occupation, 'Field of occupation')
//			->attribute('placeholder', 'FIELD OF OCCUPATION:')
			->attribute('class', 'bootstripe')
			->attribute('required', 'required')
			->attribute('tabindex', '2')
			->attribute('maxlength', '128')
			->rule('maxLength', 128)
			->required();

		$form->text('url', 'Project url')
//			->attribute('placeholder', 'PROJECT URL:')
			->attribute('tabindex', '3')
			->attribute('maxlength', '160')
			->rule('maxLength', 160)
			->rule('url');

		$form->html('label_period', false, 'Period:')
			->attribute('class', 'form-html');
		$form->html('label_from', false, 'From:')
			->attribute('class', 'form-html');

		for($i = 1; $i<= 12; $i++) $months[$i] = $i;

		$form->select('monthFrom', array('' => 'month') + $months, 'Month')
//			->attribute('placeholder', 'MONTH')
			->attribute('class', 'bootstripe')
			->attribute('tabindex', '4')
			->attribute('maxlength', '2')
			->attribute('required', 'required')
			->rule('maxLength', 2)
			->required();

		for($i = 0; $i<= 70; $i++) $years[date('Y') - $i] = date('Y') - $i;

		$form->select('yearFrom', array('' => 'year') + $years, 'Year')
//			->attribute('placeholder', 'YEAR')
			->attribute('class', 'bootstripe')
			->attribute('tabindex', '5')
			->attribute('maxlength', '4')
			->attribute('required', 'required')
			->rule('maxLength', 4)
			->required();

		$form->html('label_to', false, 'To:')
			->attribute('class', 'form-html');

		$form->select('monthTo', array('' => 'month') + $months, 'Month')
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

		$form->select('yearTo', array('' => 'year') + $years, 'Year')
//			->attribute('placeholder', 'YEAR')
			->attribute('class', 'bootstripe')
			->attribute('tabindex', '7')
			->attribute('maxlength', '4')
			->rule('maxLength', 4)
			->before(function ($field) {
				if($field->fieldset->elements['isCurrent']->value == 1){
//					unset($field->attributes);
				} else {
					$field->required();
				}
			});

		$form->checkbox('isCurrent', false, 'Project Ongoing')
			->attribute('tabindex', '8')
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
//			->attribute('tabindex', '9')
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
			'project' => $item->project_id,
			'url' => $item->url,
			'description' => $item->description,
			'monthFrom' => date('m', strtotime($item->dateFrom)),
			'yearFrom' => date('Y', strtotime($item->dateFrom)),
			'isCurrent' => $item->isCurrent
		);

		if(!empty($item->occupation_education_id)){
			$values['occupation'] = 'ed' . $item->occupation_education_id;
		} else {
			$values['occupation'] = 'ex' . $item->occupation_experience_id;
		}


		if($item->isCurrent != 1){
			$values['monthTo'] = date('m', strtotime($item->dateTo));
			$values['yearTo'] = date('Y', strtotime($item->dateTo));
		}

		$this->form->loadValues($values);
	}

	/**
	 * Get form values and check project name. If name does not isset in database, created it.
	 *
	 * @return array
	 */
	public function getPost()
	{
		$values = $this->form->getValues();

		if(substr($values['project'], 0, 4) == 'new%') {
			$name = substr($values['project'], 4);
			$check = Model_Projects::checkItemByName($name);
			if(!$check){
				$element = Model_Projects::create(array(
					'name' => $name
				));
				$this->project = $element;
				$this->project->countUsed = 0;
				$id = $element->id;
			} else {
				$id = $check->id;
				$this->project = $check;
			}
		} else {
			$name = $values['project'];
			$check = Model_Projects::checkItemById($name);
			if(!$check){
				$element = Model_Projects::create(array(
					'name' => $name
				));
				$this->project = $element;
				$this->project->countUsed = 0;
				$id = $element->id;
			} else {
				$id = $check->id;
				$this->project = $check;
			}
		}

		$values['project'] = $id;

		return $values;
	}

}