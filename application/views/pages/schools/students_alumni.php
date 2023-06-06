<?// dump($school, 1); ?>
<?// dump($students, 1); ?>
<?// dump($f_Schools_FindStudentsAlumni, 1); ?>
<?// dump($notableAlumni, 1); ?>

<div class="school_students_alumni">
	<?= View::factory('parts/parts-left_big', array(
		'lefttop' => View::factory('pages/schools/block-school_head', array(
			'active' => 'students',
			'school' => $school
		)),
		'left' => View::factory('pages/schools/block-students_alumni', array(
			'students' => $students,
			'f_Schools_FindStudentsAlumni' => $f_Schools_FindStudentsAlumni,
			'notableAlumni' => $notableAlumni
		)),
		'right' => ''
	)) ?>
</div>