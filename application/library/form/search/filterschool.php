<?php

	class Form_Search_FilterSchool extends Form_Main
	{
		protected $i = 5;

		public function __construct($searchText = '')
		{
			$form = new Form('filterschool', FALSE, Request::generateUri('search', 'school') . Request::getQuery());
			$form->attribute('method', 'GET');

			$form->hidden('searchschool', $searchText);

			$form->fieldset('submit', FALSE, array('class' => ''));
			$form->html('save', '<a class="icons i-search" href="#" onclick="$(this).closest(\'form\').find(\'input:submit\').click(); return false;">Search now<span></span></a>');
			$form->submit('submit', 'Submit')
				->visible(FALSE);

			$this->form = $form;

			return $this;
		}

		public function generateType($access, $get = FALSE)
		{
			if ($get) {
				$get = explode(',', $get);
			}
			$form =& $this->form;

			$form->fieldset('type', FALSE, array('class' => 'modernform-panel'));
			foreach ($access as $key => $access_name) {
				$form->checkbox('type_' . $this->i, FALSE, t('school_type.' . $key))
					->attribute('tabindex', $this->i)
					->attribute('data-key', 'type')
					->attribute('data-value', $key)
					->attribute('class', 'form-checkbox')
					->attribute('onchange', 'web.searchSchoolSubmit(this);');

				if ($get && in_array($key, $get)) {
					$form->elements['type_' . $this->i]->setValue(TRUE);
				}
				$this->i++;
			}
		}

		public function inSearchAll()
		{
			$form =& $this->form;

			$form->attribute('action', Request::generateUri('search', 'index') . Request::getQuery());
		}
	}