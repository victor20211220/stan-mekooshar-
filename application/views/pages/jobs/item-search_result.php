<?// dump($job, 1); ?>

<li class="" data-id="job_<?= $job->id ?>">
	<div>
		<?= View::factory('parts/companiesava-more', array(
			'company' => $job,
			'avasize' => 'avasize_52',
			'isCompanyIndustry' => TRUE,
			'otherInfo' => $job->title
		)) ?>
	</div>
	<div>
		<? if(strtotime($job->activateDate) > 10) : ?>
			<?= date('m-d-Y', strtotime($job->activateDate)) ?>
		<? endif; ?>
	</div>
	<div>
		<?= View::factory('pages/jobs/block-job_buttons', array(
			'job' => $job,
			'isViewBtn' => TRUE,
			'from' => 'search',
			'isShowStatus' => TRUE
		)); ?>
	</div>
</li>
