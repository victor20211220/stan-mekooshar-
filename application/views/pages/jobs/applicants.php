<?// dump($job, 1); ?>
<?// dump($applicants, 1); ?>

<div class="jobsearch">
	<?= View::factory('parts/parts-left_big', array(
		'lefttop' => '',
		'leftmiddle' => '',
		'left' => View::factory('pages/jobs/block-applicants', array(
			'job' => $job,
			'applicants' => $applicants
		)),
		'right' => View::factory('pages/jobs/rightpanel', array(
			'isManageJobsApplication' => TRUE
		))
	)) ?>
</div>