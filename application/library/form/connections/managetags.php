<?php

class Form_Connections_ManageTags extends Form_Main
{
	public function __construct($tags)
	{
		$form = new Form('managetags', false, Request::generateUri('connections', 'manageTags'));
		$form->attribute('onsubmit', "return box.submit(this);");

		$form->fieldset('tags', false, array('class' => 'customform on-white customform-label'));

		$this->form = $form;
		$this->generateFiled($tags);

		$form->fieldset('submit', false, array('class' => 'submit'));
		$form->html('cancel', '<a class="btn-roundbrown" href="#" onclick="return box.close();">Cancel</a>');
		$form->html('save', '<a class="btn-roundblue" href="#" onclick="$(this).closest(\'form\').find(\'input:submit\').click(); return false;">Save changes</a>');
		$form->submit('submit', 'Submit')
			->visible(false);

		$this->form = $form;

		return $this;
	}

	public function generateFiled($items)
	{
		$form =& $this->form;
		$form->fieldset('fields');

		if(Request::isPost()) {
			$items['data'] = $this->getPost($_POST[$this->form->attributes['id']]);
		}

		$i = 0;

		foreach($items['data'] as $item) {
			$i++;
			$form->fieldset('fields_' . $i, false, array('class' => 'customform on-white customform-label'));
			$form->text('name_' . (isset($item->id) ? $item->id : $item['id']), '', isset($item->name) ? $item->name : $item['name'])
				->attribute('placeholder', 'Tag name')
				->attribute('tabindex', $i)
				->attribute('class', 'isMiltiList')
				->attribute('data-id', $i)
				->attribute('onkeyup', 'web.multiList(this);')
				->attribute('maxlength', '64')
				->rule('maxLength', 64);

			$form->html('delete_' . $i, false, '<a href="#" class="btn-roundblue-border icons i-deletecustom icon-text deleteMiltiList" onclick="return web.deleteListFromMultilist(this);"><span></span></a>')
				->attribute('class', 'form-html');
		}
		$i++;

		$form->fieldset('fields_' . $i, false, array('class' => 'customform on-white customform-label'));
		$form->text('name_' . $i, '')
			->attribute('placeholder', 'Tag name')
			->attribute('tabindex', $i)
			->attribute('class', 'isMiltiList')
			->attribute('data-id', $i)
			->attribute('onkeyup', 'web.multiList(this);')
			->attribute('maxlength', '64')
			->rule('maxLength', 64);

		$form->html('delete_' . $i, false, '<a href="#" class="btn-roundblue-border icons i-deletecustom icon-text deleteMiltiList" onclick="return web.deleteListFromMultilist(this);"><span></span></a>')
			->attribute('class', 'form-html');

		$form->attribute('data-last_id', $i);

		$this->generateTemplate();

	}

	public function generateTemplate()
	{
		$form =& $this->form;
		$form->fieldset('fields_%i', false, array('class' => 'customform on-white customform-label form-template'));

		$form->text('name_%i', '')
			->attribute('placeholder', 'Tag name')
			->attribute('data-id', '%i')
			->attribute('class', 'isMiltiList')
			->attribute('onkeyup', 'web.multiList(this);')
			->attribute('maxlength', '64')
			->rule('maxLength', 64);

		$form->html('delete_%i', false, '<a href="#" class="btn-roundblue-border icons i-deletecustom icon-text deleteMiltiList" onclick="return web.deleteListFromMultilist(this);"><span></span></a>')
			->attribute('class', 'form-html');
	}

	public function getPost($values)
	{
		unset($values['name_%i']);

		$tags = array();
		foreach($values as $key => $value) {
			if(substr($key, 0, 4) == 'name') {
				$tags[substr($key, 5)] = $value;
			}
		}

		$tags_array = array();
		foreach($tags as $key => $tag) {
			if(empty($tag)) {
				continue;
			}
			$tags_array[$key] = array(
				'name' => $tag,
				'id' => $key
			);
		}
		return $tags_array;
	}
}