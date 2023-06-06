<?// dump($memberRequests, 1); ?>
<?// dump($group, 1); ?>
<?// dump($counter, 1); ?>
<?// dump($countUnchecked, 1); ?>


<div class="groupmemberadmin">
	<?= View::factory('parts/parts-left_big', array(
		'lefttop' => View::factory('pages/groups/block-group_head', array(
			'active' => 'manage',
			'group' => $group,
			'counter' => $counter,
			'countUnchecked' => $countUnchecked
		)),
		'leftmiddle' => '',
		'left' => View::factory('pages/groups/block-group_member_request', array(
			'memberRequests' => $memberRequests,
			'group' => $group
		)),
		'right' => View::factory('pages/groups/menu', array(
			'active' => 'members_requests',
			'group' => $group,
			'counter' => $counter,
			'countUnchecked' => $countUnchecked
		))
	)) ?>
</div>