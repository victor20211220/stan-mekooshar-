<?
	$countNewJobApplicant = Model_Job_Apply::getCountAllNewApplicant($user->id);
?>

<div class="block-manage_jobs_applications">
	<div class="content-title">
<!--		<div class="content-title-icon"><div><div></div></div></div>-->
		<div>Manage jobs & applications</div>
	</div>


	<a href="<?= Request::generateUri('jobs', 'newJob') ?>" class="btn-roundblue_big borderradius_2 btn-big <?= (strtolower(Request::$action) == 'newjob') ? 'active' : null ?>" onclick="<?= (!$isMyCompanies) ? 'box.message(\'Message\', \'You dont have company! Please create company first.\'); return false;' : null ?>" title="Post your new job">
		<span class="title-big">Post job</span><br>
		post your new job
	</a>
	<a href="<?= Request::generateUri('jobs', 'myJobs') ?>" class="btn-roundblue_big borderradius_2 btn-big <?= (strtolower(Request::$action) == 'myjobs') ? 'active' : null ?>" title="My jobs list">
		<span class="title-big">My jobs <? if($countNewJobApplicant > 0) : ?><span class="userpanel-counter" data-count="<?= $countNewJobApplicant ?>"><?= ($countNewJobApplicant > 9) ? '+9' : $countNewJobApplicant ?></span><? endif; ?></span><br>
		jobs list
	</a>
	<a href="<?= Request::generateUri('jobs', 'applications') ?>" class="btn-roundblue_big borderradius_2 btn-big <?= (strtolower(Request::$action) == 'applications') ? 'active' : null ?>" title="My applications list">
		<span class="title-big">Applications</span><br>
		applications list
	</a>
</div>

