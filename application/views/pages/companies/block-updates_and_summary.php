<?// dump($company, 1); ?>
<?// dump($f_Updates_AddUpdate, 1); ?>
<?// dump($timelinesCompany, 1); ?>
<?
$types = t('company_type');
$employers = t('company_number_of_employer');
$industries = t('industries');

?>

<div class="block-updates_and_summary">
	<div class="title-big">Summary</div>
	<div class="updates_and_summary-summary"><?= nl2br(Html::chars($company->description)) ?></div>
	<ul class="updates_and_summary-company_info">
		<? if(!empty($company->url)) : ?>
			<li>
				<div>Website link</div>
				<div>
					<a href="<?= $company->url ?>" title="Website link"><?= $company->url ?></a>
				</div>
			</li>
		<? endif; ?>

		<? if(!empty($company->email2)) : ?>
			<li>
				<div>Email address</div>
				<div><?= $company->email2 ?></div>
			</li>
		<? endif; ?>

		<? if(!empty($company->phone)) : ?>
			<li>
				<div>Phone</div>
				<div><?= $company->phone ?></div>
			</li>
		<? endif; ?>

		<? if(!empty($company->address)) : ?>
			<li>
				<div>Address</div>
				<div><?= $company->address ?></div>
			</li>
		<? endif; ?>

		<? if(!empty($company->type) && isset($types[$company->type])) : ?>
			<li>
				<div>Type</div>
				<div><?= $types[$company->type] ?></div>
			</li>
		<? endif; ?>

		<? if(!empty($company->industry) && isset($industries[$company->industry])) : ?>
			<li>
				<div>Industry</div>
				<div><?= $industries[$company->industry] ?></div>
			</li>
		<? endif; ?>

		<? if(!empty($company->size) && isset($employers[$company->size])) : ?>
			<li>
				<div>Employers</div>
				<div><?= $employers[$company->size] ?></div>
			</li>
		<? endif; ?>
	</ul>

	<? if($f_Updates_AddUpdate) : ?>
		<div>
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

	<div class="block-list-updates">
		<ul class="list-items">
			<li class="hidden"></li>
			<? foreach($timelinesCompany['data'] as $timeline) : ?>
				<?= View::factory('pages/updates/item-update', array(
					'timeline' => $timeline,
					'isUsernameLink' => TRUE
				)) ?>
			<? endforeach; ?>
			<li>
				<?= View::factory('common/default-pages', array(
						'isBand' => TRUE,
						'autoScroll' => TRUE
					) + $timelinesCompany['paginator']) ?>
			</li>
		</ul>

	</div>

</div>

