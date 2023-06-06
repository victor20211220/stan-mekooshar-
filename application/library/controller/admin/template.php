<?php

/**
 * Admin Template Controller.
 *
 * @version  $Id: template.php 2 2009-10-02 23:06:43Z perfilev $
 * @package  Application
 */

abstract class Controller_Admin_Template extends Controller
{
	/**
	 * @var  string  Page template.
	 */
	protected $view = 'admin/template';
	protected $resource = 'dashboard';

	/**
	 * Authenticates user.
	 *
	 * @return void
	 */
	public function before()
	{
		parent::before();
		
		if(Request::getUserAgent('browser') == 'Internet Explorer' && Request::getUserAgent('version') < 9) {
			$this->message('Please update your browser!', MESSAGE_ERROR);
		}
		if(Request::getUserAgent('mobile') == 'iPad') {
			// fixes for mobile browsers
			$this->message('You are using mobile device. Some functions are not available.', MESSAGE_ERROR);
		}
		
		$this->view->active = false;

		$this->view->script('/js/jquery/jquery.js');
//		$this->view->script('/js/jquery/jquery-ui.js');
//		$this->view->script('/js/jquery/jquery-ui.js');
		$this->view->script('/js/admin.js');
//		$this->view->script('/js/directory.js');
		
		$this->view->script('/js/eva.js');
		$this->view->style('/css/eva.css');
		$this->view->style('/css/style.css');
		$this->view->style('/css/autoform.css');
		$this->view->style('/css/admin.css');
	}
}
