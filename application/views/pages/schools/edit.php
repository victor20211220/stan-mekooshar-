<?// dump($school, 1); ?>
<?// dump($f_Schools_EditSchool, 1); ?>
<?// dump($notanleAlumni, 1); ?>
<?// dump($staffMember, 1); ?>

<div class="editschool">
	<div>
		<div class="title-big">Edit school page</div>
		<?= $f_Schools_EditSchool->form->header(); ?>
		<?= $f_Schools_EditSchool->form->render('fields1'); ?>
		<?= $f_Schools_EditSchool->form->render('fields2'); ?>
		<?= $f_Schools_EditSchool->form->render('fields3'); ?>
		<div class="editschool-notable_alumni">
			<div class="title-big">Notable alumni</div>
			<a class="icons i-add icon-text" href="<?= Request::generateUri('schools', 'addNotableAlumni', $school->id) ?>" onclick="return box.load(this);"><span></span>add notable alumni</a>
			<ul class="list-items">
				<li class="hidden"></li>
				<? foreach($notanleAlumni['data'] as $student): ?>
					<?= View::factory('pages/schools/item-notablealumni_in_settings', array(
						'student' => $student,
						'school' => $school
					)) ?>
				<? endforeach; ?>
			</ul>
		</div>
		<div class="editschool-staff_member">
			<div class="title-big">A faculty of staff member</div>
			<? if(!empty($staffMember['data'])) : ?>
				<ul class="list-items">
					<li class="hidden"></li>
					<? foreach($staffMember['data'] as $member): ?>
						<?= View::factory('pages/schools/item-staffmember_in_settings', array(
							'member' => $member,
							'school' => $school
						)) ?>
					<? endforeach; ?>
				</ul>
			<? else: ?>
				<div class="list-item-empty">
					No staff member
				</div>
			<? endif; ?>
		</div>
		<?= $f_Schools_EditSchool->form->render('submit'); ?>
		<?= $f_Schools_EditSchool->form->footer(); ?>
	</div>
</div>