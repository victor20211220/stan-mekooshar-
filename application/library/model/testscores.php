<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Ukietech-1
 * Date: 15.03.13
 * Time: 18:04
 * To change this template use File | Settings | File Templates.
 */

class Model_TestScores extends Model{

	protected static $table = 'testscores';

	public static function getByName($name)
	{
		$testscope = self::query(array(
			'where' => array('`name` = ?', $name)
		))->fetch();

		if(!is_null($testscope)) {
			$testscope = self::instance($testscope);
		} else {
			$testscope = false;
		}
		return $testscope;
	}

	/**
	 * Check Test Scope by name. Name is trimed.
	 *
	 * @param  string $name - Test Scope name
	 * @return bool|this - Object or false
	 */
	public static function checkItemByName($name)
	{
		$name = trim($name);
		return self::getByName($name);
	}

	/**
	 * Check Test Scope by id.
	 *
	 * @param  string $id - Test Scope id
	 * @return bool|this - Object or false
	 */
	public static function checkItemById($id)
	{
		$testscope = self::query(array(
			'where' => array('`id` = ?', $id)
		))->fetch();

		if (!is_null($testscope)) {
			$testscope = self::instance($testscope);
		} else {
			$testscope = false;
		}
		return $testscope;
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
			$where[0] .= ' AND testscores.name like ?';
			$where[] = '%' . strtolower($text) . '%';
		}


		return self::getList(array(
			'where' => $where,
			'order' => 'countUsed DESC, id DESC'
		), TRUE, FALSE, 100);
	}
}