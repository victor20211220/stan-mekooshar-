<?// dump($f_Jobs_NewJob, 1); ?>

<div class="block-job_new">
	<div class="title-big">Post job</div>
	<? $f_Jobs_NewJob->form->header(); ?>
	<? $f_Jobs_NewJob->form->render('fields1'); ?>
	<? $f_Jobs_NewJob->form->render('fields2'); ?>
	<? $f_Jobs_NewJob->form->render('fields4'); ?>
	<? $f_Jobs_NewJob->form->render('fields3'); ?>
	<? $f_Jobs_NewJob->form->render('fields5'); ?>
	<? $f_Jobs_NewJob->form->render('submit'); ?>
	<? $f_Jobs_NewJob->form->footer(); ?>
</div>
