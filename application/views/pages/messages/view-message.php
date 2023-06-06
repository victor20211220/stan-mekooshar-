<?// dump($message, 1); ?>
<?// dump($contentHistory, 1); ?>

<?
$isMyMessage = false;
if($user->id == $message->user_id){
	$isMyMessage = true;
}
?>

<div class="messages-view messages-list">
	<div class="block-title">
		<div class="messages-title_btn">
				<? if(($isMyMessage && $message->typeForUser == 0) || (!$isMyMessage && $message->typeForFriend == 0)): ?>
					<a href="<?= Request::generateUri('messages', 'archiveMessages', $message->id); ?>" onclick="return box.confirm(this);" class="icons i-archive icon-text btn-icon" >Move to archive<span></span></a>
					<a href="<?= Request::generateUri('messages', 'trashMessage', $message->id); ?>" onclick="return box.confirm(this);" class="icons i-delete icon-text btn-icon" >Move to trash<span></span></a>
				<? endif; ?>
				<? if(($isMyMessage && $message->typeForUser == 1) || (!$isMyMessage && $message->typeForFriend == 1)): ?>
					<a href="<?= Request::generateUri('messages', 'restoreArchive', $message->id); ?>" onclick="return box.confirm(this);" class="icons i-restore icon-text btn-icon" ><span></span>restore</a>
				<? endif; ?>
			<? if(($isMyMessage && $message->typeForUser == 2) || (!$isMyMessage && $message->typeForFriend == 2)): ?>
					<a href="<?= Request::generateUri('messages', 'restoreTrash', $message->id); ?>" onclick="return box.confirm(this);" class="icons i-restore icon-text btn-icon" ><span></span>restore</a>
				<? endif; ?>
		</div>
	</div>

	<ul class="list-items">
		<?= View::factory('pages/messages/item-received', array(
			'item' => $message,
			'typeViewMessage' => true
		)) ?>
	</ul>

	<? if(isset($contentHistory) && $contentHistory) : ?>
		<?= $contentHistory ?>
	<? else: ?>
		<div class="messages-history"></div>
	<? endif; ?>
</div>