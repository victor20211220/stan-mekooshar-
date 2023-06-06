<?php

class Form_Profile_EditSkills extends Form_Main
{
	protected $defaultSkills = array();
	protected $listSkills = '';
	protected $listNextBnt = '';
	protected $skills = array();

	public function __construct($items = false)
	{
		// Create form
		$form = new Form('editskills', false, Request::generateUri('profile', 'editSkills'));
		$form->attribute('onsubmit', "return box.submit(this);");


		// Generate custom list
		$this->defaultSkills = Model_Skills::getList_OrderCountUsed();

		$tmp = array();
		foreach($items['data'] as $id => $item) {
			$tmp[]['id'] = $item->skill_id;
		}
		$items['data'] = $tmp;


		//Get isset skills
		if(Request::isPost()) {
			$items['data'] = $this->getPost($_POST[$form->attributes['id']]);
		}

		$ids = array();
		foreach($items['data'] as $item) {
			$ids[] = $item['id'];
		}
		if(!empty($ids)) {
			$other_skills = Model_Skills::getListByIds($ids);

			foreach($other_skills['data'] as $id => $item) {
				$this->defaultSkills['data'][$id] = $item;
			}
		}



		foreach($this->defaultSkills['data'] as $skill) {
			$this->skills[$skill->id] = $skill->name;
		}

		// Generate list
		// --------------------------
		$text = '';
		foreach($this->defaultSkills['data'] as $id=>$skill) {
			$this->listSkills .= '<li data-itemid="' . $id . '" data-itemtitle="' . Html::chars($skill->name) . '" data-itemorder="' . $skill->countUsed . '">' . Html::chars($skill->name) . '</li>';
		}
		$this->listSkills = '<ul class="selectize-customitems">' . $this->listSkills .  '</ul>';

		if(!empty($this->defaultSkills['paginator']['next'])) {
			$this->listNextBnt = Request::generateUri('autoComplete', 'getListSkills') . Request::getQuery('page', '2');
		} else {
			$this->listNextBnt = '';
		}
		// --------------------------







		// Create other elements form
		$form->html('list', false, $this->listSkills)
			->visible(false)
			->attribute('class', 'form-html');



		$form->fieldset('fields', false, array('class' => 'customform on-white customform-label'));

		$this->form = $form;
		$this->generateFiledNew($items);


		$form->fieldset('submit', false, array('class' => 'submit'));
		$form->html('cancel', '<a class="btn-roundbrown" href="' . Request::generateUri('profile', 'edit') . '" onclick="return web.cancelEditBlock(this);">Cancel</a>');
		$form->html('save', '<a class="btn-roundblue" href="#" onclick="$(this).closest(\'form\').find(\'input:submit\').click(); return false;">Save changes</a>');
		$form->submit('submit', 'Submit')
			->visible(false);

		$this->form = $form;

		return $this;
	}
//
//	public function generateFiled($items)
//	{
//		$form =& $this->form;
//		$form->fieldset('fields');
//
//		if(Request::isPost()) {
//			$items['data'] = $this->getPost($_POST[$this->form->attributes['id']]);
//		}
//
//		$i = 0;
//
//		foreach($items['data'] as $item) {
//			$i++;
//			$form->fieldset('fields_' . $i, false, array('class' => 'customform on-white customform-label'));
//			$form->text('name_' . $i, 'Skill name', isset($item->skillName) ? $item->skillName : $item['name'])
////				->attribute('placeholder', 'Skills name')
//				->attribute('tabindex', $i)
//				->attribute('class', 'isMiltiList')
//				->attribute('data-id', $i)
//				->attribute('onkeyup', 'web.multiList(this);')
//				->attribute('maxlength', '128')
//				->rule('maxLength', 128);
//
//			$form->html('count_' . $i, false, '<div class="count_endorsement">' . ((isset($item->skillName)) ? $item->skillEndorsement : '') . '</div>')
//				->attribute('class', 'form-html');
//
//			$form->html('delete_' . $i, false, '<a href="#" class="btn-roundblue-border icons i-deletecustom icon-text 	 deleteMiltiList" onclick="return web.deleteListFromMultilist(this);"><span></span></a>')
//				->attribute('class', 'form-html');
//		}
//		$i++;
//
//		$form->fieldset('fields_' . $i, false, array('class' => 'customform on-white customform-label'));
//		$form->text('name_' . $i, 'Skill name')
////			->attribute('placeholder', 'Skills name')
//			->attribute('tabindex', $i)
//			->attribute('class', 'isMiltiList')
//			->attribute('data-id', $i)
//			->attribute('onkeyup', 'web.multiList(this);')
//			->attribute('maxlength', '128')
//			->rule('maxLength', 128);
//
//		$form->html('count_' . $i, false, '<div class="count_endorsement"></div>')
//			->attribute('class', 'form-html');
//
//		$form->html('delete_' . $i, false, '<a href="#" class="btn-roundblue-border icons i-deletecustom icon-text  deleteMiltiList" onclick="return web.deleteListFromMultilist(this);"><span></span></a>')
//			->attribute('class', 'form-html');
//
//		$form->attribute('data-last_id', $i);
//
//		$this->generateTemplate();
//
//	}
//
//	public function generateTemplate()
//	{
//		$form =& $this->form;
//		$form->fieldset('fields_%i', false, array('class' => 'customform on-white customform-label form-template'));
//
//		$form->text('name_%i', 'Skill name')
//			->attribute('data-id', '%i')
//			->attribute('class', 'isMiltiList')
//			->attribute('onkeyup', 'web.multiList(this);')
//			->attribute('maxlength', '128')
//			->rule('maxLength', 128);
//
//		$form->html('count_%i', false, '<div class="count_endorsement"></div>')
//			->attribute('class', 'form-html');
//
//		$form->html('delete_%i', false, '<a href="#" class="btn-roundblue-border icons i-deletecustom icon-text  deleteMiltiList" onclick="return web.deleteListFromMultilist(this);"><span></span></a>')
//			->attribute('class', 'form-html');
//	}

	public function getPost($values)
	{
		unset($values['name_%i']);

		$skills = array();
		foreach($values as $key => $value) {
			if(substr($key, 0, 4) == 'name') {
				if(substr($value, 0, 4) == 'new%') {
					$name = substr($value, 4);

					$check = Model_Skills::checkItemByName($name);
					if(!$check){
						$element = Model_Skills::create(array(
							'name' => $name
						));
						$id = $element->id;
					} else {
						$id = $check->id;
					}
					$skills[substr($key, 5)] = $id;
				} else {
					$skills[substr($key, 5)] = $value;
				}
			}
		}

		$skills_array = array();
		foreach($skills as $key => $skill) {
			if(empty($skill)) {
				continue;
			}
			$skills_array[$key] = array(
				'id' => $skill,
			);
		}
		return $skills_array;
	}





	public function generateFiledNew($items)
	{
		$form =& $this->form;
		$form->fieldset('fields');




		$i = 0;
//		dump($items, 1);
		foreach($items['data'] as $item) {
			$i++;
			$form->fieldset('fields_' . $i, false, array('class' => 'customform on-white customform-label'));
			$form->select('name_' . $i, array('' => 'Please select skill') + $this->skills, 'Skills', isset($item->skill_id) ? $item->skill_id : $item['id'])
				->attribute('class', 'selectize isMiltiList')
				->attribute('tabindex', $i)
				->attribute('maxlength', '128')
				->rule('maxLength', 128)
				->attribute('onchange', 'web.multiList(this);')
				->attribute('data-selectize-customitems', '.selectize-customitems')
				->attribute('data-selectize-url', Request::generateUri	('autoComplete', 'getListSkills'))
				->attribute('data-selectize-url-next-page', $this->listNextBnt)
				->attribute('data-selectize-order', 'true');





//			$form->text('name_' . $i, 'Skill name', isset($item->skillName) ? $item->skillName : $item['name'])
//				->attribute('tabindex', $i)
//				->attribute('class', 'isMiltiList')
//				->attribute('data-id', $i)
//				->attribute('onkeyup', 'web.multiList(this);')
//				->attribute('maxlength', '128')
//				->rule('maxLength', 128);

			$form->html('count_' . $i, false, '<div class="count_endorsement">' . ((isset($item->skillName)) ? $item->skillEndorsement : '') . '</div>')
				->attribute('class', 'form-html');

			$form->html('delete_' . $i, false, '<a href="#" class="btn-roundblue-border icons i-deletecustom icon-text 	 deleteMiltiList" onclick="return web.deleteListFromMultilist(this);"><span></span></a>')
				->attribute('class', 'form-html');
		}
		$i++;

		$form->fieldset('fields_' . $i, false, array('class' => 'customform on-white customform-label'));

		$form->select('name_' . $i, array('' => 'Please select skill') + $this->skills, 'Skill name')
			->attribute('class', 'selectize isMiltiList')
			->attribute('tabindex', $i)
			->attribute('maxlength', '128')
			->rule('maxLength', 128)
			->attribute('onchange', 'web.multiList(this);')
			->attribute('data-selectize-customitems', '.selectize-customitems')
			->attribute('data-selectize-url', Request::generateUri	('autoComplete', 'getListSkills'))
			->attribute('data-selectize-url-next-page', $this->listNextBnt)
			->attribute('data-selectize-order', 'true');

//		$form->text('name_' . $i, 'Skill name')
//			->attribute('tabindex', $i)
//			->attribute('class', 'isMiltiList')
//			->attribute('data-id', $i)
//			->attribute('onkeyup', 'web.multiList(this);')
//			->attribute('maxlength', '128')
//			->rule('maxLength', 128);

		$form->html('count_' . $i, false, '<div class="count_endorsement"></div>')
			->attribute('class', 'form-html');

		$form->html('delete_' . $i, false, '<a href="#" class="btn-roundblue-border icons i-deletecustom icon-text  deleteMiltiList" onclick="return web.deleteListFromMultilist(this);"><span></span></a>')
			->attribute('class', 'form-html');

		$form->attribute('data-last_id', $i);

		$this->generateTemplate();

	}


	public function generateTemplate()
	{
		$form =& $this->form;
		$form->fieldset('fields_%i', false, array('class' => 'customform on-white customform-label form-template'));

		$form->select('name_%i', array('' => 'Please select skill') + $this->skills, 'Skill name')
			->attribute('class', 'selectize isMiltiList')
			->attribute('data-id', '%i')
			->attribute('maxlength', '128')
			->rule('maxLength', 128)
			->attribute('onchange', 'web.multiList(this);')
			->attribute('data-selectize-customitems', '.selectize-customitems')
			->attribute('data-selectize-url', Request::generateUri	('autoComplete', 'getListSkills'))
			->attribute('data-selectize-url-next-page', $this->listNextBnt)
			->attribute('data-selectize-order', 'true');


//		$form->text('name_%i', 'Skill name')
//			->attribute('data-id', '%i')
//			->attribute('class', 'isMiltiList')
//			->attribute('onkeyup', 'web.multiList(this);')
//			->attribute('maxlength', '128')
//			->rule('maxLength', 128);

		$form->html('count_%i', false, '<div class="count_endorsement"></div>')
			->attribute('class', 'form-html');

		$form->html('delete_%i', false, '<a href="#" class="btn-roundblue-border icons i-deletecustom icon-text  deleteMiltiList" onclick="return web.deleteListFromMultilist(this);"><span></span></a>')
			->attribute('class', 'form-html');


	}
}