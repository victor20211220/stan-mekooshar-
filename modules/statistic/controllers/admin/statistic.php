<?php

class Admin_Statistic_Controller extends Controller_Admin_Template
{
	public function before()
	{
		parent::before();
		$this->view->script('/js/libs/ui/jquery-ui.custom.min.js');
		}

	public function actionUsers()
	{
		$this->view->active = 'statistic_users';
		$this->view->crumbs('Statistic Users');

		$filter = Request::get('filter', 'month');
		$from = Request::get('from', FALSE);

		$countUsers = Model_User::getCountRegistredUsers();

		if($from) {
			$users = Model_User::getListNewProfile($filter, $from);
		} else {
			$users = Model_User::getListNewProfileByFilter($filter);
		}

		$this->view->content = $content = new View('admin/statistic/users', array(
			'users' => $users,
			'countUsers' => $countUsers->countItem,
			'filter' => $filter,
			'from' => $from
		));
		$this->view->style('/css/template.css');
	}


	public function actionConnections()
	{
		$this->view->active = 'statistic_users_connections';
		$this->view->crumbs('Statistic Users Connections');

		$filter = Request::get('filter', 'month');
		$from = Request::get('from', FALSE);

		$countUsers = Model_User::getCountRegistredUsers();
		$countConnections = Model_Connections::getCountActiveConnections();

		if($from) {
			$connections = Model_Connections::getListNewConnectionsProfile($filter, $from);
		} else {
			$connections = Model_Connections::getListNewConnectionsByFilter($filter);
		}
//		dump($connections, 1);

		$this->view->content = $content = new View('admin/statistic/connections', array(
			'connections' => $connections,
			'countUsers' => $countUsers->countItem,
			'countConnections' => $countConnections,
			'filter' => $filter,
			'from' => $from
		));
		$this->view->style('/css/template.css');
	}

	public function actionPaidAccounts()
	{
		$this->view->active = 'statistic_paid_accounts';
		$this->view->crumbs('Statistic Paid Accounts');

		$filter = Request::get('filter', 'month');
		$from = Request::get('from', FALSE);

		$countGoldAccount = Model_User::getCountGoldAccount();
		$countPaidsAndSum = Model_Cartitems::getCountAccountPaidsAndSumAccounts();

		if($from) {
			$orders = Model_Cartitems::getListPaidProfile($filter, $from);
		} else {
			$orders = Model_Cartitems::getListPaidProfileByFilter($filter);
		}

		$this->view->content = $content = new View('admin/statistic/paid_accounts', array(
			'orders' => $orders,
			'countGoldAccount' => $countGoldAccount,
			'countPaidsAndSum' => $countPaidsAndSum,
			'filter' => $filter,
			'from' => $from
		));
		$this->view->style('/css/template.css');
	}


	public function actionPaidJobs()
	{
		$this->view->active = 'statistic_paid_jobs';
		$this->view->crumbs('Statistic Paid Jobs');

		$filter = Request::get('filter', 'month');
		$from = Request::get('from', FALSE);

		$countActiveJobs = Model_Jobs::getCountActiveJobs();
		$countJobs = Model_Jobs::getCountJobs();
		$countPaidsAndSum = Model_Cartitems::getCountAccountPaidsAndSumJobs();

		if($from) {
			$orders = Model_Cartitems::getListPaidProfile($filter, $from, 'jobs');
		} else {
			$orders = Model_Cartitems::getListPaidProfileByFilter($filter, 'jobs');
		}

		$this->view->content = $content = new View('admin/statistic/paid_jobs', array(
			'orders' => $orders,
			'countActiveJobs' => $countActiveJobs,
			'countJobs' => $countJobs,
			'countPaidsAndSum' => $countPaidsAndSum,
			'filter' => $filter,
			'from' => $from
		));
		$this->view->style('/css/template.css');
	}





}
