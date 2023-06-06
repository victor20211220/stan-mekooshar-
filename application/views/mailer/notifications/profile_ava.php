<?
//dump($ouser, 1);
//dump($user, 1);


if(!isset($keyId)) {
	$keyId = 'id';
}


$company = (!empty($ouser->companyName)) ? $ouser->companyName : $ouser->universityName;
$headline = $ouser->userHeadline;
$username = $ouser->userFirstName . ' ' . $ouser->userLastName;

$size = 'userava_52';
$nosize = 'noimage_52.jpg';
$invisiblesize = 'blockedimage_52.jpg';

// Check is user invisible
$isAllowedProfile = true;
if($ouser->setInvisibleProfile == USER_PROFILE_INVISIBLE) {
	if($ouser->$keyId == $user->id) {
		$isAllowedProfile = true;
	} else {
		if(!isset($user)) {
			$isAllowedProfile = false;
		} else {
			$allMyConnections = Model_Connections::getListConnectionByUserid($ouser->id, $ouser);
			if(in_array($ouser->$keyId, $allMyConnections)) {
				$isAllowedProfile = true;
			} else {
				$isAllowedProfile = false;
			}
		}
	}
}


// Check is user block you
$isUserBlockMe = FALSE;
if($ouser->$keyId != $user->id) {
	$isUserBlockMe = Model_Profile_Blocked::checkIsIInBlockListUser($ouser->id);
}


if(!$isAllowedProfile || $isUserBlockMe){
	$profileAva = '/resources/images/' . $invisiblesize;
} else {
	if(!isset($profileAva) && is_null($ouser->avaToken)) {
		$profileAva = Url::site('/resources/images/' . $nosize);
	} else {
		$profileAva = Url::site(Model_Files::generateUrl($ouser->avaToken, 'jpg', FILE_USER_AVA, TRUE, false, $size));
	}
}


if(isset($ouser->userAlias) && !empty($ouser->userAlias)) {
	$profileLink = Request::generateUri($ouser->userAlias, 'index');
} else {
	$profileLink = Request::generateUri('profile', $ouser->$keyId);
}

?>
<div style="position: relative; display: inline-block; min-width: 200px; min-height: 50px; line-height: 11px; min-height: 58px; " data-id="profile_<?= $ouser->$keyId ?>">
	<a href="<?= $profileLink ?>" style="display: inline-block; text-align: left; font-size: 14px; color: #000; text-decoration: none; outline: 0 !important;"  data-id="profile_<?= $ouser->$keyId ?>">
		<img style="width: 52px; height: 52px;" src="<?= $profileAva ?>" title="" />
	</a>
	<div class="userava-info" style="width: 550px; min-height: 50px; position: relative; display: inline-block; margin-left: 5px; padding: 2px; opacity: 1; color: #000; vertical-align: top; text-align: left; line-height: 1.1em; font-size: 12px;">
		<a href="<?= $profileLink ?>" class="userava-name" style="font-weight: bold; text-align: left; font-size: 14px; padding-bottom: 3px; display: block; color: #000; text-decoration: none; outline: 0 !important; word-break: break-word; word-wrap: break-word;"  data-id="profile_<?= $ouser->$keyId ?>">
			<?= $username ?>
		</a>

		<? if($isAllowedProfile && !$isUserBlockMe) : ?>
			<div style="font-size: 11px; line-height: 11px; padding: 2px 0 0 0; word-break: break-word; word-wrap: break-word;"><?= $headline ?></div>
			<div style="font-size: 11px; color: #129fcd; line-height: 11px; padding: 2px 0 0 0; word-break: break-word; word-wrap: break-word;"><?= $company ?></div>
		<? else : ?>
			<div style="width: 100%; margin: 0; text-align: left; color: #c6c6c6; font-size: 11px; line-height: 11px; padding: 0 0 2px 0; word-break: break-word; word-wrap: break-word;">private info</div>
		<? endif; ?>
	</div>
</div>