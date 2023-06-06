<?// dump($company, 1); ?>
<?// dump($companyUpdates, 1); ?>

<?// dump($impressions, 1); ?>
<?// dump($impressionsUnique, 1); ?>
<?// dump($clicks, 1); ?>
<?// dump($likes, 1); ?>
<?// dump($comments, 1); ?>
<?// dump($engagement, 1); ?>

<?// dump($compareCompanies, 1); ?>

<div class="company_analytics">
	<?= View::factory('parts/parts-left_big', array(
		'lefttop' => View::factory('pages/companies/block-company_head', array(
			'active' => 'analytics',
			'company' => $company
		)),
		'left' => View::factory('pages/companies/block-company_analytics', array(
			'companyUpdates' => $companyUpdates,
			'impressions' => $impressions,
			'impressionsUnique' => $impressionsUnique,
			'clicks' => $clicks,
			'likes' => $likes,
			'comments' => $comments,
			'engagement' => $engagement,
			'compareCompanies' => $compareCompanies
			)),
		'right' => false
	)) ?>
</div>