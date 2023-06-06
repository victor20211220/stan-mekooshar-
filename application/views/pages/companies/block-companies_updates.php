<?// dump($timelinesCompanies, 1); ?>

<div class="block-companies_updates">
	<div class="title-big">Recent Updates</div>

	<div class="block-list-updates">
		<ul class="list-items">
			<? if($timelinesCompanies) : ?>
				<li class="hidden"></li>
				<? foreach($timelinesCompanies['data'] as $timeline) : ?>
					<?= View::factory('pages/updates/item-update', array(
						'timeline' => $timeline,
						'isUsernameLink' => TRUE
					)) ?>
				<? endforeach; ?>
				<li>
					<?= View::factory('common/default-pages', array(
							'isBand' => TRUE,
							'autoScroll' => TRUE
						) + $timelinesCompanies['paginator']) ?>
				</li>
			<? endif; ?>
		</ul>

	</div>
</div>