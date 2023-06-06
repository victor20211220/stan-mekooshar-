<?php

class Form_Profile_PrivacySettings extends Form_Main
{
	public function __construct()
	{
		$form = new Form('privacysettings', false);

		$form->fieldset('fields1', false, array('class' => 'customform on-white customform-label'));


//		$form->html('current_label', false, '<b>Current password:</b>')
//			->attribute('class', 'form-html');
		$form->password('current', 'Current password')
			->attribute('tabindex', '1')
			->attribute('maxlength', '32')
			->attribute('minlength', '5')
			->rule('minLength', 5)
			->rule('maxLength', 32)
			->before(function ($field) {
				if(!empty($field->fieldset->elements['new']->value) || !empty($field->fieldset->elements['reenternew']->value))
				{
					$field->required();
				}
			})
			->rule(function ($field) {
				if(!empty($field->value)) {
					$auth = Auth::getInstance();
					$user = $auth->getIdentity();
					if(!$auth->checkPassword($user->email, $field->value)) {
						return 'Password is not correct!';
					}
				}
			});



//		$form->html('new_label', false, '<b>New password:</b>')
//			->attribute('class', 'form-html');
		$form->password('new', 'New password')
			->attribute('tabindex', '2')
			->attribute('maxlength', '32')
			->attribute('minlength', '5')
			->rule('minLength', 5)
			->rule('maxLength', 32)
			->before(function ($field) {
				if(!empty($field->fieldset->elements['current']->value))
				{
					$field->required();
				}
			});



//		$form->html('reenternew_label', false, '<b>Re-enter new password:</b>')
//			->attribute('class', 'form-html');
		$form->password('reenternew', 'Re-enter new password')
			->attribute('tabindex', '3')
			->attribute('maxlength', '32')
			->attribute('minlength', '5')
			->rule('maxLength', 32)
			->rule('minLength', 5)
			->before(function ($field) {
				if(!empty($field->fieldset->elements['current']->value))
				{
					$field->required();
				}
			})
			->rule(function ($field) {
				if($field->value != $field->fieldset->elements['new']->value) {
					return 'Passwords do not match!';
				}
			});



		$form->fieldset('fields2', false, array('class' => 'modernform fieldicon smalltype'));
		$form->html('share_activity_in_activity_feed_label', false, '<b>Share your activities in activity feed:</b>')
			->attribute('class', 'form-html');

		$form->html('set_invisible_profile_label', false, '<b>Make your profile invisible:</b>')
			->attribute('class', 'form-html');

//		$form->html('can_see_activity_feed_label', false, '<b>Who can see your activity feed:</b>')
//			->attribute('class', 'form-html');

		$form->html('can_see_connections_label', false, '<b>Who can see your connections:</b>')
			->attribute('class', 'form-html');

		$form->html('can_see_contact_info_label', false, '<b>Your contact information visible to:</b>')
			->attribute('class', 'form-html');





		$form->fieldset('fields3', false, array('class' => 'customform on-white customform-label'));

		$form->radio('share_activity_in_activity_feed', array(1 => 'Yes', 0 => 'No'), '')
			->attribute('class', 'form-radio')
			->attribute('tabindex', '4')
			->attribute('maxlength', '1')
			->rule('maxLength', 1)
			->setValue(1);


		$form->radio('set_invisible_profile', array(1 => 'Yes', 0 => 'No'), '')
			->attribute('class', 'form-radio')
			->attribute('tabindex', '5')
			->attribute('maxlength', '1')
			->rule('maxLength', 1)
			->setValue(1);


//		$form->select('can_see_activity', array('' => '') + t('can_see_activity'), '<span class="icons i-dot"><span></span></span>')
//			->attribute('class', 'bootstripe')
//			->attribute('tabindex', '6')
//			->attribute('maxlength', '2')
//			->rule('maxLength', 2)
//			->setValue(1);


		$form->select('can_see_connections', array('' => '') + t('can_see_connections'), '')
			->attribute('class', 'bootstripe')
			->attribute('tabindex', '6')
			->attribute('maxlength', '2')
			->rule('maxLength', 2)
			->setValue(1);


//		$form->radio('can_see_contact_info', array(1 => 'Yes', 0 => 'No'), '')
//			->attribute('class', 'form-radio')
//			->attribute('tabindex', '5')
//			->attribute('maxlength', '1')
//			->rule('maxLength', 1)
//			->setValue(1);
		$form->select('can_see_contact_info', array('' => '') + t('can_see_my_contact_info'), '')
			->attribute('class', 'bootstripe')
			->attribute('tabindex', '7')
			->attribute('maxlength', '2')
			->rule('maxLength', 2)
			->setValue(2);




		$form->fieldset('submit', false, array('class' => 'submit'));
		$form->html('cancel', '<a class="btn-roundbrown" href="' . Request::generateUri('profile', 'index') . '">Cancel</a>');
		$form->html('save', '<a class="btn-roundblue" href="#" onclick="$(this).closest(\'form\').find(\'input:submit\').click(); return false;">Save changes<span></span></a>');
		$form->submit('submit', 'Submit')
			->visible(false);

		$this->form = $form;

		return $this;
	}


	public function setValues()
	{
		$user = Auth::getInstance()->getIdentity();
		$values = array(
			'share_activity_in_activity_feed' => $user->shareActivityInActivityFeed,
			'set_invisible_profile' => $user->setInvisibleProfile,
//			'can_see_activity' => $user->whoCanSeeActivity,
			'can_see_connections' => $user->whoCanSeeConnections,
			'can_see_contact_info' => $user->whoCanSeeContactInfo
		);

		$this->form->loadValues($values);
	}
}