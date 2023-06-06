<?php

class Admin_Categories_Controller extends Controller_Admin_Template
{
	public function before()
	{
		parent::before();
		
		$this->view->active = 'categories';
	}

	public function actionIndex()
	{
		$this->view->crumbs('Manage categories');
		$this->view->content = $content = new View('admin/categories/list');

		$content->items = Model_Page_Category::getListCategories();
	}

	public function actionAdd()
	{
		return $this->actionEdit();
	}


	public function actionEdit($id = FALSE)
	{

		$this->view->crumbs('Manage category');

		$f_Categories = new Form_Categories();

		if($id) {
			$category = Model_Page_Category::getItemById($id);
			$f_Categories->edit($category);
		}

		if(Request::isPost()) {
			if($f_Categories->form->validate()) {
				$values = $f_Categories->form->getValues();

				if($id) {
					Model_Page_Category::update($values, $category->id);
					$this->message('Category has been updated');
				} else {
					Model_Page_Category::create($values);
					$this->message('New category has been created');
				}
				$this->response->redirect(Request::generateUri('admin', 'categories'));
			}
		}

		$this->view->content = new View('admin/categories/form', array('form' => $f_Categories->form));
		$this->getMessages();
	}

	public function actionRemove($id)
	{
		$category = Model_Page_Category::getItemById($id);
		Model_Page_Category::remove($category->id);

		$this->response->redirect(Request::generateUri('admin', 'categories'));
	}
}
