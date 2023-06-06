<div class="element">
	<h4>Config</h4>
	<?=Html::anchor('/admin/categories/', 'Edit categories', array('class' => ($active == 'categories') ? 'active' : ''))?>
</div>
<div class="element">
	<h4>Edit pages</h4>
	<?=Html::anchor('/admin/pages/gallery/', 'Galleries', array('class' => ($active == 'gallery') ? 'active' : ''))?>
	<?=Html::anchor('/admin/pages/banners/', 'Banners', array('class' => ($active == 'banners') ? 'active' : ''))?>
	<?=Html::anchor('/admin/pages/static/', 'Static pages', array('class' => ($active == 'static') ? 'active' : ''))?>
</div>


<!--<div class="element">-->
<!--	<h4>Text pages</h4>-->
<!--	--><?//=Html::anchor('/admin/directory/browse/text/', 'Manage texts', array('class' => ($active == 'text') ? 'active' : ''))?>
<!--</div>-->
<!--<div class="element">-->
<!--	<h4>Glosarry</h4>-->
<!--	<div>--><?//=Html::anchor('/admin/directory/browse/category/', 'Manage categories', array('class' => ($active == 'category') ? 'active' : ''))?><!--</div>-->
<!--	<div>--><?//=Html::anchor('/admin/directory/browse/glossary/', 'Manage terms', array('class' => ($active == 'glossary') ? 'active' : ''))?><!--</div>-->
<!--</div>-->
<div class="element">
	<h4>Users</h4>
	<div><?=Html::anchor('/admin/users/', 'Manage users list', array('class' => ($active == 'users') ? 'active' : ''))?></div>
	<div><?=Html::anchor('/admin/users/complaints/', 'Manage complaints list', array('class' => ($active == 'complaints') ? 'active' : ''))?></div>
</div>
<div class="element">
	<h4>Shop</h4>
	<div><?=Html::anchor('/admin/jobplans/', 'Manage job plans', array('class' => ($active == 'jobplans') ? 'active' : ''))?></div>
	<div><?=Html::anchor('/admin/profileplans/', 'Manage upgrade profile plans', array('class' => ($active == 'profileplans') ? 'active' : ''))?></div>
</div>
<div class="element">
	<h4>Commerce</h4>
	<div><?=Html::anchor('/admin/cart/orders/', 'Manage orders', array('class' => ($active == 'cart') ? 'active' : ''))?></div>
</div>
<div class="element">
	<h4>Statistic</h4>
	<div><?=Html::anchor('/admin/statistic/users/', 'Registred users', array('class' => ($active == 'statistic_users') ? 'active' : ''))?></div>
	<div><?=Html::anchor('/admin/statistic/connections/', 'Users connections', array('class' => ($active == 'statistic_users_connections') ? 'active' : ''))?></div>
	<div><?=Html::anchor('/admin/statistic/paidAccounts/', 'Paid accounts', array('class' => ($active == 'statistic_paid_accounts') ? 'active' : ''))?></div>
	<div><?=Html::anchor('/admin/statistic/paidJobs/', 'Paid jobs', array('class' => ($active == 'statistic_paid_jobs') ? 'active' : ''))?></div>
</div>
<!--<div class="element">-->
<!--	<h4>Mailer</h4>-->
<!--	<div>--><?//=Html::anchor('/admin/maillist/', 'Manage mail list', array('class' => ($active == 'maillist') ? 'active' : ''))?><!--</div>-->
<!--</div>-->
