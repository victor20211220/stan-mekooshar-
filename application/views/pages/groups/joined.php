<?// dump($myGroups, 1); ?>
<?// dump($groups_joined, 1); ?>
<?// dump($groups_interested, 1); ?>

<div class="groupsjoined">
	<?= View::factory('parts/parts-left_big', array(
		'lefttop' => View::factory('pages/groups/block-groups_list', array(
				'groups_joined' => $groups_joined
			)),
		'leftmiddle' => '',
		'left' => '',
		'right' => View::factory('pages/groups/rightpanel', array(
			'isCreateGroups' => TRUE,
			'myGroups' => $myGroups,
			'groups_interested' => $groups_interested
		))
	)) ?>
</div>