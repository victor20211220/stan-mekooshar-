<?php

class Admin_ProfilePlans_Controller extends Controller_Admin_Template
{
	public function before()
	{
		parent::before();
		
		$this->view->active = 'profileplans';
	}

	public function actionIndex()
	{
		$this->view->crumbs('Manage profile plans');
		$this->view->content = $content = new View('admin/profileplans/list');

		$content->items = Model_Plans::getListPlans(CATEGORY_PLAN_PROFILE);
	}

	public function actionAdd()
	{
		return $this->actionEdit();
	}


	public function actionEdit($id = FALSE)
	{

		$this->view->crumbs('Manage profile plan');

		$f_ProfilePlans_Plan = new Form_ProfilePlans_Plan();

		if($id) {
			$plan = Model_Plans::getItemById($id, CATEGORY_PLAN_PROFILE);
			$f_ProfilePlans_Plan->edit($plan);
		}

		if(Request::isPost()) {
			if($f_ProfilePlans_Plan->form->validate()) {
				$values = $f_ProfilePlans_Plan->form->getValues();
				$values['countDays'] = $values['countMonth'] * 30;
				unset($values['countMonth']);

				if($id) {
					Model_Plans::update($values, $plan->id);
					$this->message('Plan information has been updated');
				} else {
					$values['category'] = CATEGORY_PLAN_PROFILE;
					Model_Plans::create($values);
					$this->message('New plan has been created');
				}
				$this->response->redirect(Request::generateUri('admin', 'profileplans'));
			}
		}

		$this->view->content = new View('admin/profileplans/form', array('form' => $f_ProfilePlans_Plan->form));
		$this->getMessages();
	}

	public function actionRemove($id)
	{
		$plan = Model_Plans::getItemById($id);
		Model_Plans::update(array(
			'isRemoved' => 1
		), $plan->id);

		$this->response->redirect(Request::generateUri('admin', 'profileplans'));
	}

//
//	public function actionOrders()
//	{
//		$this->view->crumbs('Manage orders');
//		$this->view->content = $content = new View('admin/orders/list');
//
//		$content->items = Model_Cartorders::getOrders();
//	}
//
//	public function actionOrderDetails($token)
//	{
//		$this->view
//			->crumbs('Manage orders', Request::$controller . 'orders/')
//			->crumbs('Order details');
//		$this->view->content = $content = new View('admin/orders/details');
//
//		$order = Model_Cartorders::getOrderByToken($token);
//		$content->order = $order;
//		$content->items = Model_Cartitems::getOrderItems($order->id);
//	}
//
//	public function actionPaid($orderId)
//	{
//		$order = Model_Cartorders::getOrderById($orderId);
//		Cartitems::setPaid($orderId);
//
//		$this->response->redirect(Request::$controller . 'orders/');
//	}
//
//	public function actionUnpaid($orderId)
//	{
//		$order = Model_Cartorders::getOrderById($orderId);
//		Model_Cartorders::update(array('isPaid' => 0), $orderId);
//
//		$this->response->redirect(Request::$controller . 'orders/');
//	}
//
//	public function actionProcessed($orderId)
//	{
//		$order = Model_Cartorders::getOrderById($orderId);
//		Model_Cartorders::update(array('processed' => 1), $orderId);
//
//		$this->response->redirect(Request::$controller . 'orders/');
//	}
//
//	public function actionUnprocessed($orderId)
//	{
//		$order = Model_Cartorders::getOrderById($orderId);
//		Model_Cartorders::update(array('processed' => 0), $orderId);
//
//		$this->response->redirect(Request::$controller . 'orders/');
//	}
//
//
//	public function actionRemove($orderId)
//	{
//		$order = Model_Cartorders::getOrderById($orderId);
//		Model_Cartitems::removeOrderItems($orderId);
//		Model_Cartorders::remove($order->id);
//
//		$this->response->redirect(Request::$controller . 'orders/');
//	}
}
