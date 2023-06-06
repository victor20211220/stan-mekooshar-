<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Ukietech-1
 * Date: 15.03.13
 * Time: 18:04
 * To change this template use File | Settings | File Templates.
 */

class Model_Group_Discussion_Follow extends Model{

	protected static $table = 'group_discussion_follow';

	public static function checkIsset($user_id, $group_id, $post_id)
	{
		$groupFollow = self::query(array(
			'where' => array('user_id = ? AND group_id = ? AND post_id = ?', $user_id, $group_id, $post_id)
		))->fetch();

		if(!is_null($groupFollow)) {
			$groupFollow = self::instance($groupFollow);
		} else {
			$groupFollow = false;
		}
		return $groupFollow;
	}

	public static function getListByPostids(array $post_ids)
	{
		$results =  self::getList(array(
			'select' => '
						group_discussion_follow.*,
						group_discussion_follow.createDate AS id,
						SUBSTRING_INDEX(GROUP_CONCAT(CAST( users.id AS CHAR ), "_", users.firstName, " ", users.lastName ORDER BY group_discussion_follow.createDate DESC), ",",3)
			 			',
			'where' => array('post_id in (?) ', $post_ids),
			'join' => array(
				array(
					'type' => 'left',
					'table' => 'users',
					'where' => array('users.id = group_discussion_follow.user_id AND users.isConfirmed = 1 AND isRemoved = 0')
				)
			)
		), false);

		foreach($results['data'] as $item) {
			Model_User::addUserId($item->user_id);
		}
		return $results;

	}
}