<?// dump($job, 1); ?>
<?// dump($from, 1); ?>

<?// dump($isViewBtn, 1); ?>
<?// dump($isShowStatus, 1); ?>
<?
if(!isset($from)) {
	$from = 'myJobs';
}
if(!isset($isViewBtn)) {
	$isViewBtn = FALSE;
}
if(!isset($isShowStatus)) {
	$isShowStatus = FALSE;
}
if($from != 'search') {
	unset($_GET['searchjob']);
//	$search = $_GET['searchjob'];
//} else {
//	$search = false;
}
//unset($_GET['searchjob']);
?>

<div class="block-job_buttons">
	<? if($job->user_id == $user->id) : ?>
		<? if(strtotime($job->expiredDate) > time()) : ?>
			<a class="btn-blue icons i-closejob" href="<?= Request::generateUri('jobs', 'closeJob', $job->id) . Request::getQuery('from', $from) ?>"  onclick="return box.confirm(this, true);"><span></span>close</a>
		<? endif; ?>
		<? if(strtotime($job->expiredDate) < time()) : ?>
			<a class="btn-blue icons i-closewhite" href="<?= Request::generateUri('jobs', 'deleteJob', $job->id) . Request::getQuery('from', $from)  ?>" onclick="return box.confirm(this, true);"  data-confirm="Warning! If you would like to open your vacancy again, you will need to activate it one more time!"><span></span>delete</a>
			<a class="btn-blue icons i-activate" href="<?= Request::generateUri('jobs', 'activateJob', $job->id) . Request::getQuery('from', $from)  ?>"><span></span>activate</a>
		<? endif; ?>
		<a class="btn-blue icons i-editsmallwhite" href="<?= Request::generateUri('jobs', 'editJob', $job->id) . Request::getQuery('from', $from) ?>"><span></span>edit</a>

	<? else : ?>
		<? if($isViewBtn && $job->isRemoved == 0) : ?>
			<a class="btn-blue icons i-view" href="<?= Request::generateUri('jobs', 'job', $job->id) . Request::getQuery('from', $from)  ?>"><span></span>view</a>
		<? endif ?>

		<? if(is_null($job->jobapplyUserId) || ($job->expiredDate > time())) : ?>
			<a class="btn-blue i-apply" href="<?= Request::generateUri('jobs', 'apply', $job->id) . Request::getQuery('from', $from)  ?>">apply</a>
		<? endif; ?>

		<? if(!is_null($job->jobapplyUserId) && $isShowStatus) : ?>
			<? if($job->jobapplyIsInvited == JOBAPPLY_ANSWER_APPROVE) : ?>
				<div class="job_status-active"><span class="icons i-invited"><span></span></span>invited</div>
			<? endif; ?>
			<? if($job->jobapplyIsInvited == JOBAPPLY_ANSWER_DENY) : ?>
				<div class="job_status-notactive"><span class="icons i-cancelbrown"><span></span></span>denied</div>
			<? endif; ?>
		<? endif; ?>

		<? if(($job->jobapplyIsInvited == JOBAPPLY_ANSWER_NULL) && ($job->expiredDate > CURRENT_DATETIME) && (!is_null($job->jobapplyUserId))) : ?>
			<a class="btn-blue icons i-cancel" href="<?= Request::generateUri('jobs', 'applyCancel', $job->id) . Request::getQuery('from', $from)  ?>"  onclick="return box.confirm(this, true);"><span></span>cancel</a>
		<? endif; ?>

		<? if((($job->jobapplyIsInvited == JOBAPPLY_ANSWER_DENY || $job->jobapplyIsInvited == JOBAPPLY_ANSWER_APPROVE) && ($job->expiredDate > CURRENT_DATETIME) && (!is_null($job->jobapplyUserId))) ||
				(($job->expiredDate < CURRENT_DATETIME) && (!is_null($job->jobapplyUserId)))) : ?>
			<? if($job->jobapplyIsRemovedJobApplicant == 0) : ?>
				<a class="btn-blue icons i-closewhite" href="<?= Request::generateUri('jobs', 'applyDelete', $job->id) . Request::getQuery('from', $from)  ?>"  onclick="return box.confirm(this, true);"><span></span>delete</a>
			<? endif; ?>
		<? endif; ?>

		<? if(($job->jobapplyIsInvited == JOBAPPLY_ANSWER_APPROVE) && ($job->expiredDate > CURRENT_DATETIME) && (!is_null($job->jobapplyUserId))) : ?>
		<? endif; ?>


	<? endif; ?>
</div>