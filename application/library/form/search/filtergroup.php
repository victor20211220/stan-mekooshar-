<?php

	class Form_Search_FilterGroup extends Form_Main
	{
		protected $i = 5;

		public function __construct($searchText = '')
		{
			$form = new Form('filtergroup', FALSE, Request::generateUri('search', 'group') . Request::getQuery());
			$form->attribute('method', 'GET');

			$form->hidden('searchgroup', $searchText);

			$form->fieldset('submit', FALSE, array('class' => ''));
			$form->html('save', '<a class="icons i-search" href="#" onclick="$(this).closest(\'form\').find(\'input:submit\').click(); return false;">Search now<span></span></a>');
			$form->submit('submit', 'Submit')
				->visible(FALSE);

			$this->form = $form;

			return $this;
		}

		public function generateAccess($access, $get = FALSE)
		{
			if ($get) {
				$get = explode(',', $get);
			}
			$form =& $this->form;

			$form->fieldset('access', FALSE, array('class' => 'modernform-panel'));
			foreach ($access as $key => $access_name) {
				$form->checkbox('access_' . $this->i, FALSE, t('group_access_search_filter.' . $key))
					->attribute('tabindex', $this->i)
					->attribute('data-key', 'access')
					->attribute('data-value', $key)
					->attribute('class', 'form-checkbox')
					->attribute('onchange', 'web.searchGroupSubmit(this);');

				if ($get && in_array($key, $get)) {
					$form->elements['access_' . $this->i]->setValue(TRUE);
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