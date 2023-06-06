<div class="userprofile">
	<?= View::factory('parts/parts-left_big', array(
		'lefttop' => View::factory('pages/profile/edit/user-info', array(
				'profile' => $profile,

				'userinfo_experience' => $userinfo_experience,
				'userinfo_education' => $userinfo_education,
				'items_connections' => $items_connections
			)),
		'left' => View::factory('pages/profile/edit/profile-info', array(
				'items_experience' => $items_experience,
				'items_languages' => $items_languages,
				'items_skills' => $items_skills,
				'items_educations' => $items_educations,
				'items_projects' => $items_projects,
				'items_testscores' => $items_testscores,
				'items_certifications' => $items_certifications,
				'skill_endorsement' => $skill_endorsement,
				'isConnections' => $isConnections,
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