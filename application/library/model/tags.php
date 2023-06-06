<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Ukietech-1
 * Date: 15.03.13
 * Time: 18:04
 * To change this template use File | Settings | File Templates.
 */

class Model_Tags extends Model{

	protected static $table = 'tags';

	public static function getListByUser($user_id)
	{
		$results = self::getList(array(
			'where' => array('user_id = ?', $user_id),
			'order' => 'name ASC'
		), false);

		foreach($results['data'] as $item) {
			Model_User::addUserIdByKey($item, 'user_id');
		}
		return $results;
	}
//	public static function getByName($name)
//	{
//		$company = self::query(array(
//			'where' => array('`name` = ?', $name)
//		))->fetch();
//
//		if(!is_null($company)) {
//			$company = self::instance($company);
//		} else {
//			$company = false;
//		}
//		return $company;
//	}
}