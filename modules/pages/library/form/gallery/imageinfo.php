<?php

/**
 *
 * EvaProperty
 *
 * @author UkieTech Corporation
 * @copyright Copyright UkieTech Corp. (http://ukietech.com/)
 * @link http://pme.myevasystem.com/
 *
 */
class Form_Gallery_ImageInfo
{
	public function __construct($action = false)
	{
		$form = new Form('edit-image-info', false, $action ? $action : Request::$uri . Request::$query);
		$form->attribute('class', 'form-pages');
		$form->attribute('onsubmit', 'return pagesubmit(this);');
		
//		$form->fieldset->legend('Image texts');
		
		$form->text('title', 'Title')
			->rule('maxLength', 255);

		$form->text('alternative', 'Alternative text')
			->rule('maxLength', 255);

		$form->textarea('text', 'Description')
			->attribute('rows', 7);


		$form->fieldset('customFieldset', false, array('class' => 'hidden'));


		$form->fieldset('submit')
			->attribute('class', 'fieldset-submit');
		
		$form->submit('submit', 'Save')
			->attribute('class', 'btn btn-ok');

		$this->form = $form;

		return $this;
	}

	public function edit($values)
	{
//		$itemId = $this->item ? $this->item['file_id'] : null;

//		if (!empty ($itemId)) {
			$this->form->loadValues($values);
//		}

		return $this;
	}

	public function addMemberFiled()
	{
		$form =& $this->form;
		$form->fieldset('customFieldset');
		$form->fieldsets['customFieldset']->attribute('class', 'line-top');

		$form->text('memberName', 'Member name')
			->attribute('required', 'required')
			->attribute('maxlength', '64')
			->rule('maxLength', 64)
			->required();

		$form->text('memberTitle', 'Member title')
			->attribute('required', 'required')
			->attribute('maxlength', '128')
			->rule('maxLength', 128)
			->required();


		$form->textarea('memberDescription', 'About member')
			->attribute('required', 'required')
			->attribute('rows', '5')
			->attribute('maxlength', '10000')
			->rule('maxLength', 10000)
			->required();
	}
}
