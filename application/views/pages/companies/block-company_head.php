<?// dump($active, 1); ?>
<?// dump($company, 1); ?>
<?

$isMy = FALSE;
if($company->user_id == $user->id) {
	$isMy = TRUE;
}

if(is_null($company->avaToken)) {
	$companyAva = '/resources/images/noimage_100.jpg';
} else {
	$companyAva = Model_Files::generateUrl($company->avaToken, 'jpg', FILE_COMPANY_AVA, TRUE, false, 'userava_100');
}

?>

<div class="block-company_head">
	<div class="company_head-left">
		<img src="<?= $companyAva ?>" title="<?= $company->name ?>" alt="ava <?= $company->name ?>" />
	</div>
	<div class="company_head-right">
		<div class="title-big"><?= $company->name ?></div>
		<? if($isMy) : ?>
			<a href="<?= Request::generateUri('companies', $company->id) ?>" class="blue-btn <?= ($active == 'home') ? 'active' : null ?>"><span class="icons i-companyhome"><span></span></span>Home</a>
			<a href="<?= Request::generateUri('companies', 'analytics', $company->id) ?>" class="blue-btn <?= ($active == 'analytics') ? 'active' : null ?>"><span class="icons i-companyanalytics"><span></span></span>Analytics</a>
			<a href="<?= Request::generateUri('companies', 'edit', $company->id) ?>" class="btn-roundblue-border icons i-editcustom company_head-edit"><span></span>Edit company</a>
			<a href="<?= Request::generateUri('companies', 'followers', $company->id) ?>" class="company_head-followers"><?= (!is_null($company->followers)) ? $company->followers : '0' ?> follower<?= ($company->followers > 1) ? 's' : null ?></a>
		<?	else:	?>
			<a href="<?= Request::generateUri('companies', 'follow', $company->id) ?>" class="blue-btn company_head-follow"><span class="icons i-companyfollowing"><span></span></span><?= (!is_null($company->followUserId)) ? 'Unfollow' : 'Follow' ?></a>
			<? if(!is_null($company->followUserId)) : ?>
				<a href="<?= Request::generateUri('companies', 'followers', $company->id) ?>" class="company_head-followers company_head-follow"><?= (!is_null($company->followers)) ? $company->followers : '0' ?> follower<?= ($company->followers > 1) ? 's' : null ?></a>
			<? else : ?>
				<div class="company_head-followers company_head-follow"><?= (!is_null($company->followers)) ? $company->followers : '0' ?> follower<?= ($company->followers > 1) ? 's' : null ?></div>
			<? endif ?>
		<? endif ?>
	</div>
</div>