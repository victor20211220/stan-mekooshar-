<?// dump($items_experience, 1); ?>
<?// dump($items_languages, 1); ?>
<?// dump($items_skills, 1); ?>
<?// dump($items_educations, 1); ?>
<?// dump($items_projects, 1); ?>
<?// dump($items_testscores, 1); ?>
<?// dump($items_certifications, 1); ?>
<?// dump($items_connections, 1); ?>
<?// dump($follow_companies, 1); ?>
<?// dump($profile, 1); ?>
<?// dump($f_findInProfile, 1); ?>
<?// dump($follow_groups, 1); ?>
<?
$isConnected = false;
if($isConnections) {
	$isInvited = false;
	foreach($isConnections['data'] as $isConnection){
		if($isConnection->typeApproved == 1) {
			$isConnected = true;
			break;
		}
		if($isConnection->typeApproved == 0) {
			$isInvited = true;
			break;
		}
	}
}
$isRequest = Model_Connections::isSendRequestByConnection($profile->id, $user->id);
$levelConnection = Model_User::getLevelWithUser($profile->id);

$isUserBlockMe = FALSE;
if($profile->id != $user->id) {
	$isUserBlockMe = Model_User::checkIsUserBlockMe($profile->id);
}

?>

<? if (in_array($levelConnection, array(1, 2, 3)) || $user->accountType == ACCOUNT_TYPE_GOLD || $profile->id == $user->id || !empty($profile->summaryText)) : ?>
<div class="block-profile-info">

	<? if(!$isUserBlockMe) : ?>

			<? if($user->id == $profile->id || $profile->setInvisibleProfile == 0 ||
				($profile->setInvisibleProfile == USER_PROFILE_INVISIBLE && $isConnected) ||
				($profile->setInvisibleProfile == USER_PROFILE_INVISIBLE && $isRequest)) : ?>
				<? if(!empty($profile->summaryText)) : ?>
					<?= View::factory('pages/profile/edit/block-summary', array(
						'profile' => $profile,
						'isEdit' => false
					)) ?>
				<? endif; ?>



			<? if(!empty($items_experience['data'])) : ?>
						<div class="line"></div>
						<?= View::factory('pages/profile/edit/block-experience', array(
							'items_experience' => $items_experience,
							'isEdit' => false
						)) ?>
					<? endif; ?>


					<? if(!empty($items_languages['data'])) : ?>
						<div class="line"></div>
						<?= View::factory('pages/profile/edit/block-languages', array(
							'items_languages' => $items_languages,
							'isEdit' => false
						)) ?>
					<? endif; ?>


					<? if(!empty($items_skills['data'])) : ?>
						<div class="line"></div>
						<?= View::factory('pages/profile/edit/block-skills', array(
							'items_skills' => $items_skills,
							'skill_endorsement' => $skill_endorsement,
							'isConnected' => $isConnected,
							'isEdit' => false
						)) ?>
					<? endif; ?>


					<? if(!empty($items_educations['data'])) : ?>
						<div class="line"></div>
						<?= View::factory('pages/profile/edit/block-educations', array(
							'items_educations' => $items_educations,
							'isEdit' => false
						)) ?>
					<? endif; ?>


					<? if(!empty($user->birthdayDate) || !empty($user->maritalStatus) || !empty($user->interests)) : ?>
						<div class="line"></div>
						<?= View::factory('pages/profile/edit/block-addition_information', array(
							'isEdit' => false,
							'profile' => $profile
						)) ?>
					<? endif; ?>


					<? if(!empty($items_projects['data'])) : ?>
						<div class="line"></div>
						<?= View::factory('pages/profile/edit/block-projects', array(
							'items_projects' => $items_projects,
							'isEdit' => false
						)) ?>
					<? endif; ?>


					<? if(!empty($items_testscores['data'])) : ?>
						<div class="line"></div>
						<?= View::factory('pages/profile/edit/block-testscore', array(
							'items_testscores' => $items_testscores,
							'isEdit' => false
						)) ?>
					<? endif; ?>


					<? if(!empty($items_certifications['data'])) : ?>
						<div class="line"></div>
						<?= View::factory('pages/profile/edit/block-certifications', array(
							'items_certifications' => $items_certifications,
							'isEdit' => false
						)) ?>
					<? endif; ?>


					<? if(!empty($items_connections['data'])) : ?>
						<?= View::factory('pages/profile/view/block-allconnections', array(
							'f_findInProfile' => $f_findInProfile,
							'items_connections' => $items_connections,
							'profile' => $profile
						)) ?>
					<? endif; ?>


					<? if(!empty($follow_companies['data']) || !empty($follow_groups['data'])) : ?>
						<?= View::factory('pages/profile/view/block-groups-and-following', array(
							'follow_companies' => $follow_companies,
							'follow_groups' => $follow_groups,
							'profile' => $profile
						)) ?>
					<? endif; ?>

		<? else: ?>
				<div class="list-item-empty">
					Info is private
				</div>
			<? endif; ?>
	<? else: ?>
		<div class="list-item-empty">
			You can not view this profile. This user block you.
		</div>
	<? endif; ?>
<? endif; ?>
</div>