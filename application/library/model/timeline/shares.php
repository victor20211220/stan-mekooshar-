<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Ukietech-1
 * Date: 15.03.13
 * Time: 18:04
 * To change this template use File | Settings | File Templates.
 */

class Model_Timeline_Shares extends Model{

	protected static $table = 'timeline_shares';

	public static function checkIsset($user_id, $timeline_id)
	{
		$timelineShare = self::query(array(
			'where' => array('user_id = ? AND parentTimeline_id = ?', $user_id, $timeline_id)
		))->fetch();

		if(!is_null($timelineShare)) {
			$timelineShare = self::instance($timelineShare);
			Model_User::addUserIdByKey($timelineShare, 'user_id');
		} else {
			$timelineShare = false;
		}
		return $timelineShare;
	}

	public static function getParentTimelineByCurrentTimelineId($user_id, $timeline_id)
	{
		$timelineShare = self::query(array(
			'where' => array('user_id = ? AND timeline_id = ?', $user_id, $timeline_id)
		))->fetch();

		if(!is_null($timelineShare)) {
			$timelineShare = self::instance($timelineShare);
			Model_User::addUserIdByKey($timelineShare, 'user_id');
		} else {
			$timelineShare = false;
		}
		return $timelineShare;
	}

	public static function getListByTimeline($timeline)
	{
		switch($timeline->type) {
			case TIMELINE_TYPE_COMMENTS:
			case TIMELINE_TYPE_LIKE:
				$timeline_id = $timeline->parent_id;
				break;
			default:
				$timeline_id = $timeline->id;
		}

		$results = self::getList(array(
			'select' => '
							users.*,
							users.firstName as userFirstName,
							users.lastName as userLastName,
							users.professionalHeadline as userHeadline,
							users.avaToken as avaToken,
							users.alias as userAlias,
							users.setInvisibleProfile as setInvisibleProfile,
							companies.name as companyName,
							universities.name as universityName
						',
			'where' => array('parentTimeline_id = ?', $timeline_id),
			'join' => array(
				array(
					'table' => 'users',
					'where' => array('users.id = timeline_shares.user_id')
				),
				array(
					'type' => 'left',
					'table' => 'profile_expirience',
					'where' => array('profile_expirience.user_id = users.id AND profile_expirience.isCurrent = 1')
				),
				array(
					'type' => 'left',
					'table' => 'companies',
					'where' => array('companies.id = profile_expirience.company_id')
				),
				array(
					'type' => 'left',
					'table' => 'universities',
					'where' => array('universities.id = profile_expirience.university_id')
				)
			),
			'order' => 'createDate DESC'
		));

		foreach($results['data'] as $item) {
			Model_User::addUserIdByKey($item);
		}
		return $results;
	}
}