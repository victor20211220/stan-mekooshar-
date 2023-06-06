<?php

class Model_Cartorders extends Model
{
	protected static $table = 'cart_orders';

	public static function getOrders($order = 'dateTimeAdded', $orderDir = 'DESC', $limit = false)
	{
		return self::getList(array(
		    'order' => $order . ' ' . $orderDir,
		), $limit ? true : false, true, $limit);
	}
	
	public static function getOrderById($id)
	{
		return new self(array(
		    'where' => array('`id`=?', $id)
		));
	}
	
	public static function getOrderByToken($token)
	{
		return new self(array(
		    'where' => array('`token`=?', $token)
		));
	}
}