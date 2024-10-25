<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjFront extends pjAppController
{	
	public $defaultCaptcha = 'pjTicketBooking_Captcha';
	
	public $defaultLocale = 'pjTicketBooking_LocaleId';
	
	public $defaultLangMenu = 'pjTicketBooking_LangMenu';
	
	public $defaultStore = 'pjTicketBooking_Store';
	
	public $defaultForm = 'pjTicketBooking_Form';
	
	public function __construct()
	{
		$this->setLayout('pjActionFront');
		self::allowCORS();
	}

	private function _get($key)
	{
		if ($this->_is($key))
		{
			return $_SESSION[$this->defaultStore][$key];
		}
		return false;
	}
	
	private function _is($key)
	{
		return isset($_SESSION[$this->defaultStore]) && isset($_SESSION[$this->defaultStore][$key]);
	}
	
	private function _set($key, $value)
	{
		$_SESSION[$this->defaultStore][$key] = $value;
		return $this;
	}
	
	public function afterFilter()
	{		
		if (!isset($_GET['hide']) || (isset($_GET['hide']) && (int) $_GET['hide'] !== 1) &&
			in_array($_GET['action'], array('pjActionEvents', 'pjActionDetails', 'pjActionSeats', 'pjActionCheckout', 'pjActionPreview', 'pjActionGetPaymentForm')))
		{
			$locale_arr = pjLocaleModel::factory()->select('t1.*, t2.file, t2.title')
				->join('pjLocaleLanguage', 't2.iso=t1.language_iso', 'left')
				->where('t2.file IS NOT NULL')
				->orderBy('t1.sort ASC')->findAll()->getData();
			
			$this->set('locale_arr', $locale_arr);
		}
	}
	
	public function beforeFilter()
	{
		$OptionModel = pjOptionModel::factory();
		$this->option_arr = $OptionModel->getPairs($this->getForeignId());
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
		if (!in_array($_GET['action'], array('pjActionLoadCss')))
		{
			$this->loadSetFields();
		}
	}
	
	public function beforeRender()
	{
		if (isset($_GET['iframe']))
		{
			$this->setLayout('pjActionIframe');
		}
	}
	
	public function pjActionLocale()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			if (isset($_GET['locale_id']))
			{
				$this->pjActionSetLocale($_GET['locale_id']);
				
				$this->loadSetFields(true);
				
				$day_names = __('day_names', true);
				ksort($day_names, SORT_NUMERIC);
				
				$months = __('months', true);
				ksort($months, SORT_NUMERIC);
				
				pjAppController::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => 'Locale have been changed.', 'opts' => array(
					'day_names' => array_values($day_names),
					'month_names' => array_values($months)
				)));
			}
		}
		exit;
	}
	private function pjActionSetLocale($locale)
	{
		if ((int) $locale > 0)
		{
			$_SESSION[$this->defaultLocale] = (int) $locale;
		}
		return $this;
	}
	
	public function pjActionGetLocale()
	{
		return isset($_SESSION[$this->defaultLocale]) && (int) $_SESSION[$this->defaultLocale] > 0 ? (int) $_SESSION[$this->defaultLocale] : FALSE;
	}
	
	public function pjActionCaptcha()
	{
		$this->setAjax(true);
		header("Cache-Control: max-age=3600, private");
		$Captcha = new pjCaptcha(PJ_WEB_PATH.'obj/Anorexia.ttf', $this->defaultCaptcha, 6);
		$Captcha->setImage(PJ_IMG_PATH.'button.png')->init(isset($_GET['rand']) ? $_GET['rand'] : null);
		exit;
	}

	public function pjActionCheckCaptcha()
	{
		$this->setAjax(true);
		if (!isset($_GET['captcha']) || empty($_GET['captcha']) || !pjCaptcha::validate($_GET['captcha'], $_SESSION[$this->defaultCaptcha])){
			echo 'false';
		}else{
			echo 'true';
		}
		exit;
	}
	
	public function pjActionLoadCss()
	{
		$dm = new pjDependencyManager(PJ_INSTALL_PATH, PJ_THIRD_PARTY_PATH);
		$dm->load(PJ_CONFIG_PATH . 'dependencies.php')->resolve();
		
		$layout = $this->option_arr['o_theme'];
		if(isset($_GET['layout']) && in_array($_GET['layout'], array('theme1', 'theme2', 'theme3', 'theme4', 'theme5', 'theme6', 'theme7', 'theme8', 'theme9', 'theme10')))
		{
			$layout = $_GET['layout'];
		}
		$arr = array(
			array('file' => 'jquery-ui.custom.min.css', 'path' => $dm->getPath('pj_jquery_ui') . 'css/smoothness/'),
			array('file' => 'default.css', 'path' => PJ_CSS_PATH),
			array('file' => 'font-awesome.min.css', 'path' => $dm->getPath('font_awesome') . 'css/'),
			array('file' => "$layout.css", 'path' => PJ_CSS_PATH)
		);
		header("Content-Type: text/css; charset=utf-8");
		foreach ($arr as $item)
		{
			ob_start();
			@readfile($item['path'] . $item['file']);
			$string = ob_get_contents();
			ob_end_clean();
			
			if ($string !== FALSE)
			{
				echo str_replace(
					array('../fonts/glyphicons', '../fonts/fontawesome', 'images/', "pjWrapper"),
					array(
						PJ_INSTALL_URL . PJ_FRAMEWORK_LIBS_PATH . 'pj/fonts/glyphicons',
						PJ_INSTALL_URL . $dm->getPath('font_awesome') . 'fonts/fontawesome',
						PJ_INSTALL_URL . $dm->getPath('pj_jquery_ui') . 'css/smoothness/images/',
						"pjWrapperTicketBooking_" . $layout
					),
					$string
				) . "\n";
			}
		}
		exit;
	}
	
	public function pjActionLoad()
	{
		ob_start();
		header("Content-Type: text/javascript; charset=utf-8");
		
		$terms_conditions = pjMultiLangModel::factory()->select('t1.*')
			->where('t1.model','pjOption')
			->where('t1.locale', $this->getLocaleId())
			->where('t1.field', 'o_terms')
			->limit(0, 1)
			->findAll()->getData();
		$this->set('terms_conditions', $terms_conditions[0]['content']);
		if(isset($_GET['locale']) && $_GET['locale'] > 0)
		{
			$_SESSION[$this->defaultLocale] = (int) $_GET['locale'];
			$_SESSION[$this->defaultLangMenu] = 'hide';
			$this->loadSetFields(true);
		}else{
			$locale_arr = pjLocaleModel::factory()->where('is_default', 1)->limit(1)->findAll()->getData();
			if (count($locale_arr) === 1)
			{
				$_SESSION[$this->defaultLocale] = $locale_arr[0]['id'];
			}
			$_SESSION[$this->defaultLangMenu] = 'show';
		}
	}
	
	public function pjActionEvents()
	{
		$this->setAjax(true);
		
		if ($this->isXHR())
		{
			$ts = time();
			$hash_date = date('Y-m-d', $ts);
			
			$from_ts = $ts;
			
			if(isset($_GET['from_date']) && !empty($_GET['from_date']))
			{
				$from_ts = strtotime(pjUtil::formatDate($_GET['from_date'], $this->option_arr['o_date_format']));
			}
			$end_ts = $from_ts + (86400 * 7);
			
			if(isset($_GET['date']) && !empty($_GET['date']))
			{
				$hash_date = pjUtil::formatDate($_GET['date'], $this->option_arr['o_date_format']);
			}
			if(strtotime($hash_date) < $from_ts || strtotime($hash_date) > $end_ts)
			{
				$hash_date = date('Y-m-d', $from_ts);
			}
			
			if($this->_is('tickets'))
			{
				unset($_SESSION[$this->defaultStore]['tickets']);
			}
			if($this->_is('seat_id'))
			{
				unset($_SESSION[$this->defaultStore]['seat_id']);
			}
			
			$pjEventModel = pjEventModel::factory();
			$pjShowModel = pjShowModel::factory();
			
			$pjEventModel->where("t1.id IN(SELECT TS.event_id FROM `".$pjShowModel->getTable()."` AS TS WHERE DATE_FORMAT(TS.date_time,'%Y-%m-%d') = '".$hash_date."')");
			$pjShowModel->where("(DATE_FORMAT(t1.date_time,'%Y-%m-%d') = '$hash_date') AND (t1.venue_id IN (SELECT TV.id FROM `".pjVenueModel::factory()->getTable()."` AS TV WHERE TV.status='T') )");
			
			$arr = $pjEventModel
				->join('pjMultiLang', "t2.model='pjEvent' AND t2.foreign_id=t1.id AND t2.field='title' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
				->join('pjMultiLang', "t3.model='pjEvent' AND t3.foreign_id=t1.id AND t3.field='description' AND t3.locale='".$this->getLocaleId()."'", 'left outer')
				->select('t1.*, t2.content as title, t3.content as description')
				->where('status', 'T')
				->findAll()
				->getData();
			
			$_arr = $pjShowModel->orderBy("t1.date_time ASC")->findAll()->getData();
			
			$grid = $this->getShowsInGrid($_arr);

			$this->set('arr', $arr);
			$this->set('all_show_arr', $grid['all_show_arr']);
			$this->set('hash_date', $hash_date);
			$this->set('from_ts', $from_ts);
			$this->set('end_ts', $end_ts);
			$this->set('time_arr', $grid['time_arr']);
			$this->set('show_arr', $grid['show_arr']);
		}
	}
	
	public function pjActionDetails()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$hash_date = date('Y-m-d');
			if(isset($_GET['date']) && !empty($_GET['date']) && pjUtil::checkFormatDate($_GET['date'], $this->option_arr['o_date_format']) == TRUE)
			{
				$hash_date = pjUtil::formatDate($_GET['date'], $this->option_arr['o_date_format']);
			}
			
			$selected_date = $hash_date;
			$this->_set('selected_date', $selected_date);
			
			if($this->_is('tickets'))
			{
				unset($_SESSION[$this->defaultStore]['tickets']);
			}
			if($this->_is('seat_id'))
			{
				unset($_SESSION[$this->defaultStore]['seat_id']);
			}
			
			$pjEventModel = pjEventModel::factory();
				
			$arr = $pjEventModel
				->join('pjMultiLang', "t2.model='pjEvent' AND t2.foreign_id=t1.id AND t2.field='title' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
				->join('pjMultiLang', "t3.model='pjEvent' AND t3.foreign_id=t1.id AND t3.field='description' AND t3.locale='".$this->getLocaleId()."'", 'left outer')
				->select('t1.*, t2.content as title, t3.content as description')
				->find($_GET['id'])
				->getData();
			
			$show_arr = pjShowModel::factory()
				->where('t1.event_id', $_GET['id'])
				->where("(DATE_FORMAT(t1.date_time,'%Y-%m-%d') = '$selected_date') AND (t1.venue_id IN (SELECT TV.id FROM `".pjVenueModel::factory()->getTable()."` AS TV WHERE TV.status='T') )")
				->orderBy("date_time ASC")
				->findAll()
				->getData();
			$time_arr = array();
			foreach($show_arr as $v)
			{
				$time = date('H:i', strtotime($v['date_time']));
				if(strtotime($v['date_time']) > time() + $this->option_arr['o_booking_earlier'] * 60)
				{
					if(!in_array($time, $time_arr))
					{
						$time_arr[] = $time;
					}
				}
			}
			
			$this->set('arr', $arr);
			$this->set('hash_date', $hash_date);
			$this->set('selected_date', $selected_date);
			$this->set('time_arr', $time_arr);
		}
	}
	
	public function pjActionSeats()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			if (isset($_SESSION[$this->defaultStore]) && count($_SESSION[$this->defaultStore]) > 0)
			{
				$hash_date = date('Y-m-d');
				if(isset($_GET['date']) && !empty($_GET['date']))
				{
					$hash_date = pjUtil::formatDate($_GET['date'], $this->option_arr['o_date_format']);
				}
				$selected_date = $hash_date;
				if($this->_is('selected_date'))
				{
					$selected_date = $this->_get('selected_date');
				}
		
				$arr = pjEventModel::factory()
					->join('pjMultiLang', "t2.model='pjEvent' AND t2.foreign_id=t1.id AND t2.field='title' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
					->select('t1.*, t2.content as title')
					->find($this->_get('id'))
					->getData();

				$venue_id = null;
				$pjShowModel = pjShowModel::factory();
				$pjBookingShowModel = pjBookingShowModel::factory();
				$pjBookingModel = pjBookingModel::factory();
				$_show_arr = $pjShowModel
					->select('DISTINCT t1.venue_id, t2.content as venue_name')
					->join('pjMultiLang', "t2.model='pjVenue' AND t2.foreign_id=t1.venue_id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
					->where("(t1.venue_id IN (SELECT `TV`.id FROM `".pjVenueModel::factory()->getTable()."` AS `TV` WHERE `TV`.`status`='T'))")
					->where('t1.event_id', $this->_get('id'))
					->where("t1.date_time = '". $selected_date . ' ' . $this->_get('selected_time') . ":00'")					
					->findAll()
					->getData();
				if(count($_show_arr) > 0 && !$this->_is('venue_id'))
				{
					$venue_id = $_show_arr[0]['venue_id'];
				}else if($this->_is('venue_id')){
					
					$venue_id = $this->_get('venue_id');
				}
					
				if($venue_id != null)
				{
					$this->_set('venue_id', $venue_id);
					
					$ticket_arr = $pjShowModel->reset()
						->join('pjMultiLang', "t2.model='pjPrice' AND t2.foreign_id=t1.price_id AND t2.field='price_name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
						->join('pjPrice', "t1.price_id=t3.id", 'left outer')
						->select("t1.id, t1.price_id, t1.price, t2.content as ticket,
								  (     (
											SELECT SUM(TS.seats) 
											FROM `".pjSeatModel::factory()->getTable()."` AS `TS` 
											WHERE TS.venue_id='".$venue_id."' AND 
								              	TS.id IN ( SELECT(TSS.seat_id) FROM `".pjShowSeatModel::factory()->getTable()."` AS `TSS` WHERE TSS.show_id=t1.id)) - 
									    (
											IFNULL((SELECT SUM(TBS.cnt) 
											FROM `".$pjBookingShowModel->getTable()."` AS `TBS`
											WHERE TBS.show_id=t1.id AND TBS.booking_id IN (SELECT TB.id FROM `".$pjBookingModel->getTable()."` AS TB WHERE TB.event_id=".$this->_get('id')." AND TB.status<>'cancelled') ), 0)										
										)
								  ) as cnt_tickets")
						->where('t1.event_id', $this->_get('id'))
						->where("t1.date_time = '". $selected_date . ' ' . $this->_get('selected_time') . ":00'")
						->where("t1.venue_id", $venue_id)
						->findAll()
						->getData();
					
					$this->set('ticket_arr', $ticket_arr);
					
					$venue_arr = pjVenueModel::factory()->find($venue_id)->getData();
					
					$seat_arr = pjSeatModel::factory()
						->select("t1.*, (SELECT GROUP_CONCAT( TS.price_id SEPARATOR '~:~') FROM `".pjShowModel::factory()->getTable()."` AS TS WHERE TS.event_id='".$this->_get('id')."' AND TS.date_time = '". $selected_date . ' ' . $this->_get('selected_time') . ":00' AND TS.id IN (SELECT TSS.show_id FROM `".pjShowSeatModel::factory()->getTable()."` AS TSS WHERE TSS.seat_id=t1.id) ) AS price_id,
										(
											IFNULL((SELECT SUM(TBS.cnt)
											FROM `".$pjBookingShowModel->getTable()."` AS `TBS`
											WHERE TBS.show_id IN (SELECT TS.id FROM `".$pjShowModel->getTable()."` AS TS WHERE TS.event_id='".$this->_get('id')."' AND TS.date_time = '". $selected_date . ' ' . $this->_get('selected_time') . ":00') AND TBS.seat_id=t1.id AND TBS.booking_id IN (SELECT TB.id FROM `".$pjBookingModel->getTable()."` AS TB WHERE TB.event_id=".$this->_get('id')." AND TB.date_time='".$selected_date . ' ' . $this->_get('selected_time').":00' AND TB.status<>'cancelled')), 0)
										) 
									AS cnt_booked ")
						->where('t1.venue_id', $venue_id)
						->where("t1.id IN (SELECT TSS.seat_id FROM `".pjShowSeatModel::factory()->getTable()."` AS TSS WHERE TSS.show_id IN (SELECT TS.id FROM `".$pjShowModel->getTable()."` AS TS WHERE TS.event_id='".$this->_get('id')."' AND TS.date_time = '". $selected_date . ' ' . $this->_get('selected_time') . ":00') )")
						->findAll()
						->getData();
					
					$total_available_seats = $total_remaining_avaliable_seats = $total_booked_seats = 0;
					$seat_name_arr = array();
					foreach($seat_arr as $v)
					{
						$seat_name_arr[$v['id']] = $v['name'];
						$total_available_seats = $total_available_seats + $v['seats'];
						$total_booked_seats += $v['cnt_booked'];
					}
					$total_remaining_avaliable_seats = $total_available_seats - $total_booked_seats;
					$bs_arr = $pjBookingShowModel
						->reset()
						->select("SUM(t1.cnt) AS cnt_booked_seats")
						->where("(t1.booking_id IN (SELECT TB.id FROM `".$pjBookingModel->getTable()."` AS TB WHERE TB.status='confirmed' AND TB.date_time='".$selected_date . ' ' . $this->_get('selected_time').":00' AND TB.event_id='".$this->_get('id')."'))")
						->where("(t1.show_id IN (SELECT `TS`.`id` FROM `".$pjShowModel->getTable()."` AS `TS` WHERE `TS`.venue_id='".$venue_id."'))")
						->limit(1)
						->findAll()
						->getData();
					$cnt_booked_seats = 0;
					if(count($bs_arr) == 1)
					{
						$cnt_booked_seats = $bs_arr[0]['cnt_booked_seats'];
					}
					$this->set('venue_arr', $venue_arr);
					$this->set('seat_arr', $seat_arr);
					$this->set('seat_name_arr', $seat_name_arr);
					$this->set('seats_available', $cnt_booked_seats >= $total_available_seats ? false: true);
					$this->set('total_remaining_avaliable_seats', $total_remaining_avaliable_seats);
				}
				
				$this->set('arr', $arr);
				$this->set('hash_date', $hash_date);
				$this->set('selected_date', $selected_date);
				$this->set('hall_arr', $_show_arr);
				
				$this->set('status', 'OK');
			}else{
				$this->set('status', 'ERR');
			}
		}
	}
	
	public function pjActionCheckout()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			if (isset($_SESSION[$this->defaultStore]) && count($_SESSION[$this->defaultStore]) > 0)
			{
				if(isset($_POST['tb_checkout']))
				{
					if ((int) $this->option_arr['o_bf_include_captcha'] === 3 && (!isset($_POST['captcha']) ||
							!pjCaptcha::validate($_POST['captcha'], $_SESSION[$this->defaultCaptcha]) ))
					{
						pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 110, 'text' => __('system_212', true)));
					}
						
					$_SESSION[$this->defaultForm] = $_POST;

					pjAppController::jsonResponse(array('status' => 'OK', 'code' => 211, 'text' => __('system_211', true)));
				}else{
					$hash_date = date('Y-m-d');
					if(isset($_GET['date']) && !empty($_GET['date']))
					{
						$hash_date = pjUtil::formatDate($_GET['date'], $this->option_arr['o_date_format']);
					}
					$selected_date = $hash_date;
					if($this->_is('selected_date'))
					{
						$selected_date = $this->_get('selected_date');
					}
		
					$arr = pjEventModel::factory()
						->join('pjMultiLang', "t2.model='pjEvent' AND t2.foreign_id=t1.id AND t2.field='title' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
						->select('t1.*, t2.content as title')
						->find($this->_get('id'))
						->getData();
		
					
					$ticket_arr = pjShowModel::factory()
						->join('pjMultiLang', "t2.model='pjPrice' AND t2.foreign_id=t1.price_id AND t2.field='price_name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
						->join('pjPrice', "t1.price_id=t3.id", 'left outer')
						->select("t1.id, t1.price_id, t1.price, t2.content as ticket")
					  	->where('t1.event_id', $this->_get('id'))
					  	->where("t1.date_time = '". $selected_date . ' ' . $this->_get('selected_time') . ":00'")
					  	->where("t1.venue_id", $this->_get('venue_id'))
					  	->findAll()
					  	->getData();
					
					$this->set('ticket_arr', $ticket_arr);
					
					$price_arr = $this->calculatePrice($ticket_arr, $this->_get('tickets'));
					$this->set('price_arr', $price_arr);
					
					$seat_arr = pjSeatModel::factory()
						->where('t1.venue_id', $this->_get('venue_id'))
						->findAll()
						->getData();
					$seat_name_arr = array();
					foreach($seat_arr as $v)
					{
						$seat_name_arr[$v['id']] = $v['name'];
					}
					$this->set('seat_name_arr', $seat_name_arr);
		
					$this->set('country_arr', pjCountryModel::factory()
						->select('t1.*, t2.content AS name')
						->join('pjMultiLang', "t2.model='pjCountry' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
						->where('t1.status', 'T')
						->orderBy('`name` ASC')
						->findAll()
						->getData()
					);
					
					$this->set('arr', $arr);
					$this->set('hash_date', $hash_date);
					$this->set('selected_date', $selected_date);
				}
				$this->set('status', 'OK');
			}else{
				$this->set('status', 'ERR');
			}
		}
	}
	
	public function pjActionPreview()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			if (isset($_SESSION[$this->defaultStore]) && count($_SESSION[$this->defaultStore]) > 0)
			{
				if(isset($_POST['tb_checkout']))
				{
					$_SESSION[$this->defaultForm] = $_POST;
						
					pjAppController::jsonResponse(array('status' => 'OK', 'code' => 211, 'text' => __('system_211', true)));
				}else{
					$hash_date = date('Y-m-d');
					if(isset($_GET['date']) && !empty($_GET['date']))
					{
						$hash_date = pjUtil::formatDate($_GET['date'], $this->option_arr['o_date_format']);
					}
					$selected_date = $hash_date;
					if($this->_is('selected_date'))
					{
						$selected_date = $this->_get('selected_date');
					}
	
					$arr = pjEventModel::factory()
						->join('pjMultiLang', "t2.model='pjEvent' AND t2.foreign_id=t1.id AND t2.field='title' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
						->select('t1.*, t2.content as title')
						->find($this->_get('id'))
						->getData();
	
					$ticket_arr = pjShowModel::factory()
						->join('pjMultiLang', "t2.model='pjPrice' AND t2.foreign_id=t1.price_id AND t2.field='price_name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
						->join('pjPrice', "t1.price_id=t3.id", 'left outer')
						->select("t1.id, t1.price_id, t1.price, t2.content as ticket")
						->where('t1.event_id', $this->_get('id'))
						->where("t1.date_time = '". $selected_date . ' ' . $this->_get('selected_time') . ":00'")
						->where("t1.venue_id", $this->_get('venue_id'))
						->findAll()
						->getData();

					$this->set('ticket_arr', $ticket_arr);

					$price_arr = $this->calculatePrice($ticket_arr, $this->_get('tickets'));
					$this->set('price_arr', $price_arr);
					
					$seat_arr = pjSeatModel::factory()
						->where('t1.venue_id', $this->_get('venue_id'))
						->findAll()
						->getData();
					$seat_name_arr = array();
					foreach($seat_arr as $v)
					{
						$seat_name_arr[$v['id']] = $v['name'];
					}
					$this->set('seat_name_arr', $seat_name_arr);
					
					$country_arr = pjCountryModel::factory()
						->select('t1.id, t2.content AS country_title')
						->join('pjMultiLang', "t2.model='pjCountry' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
						->find($_SESSION[$this->defaultForm]['c_country'])
						->getData();
					
					$this->set('arr', $arr);
					$this->set('hash_date', $hash_date);
					$this->set('selected_date', $selected_date);
					$this->set('country_arr', $country_arr);
				}
				$this->set('status', 'OK');
			}else{
				$this->set('status', 'ERR');
			}
		}
	}
	
	public function pjActionCancel()
	{
		$this->setLayout('pjActionCancel');
	
		$pjBookingModel = pjBookingModel::factory();
	
		if (isset($_POST['booking_cancel']))
		{
			$booking_arr = pjBookingModel::factory()->find($_POST['id'])->getData();
			if (count($booking_arr) > 0)
			{
				$sql = "UPDATE `".$pjBookingModel->getTable()."` SET status = 'cancelled' WHERE SHA1(CONCAT(`id`, `created`, '".PJ_SALT."')) = '" . $_POST['hash'] . "'";
	
				$pjBookingModel->reset()->execute($sql);
	
				$booking_arr = pjAppController::pjActionGetBookingDetails($_POST['id']);
	
				pjFront::pjActionConfirmSend($this->option_arr, $booking_arr, PJ_SALT, 'cancel');
	
				pjUtil::redirect($_SERVER['PHP_SELF'] . '?controller=pjFront&action=pjActionCancel&err=200');
			}
		}else{
			if (isset($_GET['hash']) && isset($_GET['id']))
			{
				$arr = pjAppController::pjActionGetBookingDetails($_GET['id']);
				if (count($arr) == 0)
				{
					$this->set('status', 2);
				}else{
					if ($arr['status'] == 'cancelled')
					{
						$this->set('status', 4);
					}else{
						$hash = sha1($arr['id'] . $arr['created'] . PJ_SALT);
						if ($_GET['hash'] != $hash)
						{
							$this->set('status', 3);
						}else{
								
							$this->set('arr', $arr);
						}
					}
				}
			}else if (!isset($_GET['err'])) {
				$this->set('status', 1);
			}
		}
	}
	
	public function pjActionGetTime()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$selected_date = date('Y-m-d');
			if(isset($_GET['date']) && !empty($_GET['date']))
			{
				$selected_date = pjUtil::formatDate($_GET['date'], $this->option_arr['o_date_format']);
			}
			$show_arr = pjShowModel::factory()
				->where('t1.event_id', $_GET['id'])
				->where("DATE_FORMAT(t1.date_time,'%Y-%m-%d') = '$selected_date' AND t1.date_time >= NOW()")
				->orderBy("date_time ASC")
				->findAll()
				->getData();
			$time_arr = array();
			foreach($show_arr as $v)
			{
				$time = date('H:i', strtotime($v['date_time']));
				if(strtotime($v['date_time']) > time() + $this->option_arr['o_booking_earlier'] * 60)
				{
					if(!in_array($time, $time_arr))
					{
						$time_arr[] = $time;
					}
				}
			}
			$this->set('selected_date', $selected_date);
			$this->set('time_arr', $time_arr);
		}
	}
	
	public function pjActionSaveDateTime()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$response = array();
			$this->_set('id', $_POST['id']);
			$this->_set('selected_date', pjUtil::formatDate($_POST['selected_date'], $this->option_arr['o_date_format']));
			$this->_set('selected_time', $_POST['selected_time']);
			$this->_set('back_to', $_POST['back_to']);
			if($this->_is('venue_id'))
			{
			    unset($_SESSION[$this->defaultStore]['venue_id']);
			}
			$response['code'] = 200;
			pjAppController::jsonResponse($response);
			exit;
		}
	}
	
	public function pjActionSaveSeats()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$response = array();
			if($this->_is('tickets'))
			{
				unset($_SESSION[$this->defaultStore]['tickets']);
			}
			if($this->_is('seat_id'))
			{
				unset($_SESSION[$this->defaultStore]['seat_id']);
			}
			$this->_set('tickets', $_POST['tickets']);
			$this->_set('seat_id', $_POST['seat_id']);
			$response['code'] = 200;
			pjAppController::jsonResponse($response);
			exit;
		}
	}
	
	public function pjActionSetVenue()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$response = array();
			$this->_set('venue_id', $_GET['venue_id']);
			if($this->_is('tickets'))
			{
				unset($_SESSION[$this->defaultStore]['tickets']);
			}
			if($this->_is('seat_id'))
			{
				unset($_SESSION[$this->defaultStore]['seat_id']);
			}
			$response['code'] = 200;
			pjAppController::jsonResponse($response);
			exit;
		}
	}
	
	public function pjActionSaveBooking()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			
			if (!isset($_POST['tb_preview']) || !isset($_SESSION[$this->defaultForm]) || empty($_SESSION[$this->defaultForm]) || !isset($_SESSION[$this->defaultStore]) || empty($_SESSION[$this->defaultStore]))
			{
				pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 109, 'text' => __('system_109', true)));
			}
				
			if ((int) $this->option_arr['o_bf_include_captcha'] === 3 && (!isset($_SESSION[$this->defaultForm]['captcha']) ||
					!pjCaptcha::validate($_SESSION[$this->defaultForm]['captcha'], $_SESSION[$this->defaultCaptcha]) ))
			{
				pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 110, 'text' => __('system_110', true)));
			}
			if(isset($_SESSION[$this->defaultCaptcha]))
			{
			    unset($_SESSION[$this->defaultCaptcha]);
			    unset($_SESSION[$this->defaultForm]['captcha']);
			}
			
			$ticket_arr = pjShowModel::factory()
				->join('pjPrice', "t1.price_id=t2.id", 'left outer')
				->select("t1.id, t1.price_id, t1.price")
				->where('t1.event_id', $this->_get('id'))
				->where("t1.date_time = '". $this->_get('selected_date') . ' ' . $this->_get('selected_time') . ":00'")
				->where("t1.venue_id", $this->_get('venue_id'))
				->findAll()
				->getData();
			
			$show_id_arr = array();
			$_price_arr = array();
			foreach($ticket_arr as $v)
			{
				$show_id_arr[$v['price_id']] = $v['id'];
				$_price_arr[$v['price_id']] = $v['price'];
			}
			
			$STORE = @$_SESSION[$this->defaultStore];
			$FORM = @$_SESSION[$this->defaultForm];
			
			$pjBookingShowModel = pjBookingShowModel::factory();
				
			$booking_event_id = $this->_get('id');
			$booking_date_time = $this->_get('selected_date') . ' ' . $this->_get('selected_time') . ':00';
				
			$booking_id_arr = pjBookingModel::factory()
				->where('event_id', $booking_event_id)
				->where('date_time', $booking_date_time)
				->where('status <>', 'cancelled')
				->where(sprintf("(t1.id IN(SELECT `TBS`.booking_id FROM `%s` AS `TBS` WHERE `TBS`.`seat_id` IN(SELECT `TS`.id FROM `%s` AS `TS` WHERE `TS`.venue_id='%u') ))", $pjBookingShowModel->getTable(), pjSeatModel::factory()->getTable(), $this->_get('venue_id')))
				->findAll()
				->getDataPair(null, 'id');

			$all_seats_arr = pjSeatModel::factory()
		        ->where('t1.venue_id', $this->_get('venue_id'))
				->findAll()
				->getData();
			
			$all_seats_cnt = array();
			foreach($all_seats_arr as $kk=>$vv){
				$all_seats_cnt[$vv['id']] = $vv['seats'];
			}
			
			if(!empty($booking_id_arr))
			{
				foreach($STORE['seat_id'] as $price_id => $seat_arr)
				{
					foreach($seat_arr as $seat_id => $cnt)
					{
						$cnt_booked = $pjBookingShowModel
							->reset()
							->join('pjShow', 't2.id = t1.show_id')
							->whereIn('t1.booking_id', $booking_id_arr)
							->where('t1.seat_id', $seat_id)
							->where('t2.event_id', $booking_event_id)
							->where('t2.date_time', $booking_date_time)
							->where('t2.venue_id', $this->_get('venue_id'))
							->findCount()
							->getData();
						if($all_seats_cnt[$seat_id]==1 && $cnt_booked > 0)
						{
							$system_text = __('system_118', true);
							$system_text = str_replace("[STAG]", "<a href='#' class='tbStartOverButton'>", $system_text);
							$system_text = str_replace("[ETAG]", "</a>", $system_text);
							pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 118, 'text' => $system_text));
							exit;
						} elseif($all_seats_cnt[$seat_id]>1 && $cnt_booked > $all_seats_cnt[$seat_id]) {
							$system_text = __('system_118', true);
							$system_text = str_replace("[STAG]", "<a href='#' class='tbStartOverButton'>", $system_text);
							$system_text = str_replace("[ETAG]", "</a>", $system_text);
							pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 118, 'text' => $system_text));
							exit;
						}
					}
				}
			}
			
			$price_arr = $this->calculatePrice($ticket_arr, $this->_get('tickets'));
			
			$data = array();
			
			$uuid = pjUtil::uuid();
			$data['uuid'] = $uuid;
			$data['event_id'] = $booking_event_id;
			$data['date_time'] = $booking_date_time;
			$data['sub_total'] = $price_arr['sub_total'];
			$data['tax'] = $price_arr['tax'];
			$data['total'] = $price_arr['total'];
			$data['deposit'] = $price_arr['deposit'];
			$data['status'] = $this->option_arr['o_booking_status'];
			$data['ip'] = pjUtil::getClientIp();
			
			$payment = 'none';
			if(isset($FORM['payment_method']))
			{
				if (isset($FORM['payment_method'])){
					$payment = $FORM['payment_method'];
				}
			}
			
			$pjBookingModel = pjBookingModel::factory();
			
			$id = $pjBookingModel->setAttributes(array_merge($FORM, $data))->insert()->getInsertId();
			if ($id !== false && (int) $id > 0)
			{
				
				$pjBookingTicketModel = pjBookingTicketModel::factory();
				foreach($STORE['seat_id'] as $price_id => $seat_arr)
				{
					$bs_data = array();
					$bt_data = array();
					
					$bs_data['booking_id'] = $id;
					$bs_data['show_id'] = $show_id_arr[$price_id];
					$bs_data['price_id'] = $price_id;
					$bs_data['price'] = $_price_arr[$price_id];
					
					$bt_data['booking_id'] = $id;
					$bt_data['price_id'] = $price_id;
					$bt_data['unit_price'] = $_price_arr[$price_id];
					$bt_data['is_used'] = 'F';
					
					foreach($seat_arr as $seat_id => $cnt)
					{
						$bs_data['seat_id'] = $seat_id;
						$bs_data['cnt'] = $cnt;
						$pjBookingShowModel->reset()->setAttributes($bs_data)->insert();
						
						$bt_data['seat_id'] = $seat_id;
						for($i = 1; $i <= $cnt; $i++)
						{
							$bt_data['ticket_id'] = $uuid . '-' . $seat_id . '-' . $i;
							$pjBookingTicketModel->reset()->setAttributes($bt_data)->insert();
						}
					}
				}
				
				$arr = pjAppController::pjActionGetBookingDetails($id);
				
				$pdata = array();
				$pdata['booking_id'] = $id;
				$pdata['payment_method'] = $payment;
				$pdata['payment_type'] = 'online';
				$pdata['amount'] = $arr['deposit'];
				$pdata['status'] = 'notpaid';
				pjBookingPaymentModel::factory()->setAttributes($pdata)->insert();
				pjAppController::buildPdfTickets($arr);
				$this->pjActionGenerateInvoice($arr);
				pjFront::pjActionConfirmSend($this->option_arr, $arr, PJ_SALT, 'confirm');
				
				unset($_SESSION[$this->defaultStore]);
				unset($_SESSION[$this->defaultForm]);
								
				$json = array('code' => 200, 'text' => '', 'booking_id' => $id, 'payment' => $payment);
				pjAppController::jsonResponse($json);
			}else {
				pjAppController::jsonResponse(array('code' => 'ERR', 'code' => 119, 'text' => __('system_119', true)));
			}
		}
	}
	
	public function pjActionGetPaymentForm()
	{
		$this->setAjax(true);
		
		if ($this->isXHR())
		{
			$arr = pjBookingModel::factory()
				->join('pjMultiLang', "t2.model='pjEvent' AND t2.foreign_id=t1.event_id AND t2.field='title' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
				->select('t1.*, t2.content as title')
				->find($_GET['booking_id'])
				->getData();
			
			$invoice_arr = pjInvoiceModel::factory()->where('t1.order_id', $arr['uuid'])->findAll()->limit(1)->getData();
			if (!empty($invoice_arr))
			{
				$invoice_arr = $invoice_arr[0];
				
				switch ($arr['payment_method'])
				{
					case 'paypal':
						$this->set('params', array(
							'name' => 'tbPaypal',
							'id' => 'tbPaypal',
							'business' => $this->option_arr['o_paypal_address'],
							'client_id' => $this->option_arr['o_paypal_client_id'],
							'client_secret' => $this->option_arr['o_paypal_client_secret'],
							'item_name' => pjSanitize::html($arr['title']),
							'custom' => $invoice_arr['uuid'],
							'amount' => $invoice_arr['paid_deposit'],
							'currency_code' => $this->option_arr['o_currency'],
							'return' => $this->option_arr['o_thank_you_page'],
							'failure_url' => $this->option_arr['o_paypal_cancel_url'],
							'notify_url' => PJ_INSTALL_URL . 'index.php?controller=pjFront&action=pjActionConfirmPaypal',
							'target' => '_self',
							'charset' => 'utf-8'
						));
						break;
					case 'authorize':
						$this->set('params', array(
							'name' => 'tbAuthorize',
							'id' => 'tbAuthorize',
							'target' => '_self',
							'timezone' => $this->option_arr['o_authorize_timezone'],
							'transkey' => $this->option_arr['o_authorize_transkey'],
							'private_key' => $this->option_arr['o_authorize_hash'],
							'x_login' => $this->option_arr['o_authorize_merchant_id'],
							'x_description' => pjSanitize::html($arr['title']),
							'x_amount' => $invoice_arr['paid_deposit'],
							'x_invoice_num' => $invoice_arr['uuid'],
							'x_receipt_link_url' => $this->option_arr['o_thank_you_page'],
							'x_relay_url' => PJ_INSTALL_URL . 'index.php?controller=pjFront&action=pjActionConfirmAuthorize'
						));
						break;
				}
			}
			$this->set('arr', $arr);
			$this->set('get', $_GET);
		}
	}
	
	public function pjActionConfirmAuthorize()
	{
	    header('HTTP/1.1 200 OK');
	    
		$this->setAjax(true);
		
		if (pjObject::getPlugin('pjAuthorize') === NULL)
		{
			$this->log('Authorize.NET plugin not installed');
			exit;
		}
		
		$pjInvoiceModel = pjInvoiceModel::factory();
		$invoice_arr = $pjInvoiceModel
			->where('t1.uuid', $_POST['x_invoice_num'])
			->limit(1)
			->findAll()
			->getData();
		
		if (!empty($invoice_arr))
		{						
			$invoice_arr = $invoice_arr[0];
			$booking_arr = pjBookingModel::factory()
				->where('t1.uuid', $invoice_arr['order_id'])
				->limit(1)
				->findAll()
				->getData();
			if (!empty($booking_arr))
			{
				$arr = pjAppController::pjActionGetBookingDetails($booking_arr[0]['id']);
				if (count($arr) == 0)
				{
					$this->log('No such booking');
					pjUtil::redirect($this->option_arr['o_thank_you_page']);
				}					
				
				if (count($arr) > 0)
				{
					$params = array(
						'transkey' => $this->option_arr['o_authorize_transkey'],
						'x_login' => $this->option_arr['o_authorize_merchant_id'],
					    'private_key' => $this->option_arr['o_authorize_hash'],
						'key' => md5($this->option_arr['private_key'] . PJ_SALT)
					);
					
					$response = $this->requestAction(array('controller' => 'pjAuthorize', 'action' => 'pjActionConfirm', 'params' => $params), array('return'));
					if ($response !== FALSE && $response['status'] === 'OK')
					{
					    pjBookingModel::factory()->reset()->set('id', $response['transaction_id'])
							->modify(array('status' => $this->option_arr['o_payment_status'], 'processed_on' => ':NOW()'));
		
						pjBookingPaymentModel::factory()->setAttributes(array('booking_id' => $response['transaction_id'], 'payment_type' => 'online'))->limit(1)
														->modifyAll(array('status' => 'paid'));
						$pjInvoiceModel
							->reset()
							->set('id', $invoice_arr['id'])
							->modify(array('status' => 'paid', 'modified' => ':NOW()'));
							
						pjFront::pjActionConfirmSend($this->option_arr, $arr, PJ_SALT, 'payment');
						
					} elseif (!$response) {
						$this->log('Authorization failed');
					} else {
						$this->log('Booking not confirmed. ' . $response['response_reason_text']);
					}
				}
			}else{
				$this->log('No such booking');
			}
		}else{
			$this->log('Invoice not found');
		}
		?>
		<script type="text/javascript">window.location.href="<?php echo $this->option_arr['o_thank_you_page']; ?>";</script>
		<?php
		return;
	}
	
	public function pjActionConfirmPaypal()
	{
		$this->setAjax(true);
		
		if (pjObject::getPlugin('pjPaypal') === NULL)
		{
			$this->log('Paypal plugin not installed');
			pjAppController::jsonResponse(array('status' => 'ERR', 'text' => 'Paypal plugin not installed'));
		}
		$input = file_get_contents('php://input');
		$post = json_decode($input, true);
		if ($post) {
		    $_REQUEST = array_merge($_REQUEST, $post);
		}
		
		$pjInvoiceModel = pjInvoiceModel::factory();
		$invoice_arr = $pjInvoiceModel
		->where('t1.uuid', $_REQUEST['custom'])
			->limit(1)
			->findAll()
			->getData();
		if (!empty($invoice_arr))
		{
			$invoice_arr = $invoice_arr[0];
			$booking_arr = pjBookingModel::factory()
				->where('t1.uuid', $invoice_arr['order_id'])
				->limit(1)
				->findAll()
				->getData();
			if (!empty($booking_arr))
			{
				$arr = pjAppController::pjActionGetBookingDetails($booking_arr[0]['id']);				
				if (count($arr) == 0)
				{
					$this->log('No such booking');
					pjAppController::jsonResponse(array('status' => 'ERR', 'text' => 'No such booking'));
				}					
				
				$params = array(
				    'request'		=> $_REQUEST,
				    'cancel_hash'	=> sha1($arr['uuid'].strtotime($arr['created']).PJ_SALT),
					'txn_id' => @$arr['txn_id'],
					'paypal_address' => $this->option_arr['o_paypal_address'],
					'deposit' => @$invoice_arr['paid_deposit'],
					'currency' => $this->option_arr['o_currency'],
					'key' => md5($this->option_arr['private_key'] . PJ_SALT)
				);
				
				$response = $this->requestAction(array('controller' => 'pjPaypal', 'action' => 'pjActionConfirm', 'params' => $params), array('return'));
				if ($response !== FALSE && $response['status'] === 'OK')
				{
					$this->log('Booking confirmed');
					pjBookingModel::factory()->reset()->set('id', $arr['id'])->modify(array(
						'status' => $this->option_arr['o_payment_status'],
						'txn_id' => $response['transaction_id'],
						'processed_on' => ':NOW()'
					));
					pjBookingPaymentModel::factory()->where('booking_id', $arr['id'])->where('payment_type', 'online')->limit(1)->modifyAll(array('status' => 'paid'));
					
					$pjInvoiceModel
						->reset()
						->set('id', $invoice_arr['id'])
						->modify(array('status' => 'paid', 'modified' => ':NOW()'));
					
					pjFront::pjActionConfirmSend($this->option_arr, $arr, PJ_SALT, 'payment');	
					pjAppController::jsonResponse(array('status' => 'OK', 'text' => 'Booking confirmed'));
				} elseif (!$response) {
					$this->log('Authorization failed');
					pjAppController::jsonResponse(array('status' => 'ERR', 'text' => 'Authorization failed'));
				} else {
					$this->log('Booking not confirmed');
					pjAppController::jsonResponse(array('status' => 'ERR', 'text' => 'Booking not confirmed'));
				}
			}else{
				$this->log('No such booking');
				pjAppController::jsonResponse(array('status' => 'ERR', 'text' => 'No such booking'));
			}
		}else{
			$this->log('Invoice not found');
			pjAppController::jsonResponse(array('status' => 'ERR', 'text' => 'Invoice not found'));
		}
	}	

	public function pjActionConfirmSend($option_arr, $booking_arr, $salt, $opt)
	{
		$Email = new pjEmail();
		if ($option_arr['o_send_email'] == 'smtp')
		{
			$Email
				->setTransport('smtp')
				->setSmtpHost($option_arr['o_smtp_host'])
				->setSmtpPort($option_arr['o_smtp_port'])
				->setSmtpUser($option_arr['o_smtp_user'])
				->setSmtpPass($option_arr['o_smtp_pass'])
				->setSender($option_arr['o_smtp_user'])
			;
		}
		$Email->setContentType('text/html');
		
		$admin_email = $this->getAdminEmail();
		$admin_phone = $this->getAdminPhone();
		$from_email = $this->getFromEmail($option_arr);
		
		$tokens = pjAppController::getData($option_arr, $booking_arr, PJ_SALT, $this->getLocaleId());
		
		$pjMultiLangModel = pjMultiLangModel::factory();
		
		$locale_id = isset($booking_arr['locale_id']) && (int) $booking_arr['locale_id'] > 0 ? (int) $booking_arr['locale_id'] : $this->getLocaleId();
		
		if ($option_arr['o_email_payment'] == 1 && $opt == 'payment')
		{
			$lang_message = $pjMultiLangModel->reset()->select('t1.*')
				->where('t1.model','pjOption')
				->where('t1.locale', $locale_id)
				->where('t1.field', 'o_email_payment_message')
				->limit(0, 1)
				->findAll()->getData();
			$lang_subject = $pjMultiLangModel->reset()->select('t1.*')
				->where('t1.model','pjOption')
				->where('t1.locale', $locale_id)
				->where('t1.field', 'o_email_payment_subject')
				->limit(0, 1)
				->findAll()->getData();
		
			if (count($lang_message) === 1 && count($lang_subject) === 1 && !empty($lang_subject[0]['content']) && !empty($lang_message[0]['content']))
			{
				$message = str_replace($tokens['search'], $tokens['replace'], $lang_message[0]['content']);
		
				$Email
					->setTo($booking_arr['c_email'])
					->setFrom($from_email)
					->setSubject($lang_subject[0]['content'])
					->send($message);
			}
		}
		if ($option_arr['o_admin_email_payment'] == 1 && $opt == 'payment')
		{
			$lang_message = $pjMultiLangModel->reset()->select('t1.*')
				->where('t1.model','pjOption')
				->where('t1.locale', $locale_id)
				->where('t1.field', 'o_admin_email_payment_message')
				->limit(0, 1)
				->findAll()->getData();
			$lang_subject = $pjMultiLangModel->reset()->select('t1.*')
				->where('t1.model','pjOption')
				->where('t1.locale', $locale_id)
				->where('t1.field', 'o_admin_email_payment_subject')
				->limit(0, 1)
				->findAll()->getData();
		
			if (count($lang_message) === 1 && count($lang_subject) === 1 && !empty($lang_subject[0]['content']) && !empty($lang_message[0]['content']))
			{
				$message = str_replace($tokens['search'], $tokens['replace'], $lang_message[0]['content']);
		
				$Email
				->setTo($admin_email)
				->setFrom($from_email)
				->setSubject($lang_subject[0]['content'])
				->send($message);
			}
		}
		if(!empty($admin_phone) && $opt == 'payment')
		{
			$lang_message = $pjMultiLangModel->reset()->select('t1.*')
				->where('t1.model','pjOption')
				->where('t1.locale', $locale_id)
				->where('t1.field', 'o_admin_sms_payment_message')
				->limit(0, 1)
				->findAll()->getData();
			if (count($lang_message) === 1 && !empty($lang_message[0]['content']))
			{
				$message = str_replace($tokens['search'], $tokens['replace'], $lang_message[0]['content']);
				if($message != '')
				{
					$params = array(
							'text' => $message,
							'key' => md5($option_arr['private_key'] . PJ_SALT)
					);
					$params['number'] = $admin_phone;
					$this->requestAction(array('controller' => 'pjSms', 'action' => 'pjActionSend', 'params' => $params), array('return'));
				}
			}
		}
		
		if ($option_arr['o_email_confirmation'] == 1 && $opt == 'confirm')
		{
			$lang_message = $pjMultiLangModel->reset()->select('t1.*')
				->where('t1.model','pjOption')
				->where('t1.locale', $locale_id)
				->where('t1.field', 'o_email_confirmation_message')
				->limit(0, 1)
				->findAll()->getData();
			$lang_subject = $pjMultiLangModel->reset()->select('t1.*')
				->where('t1.model','pjOption')
				->where('t1.locale', $locale_id)
				->where('t1.field', 'o_email_confirmation_subject')
				->limit(0, 1)
				->findAll()->getData();
				
			if (count($lang_message) === 1 && count($lang_subject) === 1 && !empty($lang_subject[0]['content']) && !empty($lang_message[0]['content']))
			{
				$message = str_replace($tokens['search'], $tokens['replace'], $lang_message[0]['content']);
					
				$Email
					->setTo($booking_arr['c_email'])
					->setFrom($from_email)
					->setSubject($lang_subject[0]['content'])
					->send($message);
			}
		}
		if ($option_arr['o_admin_email_confirmation'] == 1 && $opt == 'confirm')
		{
			$lang_message = $pjMultiLangModel->reset()->select('t1.*')
				->where('t1.model','pjOption')
				->where('t1.locale', $locale_id)
				->where('t1.field', 'o_admin_email_confirmation_message')
				->limit(0, 1)
				->findAll()->getData();
			$lang_subject = $pjMultiLangModel->reset()->select('t1.*')
				->where('t1.model','pjOption')
				->where('t1.locale', $locale_id)
				->where('t1.field', 'o_admin_email_confirmation_subject')
				->limit(0, 1)
				->findAll()->getData();
				
			if (count($lang_message) === 1 && count($lang_subject) === 1 && !empty($lang_subject[0]['content']) && !empty($lang_message[0]['content']))
			{
				$message = str_replace($tokens['search'], $tokens['replace'], $lang_message[0]['content']);
				$Email
					->setTo($admin_email)
					->setFrom($from_email)
					->setSubject($lang_subject[0]['content'])
					->send($message);
			}
		}
		if(!empty($booking_arr['c_phone']) && $opt == 'confirm')
		{
			$lang_message = $pjMultiLangModel->reset()->select('t1.*')
				->where('t1.model','pjOption')
				->where('t1.locale', $locale_id)
				->where('t1.field', 'o_sms_confirmation_message')
				->limit(0, 1)
				->findAll()->getData();
			if (count($lang_message) === 1 && !empty($lang_message[0]['content']))
			{
				$message = str_replace($tokens['search'], $tokens['replace'], $lang_message[0]['content']);
				if($message != '')
				{
					$params = array(
							'text' => $message,
							'key' => md5($option_arr['private_key'] . PJ_SALT)
					);
					$params['number'] = $booking_arr['c_phone'];
					$this->requestAction(array('controller' => 'pjSms', 'action' => 'pjActionSend', 'params' => $params), array('return'));
				}
			}
		}
		if(!empty($admin_phone) && $opt == 'confirm')
		{
			$lang_message = $pjMultiLangModel->reset()->select('t1.*')
				->where('t1.model','pjOption')
				->where('t1.locale', $locale_id)
				->where('t1.field', 'o_admin_sms_confirmation_message')
				->limit(0, 1)
				->findAll()->getData();
			if (count($lang_message) === 1 && !empty($lang_message[0]['content']))
			{
				$message = str_replace($tokens['search'], $tokens['replace'], $lang_message[0]['content']);
				if($message != '')
				{
					$params = array(
							'text' => $message,
							'key' => md5($option_arr['private_key'] . PJ_SALT)
					);
					$params['number'] = $admin_phone;
					$this->requestAction(array('controller' => 'pjSms', 'action' => 'pjActionSend', 'params' => $params), array('return'));
				}
			}
		}
		
		if ($option_arr['o_email_cancel'] == 1 && $opt == 'cancel')
		{
			$lang_message = $pjMultiLangModel->reset()->select('t1.*')
				->where('t1.model','pjOption')
				->where('t1.locale', $locale_id)
				->where('t1.field', 'o_email_cancel_message')
				->limit(0, 1)
				->findAll()->getData();
			$lang_subject = $pjMultiLangModel->reset()->select('t1.*')
				->where('t1.model','pjOption')
				->where('t1.locale', $locale_id)
				->where('t1.field', 'o_email_cancel_subject')
				->limit(0, 1)
				->findAll()->getData();
				
			if (count($lang_message) === 1 && count($lang_subject) === 1 && !empty($lang_subject[0]['content']) && !empty($lang_message[0]['content']))
			{
				$message = str_replace($tokens['search'], $tokens['replace'], $lang_message[0]['content']);
		
				$Email
					->setTo($booking_arr['c_email'])
					->setFrom($from_email)
					->setSubject($lang_subject[0]['content'])
					->send($message);
			}
		}
		if ($option_arr['o_admin_email_cancel'] == 1 && $opt == 'cancel')
		{
			$lang_message = $pjMultiLangModel->reset()->select('t1.*')
				->where('t1.model','pjOption')
				->where('t1.locale', $locale_id)
				->where('t1.field', 'o_admin_email_cancel_message')
				->limit(0, 1)
				->findAll()->getData();
			$lang_subject = $pjMultiLangModel->reset()->select('t1.*')
				->where('t1.model','pjOption')
				->where('t1.locale', $locale_id)
				->where('t1.field', 'o_admin_email_cancel_subject')
				->limit(0, 1)
				->findAll()->getData();
				
			if (count($lang_message) === 1 && count($lang_subject) === 1 && !empty($lang_subject[0]['content']) && !empty($lang_message[0]['content']))
			{
				$message = str_replace($tokens['search'], $tokens['replace'], $lang_message[0]['content']);
		
				$Email
					->setTo($admin_email)
					->setFrom($from_email)
					->setSubject($lang_subject[0]['content'])
					->send($message);
			}
		}
	}
	
	public function isXHR()
	{
		return parent::isXHR() || isset($_SERVER['HTTP_ORIGIN']);
	}
	
	static protected function allowCORS()
	{
		$origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '*';
		header('P3P: CP="ALL DSP COR CUR ADM TAI OUR IND COM NAV INT"');
		header("Access-Control-Allow-Origin: $origin");
		header("Access-Control-Allow-Credentials: true");
		header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
		header("Access-Control-Allow-Headers: Origin, X-Requested-With");
	}
}
?>