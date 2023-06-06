<?php

class Cart_Controller extends Controller
{
	public function  before()
	{
		parent::before();

		if (!session_id()) {
			session_start();
		}
		if (!isset($_SESSION['cart'])) {
			$_SESSION['cart'] = array();
		}
	}

	public function actionAdd($section, $id)
	{
		$item = Model_Directoryitem::getItem($id, $section);

		$items =& $_SSSION['cart'];

		$find = false;
		foreach ($items as $k => $v) {
			if ($v['section'] == $section && $v['id'] == $id) {
				$find = & $items[$k];
				break;
			}
		}
		if ($find) {
			$find['quantity'] += (
			isset($_POST['quantity']) && $_POST['quantity'] > 0 ? (int)$_POST['quantity'] : 1);
		} else {
			$items[] = array(
				'id' => $item->id,
				'name' => $item->name,
				'price' => $item->price,
				'token' => $item->token,
				'text' => $item->text1,
				'quantity' => (
					isset($_POST['quantity']) && (int)$_POST['quantity'] > 0 ? (int)$_POST['quantity'] : 1),
				'section' => $section
			);
		}

		$totalQnt = 0;
		foreach ($_SESSION['cart'] as $shopItem) {
			if ($shopItem['section'] != 'shop') {
				continue;
			}
			$totalQnt += $shopItem['quantity'];
		}
		if ($totalQnt > 999) {
			$totalQnt = '999';
		}

		if (Request::$isAjax) {
			$this->autoRender = false;
			$this->response->setHeader('Content-Type', 'application/json');
			$this->response->body = json_encode(
				array(
					'success' => true,
					'total' => $totalQnt
				)
			);
		} else {
			$this->response->redirect('/commerce/checkout/');
		}
	}

	public function actionCart()
	{
		if (Request::$isAjax) {
			$this->autoRender = FALSE;
			$total = 0;
			$ajax = new View('cart/cart');

			if ($_SESSION['cart']) {
				foreach ($_SESSION['cart'] as $key => $item) {
					if ($item['section'] != 'shop') {
						continue;
					}
					$total = $total + $item['price'] * $item['quantity'];
				}
				$ajax->total = $total;
			}

			$this->response->body = $ajax;
		}
	}


	public function actionFlush()
	{
		if (Request::$isAjax) {
			$this->autoRender = false;
			$this->response->setHeader('Content-Type', 'application/json');
			$_SESSION['cart'] = array();
			$this->response->body = json_encode('Done!');
		}
	}

	public function actionCartDeleteItem()
	{
		$this->autoRender = false;
		if (Request::$isAjax) {
			$total = 0;
			$quant = 0;
			$id = (int)$_POST['id'];
			if (isset($_SESSION['cart'][$id])) {
				unset($_SESSION['cart'][$id]);
			}

			foreach ($_SESSION['cart'] as $key => $item) {
				if ($item['section'] != 'shop') {
					continue;
				}
				$total = $total + $item['price'] * $item['quantity'];
				$quant += $_SESSION['cart'][$key]['quantity'];
			}
			$response['total'] = $total;
			$response['quant'] = $quant;
		}
		$this->response->setHeader('Content-Type', 'application/json');
		$this->response->body = json_encode($response);

	}

	public function actionQuantity()
	{
		$this->autoRender = false;
		if (Request::$isAjax) {
			$total = 0;
			$quant = 0;

			$quantity = (int)$_POST['quantity'];
			$id = (int)$_POST['id'];
			if (isset($_SESSION['cart'][$id]) && $quantity > 0) {
				$_SESSION['cart'][$id]['quantity'] = $quantity;

				foreach ($_SESSION['cart'] as $key => $item) {
					if ($item['section'] != 'shop') {
						continue;
					}
					$total = $total + $item['price'] * $item['quantity'];
					$quant += $_SESSION['cart'][$key]['quantity'];
				}
				$this->response->setHeader('Content-Type', 'application/json');
				$this->response->body = json_encode(
					array(
						'total' => $total,
						'qnt' => $quant,
						'subtotal' => (int)$_SESSION['cart'][$id]['quantity']
							* (int)$_SESSION['cart'][$id]['price']
					)
				);
			} else {
				$this->response->body = false;
			}
		}

	}
}

