<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjAppController extends pjController
{
	public $models = array();
	
	public $option_arr = array();
	
	public $defaultLocale = 'admin_locale_id';
  
	public $defaultFields = 'fields';
	
	public $defaultFieldsIndex = 'fields_index';
  
	protected function loadSetFields($force=FALSE, $locale_id=NULL, $fields=NULL)
	{
		if (is_null($locale_id))
		{
			$locale_id = $this->getLocaleId();
		}
		
		if (is_null($fields))
		{
			$fields = $this->defaultFields;
		}
		
		$registry = pjRegistry::getInstance();
		if ($force
				|| !isset($_SESSION[$this->defaultFieldsIndex])
				|| $_SESSION[$this->defaultFieldsIndex] != $this->option_arr['o_fields_index']
				|| !isset($_SESSION[$fields])
				|| empty($_SESSION[$fields]))
		{
			pjAppController::setFields($locale_id);
	
			# Update session
			if ($registry->is('fields'))
			{
				$_SESSION[$fields] = $registry->get('fields');
			}
			$_SESSION[$this->defaultFieldsIndex] = $this->option_arr['o_fields_index'];
		}
	
		if (isset($_SESSION[$fields]) && !empty($_SESSION[$fields]))
		{
			# Load fields from session
			$registry->set('fields', $_SESSION[$fields]);
		}
		
		return TRUE;
	}
	
	public function isCountryReady()
    {
    	return $this->isAdmin();
    }
    
	public function isOneAdminReady()
    {
    	return $this->isAdmin();
    }
    
    public function isInvoiceReady()
    {
    	return $this->isAdmin() || $this->isEditor();
    }
	
	public static function setTimezone($timezone="UTC")
    {
    	if (in_array(version_compare(phpversion(), '5.1.0'), array(0,1)))
		{
			date_default_timezone_set($timezone);
		} else {
			$safe_mode = ini_get('safe_mode');
			if ($safe_mode)
			{
				putenv("TZ=".$timezone);
			}
		}
    }

	public static function setMySQLServerTime($offset="-0:00")
    {
		pjAppModel::factory()->prepare("SET SESSION time_zone = :offset;")->exec(compact('offset'));
		pjAppModel::factory()->prepare("SET SESSION group_concat_max_len = 100000;")->exec();
    }
    
	public function setTime()
	{
		if (isset($this->option_arr['o_timezone']))
		{
			$offset = $this->option_arr['o_timezone'] / 3600;
			if ($offset > 0)
			{
				$offset = "-".$offset;
			} elseif ($offset < 0) {
				$offset = "+".abs($offset);
			} elseif ($offset === 0) {
				$offset = "+0";
			}
	
			pjAppController::setTimezone('Etc/GMT' . $offset);
			if (strpos($offset, '-') !== false)
			{
				$offset = str_replace('-', '+', $offset);
			} elseif (strpos($offset, '+') !== false) {
				$offset = str_replace('+', '-', $offset);
			}
			pjAppController::setMySQLServerTime($offset . ":00");
		}
	}
    
    public function beforeFilter()
    {
    	$this->appendJs('jquery.min.js', PJ_THIRD_PARTY_PATH . 'jquery/');
    	$baseDir = defined("PJ_INSTALL_PATH") ? PJ_INSTALL_PATH : null;
		$dm = new pjDependencyManager($baseDir, PJ_THIRD_PARTY_PATH);
		$dm->load(PJ_CONFIG_PATH . 'dependencies.php')->resolve();
		$this->appendJs('jquery-migrate.min.js', $dm->getPath('jquery_migrate'), FALSE, FALSE);
		$this->appendJs('pjAdminCore.js');
		$this->appendCss('reset.css');
		 
		$this->appendJs('js/jquery-ui.custom.min.js', PJ_THIRD_PARTY_PATH . 'jquery_ui/');
		$this->appendCss('css/smoothness/jquery-ui.min.css', PJ_THIRD_PARTY_PATH . 'jquery_ui/');
				
		$this->appendCss('pj-all.css', PJ_FRAMEWORK_LIBS_PATH . 'pj/css/');
		$this->appendCss('admin.css');
		
    	if ($_GET['controller'] != 'pjInstaller')
		{
			$this->models['Option'] = pjOptionModel::factory();
			$this->option_arr = $this->models['Option']->getPairs($this->getForeignId());
			$this->set('option_arr', $this->option_arr);
			$this->setTime();
			
			if (!isset($_SESSION[$this->defaultLocale]))
			{
				$locale_arr = pjLocaleModel::factory()->where('is_default', 1)->limit(1)->findAll()->getData();
				if (count($locale_arr) === 1)
				{
					$this->setLocaleId($locale_arr[0]['id']);
				}
			}
			if (!in_array($_GET['action'], array('pjActionPreview')))
			{
				$this->loadSetFields();
			}
		}
    }
    
	public function isEditor()
    {
    	return $this->getRoleId() == 2;
    }
    
    public function getForeignId()
    {
    	return 1;
    }
    
    public static function setFields($locale)
    {
    	if(isset($_SESSION['lang_show_id']) && (int) $_SESSION['lang_show_id'] == 1)
		{
			$fields = pjMultiLangModel::factory()
				->select('CONCAT(t1.content, CONCAT(":", t2.id, ":")) AS content, t2.key')
				->join('pjField', "t2.id=t1.foreign_id", 'inner')
				->where('t1.locale', $locale)
				->where('t1.model', 'pjField')
				->where('t1.field', 'title')
				->findAll()
				->getDataPair('key', 'content');
		}else{
			$fields = pjMultiLangModel::factory()
				->select('t1.content, t2.key')
				->join('pjField', "t2.id=t1.foreign_id", 'inner')
				->where('t1.locale', $locale)
				->where('t1.model', 'pjField')
				->where('t1.field', 'title')
				->findAll()
				->getDataPair('key', 'content');
		}  
		$registry = pjRegistry::getInstance();
		$tmp = array();
		if ($registry->is('fields'))
		{
			$tmp = $registry->get('fields');
		}
		$arrays = array();
		foreach ($fields as $key => $value)
		{
			if (strpos($key, '_ARRAY_') !== false)
			{
				list($prefix, $suffix) = explode("_ARRAY_", $key);
				if (!isset($arrays[$prefix]))
				{
					$arrays[$prefix] = array();
				}
				$arrays[$prefix][$suffix] = $value;
			}
		}
		require PJ_CONFIG_PATH . 'settings.inc.php';
		$fields = array_merge($tmp, $fields, $settings, $arrays);
		$registry->set('fields', $fields);
    }

    public static function jsonDecode($str)
	{
		$Services_JSON = new pjServices_JSON();
		return $Services_JSON->decode($str);
	}
	
	public static function jsonEncode($arr)
	{
		$Services_JSON = new pjServices_JSON();
		return $Services_JSON->encode($arr);
	}
	
	public static function jsonResponse($arr)
	{
		header("Content-Type: application/json; charset=utf-8");
		echo pjAppController::jsonEncode($arr);
		exit;
	}

	public function getLocaleId()
	{
		return isset($_SESSION[$this->defaultLocale]) && (int) $_SESSION[$this->defaultLocale] > 0 ? (int) $_SESSION[$this->defaultLocale] : false;
	}
	
	public function setLocaleId($locale_id)
	{
		$_SESSION[$this->defaultLocale] = (int) $locale_id;
	}
	
	public function pjActionCheckInstall()
	{
		$this->setLayout('pjActionEmpty');
		
		$result = array('status' => 'OK', 'code' => 200, 'text' => 'Operation succeeded', 'info' => array());
		$folders = array(
							'app/web/upload',
							'app/web/upload/events',
							'app/web/upload/maps',
							'app/web/upload/tickets',
							'app/web/upload/tickets/barcodes',
							'app/web/upload/tickets/pdfs',
							'app/web/upload/tickets/tickets'
						);
		foreach ($folders as $dir)
		{
			if (!is_writable($dir))
			{
				$result['status'] = 'ERR';
				$result['code'] = 101;
				$result['text'] = 'Permission requirement';
				$result['info'][] = sprintf('Folder \'<span class="bold">%1$s</span>\' is not writable. You need to set write permissions (chmod 777) to directory located at \'<span class="bold">%1$s</span>\'', $dir);
			}
		}
		
		return $result;
	}
	
	public function friendlyURL($str, $divider='-')
	{
		$str = mb_strtolower($str, mb_detect_encoding($str));
		$str = trim($str);
		$str = preg_replace('/[_|\s]+/', $divider, $str);
		$str = preg_replace('/\x{00C5}/u', 'AA', $str);
		$str = preg_replace('/\x{00C6}/u', 'AE', $str);
		$str = preg_replace('/\x{00D8}/u', 'OE', $str);
		$str = preg_replace('/\x{00E5}/u', 'aa', $str);
		$str = preg_replace('/\x{00E6}/u', 'ae', $str);
		$str = preg_replace('/\x{00F8}/u', 'oe', $str);
		$str = preg_replace('/[^a-z\x{0400}-\x{04FF}0-9-]+/u', '', $str);
		$str = preg_replace('/[-]+/', $divider, $str);
		$str = preg_replace('/^-+|-+$/', '', $str);
		return $str;
	}
	
	public function calculatePrice($ticket_arr, $chosen_ar)
	{
		$price_arr = array();
		$sub_total = 0;
		$tax = 0;
		$total = 0;
		$deposit = 0;
		
		foreach($ticket_arr as $v)
		{
			if(isset($chosen_ar[$v['id']][$v['price_id']]) && $chosen_ar[$v['id']][$v['price_id']] > 0)
			{
				$sub_total += $chosen_ar[$v['id']][$v['price_id']] * $v['price'];
			}
		}
		if($sub_total > 0)
		{
			$tax = ($sub_total * $this->option_arr['o_tax_payment']) / 100;
			$total = $sub_total + $tax;
			$deposit = ($total * $this->option_arr['o_deposit_payment']) / 100;
		} 
		return compact('sub_total', 'tax', 'total', 'deposit');
	}
	
	public function getFromEmail($option_arr)
	{
		$email = $option_arr['o_email_address'];
		if($email == '')
		{
			$arr = pjUserModel::factory()->find(1)->getData();
			$email = $arr['email'];
		}
		return $email;
	}
	
	public function getAdminEmail()
	{
		$arr = pjUserModel::factory()
			->findAll()
			->orderBy("t1.id ASC")
			->limit(1)
			->getData();
		return !empty($arr) ? $arr[0]['email'] : null;	
	}
	
	public function getAdminPhone()
	{
		$arr = pjUserModel::factory()
			->findAll()
			->orderBy("t1.id ASC")
			->limit(1)
			->getData();
		return !empty($arr) ? (!empty($arr[0]['phone']) ? $arr[0]['phone'] : null) : null;	
	}
	
	public function getShowsInGrid($arr)
	{
		$show_arr = array();
		$time_arr = array();
		$all_show_arr = array();
		foreach($arr as $v)
		{
			$_time = date('H:00', strtotime($v['date_time']));
			$time = date('H:i', strtotime($v['date_time']));
			if(empty($show_arr))
			{
				$show_arr[$v['event_id']][] = $time;
				$all_show_arr[$v['event_id']][] = $v['date_time'];
			}else{
				if(array_key_exists($v['event_id'], $show_arr))
				{
					if(!in_array($time, $show_arr[$v['event_id']]))
					{
						$show_arr[$v['event_id']][] = $time;
					}
					if(!in_array($v['date_time'], $all_show_arr[$v['event_id']]))
					{
						$all_show_arr[$v['event_id']][] = $v['date_time'];
					}
				}else{
					$show_arr[$v['event_id']][] = $time;
					$all_show_arr[$v['event_id']][] = $v['date_time'];
				}
			}
			if(empty($time_arr))
			{
				$time_arr[] = $_time;
			}else{
				if(!in_array($_time, $time_arr))
				{
					$time_arr[] = $_time;
				}
			}
		}
			
		$time = array();
		foreach ($time_arr as $key => $val) {
			$time[$key] = $val[0];
		}
		array_multisort($time, SORT_ASC, $time_arr);
		
		return compact("show_arr", 'time_arr', 'all_show_arr');
	}
	
	public function getData($option_arr, $booking_arr, $salt, $locale_id)
	{
		$personal_titles = __('personal_titles', true, false);
		$cancelURL = PJ_INSTALL_URL . 'index.php?controller=pjFront&action=pjActionCancel&id='.@$booking_arr['id'].'&hash='.sha1(@$booking_arr['id'].@$booking_arr['created'].$salt);
		$cancelURL = '<a href="'.$cancelURL.'">'.$cancelURL.'</a>';
		
		$PDFticket = '';
		if(is_file(PJ_INSTALL_PATH . PJ_UPLOAD_PATH . 'tickets/pdfs/p_' . $booking_arr['uuid'] . '.pdf'))
		{
			$PDFticket = PJ_INSTALL_URL . PJ_UPLOAD_PATH . 'tickets/pdfs/p_' . $booking_arr['uuid'] . '.pdf';
			$PDFticket = '<a href="'.$PDFticket.'">'.$PDFticket.'</a>';
		}
		
		$payment_methods = __('payment_methods', true, false);
		
		$search = array(
				'{Title}', 
				'{Name}', 
				'{Email}', 
				'{Phone}', 
				'{Country}', 
				'{City}', 
				'{State}', 
				'{Zip}', 
				'{Address}',
				'{Company}', 
				'{Notes}', 
				'{Movie}', 
				'{MovieID}', 
				'{Showtime}',
				'{BookingID}', 
				'{CinemaHall}',
				'{BookingSeats}',
				'{Tickets}',
				'{Deposit}', 
				'{Tax}',
				'{Total}',
				'{PaymentMethod}', 
				'{CCType}', 
				'{CCNum}', 
				'{CCExp}',
				'{CCSec}', 
				'{CancelURL}',
				'{TicketPrice}',
				'{PDFticket}'
				
		);
		$replace = array(
				(!empty($booking_arr['c_title']) ? $personal_titles[$booking_arr['c_title']] : null), 
				$booking_arr['c_name'], 
				$booking_arr['c_email'], 
				$booking_arr['c_phone'], 
				$booking_arr['country_title'], 
				$booking_arr['c_city'],
				$booking_arr['c_state'], 
				$booking_arr['c_zip'], 
				$booking_arr['c_address'],
				$booking_arr['c_company'],
				$booking_arr['c_notes'],
				$booking_arr['event_title'],
				$booking_arr['event_id'],
				$booking_arr['date_time'],
				$booking_arr['uuid'],
				$booking_arr['hall'],
				$booking_arr['seats'],
				$booking_arr['cnt_tickets'],
				pjUtil::formatCurrencySign($booking_arr['deposit'], $option_arr['o_currency']),
				pjUtil::formatCurrencySign($booking_arr['tax'], $option_arr['o_currency']),
				pjUtil::formatCurrencySign($booking_arr['total'], $option_arr['o_currency']),
				@$payment_methods[$booking_arr['payment_method']],
				@$booking_arr['cc_type'],
				@$booking_arr['cc_number'], 
				(@$booking_arr['payment_method'] == 'creditcard' ? @$booking_arr['cc_exp'] : NULL),
				@$booking_arr['cc_code'],
				$cancelURL,
				$booking_arr['tickets'],
				$PDFticket
				
		);
		
		return compact('search', 'replace');
	}
	
	public function pjActionGenerateInvoice($arr)
	{
		$map = array(
				'confirmed' => 'paid',
				'cancelled' => 'cancelled',
				'pending' => 'not_paid'
		);

		$last_id = 1;
		$invoice_arr = pjInvoiceModel::factory()
			->limit(1)
			->orderBy("id DESC")
			->findAll()
			->getData();
		if(!empty($invoice_arr))
		{
			$last_id = $invoice_arr[0]['id'] + 1;
		}
		
		$response = $this->requestAction(
				array(
					'controller' => 'pjInvoice',
					'action' => 'pjActionCreate',
					'params' => array(
					'key' => md5($this->option_arr['private_key'] . PJ_SALT),
					// -------------------------------------------------
					'uuid' => $last_id,
					'order_id' => $arr['uuid'],
					'foreign_id' => 1,
					'issue_date' => ':CURDATE()',
					'due_date' => ':CURDATE()',
					'created' => ':NOW()',
					// 'modified' => ':NULL',
					'status' => @$map[$arr['status']],
					'payment_method' => $arr['payment_method'],
					'cc_type' => $arr['cc_type'],
					'cc_num' => $arr['cc_num'],
					'cc_exp_month' => $arr['cc_exp_month'],
					'cc_exp_year' => $arr['cc_exp_year'],
					'cc_code' => $arr['cc_code'],
					'subtotal' => $arr['sub_total'],
					// 'discount' => $arr['discount'],
					'tax' => $arr['tax'],
					// 'shipping' => $arr['shipping'],
					'total' => $arr['total'],
					'paid_deposit' => $arr['deposit'],
					'amount_due' => $arr['total'] - $arr['deposit'],
					'currency' => $this->option_arr['o_currency'],
					'notes' => $arr['c_notes'],
					// 'y_logo' => $arr[''],
					// 'y_company' => $arr[''],
					// 'y_name' => $arr[''],
					// 'y_street_address' => $arr[''],
					// 'y_city' => $arr[''],
					// 'y_state' => $arr[''],
					// 'y_zip' => $arr[''],
					// 'y_phone' => $arr[''],
					// 'y_fax' => $arr[''],
					// 'y_email' => $arr[''],
					// 'y_url' => $arr[''],
					'b_billing_address' => $arr['c_address'],
					// 'b_company' => ':NULL',
					'b_name' => $arr['c_name'],
					'b_address' => $arr['c_address'],
					'b_street_address' => $arr['c_address'],
					'b_city' => $arr['c_city'],
					'b_state' => $arr['c_state'],
					'b_zip' => $arr['c_zip'],
					'b_phone' => $arr['c_phone'],
					// 'b_fax' => ':NULL',
					'b_email' => $arr['c_email'],
					// 'b_url' => $arr['url'],
					// 's_shipping_address' => (int) $arr['same_as'] === 1 ? $arr['b_address_1'] : $arr['s_address_1'],
					// 's_company' => ':NULL',
					// 's_name' => (int) $arr['same_as'] === 1 ? $arr['b_name'] : $arr['s_name'],
					// 's_address' => (int) $arr['same_as'] === 1 ? $arr['b_address_1'] : $arr['s_address_1'],
					// 's_street_address' => (int) $arr['same_as'] === 1 ? $arr['b_address_2'] : $arr['s_address_2'],
					// 's_city' => (int) $arr['same_as'] === 1 ? $arr['b_city'] : $arr['s_city'],
					// 's_state' => (int) $arr['same_as'] === 1 ? $arr['b_state'] : $arr['s_state'],
					// 's_zip' => (int) $arr['same_as'] === 1 ? $arr['b_zip'] : $arr['s_zip'],
					// 's_phone' => $arr['phone'],
					// 's_fax' => ':NULL',
					// 's_email' => $arr['email'],
					// 's_url' => $arr['url'],
					// 's_date' => ':NULL',
					// 's_terms' => ':NULL',
					// 's_is_shipped' => ':NULL',
					'items' => array(
							array(
									'name' => $arr['event_title'],
									'description' => $arr['tickets'],
									'qty' => 1,
									'unit_price' => $arr['total'],
									'amount' => $arr['total']
							)
						)
					// -------------------------------------------------
					)
				),
				array('return')
		);
	
		return $response;
	}
	
	public function pjActionGetBookingDetails($id)
	{
		$arr = pjBookingModel::factory()
			->join('pjMultiLang', "t2.model='pjEvent' AND t2.foreign_id=t1.event_id AND t2.field='title' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
			->join('pjMultiLang', "t3.model='pjCountry' AND t3.foreign_id=t1.c_country AND t3.field='name' AND t3.locale='".$this->getLocaleId()."'", 'left outer')
			->select(sprintf("t1.*, 
				AES_DECRYPT(t1.cc_type, '%1\$s') AS `cc_type`,
				AES_DECRYPT(t1.cc_num, '%1\$s') AS `cc_num`,
				AES_DECRYPT(t1.cc_exp_month, '%1\$s') AS `cc_exp_month`,
				AES_DECRYPT(t1.cc_exp_year, '%1\$s') AS `cc_exp_year`,
				AES_DECRYPT(t1.cc_code, '%1\$s') AS `cc_code`, 
				t2.content as event_title, t3.content as country_title", PJ_SALT))
			->find($id)
			->getData();
		
		$_show_arr = pjBookingShowModel::factory()
			->join('pjMultiLang', "t2.model='pjPrice' AND t2.foreign_id=t1.price_id AND t2.field='price_name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
			->join('pjShow', "t3.id = t1.show_id", "left outer")
			->join('pjSeat', "t4.id = t1.seat_id", "left outer")
			->select('t1.*, t2.content as price_name, t3.date_time, t4.name as seat_name')
			->where('t1.booking_id', $id)
			->findAll()
			->getData();
	
		$bt_arr = pjBookingTicketModel::factory()
			->select("t2.venue_id")
			->join("pjSeat", "t2.id=t1.seat_id", "left")
			->where("booking_id", $id)
			->limit(1)
			->findAll()
			->getData();
		
		$arr['booking_date_time'] = $arr['date_time'];
		$arr['date_time'] = null;
		$arr['tickets'] = null;
		$arr['cnt_tickets'] = 0;
		$arr['hall'] = null;
		$seat_name_arr = array();
		$t_arr = array();
		$p_arr = array();
		$_tickets = array();
		
		if(count($bt_arr) > 0)
		{
			$venue_id = $bt_arr[0]['venue_id'];
			$venue_arr = pjVenueModel::factory()
				->select('t1.*, t2.content AS hall')
				->join('pjMultiLang', "t2.model='pjVenue' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
				->find($venue_id)
				->getData();
			$arr['hall'] = $venue_arr['hall'];
		}
		foreach($_show_arr as $v)
		{
			$seat_name_arr[] = $v['seat_name'];
			$arr['date_time'] = date($this->option_arr['o_date_format'], strtotime($v['date_time'])) . ', ' . date($this->option_arr['o_time_format'], strtotime($v['date_time']));
			$t_arr[$v['price_id']] = $v['price_name'] . '('.pjUtil::formatCurrencySign(number_format($v['price'], 2), $this->option_arr['o_currency']).')';
			if(!isset($p_arr[$v['price_id']]))
			{
				$p_arr[$v['price_id']] = $v['cnt'];
			}else{
				$p_arr[$v['price_id']] += $v['cnt'];
			}
			$arr['cnt_tickets'] += $v['cnt'];
		}
		foreach($t_arr as $price_id => $v)
		{
			$_tickets[] = $v . ' x ' . $p_arr[$price_id];
		}
	
		$arr['seats'] = join(', ', $seat_name_arr);
		$arr['tickets'] = join('<br/>', $_tickets);
		
		return $arr;
	}
	
	public function buildPdfTickets($arr)
	{
		$event_arr = pjEventModel::factory()
			->find($arr['event_id'])
			->getData();
		if(is_file($this->option_arr['o_ticket_image']))
		{
			$bt_arr = pjBookingTicketModel::factory()
				->join('pjBooking', 't1.booking_id = t2.id', 'left')
				->join('pjMultiLang', "t3.model='pjPrice' AND t3.foreign_id=t1.price_id AND t3.field='price_name' AND t3.locale='".$this->getLocaleId()."'", 'left outer')
				->join('pjSeat', 't1.seat_id = t4.id', 'left')
				->join('pjMultiLang', "t5.model='pjEvent' AND t5.foreign_id=t2.event_id AND t5.field='title' AND t5.locale='".$this->getLocaleId()."'", 'left outer')
				->join('pjMultiLang', "t6.model='pjCoutnry' AND t6.foreign_id=t2.c_country AND t6.field='name' AND t6.locale='".$this->getLocaleId()."'", 'left outer')
				->join('pjMultiLang', "t7.model='pjVenue' AND t7.foreign_id=t4.venue_id AND t7.field='name' AND t7.locale='".$this->getLocaleId()."'", 'left outer')
				->select("t2.*, t1.ticket_id, t1.seat_id, t1.price_id, t1.unit_price as price, t3.content as price_name, t4.name as seat_name, t5.content as event_title, t6.content as country_title, t7.content as hall")
				->where('t1.booking_id', $arr['id'])
				->findAll()
				->getData();
			
			$ticket_template = pjMultiLangModel::factory()->select('t1.*')
				->where('t1.model','pjOption')
				->where('t1.locale', $this->getLocaleId())
				->where('t1.field', 'o_ticket_data')
				->limit(0, 1)
				->findAll()->getData();
				
			foreach($bt_arr as $k => $v)
			{
				$v['seats'] = $v['seat_name'];
				$v['tickets'] = $v['price_name'] . '('.pjUtil::formatCurrencySign($v['price'], $this->option_arr['o_currency']).')';
				$v['ticket_img'] = $this->option_arr['o_ticket_image'];
				$v['ticket_info'] = '';
				$v['cnt_tickets'] = 0;
				$v['date_time'] = date($this->option_arr['o_date_format'], strtotime($v['date_time'])) . ', ' . date($this->option_arr['o_time_format'], strtotime($v['date_time']));
				$tokens = pjAppController::getData($this->option_arr, $v, PJ_SALT, $this->getLocaleId());
				if (count($ticket_template) === 1)
				{
					$ticket_info = str_replace($tokens['search'], $tokens['replace'], $ticket_template[0]['content']);
					$ticket_info = preg_replace('/\r\n|\n/', '<br />', $ticket_info);
					$v['ticket_info'] = $ticket_info;
				}
				$bt_arr[$k] = $v;
			}
			$pjTicketPdf = new pjTicketPdf();
			$pjTicketPdf->generatePdf($bt_arr);
		}
	}
	
	public function deleteTicketInfo($booking_id, $uuid)
	{
		$ticket_arr = pjBookingTicketModel::factory()->where('booking_id', $booking_id)->findAll()->getData();
		foreach($ticket_arr as $v)
		{
			$barcode_path = PJ_INSTALL_PATH . PJ_UPLOAD_PATH . 'tickets/barcodes/b_'. $v['ticket_id'] .'.png';
			$ticket_path = PJ_INSTALL_PATH . PJ_UPLOAD_PATH . 'tickets/tickets/t_' . $v['ticket_id'] . '.png';
			if(is_file($barcode_path))
			{
				@unlink($barcode_path);
			}
			if(is_file($ticket_path))
			{
				@unlink($ticket_path);
			}
		}
		$pdf_path = PJ_INSTALL_PATH . PJ_UPLOAD_PATH . 'tickets/pdfs/p_'. $uuid . '.pdf';
		if(is_file($pdf_path))
		{
			@unlink($pdf_path);
		}
	}
	
	public function getTicketInfo($booking_id_arr)
	{
		$bs_arr = pjBookingShowModel::factory()
			->select("t1.*, t2.content as price_name, CONCAT('#',t3.name) as seat_name")
			->join('pjMultiLang', "t2.model='pjPrice' AND t2.foreign_id=t1.price_id AND t2.field='price_name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
			->join('pjSeat', "t3.id=t1.seat_id", 'left')
			->whereIn('t1.booking_id', $booking_id_arr)
			->findAll()
			->getData();
			
		$ticket_cnt_arr = array();
		$ticket_seat_arr = array();
		$ticket_name_arr = array();
		foreach($bs_arr as $v)
		{
			if(isset($ticket_cnt_arr[$v['booking_id']][$v['price_id']]))
			{
				$ticket_cnt_arr[$v['booking_id']][$v['price_id']] += $v['cnt'];
			}else{
				$ticket_cnt_arr[$v['booking_id']][$v['price_id']] = $v['cnt'];
			}
			if(!isset($ticket_name_arr[$v['booking_id']][$v['price_id']] ))
			{
				$ticket_name_arr[$v['booking_id']][$v['price_id']] = $v['price_name'] . ' ('.pjUtil::formatCurrencySign($v['price'], $this->option_arr['o_currency']).')';
			}
			
			$ticket_seat_arr[$v['booking_id']][$v['price_id']][] = $v['seat_name'];
		}
		return compact('ticket_cnt_arr', 'ticket_name_arr', 'ticket_seat_arr');
	}
}
?>