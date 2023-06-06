<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Ukietech-1
 * Date: 15.03.13
 * Time: 18:04
 * To change this template use File | Settings | File Templates.
 */

class Model_Timeline extends Model{

	protected static $table = 'timeline';

	public static function createSchoolTimeline($type, $school_id, $text, $post_id = false, $parent_id = false, $typePost = POST_TYPE_TEXT, $imgAlias = null, $otherData = array())
	{
		return self::createTimeline($type, NULL, $text, $post_id, $parent_id, $typePost, $imgAlias, $otherData, NULL, NULL, $school_id);
	}

	public static function createGroupTimeline($type, $user_id, $group_id, $text, $post_id = false, $parent_id = false, $typePost = POST_TYPE_TEXT, $imgAlias = null, $otherData = array())
	{
		return self::createTimeline($type, $user_id, $text, $post_id, $parent_id, $typePost, $imgAlias, $otherData, NULL, $group_id);
	}

	public static function createCompanyTimeline($type, $company_id, $text, $post_id = false, $parent_id = false, $typePost = POST_TYPE_TEXT, $imgAlias = null, $otherData = array())
	{
		return self::createTimeline($type, NULL, $text, $post_id, $parent_id, $typePost, $imgAlias, $otherData, $company_id);
	}

	public static function createNewConnectionsTimeline($user_id, $friend_id)
	{
		self::createTimeline(TIMELINE_TYPE_NEWCONNECTION, $user_id, FALSE, FALSE, false, false, null, array('friend_id' => $friend_id));
		self::createTimeline(TIMELINE_TYPE_NEWCONNECTION, $friend_id, FALSE, FALSE, false, false, null, array('friend_id' => $user_id));
	}

	public static function createUpdatePhoto($user_id)
	{
		return self::createTimeline(TIMELINE_TYPE_UPDATEPHOTO, $user_id, FALSE);
	}

	public static function createNewJob($user_id, $profile_experience_id, $job_title, $job_company)
	{
		return self::createTimeline(TIMELINE_TYPE_NEWJOB, $user_id, ($job_title . ' at ' . $job_company), FALSE, false, false, null, array('profile_experience_id' => $profile_experience_id));
	}

	public static function createTimeline($type, $user_id, $text, $post_id = false, $parent_id = false, $typePost = POST_TYPE_TEXT, $imgAlias = null, $otherData = array(), $company_id = NULL, $group_id = NULL, $school_id = NULL)
	{
		if(!isset($otherData['title'])) {
			$otherData['title'] = '';
		}
		if(!isset($otherData['link'])) {
			$otherData['link'] = NULL;
		}
		if(!isset($otherData['isGroupAccept'])) {
			$otherData['isGroupAccept'] = NULL;
		}

		switch($type){
			case TIMELINE_TYPE_POST:
				$post = Model_Posts::createPost($user_id, $text, $typePost, $imgAlias, $otherData['title'], $otherData['link'], $company_id, $group_id, $otherData['isGroupAccept'], $school_id);
				$timeline = self::create(array(
					'user_id' => $user_id,
					'type' => $type,
					'post_id' => $post->id,
					'company_id' => $company_id,
					'group_id' => $group_id,
					'school_id' => $school_id
				));
				return $timeline;
				break;
			case TIMELINE_TYPE_SHAREPOST:
				$timeline = self::create(array(
					'user_id' => $user_id,
					'type' => $type,
					'post_id' => $post_id,
					'content' => $text,
					'parent_id' => $parent_id,
					'post_id' => $post_id,
					'company_id' => $company_id,
					'group_id' => $group_id,
					'school_id' => $school_id
				));
				return $timeline;
				break;
			case TIMELINE_TYPE_LIKE:
				$timeline = self::create(array(
					'user_id' => $user_id,
					'type' => $type,
					'content' => (string)$text,
					'parent_id' => $parent_id,
					'post_id' => $post_id,
					'company_id' => $company_id,
					'group_id' => $group_id,
					'school_id' => $school_id
				));
				return $timeline;
				break;
			case TIMELINE_TYPE_COMMENTS:
				$timeline = self::create(array(
					'user_id' => $user_id,
					'type' => $type,
					'content' => (string)$text,
					'parent_id' => $parent_id,
					'post_id' => $post_id,
					'company_id' => $company_id,
					'group_id' => $group_id,
					'school_id' => $school_id
				));
				return $timeline;
				break;
			case TIMELINE_TYPE_NEWCONNECTION:
				$timeline = self::create(array(
					'user_id' => $user_id,
					'type' => $type,
					'friend_id' => $otherData['friend_id'],
				));
				return $timeline;
				break;
			case TIMELINE_TYPE_UPDATEPHOTO:
				$timeline = self::create(array(
					'user_id' => $user_id,
					'type' => $type
				));
				return $timeline;
				break;
			case TIMELINE_TYPE_NEWJOB:
				$timeline = self::create(array(
					'user_id' => $user_id,
					'type' => $type,
					'profile_experience_id' => $otherData['profile_experience_id'],
				    'content' => $text
				));
				return $timeline;
				break;
		}
	}

	public static function getListCheckContentByUserIdGroupId($user_id, $group_id)
	{
		return self::getListByUserId($user_id, UPDATE_CATEGORY_GROUP_CHECK_CONTENT, false, $group_id);
	}

	public static function getListByUserIdGroupId($user_id, $group_id, $isPopular = FALSE)
	{
		return self::getListByUserId($user_id, UPDATE_CATEGORY_GROUP, false, $group_id, false, array('isPopular' => $isPopular));
	}

	public static function getListByUserIdCompanyId($user_id, $company_id)
	{
		return self::getListByUserId($user_id, UPDATE_CATEGORY_COMPANY, $company_id);
	}

	public static function getListByUserIdSchoolId($user_id, $school_id)
	{
		return self::getListByUserId($user_id, UPDATE_CATEGORY_SCHOOL, false, false, $school_id);
	}

	public static function getListByUserId($user_id, $category = UPDATE_CATEGORY_PEOPLE, $company_id = false, $group_id = false, $school_id = false, array $otherData = array())
	{
		$order = 'timeline.id DESC';
		$isPageDown = TRUE;
		$otherField = '';
		switch($category) {
			case UPDATE_CATEGORY_PEOPLE:
				$where = array('
					(
						(
							(
								(connections.typeApproved = 1 AND connections.user_id = ?)
								OR
								(timeline.user_id = ? AND timeline.type NOT IN (5, 10, ' . TIMELINE_TYPE_NEWCONNECTION . ',' . TIMELINE_TYPE_NEWJOB . '))
							)
							AND
								users.id IS NOT NULL
							AND
								(timeline.user_id = ? OR (timeline.user_id <> ? AND (users.shareActivityInActivityFeed = 1 OR (users.shareActivityInActivityFeed = 0 AND timeline.type in (1, 2)))))
							AND
								timeline.group_id IS NULL
							AND
								parentTimeline.group_id IS NULL
						)
						OR
						(
							posts.company_id IS NOT NULL AND company_follow.user_id IS NOT NULL
						)
						OR
						(
							posts.group_id IS NOT NULL AND timeline.user_id <> ? AND posts.isGroupAccept = 1
							AND
								(
									(posts.user_id = ? AND timeline.type IN (5, 10))
									OR
									(group_discussion_follow.user_id IS NOT NULL AND timeline.type IN (10))
								)
						)
						OR
						(
							posts.school_id IS NOT NULL AND university_follow.user_id IS NOT NULL
						)
					)', $user_id, $user_id, $user_id, $user_id, $user_id, $user_id);
				break;
			case UPDATE_CATEGORY_COMPANY:
				$where = array('
					(posts.company_id in (?) AND company_follow.user_id IS NOT NULL)', $company_id);
				break;
			case UPDATE_CATEGORY_GROUP:
				$isPopular = $otherData['isPopular'];
				$where = array('
					(posts.group_id in (?) AND timeline.type NOT IN (5, 10) AND posts.isGroupAccept = 1)', $group_id);
				if($isPopular) {
					$otherField = '((timeline.countComments * 2) + timeline.countLikes) AS timelinePopularGroup,';
					$order = 'timelinePopularGroup DESC, timeline.createDate DESC';
					$isPageDown = FALSE;
				}
				break;
			case UPDATE_CATEGORY_SCHOOL:
				$where = array('
					(posts.school_id in (?) AND university_follow.user_id IS NOT NULL)', $school_id);
				break;
			case UPDATE_CATEGORY_GROUP_CHECK_CONTENT:
				$where = array('
					(posts.group_id = ? AND timeline.type = 1 AND posts.isGroupAccept IS NULL AND group_members.memberType = ?)', $group_id, GROUP_MEMBER_TYPE_ADMIN);
				break;
		}

		$list = self::getList(array(
			'select' => '	timeline.*,
							users.firstName as userFirstName,
							users.lastName as userLastName,
							users.avaToken as userAvaToken,
							users.alias as userAlias,
							users.id as userId,
							users.whoCanSeeActivity as userWhoCanSeeActivity,
							users.setInvisibleProfile as userSetInvisibleProfile,

							owner.firstName as ownerFirstName,
							owner.lastName as ownerLastName,
							owner.avaToken as ownerAvaToken,
							owner.alias as ownerAlias,
							owner.id as ownerId,
							owner.setInvisibleProfile as ownerSetInvisibleProfile,

							posts.title as postTitle,
							posts.text as postText,
							posts.alias as postAlias,
							posts.typePost as postType,
							posts.link as postLink,
							posts.company_id as postCompanyId,
							posts.group_id as postGroupId,
							posts.school_id as postSchoolId,
							posts.countGroupFollow as postCountGroupFollow,
							posts.countImpressions as postCountImpressions,
							posts.countImpressionsUnique as postCountImpressionsUnique,

							parentTimeline.id as parentId,
							parentTimeline.user_id as parentUserId,
							parentTimeline.company_id as parentCompanyId,
							parentTimeline.school_id as parentSchoolId,
							parentTimeline.group_id as parentGroupId,
							parentTimeline.createDate as parentCreateDate,
							parentTimeline.type as parentType,
							parentTimeline.content as parentContent,
							parentTimeline.countLikes as parentCountLikes,
							parentTimeline.countComments as parentCountComments,
							parentTimeline.countShare as parentCountShare,
							parentTimeline.post_id as parentPostId,
							parentTimeline.parent_id as parentParentId,

							parentUser.id as parentUserId,
							parentUser.avaToken as parentUserAvaToken,
							parentUser.firstName as parentUserFirstName,
							parentUser.lastName as parentUserLastName,
							parentUser.alias as parentUserAlias,
							parentUser.professionalHeadline as parentUserProfessionalHeadline,
							parentUser.setInvisibleProfile as parentUserSetInvisibleProfile,

							companies.id as companyId,
							companies.user_id as companyUserId,
							companies.name as companyName,
							companies.avaToken as companyAvaToken,
							company_follow.user_id as companyFollowUserId,

							groups.id as groupId,
							groups.user_id as groupUserId,
							groups.name as groupName,
							groups.avaToken as groupAvaToken,
							group_members.user_id as groupMemberUserId,
							group_members.memberType as groupMemberType,
							group_discussion_follow.user_id as groupDiscussionFollowUserId,

							timeline_likes.user_id AS timelineLikesUserId,
							timeline_shares.user_id as timelineShareUserId,

							universities.id as schoolId,
							universities.user_id as schoolUserId,
							universities.name as schoolName,
							universities.avaToken as schoolAvaToken,

							friends.firstName as friendFirstName,
							friends.lastName as friendLastName,
							friends.alias as friendAlias,
							friends.professionalHeadline as friendProfessionalHeadline,
							friends.avaToken as friendAvaToken,
							friends.setInvisibleProfile as friendSetInvisibleProfile,
							friends.id as friendId,

							experienceCompany.name as experienceCompanyName,
							experienceCompany.id as experienceCompanyId,
							experienceCompany.avaToken as experienceCompanyAvaToken,
							experienceCompany.isAgree as experienceCompanyIsAgree,

							profile_expirience.dateFrom as experienceCompanyDateFrom,
							profile_expirience.dateTo as experienceCompanyDateTo,
							profile_expirience.isCurrent as experienceCompanyIsCurrent,
							profile_expirience.title as experienceCompanyTitle,
							' . $otherField . '
							(
								SELECT
									  SUBSTRING_INDEX(GROUP_CONCAT(CAST( id AS CHAR ), "_", firstName, " ", lastName ORDER BY timeline_likes.createDate DESC), ",",3)
								FROM
								  timeline_likes
								  INNER JOIN users
									ON users.id = timeline_likes.user_id
									AND users.isRemoved = 0
									AND users.isConfirmed = 1
								WHERE
									(timeline.type IN (5,10,' . TIMELINE_TYPE_UPDATEPHOTO . ') AND timeline_likes.parentTimeline_id = timeline.parent_id)
									OR
									(timeline.type NOT IN (5,10,' . TIMELINE_TYPE_UPDATEPHOTO . ') AND timeline_likes.parentTimeline_id = timeline.id)
								ORDER BY timeline_likes.createDate DESC
							) AS likesPeople,
							(
								SELECT
								  SUBSTRING_INDEX(GROUP_CONCAT(CAST( id AS CHAR ), "_", firstName, " ", lastName ORDER BY timeline_likes.createDate DESC), ",",3)
								FROM
								  timeline_shares
								  INNER JOIN users
									ON users.id = timeline_shares.user_id
									AND users.isRemoved = 0
									AND users.isConfirmed = 1
								WHERE
									(timeline.type IN (5,10) AND timeline_shares.parentTimeline_id = timeline.parent_id)
									OR
									(timeline.type NOT IN (5,10) AND timeline_shares.parentTimeline_id = timeline.id)
								ORDER BY timeline_shares.createDate DESC
							) AS sharesPeople,
							(
								SELECT
								  SUBSTRING_INDEX(GROUP_CONCAT(CAST( id AS CHAR ), "_", firstName, " ", lastName ORDER BY group_discussion_follow.createDate DESC), ",",3)
								FROM
								  group_discussion_follow
								  INNER JOIN users
									ON users.id = group_discussion_follow.user_id
									AND users.isRemoved = 0
									AND users.isConfirmed = 1
								WHERE
									group_discussion_follow.post_id = timeline.post_id AND
									(
										(timeline.type = ' . TIMELINE_TYPE_POST . ' AND timeline.group_id IS NOT NULL )
										OR
										(timeline.type IN (' . TIMELINE_TYPE_LIKE . ',' . TIMELINE_TYPE_COMMENTS . ') AND parentTimeline.group_id IS NOT NULL )
									)
								ORDER BY group_discussion_follow.createDate DESC
							) AS followDiscussion
							',
			'where' => $where,
			'join' => array(
				array(
					'table' => 'connections',
					'type' => 'left',
//					'where' => array('(timeline.user_id = connections.friend_id AND connections.typeApproved = 1 AND connections.user_id = ?) OR (timeline.user_id = ?)', $user_id, $user_id)
					'where' => array('timeline.user_id = connections.friend_id')
				),
				array(
					'table' => 'users',
					'type' => 'left',
					'where' => array('users.id = timeline.user_id AND users.isRemoved = 0 AND users.isConfirmed = 1')
				),
				array(
					'type' => 'left',
					'table' => 'posts',
					'where' => array('posts.id = timeline.post_id')
				),
				array(
					'noQuotes' => true,
					'type' => 'left',
					'table' => 'users as owner',
					'where' => array('owner.id = posts.user_id AND owner.isRemoved = 0 AND owner.isConfirmed = 1')
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
					'where' => array('companies.id = posts.company_id')
				),
				array(
					'type' => 'left',
					'table' => 'universities',
					'where' => array('universities.id = posts.school_id')
				),
				array(
					'type' => 'left',
					'table' => 'company_follow',
					'where' => array('company_follow.company_id = timeline.company_id AND company_follow.user_id = ?', $user_id)
				),
				array(
					'type' => 'left',
					'table' => 'university_follow',
					'where' => array('university_follow.univercity_id = timeline.school_id AND university_follow.user_id = ?', $user_id)
				),
				array(
					'type' => 'left',
					'table' => 'groups',
					'where' => array('groups.id = posts.group_id')
				),
				array(
					'type' => 'left',
					'table' => 'group_members',
					'where' => array('group_members.group_id = groups.id AND group_members.user_id = ? AND group_members.isApproved = 1', $user_id)
				),
//				array(
//					'type' => 'left',
//					'table' => 'group_discussion_follow',
//					'where' => array('group_discussion_follow.group_id = posts.group_id AND group_discussion_follow.post_id = posts.id AND group_discussion_follow.user_id = ?', $user_id)
//				),
				array(
					'type' => 'left',
					'table' => 'timeline_likes',
					'where' => array('(timeline_likes.parentTimeline_id = timeline.id OR (timeline_likes.parentTimeline_id = timeline.parent_id AND timeline.type <> 2)) AND timeline_likes.user_id = ?', $user_id)
				),
				array(
					'type' => 'left',
					'table' => 'timeline_shares',
					'where' => array('(timeline_shares.parentTimeline_id = timeline.id OR (timeline_shares.parentTimeline_id = timeline.parent_id AND timeline.type <> 2)) AND timeline_shares.user_id = ?', $user_id)
				),
				array(
					'type' => 'left',
					'table' => 'group_discussion_follow',
					'where' => array('	group_discussion_follow.post_id = timeline.post_id AND group_discussion_follow.user_id = ? AND
										(
											(timeline.type = ' . TIMELINE_TYPE_POST . ' AND timeline.group_id IS NOT NULL )
											OR
											(timeline.type IN (' . TIMELINE_TYPE_LIKE . ',' . TIMELINE_TYPE_COMMENTS . ') AND parentTimeline.group_id IS NOT NULL )
										)', $user_id)
				),
				array(
					'noQuotes' => TRUE,
					'table' => 'users as friends',
					'type' => 'left',
					'where' => array('friends.id = timeline.friend_id AND friends.isRemoved = 0 AND friends.isConfirmed = 1')
				),
				array(
					'noQuotes' => TRUE,
					'table' => 'users as parentUser',
					'type' => 'left',
					'where' => array('parentUser.id = parentTimeline.user_id AND parentUser.isRemoved = 0 AND parentUser.isConfirmed = 1')
				),
				array(
					'noQuotes' => TRUE,
					'table' => 'profile_expirience',
					'type' => 'left',
					'where' => array('profile_expirience.id = timeline.profile_experience_id')
				),
				array(
					'noQuotes' => TRUE,
					'table' => 'companies as experienceCompany',
					'type' => 'left',
					'where' => array('experienceCompany.id = profile_expirience.company_id')
				),
			),
			'group' => 'id',
			'order' => $order,
		), true, false, 10, $isPageDown);


		Model_Posts::clearPostViews();

		$group_post_ids = array();
		foreach($list['data'] as $timeline_id => $timeline) {
			if(!is_null($timeline->postCompanyId) && $timeline->companyUserId != $user_id) {
				if(!isset($_SESSION['posts_view'][$timeline->post_id])) {
					$_SESSION['posts_view'][$timeline->post_id] = time();

					$check = Model_Company_Post_Impressions::checkIsset($user_id, $timeline->post_id);
					if(!$check) {
						Model_Posts::update(array(
							'countImpressions' => $timeline->postCountImpressions += 1,
							'countImpressionsUnique' => $timeline->postCountImpressionsUnique += 1,
						), $timeline->post_id);
					} else {
						Model_Posts::update(array(
							'countImpressions' => $timeline->postCountImpressions += 1
						), $timeline->post_id);
					}

					Model_Company_Post_Impressions::create(array(
						'user_id' => $user_id,
						'post_id' => $timeline->post_id,
						'company_id' => $timeline->postCompanyId
					));
				}
			}
			if(!is_null($timeline->group_id)) {
				$group_post_ids[$timeline->id] = $timeline->post_id;
			}
		}

//		if(!empty($group_post_ids)) {
//			$peopleLike = Model_Group_Discussion_Follow::getListByPostids($group_post_ids);
//		}

		foreach($list['data'] as $item) {
			Model_User::addUserIdByKey($item, 'user_id');
			Model_User::addUserIdByKey($item, 'friend_id');
			Model_User::addUserIdByKey($item, 'ownerId');
			Model_User::addUserIdByKey($item, 'parentUserId');
			Model_User::addUserIdByKey($item, 'companyUserId');
			Model_User::addUserIdByKey($item, 'groupUserId');
			Model_User::addUserIdByKey($item, 'schoolUserId');
			Model_User::addUserIdByKey($item, 'friendId');

		}
		return $list;
	}


	public static function getItemById($timeline_id, $user_id = false, $company_id = false, $group_id = false, $memberUser_id = FALSE, $school_id = FALSE)
	{
		$where = array('timeline.id = ? ', $timeline_id);

		if($user_id) {
			$where[0] .= ' AND (timeline.user_id = ? OR companies.user_id = ? OR group_members.user_id = ? OR universities.user_id = ?)';
			$where[] = $user_id;
			$where[] = $user_id;
			$where[] = $user_id;
			$where[] = $user_id;
		}
		if($company_id) {
			$where[0] .= ' AND timeline.company_id = ?';
			$where[] = $company_id;
		}
		if($group_id) {
			$where[0] .= ' AND timeline.group_id = ?';
			$where[] = $group_id;
		}
		if($school_id) {
			$where[0] .= ' AND timeline.school_id = ?';
			$where[] = $school_id;
		}

		unset($user_id);
		$user_id = Auth::getInstance()->getIdentity()->id;



		$result = new self(array(
			'select' => '	timeline.*,
							users.firstName as userFirstName,
							users.lastName as userLastName,
							users.avaToken as userAvaToken,
							users.alias as userAlias,
							users.id as userId,
							users.whoCanSeeActivity as userWhoCanSeeActivity,
							users.setInvisibleProfile as userSetInvisibleProfile,

							owner.firstName as ownerFirstName,
							owner.lastName as ownerLastName,
							owner.avaToken as ownerAvaToken,
							owner.id as ownerId,
							owner.setInvisibleProfile as ownerSetInvisibleProfile,

							posts.user_id as postUserId,
							posts.title as postTitle,
							posts.text as postText,
							posts.alias as postAlias,
							posts.typePost as postType,
							posts.link as postLink,
							posts.company_id as postCompanyId,
							posts.group_id as postGroupId,
							posts.school_id as postSchoolId,
							posts.isGroupAccept as postIsGroupAccept,
							posts.countGroupFollow as postCountGroupFollow,

							parentTimeline.id as parentId,
							parentTimeline.user_id as parentUserId,
							parentTimeline.company_id as parentCompanyId,
							parentTimeline.school_id as parentSchoolId,
							parentTimeline.createDate as parentCreateDate,
							parentTimeline.type as parentType,
							parentTimeline.content as parentContent,
							parentTimeline.countLikes as parentCountLikes,
							parentTimeline.countComments as parentCountComments,
							parentTimeline.countShare as parentCountShare,
							parentTimeline.post_id as parentPostId,
							parentTimeline.parent_id as parentParentId,

							parentUser.id as parentUserId,
							parentUser.avaToken as parentUserAvaToken,
							parentUser.firstName as parentUserFirstName,
							parentUser.lastName as parentUserLastName,
							parentUser.professionalHeadline as parentUserProfessionalHeadline,
							parentUser.setInvisibleProfile as parentUserSetInvisibleProfile,

							companies.id as companyId,
							companies.user_id as companyUserId,
							companies.name as companyName,
							companies.avaToken as companyAvaToken,
							company_follow.user_id as companyFollowUserId,

							groups.id as groupId,
							groups.user_id as groupUserId,
							groups.name as groupName,
							groups.avaToken as groupAvaToken,
							group_members.user_id as groupMemberUserId,
							group_members.memberType as groupMemberType,
							group_discussion_follow.user_id as groupDiscussionFollowUserId,

							timeline_likes.user_id AS timelineLikesUserId,
							timeline_shares.user_id as timelineShareUserId,

							universities.id as schoolId,
							universities.user_id as schoolUserId,
							universities.name as schoolName,
							universities.avaToken as schoolAvaToken,

							friends.firstName as friendFirstName,
							friends.lastName as friendLastName,
							friends.alias as friendAlias,
							friends.professionalHeadline as friendProfessionalHeadline,
							friends.avaToken as friendAvaToken,
							friends.setInvisibleProfile as friendSetInvisibleProfile,
							friends.id as friendId,
							(
								SELECT
								  SUBSTRING_INDEX(GROUP_CONCAT(CAST( id AS CHAR ), "_", firstName, " ", lastName ORDER BY timeline_likes.createDate DESC), ",",3)
								FROM
								  timeline_likes
								  INNER JOIN users
									ON users.id = timeline_likes.user_id
									AND users.isRemoved = 0
									AND users.isConfirmed = 1
								WHERE
									(timeline.type IN (5,10,' . TIMELINE_TYPE_UPDATEPHOTO . ') AND timeline_likes.parentTimeline_id = timeline.parent_id)
									OR
									(timeline.type NOT IN (5,10,' . TIMELINE_TYPE_UPDATEPHOTO . ') AND timeline_likes.parentTimeline_id = timeline.id)
								ORDER BY timeline_likes.createDate DESC
							) AS likesPeople,
							(
								SELECT
								  SUBSTRING_INDEX(GROUP_CONCAT(CAST( id AS CHAR ), "_", firstName, " ", lastName ORDER BY timeline_likes.createDate DESC), ",",3)
								FROM
								  timeline_shares
								  INNER JOIN users
									ON users.id = timeline_shares.user_id
									AND users.isRemoved = 0
									AND users.isConfirmed = 1
								WHERE
									(timeline.type IN (5,10) AND timeline_shares.parentTimeline_id = timeline.parent_id)
									OR
									(timeline.type NOT IN (5,10) AND timeline_shares.parentTimeline_id = timeline.id)
								ORDER BY timeline_shares.createDate DESC
							) AS sharesPeople,
							(
								SELECT
								  SUBSTRING_INDEX(GROUP_CONCAT(CAST( id AS CHAR ), "_", firstName, " ", lastName ORDER BY group_discussion_follow.createDate DESC), ",",3)
								FROM
								  group_discussion_follow
								  INNER JOIN users
									ON users.id = group_discussion_follow.user_id
									AND users.isRemoved = 0
									AND users.isConfirmed = 1
								WHERE
									group_discussion_follow.post_id = timeline.post_id AND
									(
										(timeline.type = ' . TIMELINE_TYPE_POST . ' AND timeline.group_id IS NOT NULL )
										OR
										(timeline.type IN (' . TIMELINE_TYPE_LIKE . ',' . TIMELINE_TYPE_COMMENTS . ') AND parentTimeline.group_id IS NOT NULL )
									)
								ORDER BY group_discussion_follow.createDate DESC
							) AS followDiscussion
							',
			'where' => $where,
			'join' => array(
				array(
					'table' => 'connections',
					'type' => 'left',
					'where' => array('(timeline.user_id = connections.friend_id AND connections.typeApproved = 1 AND connections.user_id = ?) OR (timeline.user_id = ?)', $user_id, $user_id)
				),
				array(
					'table' => 'users',
					'type' => 'left',
					'where' => array('users.id = timeline.user_id AND users.isRemoved = 0 AND users.isConfirmed = 1')
				),
				array(
					'type' => 'left',
					'table' => 'posts',
					'where' => array('posts.id = timeline.post_id')
				),
				array(
					'noQuotes' => true,
					'type' => 'left',
					'table' => 'users as owner',
					'where' => array('owner.id = posts.user_id AND owner.isRemoved = 0 AND owner.isConfirmed = 1')
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
					'where' => array('companies.id = posts.company_id')
				),
				array(
					'type' => 'left',
					'table' => 'universities',
					'where' => array('universities.id = posts.school_id')
				),
				array(
					'type' => 'left',
					'table' => 'company_follow',
					'where' => array('company_follow.company_id = companies.id AND company_follow.user_id = ?', $user_id)
				),
				array(
					'type' => 'left',
					'table' => 'university_follow',
					'where' => array('university_follow.univercity_id = timeline.school_id AND university_follow.user_id = ?', $user_id)
				),
				array(
					'type' => 'left',
					'table' => 'groups',
					'where' => array('groups.id = posts.group_id')
				),
				array(
					'type' => 'left',
					'table' => 'group_members',
					'where' => array('group_members.group_id = groups.id AND group_members.user_id IN (?, ?) AND group_members.isApproved = 1', $user_id, $memberUser_id)
				),
				array(
					'type' => 'left',
					'table' => 'group_discussion_follow',
					'where' => array('group_discussion_follow.group_id = posts.group_id AND group_discussion_follow.post_id = posts.id AND group_discussion_follow.user_id = ?', $user_id)
				),
				array(
					'type' => 'left',
					'table' => 'timeline_likes',
					'where' => array('(timeline_likes.parentTimeline_id = timeline.id OR (timeline_likes.parentTimeline_id = timeline.parent_id AND timeline.type <> 2)) AND timeline_likes.user_id = ?', $user_id)
				),
				array(
					'type' => 'left',
					'table' => 'timeline_shares',
					'where' => array('(timeline_shares.parentTimeline_id = timeline.id OR (timeline_shares.parentTimeline_id = timeline.parent_id AND timeline.type <> 2)) AND timeline_shares.user_id = ?', $user_id)
				),
				array(
					'noQuotes' => TRUE,
					'table' => 'users as friends',
					'type' => 'left',
					'where' => array('friends.id = timeline.friend_id AND friends.isRemoved = 0 AND friends.isConfirmed = 1 AND timeline.friend_id <> ?', $user_id)
				),
				array(
					'noQuotes' => TRUE,
					'table' => 'users as parentUser',
					'type' => 'left',
					'where' => array('parentUser.id = parentTimeline.user_id AND parentUser.isRemoved = 0 AND parentUser.isConfirmed = 1')
				),
			)
		));

		Model_User::addUserIdByKey($result, 'user_id');
		Model_User::addUserIdByKey($result, 'friend_id');
		Model_User::addUserIdByKey($result, 'ownerId');
		Model_User::addUserIdByKey($result, 'parentUserId');
		Model_User::addUserIdByKey($result, 'companyUserId');
		Model_User::addUserIdByKey($result, 'groupUserId');
		Model_User::addUserIdByKey($result, 'schoolUserId');
		Model_User::addUserIdByKey($result, 'friendId');
		return $result;
	}

//	public static function getListUncheckedContent($group_id)
//	{
//		return self::getList(array(
//			'where' => array('posts.group_id = ? AND isGroupAccept IS NULL', $group_id),
//			'order' => 'id DESC'
//		), true, false, 10);
//
//
//	}

	public static function getItemByOnlyId($timeline_id)
	{
		$result = new self(array(
			'where' => array('timeline.id = ?', $timeline_id),
		));

		Model_User::addUserIdByKey($result, 'user_id');
		Model_User::addUserIdByKey($result, 'friend_id');
		return $result;
	}

	public static function getItemById_WithoutUserid($timeline_id, $user_id)
	{
		$result = new self(array(
			'select' => '	timeline.*,
							users.firstName as userFirstName,
							users.lastName as userLastName,
							users.setInvisibleProfile as setInvisibleProfile,
							posts.title as postTitle,
							posts.text as postText,
							parentTimeline.countShare as parentCountShare
							',
			'where' => array('timeline.id = ? AND (users.id IS NOT NULL OR timeline.company_id IS NOT NULL OR timeline.school_id IS NOT NULL)', $timeline_id),
			'join' => array(
				array(
					'type' => 'left',
					'table' => 'users',
					'where' => array('users.id = timeline.user_id AND users.isRemoved = 0 AND users.isConfirmed = 1')
				),
				array(
					'type' => 'left',
					'table' => 'posts',
					'where' => array('posts.id = timeline.post_id AND posts.user_id <> ? AND timeline.user_id <> ?', $user_id, $user_id)
				),
				array(
					'type' => 'left',
					'noQuotes' => true,
					'table' => 'timeline as parentTimeline',
					'where' => array('parentTimeline.id = timeline.parent_id')
				)
			)
		));

		Model_User::addUserIdByKey($result, 'user_id');
		Model_User::addUserIdByKey($result, 'friend_id');
		return $result;
	}

	public static function getOwnerTimelineByPostId ($post_id)
	{
		$result = new self(array(
			'select' => '	timeline.*,
							users.firstName as userFirstName,
							users.lastName as userLastName,
							posts.title as postTitle,
							posts.text as postText',
			'join' => array(
				array(
					'table' => 'users',
					'where' => array('users.id = timeline.user_id AND users.isRemoved = 0 AND users.isConfirmed = 1')
				),
				array(
					'type' => 'left',
					'table' => 'posts',
					'where' => array('posts.id = timeline.post_id AND posts.user_id = timeline.user_id AND posts.id = ?', $post_id)
				)
			),
			'limit' => 1
		));

		Model_User::addUserIdByKey($result, 'user_id');
		Model_User::addUserIdByKey($result, 'friend_id');
		return $result;
	}

	public static function getChildrenByTimelime($ids)
	{
		return self::getList(array(
			'where' => array('parent_id in (?)', $ids)
		), false);
	}


	public static function getListByIdsGroupid($ids, $group_id, $user_id)
	{
		$results = self::getList(array(
			'select' => '	timeline.*,
							users.firstName as userFirstName,
							users.lastName as userLastName,
							users.avaToken as userAvaToken,
							users.alias as userAlias,
							users.id as userId,
							owner.firstName as ownerFirstName,
							owner.lastName as ownerLastName,
							owner.id as ownerId,
							owner.setInvisibleProfile as ownerSetInvisibleProfile,
							posts.user_id as postUserId,
							posts.title as postTitle,
							posts.text as postText,
							posts.alias as postAlias,
							posts.typePost as postType,
							posts.link as postLink,
							posts.company_id as postCompanyId,
							posts.group_id as postGroupId,
							posts.countGroupFollow as postCountGroupFollow,
							parentTimeline.id as parentId,
							parentTimeline.user_id as parentUserId,
							parentTimeline.company_id as parentCompanyId,
							parentTimeline.createDate as parentCreateDate,
							parentTimeline.type as parentType,
							parentTimeline.content as parentContent,
							parentTimeline.countLikes as parentCountLikes,
							parentTimeline.countComments as parentCountComments,
							parentTimeline.countShare as parentCountShare,
							parentTimeline.post_id as parentPostId,
							parentTimeline.parent_id as parentParentId,
							companies.id as companyId,
							companies.user_id as companyUserId,
							companies.name as companyName,
							companies.avaToken as companyAvaToken,
							company_follow.user_id as companyFollowUserId,
							groups.id as groupId,
							groups.user_id as groupUserId,
							groups.name as groupName,
							groups.avaToken as groupAvaToken,
							group_members.user_id as groupMemberUserId,
							group_members.memberType as groupMemberType
							',
			'where' => array('timeline.id in (?) AND timeline.group_id = ? AND timeline.post_id IS NOT NULL', $ids, $group_id),
			'join' => array(
				array(
					'table' => 'connections',
					'type' => 'left',
					'where' => array('(timeline.user_id = connections.friend_id AND connections.typeApproved = 1 AND connections.user_id = ?) OR (timeline.user_id = ?)', $user_id, $user_id)
				),
				array(
					'table' => 'users',
					'type' => 'left',
					'where' => array('users.id = timeline.user_id AND users.isRemoved = 0 AND users.isConfirmed = 1')
				),
				array(
					'type' => 'left',
					'table' => 'posts',
					'where' => array('posts.id = timeline.post_id')
				),
				array(
					'noQuotes' => true,
					'type' => 'left',
					'table' => 'users as owner',
					'where' => array('owner.id = posts.user_id AND owner.isRemoved = 0 AND owner.isConfirmed = 1')
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
					'where' => array('companies.id = posts.company_id')
				),
				array(
					'type' => 'left',
					'table' => 'company_follow',
					'where' => array('company_follow.company_id = companies.id AND company_follow.user_id = ?', $user_id)
				),
				array(
					'type' => 'left',
					'table' => 'groups',
					'where' => array('groups.id = posts.group_id')
				),
				array(
					'type' => 'left',
					'table' => 'group_members',
					'where' => array('group_members.group_id = groups.id AND group_members.user_id = ? AND group_members.isApproved = 1', $user_id)
				),
			)
		));


		foreach($results['data'] as $item) {
			Model_User::addUserIdByKey($item, 'user_id');
			Model_User::addUserIdByKey($item, 'friend_id');
			Model_User::addUserIdByKey($item, 'ownerId');
			Model_User::addUserIdByKey($item, 'postUserId');
			Model_User::addUserIdByKey($item, 'parentUserId');
			Model_User::addUserIdByKey($item, 'companyUserId');
			Model_User::addUserIdByKey($item, 'groupUserId');
		}
		return $results;
	}


}