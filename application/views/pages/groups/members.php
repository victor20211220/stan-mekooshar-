<?// dump($group, 1); ?>
<?// dump($counter, 1); ?>
<?// dump($countUnchecked, 1); ?>
<?// dump($f_Groups_FindMemberInGroup, 1); ?>
<?// dump($groupMembers, 1); ?>

<div class="groupmembers">
	<?= View::factory('parts/parts-left_big', array(
		'lefttop' => View::factory('pages/groups/block-group_head', array(
			'active' => 'members',
			'group' => $group,
			'counter' => $counter,
			'countUnchecked' => $countUnchecked,
		)),
		'leftmiddle' => '',
		'left' => View::factory('pages/groups/block-members', array(
			'f_Groups_FindMemberInGroup' => $f_Groups_FindMemberInGroup,
			'groupMembers' => $groupMembers
		)),
		'right' => ''
	)) ?>
</div>