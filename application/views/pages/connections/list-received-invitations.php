<?// dump($receiveds, 1); ?>
<?// dump($countReceived, 1); ?>

<div class="connections-list_receivedinvitations">
	<div class="connections-tab">
		<a class="btn-roundblue <?= (Request::$action == 'index') ? 'active' : NULL ?>" href="<?= Request::generateUri('connections', 'index') ?>">My connections</a>
		<a class="btn-roundblue <?= (Request::$action != 'index') ? 'active' : NULL ?>" href="<?= Request::generateUri('connections', 'receivedInvitations') ?>">My invitations <?= ($countReceived != 0) ?  ('(' . $countReceived . ')') : NULL ?></a>
	</div>

	<div class="list_connections-title">
		<div class="title-big">Received invitation</div>
	</div>
	<? if(!empty($receiveds['data'])) : ?>
		<ul class="list-items">
			<? foreach($receiveds['data'] as $received) : ?>
				<?= View::factory('pages/connections/item-received-invitations', array(
					'received' => $received
				)) ?>
			<? endforeach; ?>
		</ul>
	<? else: ?>
		<div class="list-item-empty">
			You have no received invitations yet.
		</div>
	<? endif ?>
</div>