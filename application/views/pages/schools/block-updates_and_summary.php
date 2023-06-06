<?// dump($school, 1); ?>
<?// dump($f_Updates_AddUpdate, 1); ?>
<?// dump($timelinesCompany, 1); ?>
<?// dump($timelinesSchool, 1); ?>
<?
$types = t('school_type');
//$employers = t('company_number_of_employer');
//$industries = t('industries');

?>

<div class="block-updates_and_summary">
	<div class="title-big">Summary</div>
	<div class="updates_and_summary-summary"><?= nl2br(Html::chars($school->description)) ?></div>
	<ul class="updates_and_summary-company_info">
		<? if(!empty($school->url)) : ?>
			<li>
				<div>Website link</div>
				<div>
					<a href="<?= $school->url ?>" title="Website link"><?= $school->url ?></a>
				</div>
			</li>
		<? endif; ?>


		<? if(!empty($school->yearFounded)) : ?>
			<li>
				<div>Year founded</div>
				<div><?= $school->yearFounded ?></div>
			</li>
		<? endif; ?>

		<? if(!empty($school->email2)) : ?>
			<li>
				<div>Email address</div>
				<div><?= $school->email2 ?></div>
			</li>
		<? endif; ?>

		<? if(!empty($school->phone1)) : ?>
			<li>
				<div>Phone 1</div>
				<div><?= $school->phone1 ?></div>
			</li>
		<? endif; ?>

		<? if(!empty($school->phone2)) : ?>
			<li>
				<div>Phone 2</div>
				<div><?= $school->phone2 ?></div>
			</li>
		<? endif; ?>

		<? if(!empty($school->address)) : ?>
			<li>
				<div>Address</div>
				<div><?= $school->address ?></div>
			</li>
		<? endif; ?>

		<? if(!empty($school->type) && isset($types[$school->type])) : ?>
			<li>
				<div>Type</div>
				<div><?= $types[$school->type] ?></div>
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
		<? if(!empty($timelinesSchool['data'])) : ?>
			<ul class="list-items">
				<li class="hidden"></li>
				<? foreach($timelinesSchool['data'] as $timeline) : ?>
					<?= View::factory('pages/updates/item-update', array(
						'timeline' => $timeline,
						'isUsernameLink' => TRUE
					)) ?>
				<? endforeach; ?>
				<li>
					<?= View::factory('common/default-pages', array(
							'isBand' => TRUE,
							'autoScroll' => TRUE
						) + $timelinesSchool['paginator']) ?>
				</li>
			</ul>
		<? else : ?>
			<div class="list-item-empty">
				No updates
			</div>
		<? endif; ?>

	</div>

</div>

