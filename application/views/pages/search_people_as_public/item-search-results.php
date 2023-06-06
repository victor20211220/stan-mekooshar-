<?// dump($result, 1); ?>

<li>
	<div>
		<?=
//		dump($result);
		View::factory('parts/userava-more', array(
			'ouser' => $result,
			'isCustomInfo' => TRUE,
			'avasize' => 'avasize_44',
			'isTooltip' => false,
			'isPopupLoginOrRegister' => TRUE
		)) ?>
		<div class="list_search-result-btn">
			<? if(is_null($result->connectionApproved)) : ?>
				<a href="<?= Request::generateUri('profile', $result->id); ?>"  onclick="return web.popupLoginOrRegister(this, true);" class="icons i-connect icon-text btn-icon" data-id="profile_<?= $result->id ?>"><span></span>Connect</a>
			<? elseif($result->connectionApproved == 1) : ?>
				<a href="<?= Request::generateUri('messages', 'sentMessageFromProfile', $result->id) ?>" class=" icons i-messages icon-text btn-icon" onclick="return box.load(this);"  ><span></span>message</a>
			<? elseif($result->connectionApproved == 0) : ?>
				<div class="is-blocked invitationsent"><span></span>invitation sent</div>
			<? endif; ?>
		</div>
		<div class="list_search-result-data">
			<!--			--><?//= date('m.d.Y h:i A', strtotime($sentInvitation->createDate)) ?>
			<!--			--><?// if($sentInvitation->typeApproved == 2) : ?>
			<!--				<div>-->
			<!--					Your invitation was ignored by the user-->
			<!--				</div>-->
			<!--			--><?// endif; ?>
		</div>
	</div>
</li>