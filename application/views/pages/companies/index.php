<?// dump($company, 1); ?>
<?// dump($peopleAlsoViewed, 1); ?>
<?// dump($f_Updates_AddUpdate, 1); ?>
<?// dump($timelinesCompany, 1); ?>
<?

if(!is_null($company->coverToken)) {
	$companyCover = Model_Files::generateUrl($company->coverToken, 'jpg', FILE_COMPANY_COVER, TRUE, false, 'cover_580');
	$middleView = '<img src="' . $companyCover . '" title="' . $company->name . '" alt="cover ' . $company->name . '" />';
} else {
	$middleView = false;
}
?>

<div class="company">
	<?= View::factory('parts/parts-left_big', array(
		'lefttop' => View::factory('pages/companies/block-company_head', array(
			'active' => 'home',
			'company' => $company
		)),
		'leftmiddle' => $middleView,
		'left' => View::factory('pages/companies/block-updates_and_summary', array(
			'company' => $company,
			'f_Updates_AddUpdate' => $f_Updates_AddUpdate,
			'timelinesCompany' => $timelinesCompany
		)),
		'right' => View::factory('pages/companies/rightpanel', array(
			'peopleAlsoViewed' => $peopleAlsoViewed
		))
	)) ?>
</div>