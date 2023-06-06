<?// dump($f_Jobs_SearchJob, 1); ?>
<?
	$countNewJobApplicant = Model_Job_Apply::getCountAllNewApplicant($user->id);
?>

<div class="block-search_filters">
	<? $f_Jobs_SearchJob->form->header(); ?>
	<div class="search_filters_btns">
		<? $f_Jobs_SearchJob->form->render('fields1'); ?>
		<? $f_Jobs_SearchJob->form->render('search'); ?>
		<a href="<?= Request::generateUri('jobs', 'newJob') ?>" class="btn-roundblue_big borderradius_2" onclick="<?= (!$isMyCompanies) ? 'box.message(\'Message\', \'You dont have company! Please create company first.\'); return false;' : null ?>" title="Post your new job">
			<span class="title-big">Post job</span><br>
			post your new job
		</a>
		<a href="<?= Request::generateUri('jobs', 'myJobs') ?>" class="btn-roundblue_big borderradius_2" title="My jobs list">
			<span class="title-big">My jobs <? if($countNewJobApplicant > 0) : ?><span class="userpanel-counter" data-count="<?= $countNewJobApplicant ?>"><?= ($countNewJobApplicant > 9) ? '+9' : $countNewJobApplicant ?></span><? endif; ?></span><br>
			jobs list
		</a>
		<a href="<?= Request::generateUri('jobs', 'applications') ?>" class="btn-roundblue_big borderradius_2" title="My applications list">
			<span class="title-big">Applications</span><br>
			applications list
		</a>
	</div>

	<div class="title-big"><button class="search_filters-btn_filter <?= (isset($_COOKIE['isOpenedFilterJobInJobs']) && $_COOKIE['isOpenedFilterJobInJobs'] == 1) ? 'active' : null ?>" onclick="return web.showHideJobsFilter(this);">Use filter to search for a job</button></div>

	<div class="search_filters-filter_box <?= (isset($_COOKIE['isOpenedFilterJobInJobs']) && $_COOKIE['isOpenedFilterJobInJobs'] == 1) ? 'active' : null ?>">
		<? $f_Jobs_SearchJob->form->render('fields2'); ?>

		<div class="search_filters-left search_filters-label">
			Industry
		</div>
		<div class="search_filters-right search_filters-label">
			Skill
		</div>

		<div class="search_filters-left">
			<div class="checkbox-control" data-id="1" data-list=".checkbox-control-select1" data-select_label="Select all" data-hidden_data="#searchjob-industries"></div>
			<? $f_Jobs_SearchJob->form->render('fields3'); ?>
			<? $f_Jobs_SearchJob->form->render('fields4'); ?>
		</div>
		<div class="search_filters-right">
			<div class="checkbox-control" data-id="2" data-list=".checkbox-control-select2" data-select_label="Select all" data-hidden_data="#searchjob-skills"></div>
			<? $f_Jobs_SearchJob->form->render('fields5'); ?>
			<? $f_Jobs_SearchJob->form->render('fields6'); ?>
		</div>

		<button class="search_filters-btn_search btn-roundblue" onclick="$(this).closest('form').find('input:submit').click(); return false;">Search</button>
	</div>

	<? $f_Jobs_SearchJob->form->footer(); ?>
</div>
