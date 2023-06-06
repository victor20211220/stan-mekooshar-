<?php

/**
 * Kit.
 *
 * Controller library.
 *
 * @version  $Id: controller.php 84 2010-07-12 04:45:16Z eprev $
 * @package  System
 */

/**
 * Controller class.
 *
 * <code>
 * class Controller_Index extends Controller
 * {
 *     protected $view     = 'template';
 *     protected $resource = 'public';
 *
 *     public function actionIndex()
 *     {
 *         // User might be authenticated
 *         if ($this->user) {
 *             $this->view->content = 'Hi, ' . $this->user->name . '!';
 *         } else {
 *             $this->view->content = 'Welcome strange.';
 *         }
 *     }
 * }
 * </code>
 *
 * <code>
 * class Controller_Dashboard extends Controller
 * {
 *     protected $view     = 'dashboard/template';
 *     protected $resource = 'dashboard';
 *
 *     public function actionIndex()
 *     {
 *         // User has been authenticated and authorized to access the resource
 *         $this->view->content = 'Hi, ' . $this->user->name . '!';
 *         if (Request::$isAjax) {
 *             // If the response body is set then the view will not be rendered
 *             $this->response->body = json_encode(array('status' => true));
 *         }
 *     }
 * }
 * </code>
 *
 * @package System
 */
abstract class System_Controller
{
	/**
	 * @var  Response  Response that created the controller.
	 */
	public $response;

	/**
	 * @var string View template.
	 */
	protected $view;

	/**
	 * @var string ACL resource.
	 */
	protected $resource;

	/**
	 * @var Model_User Authenticated user.
	 */
	protected $user;
	
	/**
         * @var Render status.
         */
        protected $autoRender;
	
	/**
	 * Creates a new controller instance.
	 *
	 * @return  void
	 */
	public function __construct()
	{
		$this->response = new Response();
	}

	/**
	 * Automatically executed before the controller action.
	 *
	 * @return  void
	 */
	public function before()
	{
		if ($this->view) {
			$this->view = new View($this->view);
		}

		$this->config = Config::getInstance();
		System::setGlobal('config', $this->config);
		$this->autoRender = true;



		if ($this->resource) {
            self::authorize($this->resource);
		}
	}

	/**
	 * Automatically executed after the controller action.
	 *
	 * @return  void
	 */
	public function after()
	{
		// Render the view
                if ($this->view && null === $this->response->body && $this->autoRender) {
                        $this->response->body = (string) $this->view;
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
		$identity = $auth->getIdentity();
		if (false === $auth->allowed($resource)) {
			if (false === $identity) {
				$_SESSION['ret'] = ($returnUrl ?: Request::$uri . Request::$query); // Return there
				$this->response->redirect(Config::getInstance()->uri->login);
			}
			throw new ForbiddenException('Access to the controller is denied.');
		}
		if ($identity) {
			$this->user = new Model_User($identity->id);
			if ($this->view) {
				View::$global->user = $this->user;
			}
		}
	}
}
