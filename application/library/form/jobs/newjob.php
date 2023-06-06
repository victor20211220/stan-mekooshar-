<?php

class Form_Jobs_NewJob extends Form_Main
{
	public function __construct()
	{
		$form = new Form('newjob', false, Request::generateUri('jobs', 'newjob'));
		$this->form =& $form;
		$form->fieldset('fields1', false, array('class' => 'customform on-white customform-label'));


		$user = Auth::getInstance()->getIdentity();
		$companies = Model_Companies::getListAvalibleByuserId($user->id);
		$myCompanies = array();
		foreach($companies['data'] as $company) {
			$myCompanies[$company->id] = $company->name;
		}
		$selected = '';
		if(count($myCompanies) == 1) {
			$selected = key($myCompanies);
		}
//		$form->html('company_label', false, '<b>Company:</b>')
//			->attribute('class', 'form-html')
//			->inline();

//		$form->html('industry_label', false, '<b>Industry:</b>')
//			->attribute('class', 'form-html');

		$form->select('company', array('' => '') + $myCompanies, 'Company')
			->attribute('class', 'bootstripe')
			->attribute('tabindex', '1')
			->attribute('required', 'required')
			->attribute('maxlength', '10')
			->rule('maxLength', 10)
			->required()
			->setValue($selected);




		$form->select('industry', array('' => '') + t('industries'), 'Industry')
			->attribute('class', 'bootstripe')
			->attribute('tabindex', '2')
			->attribute('required', 'required')
			->attribute('maxlength', '3')
			->rule('maxLength', 3)
			->required();





//		$form->html('country_label', false, '<b>Country:</b>')
//			->attribute('class', 'form-html')
//			->inline();
//
//		$form->html('state_label', false, '<b>State:</b>')
//			->attribute('class', 'form-html')
//			->inline();
//
//		$form->html('city_label', false, '<b>City:</b>')
//			->attribute('class', 'form-html');

		$form->select('country', array('' => '') + t('countries'), 'Country')
			->attribute('class', 'bootstripe')
			->attribute('tabindex', '3')
			->attribute('required', 'required')
			->attribute('onchange', 'web.changeCountry(this)')
			->attribute('maxlength', '2')
			->rule('maxLength', 2)
			->required();




		$form->select('state', array('' => '') + t('states'), 'State')
			->attribute('class', 'bootstripe stateSelect')
			->attribute('tabindex', '4')
			->attribute('maxlength', '2')
			->rule('maxLength', 2)
			->after(function ($field) {
				if($field->fieldset->elements['country']->value != 'US') {
					$field->value = $field->fieldset->elements['state1']->value;
				}
			})
			->before(function ($field) {
				if($field->fieldset->elements['country']->value == 'US') {
					$field->required()
						->attribute('required', 'required')
						->visible(true);
				} else {
					$field->visible(false);
				}
			});
		$form->text('state1', 'State')
			->attribute('tabindex', '5')
			->attribute('class', 'stateText')
			->attribute('maxlength', '64')
			->rule('maxLength', 64)
			->phantom()
			->visible(false)
			->before(function ($field) {
				if($field->fieldset->elements['country']->value != 'US') {
					$field->required()
					->attribute('required', 'required')
					->visible(true);
				} else {
					$field->visible(false);
				}
			});




		$form->text('city', 'City')
			->attribute('tabindex', '6')
			->attribute('maxlength', '128')
			->attribute('required', 'required')
			->rule('maxLength', 128)
			->required();





		$form->fieldset('fields2', false, array('class' => 'customform on-white customform-label'));

//		$form->html('title_label', false, '<b>Title:</b>')
//			->attribute('class', 'form-html');

		$form->text('title', 'Title')
			->attribute('tabindex', '7')
			->attribute('maxlength', '128')
			->attribute('required', 'required')
			->rule('maxLength', 128)
			->required();




//		$form->html('description_label', false, '<b>Description:</b>')
//			->attribute('class', 'form-html');

		$form->textarea('description', 'Description')
			->attribute('tabindex', '8')
			->attribute('rows', '5')
			->attribute('maxlength', '10000')
			->rule('maxLength', 10000)
			->attribute('class', 'max-10000')
			->attribute('required', 'required')
			->required();




//		$form->html('about_label', false, '<b>About the company:</b>')
//			->attribute('class', 'form-html');

		$form->textarea('about', 'About the company')
			->attribute('tabindex', '10')
			->attribute('rows', '5')
			->attribute('maxlength', '10000')
			->rule('maxLength', 10000)
			->attribute('class', 'max-10000')
			->attribute('required', 'required')
			->required();





		$form->fieldset('fields3', false, array('class' => ''));
		$form->html('required_skills_label', false, '<b>Required skills:</b>')
			->attribute('class', 'form-html');

//		$form->textarea('required_skills', '<span class="icons i-dot"><span></span></span>')
//			->attribute('tabindex', '9')
//			->attribute('rows', '3')
//			->attribute('maxlength', '1000')
//			->rule('maxLength', 1000)
//			->attribute('required', 'required')
//			->required();
		$form->html('addskill', '<a class="btn-roundblue-border icons i-addcustom " href="' . Request::generateUri('jobs', 'addJobSkill') . '" onclick="box.load(this); return false;"><span></span> Add</a>');
		$this->generateSkills();





		$form->fieldset('fields4', false, array('class' => 'customform on-white customform-label'));
//		$form->html('employment_label', false, '<b>Employment:</b>')
//			->attribute('class', 'form-html');

		$form->select('employment', array('' => '') + t('employment'), 'Employment')
			->attribute('class', 'bootstripe')
			->attribute('tabindex', '11')
			->attribute('required', 'required')
			->attribute('maxlength', '1')
			->rule('maxLength', 1)
			->required();



		$form->fieldset('fields5', false, array('class' => 'customform on-white customform-label'));


		$form->radio('received', t('receive_applications'), '')
			->attribute('class', 'form-radio')
			->attribute('tabindex', '12')
			->attribute('onchange', 'web.newjobChangeReceive(this)')
			->attribute('maxlength', '1')
			->rule('maxLength', 1)
			->setValue(1);




		$form->text('email', '')
			->attribute('tabindex', '13')
			->attribute('maxlength', '64')
			->rule('maxLength', 64)
			->visible(false)
			->before(function ($field) {
				if($field->fieldset->elements['received']->value == '2') {
					$field->required()
						->attribute('required', 'required')
						->rule('email')
						->visible(true);
				} else {
					$field->visible(false)
						->phantom();
				}
			});



		$form->fieldset('submit', false, array('class' => 'submit'));
		$form->html('cancel', '<a class="btn-roundbrown" href="' . Request::generateUri('jobs', 'index') . '">Cancel</a>');
		$form->html('save', '<a class="btn-roundblue" href="#" onclick="$(this).closest(\'form\').find(\'input:submit\').click(); return false;">Post Job</a>');
		$form->submit('submit', 'Submit')
			->visible(false);


//		$this->form = $form;
		return $this;
	}

	public function edit($job) {
		$form =& $this->form;
		$form->attribute('action', Request::generateUri('jobs', 'editJob', $job->id) . Request::getQuery());
		$values = $job->getValues();
		$values['email'] = $values['receivedEmail'];
		$values['received'] = $values['receivedType'];
		$values['company'] = $values['company_id'];

		if($values['country'] != 'US') {
			$values['state1'] = $values['state'];
			unset($values['state']);
			$form->elements['state1']
				->attribute('required', 'required')
				->visible(true);
			$form->elements['state']
				->visible(false);
		} else {
			$form->elements['state1']
				->visible(false);
			$form->elements['state']
				->attribute('required', 'required')
				->visible(true);
		}

		if($values['received'] == 2) {
			$form->elements['email']
				->visible(true);
		}

		$form->fieldset('submit', false, array('class' => 'submit'));

		$from = Request::get('from', FALSE);
		$id = $job->id;
		if(!$from) {
			$from = 'myJobs';
		}
		if(in_array($from, array('myJobs', 'search'))) {
			$id = false;
		}

		$form->html('cancel', '<a class="btn-roundbrown" href="' . Request::generateUri('jobs', $from, $id) . '">Cancel</a>');
		$form->html('save', '<a class="btn-roundblue" href="#" onclick="$(this).closest(\'form\').find(\'input:submit\').click(); return false;">Save</a>');
		$form->submit('submit', 'Submit')
			->visible(false);

		$form->loadValues($values);
	}

	public function generateSkills()
	{
		$form =& $this->form;


		$getSkills = Model_Skills::getListAll();
		$skills_name = array();
		foreach($getSkills['data'] as $skill) {
			$skills_name[$skill->id] = $skill->name;
		}
		unset($getSkills);

		if(isset($_SESSION['jobs_skills'])) {
			$skills = $_SESSION['jobs_skills'];
		} else {
			$skills = array();
		}


		$i = 0;
		foreach($skills as $skill => $status) {
			if(empty($skill)) continue;
			$i ++;
			$form->html('skill_' . $skill, '<div data-id="skill_' . $skill . '">' . $skills_name[$skill] . '</div>
			<a class="icons i-deleteround" href="' . Request::generateUri('jobs', 'removeJobSkill', $skill) . '" onclick="return web.ajaxGet(this);" title="Delete skill"><span></span></a>');
		}

	}
}