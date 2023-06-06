<? // dump($f_Search_FilterGroup, 1) ?>
<? // dump($query, 1) ?>
<? $form = $f_Search_FilterGroup->form; ?>
<? if (!isset($active_menu)) {
	$active_menu = 'group';
} ?>

<div
	class="search-filterpanel  search-filterpanel_group <?= ($active_menu == 'group' || $active_menu == 'all') ? null : 'is-blocked-menu' ?>">
	<div class="content-title">
		<!--		<div class="content-title-icon"><div><div></div></div></div>-->
		Search group filter
	</div>
	<?= $form->header(); ?>
	<ul class="search-menu">
		<li>
			<a href="#"
				class="menu-item <?= ((isset($query['access']) && $query['access']) || (isset($_COOKIE['searchmenu_group_access']) && $_COOKIE['searchmenu_group_access'] == 1)) ? 'icon-down' : 'icon-next' ?>"
				onclick="return web.showHideFilterMenu(this);" data-cookie="searchmenu_group_access"><span></span>Group access</a>
			<ul class="search-submenu <?= ((isset($query['access']) && $query['access']) || (isset($_COOKIE['searchmenu_group_access']) && $_COOKIE['searchmenu_group_access'] == 1)) ? 'active' : null ?>">
				<?= $form->render('access') ?>
			</ul>
		</li>
	</ul>
	<div class="hidden">
		<?= $form->render('submit'); ?>
	</div>
	<?= $form->footer(); ?>
</div>