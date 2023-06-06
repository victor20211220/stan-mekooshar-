<?php

class Form_Connections_AddConnections extends Form_Main
{
	public function __construct($profile)
	{
		$form = new Form('addconnections', false, Request::generateUri('connections', 'addConnections', $profile->id));
		$form->attribute('onsubmit', "return box.submit(this);");

		$form->fieldset('tags', false, array('class' => 'customform on-white customform-label'));

		$user = Auth::getInstance()->getIdentity();
		$tags = Model_Tags::getListByUser($user->id);

		$i = 0;
		foreach($tags['data'] as $tag) {
			$i++;
			$form->checkbox('tags_' . $tag->id, false, $tag->name)
				->attribute('class', 'form-checkbox')
				->attribute('tabindex', $i);
		}

		$form->fieldset('message', false, array('class' => 'customform on-white customform-label'));

		$i++;
		$form->textarea('message', '')
//			->attribute('placeholder', 'MESSAGE')
			->attribute('rows', '5')
			->attribute('tabindex', $i)
			->attribute('maxlength', '1000')
			->attribute('class', 'max-1000')
			->rule('maxLength', 1000);

		$form->fieldset('submit', false, array('class' => 'submit'));
		$form->html('cancel', '<a class="btn-roundbrown" href="' . Request::generateUri('profile', 'edit') . '" onclick="return box.close();">Cancel</a>');
		$form->html('save', '<a class="btn-roundblue" href="#" onclick="$(this).closest(\'form\').find(\'input:submit\').click(); return false;">Send invitation</a>');
		$form->submit('submit', 'Submit')
			->visible(false);

		$this->form = $form;

		return $this;
	}

	public function setFromUserAvaBlock($profile_id)
	{
		$this->form->attribute('action', Request::generateUri('connections', 'addConnectionsFromUserAvaBlock', $profile_id));
	}

	public function setFromSearch($profile_id)
	{
		$this->form->attribute('action', Request::generateUri('connections', 'AddConnectionsFromSearch', $profile_id));
	}
}

