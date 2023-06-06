<?// dump($compareCompanies, 1) ?>

<? if(!empty($compareCompanies['data'])) : ?>
	<div class="block-company_analytics_compare">
		<div class="title-big">How you Compare</div>

		<ul>
			<? foreach($compareCompanies['data'] as $company) : ?><li>
					<?= View::factory('parts/companiesava-more', array(
						'company' => $company,
						'avasize' => 'avasize_52',
						'isCompanyIndustry' => TRUE,
						'isCompanyFollowers' => TRUE,
						'isLinkProfile' => TRUE
					)) ?>
				</li><? endforeach; ?>
		</ul>
	</div>
<? endif; ?>