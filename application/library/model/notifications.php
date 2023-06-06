<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Ukietech-1
 * Date: 15.03.13
 * Time: 18:04
 * To change this template use File | Settings | File Templates.
 */

class Model_Notifications extends Model{

	protected static $table = 'notifications';

	public static function checkIsset($user_id, $type, $post_id = NULL, $isView = NULL, $skill_id = NULL)
	{
		if($isView !== NULL){
			$where = array('user_id = ? AND `type` = ? AND isView = 0', $user_id, $type);
		} else {
			$where = array('user_id = ? AND `type` = ?', $user_id, $type);
		}

		if(is_null($post_id)) {
			$where[0] .= ' AND post_id IS NULL';
		} else {
			$where[0] .= ' AND post_id = ?';
			$where[] = $post_id;
		}

		if(is_null($skill_id)) {
			$where[0] .= ' AND skill_id IS NULL';
		} else {
			$where[0] .= ' AND skill_id = ?';
			$where[] = $skill_id;
		}

		$notification = self::query(array(
			'where' => $where
		))->fetch();

		if(!is_null($notification)) {
			$notification = self::instance($notification);
			Model_User::addUserIdByKey($notification, 'user_id');
			Model_User::addUserIdByKey($notification, 'friend_id');
		} else {
			$notification = false;
		}
		return $notification;
	}

	public static function getCountNewNotification($user_id)
	{
		return new self(array(
			'select' => 'COUNT(friend_id) AS countItems',
			'where' => array('friend_id = ? AND isView = 0', $user_id),
		));
	}

	public static function getItemByIdUserid($user_id, $notification_id)
	{
		$result = new self(array(
			'where' => array('friend_id = ? AND id = ?', $user_id, $notification_id)
		));

		Model_User::addUserIdByKey($result, 'user_id');
		Model_User::addUserIdByKey($result, 'friend_id');
		return $result;
	}

	public static function getItemByIdsFriendid($user_id, $notification_ids)
	{
		$results = self::getList(array(
			'where' => array('friend_id = ? AND id in (?)', $user_id, $notification_ids)
		));

		foreach($results['data'] as $item) {
			Model_User::addUserIdByKey($item, 'user_id');
			Model_User::addUserIdByKey($item, 'friend_id');
		}
		return $results;
	}

	public static function getListByUserid($user_id)
	{
		$results = self::getList(array(
			'select' => '	notifications.*,
							users.firstName as userFirstName,
							users.lastName as userLastName,
							users.id as userId,
							users.professionalHeadline as userHeadline,
							users.avaToken as avaToken,
							users.alias as userAlias,
							users.setInvisibleProfile as setInvisibleProfile,
							companies.name as companyName,
							universities.name as universityName,

							notificationCompany.name as notificationCompanyName,
							notificationCompany.avaToken as notificationCompanyAvaToken,
							notificationCompany.industry as notificationCompanyIndustry,

							notificationJob.title as notificationJobTitle,
							notificationJob.industry as notificationJobIndustry,
							notificationJob.country as notificationJobCountry,
							notificationJob.state as notificationJobState,
							notificationJob.city as notificationJobCity


							',
			'where' => array('notifications.friend_id = ?', $user_id),
			'join' => array(
				array(
					'type' => 'left',
					'table' => 'users',
					'where' => array('users.id = notifications.user_id AND users.isRemoved = 0 AND users.isConfirmed = 1')
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
				),
				array(
					'noQuotes' => TRUE,
					'type' => 'left',
					'table' => 'companies as notificationCompany',
					'where' => array('notificationCompany.id = notifications.company_id')
				),
				array(
					'noQuotes' => TRUE,
					'type' => 'left',
					'table' => 'jobs as notificationJob',
					'where' => array('notificationJob.id = notifications.job_id')
				)
			),
			'order' => 'notifications.isView ASC, notifications.id DESC'
		), true, false, 3, true);

		foreach($results['data'] as $item) {
			Model_User::addUserIdByKey($item, 'user_id');
			Model_User::addUserIdByKey($item, 'friend_id');
		}
		return $results;
	}

//	public static function createLikeNotification($user_id, $timeline)
//	{
//		if($timeline->user_id != $user_id && $timeline->companyUserId != $user_id && !empty($timeline->post_id)) {
//			$notif = Model_Notifications::checkIsset($user_id, NOTIFICATION_TYPE_LIKEPOST, $timeline->post_id);
//			if(!$notif) {
//				self::createNotification(NOTIFICATION_TYPE_LIKEPOST, $user_id, array(
//					'timeline' => $timeline
//				));
//			}
//		}
//	}

//	public static function createCommentNotification($user_id, $timeline)
//	{
//		if($timeline->user_id != $user_id && $timeline->companyUserId != $user_id && !empty($timeline->post_id)) {
//			$notif = Model_Notifications::checkIsset($user_id, NOTIFICATION_TYPE_COMMENTPOST, $timeline->post_id, TRUE);
//			if(!$notif) {
//				self::createNotification(NOTIFICATION_TYPE_COMMENTPOST, $user_id, array(
//					'timeline' => $timeline
//				));
//			}
//		}
//	}

	public static function removeOlderOneDayViewProfileNotification($user_id, $friend_id)
	{
		self::remove(array('user_id = ? AND friend_id = ? AND type = ? AND createDate > ?', $user_id, $friend_id, NOTIFICATION_TYPE_VIEWPROFILE, date('Y-m-d 00:00:00')));
	}

	public static function createViewProfileNotification($user_id, $friend_id)
	{
		self::removeOlderOneDayViewProfileNotification($user_id, $friend_id);
//		$notif = Model_Notifications::checkIsset($user_id, NOTIFICATION_TYPE_VIEWPROFILE, NULL, TRUE);
//		if(!$notif) {
			self::createNotification(NOTIFICATION_TYPE_VIEWPROFILE, $user_id, array(
				'friend_id' => $friend_id
			));
//		}
	}

	public static function createNewConnectionNotification($user_id, $friend_id)
	{
		self::createNotification(NOTIFICATION_TYPE_NEWCONNECTION, $user_id, array(
			'friend_id' => $friend_id
		));
	}

	public static function createEndorseSkillNotification($user_id, $friend_id, $skill_id, $skill_name)
	{
		$notif = Model_Notifications::checkIsset($user_id, NOTIFICATION_TYPE_ENDORSESKILL, NULL, TRUE, $skill_id);
		if(!$notif) {
			self::createNotification(NOTIFICATION_TYPE_ENDORSESKILL, $user_id, array(
				'skill_id' => $skill_id,
				'skill_name' => $skill_name,
				'friend_id' => $friend_id,
			));
		}
	}

	public static function 	createApproveApplicant($user_id, $job)
	{
		self::createNotification(NOTIFICATION_TYPE_APPLICANTAPPROVE, FALSE, array(
			'friend_id' => $user_id,
			'company_id' => $job->company_id,
			'job_id' => $job->id
		));
	}

	public static function 	createDenyApplicant($user_id, $job)
	{
		self::createNotification(NOTIFICATION_TYPE_APPLICANTDENY, FALSE, array(
			'friend_id' => $user_id,
			'company_id' => $job->company_id,
			'job_id' => $job->id
		));
	}

	public static function 	createNewApplicant($user_id, $job)
	{
		self::createNotification(NOTIFICATION_TYPE_APPLICANTNEW, $user_id, array(
			'company_id' => $job->company_id,
			'job_id' => $job->id
		));
	}

	public static function createNotification($type, $user_id, $data = array())
	{
		switch($type) {
			case NOTIFICATION_TYPE_LIKEPOST:
			case NOTIFICATION_TYPE_COMMENTPOST:
				$timeline = $data['timeline'];

				$text = View::factory('pages/updates/item-update', array(
					'timeline' => $timeline,
					'isUsernameLink' => TRUE,
					'textLen' => 150,
					'isNotification' => TRUE,
					'isPanelsSocial' => FALSE
				));

				if(!is_null($timeline->postCompanyId)) {
					$friend_id = $timeline->companyUserId;
				} elseif(!is_null($timeline->postSchoolId)) {
					$friend_id = $timeline->schoolUserId;
				} else {
					$friend_id = $timeline->postUserId;
				}

				self::create(array(
					'type' => $type,
					'user_id' => $user_id,
					'friend_id' => $friend_id,
					'notification' => (string) $text,
					'post_id' => $timeline->post_id,
				));

				Model_User::addUserId($user_id);
				Model_User::addUserId($friend_id);
				break;
			case NOTIFICATION_TYPE_VIEWPROFILE:
			case NOTIFICATION_TYPE_NEWCONNECTION:
				$friend_id = $data['friend_id'];

				self::create(array(
					'type' => $type,
					'user_id' => $user_id,
					'friend_id' => $friend_id,
					'notification' => NULL,
					'post_id' => NULL,
				));
				Model_User::addUserId($user_id);
				Model_User::addUserId($friend_id);

				break;
			case NOTIFICATION_TYPE_ENDORSESKILL:

				self::create(array(
					'type' => $type,
					'user_id' => $user_id,
					'friend_id' => $data['friend_id'],
					'notification' => '<span>"' . $data['skill_name'] . '"</span>',
					'post_id' => NULL,
					'skill_id' => $data['skill_id']
				));
				Model_User::addUserId($user_id);
				Model_User::addUserId($data['friend_id']);

				break;
			case NOTIFICATION_TYPE_APPLICANTAPPROVE:
			case NOTIFICATION_TYPE_APPLICANTDENY:

				self::create(array(
					'type' => $type,
					'friend_id' => $data['friend_id'],
					'notification' => NULL,
					'company_id' => $data['company_id'],
					'job_id' => $data['job_id']
				));
				Model_User::addUserId($data['friend_id']);

				break;
			case NOTIFICATION_TYPE_APPLICANTNEW:

				self::create(array(
					'type' => $type,
					'friend_id' => $user_id,
					'notification' => NULL,
					'company_id' => $data['company_id'],
					'job_id' => $data['job_id']
				));
				Model_User::addUserId($user_id);
				break;
		}
	}

}