<?php

class Form_Updates_AddUpdate extends Form_Main
{
	public $post = false;

	public function __construct()
	{
		$form = new Form('addupdate', false, Request::generateUri('updates', 'index') . Request::getQuery());
		$form->attribute('onsubmit', "return box.submit(this);");

		$form->hidden('selected_image');
		$form->hidden('type', POST_TYPE_TEXT);

		$form->fieldset('fields', false, array('class' => 'customform on-white customform-label'));

		$obj = $this;

		$form->text('titletext', 'Title')
			->visible(false)
//			->attribute('placeholder', 'TITLE')
			->attribute('tabindex', '0')
			->attribute('maxlength', '128')
			->rule('maxLength', 128);

		$form->textarea('text', 'Comment')
//			->attribute('placeholder', 'COMMENT')
			->attribute('rows', '4')
			->attribute('tabindex', '1')
			->attribute('maxlength', '800')
			->attribute('required', 'required')
			->attribute('class', 'max-800')
			->rule('maxLength', 800)
			->before(function($field) use($obj) {

				$type = $obj->getType();
				if($type == POST_TYPE_TEXT) {
					$field->required();
				}
			});

		$form->fieldset('urldata', false, array('class' => 'customform on-white customform-label hidden updateUrlData'));
		$form->text('title', 'Title')
//			->attribute('placeholder', 'TITLE')
			->attribute('tabindex', '2')
			->attribute('maxlength', '128')
			->rule('maxLength', 128);

		$form->textarea('urltext', 'Comment')
//			->attribute('placeholder', 'COMMENT')
			->attribute('rows', '4')
			->attribute('tabindex', '3')
			->attribute('maxlength', '800')
			->attribute('class', 'max-800')
			->rule('maxLength', 800);

		$form->checkbox('includeImage', false, 'Include image')
			->attribute('class', 'form-checkbox')
			->attribute('onchange', 'web.updateIncludeImages(this);')
			->setValue(TRUE);


		$form->fieldset('submit', false, array('class' => 'submit'));
//		$form->html('attach', '<a class="btn-save icons i-attach" href="#" onclick="$(\'#contentUploaderList\').click();" >Attach file<span></span></a>');

		$form->html('attach', '<a class="btn-roundblue-border icons i-attachhoverwhite" href="#" onclick="$(\'#contentUploaderList\').click();" ><span></span>Attach file</a>');
		$form->html('save', '<a class="btn-roundblue" href="#" onclick="web.updateSubmit(this); $(this).closest(\'form\').find(\'input:submit\').click(); return false;">Share update</a>');
		$form->submit('submit', 'Submit')
			->visible(false);
		$this->form = $form;

		return $this;
	}

	public function getType()
	{
		if(isset($this->post) && $this->post){
			return $this->post->typePost;
		} else {
			if(isset($_SESSION['updates']['isLink']) && !empty($_SESSION['updates']['isLink'])) {
				return POST_TYPE_WEB;
			}
			if(isset($_SESSION['updates']['isPdf']) && !empty($_SESSION['updates']['isPdf'])) {
				return POST_TYPE_PDF;
			}
			if(isset($_SESSION['updates']['isDoc']) && !empty($_SESSION['updates']['isDoc'])) {
				return POST_TYPE_DOC;
			}
			if(isset($_SESSION['uploader-list']) && !empty($_SESSION['uploader-list'])) {
				return POST_TYPE_IMAGE;
			}
		}

		return POST_TYPE_TEXT;
	}

	public function edit($timeline)
	{
		$this->form->attribute('id', 'editupdate');
		$this->form->attribute('action', Request::generateUri('updates', 'edit', $timeline->id) . Request::getQuery());
		$this->form->attribute('onsubmit', "return box.submit(this);");

		$user = Auth::getInstance()->getIdentity();
		switch($timeline->type){
			case TIMELINE_TYPE_POST:
				$post = Model_Posts::getItemByTimeline($timeline, $user->id);
				$this->post = $post;

				switch($post->typePost) {
					case POST_TYPE_TEXT:
					case POST_TYPE_IMAGE:
					case POST_TYPE_PDF:
						$this->form->elements['text']->setValue($post->text);

						break;
					case POST_TYPE_WEB:
						$this->form->elements['title']
							->setValue($post->title)
							->attribute('required', 'required')
							->required();
						$this->form->elements['urltext']
							->setValue($post->text)
							->attribute('required', 'required')
							->required();
						$this->form->elements['includeImage']->visible(false);

						$this->form->fieldsets['urldata']->attributes['class'] = 'customform on-white customform-label updateUrlData';
						$this->form->fieldset('fields');
						$this->form->fieldsets['fields']->attributes['class'] = 'customform on-white customform-label hidden';

						$this->form->elements['text']->visible(false);
						unset($this->form->fieldsets['fields']->elements['text']->attributes['required']);
						break;
				}

				unset($this->form->fieldsets['submit']->elements['attach'], $this->form->fieldsets['submit']->elements['save']);

				break;
			case TIMELINE_TYPE_SHAREPOST:
				$this->form->elements['text']->setValue($timeline->content);
				unset($this->form->fieldsets['submit']->elements['attach'], $this->form->fieldsets['submit']->elements['save']);
				break;
		}

		$this->form->fieldset('submit', false, array('class' => 'submit'));
		$this->form->html('cancel', '<a class="btn-roundbrown" href="' . Request::generateUri('profile', 'edit') . '" onclick="return web.cancelEditBlock(this);">Cancel</a>');
		$this->form->html('save', '<a class="btn-roundblue" href="#" onclick="$(this).closest(\'form\').find(\'input:submit\').click(); return false;">Save</a>');


	}

	public function setUpdateFromCompany ($company_id)
	{
		$this->form->attribute('action', Request::generateUri('companies', 'update', $company_id) . Request::getQuery());
	}

	public function setUpdateFromSchool ($school_id)
	{
		$this->form->attribute('action', Request::generateUri('schools', 'update', $school_id) . Request::getQuery());
	}

	public function setUpdateForGroup ($group_id)
	{
		$this->form->attribute('action', Request::generateUri('groups', 'addDiscussion', $group_id) . Request::getQuery());

		$obj = $this;

		$this->form->elements['titletext']
			->visible(TRUE)
			->attribute('required', 'required')
			->attribute('class', 'update-for-group')
			->before(function($field) use($obj) {

				$type = $obj->getType();
				if($type == POST_TYPE_TEXT) {
					$field->required();
				}
			});
		$this->form->elements['text']
			->attribute('placeholder', 'NEW DISCUSSION')
			->attribute('maxlength', '5000')
			->attribute('class', 'max-5000')
			->rule('maxLength', 5000);
		$this->form->elements['urltext']->attribute('placeholder', 'NEW DISCUSSION');
	}
}