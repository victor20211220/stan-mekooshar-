<?// dump($profile, 1); ?>

<?
	if(is_null($profile->avaToken)) {
		$profileAva = '/resources/images/noimage_174.jpg';
	} else {
		$profileAva = Model_Files::generateUrl($profile->avaToken, 'jpg', FILE_USER_AVA, TRUE, false, 'userava_174');
	}
?>

<div class="userinfo-editfoto Avatar">
	<img class="userinfo-photo" src="<?= $profileAva ?>" alt="" title="" />
	<?= View::factory('parts/file-uploader', array(
		'parentId' => $profile->id,
		'type' => FILE_USER_AVA,
		'multiple' => FALSE
	)); ?>
	<? if(!is_null($profile->avaToken)) : ?>
	<div>
		<a onclick="return crop.open(this, event, 'default');" title="Crop photo" class="icons i-crop" href="<?= Request::generateUri('profile', 'cropAva') ?>"><span></span></a>
		<a onclick="return web.ajaxGet(this, true);" title="Delete photo" class="icons i-deletewhite" href="<?= Request::generateUri('profile', 'removeAva') ?>"><span></span></a>
	</div>
	<? endif ?>
</div>
