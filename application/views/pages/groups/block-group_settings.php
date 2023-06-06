<?// dump($f_Groups_EditGroup, 1); ?>

<div class="block-group_settings">
	<div>
		<div class="title-big">Settings</div>
		<?= $f_Groups_EditGroup->form->header(); ?>

		<div class="edit_group-tabs">
			<div>
				<?= $f_Groups_EditGroup->form->render('fields1'); ?>
			</div><div>
				<?= $f_Groups_EditGroup->form->render('fields2'); ?>
			</div>
		</div>

		<?= $f_Groups_EditGroup->form->render('fields3'); ?>
		<?= $f_Groups_EditGroup->form->render('fields4'); ?>
		<?= $f_Groups_EditGroup->form->render('submit'); ?>
		<?= $f_Groups_EditGroup->form->footer(); ?>
	</div>
</div>