<?php

/**
 * Kit.
 *
 * Authentication library.
 *
 * @version $Id: auth.php 109 2010-07-29 06:46:41Z perfilev $
 * @package System
 */

class System_Auth
{
	/**
	 * @var string  Cookie's name.
	 */
	public static $cookie = 'token';

	/**
	 * @var integer  Cookie's lifetime. Default is two weeks. Set to zero to disable cookies.
	 */
	public static $lifetime = 1209600;

	/**
	 * @var object  Class instance.
	 */
	protected static $instance;

	/**
	 * Returns an instance of class.
	 *
	 * @return this
	 */
	public static function getInstance()
	{
		if (null == self::$instance) {
			self::$instance = new static();
		}
		return self::$instance;
	}

	/**
	 * Consrtuctor.
	 *
	 * @return this
	 */
	public function __construct()
	{
		if ('' === session_id()) {
			session_start();
		}
	}

	/**
	 * Returns true if a user is signed in.
	 *
	 * @return boolean
	 */
	public function hasIdentity()
	{
		return false !== $this->getIdentity();
	}

	/**
	 * Returns current identity.
	 *
	 * @return mixed
	 */
	public function getIdentity()
	{
		if (isset($_SESSION['identity']) && is_object($_SESSION['identity'])) {
			return $_SESSION['identity'];
		}
		if (self::$lifetime > 0) {
			if (isset($_COOKIE[self::$cookie])) {
				$token = explode('.', $_COOKIE[self::$cookie]);
				if (count($token) == 2 && is_numeric($token[0]) && '' !== $token[1]) {
					try {
						$user = new Model_User($token[0]);
					} catch (InvalidArgumentException $e) {
						return false;
					}
					if ($user && $user->token == $token[1]) {
						return $this->setupIdentity($user, true);
					}
				}
			}
		}
		return false;
	}

	/**
	 * Helper for getIdentity() and authenticate() methods.
	 *
	 * @return mixed
	 */
	protected function setupIdentity($user, $cookie = false)
	{
		$address = array();
		$states = t('states');
		if($user) {
			!empty($user->address) ? $address[] = $user->address : null;
			!empty($user->city) ? $address[] = $user->city : null;
			if(!empty($user->state)) {
				if(!empty($user->country) && $user->country == 'US' && isset($states[$user->state])) {
					$address[] = $states[$user->state];
				} else {
					$address[] = $user->state;
				}
			}
			!empty($user->zip) ? $address[] = $user->zip : null;
			!empty($user->country) ? $address[] = t('countries.' . $user->country) : null;
		}
		$address  = implode(', ', $address);

		$identity = (object) array(
			'id'   		=> (int) $user->id ?? '',
			'name' 		=> $user->name ?? '',
			'alias' 	=> $user->alias ?? '',
			'firstName'	=> $user->firstName ?? '',
			'lastName'	=> $user->lastName ?? '',
			'professionalHeadline' => $user->professionalHeadline ?? '',
			'industry' 	=> $user->industry ?? '',
			'summaryText' 	=> $user->summaryText ?? '',
			'birthdayDate' 	=> $user->birthdayDate ?? '',
			'maritalStatus' => $user->maritalStatus ?? '',
			'interests' 	=> $user->interests ?? '',
			'email' 	=> $user->email ?? '',
			'email2' 	=> $user->email2 ?? '',
			'phone' 	=> $user->phone ?? '',
			'address' 	=> $user->address ?? '',
			'city' 		=> $user->city ?? '',
			'state' 	=> $user->state ?? '',
			'country' 	=> $user->country ?? '',
			'zip' 		=> $user->zip ?? '',
			'fullAddress' 	=> $address ?? '',
			'websites' 	=> $user->websites ?? '',
			'role' 		=> $user->role ?? '',
			'avaToken' 	=> $user->avaToken ?? '',
			'shareActivityInActivityFeed' => $user->shareActivityInActivityFeed ?? '',
			'setInvisibleProfile' => $user->setInvisibleProfile ?? '',
//			'whoCanSeeActivity' => $user->whoCanSeeActivity,
			'whoCanSeeConnections' => $user->whoCanSeeConnections ?? '',
			'whoCanSeeContactInfo' => $user->whoCanSeeContactInfo ?? '',
			'accountType' => $user->accountType ?? '',
			'updateExp' => $user->updateExp ?? '',
			'isUpdatedConnections' => $user->isUpdatedConnections ?? '',
			'countConnections' => $user->countConnections ?? '',
			'countConnections2' => $user->countConnections2 ?? '',
			'countConnections3' => $user->countConnections3 ?? ''
		);

		if ($cookie && self::$lifetime > 0) {
			$token = Text::random('alphanum', 32);
			setcookie(self::$cookie, $user->id . '.' . $token, time() + self::$lifetime, '/');
			$user->token = $token;
			$user->save();
			$identity->token = $token;
		}
		session_regenerate_id(true);
		return $_SESSION['identity'] = $identity;
	}


	public function updateIdentity($user_id, $cookie = false){
		$user = new Model_User($user_id);
		$this->setupIdentity($user, $cookie);
	}

	/**
	 * Authenticates by username and password.
	 *
	 * @param mixed   $username  String username or user database fetch result.
	 * @param string  $password  Password.
	 * @param boolean $cookie    Auto sign in?
	 * @return boolean
	 */
	public function authenticate($username, $password, $cookie = false, $isPublic = false)
	{
		if ($username instanceof Model_User) {
			$user = $username;
		} else {
			if (empty($password)) {
				return false;
			}
			try {
				if($isPublic) {
					$user = new Model_User(array(
						'where' => array('email = ? AND isConfirmed = 1 AND isRemoved = 0', $username),
					));
				} else {
					$user = new Model_User(array(
						'where' => array('name = ? AND isConfirmed = 1 AND isRemoved = 0', $username),
					));
				}
			} catch (InvalidArgumentException $e) {
				return false;
			}
		}
		if ($user) {
			$salt = substr($user->password, 0, 8);
			if ($salt . sha1($salt . $password) == $user->password) {
				return false !== $this->setupIdentity($user, $cookie);
			}
		}
		return false;
	}


	public function authenticateWithoutPassword($facebook_ID, $cookie = false, $isPublic = false)
	{

        if($isPublic) {
            $user = new Model_User(array(
                'where' => array('facebook_ID = ? AND isRemoved = 0', $facebook_ID),
            ));
        }

		if ($user) {
			return false !== $this->setupIdentity($user, $cookie);
		}
		return false;
	}


	public function checkPassword($username, $password, $isPublic = true)
	{
		if ($username instanceof Model_User) {
			$user = $username;
		} else {
			if (empty($password)) {
				return false;
			}
			try {
				if($isPublic) {
					$user = new Model_User(array(
						'where' => array('email = ?', $username),
					));
				} else {
					$user = new Model_User(array(
						'where' => array('name = ?', $username),
					));
				}
			} catch (InvalidArgumentException $e) {
				return false;
			}
		}
		if ($user) {
			$salt = substr($user->password, 0, 8);
			if ($salt . sha1($salt . $password) == $user->password) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Sign out an user.
	 *
	 * @return void
	 */
	public function clearIdentity()
	{
		if (isset($_SESSION['identity'])) {
			unset($_SESSION['identity']);
		}
		if (isset($_COOKIE[self::$cookie])) {
			setcookie(self::$cookie, '', time() - 3600, '/');
		}
		session_regenerate_id(true);
	}

	/**
	 * Whether the requested privileges on the given resources are allowed to the current identity role?
	 *
	 * @param array $resources   Requested resoureces.
	 * @param array $privileges  Requested privileges on resoureces.
	 * @return boolean
	 */
	public function allowed($resources = null, $privileges = null)
	{
		if ($identity = $this->getIdentity()) {
			$role = $identity->role;
		} else {
			$role = 'guest';
		}
		return Acl::getInstance()->allowed($role, $resources, $privileges);
	}

}
