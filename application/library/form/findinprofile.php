<?php

class Form_FindInProfile extends Form_Main
{
	public function __construct($profile_id)
	{
		$form = new Form('findinprofile', false, Request::generateUri('profile', 'getListProfileConnection', $profile_id));
		$form->attribute('onsubmit', "return web.searchInProfile(this);");

		$form->fieldset('fields', false, array('class' => 'modernform-panel'));

		$form->text('find', false)
//			->attribute('onkeyup', 'return web.searchInProfile(this);')
			->attribute('placeholder', 'SEARCH');

		$form->fieldset('submit', false, array('class' => ''));
		$form->html('submit', '<a class="btn-roundblue borderradius_2 icons i-search" href="#" onclick="return web.searchInProfile($(\'#findinprofile-find\'));"><span></span></a>');

		$this->form = $form;

		return $this;
	}
}