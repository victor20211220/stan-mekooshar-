<?// dump($connections, 1); ?>

<div class="block-invite-add_registred">
	<div class="title-big">Search results</div>
	<div class="invite-add_registred-description">
		We have found <span><?= count($connections['data']) ?></span> people you know on Mekooshar. You can add them to your network.
		<? if(count($connections['data']) != 0 ) : ?>
			<div class="checkbox-control" data-select_label="Select all" data-id="1" data-control=".connectionsAdd" data-list=".checkbox-control-select"></div>
		<? endif; ?>
	</div>
	<? if(count($connections['data']) != 0 ) : ?>
		<ul class="list-items">
				<? foreach($connections['data'] as $connection) {
					echo View::factory('pages/connections/item-block-invite-add_registred', array(
						'connection' => $connection
					));
				} ?>
		</ul>
		<div class="block-connections-btn">
			<a class="btn-roundbrown" href="<?= Request::generateUri('connections', 'sendInvitations') ?>">Skip</a>
			<a class="btn-roundblue connectionsAdd hidden" href="<?= Request::generateUri('connections', 'addRegistered') ?>">Add to connections</a>
		</div>
	<? else : ?>
		<div class="block-invite-nodata">No find</div>
		<div class="block-connections-btn">
			<a class="btn-roundbrown" href="<?= Request::generateUri('connections', 'sendInvitations') ?>">Skip</a>
		</div>
	<? endif; ?>
</div>
