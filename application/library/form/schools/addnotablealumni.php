<?php

class Form_Schools_AddNotableAlumni extends Form_Main
{
	public $countStudents = 0;

	public function __construct($school)
	{
		$form = new Form('addnotablealumni', false, Request::generateUri('schools', 'addNotableAlumni', $school->id));
		$form->attribute('onsubmit', "return box.submit(this);");




		$form->fieldset('fields', false, array('class' => 'customform on-white customform-label'));

		$form->hidden('selectedStudent', false)
			->attribute('class', 'localsearch-user_name-id');

		$students = Model_Profile_Education::getListBySchoolId($school->id, FALSE, TRUE, FALSE);
		$edit_school = array();
		if(isset($_SESSION['edit_school'])) {
			$edit_school = $_SESSION['edit_school'];
		}

		foreach($students['data'] as $key => $student) {
			if(isset($edit_school[$school->id][$student->userId])) {
				unset($students['data'][$key]);
			}
		}

		$this->countStudents = count($students['data']);
		$text = '';
		foreach($students['data'] as $student) {
			$text .= '<li>' . View::factory('parts/userava-more', array(
					'ouser' => $student,
					'avasize' => 'avasize_44',
					'isTooltip' => FALSE,
					'isCustomInfo' => TRUE
				)) . '</li>';
		}

		$text = '<ul class="localsearch localsearch-user_name">' . $text .  '</ul>';
		$form->html('school_students', false, $text)
			->visible(false)
			->attribute('class', 'form-html');


		$form->text('userName', 'Student name')
//			->attribute('placeholder', 'Studen name')
			->attribute('tabindex', '1')
			->attribute('class', 'form-local-search')
			->attribute('data-localsearch-hidden', '.localsearch-user_name-id')
			->attribute('data-localsearch-list', '.localsearch-user_name')
			->attribute('data-localsearch-item', '.userava-name')
			->attribute('required', 'required')
			->required()
			->rule(function ($field) use ($students)  {
				if(!isset($students['data'][$field->fieldset->elements['selectedStudent']->value])) {
					return 'This student is not in your school!';
				}
			});







		$form->fieldset('submit', false, array('class' => 'submit'));
		$form->html('cancel', '<a class="btn-roundbrown" href="' . Request::generateUri('schools', 'updates') . '" onclick="return box.close();">Cancel</a>');
		$form->html('save', '<a class="btn-roundblue" href="#" onclick="$(this).closest(\'form\').find(\'input:submit\').click(); return false;">Add</a>');
		$form->submit('submit', 'Submit')
			->visible(false);

		$this->form = $form;

		return $this;
	}
}