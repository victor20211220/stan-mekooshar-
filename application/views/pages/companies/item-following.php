<?// dump($company, 1); ?>
<?
if(is_null($company->avaToken)) {
	$companyAva = '/resources/images/noimage_100.jpg';
} else {
	$companyAva = Model_Files::generateUrl($company->avaToken, 'jpg', FILE_COMPANY_AVA, TRUE, false, 'userava_100');
}

$isMy = FALSE;
if($company->user_id == $user->id) {
	$isMy = TRUE;
}
?>

<div class="userava">
	<div><a href="<?= Request::generateUri('companies', $company->id) ?>" class="userava-user_name_link"><b><?= $company->name ?></b></a></div>
	<div><a href="<?= Request::generateUri('companies', $company->id) ?>" class="userava-user_name_link"><img src="<?= $companyAva ?>" title="" /></a></div>
	<? if(!$isMy) : ?>
		<div><a href="<?= Request::generateUri('companies', 'followFromBlock', $company->id) ?>" class="blue-btn" onclick="return web.ajaxGet(this);"><span class="icons <?= (!is_null($company->followUserId)) ? 'i-closewhite' : 'i-accesswhite' ?>"><span></span></span><?= (!is_null($company->followUserId)) ? 'Unfollow' : 'Follow' ?></a></div>
	<? endif ?>
</div>