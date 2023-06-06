<?// dump($job, 1); ?>
<?// dump($f_Jobs_ApplyJob, 1); ?>

<div class="job_apply">
	<?= View::factory('parts/parts-left_big', array(
		'lefttop' => '',
		'leftmiddle' => '',
		'left' => View::factory('pages/jobs/block-job_apply', array(
			'job' => $job,
			'f_Jobs_ApplyJob' => $f_Jobs_ApplyJob
		)),
		'right' => View::factory('pages/jobs/rightpanel', array(
			'isManageJobsApplication' => TRUE
		))
	)) ?>
</div>