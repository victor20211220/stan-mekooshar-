<?php

class Form_Groups_FindMemberInGroup extends Form_Main
{
	public function __construct($group_id)
	{
		$form = new Form('findmemberingroup', false, Request::generateUri('groups', 'members', $group_id));

		$form->fieldset('fields', false, array('class' => 'customform on-white customform-label'));

		$form->text('find', false)
			->attribute('onkeyup', 'return web.searchMemberInGroup(this);')
			->attribute('placeholder', 'SEARCH');

		$form->fieldset('submit', false, array('class' => ''));
		$form->html('submit', '<a class="btn-roundblue icons i-search" href="#" onclick="return web.searchInProfile($(\'#findinprofile-find\'));"><span></span></a>');

		$this->form = $form;

		return $this;
	}
}