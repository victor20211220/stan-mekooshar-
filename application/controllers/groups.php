<?php

class Groups_Controller extends Controller_User
{

	protected $subactive = 'groups';

	public function  before() {
		parent::before();
		$this->view->script('/js/libs/fileuploader.js');
		$this->view->script('/js/uploader.js');
	}

	public function __call($action, $params)
	{
		$this->actionIndex($action);
	}


	public function actionIndex($group_id = false)
	{
		if(!$group_id) {
			$this->response->redirect(Request::generateUri('groups', 'joined'));
			die();
		}

		$isPopular = Request::get('isPopular', FALSE);
		$group = Model_Groups::getItemById_WithoutApproved($group_id, $this->user->id);

		if($group->isAgree == 0) {
			$this->response->redirect(Request::generateUri('groups', 'settings', $group->id));
			die();
		}

		$timelinesGroup = Model_Timeline::getListByUserIdGroupId($this->user->id, $group->id, $isPopular);

		// For paginator
		if((Request::get('pagedown', false) || Request::get('page', false)) && Request::$isAjax) {
			$this->autoRender = false;
			$this->response->setHeader('Content-Type', 'text/json');

			$view = '';
			foreach($timelinesGroup['data'] as $timeline) {
				$view .= View::factory('pages/updates/item-update', array(
					'timeline' => $timeline,
					'isUsernameLink' => TRUE,
					'textLen' => 200,
					'showTimelineType' => FALSE
				));
			}
			$view .= '<li>' . View::factory('common/default-pages', array(
						'controller' => Request::generateUri('groups', $group->id),
						'isBand' => TRUE,
						'autoScroll' => TRUE
					) + $timelinesGroup['paginator']) . '</li>';

			$this->response->body = json_encode(array(
				'status' => true,
				'function_name' => 'changeContent',
				'data' => array(
					'content' => (string)$view,
					'target' => '.block-list-updates > .list-items > li:last-child'
				)
			));
			return;
		}


		if($group->user_id != $this->user->id) {
			Model_Visits::create(array(
				'user_id' => $this->user->id,
				'group_id' => $group->id
			));
		}

		$groupMembers = Model_Group_Members::getListMembersByGroupid($group->id);
		$peopleAlsoViewed = Model_Visits::getListGroupAlsoViewedConnectionsByUser($this->user->id);
		$counter = Model_Group_Members::getCountMemberRequestsByGroupid($group->id);
		$countUnchecked = Model_Posts::getCountUncheckedGroupDiscussion($group->id);

		$this->view->title = 'View Group "' . $group->name . '"';

		if(!is_null($group->memberUserId) && $group->memberIsApproved == 1) {
			$f_Updates_AddUpdate = new Form_Updates_AddUpdate();
			$f_Updates_AddUpdate->setUpdateForGroup($group->id);
		} else {
			$f_Updates_AddUpdate = FALSE;
		}


		$view = new View('pages/groups/index', array(
			// Left top panel
			'group' => $group,
			'counter' => $counter,
			'countUnchecked' => $countUnchecked,

			// Left down panel
			'f_Updates_AddUpdate' => $f_Updates_AddUpdate,
			'timelinesGroup' => $timelinesGroup,

			// Right panel
			'groupMembers' => $groupMembers,
			'peopleAlsoViewed' => $peopleAlsoViewed
		));
		$this->view->content = $view;
	}




	public function actionAddDiscussion($group_id)
	{
		if(Request::$isAjax){
			$this->autoRender = false;
			$this->response->setHeader('Content-Type', 'text/json');

			$group = Model_Groups::getItemById($group_id, $this->user->id);

			$f_Updates_AddUpdate = new Form_Updates_AddUpdate();
			$f_Updates_AddUpdate->setUpdateForGroup($group->id);

			$isError = false;
			if(Request::isPost()) {
				if($f_Updates_AddUpdate->form->validate()) {

					$timeline = Updates::newUpdate($f_Updates_AddUpdate, $this->user, false, $group);
					$f_Updates_AddUpdate->form->clearValues();

					if($group->discussionControlType == GROUP_DISSCUSSION_TYPE_FREE || $group->memberType == GROUP_MEMBER_TYPE_ADMIN) {

						$group->countDiscussions += 1;
						$group->save();

						$this->autoRender = false;
						$this->response->setHeader('Content-Type', 'text/json');

						$newTimeline = Model_Timeline::getItemById($timeline->id, $this->user->id, false, $group->id);
						$content = View::factory('pages/updates/item-update', array(
							'timeline' => $newTimeline,
							'isUsernameLink' => TRUE,
							'showTimelineType' => FALSE,
							'textLen' => 200,
						));

						$this->response->body = json_encode(array(
							'status' => true,
							'function_name' => 'addBlock',
							'data' => array(
								'content' => (string)$content,
								'target' => '.block-list-discussion > .list-items > li:first-child',
								'function_name' => 'updateClear',
								'data' => array(
									'function_name' => 'addClass',
									'data' => array(
										'class' => 'hidden',
										'target' => '.list-items > li:first-child'
									)
								)
							)
						));
						return;
					} else {
						$message = 'Discussion has been sent! After check, administrator share in the group.';

						$this->response->body = json_encode(array(
							'status' => true,
							'function_name' => 'popupShow',
							'data' => array(
								'content' => $message,
								'function_name' => 'updateClear',
								'data' => array()
							)
						));
						return;
					}


				} else {
					$isError = true;
				}
			}

			if($isError) {
//				$content = View::factory('pages/updates/block-create-updates', array(
//					'f_Updates_AddUpdate' => $f_Updates_AddUpdate,
//					'initUploader' => FALSE
//				));
//
//				$this->autoRender = false;
//				$this->response->setHeader('Content-Type', 'text/json');
//				$this->response->body = json_encode(array(
//					'status' => true,
//					'function_name' => 'submitError',
//					'data' => array(
//						'content' => (string)$content,
//						'target' => '.block-create-updates'
//					)
//				));
			}
		}
		$this->response->redirect(Request::generateUri('groups', $group_id));
	}

	public function actionEditDiscussion($timeline_id)
	{
		if(Request::$isAjax){
			$this->autoRender = false;
			$this->response->setHeader('Content-Type', 'text/json');

			$timeline = Model_Timeline::getItemById($timeline_id, $this->user->id);

			$f_Updates_AddUpdate = new Form_Updates_AddUpdate();
			$f_Updates_AddUpdate->edit($timeline);


			$isError = false;
			if(Request::isPost()){
				if($f_Updates_AddUpdate->form->validate()){
					$values = $f_Updates_AddUpdate->form->getValues();

					switch($timeline->type){
						case TIMELINE_TYPE_POST:
							$post = $f_Updates_AddUpdate->post;

							switch($post->typePost) {
								case POST_TYPE_TEXT:
								case POST_TYPE_IMAGE:
								case POST_TYPE_DOC:
								case POST_TYPE_PDF:
									Model_Posts::update(array(
										'text' => $values['text']
									), $timeline->post_id);
									$timeline->postText = $values['text'];

									break;
								case POST_TYPE_WEB:
									Model_Posts::update(array(
										'text' => $values['urltext'],
										'title' => $values['title']
									), $timeline->post_id);
									$timeline->postText = $values['urltext'];
									$timeline->postTitle = $values['title'];
									break;
							}


							break;
						case TIMELINE_TYPE_SHAREPOST:
							$timeline->content = $values['text'];
							$timeline->save();
					}

					$content = View::factory('pages/updates/item-update', array(
						'timeline' => $timeline
					));
					$this->response->body = json_encode(array(
						'status' => true,
						'function_name' => 'changeContent',
						'data' => array(
							'content' => (string)$content,
							'target' => 'li[data-id="timeline_' . $timeline->id . '"]'
						)
					));
					return;

				} else {
					$isError = true;
				}
			}

			$content = $f_Updates_AddUpdate->form;
			if($isError) {
				$this->response->body = json_encode(array(
					'status' => true,
					'function_name' => 'submitError',
					'data' => array(
						'content' => (string)$content,
						'target' => '#' . $f_Updates_AddUpdate->form->attributes['id']
					)
				));
			} else {
				$this->response->body = json_encode(array(
					'status' => true,
					'function_name' => 'editBlock',
					'data' => array(
						'content' => (string)$content,
						'target' => 'li[data-id="timeline_' . $timeline_id . '"] .update-textblock'
					)
				));
			}
			return;
		}

		$this->response->redirect(Request::generateUri('groups', 'index') . Request::getQuery());
	}


	public function actionSettings($group_id)
	{
		$this->view->title = 'Edit groups';
		$group = Model_Groups::getGroupidAdminid($group_id, $this->user->id);
//		$group = Model_Groups::getUserIdGroupid($this->user->id, $group_id);

		$f_Groups_EditGroup = new Form_Groups_EditGroup($group);


		$isError = false;
		if(Request::isPost()) {
			if($f_Groups_EditGroup->form->validate()) {
				$values = $f_Groups_EditGroup->form->getValues();

				Model_Groups::update($values, $group->id);

				$this->message('Changes have been saved');
				$this->response->redirect(Request::generateUri('groups', 'settings', $group->id));

			} else {
				$isError = true;
			}
		} else {
			$f_Groups_EditGroup->setValues($group);
		}

		$counter = Model_Group_Members::getCountMemberRequestsByGroupid($group->id);
		$countUnchecked = Model_Posts::getCountUncheckedGroupDiscussion($group->id);

		if(!$group->isAgree) {
			$useOfTerm = Model_Pages::getItemByCategory(POPUP_CATEGORY_CREATE_GROUP);
			if($useOfTerm) {
				$useOfTerm = $useOfTerm->text;
				$f_Groups_EditGroup->setUseOfTerms($useOfTerm);
			}
		}

		$view = new View('pages/groups/settings', array(
			'f_Groups_EditGroup' => $f_Groups_EditGroup,
			'group' => $group,
			'counter' => $counter,
			'countUnchecked' => $countUnchecked
		));

		$this->view->content = $view;
	}


	public function actionJoined()
	{
		$this->view->title = 'Joined groups';

		$myGroups = Model_Groups::getListByuserId($this->user->id);
		$groups_joined = Model_Groups::getListJoinedGroupByUserid($this->user->id);
		$groups_interested = Model_Groups::getListInterestedByUserid($this->user->id);

		$view = new View('pages/groups/joined', array(
			// Left top panel
			'groups_joined' => $groups_joined,

			// Left down panel

			// Right panel
			'myGroups' => $myGroups,
			'groups_interested' => $groups_interested
		));
		$this->view->content = $view;

	}




	public function actionMembersAdmin($group_id)
	{
		$this->view->title = 'Members Administrator';

		$group = Model_Groups::getGroupidAdminid($group_id, $this->user->id);
//		$group = Model_Groups::getUserIdGroupid($this->user->id, $group_id);
		$memberAdmin = Model_Group_Members::getListMembersByGroupid($group->id, GROUP_MEMBER_TYPE_ADMIN);
		$counter = Model_Group_Members::getCountMemberRequestsByGroupid($group->id);
		$countUnchecked = Model_Posts::getCountUncheckedGroupDiscussion($group->id);

		$view = new View('pages/groups/member_admin', array(
			'memberAdmin' => $memberAdmin,
			'group' => $group,
			'counter' => $counter,
			'countUnchecked' => $countUnchecked
		));

		$this->view->content = $view;
	}


	public function actionMembersUser($group_id)
	{
		$this->view->title = 'Members user';

		$group = Model_Groups::getGroupidAdminid($group_id, $this->user->id);
//		$group = Model_Groups::getUserIdGroupid($this->user->id, $group_id);
		$memberUser = Model_Group_Members::getListMembersByGroupid($group->id, GROUP_MEMBER_TYPE_USER);
		$counter = Model_Group_Members::getCountMemberRequestsByGroupid($group->id);
		$countUnchecked = Model_Posts::getCountUncheckedGroupDiscussion($group->id);

		$view = new View('pages/groups/member_user', array(
			'memberUser' => $memberUser,
			'group' => $group,
			'counter' => $counter,
			'countUnchecked' => $countUnchecked
		));

		$this->view->content = $view;
	}


	public function actionMembersRequest($group_id)
	{
		$this->view->title = 'Members requests';

		$group = Model_Groups::getGroupidAdminid($group_id, $this->user->id);
//		$group = Model_Groups::getUserIdGroupid($this->user->id, $group_id);
		$memberRequests = Model_Group_Members::getListMembersByGroupid($group->id, GROUP_MEMBER_TYPE_USER, FALSE);
		$counter = Model_Group_Members::getCountMemberRequestsByGroupid($group->id);
		$countUnchecked = Model_Posts::getCountUncheckedGroupDiscussion($group->id);

		if($counter == 0 && $group->accessType == GROUP_ACCES_TYPE_FREE) {
			$this->response->redirect(Request::generateUri('groups', 'settings', $group->id));
		}

		$view = new View('pages/groups/member_request', array(
			'memberRequests' => $memberRequests,
			'group' => $group,
			'counter' => $counter,
			'countUnchecked' => $countUnchecked
		));

		$this->view->content = $view;
	}



	public function actionChangeOwner($group_id)
	{
		$this->view->title = 'Change owner';

		$group = Model_Groups::getUserIdGroupid($this->user->id, $group_id);
		$memberAdmin = Model_Group_Members::getListMembersAdminsByGroupid_WithoutMe($group->id, $this->user->id);
		$counter = Model_Group_Members::getCountMemberRequestsByGroupid($group->id);
		$countUnchecked = Model_Posts::getCountUncheckedGroupDiscussion($group->id);

		$view = new View('pages/groups/change_owner', array(
			'memberAdmin' => $memberAdmin,
			'group' => $group,
			'counter' => $counter,
			'countUnchecked' => $countUnchecked
		));

		$this->view->content = $view;
	}




	public function actionDiscussion($timeline_id)
	{

		$discussion = Model_Timeline::getItemById($timeline_id);
		$group = Model_Groups::getItemById($discussion->group_id, $this->user->id);


		if($group->user_id != $this->user->id) {
			Model_Visits::create(array(
				'user_id' => $this->user->id,
				'group_id' => $group->id
			));
		}



		switch($discussion->type) {
			case TIMELINE_TYPE_COMMENTS:
			case TIMELINE_TYPE_LIKE:
				$comments = Model_Timeline_Comments::getListByTimelineId($discussion->parent_id, $this->user->id, 50);
				break;
			default:
				$comments = Model_Timeline_Comments::getListByTimelineId($discussion->id, $this->user->id, 50);
		}

		if(is_null($group->memberUserId) || $discussion->postIsGroupAccept != 1) {
			$f_Updates_AddUpdateComments = FALSE;
		} else {
			$f_Updates_AddUpdateComments = new Form_Updates_AddUpdateComments($discussion->id);
			$f_Updates_AddUpdateComments->setForDiscussionPage($discussion->id);
		}




		// If post comment to discussion
		if(Request::isPost() && Request::isAjax()) {
			if($f_Updates_AddUpdateComments->form->validate()) {
				$values = $f_Updates_AddUpdateComments->form->getValues();

				$results = Updates::comments($discussion, $values, $this->user);

				$this->autoRender = false;
				$this->response->setHeader('Content-Type', 'text/json');
				$this->response->body = json_encode(array(
					'status' => true,
					'function_name' => 'addBlock',
					'data' => array(
						'content' => (string) $results['view'],
						'target' => '.discussion-comments .list-items > li:first-child'
					)
				));
				return;
			}
		}


		// If paginator
		if(Request::get('pagedown', false) && Request::isAjax()) {
			$this->autoRender = false;
			$this->response->setHeader('Content-Type', 'text/json');
			$view = '';
			foreach($comments['data'] as $comment) {
				$view .= View::factory('pages/updates/item-comment', array(
					'comment' => $comment
				));
			}
			$view .= '<li>' . View::factory('common/default-pages', array(
						'controller' => Request::generateUri('groups', 'discussion', $discussion->id),
						'isBand' => TRUE,
						'autoScroll' => TRUE
					) + $comments['paginator']) . '</li>';

			$this->response->body = json_encode(array(
				'status' => true,
				'function_name' => 'changeContent',
				'data' => array(
					'content' => (string)$view,
					'target' => '.discussion-comments .list-items > li:last-child'
				)
			));
			return;
		}


		// If custom load page
		$groupMembers = Model_Group_Members::getListMembersByGroupid($group->id);
		$peopleAlsoViewed = Model_Visits::getListGroupAlsoViewedConnectionsByUser($this->user->id);
		$counter = Model_Group_Members::getCountMemberRequestsByGroupid($group->id);
		$countUnchecked = Model_Posts::getCountUncheckedGroupDiscussion($group->id);
		$this->view->title = 'Discussion "' . $discussion->postTitle . '"';

		$view = new View('pages/groups/discussion', array(
			// Left top panel
			'group' => $group,
			'counter' => $counter,
			'countUnchecked' => $countUnchecked,

			// Left down panel
			'discussion' => $discussion,
			'comments' => $comments,
			'f_Updates_AddUpdateComments' => $f_Updates_AddUpdateComments,

			// Right panel
			'groupMembers' => $groupMembers,
			'peopleAlsoViewed' => $peopleAlsoViewed
		));
		$this->view->content = $view;
	}



	public function actionCheckContent($group_id)
	{
		$this->view->title = 'Check content';

		$group = Model_Groups::getGroupidAdminid($group_id, $this->user->id);
//		if($group->memberType != GROUP_MEMBER_TYPE_ADMIN) {
//			$this->response->redirect(Request::generateUri('groups', $group->id));
//		}

		$contents = Model_Timeline::getListCheckContentByUserIdGroupId($this->user->id, $group->id);

		// For paginator
		if(Request::get('pagedown', false) && Request::$isAjax) {
			$this->autoRender = false;
			$this->response->setHeader('Content-Type', 'text/json');

			$view = '';
			foreach($contents['data'] as $timeline) {
				$view .= View::factory('pages/updates/item-update', array(
					'timeline' => $timeline,
					'isUsernameLink' => TRUE,
					'textLen' => 200,
					'showTimelineType' => FALSE,
					'isEditPanels' => FALSE,
					'isCheck' => TRUE,
					'showLike' => FALSE,
					'showComment' => FALSE,
					'showFollow' => FALSE,
					'showReadMore' => TRUE
				));
			}
			$view .= '<li>' . View::factory('common/default-pages', array(
						'controller' => Request::generateUri('groups', 'checkContent', $group->id),
						'isBand' => TRUE,
						'autoScroll' => TRUE
					) + $contents['paginator']) . '</li>';

			$this->response->body = json_encode(array(
				'status' => true,
				'function_name' => 'changeContent',
				'data' => array(
					'content' => (string)$view,
					'target' => '.block-list-updates > .list-items > li:last-child'
				)
			));
			return;
		}

		$counter = Model_Group_Members::getCountMemberRequestsByGroupid($group->id);
		$countUnchecked = Model_Posts::getCountUncheckedGroupDiscussion($group->id);

		if($countUnchecked == 0 && $group->discussionControlType == GROUP_DISSCUSSION_TYPE_FREE) {
			$this->response->redirect(Request::generateUri('groups', 'settings', $group->id));
		}

		$view = new View('pages/groups/check_content', array(
			'contents' => $contents,
			'group' => $group,
			'counter' => $counter,
			'countUnchecked' => $countUnchecked
		));

		$this->view->content = $view;
	}


	public function actionMembers($group_id)
	{
		$this->view->title = 'Members group';

		$group = Model_Groups::getItemById($group_id, $this->user->id);

		if($group->isAgree == 0) {
			$this->response->redirect(Request::generateUri('groups', $group->id));
			die();
		}

		$counter = Model_Group_Members::getCountMemberRequestsByGroupid($group->id);
		$countUnchecked = Model_Posts::getCountUncheckedGroupDiscussion($group->id);
		$groupMembers = Model_Group_Members::getListMembersByGroupid($group->id);

		$f_Groups_FindMemberInGroup = new Form_Groups_FindMemberInGroup($group->id);


		$view = new View('pages/groups/members', array(
			'group' => $group,
			'counter' => $counter,
			'countUnchecked' => $countUnchecked,
			'f_Groups_FindMemberInGroup' => $f_Groups_FindMemberInGroup,
			'groupMembers' => $groupMembers
		));

		$this->view->content = $view;
	}

	public function actionJoin($group_id)
	{
		$this->view->title = 'Join/Leave group';

		$group = $this->join($group_id);

		$this->response->redirect(Request::generateUri('groups', $group->id));
	}

	public function actionJoinFromList($group_id)
	{
		if(Request::$isAjax){
			$this->autoRender = false;
			$this->response->setHeader('Content-Type', 'text/json');

			$group = $this->join($group_id);

			$view = View::factory('parts/groupsava-more', array(
				'group' => $group,
				'avasize' => 'avasize_52',
				'isGroupNameLink' => TRUE,
				'isGroupIndustry' => TRUE,
				'isFollowButton' => TRUE
			));

			$this->response->body = json_encode(array(
				'status' => true,
				'function_name' => 'changeInnerContent',
				'data' => array(
					'target' => 'li[data-id="group_' . $group->id . '"]',
					'content' => (string) $view
				)
			));
			return;
		}
		$this->response->redirect(Request::generateUri('groups', $group_id));
	}

	public function actionJoinFromSearch($group_id)
	{
		if(Request::$isAjax){
			$this->autoRender = false;
			$this->response->setHeader('Content-Type', 'text/json');

			$group = $this->join($group_id);

			$view = View::factory('pages/search/groups/item-search-results', array(
				'group' => $group,
			));

			$this->response->body = json_encode(array(
				'status' => true,
				'function_name' => 'changeContent',
				'data' => array(
					'target' => 'li[data-id="group_' . $group->id . '"]',
					'content' => (string) $view
				)
			));
			return;
		}
		$this->response->redirect(Request::generateUri('groups', $group_id));
	}


	public function actionRemoveGroup($group_id)
	{
		$this->view->title = 'Delete group';

		$myGroup = Model_Groups::getUserIdGroupid($this->user->id, $group_id);
		$counter = Model_Group_Members::getCountMemberRequestsByGroupid($myGroup->id);
		$countUnchecked = Model_Posts::getCountUncheckedGroupDiscussion($myGroup->id);

		$view = new View('pages/groups/groupdelete', array(
			// Left top panel

			// Left down panel

			// Right panel
			'group' => $myGroup,
			'counter' => $counter,
			'countUnchecked' => $countUnchecked
		));
		$this->view->content = $view;

	}

	public function actionCreateGroup()
	{
		if(Request::$isAjax){
			$this->autoRender = false;
			$this->response->setHeader('Content-Type', 'text/json');

			$f_Groups_CreateGroup = new Form_Groups_CreateGroup();

			$isError = false;
			if(Request::isPost()){
				if($f_Groups_CreateGroup->form->validate()){
					$values = $f_Groups_CreateGroup->form->getValues();

					$group = Model_Groups::create(array(
						'name' => $values['groupName'],
						'user_id' => $this->user->id,
                        'isAgree' => 1,
					));

					Model_Group_Members::create(array(
						'user_id' => $this->user->id,
						'group_id' => $group->id,
						'memberType' => GROUP_MEMBER_TYPE_ADMIN,
						'isApproved' => 1,
                        ));

					$this->response->body = json_encode(array(
						'status' => true,
						'function_name' => 'redirect',
						'data' => array(
							'url' => Request::generateUri('groups', $group->id)
						)
					));
					return;
				} else {
					$isError = true;
				}
			}

			$content = View::factory('parts/pbox-form', array(
				'title' => 'Create group page',
				'content' => View::factory('popups/groups/creategroup', array(
						'f_Groups_CreateGroup' => $f_Groups_CreateGroup->form
					))
			));

			$this->response->body = json_encode(array(
				'status' => (!$isError),
				'content' => (string)$content,
				'popupsize' => 'message'
			));
			return;
		}

		$this->response->redirect(Request::generateUri('groups', 'joined'));
	}


	public function actionChangeRoleToAdmin($group_id, $profile_ids)
	{
		if(Request::$isAjax){
			$this->autoRender = false;
			$this->response->setHeader('Content-Type', 'text/json');

			$group = Model_Groups::getGroupidAdminid($group_id, $this->user->id);
//			$group = Model_Groups::getUserIdGroupid($this->user->id, $group_id);
			$profile_ids = explode(',', $profile_ids);

			$group_members = Model_Group_Members::getListMembersByGroupidProfilesids($group->id, $profile_ids);

			$ids = array();
			$remove_items = array();
			foreach($group_members['data'] as $group_member){
				if($group_member->memberType = GROUP_MEMBER_TYPE_USER) {
					$ids[] = $group_member->user_id;
					$remove_items[] = 'li[data-id="member_' . $group_member->user_id . '"]';
				}
			}

			if(!empty($ids)) {
				Model_Group_Members::update(array(
					'memberType' => GROUP_MEMBER_TYPE_ADMIN
				), array('group_id = ? AND user_id in (?) AND isApproved = 1', $group->id, $ids));
			}

			$counter = Model_Group_Members::getCountMemberByGroupid($group->id);

			$function_name = 'removeItem';
			$content = '';
			$target = $remove_items;
			if($counter == 0) {
				$memberUser = Model_Group_Members::getListMembersByGroupid($group->id, GROUP_MEMBER_TYPE_USER);

				$function_name = 'changeContent';
				$content = (string)View::factory('pages/groups/block-group_member_user', array(
					'memberUser' => $memberUser,
					'group' => $group
				));
				$target = '.block-group_member_user';
			}


			$this->response->body = json_encode(array(
				'status' => true,
				'function_name' => $function_name,
				'data' => array(
					'content' => $content,
					'target' => $target
				)
			));
			return;

		}
		$this->response->redirect(Request::generateUri('groups', 'membersUser', $group_id));
	}


	public function actionChangeRoleToMember($group_id, $profile_ids)
	{
		if(Request::$isAjax){
			$this->autoRender = false;
			$this->response->setHeader('Content-Type', 'text/json');

			$group = Model_Groups::getGroupidAdminid($group_id, $this->user->id);
//			$group = Model_Groups::getUserIdGroupid($this->user->id, $group_id);
			$profile_ids = explode(',', $profile_ids);

			$group_members = Model_Group_Members::getListMembersByGroupidProfilesids($group->id, $profile_ids);

			$ids = array();
			$remove_items = array();
			foreach($group_members['data'] as $group_member){
				if($group_member->memberType == GROUP_MEMBER_TYPE_ADMIN && $group_member->user_id != $group->user_id) {
					$ids[] = $group_member->user_id;
					$remove_items[] = 'li[data-id="member_' . $group_member->user_id . '"]';
				}
			}

			if(!empty($ids)) {
				Model_Group_Members::update(array(
					'memberType' => GROUP_MEMBER_TYPE_USER
				), array('group_id = ? AND user_id in (?) AND isApproved = 1', $group->id, $ids));
			}

			if(in_array($this->user->id, $ids)) {
				$this->response->body = json_encode(array(
					'status' => true,
					'function_name' => 'redirect',
					'data' => array(
						'url' => Request::generateUri('groups', $group->id)
					)
				));
				return;
			}

			$this->response->body = json_encode(array(
				'status' => true,
				'function_name' => 'removeItem',
				'data' => array(
					'target' => $remove_items
				)
			));
			return;

		}
		$this->response->redirect(Request::generateUri('groups', 'membersAdmin', $group_id));
	}

	public function actionChangeRoleToOwner($group_id, $profile_id)
	{
		if(Request::$isAjax){
			$this->autoRender = false;
			$this->response->setHeader('Content-Type', 'text/json');

			$group = Model_Groups::getUserIdGroupid($this->user->id, $group_id);



			$group_member = Model_Group_Members::getListMembersByGroupidProfilesids($group->id, array($profile_id));

			if(count($group_member['data']) == 0) {
				$this->response->body = json_encode(array(
					'status' => false,
				));
				return;
			}

			$group_member = current($group_member['data']);

			if($group_member->memberType = GROUP_MEMBER_TYPE_ADMIN && $group_member->user_id != $this->user->id) {
				Model_Groups::update(array(
					'user_id' => $group_member->user_id
				), $group->id);
			}

			$this->response->body = json_encode(array(
				'status' => true,
				'function_name' => 'redirect',
				'data' => array(
					'url' => Request::generateUri('groups', 'checkContent', $group->id)
				)
			));
			return;

		}
		$this->response->redirect(Request::generateUri('groups', 'changeOwner', $group_id));
	}



	public function actionRemoveMember($group_id, $profile_ids)
	{
		if(Request::$isAjax){
			$this->autoRender = false;
			$this->response->setHeader('Content-Type', 'text/json');

			$group = Model_Groups::getGroupidAdminid($group_id, $this->user->id);
//			$group = Model_Groups::getUserIdGroupid($this->user->id, $group_id);
			$profile_ids = explode(',', $profile_ids);

			$group_members = Model_Group_Members::getListMembersByGroupidProfilesids($group->id, $profile_ids);

			$ids = array();
			$remove_items = array();
			foreach($group_members['data'] as $group_member){
				if($group_member->memberType = GROUP_MEMBER_TYPE_USER) {
					$ids[] = $group_member->user_id;
					$remove_items[] = 'li[data-id="member_' . $group_member->user_id . '"]';
				}
			}

			if(!empty($ids)) {
				Model_Group_Members::remove(array('group_id = ? AND user_id in (?) AND isApproved = 1 AND memberType = ?', $group->id, $ids, GROUP_MEMBER_TYPE_USER));
				$group->members -= count($ids);
				$group->save();
			}

			$counter = Model_Group_Members::getCountMemberRequestsByGroupid($group->id);

			$function_name = 'removeItem';
			$content = '';
			$target = $remove_items;
			if($counter == 0) {
				$memberUser = Model_Group_Members::getListMembersByGroupid($group->id, GROUP_MEMBER_TYPE_USER);

				$function_name = 'changeContent';
				$content = (string)View::factory('pages/groups/block-group_member_user', array(
					'memberUser' => $memberUser,
					'group' => $group
				));
				$target = '.block-group_member_user';
			}

			$this->response->body = json_encode(array(
				'status' => true,
				'function_name' => $function_name,
				'data' => array(
					'content' => $content,
					'target' => $target,
					'function_name' => 'negativeCount',
					'data' => array(
						'target' => '.group_head-followers > span',
						'num' => count($ids)
					)
				)
			));
			return;

		}
		$this->response->redirect(Request::generateUri('groups', 'membersUser', $group_id));
	}

	public function actionAcceptRequest($group_id, $profile_ids)
	{
		if(Request::$isAjax){
			$this->autoRender = false;
			$this->response->setHeader('Content-Type', 'text/json');

			$group = Model_Groups::getGroupidAdminid($group_id, $this->user->id);
//			$group = Model_Groups::getUserIdGroupid($this->user->id, $group_id);
			$profile_ids = explode(',', $profile_ids);

			$group_members = Model_Group_Members::getListMembersByGroupidProfilesids($group->id, $profile_ids, FALSE);

			$ids = array();
			$remove_items = array();
			foreach($group_members['data'] as $group_member){
				if($group_member->memberType = GROUP_MEMBER_TYPE_USER) {
					$ids[] = $group_member->user_id;
					$remove_items[] = 'li[data-id="member_' . $group_member->user_id . '"]';
				}
			}

			if(!empty($ids)) {
				Model_Group_Members::update(array(
					'isApproved' => TRUE
				), array('group_id = ? AND user_id in (?) AND isApproved = 0 AND memberType = ?', $group->id, $ids, GROUP_MEMBER_TYPE_USER));
				$group->members += count($ids);
				$group->save();
			}

			$counter = Model_Group_Members::getCountMemberRequestsByGroupid($group->id);

			$function_name = 'removeItem';
			$content = '';
			$target = $remove_items;
			if($counter == 0) {
				$memberRequests = Model_Group_Members::getListMembersByGroupid($group->id, GROUP_MEMBER_TYPE_USER, FALSE);

				$function_name = 'changeContent';
				$content = (string)View::factory('pages/groups/block-group_member_request', array(
					'memberRequests' => $memberRequests
				));
				$target = '.block-group_member_request';
			}

			$this->response->body = json_encode(array(
				'status' => true,
				'function_name' => $function_name,
				'data' => array(
					'content' => $content,
					'target' => $target,
					'function_name' => 'addCount',
					'data' => array(
						'target' => '.group_head-followers > span',
						'num' => count($ids),
						'function_name' => 'negativeCount',
						'data' => array(
							'target' => '.menupanel-requests',
							'num' => count($ids)
						)
					)
				)
			));
			return;

		}
		$this->response->redirect(Request::generateUri('groups', 'membersRequest', $group_id));
	}


	public function actionDeclineRequest($group_id, $profile_ids)
	{
		if(Request::$isAjax){
			$this->autoRender = false;
			$this->response->setHeader('Content-Type', 'text/json');

			$group = Model_Groups::getGroupidAdminid($group_id, $this->user->id);
//			$group = Model_Groups::getUserIdGroupid($this->user->id, $group_id);
			$profile_ids = explode(',', $profile_ids);

			$group_members = Model_Group_Members::getListMembersByGroupidProfilesids($group->id, $profile_ids, FALSE);

			$ids = array();
			$remove_items = array();
			foreach($group_members['data'] as $group_member){
				if($group_member->memberType = GROUP_MEMBER_TYPE_USER) {
					$ids[] = $group_member->user_id;
					$remove_items[] = 'li[data-id="member_' . $group_member->user_id . '"]';
				}
			}

			if(!empty($ids)) {
				Model_Group_Members::remove(array('group_id = ? AND user_id in (?) AND isApproved = 0 AND memberType = ?', $group->id, $ids, GROUP_MEMBER_TYPE_USER));
			}

			$counter = Model_Group_Members::getCountMemberRequestsByGroupid($group->id);

			$function_name = 'removeItem';
			$content = '';
			$target = $remove_items;
			if($counter == 0) {
				$memberRequests = Model_Group_Members::getListMembersByGroupid($group->id, GROUP_MEMBER_TYPE_USER, FALSE);

				$function_name = 'changeContent';
				$content = (string)View::factory('pages/groups/block-group_member_request', array(
					'memberRequests' => $memberRequests
				));
				$target = '.block-group_member_request';
			}


			$this->response->body = json_encode(array(
				'status' => true,
				'function_name' => $function_name,
				'data' => array(
					'content' => $content,
					'target' => $target,
					'function_name' => 'negativeCount',
					'data' => array(
						'target' => '.menupanel-requests',
						'num' => count($ids)
					)
				)
			));
			return;

		}
		$this->response->redirect(Request::generateUri('groups', 'membersRequest', $group_id));
	}

	public function actionFollowDiscussion($timeline_id)
	{
		if(Request::$isAjax){
			$this->autoRender = false;
			$this->response->setHeader('Content-Type', 'text/json');

			$timeline = Model_Timeline::getItemById($timeline_id);
			$group = Model_Groups::getItemById($timeline->group_id);

			$check = Model_Group_Discussion_Follow::checkIsset($this->user->id, $group->id, $timeline->post_id);

			if($check) {
				Model_Group_Discussion_Follow::remove(array('user_id = ? AND group_id = ? AND post_id = ?', $this->user->id, $group->id, $timeline->post_id));
				$postCountGroupFollow = $timeline->postCountGroupFollow;
				$postCountGroupFollow --;

				$this->response->body = json_encode(array(
					'status' => true,
					'function_name' => 'negativeCount',
					'data' => array(
						'target' => 'li[data-id="timeline_' . $timeline->id . '"] .follow-discussion > div',
						'function_name' => 'removeClass',
						'data' => array(
							'target' => 'li[data-id="timeline_' . $timeline->id . '"] .i-followdiscussion',
							'class' => 'active'
						)
					)
				));
			} else {
				Model_Group_Discussion_Follow::create(array(
					'user_id' => $this->user->id,
					'group_id' => $group->id,
					'post_id' => $timeline->post_id,
				));
				$postCountGroupFollow = is_null($timeline->postCountGroupFollow) ? 0 : $timeline->postCountGroupFollow;
				$postCountGroupFollow ++;

				$this->response->body = json_encode(array(
					'status' => true,
					'function_name' => 'addCount',
					'data' => array(
						'target' => 'li[data-id="timeline_' . $timeline->id . '"] .follow-discussion > div',
						'function_name' => 'addClass',
						'data' => array(
							'target' => 'li[data-id="timeline_' . $timeline->id . '"] .i-followdiscussion',
							'class' => 'active'
						)
					)
				));
			}

			Model_Posts::update(array(
				'countGroupFollow' => $postCountGroupFollow
			), $timeline->post_id);

			return;
		}
		$this->response->redirect(Request::generateUri('groups', 'joined'));
	}


	public function actionAcceptDiscussions($group_id, $timeline_ids, $isFromDiscussion = FALSE)
	{
		if(Request::$isAjax){
			$this->autoRender = false;
			$this->response->setHeader('Content-Type', 'text/json');

			$group = Model_Groups::getGroupidAdminid($group_id, $this->user->id);
//			$group = Model_Groups::getItemById($group_id, $this->user->id);
//			if($group->memberType != GROUP_MEMBER_TYPE_ADMIN) {
//				$this->response->redirect(Request::generateUri('groups', $group->id));
//			}

			$timeline_ids = explode(',', $timeline_ids);
			$timelines = Model_Timeline::getListByIdsGroupid($timeline_ids, $group_id, $this->user->id);

			$post_ids = array();
			$remove_item = array();
			$timeline_ids = array();
			foreach($timelines['data'] as $timeline) {
				$post_ids[$timeline->post_id] = true;
				$remove_item[] = 'li[data-id="timeline_' . $timeline->id . '"]';
				$timeline_ids[] = $timeline->id;
			}

			if(!empty($post_ids)) {
				Model_Posts::update(array(
					'isGroupAccept' => 1
				), array('id in (?)', array_keys($post_ids)));

				Model_Timeline::update(array(
					'createDate' => CURRENT_DATETIME
				), array('id in (?)', array_values($timeline_ids)));

				$group->countDiscussions += count($post_ids);
				$group->save();
			}

			if($isFromDiscussion) {
				$discussion = Model_Timeline::getItemById(current($timeline_ids));
				$comments = Model_Timeline_Comments::getListByTimelineId($discussion->id, $this->user->id, 50);
				$f_Updates_AddUpdateComments = new Form_Updates_AddUpdateComments($discussion->id);
				$f_Updates_AddUpdateComments->setForDiscussionPage($discussion->id);
				$view_discussion =  View::factory('pages/groups/block-discussion', array(
					'discussion' => $discussion,
					'comments' => $comments,
					'f_Updates_AddUpdateComments' => $f_Updates_AddUpdateComments,
				));

				$this->response->body = json_encode(array(
					'status' => true,
					'function_name' => 'changeContent',
					'data' => array(
						'content' => (string) $view_discussion,
						'target' => '.block-discussion'
					)
				));
			} else {
				$countUnchecked = Model_Posts::getCountUncheckedGroupDiscussion($group->id);
				$function_name = 'removeItem';
				$target = $remove_item;
				$content = '';

				if($countUnchecked == 0) {
					$contents = Model_Timeline::getListCheckContentByUserIdGroupId($this->user->id, $group->id);

					$function_name = 'changeContent';
					$content = (string)View::factory('pages/groups/block-group_check_content', array(
						'contents' => $contents,
						'group' => $group
					));
					$target = '.block-group_check_content';
				}


				$this->response->body = json_encode(array(
					'status' => true,
					'function_name' => $function_name,
					'data' => array(
						'content' => $content,
						'target' => $target,
						'function_name' => 'setCount',
						'data' => array(
							'target' => '.menupanel-contents',
							'content' => '0'
						)
					)
				));
			}
			return;
		}
		$this->response->redirect(Request::generateUri('groups', $group->id));
	}


	public function actionDeleteDiscussions($group_id, $timeline_ids, $isFromDiscussion = FALSE)
	{
		if(Request::$isAjax){
			$this->autoRender = false;
			$this->response->setHeader('Content-Type', 'text/json');

			$group = Model_Groups::getItemById($group_id, $this->user->id);
			if($group->memberType != GROUP_MEMBER_TYPE_ADMIN) {
				$this->response->redirect(Request::generateUri('groups', $group->id));
			}

			$timeline_ids = explode(',', $timeline_ids);
			$timelines = Model_Timeline::getListByIdsGroupid($timeline_ids, $group_id, $this->user->id);

			$post_ids = array();
			$remove_item = array();
			foreach($timelines['data'] as $timeline) {
				$post_ids[$timeline->post_id] = true;
				$remove_item[] = 'li[data-id="timeline_' . $timeline->id . '"]';
			}

			if(!empty($post_ids)) {
				Model_Posts::remove(array('id in (?)', array_keys($post_ids)));
			}

			if($isFromDiscussion) {
				$this->response->body = json_encode(array(
					'status' => true,
					'function_name' => 'redirect',
					'data' => array(
						'url' => Request::generateUri('groups', 'checkContent', $group->id),
					)
				));
			} else {
				$countUnchecked = Model_Posts::getCountUncheckedGroupDiscussion($group->id);
				$function_name = 'removeItem';
				$target = $remove_item;
				$content = '';

				if($countUnchecked == 0) {
					$contents = Model_Timeline::getListCheckContentByUserIdGroupId($this->user->id, $group->id);

					$function_name = 'changeContent';
					$content = (string)View::factory('pages/groups/block-group_check_content', array(
						'contents' => $contents,
						'group' => $group
					));
					$target = '.block-group_check_content';
				}


				$this->response->body = json_encode(array(
					'status' => true,
					'function_name' => $function_name,
					'data' => array(
						'content' => $content,
						'target' => $target,
						'function_name' => 'setCount',
						'data' => array(
							'target' => '.menupanel-contents',
							'content' => '0'
						)
					)
				));
			}
			return;
		}
		$this->response->redirect(Request::generateUri('groups', $group->id));
	}


	public function actionRemoveEmblem($group_id)
	{
		if(Request::$isAjax){
			$this->autoRender = false;
			$this->response->setHeader('Content-Type', 'text/json');

			$group = Model_Groups::getGroupidAdminid($group_id, $this->user->id);
//			$group = Model_Groups::getItemById($group_id, $this->user->id);
			Model_Files::removeByType($group->id, FILE_GROUP_EMBLEM);

			$group->avaToken = NULL;
			$group->save();

			$content = View::factory('pages/groups/block-ava_emblem', array(
				'group' => $group
			));

			$this->response->body = json_encode(array(
				'status' => true,
				'function_name' => 'changeContent',
				'data' => array(
					'content' => (string)$content,
					'target' => '.block-groupemblem'
				)
			));
			return;
		}

		$this->response->redirect(Request::generateUri('groups', 'edit', $group_id));
	}


	public function actionCropEmblem($group_id, $isSave = FALSE)
	{
		if(Request::$isAjax){
			$this->autoRender = false;
			$this->response->setHeader('Content-Type', 'text/json');

			$group = Model_Groups::getGroupidAdminid($group_id, $this->user->id);

			$image = Model_Files::getByToken($group->avaToken);
			$message = Model_Files::cropImage($image->id, $isSave);

			$this->response->body = json_encode($message);
			return;

		}

		$this->response->redirect(Request::generateUri('companies', 'edit', $company_id));
	}

	public function actionCropCover($group_id, $isSave = FALSE)
	{
		if(Request::$isAjax){
			$this->autoRender = false;
			$this->response->setHeader('Content-Type', 'text/json');

			$group = Model_Groups::getGroupidAdminid($group_id, $this->user->id);

			$image = Model_Files::getByToken($group->coverToken);
			$message = Model_Files::cropImage($image->id, $isSave);

			$this->response->body = json_encode($message);
			return;

		}

		$this->response->redirect(Request::generateUri('companies', 'edit', $company_id));
	}


	public function actionRemoveCover($group_id)
	{
		if(Request::$isAjax){
			$this->autoRender = false;
			$this->response->setHeader('Content-Type', 'text/json');

			$group = Model_Groups::getGroupidAdminid($group_id, $this->user->id);
//			$group = Model_Groups::getItemById($group_id, $this->user->id);
			Model_Files::removeByType($group->id, FILE_GROUP_COVER);

			$group->coverToken = NULL;
			$group->save();

			$content = View::factory('pages/groups/block-ava_cover', array(
				'group' => $group
			));

			$this->response->body = json_encode(array(
				'status' => true,
				'function_name' => 'changeContent',
				'data' => array(
					'content' => (string)$content,
					'target' => '.block-groupcover'
				)
			));
			return;
		}

		$this->response->redirect(Request::generateUri('groups', 'edit', $group_id));
	}


	public function actionRemove($group_id)
	{
		$this->view->title = 'Remove group';
		$group = Model_Groups::getUserIdGroupid($this->user->id, $group_id);

		Model_Groups::remove($group->id);

		$this->message('Group has been succesfully removed!');
		$this->response->redirect(Request::generateUri('groups', 'joined'));
	}

	public function actionDeleteComment($comment_id)
	{
		if(Request::$isAjax){
			$this->autoRender = false;
			$this->response->setHeader('Content-Type', 'text/json');

			$check = Updates::deleteComment($comment_id, $this->user);

			$this->response->body = json_encode(array(
				'status' => true,
				'function_name' => 'removeItem',
				'data' => array(
					'target' => 'li[data-id="comment_' . $check->id . '"]',
					'function_name' => 'negativeComments',
					'data' => array(
						'target' => 'li[data-id="timeline_' . $check->timeline_id . '"] .i-comments div'
					)
				)
			));
			return;
		}

		$this->response->redirect(Request::generateUri('groups', 'joined') . Request::getQuery());
	}

	public function actionDeleteDiscussion($timeline_id){
		if(Request::$isAjax){
			$this->autoRender = false;
			$this->response->setHeader('Content-Type', 'text/json');

			$timeline = Model_Timeline::getItemById($timeline_id, $this->user->id);
			$group = Model_Groups::getItemById($timeline->group_id);

			if(($timeline->type == TIMELINE_TYPE_POST && $timeline->groupMemberType == GROUP_MEMBER_TYPE_ADMIN) || $timeline->ownerId == $this->user->id) {
				Model_Posts::remove($timeline->post_id);
			} else {
				$this->response->redirect(Request::generateUri('groups', 'discussion', $timeline_id));
			}

			$group->countDiscussions -= 1;
			$group->save();

			$this->response->body = json_encode(array(
				'status' => true,
				'function_name' => 'removeItem',
				'data' => array(
					'target' => 'li[data-id="timeline_' . $timeline->id . '"]'
				)
			));
			return;
		}
		$this->response->redirect(Request::generateUri('groups', 'discussion', $timeline_id));
	}

	protected function join($group_id)
	{
		$group = Model_Groups::getItemById_WithoutApproved($group_id, $this->user->id);

		if(!is_null($group->memberUserId)) {
			if($group->memberIsApproved == 1) {
				$group->members -= 1;
				$group->save();
			}
			Model_Group_Members::remove(array('group_id = ? AND user_id = ?', $group->id, $this->user->id));

			$group->memberUserId = NULL;
			$group->memberIsApproved = NULL;
		} else {
			if($group->accessType == GROUP_ACCES_TYPE_FREE) {
				Model_Group_Members::create(array(
					'group_id' => $group->id,
					'user_id' => $this->user->id,
					'memberType' => GROUP_MEMBER_TYPE_USER,
					'isApproved' => 1
				));
				$group->members += 1;
				$group->save();
				$group->memberUserId = $this->user->id;
				$group->memberIsApproved = 1;
			} else {
				Model_Group_Members::create(array(
					'group_id' => $group->id,
					'user_id' => $this->user->id,
					'memberType' => GROUP_MEMBER_TYPE_USER,
					'isApproved' => 0
				));
				$group->memberUserId = $this->user->id;
				$group->memberIsApproved = false;
			}
		}

		return $group;
	}
}