<?// dump($f_Updates_AddUpdate, 1); ?>
<?// dump($timelines, 1); ?>
<?// dump($countInSearch, 1); ?>
<?// dump($countVisits, 1); ?>
<?// dump($connectionsMayKnow, 1); ?>
<?// dump($myVisits, 1); ?>
<?// dump($jobsYouMayLike, 1); ?>
<?// dump($groupsYouMayLike, 1); ?>

<div class="updates">
	<?= View::factory('parts/parts-left_big', array(
		'lefttop' => View::factory('pages/updates/block-create-updates', array(
				'f_Updates_AddUpdate' => $f_Updates_AddUpdate
			)),
		'left' => View::factory('pages/updates/list-updates', array(
				'timelines' => $timelines
			)),
		'right' => View::factory('pages/updates/rightpanel', array(
				'countInSearch' => $countInSearch,
				'countVisits' => $countVisits,
				'connectionsMayKnow' => $connectionsMayKnow,
				'myVisits' => $myVisits,
				'jobsYouMayLike' => $jobsYouMayLike,
				'groupsYouMayLike' => $groupsYouMayLike
			))
	)) ?>
</div>