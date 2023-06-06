<?// dump($page, 1); ?>
<?// dump($f_Support, 1); ?>

<div class="block_page">
	<h1 class="page-header">Support</h1>

	<div class="advertise_with_us-subtitle">
		Need help? Contact us and we'll get back to you within one business day.<br><br>
	</div>
	<div>
		<?= $f_Support->form->render(); ?>
	</div>

	<h2 class="page-header"><?= Html::chars($page->title1); ?></h2>
	<div class="advertise_info" style="padding-top: 15px">
		<?= $page->text ?>
	</div>
	<a href="/" class="btn-roundblue_big" title="Back to Home">Back to Home</a>
</div>

