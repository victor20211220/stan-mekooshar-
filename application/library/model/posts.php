<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Ukietech-1
 * Date: 15.03.13
 * Time: 18:04
 * To change this template use File | Settings | File Templates.
 */

class Model_Posts extends Model{

	protected static $table = 'posts';
	protected static $countNewGroupContentByGroupid = FALSE;

	public static function createPost($user_id, $post, $typePost = POST_TYPE_TEXT, $imgAlias = NULL, $title = '', $link = NULL, $company_id = NULL, $group_id = NULL, $isGroupAccept = NULL, $school_id = NULL)
	{
		$post = self::create(array(
			'text' => $post,
			'user_id' => $user_id,
			'typePost' => $typePost,
			'alias' => $imgAlias,
			'title' => $title,
			'link' => $link,
			'company_id' => $company_id,
			'group_id' => $group_id,
			'isGroupAccept' => $isGroupAccept,
			'school_id' => $school_id
		));

		Model_User::addUserId($user_id);
		return $post;
	}

	public static function getItemByTimeline($timeline, $user_id)
	{
		$result = new self(array(
			'where' => array('posts.id = ? AND (posts.user_id = ? OR timeline.user_id = ? OR companies.user_id = ? OR universities.user_id = ?)', $timeline->post_id, $user_id, $user_id, $user_id, $user_id),
			'join' => array(
				array(
					'table' => 'timeline',
					'where' => array('timeline.post_id = posts.id')
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
				)
			)
		));

		Model_User::addUserIdByKey($result, 'user_id');
		return $result;
	}

	public static function getItemDiscussionById($discussion_id)
	{
		$result = new self(array(
			'where' => array('id = ?', $discussion_id)
		));

		Model_User::addUserIdByKey($result, 'user_id');
		return $result;
	}

	public static function getListByCompanyid($company_id)
	{
		$results = self::getList(array(
			'where' => array('posts.company_id = ? ', $company_id),
			'order' => 'id DESC'
		), true, false, 5);


		foreach($results['data'] as $item) {
			Model_User::addUserIdByKey($item, 'user_id');
		}
		return $results;
	}



	public static function getCountUncheckedGroupDiscussion($group_id)
	{
		$count = self::query(array(
			'select' => 'COUNT(group_id) as countItems',
			'where' => array('group_id = ? AND isGroupAccept IS NULL', $group_id),
			'group' => 'group_id'
		))->fetch();

		if(!is_null($count)){
			return $count->countItems;
		} else {
			return 0;
		}
	}

	public static function clearPostClicks()
	{
		if(isset($_SESSION['posts_click'])) {
			foreach($_SESSION['posts_click'] as $key => $item) {
				if($item < (time() - 60)) {
					unset($_SESSION['posts_click'][$key]);
				}
			}
		}
	}

	public static function clearPostViews()
	{
		if(isset($_SESSION['posts_view'])) {
			foreach($_SESSION['posts_view'] as $key => $item) {
				if($item < (time() - 60)) {
					unset($_SESSION['posts_view'][$key]);
				}
			}
		}
	}

	public static function getCountAllNewGroupContent($user_id, $group_id = FALSE)
	{
		if(static::$countNewGroupContentByGroupid === FALSE) {
			$result = self::getList(array(

				'select' => '
						COUNT(posts.group_id) AS countItems,
						posts.group_id AS id
						',
				'where' => array('posts.isGroupAccept IS NULL'),
				'join' => array(
					array(
						'noQuotes' => TRUE,
						'table' => 'group_members as adminGroupMember',
						'where' => array('adminGroupMember.group_id = posts.group_id AND adminGroupMember.memberType = ? AND adminGroupMember.isApproved = 1 AND adminGroupMember.user_id = ?', GROUP_MEMBER_TYPE_ADMIN, $user_id)
					)
				),
				'group' => 'posts.group_id'
			), FALSE);

			static::$countNewGroupContentByGroupid = array();
			foreach($result['data'] as $id => $item) {
				static::$countNewGroupContentByGroupid[$id] = $item->countItems;
			}
		}

		if($group_id) {
			if(isset(static::$countNewGroupContentByGroupid[$group_id])) {
				return static::$countNewGroupContentByGroupid[$group_id];
			} else {
				return FALSE;
			}
		} else {
			$countTotal = 0;
			foreach(static::$countNewGroupContentByGroupid as $id => $count) {
				$countTotal = $countTotal + $count;
			}
			return $countTotal;
		}

	}


}