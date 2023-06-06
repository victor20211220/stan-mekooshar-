<?
// dump($school, 1);
// dump($avasize, 1);
// dump($isSchoolNameLink, 1);

// dump($isLinkProfile, 1);
// dump($isTooltip, 1);
// dump($isManageButton, 1);
// dump($isFollowButton, 1);
// dump($isSchoolDescripion, 1);
// dump($isSchoolDescripionShort, 1);
// dump($isSchoolFollowers, 1);





// -----------------------
// dump($isComments, 1);
// dump($comment, 1);

// dump($isUpdate, 1);
// dump($update, 1);


$school_name = $school->name;


if(!isset($isSchoolNameLink)) {
	$isSchoolNameLink = FALSE;
}
if(!isset($isLinkProfile)) {
	$isLinkProfile = FALSE;
}
if(!isset($isTooltip)) {
	$isTooltip = FALSE;
}
if(!isset($isManageButton)) {
	$isManageButton = FALSE;
}
if(!isset($isFollowButton)) {
	$isFollowButton = FALSE;
}
if(!isset($isSchoolDescripion)) {
	$isSchoolDescripion = FALSE;
}
if(!isset($isSchoolDescripionShort)) {
	$isSchoolDescripionShort = FALSE;
}
if(!isset($isSchoolFollowers)) {
	$isSchoolFollowers = FALSE;
}


if(!isset($avasize)) {
	$avasize = 'avasize_100';
}

$size = 'userava_100';
$nosize = 'noimage_100.jpg';
switch($avasize){
	case 'avasize_44':
		$size = 'userava_44';
		$nosize = 'noimage_44.jpg';
		break;
	case 'avasize_52':
		$size = 'userava_52';
		$nosize = 'noimage_52.jpg';
		break;
	case 'avasize_100':
		$size = 'userava_100';
		$nosize = 'noimage_100.jpg';
		break;
}

if(is_null($school->avaToken)) {
	$schoolAva = '/resources/images/' . $nosize;
} else {
	$schoolAva = Model_Files::generateUrl($school->avaToken, 'jpg', FILE_SCHOOL_AVA, TRUE, false, $size);
}


if($isFollowButton) {
	if(!is_null($school->memberUserId)) {
		$join_btn_text = 'Unfollow';
	} else {
		$join_btn_text = 'Follow';
	}
}


if($isLinkProfile) :
	?><a href="<?= Request::generateUri('schools', $school->id) ?>" class="userava <?= $avasize ?> <?= ($isTooltip === TRUE) ? 'is-tooltip' : '' ?>" title="View schools" data-id="<?= $school->id ?>">
<? else :
	?><div class="userava <?= $avasize ?> <?= ($isFollowButton) ? 'userava-follow' : null ?>" >
<? endif; ?>

		<? if($isSchoolNameLink) : ?>
			<a href="<?= Request::generateUri('schools', $school->id) ?>" class="userava-user_name_link"><img src="<?= $schoolAva ?>" title="" width="106"/></a><div class="userava-info">
		<? else : ?>
			<img src="<?= $schoolAva ?>" title="" width="106"/><div class="userava-info">
		<? endif; ?>


			<div>
				<? if($isSchoolNameLink) : ?>
					<a href="<?= Request::generateUri('schools', $school->id) ?>" class="userava-user_name_link"><b><?=$school_name ?></b></a><br>
				<? else : ?>
					<b class="userava-name"><?= $school_name ?></b><br>
				<? endif; ?>

				<? if($isSchoolDescripion) :
					$description = $school->description;
					if(strlen($description) > 200) :
						$description = substr($description, 0, 200) . '...';
					endif
					?>
					<span class="userava-description"><?= $description ?></span><br>
				<? endif ?>

				<? if($isSchoolDescripionShort) :
					$description = $school->description;
					if(strlen($description) > 80) :
						$description = substr($description, 0, 80) . '...';
					endif
					?>
					<span class="userava-description"><?= $description ?></span><br>
				<? endif ?>

				<? if($isSchoolFollowers) : ?>
					<?= $school->followers ?> follower<?= ($school->followers > 1) ? 's' : null ?><br>
				<? endif; ?>

				<? if($isManageButton) : ?>
					<? $countNewStaffMember = Model_Universities::getCountNewStaffMember($user->id, $school->id) ?>
					<a class="btn-roundblue-border is-counter icons i-editcustom" href="<?= Request::generateUri('schools', 'edit', $school->id) ?>" class="btn-grey"><span></span>Manage <? if($countNewStaffMember > 0) : ?><div class="userpanel-counter" data-count="<?= $countNewStaffMember ?>"><?= ($countNewStaffMember > 9) ? '+9' : $countNewStaffMember ?></div><? endif; ?></a>
				<? endif; ?>

				<? if($isFollowButton && $school->user_id != $user->id) : ?>
					<a href="<?= Request::generateUri('schools', 'followFromList', $school->id) ?>" class="blue-btn" onclick="return web.ajaxGet(this);"><span class="icons <?= (!is_null($school->memberUserId)) ? 'i-closewhite' : 'i-accesswhite' ?>"><span></span></span><?= $join_btn_text ?></a>
				<? endif; ?>
			</div>
		</div>
<? if($isLinkProfile) :
	?></a><?
else :
	?></div><?
endif;