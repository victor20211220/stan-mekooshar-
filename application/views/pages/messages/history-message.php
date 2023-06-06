<?// dump($messages, 1); ?>
<?// dump($friend_id, 1); ?>
<?// dump($messages_countnew, 1); ?>
<?// dump($isVisible, 1); ?>
<?
if(!isset($isVisible)) {
	$isVisible = true;
}
?>


<div class="messages-history <?= (!$isVisible) ? 'hidden' : null ?>">
	<div class="title-big">Messages history</div>
	<div class="line"></div>

	<ul class="list-items">
		<li class="hidden"></li>
		<? foreach($messages['data'] as $message) : ?>
			<?= View::factory('pages/messages/item-received', array(
				'item' => $message,
				'typeListHistory' => TRUE,
				'avasize' => 'avasize_52'
			)) ?>
		<? endforeach; ?>
		<li>
			<?= View::factory('common/default-pages', array(
				'isBand' => TRUE,
				'autoScroll' => TRUE,
				'controller' => Request::generateUri(false, 'history', $friend_id)) + $messages['paginator']
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