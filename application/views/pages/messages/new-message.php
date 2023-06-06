<?// dump($f_Messages_NewMessage, 1); ?>
<?// dump($message, 1); ?>
<?// dump($viewHistory, 1); ?>

<div class="messages-new_message">
	<? if($message) : ?>
		<div class="form-message">
			<?= $message ?><br>
			<br>
			<a class="icons i-newmessage" href="<? Request::generateUri(false, false); ?>" title="Send new message"><span></span> Send new message</a>
		</div>
	<? else : ?>
		<div class="block-title">
			<div class="title-big">Send new message</div>
		</div>
		<div class="new_message-form">
			<? $f_Messages_NewMessage->form->render(); ?>
		</div>
	<? endif; ?>
	<div class="messages-list">
		<?= $viewHistory ?>
	</div>
</div>


<script type="text/javascript">
	$(document).ready(function(){
		if($('#newmessage-userName').val().length > 0) {
			$('#newmessage-message').focus();
		}
	});
</script>