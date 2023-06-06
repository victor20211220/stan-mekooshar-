<? // dump($f_Search_FilterPeople, 1) ?>
<? // dump($query, 1) ?>
<? $countries = t('countries'); ?>
<? $form = $f_Search_FilterPeople->form; ?>
<? if (!isset($active_menu)) {
	$active_menu = 'people';
} ?>
<?//= (isset($_COOKIE['isOpenedFilterJobInJobs']) && $_COOKIE['isOpenedFilterJobInJobs'] == 1) ? 'active' : null ?>
<div
	class="search-filterpanel  search-filterpanel_people <?= ($active_menu == 'people' || $active_menu == 'all') ? NULL : 'is-blocked-menu' ?>">
	<div class="content-title">
		<!--		<div class="content-title-icon"><div><div></div></div></div>-->
		Search people filter
	</div>
	<?= $form->header(); ?>
	<ul class="search-menu">
		<li>
			<a href="#" class="menu-item <?= ((isset($query['connection']) && $query['connection']) || (isset($_COOKIE['searchmenu_people_connection']) && $_COOKIE['searchmenu_people_connection'] == 1)) ? 'icon-down' : 'icon-next' ?>"
			   onclick="return web.showHideFilterMenu(this);" data-cookie="searchmenu_people_connection"><span></span>Connection</a>
			<ul class="search-submenu <?= ((isset($query['connection']) && $query['connection']) || (isset($_COOKIE['searchmenu_people_connection']) && $_COOKIE['searchmenu_people_connection'] == 1)) ? 'active' : NULL ?>">
				<?= $form->render('connections') ?>
			</ul>
		</li>
		<li>
			<a href="#" class="menu-item <?= ((isset($query['region']) && $query['region']) || (isset($_COOKIE['searchmenu_people_region']) && $_COOKIE['searchmenu_people_region'] == 1)) ? 'icon-down' : 'icon-next' ?>"
			   onclick="return web.showHideFilterMenu(this);" data-cookie="searchmenu_people_region"><span></span>Region</a>
			<ul class="search-submenu <?= ((isset($query['region']) && $query['region']) || (isset($_COOKIE['searchmenu_people_region']) && $_COOKIE['searchmenu_people_region'] == 1)) ? 'active' : NULL ?>">
				<?= $form->render('region') ?>
			</ul>
		</li>
		<li>
			<a href="#" class="menu-item <?= ((isset($query['company']) && $query['company']) || (isset($_COOKIE['searchmenu_people_company']) && $_COOKIE['searchmenu_people_company'] == 1)) ? 'icon-down' : 'icon-next' ?>"
			   onclick="return web.showHideFilterMenu(this);" data-cookie="searchmenu_people_company"><span></span>Company</a>
			<ul class="search-submenu <?= ((isset($query['company']) && $query['company']) || (isset($_COOKIE['searchmenu_people_company']) && $_COOKIE['searchmenu_people_company'] == 1)) ? 'active' : NULL ?>">
				<?= $form->render('company') ?>
			</ul>
		</li>
		<li>
			<a href="#"
			   class="menu-item <?= ((isset($query['industrypeople']) && $query['industrypeople']) || (isset($_COOKIE['searchmenu_people_industry']) && $_COOKIE['searchmenu_people_industry'] == 1)) ? 'icon-down' : 'icon-next' ?>"
			   onclick="return web.showHideFilterMenu(this);" data-cookie="searchmenu_people_industry"><span></span>Industry</a>
			<ul class="search-submenu <?= ((isset($query['industrypeople']) && $query['industrypeople']) || (isset($_COOKIE['searchmenu_people_industry']) && $_COOKIE['searchmenu_people_industry'] == 1)) ? 'active' : NULL ?>">
				<?= $form->render('industry') ?>
			</ul>
		</li>
		<li>
			<a href="#" class="menu-item <?= ((isset($query['school']) && $query['school']) || (isset($_COOKIE['searchmenu_people_school']) && $_COOKIE['searchmenu_people_school'] == 1)) ? 'icon-down' : 'icon-next' ?>"
			   onclick="return web.showHideFilterMenu(this);" data-cookie="searchmenu_people_school"><span></span>School</a>
			<ul class="search-submenu <?= ((isset($query['school']) && $query['school']) || (isset($_COOKIE['searchmenu_people_school']) && $_COOKIE['searchmenu_people_school'] == 1)) ? 'active' : NULL ?>">
				<?= $form->render('school') ?>
			</ul>
		</li>
		<li>
			<a href="#" class="menu-item <?= ((isset($query['skills']) && $query['skills']) || (isset($_COOKIE['searchmenu_people_skills']) && $_COOKIE['searchmenu_people_skills'] == 1)) ? 'icon-down' : 'icon-next' ?>"
				onclick="return web.showHideFilterMenu(this);" data-cookie="searchmenu_people_skills"><span></span>Skills</a>
			<ul class="search-submenu <?= ((isset($query['skills']) && $query['skills']) || (isset($_COOKIE['searchmenu_people_skills']) && $_COOKIE['searchmenu_people_skills'] == 1)) ? 'active' : NULL ?>">
				<?= $form->render('skill') ?>
			</ul>
		</li>
	</ul>
	<div class="hidden">
		<?= $form->render('submit'); ?>
	</div>
	<?= $form->footer(); ?>
</div>