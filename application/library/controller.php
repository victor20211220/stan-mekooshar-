<?php

/**
 * Controller.
 *
 * @version $Id$
 * @package Application
 */

abstract class Controller extends System_Controller
{
	/**
	 * @var string View.
	 */
	protected $view = 'common/template';

	/**
	 * @var string Config.
	 */
	protected $config;

	/**
	 * @var string Cache.
	 */
	protected $cache;

	public function before()
	{
		parent::before();

		$this->view->description = t('description');
		$this->view->keywords = t('keywords');

		$this->settings = View::$global->settings = System::$global->settings = Model_Settings::get();

		/*
		Utilities::compressCss();
		Model_Link::checkRef();
		*/
	}

	/**
	 * Returns session messages.
	 *
	 * @return array
	 */
	public function getMessages()
	{
		$result = false;
		if (false === empty($_SESSION['messages'])) {
			$result = true;
			View::$global->messages = (array) $_SESSION['messages'];
			unset($_SESSION['messages']);
		}

		return $result;
	}

	/**
	 * Creates a session messages.
	 *
	 * @var string Text or message
	 * @var int Type of message (MESSAGE_INFO|MESSAGE_WARNING|MESSAGE_ERROR)
	 *
	 * @return void
	 */
	public function message($text, $type = MESSAGE_INFO)
	{
		if (empty($_SESSION['messages'])) {
			$_SESSION['messages'] = array(
				array($text, $type)
			);
		} else {
			$_SESSION['messages'][] = array($text, $type);
		}
	}

	/**
	 * Checks whether the user is authorized to access the resource?
	 *
	 * @param string $resource The resource name.
	 * @return void
	 * @throws ForbiddenException
	 */
	protected function authorize($resource, $returnUrl = null)
	{
		$auth = Auth::getInstance();
//
//		ob_start();
//		try {
//			dump($_SESSION);
//		} catch (Exception $e) {
//			ob_end_clean();
//		}
//		$tmp = ob_get_clean();
//		Log::getInstance()->write($tmp, __METHOD__);
		$identity = $auth->getIdentity();


		if (false === $auth->allowed($resource)) {
			if (false === $identity) {
				$_SESSION['ret'] = ($returnUrl ?: Request::$uri . Request::$query); // Return there
				$this->response->redirect($this->config->uri->login);
			}
			throw new ForbiddenException('Access to the controller is denied.');
		}
//		dump($identity, 1);
		if ($identity) {
			$this->user = new Model_User((int)$identity->id);
			if(isset($_SESSION['identity']->isUpdatedConnections)) {
				$_SESSION['identity']->isUpdatedConnections = $this->user->isUpdatedConnections;
				$_SESSION['identity']->countConnections = $this->user->countConnections;
				$_SESSION['identity']->countConnections2 = $this->user->countConnections2;
				$_SESSION['identity']->countConnections3 = $this->user->countConnections3;
			}

			View::$global->user = $this->user;
			View::$global->I = $this->user;

			if ($this->view) {
				View::$global->user = $this->user;
			}

		}
	}

	public function after()
	{
		$debugEnabled = $this->config->__isset('debugEnabled') ? $this->config->debugEnabled : false;
		if($debugEnabled) {
			$this->view->style('/css/dbg.css');
			$this->view->dbg = new View('common/default-dbg');
		} else {
			$this->view->dbg = '';
		}

		parent::after();
	}
}
