<?=new View('admin/maillist/messages/header');?>
<div class="content-inner">
<br/>
<table class="w60" style="margin-bottom: 20px" >
	<tr>
		<td>
			<? if (isset($messageId)): ?>
				<a href="<?=Request::$controller?>message/<?=$messageId?>/" class="large awesome green">1. Message editor</a>
			<? else: ?>
				<a href="<?=Request::$controller?>message/" class="large awesome green">1. Compose message</a>
			<? endif; ?>
		</td>
		<td>
			<? if (isset($messageId)): ?>
				<a href="<?=Request::$controller?>attachments/<?=$messageId?>/" class="large awesome light-gray">2. Attachments</a>
			<? else: ?>
				<span class="large awesome disabled">2. Attachments</span>
			<? endif; ?>
		</td>
		<td>
			<? if (isset($messageId)): ?>
				<a href="<?=Request::$controller?>recipients/<?=$messageId?>/" class="large awesome light-gray">3. Recipient list</a>
			<? else: ?>
				<span class="large awesome disabled">3. Recipient list</span>
			<? endif; ?>
		</td>
		<td>
			<? if (isset($messageId)): ?>
				<a href="<?=Request::$controller?>preview/<?=$messageId?>/" class="large awesome light-gray">4. Preview and send</a>
			<? else: ?>
				<span class="large awesome disabled">4. Preview and send</span>
			<? endif; ?>
		</td>
	</tr>
</table>

<?=(isset($form) ? $form : '')?>
</div>
<!--<div class="content-footer"></div>-->