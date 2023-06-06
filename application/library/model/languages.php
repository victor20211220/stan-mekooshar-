<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Ukietech-1
 * Date: 15.03.13
 * Time: 18:04
 * To change this template use File | Settings | File Templates.
 */

class Model_Languages extends Model{

	protected static $table = 'languages';


	public static function getByName($name)
	{
		$language = self::query(array(
			'where' => array('`name` = ?', $name)
		))->fetch();

		if (!is_null($language)) {
			$language = self::instance($language);
		} else {
			$language = false;
		}
		return $language;
	}



	public static function getListByNames($array_name)
	{
		return self::getList(array(
			'where' => array('`name` in (?)', $array_name)
		), false);

	}

	/**
	 * Check language by id.
	 *
	 * @param  string $id - Language id
	 * @return bool|this - Object or false
	 */
	public static function checkItemById($id)
	{
		$language = self::query(array(
			'where' => array('`id` = ?', $id)
		))->fetch();

		if (!is_null($language)) {
			$language = self::instance($language);
		} else {
			$language = false;
		}
		return $language;
	}


	/**
	 * Get sorted list languages for autocomplete by text, if it is.
	 *
	 * @param  bool|string $text - Search text or FALSE
	 * @return Array Objects - List result
	 */
	public static function getList_OrderCountUsed($text = false)
	{
		$where = array('id <> 0');
		if ($text) {
			$where[0] .= ' AND languages.name like ?';
			$where[] = '%' . strtolower($text) . '%';
		}


		return self::getList(array(
			'where' => $where,
			'order' => 'countUsed DESC, id DESC'
		), TRUE, FALSE, 100);
	}


	/**
	 * Check language by name. Name is trimed.
	 *
	 * @param  string $name - Language name
	 * @return bool|this - Object or false
	 */
	public static function checkItemByName($name)
	{
		$name = trim($name);
		return self::getByName($name);
	}
}