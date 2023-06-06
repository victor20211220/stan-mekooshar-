<?php

class Form_Schools_SelectTypeInSchool extends Form_Main
{
	public $profile_education = false;
	public $profile_experiance = false;

	public function __construct($school)
	{
		$form = new Form('selecttypeinschool', false, Request::generateUri('schools', 'selectTypeInSchool', $school->id));
		$form->attribute('onsubmit', 'return web.setTypeInSchool(this);');

		$form->fieldset('fields1', false, array('class' => 'customform on-white customform-label'));

		$type = array('' => '', '0' => 'None') + t('type_in_school');

		$user = Auth::getInstance()->getIdentity();
		$profile_education = Model_Profile_Education::checkUniversityBySchoolidUserid($school->id, $user->id);
		$profile_experiance = Model_Profile_Experience::checkUniversityBySchoolidUserid($school->id, $user->id);

		$this->profile_education = $profile_education;
		$this->profile_experiance = $profile_experiance;

		$value = 0;
		if($profile_education) {
			if($this->profile_education->isTypeInSchool > 0) {
				$value = $this->profile_education->isTypeInSchool;
			} else {
				if(!empty($profile_education->yearTo) && $profile_education->yearTo <= date('Y')) {
					$value = 1;
				} else {
					$value = 2;
				}
			}

		}
		if($profile_experiance && $profile_experiance->isSchoolMember == 1) {
			$value = 3;
		}
		if($profile_experiance && $profile_experiance->isSchoolMember === '0') {
			$value = 4;
			unset($type['3']);
		} else {
			unset($type['4']);
		}

		$form->select('type', $type, '')
			->attribute('class', 'bootstripe')
			->attribute('tabindex', '3')
			->attribute('maxlength', '1')
			->attribute('onchange', 'return web.changeTypeInSchool(this);')
			->rule('maxLength', 1)
			->setValue($value);



		$form->fieldset('fields2', false, array('class' => ''));
		$form->html('preloader', '<div class="hidden loader loader-22"></div>');



		$form->fieldset('submit', false, array('class' => 'submit'));
//		$form->html('cancel', '<a class="btn-roundbrown" href="' . Request::generateUri('schools', 'updates') . '" onclick="return box.close();">Cancel</a>');
		$form->html('save', '<a class="btn-roundblue" href="#" onclick="$(this).closest(\'form\').find(\'input:submit\').click(); return false;">Save</a>');
		$form->submit('submit', 'Submit')
			->visible(false);

		$this->form = $form;

		return $this;
	}
}