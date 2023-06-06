<?php

/**
 * PM-Evaware
 *
 * @author UkieTech Corporation
 * @copyright Copyright UkieTech Corp. (http://ukietech.com/)
 * @link http://pme.myevasystem.com/
 *
 */

class Visitor
{
	private static $instance = false;

	public $item;

	public static function instance()
	{
		if(!self::$instance) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	function __construct()
	{
		$ip = self::convertIp($_SERVER['REMOTE_ADDR']);
		$browser = Request::getUserAgent('browser');

		$item = Model_Visitors::checkByIp($ip, $browser);

		if($item && strtotime(CURRENT_DATETIME) > strtotime($item->createDate . ' + 1 DAY')) {
			Model_Visitors::remove($item->id);
			$item = false;
		}

		if($item) {
			if($item->isBlocked) {
				echo 'You are banned'; exit;
			}

			if(date('G') != $item->curent_hour) {
				$item->curent_hour = date('G');
				$item->hour_visiting = 0;
			}

			$item->hour_visiting++;

			$config = Config::getInstance()->visitorsChecker->limits;
			if($item->hour_visiting >= $config->maxHourVisits || $item->bad_url >= $config->maxBadUrlVisits) {
				$item->isBlocked = 1;
				$item->createDate = CURRENT_DATETIME;

				$text = "$ip - exceeded limit requests";
				$text .= $item->hour_visiting >= $config->maxHourVisits ? ' for maximum hour visits' : ' for maximum bad url opens';

				$this->reportError($text, true);
			}
		} else {
			$item = Model_Visitors::create(array(
				'ip' => $ip,
				'browser' => $browser,
				'bad_url' => 0,
				'hour_visiting' => 1,
				'curent_hour' => date('H'),
				'createdate' => CURRENT_DATETIME
			));
		}

		$this->item = $item;

		return $this;
	}

	public function setBad()
	{
		$this->item->bad_url++;

		return $this;
	}

	public function get()
	{
		return $this->item;
	}

	public function reportError($e, $force = false)
	{
		$send = true;
		$data = false;

		if(!$force) {
			$filename = APPLICATION_PATH . '_log/data.json';

			if (file_exists($filename)) {
				$data = json_decode(file_get_contents($filename), true);

				if(CURRENT_DATE != $data['date'] ) {
					$data = false;
				} else {
					$data['count']++;
				}
			}

			if(!$data) {
				$data = array ('count' => 1, 'date' => CURRENT_DATE);
			}

			file_put_contents($filename, json_encode($data));

			$send = $data['count'] <= Config::getInstance()->visitorsChecker->limits->maxReportSent;
			if($data['count'] == Config::getInstance()->visitorsChecker->limits->maxReportSent) {
				$e = 'Used email limit for sending error reports!';
			}
		}

		if($send) {
			if(
				Config::getInstance()->host != 'tenant' &&
				(!isset($_SERVER['HTTP_FROM']) || ($_SERVER['HTTP_FROM'] != 'googlebot(at)googlebot.com' && $_SERVER['HTTP_FROM'] != 'bingbot(at)microsoft.com'))
			) {
				$mail = new Mailer('exception');
				$mail->exception = $e;
				$mail->priority(1);
				$mail->send(Config::getInstance()->devEmail);
			}
		}

		return $this;
	}

	/**
	 * Add record to table if not exist
	 *
	 * @param $ip IP
	 */
	public static function addIp($ip)
	{
		if(strpos($ip, '.') !== false) {
			$ip = self::convertIp($ip);
		}

		if(!Model_Visitorsaddress::exists('ip', $ip)) {
			Model_Visitorsaddress::create(array(
				'ip' => $ip,
				'date_created' => CURRENT_DATETIME
			));
		}
	}

	public static function convertIp($ip)
	{
		if(strpos($ip, '.') !== false) {
			if(preg_match('/(\d{1,3}).(\d{1,3}).(\d{1,3}).(\d{1,3})/', $ip, $matches)) {
				return $matches[1]*0x1000000 + $matches[2]*0x10000 + $matches[3]*0x100 + $matches[4];
			}
		} else {
			$ip_number = doubleval($ip);
			$ipArr[] = $ip_number >> 24 & 255;
			$ipArr[] = $ip_number >> 16 & 255;
			$ipArr[] = $ip_number >> 8 & 255;
			$ipArr[] = $ip_number & 255;

			return implode('.', $ipArr);
		}

		return false;
	}

	function __destruct()
	{
		if($this->item) {
			$this->item->save();
		}
	}
}