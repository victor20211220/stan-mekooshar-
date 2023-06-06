<?// dump($company, 1); ?>

<?
	if(is_null($company->avaToken)) {
		$companyAva = '/resources/images/noimage_100.jpg';
	} else {
		$companyAva = Model_Files::generateUrl($company->avaToken, 'jpg', FILE_COMPANY_AVA, TRUE, false, 'userava_100');
	}
?>

<div class="block-companyava Avatar">
	<img class="companyava-photo" src="<?= $companyAva ?>" alt="" title="" />
	<?= View::factory('parts/file-uploader', array(
		'parentId' => $company->id,
		'type' => FILE_COMPANY_AVA,
		'multiple' => FALSE
	)); ?>
	<? if(!is_null($company->avaToken)) : ?>
	<div>
		<a onclick="return crop.open(this, event, 'default');" title="Crop logo" class="icons i-crop" href="<?= Request::generateUri('companies', 'cropAva', $company->id) ?>"><span></span></a>
		<a onclick="return web.ajaxGet(this, true);" title="Delete photo" class="icons i-deletewhite" href="<?= Request::generateUri('companies', 'removeAva', $company->id) ?>"><span></span></a>
	</div>
	<? endif ?>
</div>
