<?// dump($job, 1); ?>

<div class="job_view">
	<?= View::factory('parts/parts-left_big', array(
		'lefttop' => '',
		'leftmiddle' => '',
		'left' => View::factory('pages/jobs/block-job_view', array(
			'job' => $job
		)),
		'right' => View::factory('pages/jobs/rightpanel', array(
			'isManageJobsApplication' => TRUE
		))
	)) ?>
</div>