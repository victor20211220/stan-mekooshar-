<?// dump($src, 1); ?>
<?// dump($parent_id, 1); ?>
<?
	if(!$src) {
		$src = '/resources/images/noimage_94.jpg';
	}
	?>

<div class="admin_uploader Avatar">
	<img class="uploaded-photo" src="<?= $src ?>" alt="" title="" />
	<?= View::factory('parts/file-uploader', array(
		'parentId' => $parent_id,
		'type' => FILE_BANNER,
		'multiple' => FALSE
	)); ?>
	<div>
		<a onclick="return web.ajaxGet(this, true);" title="Delete image" class="icons i-deletewhite" href="<?= Request::generateUri('admin', 'removeImage') ?>"><span></span></a>
	</div>
</div>
