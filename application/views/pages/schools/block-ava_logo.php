<?// dump($school, 1); ?>

<?
	if(is_null($school->avaToken)) {
		$schoolAva = '/resources/images/noimage_100.jpg';
	} else {
		$schoolAva = Model_Files::generateUrl($school->avaToken, 'jpg', FILE_SCHOOL_AVA, TRUE, false, 'userava_100');
	}
?>

<div class="block-schoolava Avatar">
	<img class="schoolava-photo" src="<?= $schoolAva ?>" alt="" title="" />
	<?= View::factory('parts/file-uploader', array(
		'parentId' => $school->id,
		'type' => FILE_SCHOOL_AVA,
		'multiple' => FALSE
	)); ?>
	<? if(!is_null($school->avaToken)) : ?>
	<div>
		<a onclick="return crop.open(this, event, 'default');" title="Crop logo" class="icons i-crop" href="<?= Request::generateUri('schools', 'cropAva', $school->id) ?>"><span></span></a>
		<a onclick="return web.ajaxGet(this, true);" title="Delete photo" class="icons i-deletewhite" href="<?= Request::generateUri('schools', 'removeAva', $school->id) ?>"><span></span></a>
	</div>
	<? endif ?>
</div>
