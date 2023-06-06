<?// dump($myCompanies, 1); ?>
<?// dump($isCreateCompany, 1); ?>

<?// dump($youRecentlyVisit, 1); ?>
<?// dump($peopleAlsoViewed, 1); ?>

<div class="block-companies_right_panel">
	<? if(isset($myCompanies) && !empty($myCompanies['data'])) : ?>
		<?= View::factory('pages/companies/block-manage_your_company', array(
			'myCompanies' => $myCompanies
		)) ?>
	<? endif; ?>

	<? if(isset($isCreateCompany) && $isCreateCompany) : ?>
		<?= View::factory('pages/companies/block-create_company_page', array(

		)) ?>
	<? endif; ?>

	<? if(isset($youRecentlyVisit) && !empty($youRecentlyVisit['data'])) : ?>
		<?= View::factory('pages/companies/block-your_recently_visit', array(
			'youRecentlyVisit' => $youRecentlyVisit
		)) ?>
	<? endif; ?>

	<? if(isset($peopleAlsoViewed) && !empty($peopleAlsoViewed['data'])) : ?>
		<?= View::factory('pages/companies/block-people_also_viewed', array(
			'peopleAlsoViewed' => $peopleAlsoViewed
		)) ?>
	<? endif; ?>
</div>