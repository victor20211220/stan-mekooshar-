<?// dump($memberAdmin, 1); ?>
<?// dump($group, 1); ?>
<?// dump($counter, 1); ?>
<?// dump($countUnchecked, 1); ?>


<div class="groupchangeowner">
	<?= View::factory('parts/parts-left_big', array(
		'lefttop' => View::factory('pages/groups/block-group_head', array(
			'active' => 'manage',
			'group' => $group,
			'counter' => $counter,
			'countUnchecked' => $countUnchecked
		)),
		'leftmiddle' => '',
		'left' => View::factory('pages/groups/block-group_change_owner', array(
			'memberAdmin' => $memberAdmin,
			'group' => $group
		)),
		'right' => View::factory('pages/groups/menu', array(
			'active' => 'change_owner',
			'group' => $group,
			'counter' => $counter,
			'countUnchecked' => $countUnchecked
		))
	)) ?>
</div>