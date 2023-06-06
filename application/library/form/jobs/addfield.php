<?php

class Form_Jobs_AddField extends Form_Main
{
	public function __construct()
	{
		$form = new Form('addfield', false, Request::generateUri('search', 'addSearchIndustry'));
		$form->attribute('onsubmit', "return box.submit(this);");

		$form->fieldset('submit', false, array('class' => 'submit'));
		$form->html('cancel', '<a class="btn-roundbrown" href="' . Request::generateUri('profile', 'edit') . '" onclick="return box.close();">Cancel</a>');
		$form->html('save', '<a class="btn-roundblue" href="#" onclick="$(this).closest(\'form\').find(\'input:submit\').click(); return false;">Add</a>');
		$form->submit('submit', 'Submit')
			->visible(false);

		$this->form = $form;

		return $this;
	}

	public function generateIndustry()
	{
		$form =& $this->form;
		$form->fieldset('field', false, array('class' => 'customform on-white customform-label'));

		$industries = t('industries');

		if(isset($_COOKIE['search_jobs_industries'])) {
			$without_industry = explode('_', $_COOKIE['search_jobs_industries']);
		} else {
			$without_industry = array();
		}
		foreach($without_industry as $keys) {
			unset($industries[$keys]);
		}

		if(empty($industries)) {
			return false;
		}

		$form->select('industry', $industries, 'Industry')
			->attribute('class', 'bootstripe')
			->attribute('tabindex', '1')
			->attribute('maxlength', '128')
			->rule('maxLength', 128)
			->rule(function ($field) use($industries) {
				if (!isset($industries[$field->value])) {
					return 'Please select industry!';
				}
			});

		$form->attributes['action'] = Request::generateUri('jobs', 'addSearchIndustry');

		return true;
	}


	public function generateSkills()
	{
		$form =& $this->form;
		$form->fieldset('field', false, array('class' => 'customform on-white customform-label'));

		$getSkills = Model_Skills::getListAll();
		$skills = array();
		foreach($getSkills['data'] as $skill) {
			$skills[$skill->id] = $skill->name;
		}
		unset($getSkills);

		if(isset($_COOKIE['search_jobs_skills'])) {
			$without_skills = explode('_', $_COOKIE['search_jobs_skills']);
		} else {
			$without_skills = array();
		}
		foreach($without_skills as $keys) {
			unset($skills[$keys]);
		}

		if(empty($skills)) {
			return false;
		}

		$form->select('skill', array('' => '') + $skills, 'Skills')
//			->attribute('placeholder', 'SKILLS')
			->attribute('class', 'bootstripe')
			->attribute('tabindex', '1')
			->attribute('maxlength', '128')
			->rule('maxLength', 6)
			->rule(function ($field) use($skills) {
				if (!isset($skills[$field->value])) {
					return 'Please select skills!';
				}
			});

		$form->attributes['action'] = Request::generateUri('jobs', 'addSearchSkill');

		return true;
	}


	public function generateSkillsForJob()
	{
		$form =& $this->form;
		$form->fieldset('field', false, array('class' => 'customform on-white customform-label'));

		$getSkills = Model_Skills::getListAll();
		$skills = array();
		foreach($getSkills['data'] as $skill) {
			$skills[$skill->id] = $skill->name;
		}
		unset($getSkills);

		if(isset($_SESSION['jobs_skills'])) {
			$without_skills = $_SESSION['jobs_skills'];
		} else {
			$without_skills = array();
		}
		foreach($without_skills as $key => $status) {
			unset($skills[$key]);
		}

		if(empty($skills)) {
			return false;
		}

		$form->select('skill', array('' => '') + $skills, 'Skills')
//			->attribute('placeholder', 'SKILLS')
			->attribute('class', 'bootstripe')
			->attribute('tabindex', '1')
			->attribute('maxlength', '128')
			->rule('maxLength', 6)
			->rule(function ($field) use($skills) {
				if (!isset($skills[$field->value])) {
					return 'Please select skills!';
				}
			});

		$form->attributes['action'] = Request::generateUri('jobs', 'addJobSkill');

		return true;
	}

	public function generateSkillsForJobAsText()
	{
		$form =& $this->form;
		$form->fieldset('field', false, array('class' => 'customform on-white customform-label'));
		$result = Model_Skills::getList_OrderCountUsed();

//		$getSkills = Model_Skills::getListAll();
		$skills = array();
		foreach($result['data'] as $skill) {
			$skills[$skill->id] = $skill->name;
		}
		unset($getSkills);

		if(isset($_COOKIE['search_jobs_skills'])) {
			$without_skills = explode('_', $_COOKIE['search_jobs_skills']);
		} else {
			$without_skills = array();
		}
		foreach($without_skills as $keys) {
			unset($skills[$keys]);
			unset($result['data'][$keys]);
		}

		if(empty($skills)) {
			return false;
		}



		// --------------------------
		$text = '';
		foreach($result['data'] as $id=>$skill) {
			$text .= '<li data-itemid="' . $id . '" data-itemtitle="' . Html::chars($skill->name) . '" data-itemorder="' . $skill->countUsed . '">' . Html::chars($skill->name) . '</li>';
		}
		$text = '<ul class="selectize-customitems">' . $text .  '</ul>';

		if(!empty($result['paginator']['next'])) {
			$url_next = Request::generateUri('autoComplete', 'getListSkills') . Request::getQuery('page', '2');
		} else {
			$url_next = '';
		}
		// --------------------------


		$form->select('skill', array('' => 'Please select skill') + $skills, 'Skills')
			->attribute('class', 'selectize')
			->attribute('tabindex', '1')
			->attribute('maxlength', '128')
			->rule('maxLength', 6)
			->attribute('required', 'required')
			->attribute('data-selectize-customitems', '.selectize-customitems')
			->attribute('data-selectize-url', Request::generateUri	('autoComplete', 'getListSkills'))
			->attribute('data-selectize-url-next-page', $url_next)
			->attribute('data-selectize-order', 'true')
			->required()
			->rule(function ($field) use($skills) {
				$checkSkill = Model_Skills::checkItemById($field->value);

				if($checkSkill) {
					if(isset($_COOKIE['search_jobs_skills'])) {
						$without_skills = explode('_', $_COOKIE['search_jobs_skills']);
					} else {
						$without_skills = array();
					}
					if(in_array($checkSkill->id, $without_skills)) {
						return 'Please select skills!';
					}
				}
			});


		$form->html('list', false, $text)
			->visible(false)
			->attribute('class', 'form-html');

		$form->attributes['action'] = Request::generateUri('jobs', 'addSearchSkill');

		return true;
	}
}

