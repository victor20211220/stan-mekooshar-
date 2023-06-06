<?// dump($form, 1); ?>

<div class="add_new_filter_field">
	<?= $form->header(); ?>
		<?= $form->render('default') ?>
		<?= $form->render('field') ?>
		<?= $form->render('submit') ?>
	<?= $form->footer(); ?>
</div>
