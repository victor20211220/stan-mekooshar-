<?// dump($emails, 1); ?>

<div class="block-invite-sent_invitations">
	<div class="title-big">Invite connections</div>
	<div class="invite-sent_invitations-description">
		Invite your friends and collegues to join your professional network and stay in touch.
		<? if(count($emails) != 0 ) : ?>
			<div class="checkbox-control" data-select_label="Select all" data-id="1" data-control=".connectionsAdd" data-list=".checkbox-control-select"></div>
		<? endif; ?>
	</div>
	<? if(count($emails) != 0 ) : ?>
		<ul class="list-items">
			<? foreach($emails as $email) : ?><li>
				<div class="checkbox-control-select" data-id="<?= $email ?>"></div>
				<div><?= $email ?></div>
			</li><? endforeach ?>
		</ul>
		<div class="block-connections-btn">
			<a class="btn-roundbrown" href="<?= Request::generateUri('connections', 'invite') ?>">Cancel</a>
			<a class="btn-roundblue connectionsAdd hidden" href="<?= Request::generateUri('connections', 'sendInvitations') ?>">Send Invitations</a>
		</div>
	<? else : ?>
		<div class="block-invite-nodata">No find</div>
		<div class="block-connections-btn">
			<a class="btn-roundblue" href="<?= Request::generateUri('connections', 'invite') ?>">Finish</a>
		</div>
	<? endif; ?>

</div>

