<div class="block-payment">
	<?=$form->header() ?>
	<div class="title-small">Please select plan:</div>
	<?=$form->render('field') ?>

	<div class="payment-left">
		<div class="title-big">Card information</div>
		<?=$form->render('creditcard') ?>
	</div><div class="payment-right">
		<div class="title-big">Delivery information</div>
		<?=$form->render('customer') ?>
	</div>

	<?=$form->render('submit') ?>
	<?=$form->footer() ?>
</div>

