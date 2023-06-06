<?// dump($page, 1); ?>
<?// dump($f_AdvertiseWithUs, 1); ?>

<div class="block_page">
	<div style="height: 392px;">
		<h1 class="page-header">Advertise with us</h1>

		<div class="advertise_with_us-subtitle">
			Want to learn more about advertising with Mekooshar.com?<br>
			Provide your contact info and we'll follow up within one business day.
		</div>
		<div>
			<?= $f_AdvertiseWithUs->form->render(); ?>
		</div>
	</div>

	<h2 class="page-header" style="margin-top: 10px;"><?= Html::chars($page->title1); ?></h2>
	<div class="advertise_info">
		<?= $page->text ?>
	</div>
	<p style="text-align: center; padding-top: 0px;">
		<img src="/resources/images/logo-small_black.png" />
	</p>
	<br><br>
	<a href="/" class="btn-roundblue_big" title="Back to Home">Back to Home</a>
</div>

