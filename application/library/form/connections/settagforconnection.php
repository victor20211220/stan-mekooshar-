<?php

class Form_Connections_SetTagForConnection extends Form_Main
{
	protected $i = 0;
	public function __construct($connection, $tags, $setTags = false)
	{
		$form = new Form('settagsforconnection', false, Request::generateUri('connections', 'acceptReceived', $connection->id));
		$form->attribute('onsubmit', "return box.submit(this);");
		$form->hidden('hidden')->phantom();

		$form->fieldset('tags', false, array('class' => 'customform on-white customform-label'));

		foreach($tags['data'] as $tag) {
			$this->i++;
			$form->checkbox('tags_' . $tag->id, false, $tag->name)
				->attribute('class', 'form-checkbox')
				->attribute('tabindex', $this->i);
		}

		$form->fieldset('submit', false, array('class' => 'submit'));
		$form->html('cancel', '<a class="btn-roundbrown" href="' . Request::generateUri('profile', 'edit') . '" onclick="return box.close();">Cancel</a>');
		$form->html('save', '<a class="btn-roundblue" href="#" onclick="$(this).closest(\'form\').find(\'input:submit\').click(); return false;">Save<span></span></a>');
		$form->submit('submit', 'Submit')
			->visible(false);

		$this->form = $form;

		return $this;
	}

	public function setTags($connection_tags)
	{
		$form =& $this->form;

		foreach($connection_tags['data'] as $tag) {
			if(isset($form->elements['tags_' . $tag->tag_id])) {
				$form->elements['tags_' . $tag->tag_id]->setValue(true);
			}
		}
	}

	public function setTagsById($ids)
	{
		$form =& $this->form;

		foreach($ids as $id) {
			if(isset($form->elements['tags_' . $id])) {
				$form->elements['tags_' . $id]->setValue(true);
			}
		}
	}

//	public function addFriendTags($tags)
//	{
//		$form =& $this->form;
//
//		$form->fieldset('tags', false, array('class' => 'modernform fieldicon smalltype'));
//		foreach($tags['data'] as $tag) {
//			$this->i++;
//			$form->checkbox('tags_' . $tag->tag_id, false, $tag->tagName)
//				->attribute('class', 'form-checkbox')
//				->attribute('tabindex', $this->i);
//		}
//	}

	public function setAction($url)
	{
		$this->form->attribute('action', $url);
	}
}