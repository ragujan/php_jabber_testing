<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjUtil extends pjToolkit
{
	static public function uuid()
	{
		return chr(rand(65,90)) . chr(rand(65,90)) . time();
	}
	
	static public function getReferer()
	{
		if (isset($_GET['_escaped_fragment_']))
		{
			if (isset($_SERVER['REDIRECT_URL']))
			{
				return $_SERVER['REDIRECT_URL'];
			}
		}
		
		if (isset($_SERVER['HTTP_REFERER']))
		{
			$pos = strpos($_SERVER['HTTP_REFERER'], "#");
			if ($pos !== FALSE)
			{
				return substr($_SERVER['HTTP_REFERER'], 0, $pos);
			}
			return $_SERVER['HTTP_REFERER'];
		}
	}
	
	static public function getClientIp()
	{
		if (isset($_SERVER['HTTP_CLIENT_IP']))
		{
			return $_SERVER['HTTP_CLIENT_IP'];
		} else if(isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			return $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else if(isset($_SERVER['HTTP_X_FORWARDED'])) {
			return $_SERVER['HTTP_X_FORWARDED'];
		} else if(isset($_SERVER['HTTP_FORWARDED_FOR'])) {
			return $_SERVER['HTTP_FORWARDED_FOR'];
		} else if(isset($_SERVER['HTTP_FORWARDED'])) {
			return $_SERVER['HTTP_FORWARDED'];
		} else if(isset($_SERVER['REMOTE_ADDR'])) {
			return $_SERVER['REMOTE_ADDR'];
		}

		return 'UNKNOWN';
	}
	
	static public function textToHtml($content)
	{
		$content = preg_replace('/\r\n|\n/', '<br />', $content);
		return '<html><head><title></title></head><body>'.$content.'</body></html>';
	}
	
	static public function checkFormatDate($date, $inputFormat, $outputFormat = "Y-m-d")
	{
		if (empty($date))
		{
			return FALSE;
		}
		$limiters = array('.', '-', '/');
		foreach ($limiters as $limiter)
		{
			if (strpos($inputFormat, $limiter) !== false)
			{
				$_date = explode($limiter, $date);
				$_iFormat = explode($limiter, $inputFormat);
				$_iFormat = array_flip($_iFormat);
				break;
			}
		}
		if (!isset($_iFormat) || !isset($_date) || count($_date) !== 3)
		{
			return FALSE;
		}else{
			$m = $_date[isset($_iFormat['m']) ? $_iFormat['m'] : $_iFormat['n']];
			$d = $_date[isset($_iFormat['d']) ? $_iFormat['d'] : $_iFormat['j']];
			$y = $_date[$_iFormat['Y']];
			if($m == '' || $d == '' || $y == ''){
				return FALSE;
			}

		}
		return TRUE;
	}
	
	static public function getPostMaxSize()
	{
		$post_max_size = ini_get('post_max_size');
		switch (substr($post_max_size, -1))
		{
			case 'G':
				$post_max_size = (int) $post_max_size * 1024 * 1024 * 1024;
				break;
			case 'M':
				$post_max_size = (int) $post_max_size * 1024 * 1024;
				break;
			case 'K':
				$post_max_size = (int) $post_max_size * 1024;
				break;
		}
		return $post_max_size;
	}
	
	static public function getWeekRange($date, $week_start)
	{
		$week_arr = array(
				0=>'sunday',
				1=>'monday',
				2=>'tuesday',
				3=>'wednesday',
				4=>'thursday',
				5=>'friday',
				6=>'saturday');
			
		$ts = strtotime($date);
		$start = (date('w', $ts) == 0) ? $ts : strtotime('last ' . $week_arr[$week_start], $ts);
		$week_start = ($week_start == 0 ? 6 : $week_start -1);
		return array(date('Y-m-d', $start), date('Y-m-d', strtotime('next ' . $week_arr[$week_start], $start)));
	}
	
	static public function getComingWhere($period, $week_start)
	{
		$where_str = '';
		switch ($period) {
			case 1:
				$where_str = "(DATE_FORMAT(t1.date_time,'%Y-%m-%d') = CURDATE() AND NOW() <= t1.date_time)";
				break;
				;
			case 2:
				$where_str = "(DATE(DATE_ADD(NOW(), INTERVAL 1 DAY)) = DATE_FORMAT(t1.date_time,'%Y-%m-%d'))";
				break;
				;
			case 3:
				list($start_week, $end_week) = pjUtil::getWeekRange(date('Y-m-d'), $week_start);
				$where_str = "(t1.date_time BETWEEN NOW() AND '$end_week')";
				break;
				;
			case 4:
				list($start_week, $end_week) = pjUtil::getWeekRange(date('Y-m-d', strtotime("+7 days")), $week_start);
				$where_str = "(t1.date_time BETWEEN '$start_week' AND '$end_week')";
				break;
				;
			case 5:
				$end_month = date('Y-m-t',strtotime('this month'));
				$where_str = "(t1.date_time BETWEEN NOW() AND '$end_month')";
				break;
				;
			case 6:
				$start_month = date("Y-m-d", mktime(0, 0, 0, date("m") + 1, 1, date("Y")));
				$end_month = date("Y-m-d", mktime(0, 0, 0, date("m") + 2, 0, date("Y")));
				$where_str = "((t1.date_time BETWEEN '$start_month' AND '$end_month'))";
				break;
				;
		}
		return $where_str;
	}
	
	static public function getMadeWhere($period, $week_start)
	{
		$where_str = '';
		switch ($period) {
			case 1:
				$where_str = "(DATE_FORMAT(t1.date_time,'%Y-%m-%d') = CURDATE() AND NOW() >= t1.date_time)";
				break;
				;
			case 2:
				$where_str = "(DATE(DATE_SUB(NOW(), INTERVAL 1 DAY)) = DATE_FORMAT(t1.date_time,'%Y-%m-%d'))";
				break;
				;
			case 3:
				list($start_week, $end_week) = pjUtil::getWeekRange(date('Y-m-d'), $week_start);
				$where_str = "(t1.date_time BETWEEN '$start_week' AND NOW())";
				break;
				;
			case 4:
				list($start_week, $end_week) = pjUtil::getWeekRange(date('Y-m-d', strtotime("-7 days")), $week_start);
				$where_str = "(t1.date_time BETWEEN '$start_week' AND '$end_week')";
				break;
				;
			case 5:
				$start_month = date('Y-m-01',strtotime('this month'));
				$end_month = date('Y-m-t',strtotime('this month'));
				$where_str = "(t1.date_time BETWEEN '$start_month' AND NOW())";
				break;
				;
			case 6:
				$start_month = date("Y-m-d", mktime(0, 0, 0, date("m")-1, 1, date("Y")));
				$end_month = date("Y-m-d", mktime(0, 0, 0, date("m"), 0, date("Y")));
				$where_str = "(t1.date_time BETWEEN '$start_month' AND '$end_month')";
				break;
				;
		}
		return $where_str;
	}
	
	static public function getTimezoneName($timezone)
	{
		$offset = $timezone / 3600;
		$timezone_name = timezone_name_from_abbr(null, $offset * 3600, true);
		if($timezone_name === false)
		{
			$timezone_name = timezone_name_from_abbr(null, $offset * 3600, false);
		}
		if($offset == -12)
		{
			$timezone_name = 'Pacific/Wake';
		}
		return $timezone_name;
	}
	static public function html2txt($document)
	{
		$search = array('@<script[^>]*?>.*?</script>@si',
				'@<[\/\!]*?[^<>]*?>@si',
				'@<style[^>]*?>.*?</style>@siU',
				'@<![\s\S]*?--[ \t\n\r]*>@'
		);
		$text = preg_replace($search, '', $document);
		return $text;
	}
	
	static public function truncateDescription($string, $limit, $break=".", $pad="...")
	{
		if(strlen($string) <= $limit)
			return $string;
		if(false !== ($breakpoint = strpos($string, $break, $limit)))
		{
			if($breakpoint < strlen($string) - 1)
			{
				$string = substr($string, 0, $breakpoint) . $pad;
			}
		}
		return $string;
	}
	
	static public function arrayMergeDistinct ( array &$array1, array &$array2 )
	{
		$merged = $array1;
	
		foreach ( $array2 as $key => &$value )
		{
			if ( is_array ( $value ) && isset ( $merged [$key] ) && is_array ( $merged [$key] ) )
			{
				$merged [$key] = pjUtil::arrayMergeDistinct ( $merged [$key], $value );
			}
			else
			{
				$merged [$key] = $value;
			}
		}
	
		return $merged;
	}
	
	static public function sortArrayByArray(Array $array, Array $orderArray) 
	{
		$ordered = array();
		foreach($orderArray as $key)
		{
			if(array_key_exists($key,$array))
			{
				$ordered[$key] = $array[$key];
				unset($array[$key]);
			}
		}
		return $ordered + $array;
	}
}
?>