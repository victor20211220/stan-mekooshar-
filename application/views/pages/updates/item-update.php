<?// dump($timeline, 1); ?>
<?// dump($isEditPanels, 1); ?>
<?// dump($isSub, 1); ?>
<?// dump($isUsernameLink, 1); ?>

<?// dump($isOnlyTextUpdate, 1); ?>
<?// dump($textLen, 1); ?>

<?// dump($avasize, 1); ?>

<?// dump($showComment, 1); ?>
<?// dump($showLike, 1); ?>
<?// dump($showFollow, 1); ?>
<?// dump($showShare, 1); ?>
<?// dump($showFollowDiscussion, 1); ?>
<?// dump($showReadMore, 1); ?>
<?// dump($showAcceptContent, 1); ?>
<?// dump($showDeleteContent, 1); ?>
<?// dump($isDiscussionTitleLink, 1); ?>
<?// dump($showTimelineType, 1); ?>
<?// dump($isPanelsSocial, 1); ?>
<?// dump($isCheck, 1); ?>
<?


// Show checkbox?
if(!isset($isCheck)) {
	$isCheck = FALSE;
}
// Show checkbox?
if(!isset($isDiscussionTitleLink)) {
	$isDiscussionTitleLink = TRUE;
}
// Show panel like, comment, share, follow
if(!isset($isPanelsSocial)) {
	$isPanelsSocial = TRUE;
}
// Show comment button and counter on the update.
if(!isset($showTimelineType)) {
	$showTimelineType = TRUE;
}
// Show comment button and counter on the update.
if(!isset($textLen)) {
	$textLen = FALSE;
}
// Show comment button and counter on the update.
if(!isset($showComment)) {
	$showComment = TRUE;
}
// Show like button and counter on the update.
if(!isset($showLike)) {
	$showLike = TRUE;
}
// Show follow button and counter on the update.
if(!isset($showFollow)) {
	$showFollow = TRUE;
}
// Show share button and counter on the update.
if(!isset($showShare)) {
	$showShare = TRUE;
}
// Show follow discussion button and counter on the update.
if(!isset($showFollowDiscussion)) {
	$showFollowDiscussion = TRUE;
}
// Show readmore button on the check discussion content page.
if(!isset($showReadMore)) {
	$showReadMore = FALSE;
}
// Show accept button on the check discussion content page.
if(!isset($showAcceptContent)) {
	$showAcceptContent = FALSE;
}
// Show delete button on the check discussion content page.
if(!isset($showDeleteContent)) {
	$showDeleteContent = FALSE;
}
// Show edit panels. (For Likes)
if(!isset($isEditPanels)) {
	$isEditPanels = TRUE;
}
// Is it sub content (if timeline is like, share, comment)
if(!isset($isSub)) {
	$isSub = FALSE;
}
// Is username and photo link to his profile
if(!isset($isUsernameLink)) {
	$isUsernameLink = FALSE;
}
// Is it my post?
$isMyUpdate = false;
if($timeline->user_id == $user->id){
	$isMyUpdate = true;
}
// For owner! Can edit 15 min after publish
$isEdit = false;
if(strtotime($timeline->createDate) >= time()-60*15) {
	$isEdit = true;
}





// Cut text to $textLen symbols.
if($textLen) {
	$text = nl2br(Html::chars($timeline->postText));
	$text = substr($text, 0, $textLen) . ((strlen($text) > $textLen) ? '...' : null);
	$timeline->postText = $text;
}

// Check is user invisible
$isAllowedProfile = true;
if(!is_null($timeline->user_id) && $timeline->userSetInvisibleProfile == USER_PROFILE_INVISIBLE) {
	if($timeline->user_id == $user->id) {
		$isAllowedProfile = true;
	} else{
		if(!isset($user)) {
			$isAllowedProfile = false;
		} else {
			$allMyConnections = Model_Connections::getListConnectionByUserid($user->id);
			if(in_array($timeline->user_id, $allMyConnections)) {
				$isAllowedProfile = true;
			} else {
				$isAllowedProfile = false;
			}
		}
	}
}


//if(!isset($timeline->ownerSetInvisibleProfile)) {
//	$isAllowedProfile = true;
//} else {
//	$isAllowedProfile = !$timeline->ownerSetInvisibleProfile;
//}


// Check is friend
$isFriend = false;
if(isset($user)) {
	$allMyConnections = Model_Connections::getListConnectionByUserid($user->id);
	if(in_array($timeline->userId, $allMyConnections)) {
		$isFriend = true;
	}
}




$post_image_size = 'userava_200';


if(!isset($avasize)){
	$avasize = 'avasize_94';
	$nosize = 'noimage_94.jpg';
	$invisiblesize = 'blockedimage_94.jpg';
}
switch($avasize){
	case 'avasize_44':
		$size = 'userava_44';
		$nosize = 'noimage_44.jpg';
		$invisiblesize = 'blockedimage_44.jpg';
		break;
	case 'avasize_50':
		$size = 'userava_50';
		$nosize = 'noimage_50.jpg';
		$invisiblesize = 'blockedimage_50.jpg';
		break;
	case 'avasize_52':
		$size = 'userava_52';
		$nosize = 'noimage_52.jpg';
		$invisiblesize = 'blockedimage_52.jpg';
		break;
	case 'avasize_94':
		$size = 'userava_94';
		$nosize = 'noimage_94.jpg';
		$invisiblesize = 'blockedimage_94.jpg';
		break;
	case 'avasize_174':
		$size = 'userava_174';
		$nosize = 'noimage_174.jpg';
		$invisiblesize = 'blockedimage_174.jpg';
		break;
}
$profileAva = '/resources/images/' . $nosize;




$isGroupDiscussion = FALSE;
if(!is_null($timeline->postGroupId)) {
	$isGroupDiscussion = TRUE;

	$isOwner = FALSE;
	if($timeline->groupMemberType == GROUP_MEMBER_TYPE_ADMIN) {
		$isOwner = TRUE;
	}

	if(!$isSub) {
		$userName = $timeline->userFirstName . ' ' . $timeline->userLastName;
		$user_id = $timeline->user_id;
		if(!is_null($timeline->userAvaToken)) {
			$profileAva = Model_Files::generateUrl($timeline->userAvaToken, 'jpg', FILE_USER_AVA, TRUE, false, $size);
		}
//		$profileUrl = Request::generateUri('profile', $timeline->user_id);
		if(isset($timeline->userAlias)) {
			$profileUrl = Request::generateUri(Html::chars($timeline->userAlias), 'index');
		} else {
			$profileUrl = Request::generateUri('profile', $timeline->user_id);
		}
		$profileUrlTitle = 'View profile';
	} else {
		$userName = $timeline->groupName;
		if(!is_null($timeline->groupAvaToken)) {
			$profileAva = Model_Files::generateUrl($timeline->groupAvaToken, 'jpg', FILE_GROUP_EMBLEM, TRUE, false, $size);
		}
		$profileUrl = Request::generateUri('groups', $timeline->groupId);
		$profileUrlTitle = 'View group';
		$user_id = NULL;
	}

	if($timeline->type == TIMELINE_TYPE_LIKE) {
		$timeline->type = TIMELINE_TYPE_LIKEDUSCUSSION;
	}
	if($timeline->type == TIMELINE_TYPE_COMMENTS) {
		$timeline->type = TIMELINE_TYPE_COMMENTSDUSCUSSION;
	}

	echo View::factory('pages/updates/item-update_group', array(
		'timeline' => $timeline,
		'showComment' => $showComment,
		'showLike' => $showLike,
		'showFollow' => $showFollow,
		'showShare' => FALSE,
		'showFollowDiscussion' => $showFollowDiscussion,
		'showReadMore' => $showReadMore,
		'showAcceptContent' => $showAcceptContent,
		'showDeleteContent' => $showDeleteContent,
		'showTimelineType' => $showTimelineType,
		'isDiscussionTitleLink' => $isDiscussionTitleLink,
		'isEditPanels' => $isEditPanels,
		'isPanelsSocial' => $isPanelsSocial,
		'isCheck' => $isCheck,
		'isSub' => $isSub,
		'isUsernameLink' => $isUsernameLink,
		'isMyUpdate' => $isMyUpdate,
		'isEdit' => $isEdit,
		'isOwner' => $isOwner,
		'textLen' => $textLen,
		'userName' => $userName,
		'profileUrl' => $profileUrl,
		'profileUrlTitle' => $profileUrlTitle,
		'profileAva' => $profileAva,
		'isOnlyTextUpdate' => FALSE,
		'isCompanyUpdate' => FALSE,
		'isOwnerCompanyUpdate' => FALSE,
		'invisiblesize' => $invisiblesize,
		'isAllowedProfile' => $isAllowedProfile,
		'post_image_size' => $post_image_size,
		'user_id' => $user_id
	));
	return;
}








// --------------------------------------------------------
// Show only update. (Without user/company name and image)
if(!isset($isOnlyTextUpdate)) {
	$isOnlyTextUpdate = FALSE;
}

// For generate notification text
if(!isset($isNotification) || $isNotification === FALSE) {
	$isNotification = FALSE;
	$post_image_size = 'userava_200';
} else {
	$isOnlyTextUpdate = TRUE;
	$isSub = TRUE;
	$isEditPanels = FALSE;
	$post_image_size = 'userava_44';
}




if(!is_null($timeline->postCompanyId)) {
	$isOwner = FALSE;
	if($timeline->companyUserId == $user->id) {
		$isOwner = TRUE;
	}

	if(!$isSub && is_null($timeline->company_id)) {
		$userName = $timeline->userFirstName . ' ' . $timeline->userLastName;
		$user_id = $timeline->user_id;
		if(!is_null($timeline->userAvaToken)) {
			$profileAva = Model_Files::generateUrl($timeline->userAvaToken, 'jpg', FILE_USER_AVA, TRUE, false, $size);
		}
//		$profileUrl = Request::generateUri('profile', $timeline->user_id);
		if(isset($timeline->userAlias)) {
			$profileUrl = Request::generateUri(Html::chars($timeline->userAlias), 'index');
		} else {
			$profileUrl = Request::generateUri('profile', $timeline->user_id);
		}
		$profileUrlTitle = 'View profile';
	} else {
		$userName = $timeline->companyName;
		if(!is_null($timeline->companyAvaToken)) {
			$profileAva = Model_Files::generateUrl($timeline->companyAvaToken, 'jpg', FILE_COMPANY_AVA, TRUE, false, $size);
		}
		$profileUrl = Request::generateUri('companies', $timeline->company_id);
		$profileUrlTitle = 'View company';
		$user_id = NULL;
	}

	echo View::factory('pages/updates/item-update_company', array(
		'timeline' => $timeline,
		'showComment' => $showComment,
		'showLike' => $showLike,
		'showFollow' => FALSE,
		'showShare' => $showShare,
		'showFollowDiscussion' => $showFollowDiscussion,
		'showTimelineType' => $showTimelineType,
		'isEditPanels' => $isEditPanels,
		'isPanelsSocial' => $isPanelsSocial,
		'isCheck' => $isCheck,
		'isSub' => $isSub,
		'isUsernameLink' => $isUsernameLink,
		'isMyUpdate' => $isMyUpdate,
		'isEdit' => $isEdit,
		'isOwner' => $isOwner,
		'textLen' => $textLen,
		'userName' => $userName,
		'profileUrl' => $profileUrl,
		'profileUrlTitle' => $profileUrlTitle,
		'profileAva' => $profileAva,
		'isOnlyTextUpdate' => $isOnlyTextUpdate,
		'isNotification' => $isNotification,
		'post_image_size' => $post_image_size,
		'user_id' => $user_id
	));
	return;
}







// --------------------------------------------------------
if(!is_null($timeline->postSchoolId)) {
	$isOwner = FALSE;
	if($timeline->schoolUserId == $user->id) {
		$isOwner = TRUE;
	}

	if(!$isSub && is_null($timeline->school_id)) {
		$userName = $timeline->userFirstName . ' ' . $timeline->userLastName;
		$user_id = $timeline->user_id;
		if(!is_null($timeline->userAvaToken)) {
			$profileAva = Model_Files::generateUrl($timeline->userAvaToken, 'jpg', FILE_USER_AVA, TRUE, false, $size);
		}
//		$profileUrl = Request::generateUri('profile', $timeline->user_id);
		if(isset($timeline->userAlias)) {
			$profileUrl = Request::generateUri(Html::chars($timeline->userAlias), 'index');
		} else {
			$profileUrl = Request::generateUri('profile', $timeline->user_id);
		}
		$profileUrlTitle = 'View profile';
	} else {
		$userName = $timeline->schoolName;
		if(!is_null($timeline->schoolAvaToken)) {
			$profileAva = Model_Files::generateUrl($timeline->schoolAvaToken, 'jpg', FILE_SCHOOL_AVA, TRUE, false, $size);
		}
		$profileUrl = Request::generateUri('schools', $timeline->school_id);
		$profileUrlTitle = 'View school';
		$user_id = NULL;
	}

	echo View::factory('pages/updates/item-update_school', array(
		'timeline' => $timeline,
		'showComment' => $showComment,
		'showLike' => $showLike,
		'showFollow' => FALSE,
		'showShare' => $showShare,
		'showFollowDiscussion' => $showFollowDiscussion,
		'showTimelineType' => $showTimelineType,
		'isEditPanels' => $isEditPanels,
		'isPanelsSocial' => $isPanelsSocial,
		'isCheck' => $isCheck,
		'isSub' => $isSub,
		'isUsernameLink' => $isUsernameLink,
		'isMyUpdate' => $isMyUpdate,
		'isEdit' => $isEdit,
		'isOwner' => $isOwner,
		'textLen' => $textLen,
		'userName' => $userName,
		'profileUrl' => $profileUrl,
		'profileUrlTitle' => $profileUrlTitle,
		'profileAva' => $profileAva,
		'isOnlyTextUpdate' => $isOnlyTextUpdate,
		'isNotification' => $isNotification,
		'post_image_size' => $post_image_size,
		'user_id' => $user_id
	));
	return;
}











// --------------------------------------------------------
$isOwner = FALSE;
//if($timeline->parentUserId == $user->id || $timeline->user_id == $user->id) {
if($timeline->user_id == $user->id) {
	$isOwner = TRUE;
}
	if(!$isSub){
		$userName = $timeline->userFirstName . ' ' . $timeline->userLastName;
		$user_id = $timeline->user_id;
		if(!is_null($timeline->userAvaToken)) {
			$profileAva = Model_Files::generateUrl($timeline->userAvaToken, 'jpg', FILE_USER_AVA, TRUE, false, $size);
		}
		if(isset($timeline->userAlias)) {
			$profileUrl = Request::generateUri(Html::chars($timeline->userAlias), 'index');
		} else {
			$profileUrl = Request::generateUri('profile', $timeline->user_id);
		}
		$profileUrlTitle = 'View profile';
	} else {
		$userName = $timeline->ownerFirstName . ' ' . $timeline->ownerLastName;
		$user_id = $timeline->ownerId;
		if(!is_null($timeline->ownerAvaToken)) {
			$profileAva = Model_Files::generateUrl($timeline->ownerAvaToken, 'jpg', FILE_USER_AVA, TRUE, false, $size);
		}
		if(isset($timeline->ownerAlias) && !empty($timeline->ownerAlias)) {
			$profileUrl = Request::generateUri(Html::chars($timeline->ownerAlias), 'index');
		} else {
			$profileUrl = Request::generateUri('profile', $timeline->ownerId);
		}
		$profileUrlTitle = 'View profile';
	}


echo View::factory('pages/updates/item-update_people', array(
	'timeline' => $timeline,
	'showComment' => $showComment,
	'showLike' => $showLike,
	'showFollow' => $showFollow,
	'showShare' => $showShare,
	'showFollowDiscussion' => $showFollowDiscussion,
	'showReadMore' => $showReadMore,
	'showAcceptContent' => $showAcceptContent,
	'showDeleteContent' => $showDeleteContent,
	'showTimelineType' => $showTimelineType,
	'isDiscussionTitleLink' => FALSE,
	'isEditPanels' => $isEditPanels,
	'isPanelsSocial' => $isPanelsSocial,
	'isCheck' => $isCheck,
	'isSub' => $isSub,
	'isUsernameLink' => $isUsernameLink,
	'isMyUpdate' => $isMyUpdate,
	'isEdit' => $isEdit,
	'isOwner' => $isOwner,
	'textLen' => $textLen,
	'userName' => $userName,
	'profileUrl' => $profileUrl,
	'profileUrlTitle' => $profileUrlTitle,
	'profileAva' => $profileAva,
	'isOnlyTextUpdate' => $isOnlyTextUpdate,
	'isCompanyUpdate' => FALSE,
	'isOwnerCompanyUpdate' => FALSE,
	'isNotification' => $isNotification,
	'post_image_size' => $post_image_size,
	'invisiblesize' => $invisiblesize,
	'isAllowedProfile' => $isAllowedProfile,
	'user_id' => $user_id
));
return;
?>
