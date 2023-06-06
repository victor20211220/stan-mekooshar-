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
class Form_Page
{
	public $isBanner = FALSE;
	public function __construct($action = false)
	{
		$form = new Form('page', false, $action ? $action : Request::$uri . Request::$query);

		$form->fieldset('default', 'pages', array('class' => 'no-stripes'));
		$form->text('title', 'Title')
			->width('600px')
			->required()
			->attribute('required', 'required')
			->rule('maxLength', 256);

		$form->checkbox('isPublic', 'Visible');

		$categories = array();
		$result = Model_Page_Category::getListCategories();
		foreach($result['data'] as $item) {
			$categories[$item->id] = $item->name;
		}

		$form->select('category', $categories, 'Category');
		$form->html('btn_add_category', false, '<a href="' . Request::generateUri('admin', 'categories') . '" target="_blank">Edit categories</a>');


		$form->textarea('text', 'Content')
			->visible(false)
			->attribute('rows', 20);

		$obj = $this;

		$form->html('textcke', 'Content', '<div class="ckeditor" contenteditable="true"></div>')
			->rule(function($field) use($obj) {
				if(!$obj->isBanner && empty($field->fieldset->form->elements['text']->value)) {
					return 'This fieeld is required!';
				}
			});

		$form->fieldset('fields_1');

		$form->fieldset('submit', false, array('class' => 'no-stripes'));
		$form->submit('submit', 'Save')
			->attribute('onclick', '$("#page-text").val(CKEDITOR.instances["editor1"].getData());')
			->attribute('eva-content', 'Save changes')
			->attribute('class', 'btn btn-ok');

		$this->form = $form;

		return $this;
	}

	public function setId($id)
	{
		$this->form->attribute('data-id', $id);
	}

	public function setData($values)
	{
		$this->form->loadValues($values);
//		dump($values['text'], 1);
		$this->form->elements['textcke']->setValue('<div class="ckeditor" contenteditable="true">' . $values['text'] .  '</div>');
	}

//	public function loadConfig($category = 'static')
//	{
//
//	}

	public function addTitle1()
	{
		$form =& $this->form;
		$form->fieldset('default');
		$form->text('title1', 'Title in text')
			->width('600px')
			->rule('maxLength', 255);

		$elements = array_keys($form->fieldsets['default']->elements);
		end($elements);
		$key = key($elements);
		$key2 = array_search('title', $elements);

		if($key !== FALSE && $key2 !== FALSE) {
			$text = Utilities::moveElement($form->fieldsets['default']->elements, $key, ($key2 + 1));
		}
	}

	public function setBanner($id = FALSE, $src = false, $data = FALSE)
	{
		if($data){
			$data = unserialize($data);
		}
//		dump($data, 1);

		$this->isBanner = TRUE;
		$form =& $this->form;
		$form->fieldset('default');

		$form->elements['category']->visible(false);
		$form->elements['btn_add_category']->visible(false);
		$form->html('textcke', '', '')->visible(false);

		$form->select('banner_type', array(
			1 => 'Size 580x90',
			2 => 'Size 330x80 (top)',
			3 => 'Size 330x80 (bottom)'
		), 'Banner type');
		if($data) {
			$form->elements['banner_type']->setValue($data['banner_type']);
		}

		$view = View::factory('admin/file_one', array(
			'parent_id' => $id,
		    'src' => $src
		));
		$form->html('fileUpl', 'Upload file',(string)$view);

		$form->text('weburl', 'Web link')
			->required()
			->attribute('required', 'required')
			->attribute('maxLength', '255')
			->rule('maxLength', 255)
			->rule('url')
			->setValue($data['weburl']);


		$form->fieldset('fields_1');
		$form->fieldsets['fields_1']->attribute('class', 'fieldset_countries');


		$countries = array('all' => 'Select all', 'no' => 'Profile not have selected country') + t('countries');
		$i = 0;
		foreach($countries as $key => $country) {
			$i++;
			$title_text = '';
			if($i == 1) {
				$title_text = 'Show for country';
			}

			$setValue = false;
			if($data && in_array($key, $data['countries'])) {
				$setValue = TRUE;
			}
			$form->checkbox('country_' . $key, $title_text, $country)->attribute('data-country', $key)->setValue($setValue);
		}

		if(isset($form->elements['country_all'])) {
			$form->elements['country_all']->attribute('onchange', 'pages.pageSelectAllCountries(this)');
		}
	}
}
