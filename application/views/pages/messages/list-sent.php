<?// dump($messages_sent, 1); ?>
<?// dump($messages_countnew, 1); ?>

<div class="messages-list_sent messages-list">
	<div class="block-title">
		<div class="checkbox-control" data-id="1" data-list=".checkbox-control-select"  data-select_label="Select all">
			<a href="<?= Request::generateUri('messages', 'archiveSent') . Request::getQuery(); ?>" onclick="return box.confirm(this, true);" class="icons i-archive icon-text btn-icon hidden" ><span></span>Move to archive</a>
			<a href="<?= Request::generateUri('messages', 'trashSent') . Request::getQuery(); ?>" onclick="return box.confirm(this, true);" class="icons i-delete icon-text btn-icon hidden" ><span></span>Move to trash</a>
		</div>
	</div>

	<ul class="list-items">
		<li class="hidden"></li>
		<? foreach($messages_sent['data'] as $message) : ?>
			<?= View::factory('pages/messages/item-received', array(
				'item' => $message,
				'typeListSent' => true
			)) ?>
		<? endforeach; ?>
		<li>
			<?= View::factory('common/default-pages', array(
					'isBand' => TRUE,
					'autoScroll' => TRUE) + $messages_sent['paginator']
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