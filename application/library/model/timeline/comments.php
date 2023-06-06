<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Ukietech-1
 * Date: 15.03.13
 * Time: 18:04
 * To change this template use File | Settings | File Templates.
 */

class Model_Timeline_Comments extends Model{

	protected static $table = 'timeline_comments';

	public static function getListByTimelineId($timeline_id, $user_id = false, $countPaginator = 3)
	{
		$results = self::getList(array(
			'select' => '	timeline_comments.*,
							users.firstName as userFirstName,
							users.lastName as userLastName,
							users.avaToken as userAvaToken,
							users.alias as userAlias,
							users.setInvisibleProfile as setInvisibleProfile,
							users.id as userId,
							timeline.user_id as timelineUserId,
							timeline.company_id as timelineCompanyId,
							parentTimeline.user_id as timelineOwnerId,
							companies.user_id as companyUserId,
							group_members.user_id as groupMemberUserId,
							group_members.memberType as groupMemberType
							',
			'where' => array('timeline_comments.timeline_id = ?', $timeline_id),
			'join' => array(
				array(
					'table' => 'users',
					'where' => array('users.id = timeline_comments.user_id AND users.isRemoved = 0 AND users.isConfirmed = 1')
				),
				array(
					'type' => 'left',
					'table' => 'timeline',
					'where' => array('timeline.id = timeline_comments.timeline_id')
				),
				array(
					'type' => 'left',
					'noQuotes' => true,
					'table' => 'timeline as parentTimeline',
					'where' => array('parentTimeline.id = timeline.parent_id')
				),
				array(
					'type' => 'left',
					'table' => 'companies',
					'where' => array('companies.id = timeline.company_id')
				),
				array(
					'type' => 'left',
					'table' => 'posts',
					'where' => array('posts.id = timeline.post_id')
				),
				array(
					'type' => 'left',
					'table' => 'groups',
					'where' => array('groups.id = posts.group_id')
				),
				array(
					'type' => 'left',
					'table' => 'group_members',
					'where' => array('group_members.group_id = groups.id AND group_members.user_id = ? AND group_members.isApproved = 1 AND group_members.memberType = ?', $user_id, GROUP_MEMBER_TYPE_ADMIN)
				),
			),
			'order' => 'timeline_comments.id DESC'
		), true, false, $countPaginator, true);

		foreach($results['data'] as $item) {
			Model_User::addUserIdByKey($item, 'userId');
		}
		return $results;
	}

	public static function checkCommentByOwners($comment_id, $user_id)
	{
		$check = self::query(array(
			'select' => 'timeline_comments.*',
			'where' => array('timeline_comments.id = ? AND (timeline_comments.user_id = ? OR timeline.user_id = ? OR parentTimeline.user_id = ? OR posts.user_id = ? || companies.user_id = ? || groups.user_id = ? || group_members.user_id IS NOT NULL)', $comment_id, $user_id, $user_id, $user_id, $user_id, $user_id, $user_id),
			'join' => array(
				array(
					'table' => 'timeline',
					'where' => array('timeline.id = timeline_comments.timeline_id')
				),
				array(
					'type' => 'left',
					'noQuotes' => true,
					'table' => 'timeline as parentTimeline',
					'where' => array('parentTimeline.id = timeline.parent_id')
				),
				array(
					'type' => 'left',
					'table' => 'posts',
					'where' => array('posts.id = timeline.post_id')
				),
				array(
					'type' => 'left',
					'table' => 'companies',
					'where' => array('companies.id = timeline.company_id')
				),
				array(
					'type' => 'left',
					'table' => 'groups',
					'where' => array('groups.id = timeline.group_id')
				),
				array(
					'type' => 'left',
					'table' => 'group_members',
					'where' => array('group_members.group_id = groups.id AND group_members.memberType = ? AND isApproved = 1 AND group_members.user_id = ?', GROUP_MEMBER_TYPE_ADMIN, $user_id)
				),
			),
			'order' => 'timeline_comments.id DESC',
			'limit' => 1
		))->fetch();

		if(isset($check->id)) {
			$check = self::instance($check);
			Model_User::addUserIdByKey($check, 'user_id');
			return $check;
		} else {
			return FALSE;
		}
	}
}