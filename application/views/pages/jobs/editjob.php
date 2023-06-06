<?// dump($f_Jobs_NewJob, 1); ?>

<div class="editjob">
	<?= View::factory('parts/parts-left_big', array(
		'lefttop' => '',
		'leftmiddle' => '',
		'left' => View::factory('pages/jobs/block-job_new', array(
			'f_Jobs_NewJob' => $f_Jobs_NewJob
		)),
		'right' => View::factory('pages/jobs/rightpanel', array(
			'isManageJobsApplication' => TRUE
		))
	)) ?>
</div>