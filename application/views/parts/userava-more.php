<?
//dump($ouser);
// dump($avasize, 1);
// dump($text, 1);
// dump($isUsernameLink, 1);
// dump($keyId, 1);
// dump($form, 1);

// dump($isLinkProfile, 1);
// dump($isTooltip, 1);
// dump($isTextTooltip, 1);
// dump($isRemoveNotableAlumni, 1);
// dump($isShowName, 1);
// dump($isShowSchoolName, 1);
// dump($isShowHeadline, 1);
// dump($isBtnAddConnection, 1);
// dump($isBtnSendMessage, 1);
// dump($isBtnStaffMember, 1);
// dump($isBtnDeleteBlockUser, 1);
// dump(ifIssetSkillsShow, 1);


// dump($isComments, 1);
// dump($comment, 1);

// dump($isUpdate, 1);
// dump($update, 1);

// dump($isCustomInfo, 1);

// dump($isGroupRole, 1);
// dump($groupOwnerId, 1);

// dump($isPopupLoginOrRegister, 1);

if(!isset($keyId)) {
	$keyId = 'id';
}

if(!isset($isPopupLoginOrRegister)) {
	$isPopupLoginOrRegister = FALSE;
}

if(!isset($isBtnDeleteBlockUser)) {
	$isBtnDeleteBlockUser = FALSE;
}
if(!isset($isBtnStaffMember)) {
	$isBtnStaffMember = FALSE;
}
if(!isset($isBtnSendMessage)) {
	$isBtnSendMessage = FALSE;
}
if(!isset($isBtnAddConnection)) {
	$isBtnAddConnection = FALSE;
}
if(!isset($isShowYearNearName)) {
	$isShowYearNearName = FALSE;
}
if(!isset($isShowHeadline)) {
	$isShowHeadline = FALSE;
}
if(!isset($isShowSchoolName)) {
	$isShowSchoolName = FALSE;
}
if(!isset($isRemoveNotableAlumni)) {
	$isRemoveNotableAlumni = FALSE;
}
if(!isset($isShowName)) {
	$isShowName = FALSE;
}
if(!isset($isShowNameOnTop)) {
	$isShowNameOnTop = FALSE;
}
if(!isset($isLinkProfile)) {
	$isLinkProfile = TRUE;
}
if(!isset($isTooltip)) {
	 $isTooltip = FALSE;
}
if(!isset($isTextTooltip)) {
	$isTextTooltip = FALSE;
}
if(!isset($isComments)) {
	$isComments = FALSE;
}
if(!isset($isForm)) {
	$isForm = FALSE;
}
if(!isset($isUsernameLink)) {
	$isUsernameLink = FALSE;
}
if(!isset($isCustomInfo)) {
	$isCustomInfo = FALSE;
}

if(!isset($isGroupRole)) {
	$isGroupRole = FALSE;
}
if(!isset($groupOwnerId)) {
	$groupOwnerId = 0;
}
if(!isset($hideUseravaInfo)) {
	$hideUseravaInfo = FALSE;
}
//
if(!isset($ouser) && $isComments) {
	$ouser = $comment;
}

$levelConnection = Model_User::getLevelWithUser($ouser->$keyId);
//$levelConnection = Model_User::getLevelWithUser(intval($ouser->$keyId));
$levelConnectionText = '';
switch($levelConnection){
	case 1:
		$levelConnectionText = '1st';
		break;
	case 2:
		$levelConnectionText = '2nd';
		break;
	case 3:
		$levelConnectionText = '3rd';
		break;
	case 4:
		$levelConnectionText = '';
		break;
}

if($isTooltip || $isCustomInfo) {
	$company = (!empty($ouser->companyName)) ? $ouser->companyName : $ouser->universityName;
	$headline = $ouser->userHeadline;
	$username = $ouser->userFirstName . ' ' . $ouser->userLastName;
}

if($isShowHeadline) {
	$headline = $ouser->userHeadline;
}

// Check is user invisible
$isAllowedProfile = true;
if($ouser->setInvisibleProfile == USER_PROFILE_INVISIBLE) {
	if($ouser->$keyId == $user->id) {
		$isAllowedProfile = true;
	} else {
		if(!isset($user)) {
			$isAllowedProfile = false;
		} else {
			$allMyConnections = Model_Connections::getListConnectionByUserid($user->id);
			if(in_array($ouser->$keyId, $allMyConnections)) {
				$isAllowedProfile = true;
			} else {
				$isAllowedProfile = false;
			}
		}
	}
}

// Check is user my friend
$isFriend = false;
if(isset($user) && isset($user->id)) {
	$allMyConnections = Model_Connections::getListConnectionByUserid($user->id);
	if(isset($ouser) && in_array($ouser->$keyId, $allMyConnections)) {
		$isFriend = true;
	}
}



// If is my profile, than true
if(isset($ouser) && isset($user->id) && $ouser->$keyId == $user->id) {
	$isAllowedProfile = true;
}


if(!isset($avasize)){
	$avasize = 'avasize_44';
	$invisiblesize = 'blockedimage_44.jpg';
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

if($isComments && isset($comment->userFirstName)) {
	$ouser->avaToken = $comment->userAvaToken;
}

// Check is user block you
$isUserBlockMe = FALSE;
if($ouser->$keyId != $user->id) {
	$isUserBlockMe = Model_User::checkIsUserBlockMe($ouser->$keyId);
}


if(!$isAllowedProfile || $isUserBlockMe){
	$profileAva = '/resources/images/' . $invisiblesize;
} else {
	if(!isset($profileAva) && is_null($ouser->avaToken)) {
		$profileAva = '/resources/images/' . $nosize;
	} else {
		$profileAva = Model_Files::generateUrl($ouser->avaToken, 'jpg', FILE_USER_AVA, TRUE, false, $size);
	}
}



if($isComments && isset($comment->userFirstName)){
	$username = $comment->userFirstName . ' ' . $comment->userLastName;
	$userId = $comment->userId;
}


if(!isset($username)){
	if(!isset($ouser->userFirstName)){
		$username = $ouser->firstName . ' ' . $ouser->lastName;
	} else {
		$username = $ouser->userFirstName . ' ' . $ouser->userLastName;
	}
}

//if(!isset($userId)) {
//	$userId = $ouser->$keyId;
//}
if(isset($userId)) {
	$ouser->$keyId = $userId;
}



if($isGroupRole) {
	if($groupOwnerId == $ouser->id) {
		$ouser->memberType = 3;
	}
}

if($isShowYearNearName && !empty($year)) {
	$username .= ' <span class="userava-year_near_name">`' . substr($year, 2, 2) . '</span>';
}

$myProfile = Auth::getInstance()->getIdentity();
if(isset($ouser->userAlias) && !empty($ouser->userAlias)) {
	$profileLink = Request::generateUri($ouser->userAlias, 'index');
} else {
	$profileLink = Request::generateUri('profile', $ouser->$keyId);
}




if($isLinkProfile) :
		?><a href="<?= $profileLink ?>" class="userava <?= $avasize ?> <?= ($isTooltip === TRUE) ? 'is-tooltip' : '' ?> <?= ($isShowNameOnTop) ? 'is-userava_top_name' : null ?> <?= ($hideUseravaInfo) ? 'is-userava-hidden-info' : null ?>" title="View profile" onclick="<? if($isPopupLoginOrRegister) : ?> return web.popupLoginOrRegister(this); <? endif; ?>" data-id="profile_<?= $ouser->$keyId ?>">
<? else :
	?><div class="userava <?= $avasize ?> <?= ($isShowNameOnTop) ? 'is-userava_top_name' : null ?> <?= ($isComments) ? 'is-comment' : null ?> <?= ($hideUseravaInfo) ? 'is-userava-hidden-info' : null ?>" data-id="profile_<?= $ouser->$keyId ?>">
<? endif; ?>
		<? if($levelConnection) : ?>
			<div class="userava-level_connection"><?= $levelConnectionText ?></div>
		<? endif; ?>
		<? if($isShowNameOnTop) : ?>
			<? if($isUsernameLink && !$isLinkProfile) : ?>
				<a href="<?= $profileLink ?>" class="userava-name"  onclick="<? if($isPopupLoginOrRegister) : ?> return web.popupLoginOrRegister(this); <? endif; ?>"  data-id="profile_<?= $ouser->$keyId ?>">
					<?= $username ?>
				</a>
			<? else: ?>
				<div class="userava-name"><?= $username ?></div>
			<? endif; ?>
		<? endif; ?>
		<? if($isComments) : ?>
			<div class="userava-top">
				<b>
					<? if($isUsernameLink && !$isLinkProfile) : ?>
						<a href="<?= $profileLink ?>" class="userava-user_name_link"  onclick="<? if($isPopupLoginOrRegister) : ?> return web.popupLoginOrRegister(this); <? endif; ?>"  data-id="profile_<?= $ouser->$keyId ?>"><?= $username ?></a>
					<? else : ?>
						<?= $username ?>
					<? endif; ?>
				</b> <?= date('m.d.Y h:i A', strtotime($comment->createDate)) ?>
				<? if($comment->user_id == $user->id || $comment->timelineUserId == $user->id || $comment->timelineOwnerId == $user->id || $comment->companyUserId == $user->id || $comment->groupMemberType == GROUP_MEMBER_TYPE_ADMIN) : ?>
					<a href="<?= Request::generateUri(false, 'deleteComment', $comment->id); ?>" onclick="return box.confirm(this, true)" class="btn-roundblue-border icons i-deletecustom" > <span></span></a>
				<? endif; ?>
			</div>
		<? endif; ?>

		<? if($isUsernameLink && !$isLinkProfile) : ?>
			<a href="<?= $profileLink ?>" class="userava-user_name_link"  onclick="<? if($isPopupLoginOrRegister) : ?> return web.popupLoginOrRegister(this); <? endif; ?>" data-id="profile_<?= $ouser->$keyId ?>"><img src="<?= $profileAva ?>" title="" /></a><div class="userava-info">
		<? else : ?>
			<img src="<?= $profileAva ?>" title="" /><div class="userava-info">
		<? endif; ?>

			<div>
				<? if($isShowName) : ?>
					<? if($isUsernameLink && !$isLinkProfile) : ?>
						<a href="<?= $profileLink ?>" class="userava-name"  onclick="<? if($isPopupLoginOrRegister) : ?> return web.popupLoginOrRegister(this); <? endif; ?>"  data-id="profile_<?= $ouser->$keyId ?>">
							<?= $username ?>
						</a>
					<? else: ?>
						<div class="userava-name"><?= $username ?></div>
					<? endif; ?>
				<? endif; ?>


				<? if($isShowHeadline) : ?>
					<? if($isAllowedProfile && !$isUserBlockMe) : ?>
						<div class="userava-headline"><?= $headline ?></div>
					<? else : ?>
						<div class="list-item-empty_verysmall">private info</div>
					<? endif; ?>
				<? endif; ?>


				<? if($isComments) : ?>
					<div class="userava-comment">
						<?
						$tmp = explode('https://', nl2br(Html::chars($comment->comment)));
						$text = '';
						foreach($tmp as $key => $part) {
							if($key == 0) {
								$text .= $part;
							} else {
								if(strpos($part, ' ')) {
									$url = 'https://' . substr($part, 0, strpos($part, ' '));
									$part = substr($part, strpos($part, ' '));
								} else {
									$url = 'https://' . substr($part, 0);
									$part = '';
								}
								$text .= '<a class="userava-comment_link" href="' . $url . '" target="_blank">' . $url . '</a>' . $part;
							}
						}

						$tmp = explode('https://', $text);
						$text = '';
						foreach($tmp as $key => $part) {
							if($key == 0) {
								$text .= $part;
							} else {
								if(strpos($part, ' ')) {
									$url = 'https://' . substr($part, 0, strpos($part, ' '));
									$part = substr($part, strpos($part, ' '));
								} else {
									$url = 'https://' . substr($part, 0);
									$part = '';
								}

								$text .= '<a class="userava-comment_link" href="' . $url . '" target="_blank">' . $url . '</a>' . $part;
							}
						}
						$text = trim($text);
						while(strpos($text, '<br><br>')) {
							$text = str_replace('<br><br>', '<br>', $text);
						}

						?>
						<?= $text ?>
					</div>
				<? endif; ?>
				<? if($isForm && $form) : ?>
					<?= $form->render(); ?>
				<? endif; ?>

				<? if($isTooltip && !$isTextTooltip) : ?>
					<? if($isUsernameLink && !$isLinkProfile) : ?>
						<a href="<?= $profileLink ?>" class="userava-name"  onclick="<? if($isPopupLoginOrRegister) : ?> return web.popupLoginOrRegister(this); <? endif; ?>"  data-id="profile_<?= $ouser->$keyId ?>">
							<?= $username ?>
						</a>
					<? else: ?>
						<div class="userava-name"><?= $username ?></div>
					<? endif; ?>
					<? if($isAllowedProfile && !$isUserBlockMe) : ?>
						<? if(isset($isTooltip) && $isTooltip == true && !empty($headline) && !empty($company)) : ?>
							<div class="userava-headline_and_company"><?= $headline ?> | <?= $company ?></div>
						<? else: ?>
							<div class="userava-headline"><?= $headline ?></div>
							<div class="userava-company"><?= $company ?></div>
						<? endif; ?>
					<? else: ?>
						<div class="list-item-empty_verysmall">private info</div>
					<? endif; ?>
				<? else: ?>
					<? if(isset($isTextTooltip) && !empty($isTextTooltip)) : ?>
						<? if($isAllowedProfile && !$isUserBlockMe) : ?>
							<?= $isTextTooltip; ?>
						<? else: ?>
							<div class="list-item-empty_verysmall">private info</div>
						<? endif; ?>
					<? endif; ?>
				<? endif; ?>
				<? if($isCustomInfo) : ?>

					<? if($isUsernameLink && !$isLinkProfile) : ?>
						<a href="<?= $profileLink ?>" class="userava-name"  onclick="<? if($isPopupLoginOrRegister) : ?> return web.popupLoginOrRegister(this); <? endif; ?>"  data-id="profile_<?= $ouser->$keyId ?>">
							<?= $username ?>
						</a>
					<? else: ?>
						<div class="userava-name"><?= $username ?></div>
					<? endif; ?>
					<? if($isGroupRole) : ?>
						<div class="userava-group_role"><span>Member role: </span><?= t('group_member_type.' . $ouser->memberType) ?></div>
					<? endif ?>
					<? if($isAllowedProfile && !$isUserBlockMe) : ?>
						<div class="userava-headline"><?= $headline ?></div>
						<div class="userava-company"><?= $company ?></div>
					<? else : ?>
						<div class="list-item-empty_verysmall">private info</div>
					<? endif; ?>
				<? endif; ?>

				<? if($isShowSchoolName) : ?>
					<? if($isAllowedProfile && !$isUserBlockMe) : ?>
						<div class="userava-school_name">School: <?= $schoolName ?></div>
					<? else : ?>
						<div class="list-item-empty_verysmall">private info</div>
					<? endif; ?>
				<? endif; ?>


				<? if($isRemoveNotableAlumni) : ?>
					<a class="blue-btn userava-remove_notable_alumni" href="<?= Request::generateUri('schools', 'removeNotableAlumni', array($school_id, $ouser->profileEducationId)) ?>" onclick="return box.confirm(this, true);"><span class="icons i-closewhite "><span></span></span>delete notable alumni</a>
				<? endif; ?>


				<? if($isBtnAddConnection && $myProfile->id != $ouser->$keyId) : ?>
					<? if($ouser->connectionsTypeApproved === NULL || $ouser->connectionsTypeApproved == ADDCONNECTION_DENY) : ?>
						<a class="btn-grey icons i-add icon-text userava-add_connection" href="<?= Request::generateUri('connections', 'addConnectionsFromUserAvaBlock', $ouser->$keyId) ?>" onclick="return box.load(this);"><span></span>Connect</a>
						<div class="userava-invitation_sent hidden" >Invitation sent</div>
					<? elseif($ouser->connectionsTypeApproved === '0'): ?>
						<div class="userava-invitation_sent" >Invitation sent</div>
					<? endif; ?>
				<? endif; ?>


				<? if($isBtnStaffMember) : ?>
					<? if($ouser->profileSchoolMember == 0) : ?>
						<a class="blue-btn userava-staff_member_apply" href="<?= Request::generateUri('schools', 'staffMemberApply', array($school_id, $ouser->profileExperianceId)) ?>" onclick="return box.confirm(this, true);"><span class="icons i-accesswhite"><span></span></span>Apply</a>
						<a class="blue-btn userava-staff_member_deny" href="<?= Request::generateUri('schools', 'staffMemberDeny', array($school_id, $ouser->profileExperianceId)) ?>" onclick="return box.confirm(this, true);"><span class="icons i-closewhite"><span></span></span>Deny</a>
					<? endif; ?>
					<? if($ouser->profileSchoolMember == 1) : ?>
						<a class="blue-btn userava-staff_member_delete" href="<?= Request::generateUri('schools', 'staffMemberDelete', array($school_id, $ouser->profileExperianceId)) ?>" onclick="return box.confirm(this, true);"><span class="icons i-closewhite"><span></span></span>Delete a staff member</a>
					<? endif; ?>

				<? endif; ?>

				<? if($isBtnSendMessage && $myProfile->id != $ouser->$keyId && $ouser->connectionsTypeApproved == ADDCONNECTION_APPROVED) : ?>
					<a class="btn-grey icons i-messages icon-text userava-send_message" href="<?= Request::generateUri('messages', 'sentMessageFromUserAvaBlock', $ouser->$keyId) ?>" onclick="return box.load(this);"><span></span>Send message</a>
				<? endif; ?>

				<? if($isBtnDeleteBlockUser) : ?>
					<a class="blue-btn userava-user_block_delete" href="<?= Request::generateUri('profile', 'removeBlockUser', $ouser->$keyId) ?>" onclick="return box.confirm(this, true);"><span class="icons i-closewhite"><span></span></span>Delete block</a>
				<? endif; ?>
			</div>
		</div>
<? if($isLinkProfile) :
	?></a><?
else :
	?></div><?
endif;