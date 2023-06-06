<?// dump($company, 1); ?>
<?// dump($followers, 1); ?>
<?// dump($peopleAlsoViewed, 1); ?>

<div class="company">
	<?= View::factory('parts/parts-left_big', array(
		'lefttop' => View::factory('pages/companies/block-company_head', array(
			'active' => 'followers',
			'company' => $company
		)),
		'left' => View::factory('pages/companies/block-followers', array(
			'followers' => $followers
		)),
		'right' => View::factory('pages/companies/rightpanel', array(
			'peopleAlsoViewed' => $peopleAlsoViewed
		))
	)) ?>
</div>