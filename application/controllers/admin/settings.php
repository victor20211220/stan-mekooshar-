<?php

class Admin_Settings_Controller extends Controller_Admin_Template
{
	public function before()
	{
		parent::before();
		
		$this->view->title = 'Website settings';
		$this->view->active = 'settings';
	}

	public function actionIndex()
	{
		$form = new Form('item');
		$form->labelWidth = '220px';
		
		$settings = Model_Settings::get(true);
		
		// sorting
		if (Request::$isAjax && isset($_POST['sorting'])) {
			foreach ($_POST['settings'] as $k => $v) {
				$position = ($k + 1);
				if ($settings[$v]->position != $position) {
					Model_Settings::update(array('position' => $position), array('`key` = ?', $v));
				}
			}
			$this->autoRender = false;
			$this->response->setHeader('Content-Type', 'application/json');
			$this->response->body = json_encode(array('answer' => 'Changes has been saved'));
			return;
		}
		
		foreach ($settings as $setting) {
			if ($setting->root && $this->user->role != 'root') {
				continue;
			}
			switch ($setting->type) {
				case 'text':
					$form->text($setting->key, $setting->name)->attribute('size', 40);
					break;
				case 'checkbox':
					$form->checkbox($setting->key, '', $setting->name)->attribute('size', 40);
					break;
				case 'select':
					if ($setting->options) {
						$options = array();
						foreach (json_decode($setting->options) as $k => $v) {
							$options[$k] = $v;
						}
					}
					$form->select($setting->key, $options, $setting->name);
					break;
			}
			if ($setting->rules) {
				foreach (json_decode($setting->rules) as $rule => $param) {
					$form->elements[$setting->key]->rule($rule, $param);
				}
			}
		}
		$form->submit('submit', 'Save')
			->attribute('eva-content', 'Save changes')
			->attribute('class', 'btn btn-ok');
		
		
		$form->loadValues($this->settings);

		if ('POST' == Request::$method) {
			if ($form->validate()) {
				$values = $form->getValues();
				foreach ($values as $k => $v) {
					Model_Settings::update(array('value' => $v), array('`key` = ?', $k));
				}
				
				$this->message(t('changes_saved'));
				$this->response->redirect(Request::$uri);
			}
		}
		
		$this->view->content = $content = new View('admin/settings');
		$content->form = $form;
		$content->backUrl = '/admin/';
		
		if ($this->user->role == 'root') {
			$content->options = $settings;
		}
		
		$this->getMessages();
	}

	public function actionAdd()
	{
		$this->actionEdit();
	}

	public function actionEdit($key = null)
	{
		if ($this->user->role != 'root') {
			throw new ForbiddenException('You are not authorized for this action');
		}
		if (!$key) {
			$this->view->title = 'Add new settings option';
		} else {
			$this->view->title = 'Edit settings option';
			$option = new Model_Settings($key);
		}
		$form = new Form('newOption');
		$form->text('name', 'Label')
			->attribute('size', 40)
			->attribute('required', 'required')
			->required();
		$form->text('key', 'Key')
			->attribute('required', 'required')
			->attribute('size', 40)
			->rule(function($field) use($key) {
				if(!preg_match('/^[a-zA-Z0-9.-]+$/us', $field->value)) {
					return 'Only letters, numbers, "-" and "." are allowed';
				}

				if (isset($key)) {
					if (Model_Settings::query(array(
					    'select' => '1',
					    'where' => array('`key` = ? AND `key` != ?', $field->value, $key)
					))->fetch()) {
						return Text::DUPLICATE_KEY;
					}
				} else {
					if (Model_Settings::query(array(
					    'select' => '1',
					    'where' => array('`key` = ?', $field->value)
					))->fetch()) {
						return Text::DUPLICATE_KEY;
					}
				}
			})
			->required();
		$form->checkbox('root', '', 'Visible to root only')->attribute('size', 40);
		$form->select('type', array(
			'text' => 'Text',
			'select' => 'Select',
			'checkbox' => 'Checkbox',
		), 'Control type');
		$form->textarea('rules', 'Rules (JSON)')->attribute('cols', 80)->attribute('rows', 10);
		$form->textarea('options', 'Options (JSON)')->attribute('cols', 80)->attribute('rows', 10);
		$form->submit('submit', 'Save')
			->attribute('eva-content', 'Save changes')
			->attribute('class', 'btn btn-ok');
		
		if ($key) {
			$form->loadValues($option->getValues());
		}
		if ('POST' == Request::$method) {
			if ($form->validate()) {
				if ($key) {
					$values = $form->getValues();
					
					Model_Settings::update($values, array('`key` = ?', $key));
					
					$this->message('Option has been changed');
				} else {
					$values = $form->getValues();
					Model_Settings::create($values);
					
					$this->message('Option has been created');
				}
				$this->response->redirect(Request::$controller);
			}
		}
		
		$this->view->crumbs('Website settings', Request::$controller);
		$this->view->content = $content = new View('admin/settings');
		
		$content->form = $form;
		$content->backUrl = Request::$controller;
		
		if($key) {
			$this->view->crumbs('Edit option');
		} else {
			$this->view->crumbs('Add new option');
		}
		
		$this->getMessages();
	}

	public function actionRemove($key)
	{
		if ($this->user->role != 'root') {
			throw new ForbiddenException('You are not authorized for this action');
		}
		
		Model_Settings::remove(array('`key` = ?', $key));
		$this->message('Option has been removed');
		
		$this->response->redirect(Request::$controller);
	}
}
?>