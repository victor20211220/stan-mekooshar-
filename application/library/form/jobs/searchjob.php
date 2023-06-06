<?php

class Form_Jobs_SearchJob extends Form_Main
{
	public function __construct()
	{
		$form = new Form('searchjob', false, Request::generateUri('jobs', 'search'));
		$form->attribute('onsubmit', 'return web.searchJobInJob(this);');
		$form->attribute('method', 'GET');

		$this->form =& $form;

		$form->hidden('industries');
		$form->hidden('skills');

		$form->fieldset('fields1', false, array('class' => 'customform on-white customform-label'));

		$form->text('search', false)
			->attribute('tabindex', '1')
			->attribute('placeholder', 'SEARCH FOR JOBS BY KEYWORDS');





		$form->fieldset('search', false, array('class' => 'submit'));
		$form->html('searchbtn', '<a class="btn-roundblue icons i-search" href="#" onclick="$(this).closest(\'form\').find(\'input:submit\').click(); return false;"><span></span></a>');
		$form->submit('submit', 'Submit')
			->visible(false);





		$form->fieldset('fields2', false, array('class' => 'customform on-white customform-label'));
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

		$form->select('country', array('' => '', 'all' => 'Nothing selected') + t('countries'), 'Country')
			->attribute('class', 'bootstripe')
			->attribute('tabindex', '2')
			->attribute('onchange', 'web.changeCountry(this, false)')
			->attribute('maxlength', '3')
			->rule('maxLength', 3);




		$form->select('state', array('' => '', 'all' => 'Nothing selected') + t('states'), 'State')
			->attribute('class', 'bootstripe stateSelect')
			->attribute('tabindex', '3')
			->attribute('maxlength', '3')
			->rule('maxLength', 3)
			->after(function ($field) {
				if($field->fieldset->elements['country']->value != 'US') {
					$field->value = $field->fieldset->elements['state1']->value;
				}
			})
			->before(function ($field) {
				if($field->fieldset->elements['country']->value == 'US') {
					$field->visible(true);
				} else {
					$field->visible(false);
				}
			});
		$form->text('state1', 'State')
			->attribute('tabindex', '4')
			->attribute('class', 'stateText')
			->attribute('maxlength', '64')
			->rule('maxLength', 64)
			->phantom()
			->visible(false)
			->before(function ($field) {
				if($field->fieldset->elements['country']->value != 'US') {
					$field->visible(true);
				} else {
					$field->visible(false);
				}
			});

		$form->text('city', 'City')
			->attribute('tabindex', '5')
			->attribute('maxlength', '128')
			->rule('maxLength', 128);



		$form->fieldset('fields3', false, array('class' => ''));
		$form->html('industry_0', '<div></div>')->visible(false);
		$this->generateIndustries();


		$form->fieldset('fields4', false, array('class' => 'submit'));
		$form->html('addindustry', '<a class="btn-roundblue-border icons i-addcustom " href="' . Request::generateUri('jobs', 'addSearchIndustry') . '" onclick="box.load(this); return false;"><span></span>add</a>');


		$form->fieldset('fields5', false, array('class' => ''));
		$form->html('industry_0', '<div></div>')->visible(false);
		$this->generateSkills();

		$form->fieldset('fields6', false, array('class' => 'submit'));
		$form->html('addskill', '<a class="btn-roundblue-border icons i-addcustom " href="' . Request::generateUri('jobs', 'addSearchSkill') . '" onclick="box.load(this); return false;"><span></span>add</a>');





//		$form->fieldset('submit', false, array('class' => 'submit'));
//		$form->html('search', '<a class="icons i-search icon-round-max" href="#" onclick="$(this).closest(\'form\').find(\'input:submit\').click(); return false;"><span></span></a>');
//		$form->submit('submit', 'Submit')
//			->visible(false);




		return $this;
	}

	public function generateIndustries()
	{
		$form =& $this->form;
		$form->fieldset('fields3', false, array('class' => ''));

		$industries_name = t('industries');
		if(isset($_COOKIE['search_jobs_industries'])) {
			$industries = explode('_', $_COOKIE['search_jobs_industries']);
		} else {
			$industries = array();
		}

		$checked_industry = array();
		if(isset($_GET['industry'])) {
			$checked_industry = explode(',', $_GET['industry']);
		}

		$i = 0;
		foreach($industries as $industry) {
			if(empty($industry)) continue;
			$i ++;

			$is_checked = FALSE;
			if(in_array($industry, $checked_industry)) {
				$is_checked = TRUE;
			}
			$form->html('industry_' . $industry, '
				<div class="checkbox-control-select1" data-id="' . $industry  . '" data-key="industry" data-ischecked="' . $is_checked  . '"></div>
				<label for="checkboxControl-element_1_' . $i . '">' . $industries_name[$industry] . '</label>
				<a class="icons i-deleteround" href="' . Request::generateUri('jobs', 'removeSearchIndustry', $industry) . '" onclick="return web.ajaxGet(this);" title="Delete industry">
					<span></span>
				</a>');

//			$form->checkbox('industry_' . $industry, false, $industries_name[$industry])
//				->attribute('class', 'form-checkbox');
		}
	}


	public function generateSkills()
	{
		$form =& $this->form;
		$form->fieldset('fields5', false, array('class' => ''));

		$getSkills = Model_Skills::getListAll();
		$skills_name = array();
		foreach($getSkills['data'] as $skill) {
			$skills_name[$skill->id] = $skill->name;
		}
		unset($getSkills);


		if(isset($_COOKIE['search_jobs_skills'])) {
			$skills = explode('_', $_COOKIE['search_jobs_skills']);
		} else {
			$skills = array();
		}

		$checked_skill = array();
		if(isset($_GET['skill'])) {
			$checked_skill = explode(',', $_GET['skill']);
		}

		$i = 0;
		foreach($skills as $skill) {
			if(empty($skill)) continue;
			$i ++;

			$is_checked = FALSE;
			if(in_array($skill, $checked_skill)) {
				$is_checked = TRUE;
			}
			$form->html('skills_' . $skill, '<div class="checkbox-control-select2" data-id="' . $skill  . '"  data-key="skill" data-ischecked="' . $is_checked . '"></div><label for="checkboxControl-element_2_' . $i . '">' . $skills_name[$skill] . '</label>
			<a class="icons i-deleteround" href="' . Request::generateUri('jobs', 'removeSearchSkill', $skill) . '" onclick="return web.ajaxGet(this);" title="Delete skill"><span></span></a>');

//			$form->checkbox('industry_' . $industry, false, $industries_name[$industry])
//				->attribute('class', 'form-checkbox');
		}
	}
}