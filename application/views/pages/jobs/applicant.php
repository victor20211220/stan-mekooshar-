<?// dump($job, 1); ?>
<?// dump($applicant, 1); ?>
<?// dump($files, 1); ?>

<div class="jobapplicant">
	<?= View::factory('parts/parts-left_big', array(
		'lefttop' => '',
		'leftmiddle' => '',
		'left' => View::factory('pages/jobs/block-applicant', array(
			'job' => $job,
			'applicant' => $applicant,
			'files' => $files
		)),
		'right' => View::factory('pages/jobs/rightpanel', array(
			'isManageJobsApplication' => TRUE
		))
	)) ?>
</div>