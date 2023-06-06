<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Ukietech-1
 * Date: 15.03.13
 * Time: 18:04
 * To change this template use File | Settings | File Templates.
 */

class Model_Skills extends Model{

	protected static $table = 'skills';

	public static function getListByNames($array_name)
	{
		return self::getList(array(
			'where' => array('`name` in (?)', $array_name)
		), false);
	}

	public static function getListByIds($ids)
	{
		return self::getList(array(
			'where' => array('id in (?)', $ids)
		), false);
	}

	public static function getListAll()
	{
		return self::getList(array(), false);
	}

	public static function getItemById($skill_id)
	{
		return new self(array(
			'where' => array('id = ?', $skill_id)
		));
	}

	public static function getList_OrderCountUsed($text = false)
	{
		$where = array('id <> 0');
		if($text) {
			$where[0] .= ' AND skills.name like ?';
			$where[] = '%' . strtolower($text) . '%';
		}


		return self::getList(array(
			'where' => $where,
			'order' => 'countUsed DESC, id DESC'
		), TRUE, FALSE, 100);
	}

	public static function checkItemById($skill_id)
	{
		$skill = self::query(array(
			'where' => array('id = ?', $skill_id)
		))->fetch();

		if(!is_null($skill)) {
			$skill = self::instance($skill);
		} else {
			$skill = false;
		}
		return $skill;
	}

	public static function checkItemByName($skill_name)
	{
		$skill = self::query(array(
			'where' => array('name = ?', strtolower($skill_name))
		))->fetch();

		if(!is_null($skill)) {
			$skill = self::instance($skill);
		} else {
			$skill = false;
		}
		return $skill;
	}
}