<?// dump($jobs, 1); ?>

<div class="myjobs">
	<?= View::factory('parts/parts-left_big', array(
		'lefttop' => '',
		'leftmiddle' => '',
		'left' => View::factory('pages/jobs/block-my_jobs', array(
			'jobs' => $jobs
		)),
		'right' => View::factory('pages/jobs/rightpanel', array(
			'isManageJobsApplication' => TRUE
		))
	)) ?>
</div>