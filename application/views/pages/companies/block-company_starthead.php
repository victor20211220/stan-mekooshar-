<?// dump($active, 1); ?>

<div class="block-company_starthead">
	<div class="company_starthead-left">
		<img src="/resources/images/logo_companies.jpg" />
	</div>
	<div class="company_starthead-right">
		<div class="title-big">Companies</div>
		<a href="<?= Request::generateUri('companies', 'updates') ?>" class="blue-btn <?= ($active == 'updates') ? 'active' : null ?>"><span class="icons i-companyupdates"><span></span></span>Updates</a>
		<a href="<?= Request::generateUri('companies', 'following') ?>" class="blue-btn <?= ($active == 'following') ? 'active' : null ?>"><span class="icons i-companyfollowing"><span></span></span>Following</a>
	</div>
</div>