<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Ukietech-1
 * Date: 15.03.13
 * Time: 18:04
 * To change this template use File | Settings | File Templates.
 */

class Model_ConnectionBan extends Model{

	protected static $table = 'connection_ban';

	public static function countConnectionBan($user_id, $profile_id)
	{
		$count = self::query(array(
			'select' => '	COUNT(user_id) as countItems',
			'where' => array('user_id = ? AND friend_id = ?', $user_id, $profile_id),
			'group' => 'user_id'
		))->fetch();

		if(!is_null($count)){
			return $count->countItems;
		} else {
			return 0;
		}
	}
}