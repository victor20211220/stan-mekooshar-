<?// dump($applicant, 1); ?>
<?// dump($job, 1); ?>

<li class="" data-id="applicant_<?= $applicant->id ?>">
	<div>
		<?= View::factory('parts/userava-more', array(
			'ouser' => $applicant,
			'avasize' => 'avasize_52',
			'isCustomInfo' => TRUE
		)) ?>
	</div>
	<div>
		<a class="icons i-viewblue icon-text" href="<?= Request::generateUri('jobs', 'applicant', array($job->id, $applicant->id)) ?>"><span></span>View application</a>
		<? if($applicant->jobapplyIsViewed == 0) : ?>
			<div class="job_status-active">
				(new)
			</div>
		<? endif ?>
	</div>
	<div>
		<?= View::factory('/pages/jobs/block-applicant_buttons', array(
			'job' => $job,
			'applicant' => $applicant
		)); ?>
	</div>
</li>
