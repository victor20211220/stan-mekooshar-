<h1>User management</h1>
<p>
<a href="<?=Request::$controller . 'subscribers/' ?>">&larr; back to subscribers list</a>
</p>
<table class="w100">
	<tr>
		<td class="w50 padding10">
			<?=(isset($form) ? $form : '')?>
		</td>
		<td class="w50 padding10">
			<? if($id) : ?>
			<h3>Attachments</h3>
			
			<? if(!empty($attachments)) : ?>
				<table class="w100 TableWithPadding TableWithBorder">
					<thead>
						<tr>
							<th class="w70">
								Attachment
							</th>
							<th class="w5">
								Delete
							</th>
						</tr>
					</thead>
					<tbody>
						<? foreach($attachments as $attachment) : ?>
							<tr>
								<td>
									<a href="<?=$pathAttachments . $id . '/' . $attachment['alias'] . '/' . $attachment['filename']?>" target="_blank"><?=$attachment['filename']?></a>
								</td>
								<td>
									<?=Html::anchor(Request::$controller.'removeAttachment/' . $attachment['id'] . '/', '<img src="/resources/images/icons/remove.png" />', array('onclick' => 'javascript:return confirm("Are you sure?")'))?>
								</td>
							</tr>
						<? endforeach; ?>
					</tbody>
				</table>
			<? else : ?>
			<p class="italic">No attachments</p>
			<? endif; ?>
			
			<p><a href="<?=Request::$controller?>addAttachment/<?=$id ?>" class="awesome green">+ add new attachment</a></p>
			<? endif; ?>
		</td>
	</tr>
</table>
