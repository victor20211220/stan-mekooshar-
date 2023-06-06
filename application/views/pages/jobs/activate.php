<?// dump($job, 1); ?>
<?// dump($content, 1); ?>

<div class="job_activate">
	<?= View::factory('parts/parts-left_big', array(
		'lefttop' => '',
		'leftmiddle' => '',
		'left' => View::factory('pages/jobs/block-job_activate', array(
			'content' => $content,
			'job' => $job
		)),
		'right' => View::factory('pages/jobs/rightpanel', array(
			'isManageJobsApplication' => TRUE
		))
	)) ?>
</div>