<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Ukietech-1
 * Date: 15.03.13
 * Time: 18:04
 * To change this template use File | Settings | File Templates.
 */

class Model_Projects extends Model{

	protected static $table = 'projects';

	public static function getByName($name)
	{
		$project = self::query(array(
			'where' => array('`name` = ?', $name)
		))->fetch();

		if(!is_null($project)) {
			$project = self::instance($project);
			Model_User::addUserIdByKey($project, 'user_id');
		} else {
			$project = false;
		}
		return $project;
	}


	/**
	 * Check project by name. Name is trimed.
	 *
	 * @param  string $name - Project name
	 * @return bool|this - Object or false
	 */
	public static function checkItemByName($name)
	{
		$name = trim($name);
		return self::getByName($name);
	}


	/**
	 * Check project by id.
	 *
	 * @param  string $id - Project id
	 * @return bool|this - Object or false
	 */
	public static function checkItemById($id)
	{
		$project = self::query(array(
			'where' => array('`id` = ?', $id)
		))->fetch();

		if (!is_null($project)) {
			$project = self::instance($project);
		} else {
			$project = false;
		}
		return $project;
	}


	/**
	 * Get sorted list projects for autocomplete by text, if it is.
	 *
	 * @param  bool|string $text - Search text or FALSE
	 * @return Array Objects - List result
	 */
	public static function getList_OrderCountUsed($text = false)
	{
		$where = array('id <> 0');
		if ($text) {
			$where[0] .= ' AND projects.name like ?';
			$where[] = '%' . strtolower($text) . '%';
		}


		return self::getList(array(
			'where' => $where,
			'order' => 'countUsed DESC, id DESC'
		), TRUE, FALSE, 100);
	}
}