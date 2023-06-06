<?// dump($job, 1); ?>
<?// dump($applicant, 1); ?>
<?// dump($from, 1); ?>
<?
if(!isset($from)) {
	$from = 'applicants';
}
?>

<div class="block-job_buttons">
	<? if($applicant->jobapplyIsInvited == JOBAPPLY_ANSWER_NULL) : ?>
		<a class="btn-blue icons i-accesswhite" href="<?= Request::generateUri('jobs', 'applicantInvite', array($job->id, $applicant->id)) . Request::getQuery('from', $from) ?>"  onclick="return box.confirm(this, true);"><span></span>Invite</a>
		<a class="btn-blue icons i-cancel" href="<?= Request::generateUri('jobs', 'applicantDeny', array($job->id, $applicant->id)) . Request::getQuery('from', $from) ?>"  onclick="return box.confirm(this, true);"><span></span>Deny</a>
	<? else: ?>
		<? if($applicant->jobapplyIsInvited == JOBAPPLY_ANSWER_APPROVE) : ?>
			<div class="job_status-active"><span class="icons i-invited"><span></span></span>invited</div>
		<? endif; ?>
		<? if($applicant->jobapplyIsInvited == JOBAPPLY_ANSWER_DENY) : ?>
			<div class="job_status-notactive"><span class="icons i-cancelbrown"><span></span></span>denied</div>
			<a class="btn-blue icons i-closewhite" href="<?= Request::generateUri('jobs', 'applicantDelete', array($job->id, $applicant->id)) . Request::getQuery('from', $from) ?>"  onclick="return box.confirm(this, true);"><span></span>Delete</a>
		<? endif; ?>
	<? endif; ?>
</div>