<?// dump($orders, 1); ?>
<?// dump($countGoldAccount, 1); ?>
<?// dump($countPaidsAndSum, 1); ?>

<div class="content-header">
	<div class="back_button">
		<a class="btn btn-left" title="Go back" href="/admin/">Back</a>
	</div>
	<? if(!isset($title) || !$title) {
		$title = (!empty($crumbs)) ? array_pop($crumbs) : false; $title = ($title) ? $title[0] : 'Dashboard';
	} ?>
	
	<h1><?=Text::short($title) ?></h1>
</div>



<div class="content-data">
    <div class="content-inner">
	<div class="content-data-inner">

		<div class="toppanel-statistic">
<!--			<div>Total Count registred users: --><?//= $countUsers ?><!--</div>-->
			<div><b>Count gold users: </b><?= $countGoldAccount ?></div>
			<div><b>Count paids: </b><?= $countPaidsAndSum->countItems ?></div>
			<div><b>Total Sum: </b>$<?= $countPaidsAndSum->countSum ?></div>
		</div>
		<br>
		<div>
			<? unset($_GET['from']); ?>
			<a class="btn-roundblue <?= (Request::get('filter', false) == 'days') ? 'active' : null ?>" href="<?= Request::generateUri('admin', 'statistic', 'paidAccounts') . Request::getQuery('filter', 'days') ?>">Days</a>
			<a class="btn-roundblue <?= (Request::get('filter', false) == 'week') ? 'active' : null ?>" href="<?= Request::generateUri('admin', 'statistic', 'paidAccounts') . Request::getQuery('filter', 'week') ?>">Week</a>
			<a class="btn-roundblue <?= (Request::get('filter', 'month') == 'month') ? 'active' : null ?>" href="<?= Request::generateUri('admin', 'statistic', 'paidAccounts') . Request::getQuery('filter', 'month') ?>">Month</a>
		</div>
		<br>
		<?
		if($from) :
			echo view::factory('admin/statistic/list-paid_accounts_users', array(
				'orders' => $orders,
				'filter' => $filter
			));
		else :
			switch($filter) :
				case 'days':
					echo view::factory('admin/statistic/list-paid_accounts_filter_days', array(
						'orders' => $orders,
						'filter' => $filter
					));
					break;
				case 'week':
					echo view::factory('admin/statistic/list-paid_accounts_filter_week', array(
						'orders' => $orders,
						'filter' => $filter
					));
					break;
				case 'month':
					echo view::factory('admin/statistic/list-paid_accounts_filter_month', array(
						'orders' => $orders,
						'filter' => $filter
					));
			endswitch;
		endif;
		?>

	</div>
</div>
</div>
<div class="content-footer">
	<span class="status">
	</span>
</div>