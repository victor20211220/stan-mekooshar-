<?// dump($received, 1); ?>

<li data-id="profile_<?= $received->user_id ?>">
	<div>
		<? $received->setInvisibleProfile = USER_PROFILE_VISIBLE; ?>
		<?= View::factory('parts/userava-more', array(
			'isCustomInfo' => TRUE,
			'ouser' => $received,
			'keyId' => 'user_id',
			'avasize' => 'avasize_44',
			'isTooltip' => false,
			'text' => $received->message
		)) ?>
		<div class="list_connections-btn">
			<a href="<?= Request::generateUri('connections', 'acceptReceived', $received->id); ?>" onclick="return box.load(this);" class="icons i-accept icon-text btn-icon" ><span></span>accept</a>
			<a href="<?= Request::generateUri('connections', 'ignoreReceived', $received->id); ?>"  onclick="return box.confirm(this, true);" class="icons i-deny icon-text btn-icon" ><span></span>ignore</a>
		</div>
		<div class="list_connections-data"><?= date('m.d.Y h:i A', strtotime($received->createDate)) ?></div>
	</div>
</li>