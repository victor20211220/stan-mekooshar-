<?php

	class Form_Search_FilterPeople extends Form_Main
	{
		protected $i = 5;
		protected $isNoRegistredUsers = FALSE;

		public function __construct($searchText = '')
		{
			$form = new Form('filterpeople', FALSE, Request::generateUri('search', 'people') . Request::getQuery());
			$form->attribute('method', 'GET');

			$form->hidden('searchpeople', $searchText);

			$form->fieldset('submit', FALSE, array('class' => ''));
			$form->html('save', '<a class="icons i-search" href="#" onclick="$(this).closest(\'form\').find(\'input:submit\').click(); return false;">Search now<span></span></a>');
			$form->submit('submit', 'Submit')
				->visible(FALSE);

			$this->form = $form;

			return $this;
		}

		public function generateConnection($get = FALSE)
		{
			if ($get) {
				$get = explode(',', $get);
			}
			$form =& $this->form;

			$form->fieldset('connections', FALSE, array('class' => 'modernform-panel'));

			$form->checkbox('connection_1', FALSE, '1st connections', ($get && in_array(1, $get)) ? TRUE : FALSE)
				->attribute('tabindex', '1')
				->attribute('data-key', 'connection')
				->attribute('data-value', '1')
				->attribute('class', 'form-checkbox')
				->attribute('onchange', 'web.searchSubmit(this);');

			$form->checkbox('connection_2', FALSE, '2nd connections', ($get && in_array(2, $get)) ? TRUE : FALSE)
				->attribute('tabindex', '2')
				->attribute('data-key', 'connection')
				->attribute('data-value', '2')
				->attribute('class', 'form-checkbox')
				->attribute('onchange', 'web.searchSubmit(this);');

			$form->checkbox('connection_3', FALSE, '3rd connections', ($get && in_array(3, $get)) ? TRUE : FALSE)
				->attribute('tabindex', '3')
				->attribute('data-key', 'connection')
				->attribute('data-value', '3')
				->attribute('class', 'form-checkbox')
				->attribute('onchange', 'web.searchSubmit(this);');

			$form->checkbox('connection_4', FALSE, '+3rd connections', ($get && in_array(4, $get)) ? TRUE : FALSE)
				->attribute('tabindex', '4')
				->attribute('data-key', 'connection')
				->attribute('data-value', '4')
				->attribute('class', 'form-checkbox')
				->attribute('onchange', 'web.searchSubmit(this);');
		}

		public function generateRegion($regions, $get = FALSE)
		{
			if ($get) {
				$get = explode(',', $get);
			}
			$form =& $this->form;
			$form->fieldset('region', FALSE, array('class' => 'modernform-panel'));
			foreach ($regions['data'] as $region) {
				$form->checkbox('region_' . $this->i, FALSE, t('countries.' . $region->userCountry))
					->attribute('tabindex', $this->i)
					->attribute('data-key', 'region')
					->attribute('data-value', $region->userCountry)
					->attribute('class', 'form-checkbox')
					->attribute('onchange', 'web.searchSubmit(this);');

				if ($get && in_array($region->userCountry, $get)) {
					$form->elements['region_' . $this->i]->setValue(TRUE);
				}
				$this->i++;
			}

			if (isset($_SESSION['search']['people']['region'])) {
				foreach ($_SESSION['search']['people']['region'] as $key => $name) {
					$form->checkbox('region_' . $this->i, FALSE, $name)
						->attribute('tabindex', $this->i)
						->attribute('data-key', 'region')
						->attribute('data-value', $key)
						->attribute('class', 'form-checkbox')
						->attribute('onchange', 'web.searchSubmit(this);');

					if ($get && in_array($key, $get)) {
						$form->elements['region_' . $this->i]->setValue(TRUE);
					}
					$this->i++;
				}
			}

			if($this->isNoRegistredUsers) {
				$url = Request::generateUri('index', 'addSearchRegion');
			} else {
				$url = Request::generateUri('search', 'addSearchRegion');
			}

			$form->html('region_add', FALSE, '<a class="btn-roundblue-border icons i-addcustom icon-text"  href="' . $url . '"  onclick="box.load(this); return false;"><span></span>Add</a>')
				->attribute('class', 'form-html');
		}

		public function generateCompany($companies, $get = FALSE)
		{
			if ($get) {
				$get = explode(',', $get);
			}
			$form =& $this->form;
			$form->fieldset('company', FALSE, array('class' => 'modernform-panel'));
			foreach ($companies['data'] as $company) {
				$value = (!empty($company->companyName)) ? 'c' . $company->companyId : 'u' . $company->universityId;
				$form->checkbox('company_' . $this->i, FALSE, $company->companyUniversityName)
					->attribute('tabindex', $this->i)
					->attribute('data-key', 'company')
					->attribute('data-value', $value)
					->attribute('class', 'form-checkbox')
					->attribute('onchange', 'web.searchSubmit(this);');

				if ($get && in_array($value, $get)) {
					$form->elements['company_' . $this->i]->setValue(TRUE);
				}
				$this->i++;
			}

			if (isset($_SESSION['search']['people']['company'])) {
				foreach ($_SESSION['search']['people']['company'] as $key => $name) {
					$form->checkbox('company_' . $this->i, FALSE, $name)
						->attribute('tabindex', $this->i)
						->attribute('data-key', 'company')
						->attribute('data-value', $key)
						->attribute('class', 'form-checkbox')
						->attribute('onchange', 'web.searchSubmit(this);');

					if ($get && in_array($key, $get)) {
						$form->elements['company_' . $this->i]->setValue(TRUE);
					}
					$this->i++;
				}
			}

			if($this->isNoRegistredUsers) {
				$url = Request::generateUri('index', 'addSearchCompany');
			} else {
				$url = Request::generateUri('search', 'addSearchCompany');
			}

			$form->html('company_add', FALSE, '<a class="btn-roundblue-border icons i-addcustom icon-text"  href="' . $url . '"  onclick="box.load(this); return false;"><span></span>Add</a>')
				->attribute('class', 'form-html');
		}

		public function generateIndustry($industries, $get = FALSE)
		{
			if ($get) {
				$get = explode(',', $get);
			}
			$form =& $this->form;

			$form->fieldset('industry', FALSE, array('class' => 'modernform-panel'));
			foreach ($industries['data'] as $industry) {
				$form->checkbox('industry_' . $this->i, FALSE, t('industries.' . $industry->userIndustry))
					->attribute('tabindex', $this->i)
					->attribute('data-key', 'industry')
					->attribute('data-value', $industry->userIndustry)
					->attribute('class', 'form-checkbox')
					->attribute('onchange', 'web.searchSubmit(this);');

				if ($get && in_array($industry->userIndustry, $get)) {
					$form->elements['industry_' . $this->i]->setValue(TRUE);
				}
				$this->i++;
			}

			if (isset($_SESSION['search']['people']['industry'])) {
				foreach ($_SESSION['search']['people']['industry'] as $key => $name) {
					$form->checkbox('industry_' . $this->i, FALSE, $name)
						->attribute('tabindex', $this->i)
						->attribute('data-key', 'industry')
						->attribute('data-value', $key)
						->attribute('class', 'form-checkbox')
						->attribute('onchange', 'web.searchSubmit(this);');

					if ($get && in_array($key, $get)) {
						$form->elements['industry_' . $this->i]->setValue(TRUE);
					}
					$this->i++;
				}
			}

			if($this->isNoRegistredUsers) {
				$url = Request::generateUri('index', 'addSearchIndustry');
			} else {
				$url = Request::generateUri('search', 'addSearchIndustry');
			}

			$form->html('industry_add', FALSE, '<a class="btn-roundblue-border icons i-addcustom icon-text" href="' . $url . '"  onclick="box.load(this); return false;"><span></span>Add</a>')
				->attribute('class', 'form-html');
		}

		public function generateSchool($universities, $get = FALSE)
		{
			if ($get) {
				$get = explode(',', $get);
			}
			$form =& $this->form;

			$form->fieldset('school', FALSE, array('class' => 'modernform-panel'));
			foreach ($universities['data'] as $university) {
				$form->checkbox('school_' . $this->i, FALSE, $university->universityName)
					->attribute('tabindex', $this->i)
					->attribute('data-key', 'school')
					->attribute('data-value', $university->universityId)
					->attribute('class', 'form-checkbox')
					->attribute('onchange', 'web.searchSubmit(this);');

				if ($get && in_array($university->universityId, $get)) {
					$form->elements['school_' . $this->i]->setValue(TRUE);
				}
				$this->i++;
			}

			if (isset($_SESSION['search']['people']['school'])) {
				foreach ($_SESSION['search']['people']['school'] as $key => $name) {
					$form->checkbox('school_' . $this->i, FALSE, $name)
						->attribute('tabindex', $this->i)
						->attribute('data-key', 'school')
						->attribute('data-value', $key)
						->attribute('class', 'form-checkbox')
						->attribute('onchange', 'web.searchSubmit(this);');

					if ($get && in_array($key, $get)) {
						$form->elements['school_' . $this->i]->setValue(TRUE);
					}
					$this->i++;
				}
			}

			if($this->isNoRegistredUsers) {
				$url = Request::generateUri('index', 'addSearchSchool');
			} else {
				$url = Request::generateUri('search', 'addSearchSchool');
			}

			$form->html('school_add', FALSE, '<a class="btn-roundblue-border icons i-addcustom icon-text" href="' . $url . '"  onclick="box.load(this); return false;"><span></span>Add</a>')
				->attribute('class', 'form-html');
		}


		public function generateSkills($skills, $get = FALSE)
		{
			if ($get) {
				$get = explode(',', $get);
			}
			$form =& $this->form;
			$form->fieldset('skill', FALSE, array('class' => 'modernform-panel'));
			foreach ($skills['data'] as $skill) {
				$form->checkbox('skill_' . $this->i, FALSE, $skill->name)
					->attribute('tabindex', $this->i)
					->attribute('data-key', 'skill')
					->attribute('data-value', $skill->id)
					->attribute('class', 'form-checkbox')
					->attribute('onchange', 'web.searchSubmit(this);');

				if ($get && in_array($skill->id, $get)) {
					$form->elements['skill_' . $this->i]->setValue(TRUE);
				}
				$this->i++;
			}

			if (isset($_SESSION['search']['people']['skill'])) {
				foreach ($_SESSION['search']['people']['skill'] as $key => $name) {
					$form->checkbox('skill_' . $this->i, FALSE, $name)
						->attribute('tabindex', $this->i)
						->attribute('data-key', 'skill')
						->attribute('data-value', $key)
						->attribute('class', 'form-checkbox')
						->attribute('onchange', 'web.searchSubmit(this);');

					if ($get && in_array($key, $get)) {
						$form->elements['skill_' . $this->i]->setValue(TRUE);
					}
					$this->i++;
				}
			}

			$url = Request::generateUri('search', 'addSearchPeopleSkill');

			$form->html('skill_add', FALSE, '<a class="btn-roundblue-border icons i-addcustom icon-text"  href="' . $url . '"  onclick="box.load(this); return false;"><span></span>Add</a>')
				->attribute('class', 'form-html');
		}

		public function inSearchAll()
		{
			$form =& $this->form;

			$form->attribute('action', Request::generateUri('search', 'index') . Request::getQuery());
		}

		public function setForNoRegistredUser()
		{
			$this->isNoRegistredUsers = TRUE;
			$this->form->attribute('action', Request::generateUri('SearchPeople', 'index'));
		}
	}