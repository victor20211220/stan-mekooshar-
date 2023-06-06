<?// dump($youRecentlyVisit, 1); ?>

<div class="block-your_recently_visit_company">
	<div class="content-title">
<!--		<div class="content-title-icon"><div><div></div></div></div>-->
		<div>Your recently visit</div>
	</div>
	<ul class="list-items">
		<? foreach($youRecentlyVisit['data'] as $company) : ?>
			<li data-id="company_<?= $company->id ?>">
				<?= View::factory('parts/companiesava-more', array(
					'company' => $company,
					'avasize' => 'avasize_52',
					'isCompanyNameLink' => TRUE,
					'isCompanyIndustry' => TRUE,
					'isFollowButton' => TRUE
				)) ?>
			</li>
		<? endforeach ?>
	</ul>

</div>

