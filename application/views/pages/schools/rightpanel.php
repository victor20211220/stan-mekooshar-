<?// dump($isCreateSchool, 1); ?>
<?// dump($schoolsFollowing, 1); ?>
<?// dump($schoolsInterest, 1); ?>
<?// dump($notableAlumni, 1); ?>
<?// dump($followSchools, 1); ?>
<?// dump($staffMember, 1); ?>
<?// dump($interestedSchool, 1); ?>
<?// dump($f_Schools_SelectTypeInSchool, 1); ?>

<?// dump($school, 1); ?>
<?// dump($profile_experiance, 1); ?>
<?// dump($profile_education, 1); ?>
<?
if(!isset($schoolsFollowing)) {
	$schoolsFollowing = FALSE;
}
if(!isset($notableAlumni)) {
	$notableAlumni = FALSE;
}
if(!isset($schoolsInterest)) {
	$schoolsInterest = FALSE;
}
if(!isset($schoolsManage)) {
	$schoolsManage = FALSE;
}
if(!isset($isSchoolsFollowing)) {
	$isSchoolsFollowing = FALSE;
}
if(!isset($isSchoolsInterest)) {
	$isSchoolsInterest = FALSE;
}
if(!isset($isNotableAlumni)) {
	$isNotableAlumni = FALSE;
}
if(!isset($isSelectType)) {
	$isSelectType = FALSE;
}
if(!isset($isStaffMember)) {
	$isStaffMember = FALSE;
}
if(!isset($f_Schools_SelectTypeInSchool)) {
	$f_Schools_SelectTypeInSchool = FALSE;
}
?>



<div class="block-schools_right_panel">
	<? if(isset($isSelectType) && $isSelectType) : ?>
		<?= View::factory('pages/schools/block-select_type', array(
			'school' => $school,
			'profile_experiance' => $profile_experiance,
			'profile_education' => $profile_education,
			'f_Schools_SelectTypeInSchool' => $f_Schools_SelectTypeInSchool
		)) ?>
	<? endif; ?>

	<? if(isset($schoolsManage) && !empty($schoolsManage['data'])) : ?>
		<?= View::factory('pages/schools/block-manage_your_schools', array(
			'schoolsManage' => $schoolsManage
		)) ?>
	<? endif; ?>


	<? if(isset($isCreateSchool) && $isCreateSchool) : ?>
		<?= View::factory('pages/schools/block-create_school_page', array(

		)) ?>
	<? endif; ?>


	<? if(isset($isSchoolsFollowing) && $isSchoolsFollowing) : ?>
		<?= View::factory('pages/schools/block-following_schools', array(
			'followSchools' => $followSchools
		)) ?>
	<? endif; ?>


	<? if(isset($isSchoolsInterest) && $isSchoolsInterest) : ?>
		<?= View::factory('pages/schools/block-interest_schools', array(
			'interestedSchool' => $interestedSchool
		)) ?>
	<? endif; ?>


	<? if(isset($isNotableAlumni) && $isNotableAlumni) : ?>
		<?= View::factory('pages/schools/block-notable_alumni', array(
			'notableAlumni' => $notableAlumni
		)) ?>
	<? endif; ?>

	<? if(isset($isStaffMember) && $isStaffMember) : ?>
		<?= View::factory('pages/schools/block-staff_member', array(
			'staffMember' => $staffMember
		)) ?>
	<? endif; ?>



	<!--	--><?// if(isset($peopleAlsoViewed) && !empty($peopleAlsoViewed['data'])) : ?>
<!--		--><?//= View::factory('pages/companies/block-people_also_viewed', array(
//			'peopleAlsoViewed' => $peopleAlsoViewed
//		)) ?>
<!--	--><?// endif; ?>
</div>