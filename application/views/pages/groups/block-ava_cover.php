<?// dump($group, 1); ?>

<?
	if(is_null($group->coverToken)) {
		$groupCover = '/resources/images/noimage_w580.jpg';
	} else {
		$groupCover = Model_Files::generateUrl($group->coverToken, 'jpg', FILE_GROUP_COVER, TRUE, false, 'cover_580');
	}
?>

<div class="block-groupcover Avatar">
	<img class="groupcover-photo" src="<?= $groupCover ?>" alt="" title="" />
	<?= View::factory('parts/file-uploader', array(
		'parentId' => $group->id,
		'type' => FILE_GROUP_COVER,
		'multiple' => FALSE
	)); ?>
	<? if(!is_null($group->coverToken)) : ?>
	<div>
		<a onclick="return crop.open(this, event, 'banner');" title="Crop cover" class="icons i-crop" href="<?= Request::generateUri('groups', 'cropCover', $group->id) ?>"><span></span></a>
		<a onclick="return web.ajaxGet(this, true);" title="Delete cover" class="icons i-deletewhite" href="<?= Request::generateUri('groups', 'removeCover', $group->id) ?>"><span></span></a>
	</div>
	<? endif ?>
</div>
