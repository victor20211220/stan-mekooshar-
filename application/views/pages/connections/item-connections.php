<?// dump($connection, 1); ?>

<li>
	<div>
		<?= View::factory('parts/userava-more', array(
			'isCustomInfo' => TRUE,
			'ouser' => $connection,
			'keyId' => 'friend_id',
			'avasize' => 'avasize_44',
			'isTooltip' => false
		)) ?>
		<div class="list_connections-btn">
			<a href="<?= Request::generateUri('messages', 'sentMessageFromUserAvaBlock', $connection->friend_id) ?>" onclick="return box.load(this, true);" class="icons i-messages icon-text btn-icon" ><span></span>send message</a>
			<a href="<?= Request::generateUri('connections', 'deleteConnection', $connection->id); ?>"  onclick="return box.confirm(this, true);" class="icons i-delete icon-text btn-icon" ><span></span>delete</a>
		</div>
		<div class="list_connections-tags"><?= ($connection->connectionsTags != 'NULL') ? $connection->connectionsTags : null ?> <a href="<?= Request::generateUri('connections', 'editTags', $connection->id) ?>" onclick="return box.load(this);" class="btn-roundblue-border icons i-editcustom" ><span></span>edit</a></div>
	</div>
</li>

