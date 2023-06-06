<table class="w60" style="margin-bottom: 20px" >
	<tr>
		<td>
			<a href="<?=Request::$controller?>message/<?=$messageId?>/" class="large awesome light-gray">1. Message editor</a>
		</td>
		<td>
			<a href="<?=Request::$controller?>attachments/<?=$messageId?>/" class="large awesome light-gray <?= ($active == 'attachments') ? 'green' : ''?>">2. Attachments</a>
		</td>
		<td>
			<a href="<?=Request::$controller?>recipients/<?=$messageId?>/" class="large awesome light-gray <?= ($active == 'recipients') ? 'green' : ''?>">3. Recipient list</a>
		</td>
		<td>
			<a href="<?=Request::$controller?>preview/<?=$messageId?>/" class="large awesome light-gray <?= ($active == 'preview') ? 'green' : ''?>">4. Preview and send</a>
		</td>
	</tr>
</table>