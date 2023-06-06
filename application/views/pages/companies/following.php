<?// dump($myCompanies, 1); ?>
<?// dump($myFollowing, 1); ?>
<?// dump($youRecentlyVisit, 1); ?>

<div class="companies">
	<?= View::factory('parts/parts-left_big', array(
		'lefttop' => View::factory('pages/companies/block-company_starthead', array(
				'active' => 'following'
		)),
		'left' => View::factory('pages/companies/block-companies_following', array(
			'myFollowing' => $myFollowing
		)),
		'right' => View::factory('pages/companies/rightpanel', array(
			'isCreateCompany' => TRUE,
			'myCompanies' => $myCompanies,
			'youRecentlyVisit' => $youRecentlyVisit
		))
	)) ?>
</div>