<? // dump($f_Search_FilterJob, 1) ?>
<? // dump($query, 1) ?>
<? $countries = t('countries'); ?>
<? $form = $f_Search_FilterJob->form; ?>
<? if (!isset($active_menu)) {
	$active_menu = 'people';
} ?>

<div
	class="search-filterpanel  search-filterpanel_people <?= ($active_menu == 'people' || $active_menu == 'all') ? NULL : 'is-blocked-menu' ?>">
	<div class="content-title">
		<!--		<div class="content-title-icon"><div><div></div></div></div>-->
		Search people filter
	</div>
	<?= $form->header(); ?>
	<ul class="search-menu">
<!--		<li>-->
<!--			<a href="#" class="menu-item --><?//= (isset($query['connection']) && $query['connection']) ? 'icon-down' : 'icon-next' ?><!--"-->
<!--			   onclick="return web.showHideFilterMenu(this);"><span></span>Connection</a>-->
<!--			<ul class="search-submenu --><?//= (isset($query['connection']) && $query['connection']) ? 'active' : NULL ?><!--">-->
<!--				--><?//= $form->render('connections') ?>
<!--			</ul>-->
<!--		</li>-->
		<li>
			<a href="#" class="menu-item <?= ((isset($query['regionjob']) && $query['regionjob']) || (isset($_COOKIE['searchmenu_job_region']) && $_COOKIE['searchmenu_job_region'] == 1)) ? 'icon-down' : 'icon-next' ?>"
			   onclick="return web.showHideFilterMenu(this);" data-cookie="searchmenu_job_region"><span></span>Region</a>
			<ul class="search-submenu <?= ((isset($query['regionjob']) && $query['regionjob']) || (isset($_COOKIE['searchmenu_job_region']) && $_COOKIE['searchmenu_job_region'] == 1)) ? 'active' : NULL ?>">
				<?= $form->render('region') ?>
			</ul>
		</li>
<!--		<li>-->
<!--			<a href="#" class="menu-item --><?//= (isset($query['company']) && $query['company']) ? 'icon-down' : 'icon-next' ?><!--"-->
<!--			   onclick="return web.showHideFilterMenu(this);"><span></span>Company</a>-->
<!--			<ul class="search-submenu --><?//= (isset($query['company']) && $query['company']) ? 'active' : NULL ?><!--">-->
<!--				--><?//= $form->render('company') ?>
<!--			</ul>-->
<!--		</li>-->
		<li>
			<a href="#"
			   class="menu-item <?= ((isset($query['industryjob']) && $query['industryjob']) || (isset($_COOKIE['searchmenu_job_industry']) && $_COOKIE['searchmenu_job_industry'] == 1)) ? 'icon-down' : 'icon-next' ?>"
			   onclick="return web.showHideFilterMenu(this);" data-cookie="searchmenu_job_industry"><span></span>Industry</a>
			<ul class="search-submenu <?= ((isset($query['industryjob']) && $query['industryjob']) || (isset($_COOKIE['searchmenu_job_industry']) && $_COOKIE['searchmenu_job_industry'] == 1)) ? 'active' : NULL ?>">
				<?= $form->render('industry') ?>
			</ul>
		</li>
		<li>
			<a href="#" class="menu-item <?= ((isset($query['skilljob']) && $query['skilljob']) || (isset($_COOKIE['searchmenu_job_skill']) && $_COOKIE['searchmenu_job_skill'] == 1)) ? 'icon-down' : 'icon-next' ?>"
			   onclick="return web.showHideFilterMenu(this);" data-cookie="searchmenu_job_skill"><span></span>Skills</a>
			<ul class="search-submenu <?= ((isset($query['skilljob']) && $query['skilljob']) || (isset($_COOKIE['searchmenu_job_skill']) && $_COOKIE['searchmenu_job_skill'] == 1)) ? 'active' : NULL ?>">
				<?= $form->render('skill') ?>
			</ul>
		</li>
	</ul>
	<div class="hidden">
		<?= $form->render('submit'); ?>
	</div>
	<?= $form->footer(); ?>
</div>