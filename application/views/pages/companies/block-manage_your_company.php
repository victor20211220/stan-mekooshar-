<?// dump($myCompanies, 1); ?>

<div class="block-manage_your_company">
	<div class="content-title">
<!--		<div class="content-title-icon"><div><div></div></div></div>-->
		<div>Manage your company</div>
	</div>

	<ul>
		<? foreach($myCompanies['data'] as $company) : ?>
			<li>
				<?= View::factory('parts/companiesava-more', array(
					'company' => $company,
					'isManageButton' => TRUE,
					'isCompanyDescripion' => TRUE
				)) ?>
			</li>
		<? endforeach; ?>
	</ul>

</div>

