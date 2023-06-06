<?// dump($job, 1); ?>

<li data-id="job_<?= $job->id ?>">
	<div><?= $job->companyName ?></div>
	<div class="my_jobs-title">
		<a href="<?= Request::generateUri('jobs', 'job', $job->id) ?>">
			<?= $job->title ?>
		</a>
	</div>
	<div>
		<? if($job->expiredDate == 0) : ?>
			<span class="job_status-notactive">NOT ACTIVE</span>
		<? elseif(strtotime($job->expiredDate) < time()) : ?>
			<span class="job_status-notactive">EXPIRED</span>
		<? else : ?>
			<span class="job_status-active">ACTIVE</span>
			<div class="job_status-laftdate"><?= round((strtotime($job->expiredDate) - time()) / (60*60*24)) ?> days left</div>
		<? endif ?>
	</div>
	<div class="my_jobs-applicants">
		<? if($job->countApplicants == 0) : ?>
			<span class="job_status-notactive">NOT APPLICANTS</span>
		<? else : ?>
			<a href="<?= Request::generateUri('jobs', 'applicants', $job->id) ?>">
				<span><?= $job->countApplicants ?> applicants</span>
			</a>
			<? if($job->countNewApplicants != 0) : ?>
				<div class="job_status-active">
					<a href="<?= Request::generateUri('jobs', 'applicants', $job->id) ?>">
						<span class="cointer" data-count="<?= $job->countNewApplicants ?>"><?= ($job->countNewApplicants > 9) ? '+9' : $job->countNewApplicants ?></span> new
					</a>
				</div>
			<? endif; ?>
		<? endif; ?>
	</div>
	<div>
		<?= View::factory('pages/jobs/block-job_buttons', array(
			'job' => $job
		)) ?>
	</div>
</li>