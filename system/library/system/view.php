<?php

/**
 * Kit.
 *
 * View class.
 *
 * @version  $Id: view.php 93 2010-07-18 10:50:24Z eprev $
 * @package  System
 */

class System_View
{
	/**
	 * @var stdclass  List of global variables.
	 */
	public static $global;

	/**
	 * Returns a new View object.
	 *
	 * @param string $templat  View's template.
	 * @param array  $data     Array of values.
	 * @return this
	 */
	public static function factory($template, array $data = array())
	{
		return new View($template, $data);
	}


	/**
	 * Static initialization.
	 *
	 * @return void
	 */
	public static function initialize()
	{
		self::$global = new StdClass();
		self::$global->crumbs = array();
	}

	/**
	 * @var string  View's template name.
	 */
	protected $template;

	/**
	 * @var array  Array of local variables.
	 */
	protected $data = array(
		'links'   => array(),
		'scripts' => array()
	);

	/**
	 * Sets the initial view's templare and local data.
	 *
	 * @param string $template  View's template.
	 * @param array  $data      Array of values.
	 * @return void
	 */
	public function __construct($template, array $data = array())
	{
		if (null !== $data && !is_array($data)) {
			throw new InvalidArgumentException('Argument $data must be an associative array.');
		}
		$this->template = $template;
		$this->data = array_replace($this->data, $data);
	}

	/**
	 * PHP Magic method implementation.
	 *
	 * Returns a value of the specified variable name.
	 * Throws InvalidArgumentException if the local variable does not exist.
	 *
	 * @param string $key  Variable name.
	 * @return mixed
	 * @throws InvalidArgumentException
	 */
	public function __get($key)
	{
		if (array_key_exists($key, $this->data)) {
			return $this->data[$key];
		} else {
			throw new InvalidArgumentException('Variable "' . $key . '" does not exist.');
		}
	}

	/**
	 * PHP Magic method implementation.
	 *
	 * Assigns a variable by name.
	 *
	 * @param string $key    Variable name.
	 * @param mixed  $value  Value.
	 * @return void
	 */
	public function __set($key, $value)
	{
		$this->data[$key] = $value;
	}

	/**
	 * PHP Magic method implementation.
	 *
	 * Checks whether the given variable exist?
	 *
	 * @param string $key  Variable name.
	 * @return boolean
	 */
	public function __isset($key)
	{
		return isset($this->data[$key]);
	}


	/**
	 * PHP Magic method implementation.
	 *
	 * Unset local variable.
	 *
	 * @param  string  $key  Variable name.
	 * @return void
	 */
	public function __unset($key)
	{
		unset($this->data[$key]);
	}

	/**
	 * Magic method, returns the output of render().
	 *
	 * @return string
	 * @throws mixed
	 */
	public function __toString()
	{
		ob_start();
		try {
			$this->render();
		} catch (Exception $e) {
			ob_end_clean();
			return ucfirst($e);
		}
		return ob_get_clean();
	}

	/**
	 * Assigns variables.
	 *
	 * @param array $variables  Array of variables.
	 * @return this
	 */
	public function set(array $variables)
	{
		foreach ($variables as $name => $value) {
			$this->data[$name] = $value;
		}
		return $this;
	}

	/**
	 * Assigns a value by reference. The benefit of binding is that values can
	 * be altered without re-setting them. It is also possible to bind variables
	 * before they have values.
	 *
	 * @param   string  $key    Variable name.
	 * @param   mixed   $value  Referenced variable.
	 * @return  View
	 */
	public function bind($key, & $value)
	{
		$this->data[$key] =& $value;
		return $this;
	}

	/**
	 * Renders the view object to a string and outputs it. Global and local data are merged
	 * and extracted to create local variables within the view file.
	 *
	 * Note: Global variables with the same key name as local variables will be
	 * overwritten by the local variable.
	 *
	 * @return void
	 * @throws Exception
	 */
	public function render()
	{
		if (null === $path = System::locate('views/' . $this->template . '.php')) {
			throw new RuntimeException('Template "' . $this->template . '" does not exist.');
		}
		return static::evaluate($path, array_merge((array) self::$global, $this->data));
	}

	/**
	 * Helper for render().
	 *
	 * The view data will be extracted to make local variables. This method
	 * is static to prevent object scope resolution.
	 *
	 * @param string $path  File path.
	 * @param array  $data  Variables.
	 * @return string
	 */
	protected static function evaluate($path, array $data)
	{
		extract($data, EXTR_SKIP);
		include $path;
	}

	/**
	 * Adds Javascript reference to the view.
	 *
	 * @param mixed $uri URI or array of URIs.
	 * @return this
	 */
	public function script($uri)
	{
		if (is_array($uri)) {
			foreach ($uri as $i) {
				$this->script($i);
			}
		} else {
			if(substr($uri, 0, 4) != 'http') {
				$uri = '/resources' . $uri;
			}
			if (false === in_array($uri, $this->data['scripts'])) {
				$this->data['scripts'][] = $uri;
			}
		}
		return $this;
	}

	/**
	 * Adds link reference to the view.
	 *
	 * @param array $uri Link attributes.
	 * @return this
	 */
	public function link(array $attrs)
	{
		if (array_key_exists(0, $attrs)) {
			foreach ($attrs as $i) {
				$this->link($i);
			}
		} else {
			$attrs['href'] = '/resources' . $attrs['href'];
			if (false === array_key_exists($attrs['href'], $this->data['links'])) {
				$this->data['links'][$attrs['href']] = $attrs;
			}
		}
		return $this;
	}

	/**
	 * Adds Stylesheet reference to the view.
	 *
	 * @param mixed $uri URI or array of URIs.
	 * @return this
	 */
	public function style($uri) {
		if (is_array($uri)) {
			foreach ($uri as $i) {
				$this->style($i);
			}
		} else {
			$this->link(array(
				'href' => $uri,
				'rel'  => 'stylesheet',
				'type' => 'text/css'
			));
		}
		return $this;
	}

	/**
	 * Adds part of breadcrumbs.
	 *
	 * @param string $title Title.
	 * @param string $ref   Anchor reference.
	 * @return this
	 */
	public function crumbs($title, $ref = null)
	{
		self::$global->crumbs[] = null !== $ref ? array($title, $ref) : array($title);
		return $this;
	}

}
