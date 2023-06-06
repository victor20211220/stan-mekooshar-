<?// dump($company, 1); ?>

<?
	if(is_null($company->coverToken)) {
		$companyCover = '/resources/images/noimage_w500.jpg';
	} else {
		$companyCover = Model_Files::generateUrl($company->coverToken, 'jpg', FILE_COMPANY_COVER, TRUE, false, 'cover_500');
	}
?>

<div class="block-companycover Avatar">
	<img class="companycover-photo" src="<?= $companyCover ?>" alt="" title="" />
	<?= View::factory('parts/file-uploader', array(
		'parentId' => $company->id,
		'type' => FILE_COMPANY_COVER,
		'multiple' => FALSE
	)); ?>
	<? if(!is_null($company->coverToken)) : ?>
		<div>
			<a onclick="return crop.open(this, event, 'banner');" title="Crop cover" class="icons i-crop" href="<?= Request::generateUri('companies', 'cropCover', $company->id) ?>"><span></span></a>
			<a onclick="return web.ajaxGet(this, true);" title="Delete photo" class="icons i-deletewhite" href="<?= Request::generateUri('companies', 'removeCover', $company->id) ?>"><span></span></a>
		</div>
	<? endif ?>
</div>
