<?php

class Form_Profile_EditLanguages extends Form_Main
{
	public function __construct($items = false)
	{
		$form = new Form('editlanguages', false, Request::generateUri('profile', 'editLanguage'));
		$form->attribute('onsubmit', "return box.submit(this);");
		$this->form =& $form;

		$this->form = $form;
		$this->generateFiled($items);

		$form->fieldset('submit', false, array('class' => 'submit'));
		$form->html('cancel', '<a class="btn-roundbrown" href="' . Request::generateUri('profile', 'edit') . '" onclick="return web.cancelEditBlock(this);">Cancel</a>');
		$form->html('save', '<a class="btn-roundblue" href="#" onclick="$(this).closest(\'form\').find(\'input:submit\').click(); return false;">Save changes</a>');
		$form->submit('submit', 'Submit')
			->visible(false);

		$this->form = $form;

		return $this;
	}

	public function generateFiled($items)
	{
		$form =& $this->form;

		if(Request::isPost()) {
			$items['data'] = $this->getPost($_POST[$this->form->attributes['id']]);
		}
		$firstValue = current(t('language_level'));

		// Get list for autocomplete
		$languages = Model_Languages::getList_OrderCountUsed();
		$this->languages = $languages;

		$i = 0;
		foreach($items['data'] as $item) {
			$i++;
			$form->fieldset('fields_' . $i, false, array('class' => 'customform on-white customform-label'));
			$this->generateAutocomplete('name_' . $i, ' ', 'Language', false, $languages, 'getListLanguages')
				->attribute('tabindex', $i * 2 - 1)
				->attribute('class', ($form->elements['name_' . $i]->attributes['class'] . ' isMiltiList'))
				->attribute('data-id', $i)
				->attribute('onchange', 'web.multiList(this);')
				->attribute('maxlength', '128')
				->rule('maxLength', 128)
				->rule(function($field) {
					if(Request::isPost()) {
						if(isset($_POST['editlanguages'])) {
							$elements = $_POST['editlanguages'];
							foreach($elements as $name => $value) {
								if( substr($name, 0, 5) == 'name_' && $name != $field->name) {
									if($value == $field->value) {
										return 'Dublicate language';
									}
								}
							}
						} else {
							return 'Error';
						}
					}
				})
				->setValue(isset($item->languageName) ? $item->language_id : $item['id']);


//			$form->text('name_' . $i, 'Language', isset($item->languageName) ? $item->languageName : $item['name'])
////				->attribute('placeholder', 'Language')
//				->attribute('tabindex', $i * 2 - 1)
//				->attribute('class', 'isMiltiList')
//				->attribute('data-id', $i)
//				->attribute('onkeyup', 'web.multiList(this);')
//				->attribute('maxlength', '128')
//				->rule('maxLength', 128)
//				->rule(function($field) {
//					if(Request::isPost()) {
//						if(isset($_POST['editlanguages'])) {
//							$elements = $_POST['editlanguages'];
//							foreach($elements as $name => $value) {
//								if( substr($name, 0, 5) == 'name_' && $name != $field->name) {
//									if($value == $field->value) {
//										return 'Dublicate language';
//									}
//								}
//							}
//						} else {
//							return 'Error';
//						}
//					}
//				});

			$form->html('delete_' . $i, false, '<a href="#" class="btn-roundblue-border icons i-deletecustom icon-text deleteMiltiList" onclick="return web.deleteListFromMultilist(this);"><span></span></a>')
				->attribute('class', 'form-html');

			$form->select('level_' . $i, array('' => $firstValue) + t('language_level') , 'Proficiency')
//				->attribute('placeholder', 'Proficiency...')
				->attribute('tabindex', $i*2)
				->attribute('class', 'bootstripe')
				->attribute('maxlength', '1')
				->rule('maxLength', 1)
				->setValue(((isset($item) && isset($item->levelType) && isset($item->languageName)) ? $item->levelType : $item['levelType']));

		}
		$i++;

		$form->fieldset('fields_' . $i, false, array('class' => 'customform on-white customform-label'));
		$this->generateAutocomplete('name_' . $i, '', 'Language', false, $languages, 'getListLanguages')
			->attribute('tabindex', $i * 2 - 1)
			->attribute('class', ($form->elements['name_' . $i]->attributes['class'] . ' isMiltiList'))
			->attribute('data-id', $i)
			->attribute('onchange', 'web.multiList(this);')
			->attribute('maxlength', '128')
			->rule('maxLength', 128);
//		$form->text('name_' . $i, 'Language')
////			->attribute('placeholder', 'Language')
//			->attribute('tabindex', $i * 2 - 1)
//			->attribute('class', 'isMiltiList')
//			->attribute('data-id', $i)
//			->attribute('onkeyup', 'web.multiList(this);')
//			->attribute('maxlength', '128')
//			->rule('maxLength', 128);

		$form->html('delete_' . $i, false, '<a href="#" style="display: none" class="btn-roundblue-border icons i-deletecustom icon-text deleteMiltiList" onclick="return web.deleteListFromMultilist(this);"><span></span></a>')
			->attribute('class', 'form-html');

		$form->select('level_' . $i, array('' => $firstValue) + t('language_level') , 'Proficiency')
//			->attribute('placeholder', 'Proficiency...')
			->attribute('tabindex', $i*2)
			->attribute('class', 'bootstripe')
			->attribute('maxlength', '1')
			->rule('maxLength', 1)
			->setValue(((isset($item) && isset($item->levelType) && isset($item->languageName)) ? $item->levelType : 1));



		$form->attribute('data-last_id', $i);

		$this->generateTemplate();

	}

	public function generateTemplate()
	{
		$form =& $this->form;
		$form->fieldset('fields_%i', false, array('class' => 'customform on-white customform-label form-template'));

		$firstValue = current(t('language_level'));

		$this->generateAutocomplete('name_%i', '', 'Language', false, $this->languages, 'getListLanguages')
			->attribute('data-id', '%i')
			->attribute('class', ($form->elements['name_%i']->attributes['class'] . ' isMiltiList'))
			->attribute('onchange', 'web.multiList(this);')
			->attribute('maxlength', '128')
			->rule('maxLength', 128);

//		$form->text('name_%i', 'Language')
////			->attribute('placeholder', 'Language')
//			->attribute('data-id', '%i')
//			->attribute('class', 'isMiltiList')
//			->attribute('onkeyup', 'web.multiList(this);')
//			->attribute('maxlength', '128')
//			->rule('maxLength', 128);

		$form->html('delete_%i', false, '<a href="#" class="btn-roundblue-border icons i-deletecustom icon-text deleteMiltiList" onclick="return web.deleteListFromMultilist(this);"><span></span></a>')
			->attribute('class', 'form-html');

		$form->select('level_%i', array('' => $firstValue) + t('language_level'), 'Proficiency')
//			->attribute('placeholder', 'Proficiency...')
			->attribute('class', 'bootstripe')
			->attribute('maxlength', '1')
			->rule('maxLength', 1);
	}

	public function getPost($values)
	{
		unset($values['name_%i'], $values['level_%i']);

		$languages = array();
		$level = array();
		foreach($values as $key => $value) {
			if(substr($key, 0, 4) == 'name') {
				$languages[substr($key, 5)] = $value;
			}
			if(substr($key, 0, 5) == 'level') {
				$level[substr($key, 6)] = $value;
			}
		}

		$languages_array = array();
		foreach($languages as $key => $language) {
			if(empty($language)) {
				continue;
			}

			if(substr($language, 0, 4) == 'new%') {
				$name = substr($language, 4);
				$check = Model_Languages::checkItemByName($name);
				if(!$check){
					$element = Model_Languages::create(array(
						'name' => $name
					));
					$countUsed = 0;
					$id = $element->id;
				} else {
					$id = $check->id;
					$countUsed = $check->countUsed;
				}
			} else {
				$name = $language;
				$check = Model_Languages::checkItemById($name);
				if(!$check){
					$element = Model_Languages::create(array(
						'name' => $name
					));
					$countUsed = 0;
					$id = $element->id;
				} else {
					$id = $check->id;
					$countUsed = $check->countUsed;
				}
			}

			$languages_array[$key] = array(
				'id' => $id,
				'levelType' => $level[$key],
				'countUsed' => $countUsed
			);
		}

		// Check

		return $languages_array;
	}
}