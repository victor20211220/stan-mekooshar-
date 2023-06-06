<?// dump($f_Updates_AddUpdate, 1); ?>
<?// dump($initUploader, 1); ?>

<?

if(!isset($initUploader)) {
	$initUploader = TRUE;
}

if(is_null($user->avaToken)) {
	$userAva = '/resources/images/noimage_174.jpg';
} else {
	$userAva = Model_Files::generateUrl($user->avaToken, 'jpg', FILE_USER_AVA, TRUE, false, 'userava_174');
}

?>

<div class="block-create-updates block-userinfo">
	<div class="userinfo-left">
		<img class="userinfo-photo" src="<?= $userAva ?>" alt="" title="" />
	</div><div class="userinfo-right">
		<div class="userinfo-name"><?= $user->firstName . ' ' . $user->lastName ?></div>
		<? $f_Updates_AddUpdate->form->header() ?>
		<a href="#" class="icons i-close hidden upload-cancel_load"><span></span></a>
		<? $f_Updates_AddUpdate->form->render('fields') ?>
		<div class="loader hidden">Loading...</div>
		<? $f_Updates_AddUpdate->form->render('urldata') ?>
		<div class="updates-file hidden">
			<a href="#" class="updates-file-link" target="_blank"></a>
			<a href="#" class="icons i-close upload-cancel_pdfdoc" onclick="return web.updateClear($(this).closest('form'));" ><span></span></a>
		</div>
		<div class="upload-images user-gallery hidden">
			<div class="upload-image_text">Only 1 photo can be attached</div>
			<div class="btn-prev icon-prev" onclick="web.prevUserGallery(this);"><span></span></div><ul class="uploader-list uploaderAddUpdate">
				<li class="hidden"></li>
			</ul><div class="btn-next icon-next" onclick="web.nextUserGallery(this);"><span></span></div>
		</div>
		<? $f_Updates_AddUpdate->form->render('submit') ?>
		<? $f_Updates_AddUpdate->form->footer() ?>
	</div>
</div>

<? if($initUploader === TRUE) : ?>
	<?= View::factory('parts/file-uploader', array(
		'initFunction' => 'contentUploaderList',
		'parentId' => 0,
		'type' => FILE_UPDATES,
		'multiple' => FALSE
	)); ?>

	<script type="text/javascript">
		editorGallery.addImg($('.uploader-list > .hidden'));
		$('#addupdate-text').on('keyup input',function(){
			web.updateAddUrl(this);
		});
		setTimeout(function(){
			$('.qq-uploader input[type="file"]').removeAttr('multiple');
		}, 400);
	</script>
<? endif; ?>