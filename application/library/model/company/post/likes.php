<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Ukietech-1
 * Date: 15.03.13
 * Time: 18:04
 * To change this template use File | Settings | File Templates.
 */


class Model_Company_Post_Likes extends Model{

	protected static $table = 'company_post_likes';

	public static function checkIsset($user_id, $post_id)
	{
		$like = self::query(array(
			'where' => array('user_id = ? AND post_id = ?', $user_id, $post_id)
		))->fetch();

		if(!is_null($like)) {
			$like = self::instance($like);
		} else {
			$like = false;
		}
		return $like;
	}


	public static function getListMonthStatistic($company_id)
	{
		return self::getList(array(
			'select' => '	DATE_FORMAT(createDate, "%Y-%m-01") as id,
							COUNT(company_id) as countItems',
			'where' => array('company_id = ? AND createDate >= ?', $company_id, date('Y-m-01', (time() - 60*60*24*30*5))),
			'group' => 'MONTH(createDate)',
			'order' => 'createDate DESC'
		));
	}
}