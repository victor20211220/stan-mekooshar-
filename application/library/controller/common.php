<?php

/**
 * Admin Template Controller.
 *
 * @version  $Id: template.php 2 2009-10-02 23:06:43Z perfilev $
 * @package  Application
 */

abstract class Controller_Common extends Controller
{
	/**
	 *
	 * @var bool Is browser old?
	 */
	protected $oldBrowser;

	/**
	 * Authenticates user.
	 *
	 * @return void
	 */
	public function before()
	{

		// check old browser
		$browser = Request::getUserAgent('browser');
		$version = (float) Request::getUserAgent('version');

		if(
			$browser == 'Firefox' && $version < 5 ||
			$browser == 'Internet Explorer' && $version < 8 ||
			$browser == 'Opera' && $version < 9 ||
			$browser == 'Safari' && $version < 4 ||
			$browser == 'Chrome' && $version < 5
		) {
			$this->view = 'common/template-oldBrowser';
			$this->oldBrowser = View::$global->oldBrowser = true;
		}

		parent::before();

		// send responce if old browser
		if($this->oldBrowser) {
			$this->after();
//			$this->view->title = $this->settings['title'];
//			$response = new Response(200, $this->view);
//			$response->send(); exit(0);
		}

		// check mobile device
		if(!System::$inCli) {
			$this->view->mobile = Request::getUserAgent('mobile') ? true : false;
		}


//		dump($_SESSION);

		$auth = Auth::getInstance();
		$user = false;
		if($auth->hasIdentity()) {
			$user = $auth->getIdentity();
		}
		View::$global->user = $user;
		View::$global->I = $user;

		$f_login = new Form_Login();
		$this->view->f_login = $f_login->form;

		$this->view->script('/js/jquery/jquery.js');
		$this->view->script('/js/libs/bootstrap/bootstrap.min.js');
		$this->view->script('/js/libs/bootstrap/bootstrap-select.min.js');
//		$this->view->script('/js/libs/bootstrap/select2.min.js');
		$this->view->script('/js/libs/selectize.min.js');
		$this->view->script('/js/libs/ui/jquery.ui.core.js');
		$this->view->script('/js/libs/ui/jquery.ui.widget.js');
		$this->view->script('/js/libs/ui/jquery.ui.position.js');
		$this->view->script('/js/libs/ui/jquery.ui.tooltip.js');
		$this->view->script('/js/libs/ui/jquery.ui.datepicker.js');
		$this->view->script('/js/libs/jquery.Jcrop.min.js');
		$this->view->script('/js/libs/jquery.backstretch.js');


//		$this->view->script('/js/libs/ui/i18n/jquery.ui.datepicker-en-AU.min.js');
		$this->view->script('/js/libs/jquery.autosize.min.js');
		$this->view->script('/js/crop.js');
		$this->view->script('/js/website.js');
		$this->view->script('/js/system.js');


		$this->view->style('/css/libs/jquery.Jcrop.css');
		$this->view->style('/css/libs/bootstrap/bootstrap-select.min.css');
		$this->view->style('/css/libs/bootstrap/bootstrap.min.css');
//		$this->view->style('/css/libs/bootstrap/select2-bootstrap.css');
		$this->view->style('/css/libs/selectize.bootstrap3.css');
		$this->view->style('/css/libs/ui/jquery-ui-1.10.4.custom.min.css');
		$this->view->style('/css/template.css');
		$this->view->style('/css/website.css');

//		$this->view->style('/css/libs/fileuploader.css');

		if(Request::$controller == '/index/' && in_array(strtolower(Request::$action), array('support', 'index', 'about', 'policy', 'advertisewithus', 'searchpeople'))) {
			$this->view->page = 'page_' . strtolower(Request::$action);
		}
	}

	public function after()
	{
		if(!isset($this->view->notifications)) {
			$this->view->notifications = array('data' => array());
		}

		$title = isset($this->view->title) ? $this->view->title : (!empty($this->view->crumbs) ? end($this->view->crumbs) : false);
		$title = $title ? implode(t('title_separator'), array($title, $this->settings['title'])) : $this->settings['title'];

		$this->view->title = $title;

		if (Request::$isAjax && $this->autoRender) { // && Request::get('load', false)
			$response['html'] = array(
				'.content' => (string) $this->view->content,
				'title' => $this->view->title
			);

			$this->autoRender = false;
			$this->response->setHeader('Content-Type', 'text/json');
			$this->response->body = json_encode($response);
		} else {
			$this->getMessages();
		}


		parent::after();
//		dump($_SESSION, 1);
	}
}
