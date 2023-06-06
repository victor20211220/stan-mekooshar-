<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Ukietech-1
 * Date: 15.03.13
 * Time: 18:04
 * To change this template use File | Settings | File Templates.
 */


class Model_Profile_Complaint extends Model{

	protected static $table = 'profile_complaint';

	/**
	 * Check is already sent complaint by user to another profile
	 *
	 * @param  int  $profile_id - Profile id
	 * @return bool
	 */
	public static function checkIsComplaint($profile_id)
	{
		$user = Auth::getInstance()->getIdentity();

		$complaint = self::query(array(
			'where' => array('user_id = ? AND profile_id = ?', $user->id, $profile_id)
		))->fetch();

		if(!is_null($complaint)) {
			return TRUE;
		} else {
			return FALSE;
		}
	}


	public static function getOneUserWithComplaintsInfo($user_id)
	{
		$result = new self(array(
			'select' => '
						profile_complaint.profile_id 	AS id,
						profile_complaint.profile_id,
						COUNT(profile_complaint.profile_id) AS countComplaints,
						SUM(profile_complaint.isViewed) 	AS countReadedComplaints,
						profile_complaint.createDate 		AS createDate,
						users.isBlocked,
						users.firstName,
						users.lastName,
						users.email,
						users.alias
						',
			'where' => array('profile_complaint.profile_id = ?', $user_id),
			'join' => array(
				array(
					'table' => 'users',
					'where' => array('users.id = profile_complaint.profile_id AND users.isRemoved = 0 AND users.isConfirmed = 1')
				)
			),
			'group' => 'profile_complaint.profile_id',
			'order' => 'profile_complaint.isViewed ASC, profile_complaint.createDate DESC'
		));

		return $result;
	}


	/**
	 * Show all users with complaints if isset
	 *
	 * @return array List
	 */
	public static function getAll()
	{
		$results = self::getList(array(
			'select' => '
						profile_complaint.profile_id 	AS id,
						profile_complaint.profile_id,
						COUNT(profile_complaint.profile_id) AS countComplaints,
						SUM(profile_complaint.isViewed) 	AS countReadedComplaints,
						profile_complaint.createDate 		AS createDate,
						users.isBlocked,
						users.firstName,
						users.lastName,
						users.email,
						users.alias
						',
			'join' => array(
				array(
					'table' => 'users',
					'where' => array('users.id = profile_id AND users.isRemoved = 0 AND isConfirmed = 1')
				)
			),
			'group' => 'profile_complaint.profile_id',
			'order' => 'profile_complaint.isViewed ASC, profile_complaint.createDate DESC',
		), TRUE, FALSE, 100, FALSE);

		return $results;
	}


	/**
	 * Get all complaints by one user
	 *
	 * @param  integer $user_id - User id
	 * @return array List
	 */
	public static function getAllComplaintsByUser($user_id)
	{
		$results = self::getList(array(
			'select' => '
						users.id AS id,
						users.id		AS userFromId,
						users.firstName AS userFromFirstName,
						users.lastName 	AS userFromLastName,
						users.email 	AS userFromEmail,
						users.isBlocked	AS userFromIsBlocked,
						users.alias		AS userFromAlias,

						users2.id		 	AS userToId,
						users2.firstName 	AS userToFirstName,
						users2.lastName 	AS userToLastName,
						users2.email 		AS userToEmail,
						users2.isBlocked	AS userToIsBlocked,
						users2.alias		AS userToAlias,

						profile_complaint.createDate,
						profile_complaint.description,
						profile_complaint.isViewed,
						profile_complaint.id 	AS complaintId

						',
			'where' => array('profile_complaint.profile_id = ?', $user_id),
			'join' => array(
				array(
					'table' => 'users',
					'where' => array('users.id = profile_complaint.user_id AND users.isRemoved = 0 AND users.isConfirmed = 1')
				),
				array(
					'noQuotes' => TRUE,
					'table' => 'users AS users2',
					'where' => array('users2.id = profile_complaint.profile_id AND users2.isRemoved = 0 AND users2.isConfirmed = 1')
				)
			),
			'order' => 'profile_complaint.isViewed ASC, profile_complaint.createDate DESC'
		), TRUE, FALSE, 100, FALSE);

		return $results;
	}


	/**
	 * Get one complaint by id
	 *
	 * @param  integer $complaint_id - Complaint id
	 * @return Model_Profile_Complaint
	 */
	public static function getOneComplaintsById($complaint_id)
	{
		$complaint = new self(array(
			'select' => '
						users.id AS id,
						users.id		AS userFromId,
						users.firstName AS userFromFirstName,
						users.lastName 	AS userFromLastName,
						users.email 	AS userFromEmail,
						users.isBlocked	AS userFromIsBlocked,
						users.alias		AS userFromAlias,

						users2.id		 	AS userToId,
						users2.firstName 	AS userToFirstName,
						users2.lastName 	AS userToLastName,
						users2.email 		AS userToEmail,
						users2.isBlocked	AS userToIsBlocked,
						users2.alias		AS userToAlias,

						profile_complaint.createDate,
						profile_complaint.description,
						profile_complaint.isViewed,
						profile_complaint.id 	AS complaintId

						',
			'where' => array('profile_complaint.id = ?', $complaint_id),
			'join' => array(
				array(
					'table' => 'users',
					'where' => array('users.id = profile_complaint.user_id AND users.isRemoved = 0 AND users.isConfirmed = 1')
				),
				array(
					'noQuotes' => TRUE,
					'table' => 'users AS users2',
					'where' => array('users2.id = profile_complaint.user_id AND users2.isRemoved = 0 AND users2.isConfirmed = 1')
				)
			)
		));

		return $complaint;
	}
}