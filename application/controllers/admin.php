<?php

class Admin_Controller extends Controller_Admin_Template
{
	public function actionIndex()
	{
		$this->view->title = 'Dashboard';
		$this->view->content = new View('admin/index');
		
		$this->getMessages();
	}
}