<div class="userprofile">
	<?= View::factory('parts/parts-left_big', array(
		'lefttop' => View::factory('pages/profile/view/user-info', array(
				'profile' => $profile,
				'isConnections' => $isConnections,

				'userinfo_experience' => $userinfo_experience,
				'userinfo_education' => $userinfo_education,
				'items_connections' => $items_connections
			)),
		'left' => View::factory('pages/profile/statistic/list-profile-statistic', array(
				'connectionWhoVisitMyProfile' => $connectionWhoVisitMyProfile
			)),
		'right' => View::factory('pages/profile/view/rightpanel-userstatistic', array(
				'statisticByMonth' => $statisticByMonth,
				'statisticByWeek' => $statisticByWeek,
				'statisticByDay' => $statisticByDay,
				'f_Profile_ChangeGpaphStatistic' => $f_Profile_ChangeGpaphStatistic
			))
	)) ?>
</div>