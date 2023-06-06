<?
// dump($company, 1);
// dump($avasize, 1);
// dump($isCompanyNameLink, 1);

// dump($isLinkProfile, 1);
// dump($isTooltip, 1);
// dump($isManageButton, 1);
// dump($isFollowButton, 1);
// dump($isCompanyDescripion, 1);
// dump($isCompanyIndustry, 1);
// dump($isCompanyType, 1);
// dump($isCompanySize, 1);
// dump($isCompanyFollowers, 1);
// dump($keyId, 1);
// dump($otherInfo, 1);
// dump($maxSizeIndustry, 1);
// dump($maxSizeOtherInfo, 1);
// dump($isLinkJob, 1);





// -----------------------
// dump($isComments, 1);
// dump($comment, 1);

// dump($isUpdate, 1);
// dump($update, 1);


$company_name = $company->name;
//$industries = t('industries');
//if(!empty($company->mainIndustry) && isset($industries[$company->mainIndustry])) {
//	$industry = $industries[$company->mainIndustry];
//}
//$description = $company->description;
//if(strlen($description) > 50)  {
//	$description = substr($description, 0, 50) . '...';
//}

if(!isset($isLinkJob)) {
	$isLinkJob = FALSE;
}
if(!isset($maxSizeIndustry)) {
	$maxSizeIndustry = FALSE;
}
if(!isset($maxSizeOtherInfo)) {
	$maxSizeOtherInfo = FALSE;
}
if(!isset($otherInfo)) {
	$otherInfo = FALSE;
}
if(!isset($keyId)) {
	$keyId = 'id';
}
if(!isset($isCompanyNameLink)) {
	$isCompanyNameLink = FALSE;
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
if(!isset($isCompanyDescripion)) {
	$isCompanyDescripion = FALSE;
}
if(!isset($isCompanyIndustry)) {
	$isCompanyIndustry = FALSE;
}
if(!isset($isCompanyType)) {
	$isCompanyType = FALSE;
}
if(!isset($isCompanySize)) {
	$isCompanySize = FALSE;
}
if(!isset($isCompanyFollowers)) {
	$isCompanyFollowers = FALSE;
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

if(is_null($company->avaToken)) {
	$companyAva = '/resources/images/' . $nosize;
} else {
	$companyAva = Model_Files::generateUrl($company->avaToken, 'jpg', FILE_COMPANY_AVA, TRUE, false, $size);
}



if($isLinkProfile) :
	?><a href="<?= Request::generateUri('companies', $company->$keyId) ?>" class="userava <?= $avasize ?> <?= ($isTooltip === TRUE) ? 'is-tooltip' : '' ?>" title="View company" data-id="<?= $company->$keyId ?>">
<? elseif($isLinkJob) :
	?><a href="<?= Request::generateUri('jobs', 'job', $company->$keyId) ?>" class="userava <?= $avasize ?> <?= ($isTooltip === TRUE) ? 'is-tooltip' : '' ?>" title="View job" data-id="<?= $company->$keyId ?>">
<? else :
	?><div class="userava <?= $avasize ?> <?= ($isFollowButton) ? 'userava-follow' : null ?>" >
<? endif; ?>

		<? if($isCompanyNameLink) : ?>
			<a href="<?= Request::generateUri('companies', $company->$keyId) ?>" class="userava-user_name_link"><img src="<?= $companyAva ?>" title="" /></a><div class="userava-info">
		<? else : ?>
			<img src="<?= $companyAva ?>" title="" /><div class="userava-info">
		<? endif; ?>


			<div>
				<? if($isCompanyNameLink) : ?>
					<a href="<?= Request::generateUri('companies', $company->$keyId) ?>" class="userava-user_name_link"><b><?= $company_name ?></b></a><br>
				<? else : ?>
					<b class="userava-name"><?= $company_name ?></b><br>
				<? endif; ?>

				<? if($isCompanyDescripion) :
					$description = $company->description;
					if(strlen($description) > 50) :
						$description = substr($description, 0, 50) . '...';
					endif
					?>
					<span><?= $description ?></span><br>
				<? endif ?>

				<? if($isCompanyIndustry) : ?>
					<? $industries = t('industries'); ?>
					<? if(!empty($company->industry) && isset($industries[$company->industry])) : ?>
						<?
						$industryText = $industries[$company->industry];
						if($maxSizeIndustry && (strlen($industryText) > $maxSizeIndustry)) {
							$industryText = substr($industryText, 0, $maxSizeIndustry) . '...';
						}
						?>
						<?= $industryText ?><br>
					<? endif; ?>
				<? endif; ?>

				<? if($isCompanyType) : ?>
					<? $types = t('company_type'); ?>
					<? if(!empty($company->type) && isset($types[$company->type])) : ?>
						<?= $types[$company->type] ?><br>
					<? endif; ?>
				<? endif; ?>

				<? if($isCompanySize) : ?>
					<? $sizes = t('company_number_of_employer'); ?>
					<? if(!empty($company->size) && isset($sizes[$company->size])) : ?>
						<?= $sizes[$company->size] ?> employers<br>
					<? endif; ?>
				<? endif; ?>

				<? if($otherInfo) : ?>
					<?
					if($maxSizeOtherInfo && (strlen($otherInfo) > $maxSizeOtherInfo)) {
						$otherInfo = substr($otherInfo, 0, $maxSizeOtherInfo) . '...';
					}
					?>
					<?= $otherInfo; ?>
				<? endif; ?>

				<? if($isCompanyFollowers) : ?>
					<?= $company->followers ?> follower<?= ($company->followers > 1) ? 's' : null ?><br>
				<? endif; ?>

				<? if($isManageButton) : ?>
					<a class="btn-roundblue-border icons i-editcustom" href="<?= Request::generateUri('companies', $company->$keyId) ?>" class="btn-grey"><span></span>Manage</a>
				<? endif; ?>

				<? if($isFollowButton && $company->user_id != $user->id) : ?>
					<a href="<?= Request::generateUri('companies', 'followFromList', $company->$keyId) ?>" class="blue-btn" onclick="return web.ajaxGet(this);"><span class="icons <?= (!is_null($company->followUserId)) ? 'i-closewhite' : 'i-accesswhite' ?>"><span></span></span><?= (!is_null($company->followUserId)) ? 'Unfollow' : 'Follow' ?></a>
				<? endif; ?>
			</div>
		</div>
<? if($isLinkProfile) :
	?></a><?
elseif($isLinkJob) :
	?></a><?
else :
	?></div><?
endif;