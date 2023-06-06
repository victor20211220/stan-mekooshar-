<?php

class Form_Updates_ShareUpdate extends Form_Main
{
	public $post = false;

	public function __construct($timeline_id)
	{
		$form = new Form('shareupdate', false, Request::generateUri('updates', 'share', $timeline_id) . Request::getQuery());
		$form->attribute('onsubmit', "return box.submit(this);");


		$form->fieldset('fields', false, array('class' => 'customform on-white customform-label'));

		$form->textarea('text', 'Your comment')
//			->attribute('placeholder', 'YOUR COMMENT')
			->attribute('rows', '5')
			->attribute('tabindex', '1')
			->attribute('maxlength', '800')
			->attribute('class', 'max-800')
			->attribute('required', 'required')
			->rule('maxLength', 800)
			->required();


		$form->fieldset('submit', false, array('class' => 'submit'));
		$form->html('cancel', '<a class="btn-roundbrown" href="' . Request::generateUri('profile', 'edit') . '" onclick="return box.close();">Cancel</a>');
		$form->html('save', '<a class="btn-roundblue icon-next" href="#" onclick="$(this).closest(\'form\').find(\'input:submit\').click(); return false;">Share</a>');
		$form->submit('submit', 'Submit')
			->visible(false);
		$this->form = $form;

		return $this;
	}
}