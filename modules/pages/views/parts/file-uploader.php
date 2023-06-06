<?php
$wordType = $config->fileTypes->$type;

if ($config->fileCondition->__isset($wordType)) {

	if ($config->fileCondition->$wordType->__isset('maxSize')) {
		$maxSize = $config->fileCondition->$wordType->maxSize;
	}
	if ($config->fileCondition->$wordType->__isset('onlyImgs') && $config->fileCondition->$wordType->onlyImgs) {
		$exts = $config->files->allowed->imgExt->arrayize();
	} elseif ($config->fileCondition->$wordType->__isset('fileExt') && $config->fileCondition->$wordType->fileExt) {
		$exts = $config->fileCondition->$wordType->fileExt->arrayize();
	}
	if ($config->fileCondition->$wordType->__isset('onlyFiles') && $config->fileCondition->$wordType->onlyFiles) {
		$exts = $config->files->allowed->fileExt->arrayize();
	}

	$multiple = false;
	if ($config->fileCondition->$wordType->__isset('multiple')) {
		$multiple = $config->fileCondition->$wordType->multiple;
	}
}
if (empty($maxSize)) {
	$maxSize = $config->files->allowed->maxSize;
}
if (empty($exts)) {
	$exts = array_merge($config->files->allowed->imgExt->arrayize(), $config->files->allowed->fileExt->arrayize());
}
$exts = implode(',', $exts);

?>

<div
	class="file-uploader settings-btn <?=(!isset($disableContentClass)) ? 'contentFileUploader' : '';?> unInitialized <?=isset($class) ? $class : '';?> <?=(isset($initFunction)) ? $initFunction : '';?>"
	action="<?= Request::generateUri('uploader', 'files', array('upload', $type, (!empty($parentId) ? $parentId : false))) ?>"
	maxSize="<?= $maxSize ?>"
	type="<?= $type ?>"
	exts="<?= $exts ?>"
	data-type="<?= (isset($dataType)) ? $dataType : ''; ?>"
	style="display: none;"
	<?= $multiple ? 'multiple="true"' : '' ?>>
	<noscript>
	<p><?= t('disabled_js_fileuploader') ?></p>
	</noscript>
</div>

<?php if (isset($initFunction)) : ?>
	<script type="text/javascript">
		$(document).ready(function(){
			var func = '<?=$initFunction;?>';
			<?=$initFunction;?>.init();

			$('#'+func).on('click', function(){
				$('.'+func+' input[type="file"]').click();
			});
		});
	</script>
	<button id="<?=$initFunction;?>" style="opacity: 0; width: 0; height: 0; overflow: hidden;">Upload</button>

<?php else: ?>
	<script type="text/javascript">
		$(document).ready(function(){
			contentUploader.init();
		});
	</script>
<?php endif; ?>