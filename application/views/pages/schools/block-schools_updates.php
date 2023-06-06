<?// dump($timelinesSchools, 1); ?>

<div class="block-schools_updates">
	<div class="title-big">Recent Updates</div>

	<? if(isset($timelinesSchools) && !empty($timelinesSchools['data'])) : ?>
		<div class="block-list-updates">
			<ul class="list-items">
							<? if($timelinesSchools) : ?>
								<li class="hidden"></li>
								<? foreach($timelinesSchools['data'] as $timeline) : ?>
									<?= View::factory('pages/updates/item-update', array(
										'timeline' => $timeline,
										'isUsernameLink' => TRUE
									)) ?>
								<? endforeach; ?>
								<li>
									<?= View::factory('common/default-pages', array(
											'isBand' => TRUE,
											'autoScroll' => TRUE
										) + $timelinesSchools['paginator']) ?>
								</li>
							<? endif; ?>
			</ul>
		</div>
	<? else: ?>
		<div class="list-item-empty">
			No recent updates
		</div>
	<? endif; ?>
</div>