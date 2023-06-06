<?// dump($jobs, 1); ?>

<div class="jobsearch">
	<?= View::factory('parts/parts-left_big', array(
		'lefttop' => '',
		'leftmiddle' => '',
		'left' => View::factory('pages/jobs/block-search_result', array(
			'jobs' => $jobs
		)),
		'right' => View::factory('pages/jobs/rightpanel', array(
			'isManageJobsApplication' => TRUE
		))
	)) ?>
</div>