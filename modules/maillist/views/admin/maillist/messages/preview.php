<?= new View('admin/maillist/messages/header'); ?>
<div class="content-inner">
<br/>
	<?= new View('admin/maillist/messages/nav', array('messageId' => $messageId, 'active' => $active)); ?>
	<a class="send_message" onclick="return confirmSend();" href="<?= Request::$controller . 'send/' . $messageId . '/' ?>">Send</a>
	<? foreach ($recipients as $recipient) : ?>
		<span style="display: inline-block"><?= $recipient['name']; ?> (<?= $recipient['email']; ?>) |</span>
	<? endforeach; ?>
	<div>
		<?= isset($message) ? $message : '' ?>
	</div>
</div>
<!--<div class="content-footer"></div>-->
<script>
		function confirmSend() {
			if (confirm("Are you sure?")) {
				return true;
			} else {
				return false;
			}
		}
</script>