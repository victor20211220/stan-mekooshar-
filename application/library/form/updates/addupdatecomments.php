<?php

class Form_Updates_AddUpdateComments extends Form_Main
{
	public $post = false;

	public function __construct($timeline_id)
	{
		$form = new Form('addupdatecomments_' . $timeline_id, false, Request::generateUri('updates', 'comments', $timeline_id) . Request::getQuery());
		$form->attribute('onsubmit', "return box.submit(this);");


		$form->fieldset('fields', false, array('class' => 'customform on-white customform-label'));

		$form->textarea('text', 'Comment')
//			->attribute('placeholder', 'COMMENTS')
			->attribute('rows', '2')
			->attribute('tabindex', '1')
			->attribute('maxlength', '800')
			->attribute('required', 'required')
			->attribute('class', 'max-800')
			->rule('maxLength', 800)
			->required();


		$form->fieldset('submit', false, array('class' => 'submit'));
		$form->html('save', '<a class="btn-roundblue" href="#" onclick="$(this).closest(\'form\').find(\'input:submit\').click(); return false;">Add comment</a>');
		$form->submit('submit', 'Submit')
			->visible(false);
		$this->form = $form;

		return $this;
	}

	public function setForDiscussionPage($timeline_id)
	{
		$this->form->attribute('action', Request::generateUri('groups', 'discussion', $timeline_id) . Request::getQuery());
	}
}