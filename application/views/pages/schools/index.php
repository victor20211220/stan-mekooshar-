<?// dump($school, 1); ?>
<?// dump($f_Updates_AddUpdate, 1); ?>
<?// dump($timelinesSchool, 1); ?>
<?// dump($notableAlumni, 1); ?>
<?// dump($profile_experiance, 1); ?>
<?// dump($profile_education, 1); ?>
<?// dump($staffMember, 1); ?>
<?// dump($f_Schools_SelectTypeInSchool, 1); ?>
<?

if(!is_null($school->coverToken)) {
	$schoolCover = Model_Files::generateUrl($school->coverToken, 'jpg', FILE_SCHOOL_COVER, TRUE, false, 'cover_580');
	$middleView = '<img src="' . $schoolCover . '" title="' . $school->name . '" alt="cover ' . $school->name . '"  width ="580px"/>';
} else {
	$middleView = false;
}
?>

<div class="school">
	<?= View::factory('parts/parts-left_big', array(
		'lefttop' => View::factory('pages/schools/block-school_head', array(
			'school' => $school
		)),
		'leftmiddle' => $middleView,
		'left' => View::factory('pages/schools/block-updates_and_summary', array(
			'school' => $school,
			'f_Updates_AddUpdate' => $f_Updates_AddUpdate,
			'timelinesSchool' => $timelinesSchool
		)),
		'right' => View::factory('pages/schools/rightpanel', array(
			'isSelectType' => TRUE,
			'isNotableAlumni' => TRUE,
			'isStaffMember' => TRUE,
			'notableAlumni' => $notableAlumni,
			'school' => $school,
			'profile_experiance' => $profile_experiance,
			'profile_education' => $profile_education,
			'staffMember' => $staffMember,
			'f_Schools_SelectTypeInSchool' => $f_Schools_SelectTypeInSchool
		))
	)) ?>
</div>