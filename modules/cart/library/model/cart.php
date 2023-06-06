<?php

class Model_Cart extends Model
{

	public function getOrder($orderId)
	{
		return $this->db->query('SELECT * FROM `cart_orders` WHERE `id`=?', $orderId)->fetch();
	}

	public function getOrders($order = 'dateTimeAdded', $orderDir = 'DESC')
	{
		return $this->db->query('SELECT * FROM `cart_orders` ORDER BY `' . $order . '` ' . $orderDir)->fetchAll();
	}

	public function getOrderByToken($token)
	{
		return $this->db->query('SELECT * FROM `cart_orders` WHERE `token`=?', $token)->fetch();
	}

	public function getOrderBySession($sessionId)
	{
		return $this->db->query('SELECT * FROM `cart_orders` WHERE `sessionId`=?', $sessionId)->fetchAll();
	}

	public function getItem($itemId)
	{
		return $this->db->query('SELECT * FROM `cart_items` WHERE `id`=?', $itemId)->fetch();
	}

	public function getItemByAlias($alias)
	{
		return $this->db->query('SELECT * FROM `cart_items` WHERE `alias`=?', $alias)->fetch();
	}

	public function getItems($order = 'orderId', $orderDir = 'DESC')
	{
		$items = $this->db->query('SELECT * FROM `cart_items` ORDER BY `' . $order . '` ' . $orderDir)->fetchAll();
		$result = array();
		foreach ($items as $item) {
			$result[$item['id']] = $item;
		}
		return $result;
	}

	public function getItemsByOrder()
	{
		$items = $this->db->query('SELECT * FROM `cart_items` ORDER BY `orderId` ASC')->fetchAll();
		$result = array();
		foreach ($items as $item) {
			if (!isset($result[$item['order_id']])) {
				$result[$item['order_id']] = array();
			}
			$result[$item['order_id']][] = $item;
		}
		return $result;
	}

	public function getOrderItems($orderId, $order = 'orderId', $orderDir = 'DESC')
	{

		$items = $this->db->query('SELECT * FROM `cart_items` WHERE `order_id`=? ORDER BY `' . $order . '` ' . $orderDir, $orderId)->fetchAll();
		$result = array();
		foreach ($items as $item) {
			$result[$item['id']] = $item;
		}
		return $result;
	}
}