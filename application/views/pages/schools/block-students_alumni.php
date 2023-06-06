<?// dump($students, 1); ?>
<?// dump($f_Schools_FindStudentsAlumni, 1); ?>
<?// dump($notableAlumni, 1); ?>
<?
if(!isset($notableAlumni)) {
	$notableAlumni = FALSE;
}
?>


<div class="block-students_alumni">
<!--	<div class="title-big">Students and alumni</div>-->
	<div class="bg-blue bg-brown">
		<div class="text-bgtitle"><span><?= $students['paginator']['count'] ?></span> RESULTS</div>
		<div>
			<? $f_Schools_FindStudentsAlumni->form->header(); ?>
			<? $f_Schools_FindStudentsAlumni->form->render('fields'); ?>
			<? $f_Schools_FindStudentsAlumni->form->render('submit'); ?>
			<? $f_Schools_FindStudentsAlumni->form->footer(); ?>
		</div>
	</div>
	<div>
		<div class="students_alumni_result">
			<ul class="list-items">
				<? foreach($students['data'] as $student):
					echo View::factory('pages/schools/list-students_alumni', array(
						'student' => $student
					));
				endforeach ?><li>
					<?= View::factory('common/default-pages', array(
							'isBand' => TRUE,
							'autoScroll' => TRUE
						) + $students['paginator']); ?>
				</li>
			</ul>
		</div><div class="students_alumni_right_panel">
			<?= View::factory('pages/schools/block-notable_alumni', array(
				'notableAlumni' => $notableAlumni
			)) ?>
		</div>
	</div>
</div>