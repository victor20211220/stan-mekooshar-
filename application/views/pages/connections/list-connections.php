<?// dump($connections, 1); ?>
<?// dump($countReceived, 1); ?>

<div class="connections-list_connections">
	<div class="connections-tab">
		<a class="btn-roundblue <?= (Request::$action == 'index') ? 'active' : NULL ?>" href="<?= Request::generateUri('connections', 'index') ?>">My connections</a>
		<a class="btn-roundblue <?= (Request::$action != 'index') ? 'active' : NULL ?>" href="<?= Request::generateUri('connections', 'receivedInvitations') ?>">My invitations <?= ($countReceived != 0) ?  ('(' . $countReceived . ')') : NULL ?></a>
	</div>

	<? if(!empty($connections['data'])) : ?>
		<div class="list_connections-title">
			<div class="title-big">Connections</div>
<!--			<a href="--><?//= Request::generateUri('connections', 'removeConnections')?><!--" class="infuture icons i-delete icon-text"  onclick="return box.confirm(this, true);" title="Delete selected">Delete selected<span></span></a>-->
		</div>
		<ul class="list-items">
			<? foreach($connections['data'] as $connection) : ?>
				<?= View::factory('pages/connections/item-connections', array(
					'connection' => $connection
				)) ?>
			<? endforeach; ?>
		</ul>
	<? else: ?>
		<div class="list-item-empty">
			You have no connections yet.
		</div>
	<? endif ?>
</div>