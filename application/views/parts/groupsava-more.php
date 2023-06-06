<?
// dump($group, 1);
// dump($avasize, 1);
// dump($isGroupNameLink, 1);

// dump($isLinkProfile, 1);
// dump($isTooltip, 1);
// dump($isManageButton, 1);
// dump($isFollowButton, 1);
// dump($isGroupDescripion, 1);
// dump($isGroupDescripionShort, 1);
// dump($isGroupIndustry, 1);
// dump($isGroupType, 1);
// dump($isGroupSize, 1);
// dump($isGroupFollowers, 1);

// dump($isGroupDescription, 1);
// dump($maxSizeDescription, 1);





// -----------------------
// dump($isComments, 1);
// dump($comment, 1);

// dump($isUpdate, 1);
// dump($update, 1);


$group_name = $group->name;
//$industries = t('industries');
//if(!empty($group->mainIndustry) && isset($industries[$group->mainIndustry])) {
//	$industry = $industries[$group->mainIndustry];
//}
//$description = $group->description;
//if(strlen($description) > 50)  {
//	$description = substr($description, 0, 50) . '...';
//}


if(!isset($maxSizeDescription)) {
	$maxSizeDescription = 50;
}
if(!isset($isGroupDescription)) {
	$isGroupDescription = FALSE;
}
if(!isset($isGroupNameLink)) {
	$isGroupNameLink = FALSE;
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
if(!isset($isGroupDescripion)) {
	$isGroupDescripion = FALSE;
}
if(!isset($isGroupIndustry)) {
	$isGroupIndustry = FALSE;
}
if(!isset($isGroupType)) {
	$isGroupType = FALSE;
}
if(!isset($isGroupSize)) {
	$isGroupSize = FALSE;
}
if(!isset($isGroupFollowers)) {
	$isGroupFollowers = FALSE;
}
if(!isset($isGroupDescripionShort)) {
	$isGroupDescripionShort = FALSE;
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

if(is_null($group->avaToken)) {
	$groupAva = '/resources/images/' . $nosize;
} else {
	$groupAva = Model_Files::generateUrl($group->avaToken, 'jpg', FILE_GROUP_EMBLEM, TRUE, false, $size);
}


if($isFollowButton) {
	if(!is_null($group->memberUserId)) {
		if($group->memberIsApproved == 1){
			$join_btn_text = 'Leave';
		} else {
			$join_btn_text = 'Cancel request';
		}
	} else {
		$join_btn_text = 'Join';
	}
}


if($isLinkProfile) :
	?><a href="<?= Request::generateUri('groups', $group->id) ?>" class="userava <?= $avasize ?> <?= ($isTooltip === TRUE) ? 'is-tooltip' : '' ?>" title="View group" data-id="<?= $group->id ?>">
<? else :
	?><div class="userava <?= $avasize ?> <?= ($isFollowButton) ? 'userava-follow' : null ?>" >
<? endif; ?>

		<? if($isGroupNameLink) : ?>
			<a href="<?= Request::generateUri('groups', $group->id) ?>" class="userava-user_name_link"><img src="<?= $groupAva ?>" title="" width="106px"/></a><div class="userava-info">
		<? else : ?>
			<img src="<?= $groupAva ?>" title="" /><div class="userava-info">
		<? endif; ?>


			<div>
				<? if($isGroupNameLink) : ?>
					<a href="<?= Request::generateUri('groups', $group->id) ?>" class="userava-user_name_link"><b><?=$group_name ?></b></a><br>
				<? else : ?>
					<b class="userava-name"><?= $group_name ?></b><br>
				<? endif; ?>

				<? if($isGroupDescripion) :
					$description = $group->description;
					if(strlen($description) > $maxSizeDescription) :
						$description = substr($description, 0, $maxSizeDescription) . '...';
					endif
					?>
					<span><?= $description ?></span><br>
				<? endif ?>

				<? if($isGroupIndustry) : ?>
					<? $industries = t('industries'); ?>
					<? if(!empty($group->industry) && isset($industries[$group->industry])) : ?>
						<?= $industries[$group->industry] ?><br>
					<? endif; ?>
				<? endif; ?>

				<? if($isGroupType) : ?>
					<? $types = t('company_type'); ?>
					<? if(!empty($group->type) && isset($types[$group->type])) : ?>
						<?= $types[$group->type] ?><br>
					<? endif; ?>
				<? endif; ?>

				<? if($isGroupSize) : ?>
					<? $sizes = t('company_number_of_employer'); ?>
					<? if(!empty($group->size) && isset($sizes[$group->size])) : ?>
						<?= $sizes[$group->size] ?> employers<br>
					<? endif; ?>
				<? endif; ?>

				<? if($isGroupDescripionShort) : ?>
					<div class="userava-group_description_short">
						<? if(!empty($group->descriptionShort)) : ?>
							<?= $group->descriptionShort ?> <br>
						<? endif ?>
					</div>
				<? endif ?>

				<? if($isGroupFollowers) : ?>
					<?= $group->followers ?> follower<?= ($group->followers > 1) ? 's' : null ?><br>
				<? endif; ?>

				<? if($isManageButton) : ?>
					<? $countNewManage = Model_Group_Members::getCountAllNewMemberByGroupid($user->id, $group->id) ?>
					<? $countNewContent = Model_Posts::getCountAllNewGroupContent($user->id, $group->id) ?>
					<? $newGroup = $countNewManage + $countNewContent ?>
					<a class="btn-roundblue-border is-counter icons i-editcustom" href="<?= Request::generateUri('groups', 'settings', $group->id) ?>" class="btn-grey"><span></span>Manage <? if($newGroup > 0) : ?><div class="userpanel-counter" data-count="<?= $newGroup ?>"><?= ($newGroup > 9) ? '+9' : $newGroup ?></div><? endif; ?></a>
				<? endif; ?>

				<? if($isFollowButton && $group->user_id != $user->id) : ?>
					<a href="<?= Request::generateUri('groups', 'joinFromList', $group->id) ?>" class="blue-btn" onclick="return web.ajaxGet(this);"><span class="icons <?= (!is_null($group->memberUserId)) ? 'i-joingroup' : 'i-joingroup' ?>"><span></span></span><?= $join_btn_text ?></a>
				<? endif; ?>
			</div>
		</div>
<? if($isLinkProfile) :
	?></a><?
else :
	?></div><?
endif;