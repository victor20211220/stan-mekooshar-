<?// dump($notifications, 1); ?>

<div class="block-notifications">
	<div>
		<div>
			<ul class="list-items">
				<li class="hidden"></li>
				<? if(isset($notifications) && !empty($notifications['data'])) : ?>
					<? foreach($notifications['data'] as $notification) : ?>
						<?= View::factory('pages/item-notifications', array(
							'notification' => $notification
						)) ?>
					<? endforeach; ?>
					<li>
						<?= View::factory('common/default-pages', array(
								'controller' => Request::generateUri('notifications', 'index'),
								'isBand' => TRUE
							) + $notifications['paginator']) ?>
					</li>
				<? else: ?>
					<li class="list-item-empty">
						No notifications
					</li>
				<? endif; ?>
			</ul>
		</div>
	</div>
</div>