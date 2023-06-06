<?= $form->header(); ?>
<div class="admin_upload_file_ico">
	<? if ($type == "images") { ?>
		<img class="admin_upload_file_ico_image" src="<?= $url ?>" />
	<? } else { ?>
		<div class="admin_upload_file_ico_other <?=$type == "audios" ? "uploader_audio" : ''?> <?= $type == "attachments" ? "uploader_attachment" : ''; ?> "></div>
	<? } ?>
</div>
<div class="admin_upload_file_info">
	<fieldset class="fileName">
		<ol>
			<li>
				<div class="remove_li">
					<a eva-content="Remove item" class="nav-btn main-btn-remove" eva-confirm="" href="<?= Request::generateUri('admin/directory', 'removeuploadfile/' . $token, false) ?>" onClick="return uploader.removeUploadFile(this);"><span>Remove</span></a>
				</div>
				<div class="autoform-label">
					<label>File name:</label>
				</div>
				<div class="autoform-element">
					<?= $name ?>
				</div>
				
			</li>
		</ol>
	</fieldset>
	<?= $form->render('default'); ?>
</div>
<?= $form->footer(); ?>

<script type="text/javascript">
	$(document).ready(function() {
		$.system.__init();
	})
</script>
