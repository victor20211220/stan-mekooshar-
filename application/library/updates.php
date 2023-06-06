<?php

class Updates
{
	public static function newUpdate($f_Updates_AddUpdate, $user = false, $company = false, $group = false, $school = false)
	{
		$values = $f_Updates_AddUpdate->form->getValues();
		$type = $f_Updates_AddUpdate->getType();
		switch($type){
			case POST_TYPE_TEXT:
				if($school) {
					$timeline = Model_Timeline::createSchoolTimeline(TIMELINE_TYPE_POST, $school->id, $values['text']);
				} elseif($group) {
					$otherData['title'] = trim($values['titletext']);
					if($group->discussionControlType == GROUP_DISSCUSSION_TYPE_FREE || $group->memberType == GROUP_MEMBER_TYPE_ADMIN) {
						$otherData['isGroupAccept'] = 1;
					}
					$timeline = Model_Timeline::createGroupTimeline(TIMELINE_TYPE_POST, $user->id, $group->id, trim($values['text']), false, false, POST_TYPE_TEXT, null, $otherData);
				} elseif($company) {
					$timeline = Model_Timeline::createCompanyTimeline(TIMELINE_TYPE_POST, $company->id, trim($values['text']));
				} else {
					$timeline = Model_Timeline::createTimeline(TIMELINE_TYPE_POST, $user->id, trim($values['text']));
				}


				break;
			case POST_TYPE_IMAGE:
				if(!empty($_SESSION['uploader-list'])) {
					if(!empty($values['selected_image'])){
						if(isset($_SESSION['uploader-list'][$values['selected_image']])) {
							$imageId = $values['selected_image'];
							unset($_SESSION['uploader-list'][$values['selected_image']]);
						}
					}

					if(!isset($imageId)) {
						$imageId = key($_SESSION['uploader-list']);
					}
				}
				$keys = array();
				if(isset($_SESSION['uploader-list'])) {
					$keys = array_keys($_SESSION['uploader-list']);
				}
				if(!empty($keys)) {
					Model_Files::removeByIds($keys, FILE_UPDATES, $user->id);
				}

				$image = Model_Files::getByIds($imageId);
				if($school) {
					$timeline = Model_Timeline::createSchoolTimeline(TIMELINE_TYPE_POST, $school->id, $values['text'], false, false, POST_TYPE_IMAGE, $image[$imageId]->token);
				} elseif($group) {
					$otherData['title'] = $values['titletext'];
					if($group->discussionControlType == GROUP_DISSCUSSION_TYPE_FREE || $group->memberType == GROUP_MEMBER_TYPE_ADMIN) {
						$otherData['isGroupAccept'] = 1;
					}
					$timeline = Model_Timeline::createGroupTimeline(TIMELINE_TYPE_POST, $user->id, $group->id, $values['text'], false, false, POST_TYPE_IMAGE, $image[$imageId]->token, $otherData);
				} elseif($company) {
					$timeline = Model_Timeline::createCompanyTimeline(TIMELINE_TYPE_POST, $company->id, $values['text'], false, false, POST_TYPE_IMAGE, $image[$imageId]->token);
				} else {
					$timeline = Model_Timeline::createTimeline(TIMELINE_TYPE_POST, $user->id, $values['text'], false, false, POST_TYPE_IMAGE, $image[$imageId]->token);
				}

				Model_Files::update(array('parent_id' => $timeline->post_id), $image[$imageId]->id);
				unset($_SESSION['uploader-list']);

				break;
			case POST_TYPE_WEB:
				if(!empty($_SESSION['uploader-list'])) {
					if(!empty($values['selected_image'])){
						if(isset($_SESSION['uploader-list'][$values['selected_image']])) {
							$imageId = $values['selected_image'];
							unset($_SESSION['uploader-list'][$values['selected_image']]);
						}
					}

					if(!isset($imageId)) {
						$imageId = key($_SESSION['uploader-list']);
					}
				} else {
					$imageId = FALSE;
				}

				if(!isset($values['includeImage']) || $values['includeImage'] != TRUE) {
					$imageId = FALSE;
				}

				$keys = array();
				if(isset($_SESSION['uploader-list'])) {
					$keys = array_keys($_SESSION['uploader-list']);
				}
				if(!empty($keys)) {
					Model_Files::removeByIds($keys, FILE_UPDATES, $user->id);
				}
				unset($_SESSION['uploader-list']);

				if($imageId) {
					$image = Model_Files::getByIds($imageId);
					$token = $image[$imageId]->token;
				} else {
					$token = NULL;
				}

				if($school) {
					$timeline = Model_Timeline::createSchoolTimeline(TIMELINE_TYPE_POST, $school->id, $values['urltext'], false, false, POST_TYPE_WEB, $token, array(
						'title' => $values['title'],
						'link' => $_SESSION['updates']['isLink']
					));
				} elseif($group) {
					$timeline = Model_Timeline::createGroupTimeline(TIMELINE_TYPE_POST, $user->id, $group->id, $values['urltext'], false, false, POST_TYPE_WEB, $token, array(
						'title' => $values['title'],
						'link' => $_SESSION['updates']['isLink'],
						'isGroupAccept' => ((($group->discussionControlType == GROUP_DISSCUSSION_TYPE_FREE || $group->memberType == GROUP_MEMBER_TYPE_ADMIN)) ? 1 : NULL)
					));
				} elseif($company) {
					$timeline = Model_Timeline::createCompanyTimeline(TIMELINE_TYPE_POST, $company->id, $values['urltext'], false, false, POST_TYPE_WEB, $token, array(
						'title' => $values['title'],
						'link' => $_SESSION['updates']['isLink']
					));
				} else {
					$timeline = Model_Timeline::createTimeline(TIMELINE_TYPE_POST, $user->id, $values['urltext'], false, false, POST_TYPE_WEB, $token, array(
						'title' => $values['title'],
						'link' => $_SESSION['updates']['isLink']
					));
				}


				unset($_SESSION['updates']);

				if($imageId) {
					Model_Files::update(array('parent_id' => $timeline->post_id), $image[$imageId]->id);
				}

				break;
			case POST_TYPE_DOC:
			case POST_TYPE_PDF:
				// Must be install ImageMagic on the system and libreoffice
				if(!empty($_SESSION['uploader-list'])) {
					if(!empty($values['selected_image'])){
						if(isset($_SESSION['uploader-list'][$values['selected_image']])) {
							$imageId = $values['selected_image'];
							unset($_SESSION['uploader-list'][$values['selected_image']]);
						}
					}

					if(!isset($imageId)) {
						$imageId = key($_SESSION['uploader-list']);
					}
				} else {
					$imageId = FALSE;
				}

				$keys = array();
				if(isset($_SESSION['uploader-list'])) {
					$keys = array_keys($_SESSION['uploader-list']);
				}
				if(!empty($keys)) {
					Model_Files::removeByIds($keys, FILE_UPDATES, $user->id);
				}
				unset($_SESSION['uploader-list']);

				if($imageId) {
					$image = Model_Files::getByIds($imageId);
					$token = $image[$imageId]->token;
				} else {
					$token = NULL;
				}

				if($type == POST_TYPE_DOC){
					$attach = Model_Files::getByIds($_SESSION['updates']['isDoc']);
					if($school) {
						$timeline = Model_Timeline::createSchoolTimeline(TIMELINE_TYPE_POST, $school->id, $values['text'], false, false, POST_TYPE_DOC, $token, array(
							'title' => $attach[$_SESSION['updates']['isDoc']]->name,
							'link' => $attach[$_SESSION['updates']['isDoc']]->url
						));
					} elseif($group) {
						$timeline = Model_Timeline::createGroupTimeline(TIMELINE_TYPE_POST, $user->id, $group->id, $values['text'], false, false, POST_TYPE_DOC, $token, array(
							'title' => $attach[$_SESSION['updates']['isDoc']]->name,
							'link' => $attach[$_SESSION['updates']['isDoc']]->url,
							'isGroupAccept' => ((($group->discussionControlType == GROUP_DISSCUSSION_TYPE_FREE || $group->memberType == GROUP_MEMBER_TYPE_ADMIN)) ? 1 : NULL)
						));
					} elseif($company) {
						$timeline = Model_Timeline::createCompanyTimeline(TIMELINE_TYPE_POST, $company->id, $values['text'], false, false, POST_TYPE_DOC, $token, array(
							'title' => $attach[$_SESSION['updates']['isDoc']]->name,
							'link' => $attach[$_SESSION['updates']['isDoc']]->url
						));
					} else {
						$timeline = Model_Timeline::createTimeline(TIMELINE_TYPE_POST, $user->id, $values['text'], false, false, POST_TYPE_DOC, $token, array(
							'title' => $attach[$_SESSION['updates']['isDoc']]->name,
							'link' => $attach[$_SESSION['updates']['isDoc']]->url
						));
					}
					Model_Files::update(array('parent_id' => $timeline->post_id), $attach[$_SESSION['updates']['isDoc']]->id);
				}

				if($type == POST_TYPE_PDF){
					$attach = Model_Files::getByIds($_SESSION['updates']['isPdf']);
					if($school) {
						$timeline = Model_Timeline::createSchoolTimeline(TIMELINE_TYPE_POST, $school->id, $values['text'], false, false, POST_TYPE_PDF, $token, array(
							'title' => $attach[$_SESSION['updates']['isPdf']]->name,
							'link' => $attach[$_SESSION['updates']['isPdf']]->url
						));
					} elseif($group) {
						$timeline = Model_Timeline::createGroupTimeline(TIMELINE_TYPE_POST, $user->id, $group->id, $values['text'], false, false, POST_TYPE_PDF, $token, array(
							'title' => $attach[$_SESSION['updates']['isPdf']]->name,
							'link' => $attach[$_SESSION['updates']['isPdf']]->url,
							'isGroupAccept' => ((($group->discussionControlType == GROUP_DISSCUSSION_TYPE_FREE || $group->memberType == GROUP_MEMBER_TYPE_ADMIN)) ? 1 : NULL)
						));
					} elseif($company) {
						$timeline = Model_Timeline::createCompanyTimeline(TIMELINE_TYPE_POST, $company->id, $values['text'], false, false, POST_TYPE_PDF, $token, array(
							'title' => $attach[$_SESSION['updates']['isPdf']]->name,
							'link' => $attach[$_SESSION['updates']['isPdf']]->url
						));
					} else {
						$timeline = Model_Timeline::createTimeline(TIMELINE_TYPE_POST, $user->id, $values['text'], false, false, POST_TYPE_PDF, $token, array(
							'title' => $attach[$_SESSION['updates']['isPdf']]->name,
							'link' => $attach[$_SESSION['updates']['isPdf']]->url
						));
					}
					Model_Files::update(array('parent_id' => $timeline->post_id), $attach[$_SESSION['updates']['isPdf']]->id);
				}

				unset($_SESSION['updates']);
				if($imageId) {
					Model_Files::update(array('parent_id' => $timeline->post_id), $image[$imageId]->id);
				}

				break;
		}
		return $timeline;
	}

	public static function share($timeline, $values, $user)
	{
		if(!empty($timeline->parent_id)) {
			$parentId = $timeline->parent_id;
		} else {
			$parentId = $timeline->id;
		}
		$check = Model_Timeline_Shares::checkIsset($user->id, $parentId);

		if($check) {
			return false;
		}

		if(!empty($timeline->parent_id)) {
			Model_Timeline::update(array(
				'countShare' => $timeline->parentCountShare + 1
			), $timeline->parent_id);

		} else {
			$timeline->countShare += 1;
			$timeline->save();
		}

		$newTimeline = Model_Timeline::createTimeline(TIMELINE_TYPE_SHAREPOST, $user->id, $values['text'], $timeline->post_id, $parentId);
		$timelineShare = Model_Timeline_Shares::create(array(
			'user_id' => $user->id,
			'timeline_id' => $newTimeline->id,
			'parentTimeline_id' => $parentId
		));



		$newTimeline = Model_Timeline::getItemById($newTimeline->id, $user->id);

		return $newTimeline;
	}


	// TODO In future review this code (do short)
	public static function like($timeline, $user)
	{
		$resultFunction = 'addCount';
		$resultFunction3 = 'addClass';
		$target2 = false;
		$target4 = false;
		switch($timeline->type) {
			case TIMELINE_TYPE_COMMENTS:
			case TIMELINE_TYPE_LIKE:
				$timelineLike = Model_Timeline_Likes::checkIsset($user->id, $timeline->parent_id);

				if($timelineLike) {
					Model_Timeline::remove($timelineLike->timeline_id);
					Model_Timeline::update(array(
						'countLikes' => $timeline->parentCountLikes - 1
					), $timeline->parent_id);
					$resultFunction = 'negativeCount';
					$resultFunction3 = 'removeClass';
				} else {


					Model_Timeline::update(array(
						'countLikes' => $timeline->parentCountLikes + 1
					), $timeline->parent_id);

					$result = Model_Timeline::createTimeline(TIMELINE_TYPE_LIKE, $user->id, NULL, $timeline->post_id, $timeline->parent_id);
					$newTimeline = Model_Timeline::getItemById($result->id, $user->id);

					Model_Timeline_Likes::create(array(
						'user_id' => $user->id,
						'timeline_id' => $newTimeline->id,
						'parentTimeline_id' => $timeline->parent_id
					));
					$resultFunction = 'addCount';
					$resultFunction3 = 'addClass';
				}
				$timeline->save();
				$target2 = 'li[data-id="timeline_' . $timeline->parent_id . '"] .i-like div';
				$target4 = 'li[data-id="timeline_' . $timeline->parent_id . '"] .i-like';





				break;
			default :
				$timelineLike = Model_Timeline_Likes::checkIsset($user->id, $timeline->id);
				if($timelineLike) {
					Model_Timeline::remove($timelineLike->timeline_id);
					$timeline->countLikes = $timeline->countLikes - 1;
					$resultFunction = 'negativeCount';
					$resultFunction3 = 'removeClass';
				} else {
					$timeline->countLikes = $timeline->countLikes + 1;

					$result = Model_Timeline::createTimeline(TIMELINE_TYPE_LIKE, $user->id, NULL, $timeline->post_id, $timeline->id);
					$newTimeline = Model_Timeline::getItemById($result->id, $user->id);

					Model_Timeline_Likes::create(array(
						'user_id' => $user->id,
						'timeline_id' => $newTimeline->id,
						'parentTimeline_id' => $timeline->id
					));
					$resultFunction = 'addCount';
					$resultFunction3 = 'addClass';
				}
				$timeline->save();
		}



		// for company STATISTIC
		$company_id = false;
		if(!is_null($timeline->company_id)) {
			$company_id = $timeline->company_id;
		} elseif(!is_null($timeline->postCompanyId)) {
			$company_id = $timeline->postCompanyId;
		}

		if($company_id) {
			$post = new Model_Posts($timeline->post_id);
			$check = Model_Company_Post_Likes::checkIsset($user->id, $post->id);

			if($resultFunction == 'addCount') {
				if(!$check) {
					$post->countLikes += 1;
					$post->save();
				}

				Model_Company_Post_Likes::create(array(
					'user_id' => $user->id,
					'post_id' => $post->id,
					'company_id' => $company_id,
					'timeline_id' => $newTimeline->id
				));
			} else {
				if(!$check) {
					$post->countLikes -= 1;
					$post->save();
				}
			}
		}




		// Create notification
//		if($resultFunction == 'addCount' && is_null($timeline->postGroupId)){
//			Model_Notifications::createLikeNotification($user->id, $timeline);
//		}


		return array(
			'resultFunction' => $resultFunction,
			'target' => 'li[data-id="timeline_' . $timeline->id . '"] .i-like div',
			'resultFunction2' => $resultFunction,
			'target2' => ($target2 ? $target2 : null),

			// if is parent
			'resultFunction3' => $resultFunction3,
			'target3' => 'li[data-id="timeline_' . $timeline->id . '"] .i-like',
			'resultFunction4' => $resultFunction3,
			'target4' => ($target4 ? $target4 : null)
		);
	}

	public static function comments($timeline, $values, $user)
	{
		$target2 = FALSE;
		switch($timeline->type) {
			case TIMELINE_TYPE_COMMENTS:
			case TIMELINE_TYPE_LIKE:
				Model_Timeline::update(array(
					'countComments' => $timeline->parentCountComments + 1
				), $timeline->parent_id);

//				$text = $timeline->content;
				$newTimeline = Model_Timeline::createTimeline(TIMELINE_TYPE_COMMENTS, $user->id, NULL, $timeline->post_id, $timeline->parent_id);
				$target2 = '.block-list-updates li[data-id="timeline_' . $timeline->parent_id . '"] .block-list-updatecomments > .list-items > li:first-child';

				$comment = Model_Timeline_Comments::create(array(
					'user_id' => $user->id,
					'timeline_id' => $timeline->parent_id,
					'timelineComment_id' => $newTimeline->id,
					'comment' => $values['text']
				));
				$comment->createDate = CURRENT_DATETIME;
				$comment->setInvisibleProfile = $user->setInvisibleProfile;
				$comment->avaToken = $user->avaToken;
				$comment->firstName = $user->firstName;
				$comment->lastName = $user->lastName;
				$comment->timelineUserId = $timeline->user_id;
				$comment->timelineOwnerId = $timeline->ownerId;
				$comment->companyUserId = $timeline->company_id;
				$comment->groupMemberType = NULL;
				$view = View::factory('pages/updates/item-comment', array(
					'comment' => $comment,
					'isAddComment' => TRUE,
					'isComments' => TRUE,
					'lastTimeline_id' => $timeline->id
				));



				break;
			default:
//				$timeline->countComments += 1;
//				$timeline->save();
				Model_Timeline::update(array(
					'countComments' => $timeline->countComments + 1
				), $timeline->id);

				$newTimeline = Model_Timeline::createTimeline(TIMELINE_TYPE_COMMENTS, $user->id, NULL, $timeline->post_id, $timeline->id);


				$comment = Model_Timeline_Comments::create(array(
					'user_id' => $user->id,
					'timeline_id' => $timeline->id,
					'timelineComment_id' => $newTimeline->id,
					'comment' => $values['text']
				));
				$comment->createDate = CURRENT_DATETIME;
				$comment->setInvisibleProfile = $user->setInvisibleProfile;
				$comment->avaToken = $user->avaToken;
				$comment->firstName = $user->firstName;
				$comment->lastName = $user->lastName;
				$comment->timelineUserId = $timeline->user_id;
				$comment->timelineOwnerId = $timeline->ownerId;
				$comment->companyUserId = $timeline->company_id;
				$comment->groupMemberType = NULL;
				$view = View::factory('pages/updates/item-comment', array(
					'comment' => $comment,
					'isComments' => TRUE,
					'isAddComment' => TRUE
				));
		}



		// for company STATISTIC
		$company_id = false;
		if(!is_null($timeline->company_id)) {
			$company_id = $timeline->company_id;
		} elseif(!is_null($timeline->postCompanyId)) {
			$company_id = $timeline->postCompanyId;
		}

		if($company_id) {
			$post = new Model_Posts($timeline->post_id);
			$post->countComments += 1;
			Model_Company_Post_Comments::create(array(
				'user_id' => $user->id,
				'post_id' => $post->id,
				'company_id' => $company_id,
				'comment_id' => $comment->id
			));

			$post->save();
		}


		// Create notification
//		if(is_null($timeline->postGroupId)) {
//			Model_Notifications::createCommentNotification($user->id, $timeline);
//		}


		return array(
			'target2' => $target2,
			'view' => $view
		);
	}

	public static function deleteComment($comment_id, $user)
	{
		$check = Model_Timeline_Comments::checkCommentByOwners($comment_id, $user->id);

		if($check) {
			Model_Timeline::remove(array(
				'id = ?', $check->timelineComment_id
			));

			$timeline = Model_Timeline::getItemById($check->timeline_id);
			$timeline->countComments -= 1;
			$check->delete();
			$timeline->save();


			// for company STATISTIC
			$company_id = false;
			if(!is_null($timeline->company_id)) {
				$company_id = $timeline->company_id;
			} elseif(!is_null($timeline->postCompanyId)) {
				$company_id = $timeline->postCompanyId;
			}

			if($company_id) {
				$post = new Model_Posts($timeline->post_id);
				$post->countComments -= 1;
				$post->save();
			}
		}

		return $check;
	}


}
