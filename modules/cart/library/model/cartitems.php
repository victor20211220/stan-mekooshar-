<?php

class Model_Cartitems extends Model
{
	protected static $table = 'cart_items';

	public static function getItem($id)
	{
		return new self(array(
		    'where' => array('`id`=?', $id)
		));
	}
	
	public static function getItemByAlias($alias)
	{
		return new self(array(
		    'where' => array('`alias`=?', $id)
		));
	}
	
	public static function getItems($order = 'order_id', $orderDir = 'DESC')
	{
		$result = array();
		
		foreach(self::query(array(
		    'order' => $order . ' ' . $orderDir,
		)) as $item) {
			$result[$item['id']] = $item;
		};
		
		return $result;
	}
	
	public static function getOrderItems($orderId, $order = 'order_id', $orderDir = 'DESC')
	{
		$result = array();
		
		foreach(self::query(array(
			'select' => '
						cart_items.*,
						plans.name AS planName
						',
		    'where' => array('`order_id`=?', $orderId),
			'join' => array(
				array(
					'type' => 'left',
					'table' => 'plans',
					'where' => array('plans.id = cart_items.plan_id')
				)
			),
		    'order' => $order . ' ' . $orderDir,
		)) as $item) {
			$result[$item->id] = $item;
		};
		
		return $result;
	}
	
	public static function removeOrderItems($orderId)
	{
		self::remove(array('`order_id` = ?', $orderId));
	}

	public static function getListPaidProfileByFilter($filter, $type = 'profile')
	{
		$auth = Auth::getInstance();

		if($auth->allowed('dashboard')) {
			switch($filter) {
				case 'days':
					$result = self::getList(array(
						'select' => '	DATE_FORMAT(cart_orders.dateTimePaid, "%Y-%m-%d") as id,
										COUNT(DISTINCT(cart_items.id)) as countPaid,
										COUNT(DISTINCT(users.id)) as countAccounts,
										SUM(cart_items.price) as paidSum',
						'where' => array('cart_items.section = ? AND cart_orders.isPaid = 1', $type),
						'join' => array(
							array(
								'type' => 'left',
								'table' => 'cart_orders',
								'where' => array('cart_orders.id = cart_items.order_id')
							),
							array(
								'type' => 'left',
								'table' => 'users',
								'where' => array('users.id = cart_items.user_id')
							)
						),
						'group' => 'DAY(cart_orders.dateTimePaid), MONTH(cart_orders.dateTimePaid), YEAR(cart_orders.dateTimePaid) ',
						'order' => 'cart_orders.dateTimePaid DESC'
					));

					$days = array();
					$maxDate = 0;
					$minDate = 99999999;

					foreach ($result['data'] as $date => $items) {
						$date = date('Ymd', strtotime($date));

						if(!isset($days[$date])) {
							$days[$date]['paid'] = 0;
							$days[$date]['accounts'] = 0;
							$days[$date]['sum'] = 0;
						}

						$days[$date]['paid'] += $items->countPaid;
						$days[$date]['accounts'] += $items->countAccounts;
						$days[$date]['sum'] += $items->paidSum;


						if($maxDate < $date) {
							$maxDate = $date;
						}
						if($minDate > $date) {
							$minDate = $date;
						}
					}

					$i = 0;
					$tmp = 999;

					while(date('Ymd', (strtotime($minDate) + 60*60*24*$i)) < $maxDate) {
						if(!isset($days[date('Ymd', (strtotime($minDate) + 60*60*24*$i))])) {
							$days[date('Ymd', (strtotime($minDate) + 60*60*24*$i))]['paid'] = 0;
							$days[date('Ymd', (strtotime($minDate) + 60*60*24*$i))]['accounts'] = 0;
							$days[date('Ymd', (strtotime($minDate) + 60*60*24*$i))]['sum'] = 0;
						}

						$i++;


						$tmp++;
						if($tmp < 0) break;
					}


					krsort($days);
					return $days;
					break;

				case 'week':
					$result = self::getList(array(
						'select' => '	DATE_FORMAT(cart_orders.dateTimePaid, "%Y-%m-%d") as id,
										COUNT(DISTINCT(cart_items.id)) as countPaid,
										COUNT(DISTINCT(users.id)) as countAccounts,
										SUM(cart_items.price) as paidSum',
						'where' => array('cart_items.section = ? AND cart_orders.isPaid = 1', $type),
						'join' => array(
							array(
								'type' => 'left',
								'table' => 'cart_orders',
								'where' => array('cart_orders.id = cart_items.order_id')
							),
							array(
								'type' => 'left',
								'table' => 'users',
								'where' => array('users.id = cart_items.user_id')
							)
						),
						'group' => 'DAY(cart_orders.dateTimePaid), MONTH(cart_orders.dateTimePaid), YEAR(cart_orders.dateTimePaid) ',
						'order' => 'cart_orders.dateTimePaid DESC'
					));

					$weeks = array();
					$maxWeek = 0;
					$startWeek = date("U", strtotime("Next Monday"));

					foreach ($result['data'] as $date => $items) {
						$dayleft = ($startWeek - strtotime($date . ' 00:00:10')) / (60 * 60 * 24);
						$week = floor($dayleft / 7);

						if (!isset($weeks[$week])) {
							$weeks[$week]['paid'] = 0;
							$weeks[$week]['accounts'] = 0;
							$weeks[$week]['sum'] = 0;
						}
						if($maxWeek < $week) {
							$maxWeek = $week;
						}

						$weeks[$week]['paid'] += $items->countPaid;
						$weeks[$week]['accounts'] += $items->countAccounts;
						$weeks[$week]['sum'] += $items->paidSum;
					}

					for($i=0; $i < $maxWeek; $i++) {
						if(!isset($weeks[$i])) {
							$weeks[$i]['paid'] = 0;
							$weeks[$i]['accounts'] = 0;
							$weeks[$i]['sum'] = 0;
						}
					}

					ksort($weeks);
					return $weeks;
					break;

				case 'month':
					$result = self::getList(array(
						'select' => '	DATE_FORMAT(cart_orders.dateTimePaid, "%Y-%m-01") as id,
										COUNT(DISTINCT(cart_items.id)) as countPaid,
										COUNT(DISTINCT(users.id)) as countAccounts,
										SUM(cart_items.price) as paidSum',
						'where' => array('cart_items.section = ? AND cart_orders.isPaid = 1', $type),
						'join' => array(
							array(
								'type' => 'left',
								'table' => 'cart_orders',
								'where' => array('cart_orders.id = cart_items.order_id')
							),
							array(
								'type' => 'left',
								'table' => 'users',
								'where' => array('users.id = cart_items.user_id')
							)
						),
						'group' => 'MONTH(cart_orders.dateTimePaid), YEAR(cart_orders.dateTimePaid) ',
						'order' => 'cart_orders.dateTimePaid DESC'
					));


					$years = array();
					$maxDate = 0;
					$minDate = 999999;

					foreach ($result['data'] as $date => $items) {
						$yeardate = date('Ym', strtotime($date));

						if(!isset($years[$yeardate])) {
							$years[$yeardate]['paid'] = 0;
							$years[$yeardate]['accounts'] = 0;
							$years[$yeardate]['sum'] = 0;
						}

						$years[$yeardate]['paid'] += $items->countPaid;
						$years[$yeardate]['accounts'] += $items->countAccounts;
						$years[$yeardate]['sum'] += $items->paidSum;

						if($maxDate < $yeardate) {
							$maxDate = $yeardate;
						}
						if($minDate > $yeardate) {
							$minDate = $yeardate;
						}
					}

					for($i= ((int) substr($minDate, 0, 4)); $i <= ((int) substr($maxDate, 0, 4)); $i++) {
						for($j= 1; $j <= 12; $j++) {
							if(!isset($years[$i . sprintf("%02s", $j)]) && ((int)($i . sprintf("%02s", $j))) >= (int)$minDate && ((int)($i . sprintf("%02s", $j))) <= (int)$maxDate) {
								$years[$i . sprintf("%02s", $j)]['paid'] = 0;
								$years[$i . sprintf("%02s", $j)]['accounts'] = 0;
								$years[$i . sprintf("%02s", $j)]['sum'] = 0;
							}
						}
					}

					krsort($years);
					return $years;
					break;
			}

			return $result;
		}
	}

	public static function getListPaidProfile($filter, $date, $type = 'profile')
	{
		$auth = Auth::getInstance();

		if($auth->allowed('dashboard')) {
			$where = array('cart_items.section = ? AND cart_orders.isPaid = 1', $type);

			switch($filter) {
				case 'days':
					$where[0] .= ' AND cart_orders.dateTimePaid between ? and ?';
					$where[] = date('Y-m-d 00:00:00', $date);
					$where[] = date('Y-m-d 23:59:59', $date);
					break;
				case 'week':
					$where[0] .= ' AND cart_orders.dateTimePaid between ? and ?';
					$where[] = date('Y-m-d 00:00:00', $date - 60*60*24*6);
					$where[] = date('Y-m-d 23:59:59', $date);
					break;
				case 'month':
					$days = cal_days_in_month(CAL_GREGORIAN, date('m', $date), date('Y', $date));
					$where[0] .= ' AND cart_orders.dateTimePaid between ? and ?';
					$where[] = date('Y-m-d 00:00:00', $date);
					$where[] = date('Y-m-d 23:59:59', $date + 60*60*24*($days - 1));
					break;
			}

			return self::getList(array(
				'select' => '
								cart_items.*,
								users.id AS userId,
								users.firstName AS userFirstName,
								users.updateExp AS userUpdateExp,
								users.lastName AS userLastName,
								cart_orders.token AS orderToken,
								cart_orders.dateTimePaid AS orderDatePaid,
								plans.name AS planName
							',
				'where' => $where,
				'join' => array(
					array(
						'type' => 'left',
						'table' => 'cart_orders',
						'where' => array('cart_orders.id = cart_items.order_id')
					),
					array(
						'type' => 'left',
						'table' => 'users',
						'where' => array('users.id = cart_items.user_id')
					),
					array(
						'type' => 'left',
						'table' => 'plans',
						'where' => array('plans.id = cart_items.plan_id')
					)
				),
				'order' => 'id DESC'
			), false);
		}
	}

	public static function getCountAccountPaidsAndSumAccounts()
	{
		$result = new self(array(
			'select' => 'COUNT(id) as countItems,
						 SUM(price) as countSum',
			'where' => array('section = ?', 'profile')
		));
		return $result;
	}

	public static function getCountAccountPaidsAndSumJobs()
	{
		$result = new self(array(
			'select' => 'COUNT(id) as countItems,
						 SUM(price) as countSum',
			'where' => array('section = ?', 'jobs')
		));
		return $result;
	}
}