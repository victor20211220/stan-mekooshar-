<?php

class Admin_Cart_Controller extends Controller_Admin_Template
{
	public function before()
	{
		parent::before();
		
		$this->view->active = 'cart';
	}

	public function actionOrders()
	{
		$this->view->crumbs('Manage orders');
		$this->view->content = $content = new View('admin/orders/list');
		
		$content->items = Model_Cartorders::getOrders();
	}

	public function actionOrderDetails($token)
	{
		$this->view
			->crumbs('Manage orders', Request::$controller . 'orders/')
			->crumbs('Order details');
		$this->view->content = $content = new View('admin/orders/details');
		
		$order = Model_Cartorders::getOrderByToken($token);
		$content->order = $order;
		$content->items = Model_Cartitems::getOrderItems($order->id);
	}

	public function actionPaid($orderId)
	{
		$order = Model_Cartorders::getOrderById($orderId);
		Cartitems::setPaid($orderId);
		
		$this->response->redirect(Request::$controller . 'orders/');
	}

	public function actionUnpaid($orderId)
	{
		$order = Model_Cartorders::getOrderById($orderId);
		Model_Cartorders::update(array('isPaid' => 0), $orderId);
		
		$this->response->redirect(Request::$controller . 'orders/');
	}

	public function actionProcessed($orderId)
	{
		$order = Model_Cartorders::getOrderById($orderId);
		Model_Cartorders::update(array('processed' => 1), $orderId);
		
		$this->response->redirect(Request::$controller . 'orders/');
	}

	public function actionUnprocessed($orderId)
	{
		$order = Model_Cartorders::getOrderById($orderId);
		Model_Cartorders::update(array('processed' => 0), $orderId);
		
		$this->response->redirect(Request::$controller . 'orders/');
	}


	public function actionRemove($orderId)
	{
		$order = Model_Cartorders::getOrderById($orderId);
		Model_Cartitems::removeOrderItems($orderId);
		Model_Cartorders::remove($order->id);
		
		$this->response->redirect(Request::$controller . 'orders/');
	}
}
