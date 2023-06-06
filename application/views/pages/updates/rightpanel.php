<?// dump($countInSearch, 1); ?>
<?// dump($countVisits, 1); ?>
<?// dump($connectionsMayKnow, 1); ?>
<?// dump($myVisits, 1); ?>
<?// dump($jobsYouMayLike, 1); ?>
<?// dump($groupsYouMayLike, 1); ?>

<div class="block-rightpanel-updates">
	<? if(isset($countInSearch) || isset($countVisits)) : ?>
		<?= View::factory('pages/profile/view/block-who-view-profile', array(
			'countInSearch' => $countInSearch,
			'countVisits' => $countVisits
		)) ?>
	<? endif; ?>

	<? if(isset($connectionsMayKnow) && !empty($connectionsMayKnow['data'])) : ?>
		<?= View::factory('pages/profile/view/block-people-you-may-know', array(
			'connectionsMayKnow' => $connectionsMayKnow
		)) ?>
	<? endif; ?>


	<? if(isset($connectionsAlsoViewed) && !empty($connectionsAlsoViewed['data'])) : ?>
		<?= View::factory('pages/profile/view/block-people-also-view', array(
			'connectionsAlsoViewed' => $connectionsAlsoViewed
		)) ?>
	<? endif; ?>


	<? if(isset($myVisits)) : ?>
		<?= View::factory('pages/updates/block-you-recently-visited', array(
			'myVisits' => $myVisits
		)) ?>
	<? endif; ?>

	<?= View::factory('pages/updates/block-my_network') ?>


	<? if(isset($jobsYouMayLike) && !empty($jobsYouMayLike['data'])) : ?>
		<?= View::factory('pages/updates/block-jobs_you_may_like', array(
			'jobsYouMayLike' => $jobsYouMayLike
		)) ?>
	<? endif; ?>

	<? if(isset($groupsYouMayLike) && !empty($groupsYouMayLike['data'])) : ?>
		<?= View::factory('pages/updates/block-groups_you_may_like', array(
			'groupsYouMayLike' => $groupsYouMayLike
		)) ?>
	<? endif; ?>
</div>