<?// dump($messages_archive, 1); ?>
<?// dump($f_Messages_FilterMessage, 1); ?>
<?// dump($messages_countnew, 1); ?>

<div class="messages-list_archive messages-list">
	<div class="block-title">
		<?= $f_Messages_FilterMessage->form ?>
		<div class="checkbox-control" data-id="1" data-list=".checkbox-control-select"  data-select_label="Select all">
			<a href="<?= Request::generateUri('messages', 'restoreArchive') . Request::getQuery(); ?>" onclick="return web.confirm(this, true);" class="icons i-restore icon-text btn-icon hidden" ><span></span>Restore</a>
			<a href="<?= Request::generateUri('messages', 'trashArchive') . Request::getQuery(); ?>" onclick="return box.confirm(this, true);" class="icons i-delete icon-text btn-icon hidden" ><span></span>Move to trash</a>
		</div>
	</div>

	<ul class="list-items">
		<li class="hidden"></li>
		<? foreach($messages_archive['data'] as $message) : ?>
			<?= View::factory('pages/messages/item-received', array(
				'item' => $message,
				'typeListArchive' => true
			)) ?>
		<? endforeach; ?>
		<li>
			<?= View::factory('common/default-pages', array(
					'isBand' => TRUE,
					'autoScroll' => TRUE) + $messages_archive['paginator']
			) ?>
		</li>
	</ul>
</div>

<? if(isset($messages_countnew)) : ?>
	<script type="text/javascript">
		$(document).ready(function(){
			web.changeCountMessages(<?= $messages_countnew ?>);
		});
	</script>
<? endif ?>