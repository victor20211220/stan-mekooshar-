<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Ukietech-1
 * Date: 15.03.13
 * Time: 18:04
 * To change this template use File | Settings | File Templates.
 */

class Model_Certifications extends Model{

	protected static $table = 'certifications';

	public static function getByName($name)
	{
		$certification = self::query(array(
			'where' => array('`name` = ?', $name)
		))->fetch();

		if (!is_null($certification)) {
			$certification = self::instance($certification);
		} else {
			$certification = false;
		}
		return $certification;
	}

	/**
	 * Check certification by name. Name is trimed.
	 *
	 * @param  string $name - certification name
	 * @return bool|this - Object or false
	 */
	public static function checkItemByName($name)
	{
		$name = trim($name);
		return self::getByName($name);
	}


	/**
	 * Check certification by id.
	 *
	 * @param  string $id - certification id
	 * @return bool|this - Object or false
	 */
	public static function checkItemById($id)
	{
		$certification = self::query(array(
			'where' => array('`id` = ?', $id)
		))->fetch();

		if (!is_null($certification)) {
			$certification = self::instance($certification);
		} else {
			$certification = false;
		}
		return $certification;
	}

	/**
	 * Get sorted list certifications for autocomplete by text, if it is.
	 *
	 * @param  bool|string $text - Search text or FALSE
	 * @return Array Objects - List result
	 */
	public static function getList_OrderCountUsed($text = false)
	{
		$where = array('id <> 0');
		if ($text) {
			$where[0] .= ' AND certifications.name like ?';
			$where[] = '%' . strtolower($text) . '%';
		}


		return self::getList(array(
			'where' => $where,
			'order' => 'countUsed DESC, id DESC'
		), TRUE, FALSE, 100);
	}
}