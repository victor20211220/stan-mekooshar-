<?// dump($contents, 1); ?>
<?// dump($group, 1); ?>
<?// dump($counter, 1); ?>
<?// dump($countUnchecked, 1); ?>


<div class="groupcheckcontent">
	<?= View::factory('parts/parts-left_big', array(
		'lefttop' => View::factory('pages/groups/block-group_head', array(
			'active' => 'manage',
			'group' => $group,
			'counter' => $counter,
			'countUnchecked' => $countUnchecked
		)),
		'leftmiddle' => '',
		'left' => View::factory('pages/groups/block-group_check_content', array(
			'contents' => $contents,
			'group' => $group
		)),
		'right' => View::factory('pages/groups/menu', array(
			'active' => 'check_content',
			'group' => $group,
			'counter' => $counter,
			'countUnchecked' => $countUnchecked
		))
	)) ?>
</div>