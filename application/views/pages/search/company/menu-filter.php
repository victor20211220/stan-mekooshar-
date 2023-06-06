<? // dump($f_Search_FilterCompany, 1) ?>
<? // dump($query, 1) ?>
<? $form = $f_Search_FilterCompany->form; ?>
<? if (!isset($active_menu)) {
	$active_menu = 'company';
} ?>

<div
	class="search-filterpanel search-filterpanel_company <?= ($active_menu == 'company' || $active_menu == 'all') ? null : 'is-blocked-menu' ?>">
	<div class="content-title">
		<!--		<div class="content-title-icon"><div><div></div></div></div>-->
		Search company filter
	</div>
	<?= $form->header(); ?>
	<ul class="search-menu">
		<li>
			<a href="#"
				class="menu-item <?= ((isset($query['industrycompany']) && $query['industrycompany']) || (isset($_COOKIE['searchmenu_company_industry']) && $_COOKIE['searchmenu_company_industry'] == 1)) ? 'icon-down' : 'icon-next' ?>"
				onclick="return web.showHideFilterMenu(this);" data-cookie="searchmenu_company_industry"><span></span>Industry</a>
			<ul class="search-submenu <?= ((isset($query['industrycompany']) && $query['industrycompany']) || (isset($_COOKIE['searchmenu_company_industry']) && $_COOKIE['searchmenu_company_industry'] == 1)) ? 'active' : null ?>">
				<?= $form->render('industry') ?>
			</ul>
		</li>
		<li>
			<a href="#"
				class="menu-item <?= ((isset($query['typecompany']) && $query['typecompany']) || (isset($_COOKIE['searchmenu_company_type']) && $_COOKIE['searchmenu_company_type'] == 1)) ? 'icon-down' : 'icon-next' ?>"
				onclick="return web.showHideFilterMenu(this);" data-cookie="searchmenu_company_type"><span></span>Type</a>
			<ul class="search-submenu <?= ((isset($query['typecompany']) && $query['typecompany']) || (isset($_COOKIE['searchmenu_company_type']) && $_COOKIE['searchmenu_company_type'] == 1)) ? 'active' : null ?>">
				<?= $form->render('type') ?>
			</ul>
		</li>
		<li>
			<a href="#"
				class="menu-item <?= ((isset($query['employer']) && $query['employer']) || (isset($_COOKIE['searchmenu_company_employee']) && $_COOKIE['searchmenu_company_employee'] == 1)) ? 'icon-down' : 'icon-next' ?>"
				onclick="return web.showHideFilterMenu(this);" data-cookie="searchmenu_company_employee"><span></span>Employee</a>
			<ul class="search-submenu <?= ((isset($query['employer']) && $query['employer']) || (isset($_COOKIE['searchmenu_company_employee']) && $_COOKIE['searchmenu_company_employee'] == 1)) ? 'active' : null ?>">
				<?= $form->render('employer') ?>
			</ul>
		</li>
	</ul>
	<div class="hidden">
		<?= $form->render('submit'); ?>
	</div>
	<?= $form->footer(); ?>
</div>