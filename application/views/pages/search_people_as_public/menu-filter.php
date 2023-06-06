<? // dump($f_Search_FilterPeople, 1) ?>
<? // dump($query, 1) ?>
<? $countries = t('countries'); ?>
<? $form = $f_Search_FilterPeople->form; ?>

<div class="search-filterpanel public_search">
	<div class="content-title">
		<!--		<div class="content-title-icon"><div><div></div></div></div>-->
		Search filter
	</div>
	<?= $form->header(); ?>
	<ul class="search-menu">
		<li>
			<a href="#"
				class="menu-item <?= ((isset($query['region']) && $query['region']) || (isset($_COOKIE['searchmenu_public_region']) && $_COOKIE['searchmenu_public_region'] == 1)) ? 'icon-down' : 'icon-next' ?>"
				onclick="return web.showHideFilterMenu(this);" data-cookie="searchmenu_public_region"><span></span>Region</a>
			<ul class="search-submenu <?= ((isset($query['region']) && $query['region']) || (isset($_COOKIE['searchmenu_public_region']) && $_COOKIE['searchmenu_public_region'] == 1)) ? 'active' : null ?>">
				<?= $form->render('region') ?>
			</ul>
		</li>
		<li>
			<a href="#"
				class="menu-item <?= ((isset($query['company']) && $query['company']) || (isset($_COOKIE['searchmenu_public_company']) && $_COOKIE['searchmenu_public_company'] == 1)) ? 'icon-down' : 'icon-next' ?>"
				onclick="return web.showHideFilterMenu(this);" data-cookie="searchmenu_public_company"><span></span>Company</a>
			<ul class="search-submenu <?= ((isset($query['company']) && $query['company']) || (isset($_COOKIE['searchmenu_public_company']) && $_COOKIE['searchmenu_public_company'] == 1)) ? 'active' : null ?>">
				<?= $form->render('company') ?>
			</ul>
		</li>
		<li>
			<a href="#"
				class="menu-item <?= ((isset($query['industrypeople']) && $query['industrypeople']) || (isset($_COOKIE['searchmenu_public_industry']) && $_COOKIE['searchmenu_public_industry'] == 1)) ? 'icon-down' : 'icon-next' ?>"
				onclick="return web.showHideFilterMenu(this);" data-cookie="searchmenu_public_industry"><span></span>Industry</a>
			<ul class="search-submenu <?= ((isset($query['industrypeople']) && $query['industrypeople']) || (isset($_COOKIE['searchmenu_public_industry']) && $_COOKIE['searchmenu_public_industry'] == 1)) ? 'active' : null ?>">
				<?= $form->render('industry') ?>
			</ul>
		</li>
		<li>
			<a href="#"
				class="menu-item <?= ((isset($query['school']) && $query['school']) || (isset($_COOKIE['searchmenu_public_school']) && $_COOKIE['searchmenu_public_school'] == 1)) ? 'icon-down' : 'icon-next' ?>"
				onclick="return web.showHideFilterMenu(this);" data-cookie="searchmenu_public_school"><span></span>School</a>
			<ul class="search-submenu <?= ((isset($query['school']) && $query['school']) || (isset($_COOKIE['searchmenu_public_school']) && $_COOKIE['searchmenu_public_school'] == 1)) ? 'active' : null ?>">
				<?= $form->render('school') ?>
			</ul>
		</li>
	</ul>
	<div class="hidden">
		<?= $form->render('submit'); ?>
	</div>
	<?= $form->footer(); ?>
</div>