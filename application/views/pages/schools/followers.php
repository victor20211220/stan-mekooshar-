<?// dump($school, 1); ?>
<?// dump($followers, 1); ?>
<?// dump($notableAlumni, 1); ?>
<?// dump($profile_experiance, 1); ?>
<?// dump($profile_education, 1); ?>
<?// dump($staffMember, 1); ?>
<?// dump($f_Schools_SelectTypeInSchool, 1); ?>


<div class="company">
	<?= View::factory('parts/parts-left_big', array(
		'lefttop' => View::factory('pages/schools/block-school_head', array(
			'active' => 'followers',
			'school' => $school
		)),
		'left' => View::factory('pages/schools/block-followers', array(
			'followers' => $followers
		)),
		'right' => View::factory('pages/schools/rightpanel', array(
			'isSelectType' => TRUE,
			'isNotableAlumni' => TRUE,
			'isStaffMember' => TRUE,
			'school' => $school,
			'profile_education' => $profile_education,
			'profile_experiance' => $profile_experiance,
			'notableAlumni' => $notableAlumni,
			'staffMember' => $staffMember,
			'f_Schools_SelectTypeInSchool' => $f_Schools_SelectTypeInSchool
		))
	)) ?>
</div>