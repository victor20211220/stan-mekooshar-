<?// dump($schoolsManage, 1); ?>
<?// dump($timelinesSchools, 1); ?>
<?// dump($followSchools, 1); ?>
<?// dump($interestedSchool, 1); ?>

<div class="schools_updates">
	<?= View::factory('parts/parts-left_big', array(
		'lefttop' => View::factory('pages/schools/block-schools_starthead', array(

		)),
		'left' => View::factory('pages/schools/block-schools_updates', array(
			'timelinesSchools' => $timelinesSchools
		)),
		'right' => View::factory('pages/schools/rightpanel', array(
			'isCreateSchool' => TRUE,
			'schoolsManage' => $schoolsManage,
			'followSchools' => $followSchools,
			'isSchoolsFollowing' => TRUE,
			'isSchoolsInterest' => TRUE,
			'interestedSchool' => $interestedSchool
		))
	)) ?>
</div>