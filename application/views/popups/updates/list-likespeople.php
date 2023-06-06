<?// dump($listLikes, 1); ?>

<div class="block-list-likespeople">
	<? foreach($listLikes['data'] as $user) : ?>
		<?= View::factory('parts/userava-more', array(
			'ouser' => $user,
			'isTooltip' => FALSE,
			'isCustomInfo' => TRUE,
		)) ?>
	<? endforeach; ?>
	<div>
		<a class="btn-roundblue" href="#" onclick="box.close(); return false;">Close</a>
	</div>
</div>
