<?// dump($timelines, 1); ?>

<div class="block-list-updates">
	<div class="line"></div>
	<ul class="list-items">
		<li class="hidden"></li>
				<? foreach($timelines['data'] as $timeline) : ?>
					<?= View::factory('pages/updates/item-update', array(
						'timeline' => $timeline,
						'isUsernameLink' => TRUE
					)) ?>
				<? endforeach; ?>
		<li>
			<?= View::factory('common/default-pages', array(
					'isBand' => TRUE,
					'autoScroll' => TRUE
				) + $timelines['paginator']) ?>
		</li>
	</ul>

</div>
