<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Ukietech-1
 * Date: 15.03.13
 * Time: 18:04
 * To change this template use File | Settings | File Templates.
 */

class Model_ConnectionSearchResult extends Model{

	protected static $table = 'connection_search_result';

	public static function insertShowResult($results)
	{
		if(!empty($results['data'])) {
			$update = '';
			foreach($results['data'] as $result){
				if($result instanceof Model_User) {
					$update .= ',( NULL,' . $result->id . ',CURRENT_TIMESTAMP)';
				}
			}
			if(!empty($update)) {
				$update = 'INSERT INTO `connection_search_result`(`id`,`profile_id`,`createDate`) VALUES ' . substr($update, 1);
				$db = static::getDatabase();
				$result = $db->query($update)->count();
			}

			return $result;
		}
		return 0;
	}

	public static function countInSearchResult($user_id)
	{
		$result = new self (array(
			'select' => 'COUNT(profile_id) as countItem',
			'where' => array('profile_id = ? AND createDate >= ?', $user_id, date('Y-m-d 00:00:00', (time() - 60*60*24*30)))
		), false);

		return $result->countItem;
	}

//	public static function countConnectionBan($user_id, $profile_id)
//	{
//		$count = self::query(array(
//			'select' => '	COUNT(user_id) as countItems',
//			'where' => array('user_id = ? AND friend_id = ?', $user_id, $profile_id),
//			'group' => 'user_id'
//		))->fetch();
//
//		if(!is_null($count)){
//			return $count->countItems;
//		} else {
//			return 0;
//		}
//	}
}