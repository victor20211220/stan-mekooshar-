<?// dump($sentInvitation, 1); ?>

<li>
	<div>
		<?= View::factory('parts/userava-more', array(
			'isCustomInfo' => TRUE,
			'ouser' => $sentInvitation,
			'keyId' => 'friend_id',
			'avasize' => 'avasize_44',
			'isTooltip' => false,
			'text' => $sentInvitation->message
		)) ?>
		<div class="list_connections-btn <?= ($sentInvitation->typeApproved == 0) ? 'invitation-noanswer' : 'invitation-ignore' ?>">
			<? if($sentInvitation->typeApproved == 2) : ?>
				<a href="<?= Request::generateUri('connections', 'resentInvitation', $sentInvitation->id); ?>"  class="icons i-resent icon-text btn-icon" ><span></span>resent</a>
				<a href="<?= Request::generateUri('connections', 'deleteInvitation', $sentInvitation->id); ?>"  onclick="return box.confirm(this, true);" class="icons i-delete icon-text btn-icon" ><span></span>delete</a>
			<? else: ?>
				<a href="<?= Request::generateUri('connections', 'discartInvitation', $sentInvitation->id); ?>"  onclick="return box.confirm(this, true);" class="icons i-discart icon-text btn-icon" ><span></span>cancel</a>
			<? endif; ?>
		</div>
		<div class="list_connections-data  <?= ($sentInvitation->typeApproved == 2) ? 'invitation-ignore' : 'invitation-noanswer' ?>">
			<?= date('m.d.Y h:i A', strtotime($sentInvitation->createDate)) ?>
			<? if($sentInvitation->typeApproved == 2) : ?>
				<div>
					Your invitation was ignored by the user
				</div>
			<? endif; ?>
		</div>
	</div>
</li>