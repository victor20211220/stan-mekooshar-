<?php

/**
 * Kit.
 *
 * ACL library.
 *
 * @version $Id: acl.php 54 2010-06-24 06:54:24Z eprev $
 * @package System
 */

class System_Acl
{
	/**
	 * @var array  Class instance.
	 */
	protected static $instance;

	/**
	 * Returns an instance of class.
	 *
	 * @return object
	 */
	public static function getInstance()
	{
		if (null == self::$instance) {
			self::$instance = new static();
		}
		return self::$instance;
	}

	/**
	 * @var array  Roles.
	 */
	protected $roles;

	/**
	 * @var array  Resources.
	 */
	protected $resources;

	/**
	 * @var array  Rules.
	 */
	protected $rules = array();

	/**
	 * Consrtuctor.
	 *
	 * @param System_Config $config ACL config.
	 * @return this
	 */
	public function __construct()
	{
		$acl = Config::getInstance()->acl;
		$this->roles = $acl->roles->arrayize();
		if (false === array_key_exists('guest', $this->roles)) {
			$this->roles['guest'] = null;
		}
		$this->resources = $acl->resources->arrayize();
		foreach ($acl->allow->arrayize() as $rule) {
			$this->add(true, $rule[0], isset($rule[1]) ? $rule[1] : null, isset($rule[2]) ? $rule[2] : null);
		}
		foreach ($acl->deny->arrayize() as $rule) {
			$this->add(false, $rule[0], isset($rule[1]) ? $rule[1] : null, isset($rule[2]) ? $rule[2] : null);
		}
	}
	
	public function rolesList()
	{
		$acl = Config::getInstance()->acl;
		
		$result = array();
		foreach($this->roles as $k => $role) {
			if($k == 'root' || $k == 'guest') {
				continue;
			}
			$result[$k] = isset($acl->titles->$k) ? $acl->titles->$k : ucfirst(strtolower($k));
		}
		
		return $result;
	}

	/**
	 * Adds ACL rule.
	 *
	 * @param boolean $allow      Whether to allow or deny.
	 * @param array   $roles      Roles.
	 * @param array   $resources  Resources.
	 * @param array   $privileges Privileges.
	 * @return this
	 */
	private function add($allow, $roles, $resources, $privileges)
	{
		if (null === $privileges) {
			$rule = array('*' => $allow);
		} else {
			$rule = array_fill_keys((array)$privileges, $allow);
		}
		if (null === $roles) {
			$rule = array('*' => $rule);
		} else {
			$rule = array_fill_keys((array)$roles, $rule);
		}
		if (null === $resources) {
			$rule = array('*' => $rule);
		} else {
			$rule = array_fill_keys((array)$resources, $rule);
		}
		$this->rules = array_replace_recursive($this->rules, $rule);
		return $this;
	}

	/**
	 * Checks if the given resource privilege is allowed or denied for the specified role.
	 *
	 * @param string $role      Role.
	 * @param string $resource  Resource.
	 * @param string $privilege Privilege.
	 * @return boolean
	 * @throws InvalidArgumentException
	 */
	public function allowed($role, $resource = null, $privilege = null)
	{
		if (false == array_key_exists($role, $this->roles)) {
			throw new InvalidArgumentException('Role "' . $role . '" does not exit.');
		}
		if (null !== $resource && array_key_exists($resource, $this->resources) == false) {
			throw new InvalidArgumentException('Resource "' . $resource . '" does not exit.');
		}
		$role = array($role);
		for ( ; ; ) {
			if (null !== ($rule = $this->matchRoles($role, $resource ?: '*', $privilege ?: '*'))) {
				return $rule;
			}
			if (null === $resource) {
				break;
			}
			$resource = $this->resources[$resource];
		}
		return false;
	}

	/**
	 * Helper for allowed() method.
	 *
	 * @param array  $roles      Array of roles.
	 * @param string $resource   Resource.
	 * @param string $privilege  Privilege.
	 * @return mixed
	 */
	private function matchRoles(array $roles, $resource, $privilege)
	{
		foreach ($roles as $role) {
			if (null !== $role && array_key_exists($role, $this->roles) == false) {
				continue;
			}
			if (null !== ($rule = $this->matchRules($role ?: '*', $resource, $privilege))) {
				return $rule;
			}
			if (null !== $role && empty($this->roles[$role]) == false) {
				return $this->matchRoles(array_reverse($this->roles[$role]), $resource, $privilege);
			}
		}
		return null;
	}

	/**
	 * Helper for matchRoles() method.
	 *
	 * @param string $role       Role.
	 * @param string $resource   Resource.
	 * @param string $privilege  Privilege.
	 * @return mixed
	 */
	private function matchRules($role, $resource, $privilege)
	{
		if (false == array_key_exists($resource, $this->rules)) {
			return null;
		}
		if (false == array_key_exists($role, $this->rules[$resource])) {
			if (array_key_exists('*', $this->rules[$resource])) {
				$role = '*';
			} else {
				return null;
			}
		}
		$default = null;
		if ('*' !== $role && array_key_exists('*', $this->rules[$resource])) {
			if (array_key_exists($privilege, $this->rules[$resource]['*'])) {
				$default = $this->rules[$resource]['*'][$privilege];
			}
		}
		if (false == array_key_exists($privilege, $this->rules[$resource][$role])) {
			if ($privilege != '*') {
				if (null === $default) {
					return array_key_exists('*', $this->rules[$resource][$role]) ? $this->rules[$resource][$role]['*'] : false;
				} else {
					return $default;
				}
			} else {
				return null;
			}
		} else {
			return $this->rules[$resource][$role][$privilege];
		}
	}
}
