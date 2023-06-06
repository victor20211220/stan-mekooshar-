<?// dump($f_Groups_EditGroup, 1); ?>
<?// dump($group, 1); ?>
<?// dump($counter, 1); ?>
<?// dump($countUnchecked, 1); ?>


<div class="groupsetting">
	<?= View::factory('parts/parts-left_big', array(
		'lefttop' => View::factory('pages/groups/block-group_head', array(
			'active' => 'manage',
			'group' => $group,
			'counter' => $counter,
			'countUnchecked' => $countUnchecked
		)),
		'leftmiddle' => '',
		'left' => View::factory('pages/groups/block-group_settings', array(
			'f_Groups_EditGroup' => $f_Groups_EditGroup
		)),
		'right' => View::factory('pages/groups/menu', array(
			'active' => 'settings',
			'group' => $group,
			'counter' => $counter,
			'countUnchecked' => $countUnchecked
		))
	)) ?>
</div>