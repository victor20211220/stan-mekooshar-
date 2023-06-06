<?// dump($school, 1); ?>

<?
	if(is_null($school->coverToken)) {
		$schoolCover = '/resources/images/noimage_w500.jpg';
	} else {
		$schoolCover = Model_Files::generateUrl($school->coverToken, 'jpg', FILE_SCHOOL_COVER, TRUE, false, 'cover_500');
	}
?>

<div class="block-schoolcover Avatar">
	<img class="schoolcover-photo" src="<?= $schoolCover ?>" alt="" title="" />
	<?= View::factory('parts/file-uploader', array(
		'parentId' => $school->id,
		'type' => FILE_SCHOOL_COVER,
		'multiple' => FALSE
	)); ?>
	<? if(!is_null($school->coverToken)) : ?>
		<div>
			<a onclick="return crop.open(this, event, 'banner');" title="Crop cover" class="icons i-crop" href="<?= Request::generateUri('schools', 'cropCover', $school->id) ?>"><span></span></a>
			<a onclick="return web.ajaxGet(this, true);" title="Delete photo" class="icons i-deletewhite" href="<?= Request::generateUri('schools', 'removeCover', $school->id) ?>"><span></span></a>
		</div>
	<? endif ?>
</div>
