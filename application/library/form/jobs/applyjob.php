<?php

class Form_Jobs_ApplyJob extends Form_Main
{
	public function __construct($job)
	{
		$form = new Form('applyjob', false, Request::generateUri('jobs', 'apply', $job->id) . Request::getQuery());



		$form->hidden('files', '');



		$form->fieldset('fields1', false, array('class' => 'customform on-white customform-label'));
//		$form->html('description_label', false, '<b>Cover letter:</b>')
//			->attribute('class', 'form-html');

		$form->textarea('cover_letter', 'Cover letter')
			->attribute('tabindex', '1')
			->attribute('rows', '5')
			->attribute('maxlength', '10000')
			->attribute('class', 'max-10000')
			->attribute('required', 'required')
			->required()
			->rule('maxLength', 10000);




		$form->fieldset('fields2', false);
		$form->html('attach', '<a class="btn-roundblue-border icons i-attachhoverwhite" href="#" onclick="$(\'#contentUploaderList\').click();" ><span></span>Attach file</a>');
//		$form->html('addindustry', '<a class="icons i-add icon-text" href="' . Request::generateUri('jobs', 'addSearchIndustry') . '" onclick="box.load(this); return false;"><span></span>add</a>');


		$from = Request::get('from', FALSE);
		$id = $job->id;
		if(!$from) {
			$from = 'myJobs';
		}
		if(in_array($from, array('myJobs', 'search'))) {
			$id = false;
		}


		$form->fieldset('submit', false, array('class' => 'submit'));
		$form->html('cancel', '<a class="btn-roundbrown" href="' . Request::generateUri('jobs', $from, $id) . (($from == 'search') ? Request::getQuery() : null) . '">Cancel</a>');
		$form->html('save', '<a class="btn-roundblue" href="#" onclick="$(this).closest(\'form\').find(\'input:submit\').click(); return false;">Apply</a>');
		$form->submit('submit', 'Submit')
			->visible(false);

		$this->form = $form;

		return $this;
	}
}

