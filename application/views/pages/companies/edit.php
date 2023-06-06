<?// dump($f_Companies_EditCompany, 1); ?>

<div class="editcompany">
	<div>
		<div class="title-big">Edit company page</div>
		<?= $f_Companies_EditCompany->form->header(); ?>
		<?= $f_Companies_EditCompany->form->render('fields1'); ?>
		<?= $f_Companies_EditCompany->form->render('fields2'); ?>
		<?= $f_Companies_EditCompany->form->render('fields3'); ?>
		<?= $f_Companies_EditCompany->form->render('submit'); ?>
		<?= $f_Companies_EditCompany->form->footer(); ?>
	</div>
</div>