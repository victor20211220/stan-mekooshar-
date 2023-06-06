<?// dump($group, 1); ?>
<?// dump($groupMembers, 1); ?>
<?// dump($peopleAlsoViewed, 1); ?>
<?// dump($counter, 1); ?>
<?// dump($f_Updates_AddUpdate, 1); ?>
<?// dump($timelinesGroup, 1); ?>
<?// dump($counter, 1); ?>
<?// dump($countUnchecked, 1); ?>
<?

if(!is_null($group->coverToken)) {
	$groupCover = Model_Files::generateUrl($group->coverToken, 'jpg', FILE_GROUP_COVER, TRUE, false, 'cover_580');
	$middleView = '<img src="' . $groupCover . '" title="' . $group->name . '" alt="cover ' . $group->name . '" width="580px" />';
} else {
	$middleView = false;
}
?>

<div class="group">
	<?= View::factory('parts/parts-left_big', array(
		'lefttop' => View::factory('pages/groups/block-group_head', array(
			'active' => 'discussions',
			'group' => $group,
			'counter' => $counter,
			'countUnchecked' => $countUnchecked,
		)),
		'leftmiddle' => $middleView,
		'left' => View::factory('pages/groups/block-discussions', array(
				'f_Updates_AddUpdate' => $f_Updates_AddUpdate,
				'timelinesGroup' => $timelinesGroup,
				'group' => $group
			)),
		'right' => View::factory('pages/groups/rightpanel', array(
			'groupMembers' => $groupMembers,
			'peopleAlsoViewed' => $peopleAlsoViewed
		))
	)) ?>
</div>