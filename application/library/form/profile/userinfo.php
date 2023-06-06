<?php

class Form_Profile_Userinfo extends Form_Main
{
	public function __construct()
	{
		$form = new Form('userinfo', false, Request::generateUri('profile', 'editUserInfo'));
		$form->attribute('onsubmit', "return box.submit(this, function(content){web.login(content)});");
		$this->form =& $form;


		$form->fieldset('fields', false, array('class' => 'customform on-white customform-label'));



		$form->text('email2', 'Email')
//			->attribute('placeholder', 'EMAIL')
			->attribute('tabindex', '1')
			->attribute('maxlength', '64')
			->rule('maxLength', 64);


		$form->text('phone', 'Phone')
//			->attribute('placeholder', 'PHONE')
			->attribute('tabindex', '2')
			->attribute('maxlength', '32')
			->rule('maxLength', 32);


		$countries = array(
			'data' => array(),
			'paginator' => array()
		);
		foreach(t('countries') as $key => $name) {
			$countries['data'][$key]  = (object) array(
				'id' => $key,
				'name' => $name,
				'countUsed' => '1'
			);
		}

		$obj = $this->generateAutocomplete('country', 'Please select country or create new', 'Country', false, $countries, FALSE, FALSE)
			->attribute('tabindex', '3')
			->attribute('maxlength', '2')
			->rule('maxLength', 2);


//		$form->select('country', array('' => ' ') + t('countries'), 'Country')
////			->attribute('placeholder', 'COUNTRY')
//			->attribute('class', 'bootstripe')
//			->attribute('tabindex', '3')
//			->attribute('maxlength', '2')
//			->rule('maxLength', 2);

		$form->text('state', 'State')
//			->attribute('placeholder', 'STATE')
			->attribute('tabindex', '5')
			->attribute('maxlength', '64')
			->rule('maxLength', 64);

		$form->text('city', 'City')
//			->attribute('placeholder', 'CITY')
			->attribute('tabindex', '5')
			->attribute('maxlength', '64')
			->rule('maxLength', 64);


		$form->text('address', 'Address')
//			->attribute('placeholder', 'ADDRESS')
			->attribute('tabindex', '6')
			->attribute('maxlength', '128')
			->rule('maxLength', 128);

		$form->text('zip', 'Zip')
//			->attribute('placeholder', 'ZIP')
			->attribute('tabindex', '7')
			->attribute('maxlength', '24')
			->rule('maxLength', 24);

		$form->text('alias', 'Public profile url (ex. name.surname)')
//			->attribute('placeholder', 'ZIP')
			->attribute('tabindex', '7')
			->attribute('maxlength', '64')
			->rule('maxLength', 64)
			->rule(function($field){
				$notAllowed = array('index', 'admin', 'auth', 'git', 'captcha', 'cart', 'companies', 'connections', 'cron', 'download', 'groups',
									'jobs', 'messages', 'notifications', 'profile', 'schools', 'search', 'updates', 'commerce', 'page', 'invite', 'socials',
									'thumbshot', 'about', 'addsearchcompany', 'addsearchindustry', 'addsearchregion', 'addsearchschool', 'policy', 'registration',
									'searchpeople', 'signin');

				if(in_array(strtolower($field->value), $notAllowed)) {
					return 'This alias name is not allow!';
				}

				$user = Auth::getInstance()->getIdentity();
				$alias_user = Model_User::checkByUserAlias_WithoutUserid($field->value, $user->id);
				if($alias_user) {
					return 'Alias is exist!';
				}
			});


		$this->form = $form;
		$this->generateFiled(8);


		$form->fieldset('submit', false, array('class' => 'submit'));
		$form->html('cancel', '<a class="btn-roundbrown" href="' . Request::generateUri('profile', 'edit') . '" onclick="return web.cancelEditBlock(this);">Cancel</a>');
		$form->html('save', '<a class="btn-roundblue" href="#" onclick="$(this).closest(\'form\').find(\'input:submit\').click(); return false;">Save changes</a>');
		$form->submit('submit', 'Submit')
			->visible(false);

		$this->form = $form;

		return $this;
	}


	public function generateFiled($i = 0)
	{
		$form =& $this->form;

		$websites = array();
		if(Request::isPost()) {
			$values = $this->getPost($_POST[$this->form->attributes['id']]);
			$websites = $values['websites'];
		} else {
			$user = Auth::getInstance()->getIdentity();
			if(!empty($user->websites)) {
				$websites = unserialize($user->websites);
			}
		}

		if($websites) {
			foreach($websites as $website) {
				$i++;
				$form->fieldset('fields_' . $i, false, array('class' => 'customform on-white customform-label multi-list'));
				$form->text('website_' . $i, 'Website', isset($website) ? $website : '')
//					->attribute('placeholder', 'WEBSITE')
					->attribute('tabindex', $i)
					->attribute('class', 'isMiltiList')
					->attribute('data-id', $i)
					->attribute('onkeyup', 'web.multiList(this);')
					->attribute('maxlength', '160')
					->rule('maxLength', 160)
					->rule('url');

				$form->html('delete_' . $i, false, '<a href="#" class="icons i-delete deleteMiltiList" onclick="return web.deleteListFromMultilist(this);"><span></span></a>')
					->attribute('class', 'form-html');
			}
		}
		$i++;

		$form->fieldset('fields_' . $i, false, array('class' => 'customform on-white customform-label multi-list'));
		$form->text('website_' . $i, 'Website')
//			->attribute('placeholder', 'WEBSITE')
			->attribute('tabindex', $i)
			->attribute('class', 'isMiltiList')
			->attribute('data-id', $i)
			->attribute('onkeyup', 'web.multiList(this);')
			->attribute('maxlength', '160')
			->rule('maxLength', 160);

		$form->html('delete_' . $i, false, '<a href="#" class="icons i-delete deleteMiltiList" onclick="return web.deleteListFromMultilist(this);"><span></span></a>')
			->attribute('class', 'form-html');

		$form->attribute('data-last_id', $i);

		$this->generateTemplate();

	}

	public function generateTemplate()
	{
		$form =& $this->form;
		$form->fieldset('fields_%i', false, array('class' => 'customform on-white customform-label form-template multi-list'));

		$form->text('website_%i', 'Website')
//			->attribute('placeholder', 'WEBSITE')
			->attribute('data-id', '%i')
			->attribute('class', 'isMiltiList')
			->attribute('onkeyup', 'web.multiList(this);')
			->attribute('maxlength', '160')
			->rule('maxLength', 160);

		$form->html('delete_%i', false, '<a href="#" class="icons i-delete deleteMiltiList" onclick="return web.deleteListFromMultilist(this);"><span></span></a>')
			->attribute('class', 'form-html');
	}

	public function getPost($values)
	{
		unset($values['website_%i']);

		$websites = array();
		foreach($values as $key => $value) {
			if(substr($key, 0, 7) == 'website') {
				unset($values[$key]);
				if(empty($value)) {
					continue;
				}
				$websites[] = $value;
			}
		}
		$values['websites'] = $websites;

		return $values;
	}

	public function setValue()
	{
		$user = Auth::getInstance()->getIdentity();
		$values = array(
			'email' => $user->email,
			'phone' => $user->phone,
			'country' => $user->country,
			'state' => $user->state,
			'city' => $user->city,
			'address' => $user->address,
			'zip' => $user->zip,
			'alias' =>$user->alias
		);

		$this->form->loadValues($values);
	}
}