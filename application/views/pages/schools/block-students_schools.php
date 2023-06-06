<?// dump($f_Schools_FindStudentsAlumni, 1); ?>
<?// dump($students, 1); ?>

<div class="block-students_schools">
	<div class="title-big">Schools</div>
	<? if(!empty($students['data'])) : ?>
		<div class="bg-blue bg-brown">
			<div class="text-bgtitle"><span><?= $students['paginator']['count'] ?></span> RESULTS</div>
			<div>
				<? $f_Schools_FindStudentsAlumni->form->header(); ?>
				<? $f_Schools_FindStudentsAlumni->form->render('fields'); ?>
				<? $f_Schools_FindStudentsAlumni->form->render('submit'); ?>
				<? $f_Schools_FindStudentsAlumni->form->render('fields2'); ?>
				<? $f_Schools_FindStudentsAlumni->form->footer(); ?>

			</div>
		</div>
		<div>
			<div class="students_schools_result">
				<ul class="list-items">
					<? foreach($students['data'] as $student):
						echo View::factory('pages/schools/item-students_school', array(
							'student' => $student
						));
					endforeach ?><li>
						<?= View::factory('common/default-pages', array(
								'isBand' => TRUE,
								'autoScroll' => TRUE
							) + $students['paginator']); ?>
					</li>
				</ul>
			</div>
		</div>
	<? else: ?>
		<div class="list-item-empty">
			No result
		</div>
	<? endif; ?>
</div>