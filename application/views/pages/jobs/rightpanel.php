<div class="block-jobs_right_panel">
<!--	--><?// if(isset($myCompanies) && !empty($myCompanies['data'])) : ?>
<!--		--><?//= View::factory('pages/companies/block-manage_your_company', array(
//			'myCompanies' => $myCompanies
//		)) ?>
<!--	--><?// endif; ?>

	<? if(isset($isManageJobsApplication) && $isManageJobsApplication) : ?>
		<?= View::factory('pages/jobs/block-manage_jobs_applications', array(

		)) ?>
	<? endif; ?>

<!--	--><?// if(isset($youRecentlyVisit) && !empty($youRecentlyVisit['data'])) : ?>
<!--		--><?//= View::factory('pages/companies/block-your_recently_visit', array(
//			'youRecentlyVisit' => $youRecentlyVisit
//		)) ?>
<!--	--><?// endif; ?>
<!---->
<!--	--><?// if(isset($peopleAlsoViewed) && !empty($peopleAlsoViewed['data'])) : ?>
<!--		--><?//= View::factory('pages/companies/block-people_also_viewed', array(
//			'peopleAlsoViewed' => $peopleAlsoViewed
//		)) ?>
<!--	--><?// endif; ?>
</div>