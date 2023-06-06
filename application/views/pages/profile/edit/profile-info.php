<?// dump($f_findInProfile, 1); ?>
<?
	if($isConnections) {
		$isConnected = false;
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
?>

<div class="block-profile-info">
	<?= View::factory('pages/profile/edit/block-summary', array(
		'isEdit' => true,
		'profile' => $profile
	)) ?>
	<div class="line"></div>
	<?= View::factory('pages/profile/edit/block-experience', array(
		'items_experience' => $items_experience,
		'isEdit' => true
	)) ?>
	<div class="line"></div>
	<?= View::factory('pages/profile/edit/block-languages', array(
		'items_languages' => $items_languages,
		'isEdit' => true
	)) ?>
	<div class="line"></div>
	<?= View::factory('pages/profile/edit/block-skills', array(
		'items_skills' => $items_skills,
		'skill_endorsement' => $skill_endorsement,
		'isConnections' => FALSE,
		'isEdit' => true
	)) ?>
	<div class="line"></div>
	<?= View::factory('pages/profile/edit/block-educations', array(
		'items_educations' => $items_educations,
		'isEdit' => true
	)) ?>
	<div class="line"></div>
	<?= View::factory('pages/profile/edit/block-addition_information', array(
		'isEdit' => true,
		'profile' => $profile
	)) ?>
	<div class="line"></div>
	<?= View::factory('pages/profile/edit/block-projects', array(
		'items_projects' => $items_projects,
		'isEdit' => true
	)) ?>
	<div class="line"></div>
	<?= View::factory('pages/profile/edit/block-testscore', array(
		'items_testscores' => $items_testscores,
		'isEdit' => true
	)) ?>
	<div class="line"></div>
	<?= View::factory('pages/profile/edit/block-certifications', array(
		'items_certifications' => $items_certifications,
		'isEdit' => true
	)) ?>
</div>