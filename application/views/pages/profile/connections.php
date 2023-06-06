<div class="userprofile">
	<?= View::factory('parts/parts-left_big', array(
		'lefttop' => View::factory('pages/profile/view/user-info', array(
				'profile' => $profile,
				'isConnections' => $isConnections,
				'userinfo_experience' => $userinfo_experience,
				'userinfo_education' => $userinfo_education,
				'items_connections' => $items_connections,
				'isCountConnections' => FALSE,
				'isBtnContactInfo' => FALSE,
				'isNameLink' => TRUE
			)),
		'left' => View::factory('pages/profile/view/block-profile_connections', array(
			'items_connections' => $items_connections,
			'profile' => $profile
		)),
		'right' => View::factory('pages/profile/view/rightpanel-userstatistic', array(
			'countInSearch' => $countInSearch,
			'countVisits' => $countVisits,
			'connectionsMayKnow' => $connectionsMayKnow,
			'connectionsAlsoViewed' => $connectionsAlsoViewed
		))
	)) ?>
</div>