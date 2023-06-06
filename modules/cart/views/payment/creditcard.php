<? if (!empty($errors)) :
	foreach($errors as $error) :  ?>
		<ul class="payment-error">
			<? foreach ($errors as $error): ?>
				<li><?=$error?></li>
			<? endforeach ?>
		</ul>
	<? endforeach; ?>
	<div class="line"></div>
<? endif; ?>

<?=$form?>