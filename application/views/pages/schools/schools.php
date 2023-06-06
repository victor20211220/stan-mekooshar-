<?// dump($f_Schools_FindStudentsAlumni, 1); ?>
<?// dump($students, 1); ?>

<div class="school_students_schools">
	<?= View::factory('parts/parts-left_big', array(
		'lefttop' => '',
		'left' => View::factory('pages/schools/block-students_schools', array(
			'f_Schools_FindStudentsAlumni' => $f_Schools_FindStudentsAlumni,
			'students' => $students
		)),
		'right' => ''
	)) ?>
</div>