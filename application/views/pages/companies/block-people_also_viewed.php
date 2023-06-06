<?// dump($peopleAlsoViewed, 1); ?>

<div class="block-people_also_viewed_company">
	<div class="content-title">
<!--		<div class="content-title-icon"><div><div></div></div></div>-->
		<div>People also viewed</div>
	</div>
	<ul class="list-items">
		<? foreach($peopleAlsoViewed['data'] as $company) : ?>
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

