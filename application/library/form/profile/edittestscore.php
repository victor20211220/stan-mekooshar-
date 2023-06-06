<?php

class Form_Profile_EditTestScore extends Form_Main
{
	public $testscore = FALSE;

	public function __construct($id = false)
	{
		$form = new Form('edittestscore', false, Request::generateUri('profile', 'editTestScore', $id));
		$form->attribute('onsubmit', "return box.submit(this);");
		$this->form =& $form;

		// Get list for autocomplete
		$testscopes = Model_TestScores::getList_OrderCountUsed();



		$form->fieldset('fields', false, array('class' => 'customform on-white customform-label'));

		$obj = $this->generateAutocomplete('testscore', 'Please select test scope or create new', 'Test score name', false, $testscopes, 'getListTestScopes')
			->attribute('required', 'required')
			->attribute('tabindex', '1')
			->attribute('maxlength', '128')
			->rule('maxLength', 128)
			->required();


//		$form->text('testscore', 'Test score name')
////			->attribute('placeholder', 'TEST SCORE NAME')
//			->attribute('required', 'required')
//			->attribute('tabindex', '1')
//			->attribute('maxlength', '128')
//			->rule('maxLength', 128)
//			->required();

		$form->text('occupation', 'Field of occupation')
//			->attribute('placeholder', 'FIELD OF OCCUPATION')
			->attribute('required', 'required')
			->attribute('tabindex', '2')
			->attribute('maxlength', '160')
			->rule('maxLength', 160)
			->required();

		$form->text('score', 'Degree')
//			->attribute('placeholder', 'DEGREE')
			->attribute('required', 'required')
			->attribute('tabindex', '3')
			->attribute('maxlength', '128')
			->rule('maxLength', 128)
			->required();

		$form->text('url', 'Url')
//			->attribute('placeholder', 'URL')
			->attribute('tabindex', '4')
			->attribute('maxlength', '160')
			->rule('maxLength', 160)
			->rule('url');

		$form->text('dateScore', 'Date')
//			->attribute('placeholder', 'DATE')
			->attribute('required', 'required')
			->attribute('class', 'datepicker')
			->attribute('tabindex', '4')
			->attribute('maxlength', '10')
			->rule('maxLength', 10)
			->required();


		$form->textarea('description', 'Description')
//			->attribute('placeholder', 'DESCRIPTION')
			->attribute('rows', '5')
			->attribute('tabindex', '5')
			->attribute('maxlength', '10000')
			->attribute('class', 'max-10000')
			->rule('maxLength', 10000);

		$form->fieldset('submit', false, array('class' => 'submit'));
		$form->html('cancel', '<a class="btn-roundbrown" href="' . Request::generateUri('profile', 'edit') . '" onclick="return web.cancelEditBlock(this);">Cancel</a>');
		$form->html('save', '<a class="btn-roundblue" href="#" onclick="$(this).closest(\'form\').find(\'input:submit\').click(); return false;">Save changes</a>');
		$form->submit('submit', 'Submit')
			->visible(false);

		$this->form = $form;

		return $this;
	}

	public function setValue($item)
	{
		$values = array(
			'testscore' => $item->testscore_id,
			'occupation' => $item->occupation,
			'score' => $item->score,
			'dateScore' => (!empty($item->dateScore)) ? date('m/d/Y', strtotime($item->dateScore)) : '',
			'description' => $item->description,
			'url' => $item->url,
		);

		$this->form->loadValues($values);
	}

	/**
	 * Get form values and check certification name. If name does not isset in database, created it.
	 *
	 * @return array
	 */
	public function getPost()
	{
		$values = $this->form->getValues();

		if(substr($values['testscore'], 0, 4) == 'new%') {
			$name = substr($values['testscore'], 4);
			$check = Model_TestScores::checkItemByName($name);
			if(!$check){
				$element = Model_TestScores::create(array(
					'name' => $name
				));
				$this->testscore = $element;
				$this->testscore->countUsed = 0;
				$id = $element->id;
			} else {
				$id = $check->id;
				$this->testscore = $check;
			}
		} else {
			$name = $values['testscore'];
			$check = Model_TestScores::checkItemById($name);
			if(!$check){
				$element = Model_TestScores::create(array(
					'name' => $name
				));
				$this->testscore = $element;
				$this->testscore->countUsed = 0;
				$id = $element->id;
			} else {
				$id = $check->id;
				$this->testscore = $check;
			}
		}

		$values['testscore'] = $id;

		return $values;
	}

}