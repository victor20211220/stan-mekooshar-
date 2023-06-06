<?// dump($attach, 1); ?>

<li data-id="file_<?= $attach->token ?>">
	<a class="apply_file" href="<?= Request::generateUri('download', 'apply', array('0', $attach->token)) ?>" target="_blank"><?= $attach->name ?></a>
	<a class="icons i-deleteround" href="<?= Request::generateUri('jobs', 'deleteApplyFile', $attach->token) ?>" onclick="return box.confirm(this, true)"><span></span></a>
</li>

<script type="text/javascript">
	web.applyJobAddFile('<?= $attach->token ?>');
	$('.uploader-list > .image-wrap').remove();
</script>