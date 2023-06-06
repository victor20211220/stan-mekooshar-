
<div class="block-connections-new">
	<?= View::factory('parts/parts-left_big', array(
		'lefttop' => View::factory('pages/connections/block-invite', array(
				'followers' => $followers,
                'inviter' => $inviter,
			)),
		'left' => '',
		'right' => View::factory('pages/profile/view/rightpanel-userstatistic', array(
				'countInSearch' => $countInSearch,
				'countVisits' => $countVisits,
				'connectionsMayKnow' => $connectionsMayKnow,
				'connectionsAlsoViewed' => $connectionsAlsoViewed
			))
	)) ?>
</div>