<?// dump($messages_trash, 1); ?>
<?// dump($f_Messages_FilterMessage, 1); ?>
<?// dump($messages_countnew, 1); ?>

<div class="messages-list_trash messages-list">
	<div class="block-title">
		<?= $f_Messages_FilterMessage->form ?>
		<div class="checkbox-control" data-id="1" data-list=".checkbox-control-select"  data-select_label="Select all">
			<a href="<?= Request::generateUri('messages', 'restoreTrash') . Request::getQuery(); ?>" onclick="return box.confirm(this, true);" class="icons i-restore icon-text btn-icon hidden" ><span></span>restore</a>
			<a href="<?= Request::generateUri('messages', 'delete') . Request::getQuery(); ?>" onclick="return box.confirm(this, true);" class="icons i-delete icon-text btn-icon hidden" ><span></span>Delete</a>
		</div>
	</div>

	<ul class="list-items">
		<li class="hidden"></li>
		<? foreach($messages_trash['data'] as $message) : ?>
			<?= View::factory('pages/messages/item-received', array(
				'item' => $message,
				'typeListTrash' => true
			)) ?>
		<? endforeach; ?>
		<li>
			<?= View::factory('common/default-pages', array(
					'isBand' => TRUE,
					'autoScroll' => TRUE) + $messages_trash['paginator']
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