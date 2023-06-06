<?php

class Form_FindPanel extends Form_Main
{
	public function __construct()
	{
		$form = new Form('findpanel', false, Request::generateUri('search', 'index') . Request::getQuery());
		$form->attribute('onsubmit', 'return web.submitFindPanel(this);');

		$form->fieldset('fields', false, array('class' => 'modernform-panel'));

		$type = t('find_type');
		$form->select('type', array('' => '') + $type)
			->attribute('class', 'bootstripe')
			->attribute('onchange', 'web.changeFind(this)');

		$form->text('searchall', false)
			->attribute('placeholder', 'SEARCH PEOPLE, COMPANIES, GROUPS, SCHOOLS')
			->attribute('data-action', Request::generateUri('search', 'index'));
//			->before(function ($field) {
//				if(empty($field->fieldset->elements['find_all']->value) && empty($field->fieldset->elements['find_people']->value) &&
//					empty($field->fieldset->elements['find_companies']->value) && empty($field->fieldset->elements['searchgroups']->value) &&
//					empty($field->fieldset->elements['find_universities']->value))
//				{
//					$field->required();
//					$field->attribute('required', 'required');
//				}
//			});

		$form->text('searchpeople', false)
			->attribute('placeholder', 'SEARCH PEOPLE')
			->attribute('data-action', Request::generateUri('search', 'people'))
			->visible(false);

		$form->text('searchcompany', false)
			->attribute('placeholder', 'SEARCH COMPANIES')
			->attribute('data-action', Request::generateUri('search', 'company'))
			->visible(false);

		$form->text('searchgroup', false)
			->attribute('placeholder', 'SEARCH GROUPS')
			->attribute('data-action', Request::generateUri('search', 'group'))
			->visible(false);

		$form->text('searchschool', false)
			->attribute('placeholder', 'SEARCH SCHOOLS')
			->attribute('data-action', Request::generateUri('search', 'school'))
			->visible(false);

		$form->text('searchjob', false)
			->attribute('placeholder', 'SEARCH JOBS')
			->attribute('data-action', Request::generateUri('search', 'job'))
			->visible(false);

		$form->fieldset('submit', false, array('class' => ''));
		$form->html('save', '<a class="btn-roundblue borderradius_2" href="#" onclick="$(this).closest(\'form\').find(\'input:submit\').click(); return false;">Search now</a>');
		$form->submit('submit', 'Submit')
			->visible(false);

		$this->form = $form;

		return $this;
	}

	public function setFindType($type)
	{
		$form =& $this->form;
		$form->elements['searchall']->visible(false);
		$form->elements['searchpeople']->visible(false);
		$form->elements['searchcompany']->visible(false);
		$form->elements['searchgroup']->visible(false);
		$form->elements['searchschool']->visible(false);
		$form->elements['searchjob']->visible(false);


		if(isset($_GET['searchpeople'])) {
			$value = $_GET['searchpeople'];
		}
		if(isset($_GET['searchcompany'])) {
			$value = $_GET['searchcompany'];
		}
		if(isset($_GET['searchschool'])) {
			$value = $_GET['searchschool'];
		}
		if(isset($_GET['searchgroup'])) {
			$value = $_GET['searchgroup'];
		}
		if(isset($_GET['searchjob'])) {
			$value = $_GET['searchjob'];
			if(is_array($value)) {
				$value = $value['search'];
				$type = 'job';
			}
		}
		if(isset($_GET['searchall'])) {
			$value = $_GET['searchall'];
		}

		unset($_GET['searchpeople'], $_GET['searchcompany'], $_GET['searchschool'], $_GET['searchgroup'], $_GET['searchjob'], $_GET['searchall']);
		$form->attributes['action'] = Request::generateUri('search', $type) . Request::getQuery();

		switch($type){
			case 'people':
				$form->elements['searchpeople']->visible(true);
				if(!empty($value)){
					$form->elements['searchpeople']->setValue($value);
				}
				$form->elements['type']->setValue(2);
				break;

			case 'company':
				$form->elements['searchcompany']->visible(true);
				if(!empty($value)){
					$form->elements['searchcompany']->setValue($value);
				}
				$form->elements['type']->setValue(3);
				break;

			case 'group':
				$form->elements['searchgroup']->visible(true);
				if(!empty($value)){
					$form->elements['searchgroup']->setValue($value);
				}
				$form->elements['type']->setValue(4);
				break;

			case 'school':
				$form->elements['searchschool']->visible(true);
				if(!empty($value)){
					$form->elements['searchschool']->setValue($value);
				}
				$form->elements['type']->setValue(5);
				break;

			case 'job':
				$form->elements['searchjob']->visible(true);
				if(!empty($value)){
					$form->elements['searchjob']->setValue($value);
				}
				$form->elements['type']->setValue(5);
				break;

			default:

				$form->elements['searchall']->visible(true);
				if(!empty($value)){
					$form->elements['searchall']->setValue($value);
				}
				$form->elements['type']->setValue(1);
				$form->attribute('action', Request::generateUri('search', 'index') . Request::getQuery());
		}
	}
}