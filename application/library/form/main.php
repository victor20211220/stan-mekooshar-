<?php

class Form_Main
{
	public $form;

	/**
	 * Default list items, if the field is empty.
	 *
	 * @var array - List items
	 */
	protected $resultFromDBListItems = array();

	/**
	 * Generated html code with list items
	 *
	 * @var string
	 */
	protected $htmlItems = '';

	/**
	 * Url to next page in autocomplete
	 *
	 * @var string - URL to next page
	 */
	protected $listNextBnt = '';

	/**
	 * List items as array, where key = id and value = name
	 *
	 * @var array - List items
	 */
	protected $listItems = array();



	/**
	 *
	 */
	public function generateAutocomplete($name, $firstField = '', $title = false, $value = false, $resultFromDBListItems, $autocompleteLink = FALSE, $allowAddNewPosition = true)
	{
		$this->resultFromDBListItems = $resultFromDBListItems;

		// Build default list
		foreach($this->resultFromDBListItems['data'] as $item) {
			$this->listItems[$item->id] = $item->name;
		}

		// Generate list
		// --------------------------
		$text = '';
		foreach($this->resultFromDBListItems['data'] as $id => $item) {
			$this->htmlItems .= '<li data-itemid="' . $id . '" data-itemtitle="' . Html::chars($item->name) . '" data-itemorder="' . $item->countUsed . '">' . Html::chars($item->name) . '</li>';
		}
		$this->htmlItems = '<ul class="selectize-customitems">' . $this->htmlItems .  '</ul>';

		if(isset($this->resultFromDBListItems['paginator']['next']) && !empty($this->resultFromDBListItems['paginator']['next'])) {
			$query = '';
			if(strpos($this->resultFromDBListItems['paginator']['next'], '?')) {
				$query = substr($this->resultFromDBListItems['paginator']['next'], strpos($this->resultFromDBListItems['paginator']['next'], '?'));
			}
			$this->listNextBnt = Request::generateUri('autoComplete', $autocompleteLink) . $query;
		} else {
			$this->listNextBnt = '';
		}
		// --------------------------



		$currentFieldset = $this->form->fieldset->name;
		$this->form->fieldset('default');

		// Create object list
		$this->form->html('list', false, $this->htmlItems)
			->visible(false)
			->attribute('class', 'form-html');


		$this->form->fieldset($currentFieldset);
		$obj = $this->form->select($name, array('' => $firstField) + $this->listItems, $title, $value)
			->attribute('class', 'selectize')
//			->attribute('tabindex', $i)
			->attribute('maxlength', '128')
			->rule('maxLength', 128)
			->attribute('data-selectize-customitems', '.selectize-customitems')
			->attribute('data-selectize-url', ($autocompleteLink ? Request::generateUri	('autoComplete', $autocompleteLink) : '')) // For find text
			->attribute('data-selectize-url-next-page', $this->listNextBnt)
			->attribute('data-selectize-order', 'true')
			->attribute('data-selectize-add-new-position', ($allowAddNewPosition ? 'true' : 'false'));

		return $obj;
	}

	public function __construct()
	{

	}

	public function setIndex($fieldName)
	{
		$this->form->fieldset('default');
		$elementName = '#' . $this->form->attributes['id'] . '-' . $fieldName;
		$this->form->html('settabindex', false, '<script type="text/javascript">$(document).ready(function(){$(\'' . $elementName . '\').focus()})</script>')
			->visible(false);
	}

	public function setAjaxSendData()
	{
		$form =& $this->form;
		$form->attribute('onsubmit', '$(this).parent().addClass(\'is-preloader\'); return web.submitForm(this);');
	}



}