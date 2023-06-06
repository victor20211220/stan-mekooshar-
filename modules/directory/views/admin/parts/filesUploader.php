<?
//$wordType = $config->fileTypes->$type;
//if($config->fileCondition->__isset($wordType))
//{
//	if($config->fileCondition->$wordType->__isset('maxSize')) {
//		$maxSize = $config->fileCondition->$wordType->maxSize;
//	}
//
//	if($config->fileCondition->$wordType->__isset('onlyImgs') && $config->fileCondition->$wordType->onlyImgs) {
//		$exts = $config->files->allowed->imgExt->arrayize();
//	}
//}
//if( empty ($maxSize) ) {
//	$maxSize = $config->files->allowed->maxSize;
//}
//if(empty ($exts)) {
//	$exts = array_merge($config->files->allowed->imgExt->arrayize(), $config->files->allowed->fileExt->arrayize());
//}
//$exts = implode(',', $exts);
////admin/directory/upload/
//dump($maxSize,1);
?>

<div class="settings-btn fileUploader unInitialized"
     action="<?= Request::generateUri('admin/directory', 'upload', array($type, $section, $itemId)) ?>"
     maxSize="<?= $maxSize ?>"
     exts="<?= implode(",", (array) $ext); ?>" 
     buttonName = "+ Upload more files" >
        <noscript>
	<p>Please enable JavaScript to use file uploader.</p>
        </noscript>

</div>

<link href="/resources/css/libs/fileuploader.css" rel="stylesheet" type="text/css" media="screen">
<script type="text/javascript" src="/resources/js/libs/fileuploader.js"></script>
<script type="text/javascript" src="/resources/js/uploader.js"></script>
<script type="text/javascript" src="/resources/js/directory.js"></script>
<script type="text/javascript">
	$(document).ready(function() {
		uploader.init({
		});
	})
</script>

