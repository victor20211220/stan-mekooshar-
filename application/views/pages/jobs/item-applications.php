<?// dump($job, 1); ?>

<li class="" data-id="job_<?= $job->id ?>">
	<div>
		<?= View::factory('parts/companiesava-more', array(
			'company' => $job,
			'avasize' => 'avasize_52',
			'isCompanyIndustry' => TRUE,
			'otherInfo' => $job->title
		)) ?>
	</div>
	<div>
		<? if($job->jobapplyIsInvited == JOBAPPLY_ANSWER_APPROVE): ?>
			<span class="job_status-invited">Invited</span>
		<? elseif($job->jobapplyIsInvited == JOBAPPLY_ANSWER_DENY): ?>
			<span class="job_status-denied">Denied</span>
		<? elseif(strtotime($job->expiredDate) < time() && $job->jobapplyIsInvited == JOBAPPLY_ANSWER_NULL) : ?>
			<span class="job_status-closed">Job closed</span>
		<? elseif(strtotime($job->expiredDate) > time() && $job->jobapplyIsInvited == JOBAPPLY_ANSWER_NULL): ?>
			<span class="job_status-under_review">Under review</span>
		<? endif; ?>

		<? if($job->isRemoved == 1) : ?>
			<span class="job_status-deleted">Job deleted</span>
		<? endif ?>
	</div>
	<div>
		<?= View::factory('pages/jobs/block-job_buttons', array(
			'job' => $job,
			'isViewBtn' => TRUE,
			'from' => 'applications'
		)); ?>
	</div>
</li>
