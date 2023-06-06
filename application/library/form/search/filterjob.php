<?php

	class Form_Search_FilterJob extends Form_Main
	{
		protected $i = 5;

		public function __construct($searchText = '')
		{
			$form = new Form('filterjob', FALSE, Request::generateUri('search', 'job') . Request::getQuery());
			$form->attribute('method', 'GET');

			$form->hidden('searchjob', $searchText);

			$form->fieldset('submit', FALSE, array('class' => ''));
			$form->html('save', '<a class="icons i-search" href="#" onclick="$(this).closest(\'form\').find(\'input:submit\').click(); return false;">Search now<span></span></a>');
			$form->submit('submit', 'Submit')
				->visible(FALSE);

			$this->form = $form;

			return $this;
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
					->attribute('onchange', 'web.searchJobSubmit(this);');

				if ($get && in_array($industry->userIndustry, $get)) {
					$form->elements['industry_' . $this->i]->setValue(TRUE);
				}
				$this->i++;
			}

			if (isset($_SESSION['search']['job']['industry'])) {
				foreach ($_SESSION['search']['job']['industry'] as $key => $name) {
					$form->checkbox('industry_' . $this->i, FALSE, $name)
						->attribute('tabindex', $this->i)
						->attribute('data-key', 'industry')
						->attribute('data-value', $key)
						->attribute('class', 'form-checkbox')
						->attribute('onchange', 'web.searchJobSubmit(this);');

					if ($get && in_array($key, $get)) {
						$form->elements['industry_' . $this->i]->setValue(TRUE);
					}
					$this->i++;
				}
			}

			$url = Request::generateUri('search', 'addSearchJobIndustry');

			$form->html('industry_add', FALSE, '<a class="btn-roundblue-border icons i-addcustom icon-text" href="' . $url . '"  onclick="box.load(this); return false;"><span></span>Add</a>')
				->attribute('class', 'form-html');
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
					->attribute('onchange', 'web.searchJobSubmit(this);');

				if ($get && in_array($region->userCountry, $get)) {
					$form->elements['region_' . $this->i]->setValue(TRUE);
				}
				$this->i++;
			}

			if (isset($_SESSION['search']['job']['region'])) {
				foreach ($_SESSION['search']['job']['region'] as $key => $name) {
					$form->checkbox('region_' . $this->i, FALSE, $name)
						->attribute('tabindex', $this->i)
						->attribute('data-key', 'region')
						->attribute('data-value', $key)
						->attribute('class', 'form-checkbox')
						->attribute('onchange', 'web.searchJobSubmit(this);');

					if ($get && in_array($key, $get)) {
						$form->elements['region_' . $this->i]->setValue(TRUE);
					}
					$this->i++;
				}
			}

//			if($this->isNoRegistredUsers) {
//				$url = Request::generateUri('index', 'addSearchJobRegion');
//			} else {
				$url = Request::generateUri('search', 'addSearchJobRegion');
//			}

			$form->html('region_add', FALSE, '<a class="btn-roundblue-border icons i-addcustom icon-text"  href="' . $url . '"  onclick="box.load(this); return false;"><span></span>Add</a>')
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
					->attribute('onchange', 'web.searchJobSubmit(this);');

				if ($get && in_array($skill->id, $get)) {
					$form->elements['skill_' . $this->i]->setValue(TRUE);
				}
				$this->i++;
			}

			if (isset($_SESSION['search']['job']['skill'])) {
				foreach ($_SESSION['search']['job']['skill'] as $key => $name) {
					$form->checkbox('skill_' . $this->i, FALSE, $name)
						->attribute('tabindex', $this->i)
						->attribute('data-key', 'skill')
						->attribute('data-value', $key)
						->attribute('class', 'form-checkbox')
						->attribute('onchange', 'web.searchJobSubmit(this);');

					if ($get && in_array($key, $get)) {
						$form->elements['skill_' . $this->i]->setValue(TRUE);
					}
					$this->i++;
				}
			}

			$url = Request::generateUri('search', 'addSearchJobSkill');

			$form->html('skill_add', FALSE, '<a class="btn-roundblue-border icons i-addcustom icon-text"  href="' . $url . '"  onclick="box.load(this); return false;"><span></span>Add</a>')
				->attribute('class', 'form-html');
		}


		public function inSearchAll()
		{
			$form =& $this->form;

			$form->attribute('action', Request::generateUri('search', 'index') . Request::getQuery());
		}
	}