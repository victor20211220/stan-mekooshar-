<? // dump($f_Search_FilterSchool, 1) ?>
<? // dump($query, 1) ?>
<? $form = $f_Search_FilterSchool->form; ?>
<? if (!isset($active_menu)) {
	$active_menu = 'school';
} ?>

<div class="search-filterpanel search-filterpanel_school <?= ($active_menu == 'school' || $active_menu == 'all') ? null : 'is-blocked-menu' ?>">
	<div class="content-title">
<!--		<div class="content-title-icon">-->
<!--			<div>-->
<!--				<div></div>-->
<!--			</div>-->
<!--		</div>-->
		Search school filter
	</div>
	<?= $form->header(); ?>
	<ul class="search-menu">
		<li>
			<a href="#" class="menu-item <?= ((isset($query['typeschool']) && $query['typeschool']) || (isset($_COOKIE['searchmenu_school_type']) && $_COOKIE['searchmenu_school_type'] == 1)) ? 'icon-down' : 'icon-next' ?>"
			   onclick="return web.showHideFilterMenu(this);" data-cookie="searchmenu_school_type"><span></span>School type</a>
			<ul class="search-submenu <?= ((isset($query['typeschool']) && $query['typeschool']) || (isset($_COOKIE['searchmenu_school_type']) && $_COOKIE['searchmenu_school_type'] == 1)) ? 'active' : NULL ?>">
				<?= $form->render('type') ?>
			</ul>
		</li>
	</ul>
	<div class="hidden">
		<?= $form->render('submit'); ?>
	</div>
	<?= $form->footer(); ?>
</div>