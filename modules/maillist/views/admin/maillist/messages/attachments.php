<?=new View('admin/maillist/messages/header');?>
<div class="content-inner">
<br />

	<?=new View('admin/maillist/messages/nav',array('messageId' => $messageId,'active' => $active));?>

<? if (count($attachments)): ?>
<table class="w100 TableWithPadding TableWithBorder">
	<thead>
		<tr>
			<th class="w70">
				Attachment
			</th>
			<th class="w15">
				Size
			</th>
			<th class="w5">
				Delete
			</th>
		</tr>
	</thead>
	<tbody>
		<? foreach ($attachments as $attachment): ?>
			<tr>
				<td>
					<a href="<?=$pathAttachments . $messageId . '/' . $attachment->alias . '/' . $attachment->filename ?>" target="_blank"><?=$attachment->filename ?></a>
				</td>
				<td><?=number_format(($attachment->filesize / 1024), 2, '.', ' ');?>kb</td>
				<td>
					<?=Html::anchor(Request::$controller.'removeAttachment/' . $attachment->id . '/', '<img src="/resources/images/icons/remove.png" />', array('onclick' => 'javascript:return confirm("Are you sure?")'))?>
				</td>
			</tr>
		<? endforeach; ?>
	</tbody>
</table>
<? else: ?>
<p class="large">No attachments has been uploaded yet for this message.</p>
<? endif; ?>
<br />
<hr />
<p class="large blue">Upload attachment</p>
<p>
<? if (count($attachments)): ?>
	Your <strong>current</strong> attachments size: <?=number_format(($totalSize / 1024), 2, '.', '');?>Kb (~<?=number_format(($totalSize / (1024 * 1024)), 1, '.', '')?>Mb)
	<br />
<? endif; ?>
	Total size of your attachments <strong>must not exceed  <?=number_format(($maxSize / (1024 * 1024)), 0, '.', '');?>Mb</strong>
</p>
<?=$form?>
<br />
<p><a href="<?=Request::$controller?>recipients/<?=$messageId?>/" class="awesome">Save and go to recipients list</a></p>
</div>
<!--<div class="content-footer"></div>-->