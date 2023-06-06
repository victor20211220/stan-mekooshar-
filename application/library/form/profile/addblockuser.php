<?php

class Form_Profile_AddBlockUser extends Form_Main
{
	public $profile_id = FALSE;
	public $profile_name = FALSE;
	public $finded_profile = FALSE;
	public $current_blocked_list = FALSE;

	public function __construct()
	{
		$form = new Form('addblockuser', FALSE, Request::generateUri('profile', 'addBlockUser'));
		$form->attribute('onsubmit', "return box.submit(this);");

		$this->form =& $form;

		$form->fieldset('fields', false, array('class' => 'customform on-white customform-label'));

		$obj = $this;
		$form->text('url', 'Url to profile:')
			->attribute('tabindex', '1')
			->attribute('maxlength', '255')
			->attribute('required', 'required')
			->rule('url')
			->rule('maxLength', 255)
			->required()
			->rule(function($field) use ($obj){

				$parser = explode('/', $field->value);
				$profile_id = FALSE;
				$profile_name = FALSE;
				if(isset($parser[3])) {
					if($parser[3] == 'profile' && isset($parser[4]) && !empty($parser[4])) {
						$obj->profile_id = $parser[4];
						$obj->finded_profile = Model_User::getItemByUserid_withoutError($obj->profile_id);
					} else {
						$obj->profile_name = $parser[3];
						$obj->finded_profile = Model_User::getItemByUseralias_withoutError($obj->profile_name);
					}
				}



				if(empty($obj->finded_profile)){
					return('User doesn\'t find. Bad url.');
				}

				if(!empty($obj->finded_profile)) {
					$isAlreadyBlocked = FALSE;
					$obj->current_blocked_list = Model_Profile_Blocked::getListBlockedUser();

					if(isset($obj->current_blocked_list['data'][$obj->finded_profile->id]) && !isset($_SESSION['privacy_settings']['blocked']['remove'][$obj->finded_profile->id])) {
						$isAlreadyBlocked = TRUE;
					}
					if(isset($_SESSION['privacy_settings']['blocked']['add'][$obj->finded_profile->id])) {
						$isAlreadyBlocked = TRUE;
					}

					if($isAlreadyBlocked) {
						return('User already blocked!');
					}
				}
			});




		$form->fieldset('submit', false, array('class' => 'submit'));
		$form->html('cancel', '<a class="btn-roundbrown" href="' . Request::generateUri('profile', 'privacySettings') . '" onclick="return box.close();">Cancel</a>');
		$form->html('add', '<a class="btn-roundblue" href="#" onclick="$(this).closest(\'form\').find(\'input:submit\').click(); return false;">Add</a>');
		$form->submit('submit', 'Submit')
			->visible(false);




		return $this;
	}

}