<?// dump($listShare, 1); ?>

<div class="block-list-sharespeople">
	<? foreach($listShare['data'] as $user) : ?>
		<?= View::factory('parts/userava-more', array(
			'ouser' => $user,
			'isTooltip' => FALSE,
			'isCustomInfo' => TRUE,
		)) ?>
	<? endforeach; ?>
	<div>
		<a class="btn-blue" href="#" onclick="box.close(); return false;">Close</a>
	</div>
</div>
