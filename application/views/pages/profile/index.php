<?
	$middleBanner = '';
	$rightBanner = '';
	$rightBannerBottom = '';
	foreach($banners as $banner_id => $banner) {
		if($banner->bannerType == 1) {
			if($banner->webUrl) {
				$middleBanner .= '<a href="' . $banner->webUrl .  '" target="_blank"><img src="' . $banner->url_580 . '"  /></a>';
			} else {
				$middleBanner .= '<img src="' . $banner->url_580 . '"  />';
			}
		} elseif($banner->bannerType == 2) {
			if($banner->webUrl) {
				//$rightBanner .= '<a href="' . $banner->webUrl .  '" target="_blank"><img src="' . $banner->url_330 . '"  /></a>';
			} else {
				//$rightBanner .= '<img src="' . $banner->url_330 . '" />';
			}
		} elseif($banner->bannerType == 3) {
			if($banner->webUrl) {
				//$rightBannerBottom .= '<a href="' . $banner->webUrl .  '" target="_blank"><img src="' . $banner->url_330 . '"  /></a>';
			} else {
				//$rightBannerBottom .= '<img src="' . $banner->url_330 . '" />';
			}
		}

	}
?>

<? if(empty($items_experience['data']) && empty($items_languages['data']) && empty($items_skills['data']) &&
	empty($items_educations['data']) && empty($items_projects['data']) && empty($items_testscores['data']) &&
	empty($items_certifications['data']) && empty($items_connections['data']) && empty($profile->summaryText) &&
	empty($profile->birthdayDate) && empty($profile->maritalStatus) && empty($profile->interests) &&
	empty($follow_companies['data']) && empty($follow_groups['data'])) {

		$left = false;
} else {
	$left =  View::factory('pages/profile/view/profile-info', array(
		'isConnections' => $isConnections,
		'profile' => $profile,
		'f_findInProfile' => $f_findInProfile,
		'items_experience' => $items_experience,
		'items_languages' => $items_languages,
		'items_skills' => $items_skills,
		'items_educations' => $items_educations,
		'items_projects' => $items_projects,
		'items_testscores' => $items_testscores,
		'items_certifications' => $items_certifications,
		'items_connections' => $items_connections,
		'follow_companies' => $follow_companies,
		'follow_groups' => $follow_groups,
		'skill_endorsement' => $skill_endorsement));
}?>


<div class="userprofile">
	<?= View::factory('parts/parts-left_big', array(
		'lefttop' => View::factory('pages/profile/view/user-info', array(
				'profile' => $profile,
				'isConnections' => $isConnections,

				'userinfo_experience' => $userinfo_experience,
				'userinfo_education' => $userinfo_education,
				'items_connections' => $items_connections
			)),
		'leftmiddle' => '',
		'left' => $left,
		'right' => View::factory('pages/profile/view/rightpanel-userstatistic', array(
				'countInSearch' => $countInSearch,
				'countVisits' => $countVisits,
				'connectionsMayKnow' => $connectionsMayKnow,
				'connectionsAlsoViewed' => $connectionsAlsoViewed,
				'rightBanner' => $rightBanner,
				'rightBannerBottom' => $rightBannerBottom
			))
	)) ?>

	<? if($profile->id != $user->id && ((isset($profile->isComplaint) && empty($profile->isComplaint)) || (!isset($profile->isComplaint)))) : ?>
		<a class="userinfo-complain" href="<?= Request::generateUri('profile', 'complaint', $profile->id) ?>" onclick="return box.load(this);">Complain on the user</a>
	<? endif; ?>
</div>