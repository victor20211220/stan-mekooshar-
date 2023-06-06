<?// dump($group, 1); ?>

<?
	if(is_null($group->avaToken)) {
		$groupAva = '/resources/images/noimage_100.jpg';
	} else {
		$groupAva = Model_Files::generateUrl($group->avaToken, 'jpg', FILE_GROUP_EMBLEM, TRUE, false, 'userava_100');
	}
?>

<div class="block-groupemblem Avatar">
	<img class="groupemblem-photo" src="<?= $groupAva ?>" alt="" title="" />
	<?= View::factory('parts/file-uploader', array(
		'parentId' => $group->id,
		'type' => FILE_GROUP_EMBLEM,
		'multiple' => FALSE
	)); ?>
	<? if(!is_null($group->avaToken)) : ?>
		<div>
			<a onclick="return crop.open(this, event, 'default');" title="Crop emblem" class="icons i-crop" href="<?= Request::generateUri('groups', 'cropEmblem', $group->id) ?>"><span></span></a>
			<a onclick="return web.ajaxGet(this, true);" title="Delete emblem" class="icons i-deletewhite" href="<?= Request::generateUri('groups', 'removeEmblem', $group->id) ?>"><span></span></a>
		</div>
	<? endif ?>
</div>
