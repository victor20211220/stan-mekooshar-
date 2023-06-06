<?// dump($myCompanies, 1); ?>
<?// dump($youRecentlyVisit, 1); ?>
<?// dump($timelinesCompanies, 1); ?>

<div class="companies_updates">
	<?= View::factory('parts/parts-left_big', array(
		'lefttop' => View::factory('pages/companies/block-company_starthead', array(
				'active' => 'updates'
		)),
		'left' => View::factory('pages/companies/block-companies_updates', array(
			'timelinesCompanies' => $timelinesCompanies
		)),
		'right' => View::factory('pages/companies/rightpanel', array(
			'isCreateCompany' => TRUE,
			'myCompanies' => $myCompanies,
			'youRecentlyVisit' => $youRecentlyVisit
		))
	)) ?>
</div>