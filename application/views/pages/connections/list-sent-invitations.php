<?// dump($sentInvitations, 1); ?>
<?// dump($countReceived, 1); ?>

<div class="connections-list_sentinvitations">
	<div class="connections-tab">
		<a class="btn-roundblue <?= (Request::$action == 'index') ? 'active' : NULL ?>" href="<?= Request::generateUri('connections', 'index') ?>">My connections</a>
		<a class="btn-roundblue <?= (Request::$action != 'index') ? 'active' : NULL ?>" href="<?= Request::generateUri('connections', 'receivedInvitations') ?>">My invitations  <?= ($countReceived != 0) ?  ('(' . $countReceived . ')') : NULL ?></a>
	</div>


	<div class="list_connections-title">
		<div class="title-big">Sent invitation</div>
	</div>
	<? if(!empty($sentInvitations['data'])) : ?>
		<ul class="list-items">
			<? foreach($sentInvitations['data'] as $sentInvitation) : ?>
				<?= View::factory('pages/connections/item-sent-invitations', array(
					'sentInvitation' => $sentInvitation
				)) ?>
			<? endforeach; ?>
		</ul>
	<? else: ?>
		<div class="list-item-empty">
			You have no sent invitations yet.
		</div>
	<? endif ?>
</div>