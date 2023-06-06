<img src="<?= $image->urls['url_tiny']; ?>" data-id="<?= $image->id; ?>" width="100" height="100" />

<div class="uploader-list-actions" id="images_<?= $image->id ?>">
	<a eva-confirm href="/uploader/files/removeById/<?= $image->id; ?>/" onclick="return web.removeImageFromAddUpdate(this);"  class="icons i-close"><span></span></a>
</div>

<script type="text/javascript">
	$('#addupdate').find('.upload-images').removeClass('hidden');
	$('#addupdate-text').removeAttr('required');
	$(document).ready(function(){
		web.reinitUserGallery('.upload-images');
	});
	<? if(isset($_SESSION['updates']['isLink'])) : ?>
		$('#addupdate-type').val('<?= POST_TYPE_WEB ?>');
	<? elseif(!isset($_SESSION['updates']['isLink']) && isset($_SESSION['uploader-list']) && !empty($_SESSION['uploader-list'])) : ?>
		$('#addupdate-type').val('<?= POST_TYPE_IMAGE ?>');
	<? else : ?>
		$('#addupdate-type').val('<?= POST_TYPE_TEXT ?>');
	<? endif; ?>
</script>
