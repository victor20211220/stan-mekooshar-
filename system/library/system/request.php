<?php

/**
 * Kit.
 *
 * Request library.
 *
 * @version  $Id: request.php 104 2010-07-26 06:40:47Z eprev $
 * @package  System
 */

class System_Request
{
	/**
	 * @var array  User agents identifiers.
	 */
	protected static $agents = array(
		'browsers' => array(
			'Opera'             => 'Opera',
			'MSIE'              => 'Internet Explorer',
			'Internet Explorer' => 'Internet Explorer',
			'Shiira'            => 'Shiira',
			'Firefox'           => 'Firefox',
			'Chimera'           => 'Chimera',
			'Phoenix'           => 'Phoenix',
			'Firebird'          => 'Firebird',
			'Camino'            => 'Camino',
			'Netscape'          => 'Netscape',
			'OmniWeb'           => 'OmniWeb',
			'Chrome'            => 'Chrome',
			'Safari'            => 'Safari',
			'Konqueror'         => 'Konqueror',
			'Epiphany'          => 'Epiphany',
			'Galeon'            => 'Galeon',
			'Mozilla'           => 'Mozilla',
			'icab'              => 'iCab',
			'lynx'              => 'Lynx',
			'links'             => 'Links',
			'hotjava'           => 'HotJava',
			'amaya'             => 'Amaya',
			'IBrowse'           => 'IBrowse'
		),
		'mobile' => array (
			'mobileexplorer' => 'Mobile Explorer',
			'openwave'       => 'Open Wave',
			'opera mini'     => 'Opera Mini',
			'operamini'      => 'Opera Mini',
			'elaine'         => 'Palm',
			'palmsource'     => 'Palm',
			'digital paths'  => 'Palm',
			'avantgo'        => 'Avantgo',
			'xiino'          => 'Xiino',
			'palmscape'      => 'Palmscape',
			'nokia'          => 'Nokia',
			'ericsson'       => 'Ericsson',
			'blackBerry'     => 'BlackBerry',
			'motorola'       => 'Motorola',
			'iphone'         => 'iPhone',
			'android'        => 'Android',
			'ipad'		 => 'Ipad',
			'ipod'		 => 'Ipod',
		),
	);

	/**
	 * @var string  Used method: GET, POST, PUT, DELETE, etc.
	 */
	public static $method = 'GET';

	/**
	 * @var string  Used protocol: http, https.
	 */
	public static $protocol = 'http';

	/**
	 * @var string  Virtual Host.
	 */
	public static $host;

	/**
	 * @var string  Referring URL
	 */
	public static $referrer;

	/**
	 * @var string  User agent.
	 */
	public static $userAgent;

	/**
	 * @var string  Remote client's IP address
	 */
	public static $remoteAddress = '0.0.0.0';

	/**
	 * @var boolean  AJAX request.
	 */
	public static $isAjax = false;

	/**
	 * @var string  Query string.
	 */
	public static $query;

	/**
	 * @var string  URI of the request.
	 */
	public static $uri;

	/**
	 * @var string  URI of the requested controller.
	 */
	public static $controller;

	/**
	 * @var string  URI of the requested action.
	 */
	public static $action;

	/**
	 * @var string  URI of the requested params.
	 */
	public static $params;
	public static $controllerclass;

	/**
	 * Static initialization.
	 *
	 * @return void
	 */
	public static function initialize()
	{
		global $argc, $argv;

		if (isset($_SERVER['REQUEST_METHOD'])) {
			self::$method = $_SERVER['REQUEST_METHOD'];
		}

		if (false == empty($_SERVER['QUERY_STRING'])) {
			self::$query = '?' . trim($_SERVER['QUERY_STRING'], '&/');
		}

		if (false == empty($_SERVER['HTTPS']) && filter_var($_SERVER['HTTPS'], FILTER_VALIDATE_BOOLEAN)) {
			self::$protocol = 'https';
		} else {
			Config::getInstance()->protocol;
		}

		if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
			self::$isAjax = true;
		}

		if (isset($_SERVER['HTTP_REFERER'])) {
			self::$referrer = $_SERVER['HTTP_REFERER'];
		}

		if (isset($_SERVER['HTTP_USER_AGENT'])) {
			self::$userAgent = $_SERVER['HTTP_USER_AGENT'];
		}

		if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			self::$remoteAddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
			self::$remoteAddress = $_SERVER['HTTP_CLIENT_IP'];
		} elseif (isset($_SERVER['REMOTE_ADDR'])) {
			self::$remoteAddress = $_SERVER['REMOTE_ADDR'];
		}

		if (isset($_SERVER['HTTP_HOST'])) {
			self::$host = $_SERVER['HTTP_HOST'];
		} else {
			self::$host = Config::getInstance()->host;
		}

		if (System::$inCli) {
			if ($argc == 2) {
				$uri = $argv[1];
			} else {
				$uri = '/';
			}
		} else {
			if (isset($_SERVER['PATH_INFO']) && !empty($_SERVER['PATH_INFO'])) {
				$uri = $_SERVER['PATH_INFO'];
			} else {
				$uri = $_SERVER['REQUEST_URI'];
				if (0 === strpos($uri, $_SERVER['SCRIPT_NAME'])) {
					$uri = substr($uri, strlen($_SERVER['SCRIPT_NAME']));
				}
				if(false !== strpos($uri, '?')) {
					$uri = substr($uri, 0, strpos($uri, '?'));
				}
			}
		}
		$uri = preg_replace('#//+#', '/', $uri);
		self::$uri = $uri;
	}

	/**
	 * Returns TRUE if there is POST request.
	 *
	 * @return boolean
	 */
	public static function isPost()
	{
		return 'POST' == self::$method;
	}

	/**
	 * Returns TRUE if there is AJAX request.
	 *
	 * @return boolean
	 */
	public static function isAjax()
	{
		return self::$isAjax;
	}

	/**
	 * Returns controller's name, action, parameters and parameters for the URI.
	 *
	 * @param  string  $uri  URI to parse.
	 * @return array|false
	 */
	public static function route($uri)
	{
		$uri = trim($uri, '/');

		if ('' === $uri) {
			$uri = 'index';
		}

		$ctrlKey  = 0;
		$ctrlPath = '';
		$ctrlSubpath = '';

		$segments =  explode('/', $uri);
		$subpath  = '';
		foreach ($segments as $key => $segment) {
			$subpath .= $segment;
			$continue = false;
			foreach (System::$paths as $path) {
				$path .= 'controllers/';
				if (is_dir($path . $subpath)) {
					$continue = true;
				}
				if (is_file($path . $subpath . '.php')) {
					$continue = true;
					$ctrlKey  = $key;
					$ctrlPath = $path;
					$ctrlSubpath = $subpath;
					break;
				}
			}
			if (false == $continue) {
				break;
			}
			$subpath .= '/';
		}

		if ($ctrlPath) {
			if (array_key_exists($ctrlKey + 1, $segments)) {
				$action = $segments[$ctrlKey + 1];
				$params = array_map(function($param) {
					return ctype_digit($param) ? (int) $param : $param;
				}, array_slice($segments, $ctrlKey + 2));

			} else {
				$action = 'index';
				$params = array();
			}
			self::$params = $params;
			return array(
				'controller' => str_replace('/', '_', $ctrlSubpath),
				'action'     => $action,
				'params'     => $params,
				'path'       => $ctrlPath . $ctrlSubpath . '.php',
				'uri'        => $ctrlSubpath
			);
		} else {
			$params = $segments;
			$action = array_shift($params);
			self::$params = $params;
			return array(
				'controller' => 'index',
				'action'     => $action,
				'params'     => $params,
				'path'       => APPLICATION_PATH . 'controllers/index.php',
				'uri'        => 'index'
			);
		}

		return false;
	}

	/**
	 * Executes controller by the given URI.
	 *
	 * @param  string  $uri  URI to execute.
	 * @return Response
	 * @throws NotFoundException
	 */
	public static function execute($uri)
	{
		// Uncommit when issue with GET paramerts
//		$uri = explode('?', $uri);
//		$uri = $uri[0];
//		$uri = rtrim($uri, '/') . '/';

		if (false === $exec = self::route($uri)) {
			throw new NotFoundException('Controller does not exist.');
		}
		if (null === self::$controller) {
			self::$controller = '/' . $exec['uri'] . '/';
		}
		if (null === self::$action) {
			self::$action = $exec['action'];
		}
		require $exec['path'];
		try {
			$class = new ReflectionClass($exec['controller'] . '_controller');
			$controller = $class->newInstance();
			self::$controllerclass = $controller;
			$class->getMethod('before')->invoke($controller);
			$action = 'action' . str_replace('-', '', $exec['action']);
			if ($class->hasMethod($action)) {
				$class->getMethod($action)->invokeArgs($controller, $exec['params']);
			} else {
				$class->getMethod('__call')->invoke($controller, $exec['action'], $exec['params']);
			}
			$class->getMethod('after')->invoke($controller);
		} catch (ReflectionException $e) {
			throw new NotFoundException(ucfirst($e));
		}
		return $controller->response;
	}

	/**
	 * Returns information about the client browser.
	 *
	 * @param  string  $value  Value to return: browser, version, mobile.
	 * @return mixed
	 */
	public static function getUserAgent($value)
	{
		static $info = array();

		if (array_key_exists($value, $info)) {
			return $info[$value];
		}

		if ($value == 'browser' || $value == 'version') {
			foreach (self::$agents['browsers'] as $key => $name) {
				if (false !== stripos(self::$userAgent, $key)) {
					$info['browser'] = $name;
					if (preg_match('#' . preg_quote($key) . '[^0-9.]*+([0-9.][0-9.a-z]*)#i', self::$userAgent, $matches)) {
						$info['version'] = $matches[1];
					} else {
						$info['version'] = false;
					}
					return $info[$value];
				}
			}
		} else {
			foreach (self::$agents[$value] as $key => $name) {
				if (false !== stripos(self::$userAgent, $key)) {
					return $info[$value] = $name;
				}
			}
		}

		return $info[$value] = false;
	}

	/**
	 * Returns value of the given request parameter name.
	 *
	 * @param string $name    Parameter name.
	 * @param mixed  $default Default value (optional).
	 * @param mixed  $filter  Filter, e.g. possible values (optional).
	 * @return string
	 * @throws RuntimeException
	 */
	public static function get($name, $default = null, $filter = null)
	{
		if (array_key_exists($name, $_REQUEST)) {
			$value = $_REQUEST[$name];
			if (is_array($filter) && false === in_array($value, $filter)) {
				throw new RuntimeException('Illegal value for parameter "' . $name . '".');
			}
			return $value;
		} else {
			if (null === $default) {
				throw new RuntimeException('Parameter "' . $name . '" does not exist.');
			}
			return $default;
		}
	}
}
