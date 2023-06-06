<?// dump($job, 1); ?>
<?// dump($f_Jobs_ApplyJob, 1); ?>

<div class="block-job_apply">
	<div class="block-job_view">
		<div class="job_view-company_short_info">
			<?
			$industry = t('industries.' . $job->industry);

			$location = array();
			$location[] = t('countries.' . $job->country);
			if($job->country == 'US') {
				$location[] = t('states.' . $job->state);
			} else {
				$location[] = $job->state;
			}
			$location[] = $job->city;
			?>
			<?= View::factory('parts/companiesava-more', array(
				'company' => $job,
				'avasize' => 'avasize_52',
				'isCompanyNameLink' => TRUE,
				'keyId' => 'companyId',
				'otherInfo' => '<div>' . $industry . '</div><div>' . implode(', ', $location) . '</div>'
			))?>
			<div><b>Employment: </b><?= t('employment.' . $job->employment) ?></div>
			<div><b>Job title: </b><?= $job->title ?></div>
		</div>
	</div>


	<? $f_Jobs_ApplyJob->form->header() ?>
	<a href="#" class="icons i-close hidden upload-cancel_load"><span></span></a>
	<? $f_Jobs_ApplyJob->form->render('fields1') ?>
	<div class="loader hidden">Loading...</div>

	<div class="updates-file hidden">
		<a href="#" class="updates-file-link" target="_blank"></a>
		<a href="#" class="icons i-close upload-cancel_pdfdoc" onclick="return web.updateClear($(this).closest('form'));" ><span></span></a>
	</div>
	<div>
		<ul class="uploader-list uploaderAddUpdate">
			<li class="hidden"></li>
		</ul>
	</div>
	<? $f_Jobs_ApplyJob->form->render('fields2') ?>
	<? $f_Jobs_ApplyJob->form->render('submit') ?>
	<? $f_Jobs_ApplyJob->form->footer() ?>
	<div class="hidden">
		<?= View::factory('parts/file-uploader', array(
			'initFunction' => 'contentUploaderList',
			'parentId' => 0,
			'type' => FILE_JOB_APPLY,
			'multiple' => TRUE
		)); ?>
	</div>
</div>




	<script type="text/javascript">
		editorGallery.addImg($('.uploader-list > .hidden'));
//		$('#addupdate-text').keyup(function(){
//			web.updateAddUrl(this);
//		});
//		setTimeout(function(){
//			$('.qq-uploader input[type="file"]').removeAttr('multiple');
//		}, 400);
	</script>
