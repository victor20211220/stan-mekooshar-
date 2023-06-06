<?php
class Cartitems
{
	/**
	 * @var  array  Class instances.
	 */
	protected static $instance;

	/**
	 * @var  array  Items.
	 */
	public static $items;

	public static $delivery = false;

	public static $discount = 0;

	public static $sections = array(
		'shop' => array(
			'source'	=> 'directory',
			'isDigital'	=> false,
		),
		'jobs' => array(
			'source'	=> 'model_plans',
			'isDigital'	=> false,
		),
		'profile' => array(
			'source'	=> 'model_plans',
			'isDigital'	=> false,
		)
	);

	/**
	 * Returns an instance of Auth class by the given session id.
	 *
	 * @return  Auth
	 */
//	public static function getInstance()
//	{
//		if (null == self::$instance) {
//			self::$instance = new self();
//		}
//		return self::$instance;
//	}
//
	public static function instance()
	{
		if(!session_id()) {
			session_start();
		}

//		unset($_SESSION['cart']);
//		dump(1,1);
		if (!isset($_SESSION['cart'])) {
			$_SESSION['cart'] = array();
		}

		if (null == self::$instance) {
			self::$instance = new self();
		}

		self::$delivery = (isset($_SESSION['cartDelivery']) ? $_SESSION['cartDelivery'] : false);
		self::$discount = (isset($_SESSION['cartDiscount']) ? $_SESSION['cartDiscount'] : false);

		self::$items = self::validateItems();
		return self::$instance;
	}

	public static function validateItems($cartSection = null)
	{
		$validItems = array();
		$items = array();
		if (isset($_SESSION['cart']) ? count($_SESSION['cart']) : false) {
			foreach ($_SESSION['cart'] as $cartItem) {
				if($cartSection && ($cartSection != $cartItem['section'] || !isset(self::$sections[$cartItem['section']]))) {
					continue;
				}
				$items[] = array_merge(array('source' => self::$sections[$cartItem['section']]['source']), $cartItem);
			}
		}
		foreach ($items as $item) {
			try {
				$class = (ucfirst($item['source']) == 'Directory') ? 'Directoryclass' : ucfirst($item['source']);
				if (method_exists($class, 'validateItem')) {
					if (false !== ($result = $class::validateItem($item))) {
						$validItems[] = array_merge(array(
							'user_id' => $item['user_id'],
							'job_id' => $item['job_id'],
							'plan_id' => $item['plan_id'],
							'source' => $item['source'],
							'quantity' => $item['quantity'],
							'token' => $item['token']), $result);
					}
				}
			} catch (ReflectionException $e) {
				continue;
			}
		}

		return $validItems;
	}

	public function isDigital()
	{
		$isDigital = true;
		if (count(self::$items)) {
			foreach (self::$items as $item) {
				$isDigital &= self::$sections[$item['section']]['isDigital'];
			}
		}
		return $isDigital;
	}

	public function getItems()
	{
		return self::$items;
	}

	public function delivery()
	{
		return self::$delivery;
	}

	public function getAmount()
	{
		$result = 0;

		foreach (self::$items as $item) {
			$result += (round(((float)$item['price'] * (int)(isset($item['quantity']) ? $item['quantity'] : 1)) * 100) / 100);
		}
//                if(is_array($discount)) {
//                    $result = ($discount['type'] == 'percentage')?($result - $result*$discount['price']/100):($result - $discount['price']);
//                }

		if (self::$delivery) {
			$result = $result + self::$delivery;
		}
		if ($result <= 0) {
			$result = .01;
		}

		return $result;
	}

	public static function setPaid($orderId)
	{
		$order = Model_Cartorders::getOrderById($orderId);

		if ($order->isPaid == 0) {
			Model_Cartorders::update(array(
				'dateTimePaid' => date('Y-m-d H:i:s'),
				'isPaid' => 1,
			), $orderId);
			$items = Model_Cartitems::getOrderItems($orderId);
			foreach ($items as $item) {
				try {
					$class = (ucfirst($item->source) == 'Directory') ? 'Directoryclass' : ucfirst($item->source);
					if (method_exists($class, 'setPaid')) {
						$class::setPaid($item);
					}
				} catch (ReflectionException $e) {
					continue;
				}
			}
			self::notifyOrderPaid($orderId);
		}
	}

	private static function notifyOrderPaid($orderId)
	{
		$order = Model_Cartorders::getOrderById($orderId);

		// send to admin
		$mail = new Mailer('cart/notifyPaidAdmin');
		$mail->order = $order;
		$mail->send(System::$global->settings['email']);

		// send to client
		$mail = new Mailer('cart/notifyPaid');
		$mail->order = $order;
		$mail->send($order->email);
	}

	public function createOrder($order)
	{
		$order['sessionId'] = session_id();

		$cnt = 0;
		while (true === Model_Cartorders::exists('token', ($token = Text::random('alphanuml', 6))) && $cnt < 7) {
			$cnt++;
		}
		$order['token'] = $token;

		$order['delivery'] = (self::$delivery && !$this->isDigital()) ? 1 : 0;
		$order['deliveryCost'] = self::$delivery;
		// set discount
//		$discount = (isset($order['order']['discount']) ? $order['order']['discount'] : 0);

		$order['amount'] = $this->getAmount();
		$order['dateTimeAdded'] = date('Y-m-d H:i:s');

		$order = Model_Cartorders::create($order);

		$values = array();
		$values['order_id'] = $order->id;

		foreach (self::$items as $item) {
			$values['user_id'] = $item['user_id'];
			$values['job_id'] = $item['job_id'];
			$values['plan_id'] = $item['plan_id'];
			$values['itemName'] = $item['name'];
			$values['source'] = $item['source'];
			$values['price'] = $item['price'];
			$values['note'] = (isset($item['note']) ? $item['note'] : null);
			$values['section'] = (isset($item['section']) ? $item['section'] : null);
			$values['quantity'] = (isset($item['quantity']) ? $item['quantity'] : 1);

			Model_Cartitems::create($values);
		}

		return $order;
	}

	public function getPaypalLink($order)
	{
		$settings = System::$global->settings;

		if ($settings['sandbox']) {
			$link = 'https://www.sandbox.paypal.com/cgi-bin/webscr?';
		} else {
			$link = 'https://www.paypal.com/cgi-bin/webscr?';
		}

		$amount = ($order->deliveryCost) ? $order->amount - $order->deliveryCost : $order->amount;

		$link .= 'cmd=_xclick&';
		$link .= 'business=' . urlencode($settings['paypalEmail']) . '&';
		$link .= 'item_name=' . urlencode('Your order from ' . $settings['title']) . '&';
		$link .= 'item_number=' . urlencode($order->token) . '&';
		if ($order->deliveryCost) {
			$link .= 'shipping=' . urlencode($order->deliveryCost) . '&';
		}
		$link .= 'amount=' . urlencode($amount) . '&';
		$link .= 'no_shipping=0&';
		$link .= 'no_note=1&';
		$link .= 'currency_code=USD&';
		$link .= 'lc=US&';
		$link .= 'rm=2&';
		$link .= 'notify_url=' . urlencode(Url::site('/commerce/ipn/'));

		return $link;
	}

//	private function getPaypalSubscriptionLink($orderId)
//	{
//		if (!($order = $this->model->getOrder($orderId))) {
//			return null;
//		}
//		if ($this->settings['sandbox']) {
//			$link = 'https://www.sandbox.paypal.com/cgi-bin/webscr?';
//		} else {
//			$link = 'https://www.paypal.com/cgi-bin/webscr?';
//		}
//		$link .= 'cmd=_xclick-subscriptions&';
//		$link .= 'business=' . urlencode($this->settings['paypalEmail']) . '&';
//		$link .= 'item_name=' . urlencode('Subsctiption from ' . $this->settings['title']) . '&';
//		$link .= 'invoice=' . urlencode($order['token']) . '&';
//		$link .= 'a3=' . urlencode($order['amount']) . '&';
//		$link .= 'p3=' . urlencode($order['subscriptionPeriod']) . '&';
//		$link .= 't3=M&';
//		$link .= 'src=1&';
//		$link .= 'no_shipping=0&';
//		$link .= 'no_note=1&';
//		$link .= 'currency_code=USD&';
//		$link .= 'lc=US&';
//		$link .= 'rm=2&';
//		$link .= 'notify_url=' . urlencode(Url::site('/commerce/ipn/'));
//		return $link;
//	}

	public function callbackModules($orderId)
	{
		$order = $this->model->getOrder($orderId);
		if ($order['isPaid']) {
			$items = $this->model->getOrderItems($orderId);
			foreach ($items as $source => $item) {
				try {
					$class = new ReflectionClass(ucfirst($item['source']) == 'Directory' ? 'DirectoryClass' : ucfirst($item['source']));
					$controller = $class->newInstance();
					try {
						$class->getMethod('getPaid')->invokeArgs($controller, $items);
					} catch (ReflectionException $e) {
					}
				} catch (ReflectionException $e) {
					throw new NotFoundException(ucfirst($e));
				}
			}
		}
	}
}