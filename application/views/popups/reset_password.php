<?// dump($f_Reset, 1); ?>

<div class="resetpassword">
	<?= $f_Reset->header(); ?>

		<div>For reset password, please enter you registration email</div>
		<?= $f_Reset->render('fields') ?>
	<br>
		<?= $f_Reset->render('submit') ?>
	<?= $f_Reset->footer(); ?>
</div>
