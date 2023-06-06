<?// dump($f_NewPassword, 1); ?>

<div class="newpassword">
	<?= $f_NewPassword->header(); ?>
		<div>Please enter new password for your account!</div>
		<br>
		<?= $f_NewPassword->render('fields') ?>
	<br>

		<?= $f_NewPassword->render('submit') ?>
	<?= $f_NewPassword->footer(); ?>
</div>
