<?php

class Form_Payment extends Form_Main
{
	public function __construct()
	{
		$form = new Form('payment');
		$form->attribute('onsubmit', '$(this).parent().addClass(\'is-preloader\');');

		$form->fieldset('field', false, array('class' => 'customform on-white customform-label'));


//		$plans = Model_Plans::getListPlans($categoryPlan);
//
//		$tmp_plans = array();
//		foreach($plans['data'] as $plan) {
//			$tmp_plans[$plan->id] = $plan->name;
//		}
//		$plans = $tmp_plans;
//		$keys = array_keys($plans);
//
//		$form->radio('plan', $plans, '<span class="icons i-dot"><span></span></span>')
//			->attribute('class', 'form-radio')
//			->attribute('tabindex', '1')
//			->attribute('maxlength', '2')
//			->attribute('required', 'required')
//			->rule('maxLength', 2)
//			->rule('required')
//			->setValue(current($keys));



		$form->fieldset('customer', false, array('class' => 'customform on-white customform-label'));

		$form->text('customerCustomer', 'Full name')
//			->attribute('placeholder', 'Full name')
//			->attribute('size', 40)
			->attribute('maxlength', '40')
			->attribute('required', 'required')
			->rule('required');

		$form->text('customerEmail', 'Email')
//			->attribute('placeholder', 'Email')
//			->attribute('size', 40)
			->attribute('maxlength', '40')
			->attribute('required', 'required')
			->rule('required')
			->rule('email');

		$form->text('customerAddress', 'Address')
//			->attribute('placeholder', 'Address')
//			->attribute('size', 40)
			->attribute('maxlength', '40')
			->attribute('required', 'required')
			->rule('required');

		$form->text('customerCity', 'City')
//			->attribute('placeholder', 'City')
//			->attribute('size', 40)
			->attribute('maxlength', '40')
			->attribute('required', 'required')
			->rule('required');

		$form->select('customerState', array_merge(array('' => ' '), Text::get('states')), 'State')
//			->attribute('placeholder', 'State')
			->attribute('class', 'bootstripe')
			->attribute('required', 'required')
			->rule('required');

		$form->text('customerZip', 'Zip-code')
//			->attribute('placeholder', 'ZIP-code')
			->attribute('maxlength', '10')
//			->attribute('size', 10)
			->attribute('required', 'required')
			->rule('required')
			->rule('integer')
			->rule('minLength', 5);

		$form->text('customerPhone', 'Phone')
//			->attribute('placeholder', 'Phone')
			->attribute('maxlength', '40');
//			->attribute('size', 40);

		$form->textarea('customerComment', 'Comments')
//			->attribute('placeholder', 'Comments')
			->attribute('maxlength', '3000')
			->attribute('rows', 5);






		$form->fieldset('creditcard', false, array('class' => 'customform on-white customform-label'));

		$form->select('creditCardType', array(
			'visa' => 'VISA',
			'mastercard' => 'Mastercard',
			'discover' => 'Discover',
			'amex' => 'American Express',
		), 'Card type')
//			->attribute('placeholder', 'Card type')
			->attribute('required', 'required')
			->attribute('class', 'bootstripe')
			->rule('required');

		$form->text('acct', 'Card number')
//			->attribute('placeholder', 'Card number')
//			->attribute('size', 21)
			->attribute('required', 'required')
			->rule('maxLength', 19)
			->rule('required');

		$form->text('firstName', 'First name')
//			->attribute('placeholder', 'First name')
			->attribute('required', 'required')
			->rule('required');

		$form->text('lastName', 'Last name')
//			->attribute('placeholder', 'Last name')
			->attribute('required', 'required')
			->rule('required');

		$form->text('expMonth', 'Expiration month')
//			->attribute('placeholder', 'Expiration month')
			->attribute('required', 'required')
//			->attribute('size', 2)
			->rule('minLength', 2)
			->rule('maxLength', 2)
			->rule('integer')
			->rule('required');

		$form->text('expYear', 'Expiration year')
//			->attribute('placeholder', 'Expiration year')
			->attribute('required', 'required')
//			->attribute('size', 4)
			->rule('minLength', 4)
			->rule('maxLength', 4)
			->rule('integer')
			->rule('required');

		$form->text('cvv2', 'CSC')
//			->attribute('placeholder', 'CSC')
			->attribute('required', 'required')
//			->attribute('size', 5)
			->attribute('maxlength', '5')
			->rule('required');

		$form->textarea('street', 'Address')
//			->attribute('placeholder', 'Address')
			->attribute('required', 'required')
			->attribute('cols', 25)
			->attribute('rows', 3)
			->attribute('maxlength', '255')
			->rule('required');

		$form->text('city', 'City')
//			->attribute('placeholder', 'City')
			->attribute('required', 'required')
			->rule('required');

		$form->select('state', array_merge(array('' => ' '), Text::get('states')), 'State')
			->attribute('class', 'bootstripe')
			->attribute('required', 'required')
//			->attribute('placeholder', 'State')
			->rule('required');

		$form->text('zip', 'Zip code')
//			->attribute('placeholder', 'ZIP code')
			->attribute('required', 'required')
//			->attribute('size', 7)
			->rule('maxLength', 5)
			->rule('required');





		$form->fieldset('submit', false, array('class' => 'submit'));

//		$from = FALSE;
//		if(isset($_SESSION['jobs_from'])) {
//			$from = $_SESSION['jobs_from'];
//		}
//		$id = $job->id;
//		if(!$from) {
//			$from = 'myJobs';
//		}
//		if(in_array($from, array('myJobs', 'search'))) {
//			$id = false;
//		}
//
//		$form->html('cancel', '<a class="btn-cancel" href="' . Request::generateUri('jobs', $from, $id) . '">Cancel</a>');
//		$form->html('save', '<a class="btn-save icon-next" href="#" onclick="$(this).closest(\'form\').find(\'input:submit\').click(); return false;">Pay now<span></span></a>');
//		$form->submit('submit', 'Submit')
//			->visible(false);

		$this->form = $form;

		return $this;
	}

	public function setTypeUpgradeProfile()
	{
		$form =& $this->form;

		$form->fieldset('field');

		$plans = Model_Plans::getListPlans(CATEGORY_PLAN_PROFILE);
		$tmp_plans = array();
		foreach($plans['data'] as $plan) {
			$tmp_plans[$plan->id] = $plan->name;
		}
		$plans = $tmp_plans;
		$keys = array_keys($plans);

		$form->radio('plan', $plans, '')
			->attribute('class', 'form-radio')
			->attribute('tabindex', '1')
			->attribute('maxlength', '2')
			->attribute('required', 'required')
			->rule('maxLength', 2)
			->rule('required')
			->setValue(current($keys));



		$form->fieldset('submit');
		$form->html('cancel', '<a class="btn-roundbrown" href="' . Request::generateUri('profile', 'upgrade') . '">Cancel</a>');
		$form->html('save', '<a class="btn-roundblue" href="#" onclick="$(this).closest(\'form\').find(\'input:submit\').click(); return false;">Pay now</a>');
		$form->submit('submit', 'Submit')
			->visible(false);
	}


	public function setTypeActivateJob($job)
	{
		$form =& $this->form;

		$form->fieldset('field');

		$plans = Model_Plans::getListPlans(CATEGORY_PLAN_JOB);
		$tmp_plans = array();
		foreach($plans['data'] as $plan) {
			$tmp_plans[$plan->id] = $plan->name;
		}
		$plans = $tmp_plans;
		$keys = array_keys($plans);

		$form->radio('plan', $plans, '')
			->attribute('class', 'form-radio')
			->attribute('tabindex', '1')
			->attribute('maxlength', '2')
			->attribute('required', 'required')
			->rule('maxLength', 2)
			->rule('required')
			->setValue(current($keys));



		$form->fieldset('submit', false, array('class' => 'submit'));

		$from = FALSE;
		if(isset($_SESSION['jobs_from'])) {
			$from = $_SESSION['jobs_from'];
		}
		$id = $job->id;
		if(!$from) {
			$from = 'myJobs';
		}
		if(in_array($from, array('myJobs', 'search'))) {
			$id = false;
		}

		$form->html('cancel', '<a class="btn-roundbrown" href="' . Request::generateUri('jobs', $from, $id) . '">Cancel</a>');
		$form->html('save', '<a class="btn-roundblue" href="#" onclick="$(this).closest(\'form\').find(\'input:submit\').click(); return false;">Pay now</a>');
		$form->submit('submit', 'Submit')
			->visible(false);
	}

	public function setTestData()
	{
		$form =& $this->form;

		$form->elements['creditCardType']->value = 'visa';
		$form->elements['acct']->value = '4334981618387435';
		$form->elements['firstName']->value = 'Test';
		$form->elements['lastName']->value = 'User';
		$form->elements['expMonth']->value = '01';
		$form->elements['expYear']->value = '2015';
		$form->elements['cvv2']->value = '123';
		$form->elements['street']->value = '';
		$form->elements['city']->value = 'San Jose';
		$form->elements['state']->value = 'CA';
		$form->elements['zip']->value = '95131';
		$form->elements['street']->value = 'some address';

		$user = Auth::getInstance()->getIdentity();
		if ($user) {
			$form->elements['customerCustomer']->value = $user->firstName . ' ' . $user->lastName;
			$form->elements['customerEmail']->value    = $user->email;
			$form->elements['customerAddress']->value  = $user->address;
			$form->elements['customerCity']->value     = $user->city;
			$form->elements['customerState']->value    = $user->state;
			$form->elements['customerZip']->value      = $user->zip;
			$form->elements['customerPhone']->value    = $user->phone;
		}
	}
}