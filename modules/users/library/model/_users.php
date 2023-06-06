<?php

/**
 * Clients model.
 *
 * @version  $Id$
 * @package  Application
 */

class Model_Users extends Model
{
	protected static $table = 'users';
	
	public function getUser($id)
	{
		return $this->db->query('SELECT * FROM `users` WHERE `id` = ?', $id)->fetch();
	}

	public function getUsers()
	{
		$users = $this->db->query('SELECT * FROM `users` WHERE `role` != "root" ORDER BY `fullName` ASC')->fetchAll();
		$result = array();
		if ($users) {
			foreach ($users as $user) {
				$result[$user['id']] = $user;
			}
		}
		return $result;
	}

	public function getUsersWithRoot()
	{
		$users = $this->db->query('SELECT * FROM `users` ORDER BY `fullName` ASC')->fetchAll();
		$result = array();
		if ($users) {
			foreach ($users as $user) {
				$result[$user['id']] = $user;
			}
		}
		return $result;
	}

	public function getUserByRole($id, $role)
	{
		return $this->db->query('SELECT * FROM `users` WHERE role`=? AND `id` = ?', array($role, $id))->fetch();
	}

	public function getUsersByRole($role)
	{
		$users = $this->db->query('SELECT * FROM `users` WHERE `role` = ? ORDER BY `fullName` ASC', $role)->fetchAll();
		$result = array();
		if ($users) {
			foreach ($users as $user) {
				$result[$user['id']] = $user;
			}
		}
		return $result;
	}
	
	public function getSubscribedUsers()
	{
		$users = $this->db->query('SELECT * FROM `users` WHERE `role` != "root" AND `subscribed`=1 ORDER BY `isConfirmed` ASC, `fullName` ASC')->fetchAll();
		$result = array();
		if ($users) {
			foreach ($users as $user) {
				$result[$user['id']] = $user;
			}
		}
		return $result;
	}

	/**
	 * Registers a new user. Returns Id of new user.
	 *
	 * @param  array  $values  Values to insert.
	 * @return integer
	 */
	public function insert(array $values)
	{
		$salt = System::random('hexdec', 8);
		$values['password'] = $salt . sha1($salt . $values['password']);
		return $this->db->insert('users', $values);
	}

	/**
	 * Updates an existent user.
	 *
	 * @param  array   $values  Values to update.
	 * @param  integer $id      User Id.
	 * @return integer
	 */
	public function update(array $values, $id)
	{
		if (array_key_exists('password', $values)) {
			$salt = System::random('hexdec', 8);
			$values['password'] = $salt . sha1($salt . $values['password']);
		}
		return $this->db->update('users', $values, array('id' => $id));
	}

	/**
	 * Returns true if user exists.
	 *
	 * @param  string  $username  Username.
	 * @param  integer $testId    User Id to test with.
	 * @return boolean
	 */
	public function exists($username, $testId = null)
	{
		if (false === mb_strpos($username, '@')) {
			$row = $this->db->query('SELECT `id` FROM `users` WHERE LOWER(`name`) = ?', mb_strtolower($username))->fetch();
		} else {
			$row = $this->db->query('SELECT `id` FROM `users` WHERE `email` = ?', $username)->fetch();
		}
		if (null === $row) {
			return false;
		} else {
			return (null === $testId) ? true : $testId != current($row);
		}
	}

}
