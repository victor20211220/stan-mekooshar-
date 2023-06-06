<?php

class Form_Schools_FindStudentsAlumni extends Form_Main
{
	public $schools = FALSE;

	public function __construct($school_id)
	{
		$form = new Form('findstudentsalumni', false, Request::generateUri('schools', 'studentsAlumni', $school_id));

		$form->fieldset('fields', false, array('class' => 'customform on-white customform-label'));

		$form->text('year', false)
			->attribute('placeholder', 'GRADUATION YEAR')
			->attribute('tabindex', '1')
			->attribute('maxlength', '4');

		$form->text('find', false)
			->attribute('tabindex', '2')
			->attribute('maxlength', '64')
			->attribute('placeholder', 'SEARCH');

		$form->fieldset('submit', false, array('class' => ''));
		$form->html('submit', '<a class="btn-roundblue icons i-search" href="#" onclick="return web.searchMemberInSchool(this);"><span></span></a>');

		$this->form = $form;

		return $this;
	}

	public function setAllschool()
	{
		$form =& $this->form;
		$form->attribute('action', Request::generateUri('schools', 'viewYourSchool'));

		$form->fieldset('fields2', false, array('class' => 'customform on-white customform-label'));

		$user = Auth::getInstance()->getIdentity();
		$result_school = Model_Profile_Education::getListByUser($user->id);
		$schools = array();
		foreach($result_school['data'] as $school){
			$schools[$school->university_id] = $school->universityName;
		}

		$this->schools = $schools;

		if(!empty($schools)) {
			$form->select('school', array('' => '', 'all' => 'All') + $schools, false)
				->attribute('class', 'bootstripe')
				->attribute('tabindex', '3')
				->attribute('maxlength', '1')
				->rule('maxLength', 1)
				->setValue('all');
		}

	}
}