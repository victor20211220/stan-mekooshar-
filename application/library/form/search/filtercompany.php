<?php

	class Form_Search_FilterCompany extends Form_Main
	{
		protected $i = 5;

		public function __construct($searchText = '')
		{
			$form = new Form('filtercompany', FALSE, Request::generateUri('search', 'company') . Request::getQuery());
			$form->attribute('method', 'GET');

			$form->hidden('searchcompany', $searchText);

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
				$form->checkbox('industry_' . $this->i, FALSE, t('industries.' . $industry->companyIndustry))
					->attribute('tabindex', $this->i)
					->attribute('data-key', 'industry')
					->attribute('data-value', $industry->companyIndustry)
					->attribute('class', 'form-checkbox')
					->attribute('onchange', 'web.searchCompanySubmit(this);');

				if ($get && in_array($industry->companyIndustry, $get)) {
					$form->elements['industry_' . $this->i]->setValue(TRUE);
				}
				$this->i++;
			}

			if (isset($_SESSION['search']['company']['industry'])) {
				foreach ($_SESSION['search']['company']['industry'] as $key => $name) {
					$form->checkbox('industry_' . $this->i, FALSE, $name)
						->attribute('tabindex', $this->i)
						->attribute('data-key', 'industry')
						->attribute('data-value', $key)
						->attribute('class', 'form-checkbox')
						->attribute('onchange', 'web.searchCompanySubmit(this);');

					if ($get && in_array($key, $get)) {
						$form->elements['industry_' . $this->i]->setValue(TRUE);
					}
					$this->i++;
				}
			}

			$form->html('industry_add', FALSE, '<a class="btn-roundblue-border icons i-addcustom icon-text" href="' . Request::generateUri('search', 'addSearchCompanyIndustry') . '"  onclick="box.load(this); return false;"><span></span>Add</a>')
				->attribute('class', 'form-html');
		}

		public function generateType($types, $get = FALSE)
		{
			if ($get) {
				$get = explode(',', $get);
			}
			$form =& $this->form;

			$form->fieldset('type', FALSE, array('class' => 'modernform-panel'));
			foreach ($types['data'] as $type) {
				$form->checkbox('type_' . $this->i, FALSE, t('company_type.' . $type->companyType))
					->attribute('tabindex', $this->i)
					->attribute('data-key', 'type')
					->attribute('data-value', $type->companyType)
					->attribute('class', 'form-checkbox')
					->attribute('onchange', 'web.searchCompanySubmit(this);');

				if ($get && in_array($type->companyType, $get)) {
					$form->elements['type_' . $this->i]->setValue(TRUE);
				}
				$this->i++;
			}

			if (isset($_SESSION['search']['company']['type'])) {
				foreach ($_SESSION['search']['company']['type'] as $key => $name) {
					$form->checkbox('type_' . $this->i, FALSE, $name)
						->attribute('tabindex', $this->i)
						->attribute('data-key', 'type')
						->attribute('data-value', $key)
						->attribute('class', 'form-checkbox')
						->attribute('onchange', 'web.searchCompanySubmit(this);');

					if ($get && in_array($key, $get)) {
						$form->elements['type_' . $this->i]->setValue(TRUE);
					}
					$this->i++;
				}
			}

			$form->html('type_add', FALSE, '<a class="btn-roundblue-border icons i-addcustom icon-text"  href="' . Request::generateUri('search', 'addSearchCompanyType') . '"  onclick="box.load(this); return false;"><span></span>Add</a>')
				->attribute('class', 'form-html');
		}

		public function generateEmployer($employers, $get = FALSE)
		{
			if ($get) {
				$get = explode(',', $get);
			}
			$form =& $this->form;

			$form->fieldset('employer', FALSE, array('class' => 'modernform-panel'));
			foreach ($employers['data'] as $employer) {
				$form->checkbox('employer_' . $this->i, FALSE, t('company_number_of_employer.' . $employer->companySize))
					->attribute('tabindex', $this->i)
					->attribute('data-key', 'employer')
					->attribute('data-value', $employer->companySize)
					->attribute('class', 'form-checkbox')
					->attribute('onchange', 'web.searchCompanySubmit(this);');

				if ($get && in_array($employer->companySize, $get)) {
					$form->elements['employer_' . $this->i]->setValue(TRUE);
				}
				$this->i++;
			}

			if (isset($_SESSION['search']['company']['employer'])) {
				foreach ($_SESSION['search']['company']['employer'] as $key => $name) {
					$form->checkbox('employer_' . $this->i, FALSE, $name)
						->attribute('tabindex', $this->i)
						->attribute('data-key', 'employer')
						->attribute('data-value', $key)
						->attribute('class', 'form-checkbox')
						->attribute('onchange', 'web.searchCompanySubmit(this);');

					if ($get && in_array($key, $get)) {
						$form->elements['employer_' . $this->i]->setValue(TRUE);
					}
					$this->i++;
				}
			}

			$form->html('employer_add', FALSE, '<a class="btn-roundblue-border icons i-addcustom icon-text" href="' . Request::generateUri('search', 'addSearchCompanyEmployer') . '"  onclick="box.load(this); return false;"><span></span>Add</a>')
				->attribute('class', 'form-html');
		}

		public function inSearchAll()
		{
			$form =& $this->form;

			$form->attribute('action', Request::generateUri('search', 'index') . Request::getQuery());
		}

	}