<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjAdminBookings extends pjAdmin
{                  
	public function pjActionCheckUniqueId()
	{
		$this->setAjax(true);
		
		if ($this->isXHR() && isset($_GET['uuid']))
		{
			$pjBookingModel = pjBookingModel::factory();
			if (isset($_GET['id']) && (int) $_GET['id'] > 0)
			{
				$pjBookingModel->where('t1.id !=', $_GET['id']);
			}
			echo $pjBookingModel->where('t1.uuid', $_GET['uuid'])->findCount()->getData() == 0 ? 'true' : 'false';
		}
		exit;
	}
	
	public function pjActionGetBooking()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$pjBookingModel = pjBookingModel::factory()
				->join('pjMultiLang', "t2.model='pjEvent' AND t2.foreign_id=t1.event_id AND t2.field='title' AND t2.locale='".$this->getLocaleId()."'", 'left outer');;
			
			if (isset($_GET['q']) && !empty($_GET['q']))
			{
				$q = pjObject::escapeString($_GET['q']);
				$pjBookingModel->where('t1.uuid LIKE', "%$q%");
				$pjBookingModel->orWhere('t1.c_name LIKE', "%$q%");
				$pjBookingModel->orWhere('t1.c_email LIKE', "%$q%");
				$pjBookingModel->orWhere('t1.c_phone LIKE', "%$q%");
			}
						
			if (isset($_GET['event_id']) && (int) $_GET['event_id'] > 0)
			{
				$pjBookingModel->where('t1.event_id', $_GET['event_id']);
			}
			if (isset($_GET['time']) && $_GET['time'] != '')
			{
				$pjBookingModel->where("DATE_FORMAT(t1.date_time,'%H:%i')", $_GET['time']);
			}
			if (isset($_GET['dt']) && (int) $_GET['dt'] > 0)
			{
				$pjBookingModel->where("t1.date_time", date('Y-m-d H:i:s', $_GET['dt']));
			}
			if (isset($_GET['uuid']) && $_GET['uuid'] != '')
			{
				$uuid = pjObject::escapeString($_GET['uuid']);
				$pjBookingModel->where('t1.uuid LIKE', "%$uuid%");
			}
			if (isset($_GET['c_name']) && $_GET['c_name'] != '')
			{
				$customer_name = pjObject::escapeString($_GET['c_name']);
				$pjBookingModel->where('t1.c_name LIKE', "%$customer_name%");
			}
			if (isset($_GET['c_email']) && $_GET['c_email'] != '')
			{
				$customer_email = pjObject::escapeString($_GET['c_email']);
				$pjBookingModel->where('t1.c_email LIKE', "%$customer_email%");
			}
			if (isset($_GET['from_price']) && $_GET['from_price'] != '')
			{
				$from = $_GET['from_price'];
				$pjBookingModel->where("t1.total >=" , $from);
			}
			if (isset($_GET['to_price'])  && $_GET['to_price'] != '')
			{
				$to = $_GET['to_price'];
				$pjBookingModel->where("t1.total <=" , $to);
			}
			if (isset($_GET['status']) && !empty($_GET['status']))
			{
				$pjBookingModel->where('t1.status', $_GET['status']);
			}
			
			$column = 'created';
			$direction = 'DESC';
			if (isset($_GET['direction']) && isset($_GET['column']) && in_array(strtoupper($_GET['direction']), array('ASC', 'DESC')))
			{
				$column = $_GET['column'];
				$direction = strtoupper($_GET['direction']);
			}

			$total = $pjBookingModel->findCount()->getData();
			$rowCount = isset($_GET['rowCount']) && (int) $_GET['rowCount'] > 0 ? (int) $_GET['rowCount'] : 10;
			$pages = ceil($total / $rowCount);
			$page = isset($_GET['page']) && (int) $_GET['page'] > 0 ? intval($_GET['page']) : 1;
			$offset = ((int) $page - 1) * $rowCount;
			if ($page > $pages)
			{
				$page = $pages;
			}
			
			$booking_id_arr = $pjBookingModel->findAll()->getDataPair('id', 'id');
			$ticket_info_arr =!empty($booking_id_arr) ? $this->getTicketInfo($booking_id_arr) : array('ticket_cnt_arr' => array(), 'ticket_name_arr' => array());
			$ticket_cnt_arr = $ticket_info_arr['ticket_cnt_arr'];
			$ticket_name_arr = $ticket_info_arr['ticket_name_arr'];
			
			$data = $pjBookingModel
				->select("t1.id, t1.uuid, t1.c_name, t1.date_time, t1.status, t2.content as title")
				->orderBy("$column $direction")->limit($rowCount, $offset)->findAll()->getData();
			
			$data = pjSanitize::clean($data);
			foreach($data as $k => $v)
			{
				$_arr = array();
				if(isset($ticket_cnt_arr[$v['id']]))
				{
					foreach($ticket_cnt_arr[$v['id']] as $price_id => $cnt)
					{
						$_arr[] = $ticket_name_arr[$v['id']][$price_id] . ' x ' . $cnt; 
					}
				}
				$v['date_time'] = date($this->option_arr['o_date_format'], strtotime($v['date_time'])) . ', ' . date($this->option_arr['o_time_format'], strtotime($v['date_time']));
				$pdf = '';
				if($v['status'] == 'confirmed' && is_file(PJ_INSTALL_PATH . PJ_UPLOAD_PATH . 'tickets/pdfs/p_' . $v['uuid'] . '.pdf'))
				{
					$pdf = PJ_UPLOAD_PATH . 'tickets/pdfs/p_' . $v['uuid'] . '.pdf';
				}
				if($pdf == '')
				{
					$v['tickets'] = !empty($_arr) ? join('<br/>', $_arr) : '';
				}else{
					$v['tickets'] = !empty($_arr) ? '<a href="'.$pdf.'" target="blank">' . join('<br/>', $_arr) . '</a>' : '';
				}
				$data[$k] = $v;
			}
			pjAppController::jsonResponse(compact('data', 'total', 'pages', 'page', 'rowCount', 'column', 'direction'));
		}
		exit;
	}
	
	public function pjActionIndex()
	{
		$this->checkLogin();
		
		if ($this->isAdmin() || $this->isEditor())
		{
			$event_arr = pjEventModel::factory()
				->join('pjMultiLang', "t2.model='pjEvent' AND t2.foreign_id=t1.id AND t2.field='title' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
				->select(" t1.*, t2.content as title")
				->where("t1.status", "T")
				->orderBy("t1.created DESC")
				->findAll()
				->getData();
			$this->set('event_arr', $event_arr);
			
			$this->appendJs('chosen.jquery.js', PJ_THIRD_PARTY_PATH . 'chosen/');
			$this->appendCss('chosen.css', PJ_THIRD_PARTY_PATH . 'chosen/');
			$this->appendJs('jquery.datagrid.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
			$this->appendJs('pjAdminBookings.js');
		} else {
			$this->set('status', 2);
		}
	}
	
	public function pjActionExportBooking()
	{
		$this->checkLogin();
		
		if (isset($_POST['record']) && is_array($_POST['record']))
		{
			$arr = pjBookingModel::factory()->whereIn('id', $_POST['record'])->findAll()->getData();
			$csv = new pjCSV();
			$csv
				->setHeader(true)
				->setName("Bookings-".time().".csv")
				->process($arr)
				->download();
		}
		exit;
	}
	
	public function pjActionCreate()
	{
		$this->checkLogin();
		
		if ($this->isAdmin() || $this->isEditor())
		{
			$pjEventModel = pjEventModel::factory();
			
			if (isset($_POST['booking_create']))
			{
				$data = array();
				
				$pjBookingModel = pjBookingModel::factory();
				
				$data['uuid'] = pjUtil::uuid();
				$data['ip']= pjUtil::getClientIp();
				
				$post = array_merge($_POST, $data);

				if (!$pjBookingModel->validates($post))
				{
					pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminBookings&action=pjActionIndex&err=AR04");
				}
				
				$insert_id = $pjBookingModel->setAttributes($post)->insert()->getInsertId();
				if ($insert_id !== false && (int) $insert_id > 0)
				{
					$ticket_arr = pjShowModel::factory()
						->join('pjPrice', "t1.price_id=t2.id", 'left outer')
						->select("t1.id, t1.price_id, t1.price")
						->where('t1.event_id', $_POST['event_id'])
						->where("t1.date_time = '". $_POST['date_time'] . "'")
						->where("t1.venue_id", $_POST['venue_id'])
						->findAll()
						->getData();
					$price_arr = $this->calculatePrice($ticket_arr, $_POST['tickets']);
					
					$show_id_arr = array();
					$_price_arr = array();
					foreach($ticket_arr as $v)
					{
						$show_id_arr[$v['price_id']] = $v['id'];
						$_price_arr[$v['price_id']] = $v['price'];
					}
					
					$pjBookingShowModel = pjBookingShowModel::factory();
					$pjBookingTicketModel = pjBookingTicketModel::factory();
					foreach($_POST['seat_id'] as $price_id => $seat_arr)
					{
						$bs_data = array();
						$bt_data = array();
						
						$bs_data['booking_id'] = $insert_id;
						$bs_data['show_id'] = $show_id_arr[$price_id];
						$bs_data['price_id'] = $price_id;
						$bs_data['price'] = $_price_arr[$price_id];
						
						$bt_data['booking_id'] = $insert_id;
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
								$bt_data['ticket_id'] = $data['uuid'] . '-' . $seat_id . '-' . $i;
								$pjBookingTicketModel->reset()->setAttributes($bt_data)->insert();
							}
						}
					}
					$arr = pjAppController::pjActionGetBookingDetails($insert_id);
					pjAppController::buildPdfTickets($arr);
					pjAppController::pjActionGenerateInvoice($arr);
					
					pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminBookings&action=pjActionIndex&err=AR03");
				} else {
					pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminBookings&action=pjActionIndex&err=AR04");
				}
			}else{
				$pjBookingModel = pjBookingModel::factory();
				$pjEventModel = pjEventModel::factory();
				
				$country_arr = pjCountryModel::factory()
					->select('t1.id, t2.content AS country_title')
					->join('pjMultiLang', "t2.model='pjCountry' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
					->orderBy('`country_title` ASC')
					->findAll()
					->getData();
				
				$event_arr = pjEventModel::factory()
					->join('pjMultiLang', "t2.model='pjEvent' AND t2.foreign_id=t1.id AND t2.field='title' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
					->select(" t1.*, t2.content as title")
					->where("t1.status", "T")
					->where("(t1.id IN (SELECT TS.event_id FROM `".pjShowModel::factory()->getTable()."` AS TS WHERE TS.date_time >= NOW()) )")
					->orderBy("t1.created DESC")
					->findAll()
					->getData();
				
				if(isset($_GET['event_id']) && (int) $_GET['event_id'] > 0 )
				{
					$pjShowModel = pjShowModel::factory();
					
					$event_id = $_GET['event_id'];
					$date_time = date('Y-m-d H:i:s', $_GET['ts']);
					$venue_id = NULL;
					
					$show_arr = $pjShowModel
						->select("DISTINCT t1.date_time")
						->where('t1.event_id', $event_id)
						->where("t1.date_time >= NOW()")
						->orderBy("t1.date_time ASC")
						->findAll()
						->getData();
					
					$_show_arr = $pjShowModel
						->reset()
						->where('t1.event_id', $event_id)
						->where("t1.date_time = '". $date_time . "'")
						->limit(1)
						->findAll()
						->getData();
					if(count($_show_arr) > 0)
					{
						$venue_id = $_show_arr[0]['venue_id'];
					}
					if($venue_id != null)
					{
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
											FROM `".pjBookingShowModel::factory()->getTable()."` AS `TBS`
											WHERE TBS.show_id=t1.id AND TBS.booking_id IN (SELECT TB.id FROM `".pjBookingModel::factory()->getTable()."` AS TB WHERE TB.event_id=".$event_id." AND TB.status='confirmed') ), 0)										
										)
								  ) as cnt_tickets")
							->where('t1.event_id', $event_id)
							->where("t1.date_time = '". $date_time . "'")
							->where("t1.venue_id", $venue_id)
							->findAll()
							->getData();
						
						$venue_arr = pjVenueModel::factory()->find($venue_id)->getData();
						$has_map = 1;
						if (empty($venue_arr['map_path']))
						{
							$has_map = 0;
						}
						$seat_arr = pjSeatModel::factory()
							->select("t1.*, (SELECT GROUP_CONCAT( TS.price_id SEPARATOR '~:~') FROM `".pjShowModel::factory()->getTable()."` AS TS WHERE TS.event_id='".$event_id."' AND TS.date_time = '". $date_time . "' AND TS.id IN (SELECT TSS.show_id FROM `".pjShowSeatModel::factory()->getTable()."` AS TSS WHERE TSS.seat_id=t1.id) ) AS price_id,
												(
													IFNULL((SELECT SUM(TBS.cnt)
													FROM `".pjBookingShowModel::factory()->getTable()."` AS `TBS`
													WHERE TBS.show_id IN (SELECT TS.id FROM `".pjShowModel::factory()->getTable()."` AS TS WHERE TS.event_id='".$event_id."' AND TS.date_time = '". $date_time . "') AND TBS.seat_id=t1.id AND TBS.booking_id IN (SELECT TB.id FROM `".pjBookingModel::factory()->getTable()."` AS TB WHERE TB.event_id=".$event_id." AND TB.status='confirmed')), 0)
												)
											AS cnt_booked ")
							->where('t1.venue_id', $venue_id)
							->where("t1.id IN (SELECT TSS.seat_id FROM `".pjShowSeatModel::factory()->getTable()."` AS TSS WHERE TSS.show_id IN (SELECT TS.id FROM `".pjShowModel::factory()->getTable()."` AS TS WHERE TS.event_id='".$event_id."' AND TS.date_time = '". $date_time . "') )")
							->findAll()
							->getData();
					
						$seat_name_arr = array();
						foreach($seat_arr as $v)
						{
							$seat_name_arr[$v['id']] = $v['name'];
						}
						$ticket_name_arr = array();
						foreach($ticket_arr as $v)
						{
							$ticket_name_arr[$v['price_id']] = pjSanitize::html($v['ticket']);
						}
					
						$this->set('venue_id', $venue_id);
						$this->set('seat_arr', $seat_arr);
						$this->set('seat_name_arr', $seat_name_arr);
						$this->set('ticket_arr', $ticket_arr);
						$this->set('date_time', $date_time);
						$this->set('ticket_name_arr', $ticket_name_arr);
						$this->set('show_arr', $show_arr);
						$this->set('has_map', $has_map);
					}
				}
				
				$this->set('event_arr', $event_arr);
				$this->set('country_arr', $country_arr);
				
				$this->appendJs('chosen.jquery.js', PJ_THIRD_PARTY_PATH . 'chosen/');
				$this->appendCss('chosen.css', PJ_THIRD_PARTY_PATH . 'chosen/');
				$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
				$this->appendJs('pjAdminBookings.js');
			}
		} else {
			$this->set('status', 2);
		}
	}
	
	public function pjActionUpdate()
	{
		$this->checkLogin();
		
		if ($this->isAdmin() || $this->isEditor())
		{
			$pjBookingModel = pjBookingModel::factory();
			$pjEventModel = pjEventModel::factory();

			$pjBookingModel = pjBookingModel::factory();
			if (isset($_REQUEST['id']) && (int) $_REQUEST['id'] > 0)
			{
				$pjBookingModel->where('t1.id', $_REQUEST['id']);
			} elseif (isset($_GET['uuid']) && !empty($_GET['uuid'])) {
				$pjBookingModel->where('t1.uuid', $_GET['uuid']);
			}
			$booking = $pjBookingModel
				->join('pjEvent', 't2.id=t1.event_id')
				->limit(1)
				->findAll()
				->getDataIndex(0);
			
			if (!$booking)
			{
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminBookings&action=pjActionIndex&err=AR08");
			}
			
			$event_id = $booking['event_id'];
			
			$event = $pjEventModel->find($event_id)->getData();
			if (empty($event) || count($event) == 0)
			{
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminBookings&action=pjActionIndex&err=AR09");
			}
			
			if (isset($_POST['booking_update']))
			{
				$data = array();
				
				$data['ip']= pjUtil::getClientIp();
				$post = array_merge($_POST, $data);
				
				if (!$pjBookingModel->validates($post))
				{
					pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminBookings&action=pjActionIndex&err=AR02");
				}
				
				$pjBookingModel->reset()->set('id', $_POST['id'])->modify($post);
				
				$pjBookingShowModel = pjBookingShowModel::factory();
				$pjBookingTicketModel = pjBookingTicketModel::factory();
				
				$pjBookingShowModel->where('booking_id', $_POST['id'])->eraseAll();
				
				pjAppController::deleteTicketInfo($booking['id'], $booking['uuid']);
				$pjBookingTicketModel->where('booking_id', $_POST['id'])->eraseAll();
				
				$booking = $pjBookingModel
					->reset()
					->select(sprintf("t1.*,
						AES_DECRYPT(t1.cc_num, '%1\$s') AS `cc_num`,
						AES_DECRYPT(t1.cc_exp_month, '%1\$s') AS `cc_exp_month`,
						AES_DECRYPT(t1.cc_exp_year, '%1\$s') AS `cc_exp_year`,
						AES_DECRYPT(t1.cc_code, '%1\$s') AS `cc_code`", PJ_SALT))
					->join('pjEvent', 't2.id=t1.event_id')
					->find($_POST['id'])->getData();

				$ticket_arr = pjShowModel::factory()
					->join('pjPrice', "t1.price_id=t2.id", 'left outer')
					->select("t1.id, t1.price_id, t1.price")
					->where('t1.event_id', $_POST['event_id'])
					->where("t1.date_time = '". $_POST['date_time'] . "'")
					->where("t1.venue_id", $_POST['venue_id'])
					->findAll()
					->getData();
				$price_arr = $this->calculatePrice($ticket_arr, $_POST['tickets']);
					
				$show_id_arr = array();
				$_price_arr = array();
				foreach($ticket_arr as $v)
				{
					$show_id_arr[$v['price_id']] = $v['id'];
					$_price_arr[$v['price_id']] = $v['price'];
				}
				foreach($_POST['seat_id'] as $price_id => $seat_arr)
				{
					$bs_data = array();
					$bt_data = array();
					
					$bs_data['booking_id'] = $_POST['id'];
					$bs_data['show_id'] = $show_id_arr[$price_id];
					$bs_data['price_id'] = $price_id;
					$bs_data['price'] = $_price_arr[$price_id];
					
					$bt_data['booking_id'] = $_POST['id'];
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
							$bt_data['ticket_id'] = $booking['uuid'] . '-' . $seat_id . '-' . $i;
							$pjBookingTicketModel->reset()->setAttributes($bt_data)->insert();
						}
					}
				}
				pjAppController::buildPdfTickets($booking);
				
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminBookings&action=pjActionIndex&err=AR01");
			} else {
				$venue_id = null;
				$has_map = 1;
				
				$pjShowModel = pjShowModel::factory();
				
				$country_arr = pjCountryModel::factory()
					->select('t1.id, t2.content AS country_title')
					->join('pjMultiLang', "t2.model='pjCountry' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
					->orderBy('`country_title` ASC')
					->findAll()
					->getData();
				
				$event_arr = $pjEventModel
					->reset()
					->join('pjMultiLang', "t2.model='pjEvent' AND t2.foreign_id=t1.id AND t2.field='title' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
					->select(" t1.*, t2.content as title")
					->where("t1.status", "T")
					->orderBy("t1.created DESC")
					->findAll()
					->getData();
				
				$show_arr = $pjShowModel
					->select("DISTINCT t1.date_time, t1.venue_id, t2.content as venue_name")
					->join('pjMultiLang', "t2.model='pjVenue' AND t2.foreign_id=t1.venue_id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
					->where("(t1.venue_id IN (SELECT `TV`.id FROM `".pjVenueModel::factory()->getTable()."` AS `TV` WHERE `TV`.`status`='T'))")
					->where('t1.event_id', $event_id)
					->orderBy("t1.date_time ASC")
					->findAll()
					->getData();
				
				$bt_arr = pjBookingTicketModel::factory()
					->select("t2.venue_id")
					->join("pjSeat", "t2.id=t1.seat_id", "left")
					->where("booking_id", $booking['id'])
					->limit(1)
					->findAll()
					->getData();
				
				if(count($bt_arr) > 0)
				{
					$venue_id = $bt_arr[0]['venue_id'];
				}
				
				if($venue_id != null)
				{
					$ticket_arr = $pjShowModel->reset()
						->join('pjMultiLang', "t2.model='pjPrice' AND t2.foreign_id=t1.price_id AND t2.field='price_name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
						->join('pjPrice', "t1.price_id=t3.id", 'left outer')
						->select("t1.id, t1.price_id, t1.price, t2.content as ticket,
								  (SELECT SUM(TBS.cnt) FROM `".pjBookingShowModel::factory()->getTable()."` AS TBS WHERE TBS.booking_id = '".$booking['id']."' AND t1.price_id=TBS.price_id) as cnt,	
								  (     (
											SELECT SUM(TS.seats)
											FROM `".pjSeatModel::factory()->getTable()."` AS `TS`
											WHERE TS.venue_id='".$venue_id."' AND
								              	TS.id IN ( SELECT(TSS.seat_id) FROM `".pjShowSeatModel::factory()->getTable()."` AS `TSS` WHERE TSS.show_id=t1.id)) -
									    (
											IFNULL((SELECT SUM(TBS.cnt)
											FROM `".pjBookingShowModel::factory()->getTable()."` AS `TBS`
											WHERE TBS.show_id=t1.id AND TBS.booking_id IN (SELECT TB.id FROM `".pjBookingModel::factory()->getTable()."` AS TB WHERE TB.event_id=".$event_id." AND TB.status='confirmed' AND TB.id <> '".$booking['id']."') ), 0)
										)
								  ) as cnt_tickets")
						  ->where('t1.event_id', $event_id)
						  ->where("t1.date_time = '". $booking['date_time'] . "'")
						  ->where("t1.venue_id", $venue_id)
						  ->findAll()
						  ->getData();
					
					$venue_arr = pjVenueModel::factory()->find($venue_id)->getData();
						
					if (empty($venue_arr['map_path']))
					{
						$has_map = 0;
					}
					$seat_arr = pjSeatModel::factory()
						->select("t1.*, (SELECT GROUP_CONCAT( TS.price_id SEPARATOR '~:~') FROM `".pjShowModel::factory()->getTable()."` AS TS WHERE TS.event_id='".$event_id."' AND TS.date_time = '". $booking['date_time'] . "' AND TS.id IN (SELECT TSS.show_id FROM `".pjShowSeatModel::factory()->getTable()."` AS TSS WHERE TSS.seat_id=t1.id) ) AS price_id,
											(
												IFNULL((SELECT SUM(TBS.cnt)
												FROM `".pjBookingShowModel::factory()->getTable()."` AS `TBS`
												WHERE TBS.show_id IN (SELECT TS.id FROM `".pjShowModel::factory()->getTable()."` AS TS WHERE TS.event_id='".$event_id."' AND TS.date_time = '". $booking['date_time'] . "') AND TBS.seat_id=t1.id AND TBS.booking_id IN (SELECT TB.id FROM `".pjBookingModel::factory()->getTable()."` AS TB WHERE TB.event_id=".$event_id." AND TB.date_time = '".$booking['date_time']."' AND TB.status='confirmed' AND TB.id <> '".$booking['id']."')), 0)
											)
										AS cnt_booked ")
						->where('t1.venue_id', $venue_id)
						->where("t1.id IN (SELECT TSS.seat_id FROM `".pjShowSeatModel::factory()->getTable()."` AS TSS WHERE TSS.show_id IN (SELECT TS.id FROM `".pjShowModel::factory()->getTable()."` AS TS WHERE TS.event_id='".$event_id."' AND TS.date_time = '". $booking['date_time'] . "') )")
						->findAll()
						->getData();
						
					$seat_name_arr = array();
					foreach($seat_arr as $v)
					{
						$seat_name_arr[$v['id']] = $v['name'];
					}
					$ticket_name_arr = array();
					foreach($ticket_arr as $v)
					{
						$ticket_name_arr[$v['price_id']] = pjSanitize::html($v['ticket']);
					}
					
					$this->set('seat_arr', $seat_arr);
					$this->set('seat_name_arr', $seat_name_arr);
					$this->set('ticket_arr', $ticket_arr);
					$this->set('venue_arr', $venue_arr);
					$this->set('ticket_name_arr', $ticket_name_arr);
				}
				$pjBookingShowModel = pjBookingShowModel::factory();
				$seat_id_arr = array();
				$_arr = $pjBookingShowModel
					->where('booking_id', $booking['id'])
					->findAll()
					->getData();
				foreach($_arr as $v)
				{
					$seat_id_arr[$v['price_id']][$v['seat_id']] = $v['cnt'];
				}
				
				$cnt_tickets = pjBookingTicketModel::factory()->where('t1.booking_id', $_GET['id'])->findCount()->getData();
				if($cnt_tickets == 0)
				{
					$bs_arr = $pjBookingShowModel
						->reset()
						->where('t1.booking_id', $_GET['id'])
						->findAll()
						->getData();
					$pjBookingTicketModel = pjBookingTicketModel::factory();
					
					foreach($bs_arr as $v)
					{
						$bt_data = array();
						$bt_data['booking_id'] = $v['booking_id'];
						$bt_data['seat_id'] = $v['seat_id'];
						$bt_data['price_id'] = $v['price_id'];
						$bt_data['unit_price'] = $v['price'];
						$bt_data['is_used'] = 'F';
						for($i = 1; $i <= $v['cnt']; $i++)
						{
							$bt_data['ticket_id'] = $booking['uuid'] . '-' . $v['seat_id'] . '-' . $i;
							$pjBookingTicketModel->reset()->setAttributes($bt_data)->insert();
						}
					}
					pjAppController::buildPdfTickets($booking);
				}

				$this->set('invoice_arr', pjInvoiceModel::factory()
						->where('t1.order_id', $booking['uuid'])
						->findAll()
						->getData()
				);
				
				$booking = pjAppController::pjActionGetBookingDetails($booking['id']);
				
				$this->set('has_map', $has_map);
				$this->set('venue_id', $venue_id);
				$this->set('event_arr', $event_arr);
				$this->set('show_arr', $show_arr);
				$this->set('seat_id_arr', $seat_id_arr);
				$this->set('country_arr', $country_arr);
				
				$this->appendJs('chosen.jquery.js', PJ_THIRD_PARTY_PATH . 'chosen/');
				$this->appendCss('chosen.css', PJ_THIRD_PARTY_PATH . 'chosen/');
				$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
				$this->appendJs('jquery.datagrid.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
				$this->appendJs('pjAdminBookings.js');
				
				$this->set('arr', $booking);
			}
		}
	}
	
	public function pjActionResend()
	{
		if(isset($_POST['resend_email']))
		{
			$booking_id = $_POST['id'];
	
			if(!empty($_POST['to']))
			{
				$subject = stripslashes($_POST['subject']);
				$to = stripslashes($_POST['to']);
				$from = $this->getFromEmail($this->option_arr);
				$message = stripslashes($_POST['message']);
	
				$pjEmail = new pjEmail();
	
				if ($this->option_arr['o_send_email'] == 'smtp')
				{
					$pjEmail
						->setTransport('smtp')
						->setSmtpHost($this->option_arr['o_smtp_host'])
						->setSmtpPort($this->option_arr['o_smtp_port'])
						->setSmtpUser($this->option_arr['o_smtp_user'])
						->setSmtpPass($this->option_arr['o_smtp_pass'])
						->setSender($this->option_arr['o_smtp_user'])
					;
				}
	
				$pjEmail->setContentType('text/html');
				$pjEmail->setFrom($from);
				$pjEmail->setSubject($subject);
				$pjEmail->setTo($to);
				$pjEmail->send($message);
				
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminBookings&action=pjActionResend&id=$booking_id&err=AR10");
			}else{
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminBookings&action=pjActionResend&id=$booking_id&err=AR11");
			}
	
		}else{
	
			$booking_id = $_GET['id'];
			
			$arr = pjAppController::pjActionGetBookingDetails($booking_id);
	
			$tokens = pjAppController::getData($this->option_arr, $arr, PJ_SALT, $this->getLocaleId());
				
			$pjMultiLangModel = pjMultiLangModel::factory();
				
			$locale_id = $this->getLocaleId();
			$foreign_id = 1;
				
			$lang_message = $pjMultiLangModel->reset()->select('t1.*')
				->where('foreign_id', $foreign_id)
				->where('t1.model','pjOption')
				->where('t1.locale', $locale_id)
				->where('t1.field', 'o_email_payment_message')
				->limit(0, 1)
				->findAll()->getData();
			$lang_subject = $pjMultiLangModel->reset()->select('t1.*')
				->where('foreign_id', $foreign_id)
				->where('t1.model','pjOption')
				->where('t1.locale', $locale_id)
				->where('t1.field', 'o_email_payment_subject')
				->limit(0, 1)
				->findAll()->getData();
				
			if (count($lang_message) === 1 && count($lang_subject) === 1)
			{
				$message = str_replace($tokens['search'], $tokens['replace'], $lang_message[0]['content']);
					
				$arr['payment_subject'] = $lang_subject[0]['content'];
				$arr['payment_message'] = $message;
			}
			$lang_message = $pjMultiLangModel->reset()->select('t1.*')
				->where('foreign_id', $foreign_id)
				->where('t1.model','pjOption')
				->where('t1.locale', $locale_id)
				->where('t1.field', 'o_email_confirmation_message')
				->limit(0, 1)
				->findAll()->getData();
			$lang_subject = $pjMultiLangModel->reset()->select('t1.*')
				->where('foreign_id', $foreign_id)
				->where('t1.model','pjOption')
				->where('t1.locale', $locale_id)
				->where('t1.field', 'o_email_confirmation_subject')
				->limit(0, 1)
				->findAll()->getData();
	
			if (count($lang_message) === 1 && count($lang_subject) === 1)
			{
				$message = str_replace($tokens['search'], $tokens['replace'], $lang_message[0]['content']);
					
				$arr['confirm_subject'] = $lang_subject[0]['content'];
				$arr['confirm_message'] = $message;
			}
				
			$this->set('arr', $arr);
	
			$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
			$this->appendJs('tinymce.min.js', PJ_THIRD_PARTY_PATH . 'tinymce/');
			$this->appendJs('pjAdminBookings.js');
		}
	}
	
	public function pjActionBarcode()
	{
		$this->checkLogin();
		
		if ($this->isAdmin() || $this->isEditor())
		{
			if(isset($_POST['read_barcode']))
			{
				$ticket_arr = pjBookingTicketModel::factory()
					->where('ticket_id', $_POST['barcode_label'])
					->findAll()
					->getData();
					
				$status = 1;
					
				if(count($ticket_arr) > 0)
				{
				    $arr = pjAppController::pjActionGetBookingDetails($ticket_arr[0]['booking_id']);
					$arr['ticket_id'] = $ticket_arr[0]['id'];
					$arr['is_used'] = $ticket_arr[0]['is_used'];
					
					if($arr['status'] != 'confirmed')
					{
						$status = 2;
					}else if($arr['is_used'] == 'T'){
						$status = 3;
					}
					
					$this->set('arr', $arr);
				}else{
					$status = 4;
				}
				$this->set('ticket_status', $status);
			}
			
			$this->appendJs('pjAdminBookings.js');
		} else {
			$this->set('status', 2);
		}
	}
	
	public function pjActionSetUseTicket()
	{
		$this->setAjax(true);
	
		$json_arr = array();
	
		pjBookingTicketModel::factory()->where('id', $_POST['id'])->limit(1)->modifyAll(array('is_used' => 'T'));
		$json_arr['status'] = 1;
	
		pjAppController::jsonResponse($json_arr);
	}
	
	public function pjActionDeleteBooking()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$response = array();
			$pjBookingModel = pjBookingModel::factory();
			$arr = $pjBookingModel->find($_GET['id'])->getData();
			if ($pjBookingModel->reset()->setAttributes(array('id' => $_GET['id']))->erase()->getAffectedRows() == 1)
			{
				pjBookingPaymentModel::factory()->where('booking_id', $_GET['id'])->eraseAll();
				pjBookingShowModel::factory()->where('booking_id', $_GET['id'])->eraseAll();
				pjBookingTicketModel::factory()->where('booking_id', $_GET['id'])->eraseAll();
				
				$response['code'] = 200;
			} else {
				$response['code'] = 100;
			}
			pjAppController::jsonResponse($response);
		}
		exit;
	}
	
	public function pjActionDeleteBookingBulk()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			if (isset($_POST['record']) && count($_POST['record']) > 0)
			{
				$pjBookingModel = pjBookingModel::factory();
				
				$pjBookingModel->reset()->whereIn('id', $_POST['record'])->eraseAll();
				pjBookingPaymentModel::factory()->whereIn('booking_id', $_POST['record'])->eraseAll();
				pjBookingShowModel::factory()->whereIn('booking_id', $_POST['record'])->eraseAll();
				pjBookingTicketModel::factory()->whereIn('booking_id', $_POST['record'])->eraseAll();
			}
		}
		exit;
	}
	
	public function pjActionSaveBooking()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			pjBookingModel::factory()->where('id', $_GET['id'])->limit(1)->modifyAll(array($_POST['column'] => $_POST['value']));
		}
		exit;
	}
	
	public function pjActionGetShows()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
		    if (isset($_GET['event_id']) && (int) $_GET['event_id'] > 0)
			{
				$show_arr = pjShowModel::factory()
					->select('DISTINCT t1.venue_id, t1.date_time, t2.content as venue_name')
					->join('pjMultiLang', "t2.model='pjVenue' AND t2.foreign_id=t1.venue_id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
					->where("(t1.venue_id IN (SELECT `TV`.id FROM `".pjVenueModel::factory()->getTable()."` AS `TV` WHERE `TV`.`status`='T'))")
					->where('t1.event_id', $_GET['event_id'])
					->where("t1.date_time >= NOW()")
					->orderBy("t1.date_time ASC")
					->findAll()
					->getData();
				
				$this->set('show_arr', $show_arr);	
			}
		}
	}
	
	public function pjActionGetTickets()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
		    if (isset($_GET['event_id']) && (int) $_GET['event_id'] > 0 && isset($_GET['venue_id']) && (int) $_GET['venue_id'] > 0)
			{
				$venue_id = null;
				$date_time = $_GET['date_time'];
				$where = '';
				$pjShowModel = pjShowModel::factory();
				$_show_arr = $pjShowModel
					->where('t1.event_id', $_GET['event_id'])
					->where('t1.venue_id', $_GET['venue_id'])		
					->limit(1)
					->findAll()
					->getData();
				if(count($_show_arr) > 0)
				{
					$venue_id = $_show_arr[0]['venue_id'];
				}
				if(isset($_GET['id']) && (int) $_GET['id'] > 0 )
				{
					$where = " AND TB.id <> '" . $_GET['id'] . "'";
				}
				if($venue_id != null)
				{
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
											FROM `".pjBookingShowModel::factory()->getTable()."` AS `TBS`
											WHERE TBS.show_id=t1.id AND TBS.booking_id IN (SELECT TB.id FROM `".pjBookingModel::factory()->getTable()."` AS TB WHERE TB.event_id=".$_GET['event_id']." AND TB.status='confirmed'$where) ), 0)
										)
								  ) as cnt_tickets")
						  ->where('t1.event_id', $_GET['event_id'])
						  ->where("t1.date_time = '". $date_time . "'")
						  ->where("t1.venue_id", $venue_id)
						  ->findAll()
						  ->getData();

					$venue_arr = pjVenueModel::factory()->find($venue_id)->getData();
					
					if (empty($venue_arr['map_path']))
					{
						$seat_arr = pjSeatModel::factory()
							->select("t1.*, (SELECT GROUP_CONCAT( TS.price_id SEPARATOR '~:~') FROM `".pjShowModel::factory()->getTable()."` AS TS WHERE TS.event_id='".$_GET['event_id']."' AND TS.date_time = '". $date_time . "' AND TS.id IN (SELECT TSS.show_id FROM `".pjShowSeatModel::factory()->getTable()."` AS TSS WHERE TSS.seat_id=t1.id) ) AS price_id,
											(
												IFNULL((SELECT SUM(TBS.cnt)
												FROM `".pjBookingShowModel::factory()->getTable()."` AS `TBS`
												WHERE TBS.show_id IN (SELECT TS.id FROM `".pjShowModel::factory()->getTable()."` AS TS WHERE TS.event_id='".$_GET['event_id']."' AND TS.date_time = '". $date_time . "') AND TBS.seat_id=t1.id AND TBS.booking_id IN (SELECT TB.id FROM `".pjBookingModel::factory()->getTable()."` AS TB WHERE TB.event_id=".$_GET['event_id']." AND TB.status='confirmed'$where)), 0)
											)
										AS cnt_booked ")
							->where('t1.venue_id', $venue_id)
							->where("t1.id IN (SELECT TSS.seat_id FROM `".pjShowSeatModel::factory()->getTable()."` AS TSS WHERE TSS.show_id IN (SELECT TS.id FROM `".pjShowModel::factory()->getTable()."` AS TS WHERE TS.event_id='".$_GET['event_id']."' AND TS.date_time = '". $_GET['date_time'] . "') )")
							->findAll()
							->getData();

						$seat_name_arr = array();
						foreach($seat_arr as $v)
						{
							$seat_name_arr[$v['id']] = $v['name'];
						}
						$this->set('seat_arr', $seat_arr);
						$this->set('seat_name_arr', $seat_name_arr);
					}
					
					
					$this->set('venue_id', $venue_id);
					$this->set('ticket_arr', $ticket_arr);
				}
			}
		}
	}
	
	public function pjActionGetSeats()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
		    if (isset($_POST['event_id']) && (int) $_POST['event_id'] > 0)
			{
				$venue_id = null;
				$where = '';
				$pjShowModel = pjShowModel::factory();
				
				if (isset($_POST['venue_id']) && (int) $_POST['venue_id'] > 0)
				{
					$venue_id = $_POST['venue_id'];
				}else{
					$_show_arr = $pjShowModel
						->where('t1.event_id', $_POST['event_id'])
						->where("t1.date_time = '". $_POST['date_time'] . "'")
						->limit(1)
						->findAll()
						->getData();
					if(count($_show_arr) > 0)
					{
						$venue_id = $_show_arr[0]['venue_id'];
					}
				}
				if(isset($_POST['booking_update']))
				{
					$where = " AND TB.id <> '" . $_POST['id'] . "'";
					$seat_id_arr = array();
					$_arr = pjBookingShowModel::factory()
						->where('booking_id', $_POST['id'])
						->findAll()
						->getData();
					foreach($_arr as $v)
					{
						$seat_id_arr[$v['price_id']][$v['seat_id']] = $v['cnt'];
					}
					$this->set('seat_id_arr', $seat_id_arr);
				}
				if($venue_id != null)
				{
					$ticket_arr = $pjShowModel->reset()
						->join('pjMultiLang', "t2.model='pjPrice' AND t2.foreign_id=t1.price_id AND t2.field='price_name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
						->join('pjPrice', "t1.price_id=t3.id", 'left outer')
						->select("t1.id, t1.price_id, t1.price, t2.content as ticket")
						->where('t1.event_id', $_POST['event_id'])
						->where("t1.date_time = '". $_POST['date_time'] . "'")
						->where("t1.venue_id", $venue_id)
						->findAll()
						->getData();
					
					$ticket_name_arr = array();
					foreach($ticket_arr as $v)
					{
						$ticket_name_arr[$v['price_id']] = pjSanitize::html($v['ticket']);
					}
					$venue_arr = pjVenueModel::factory()->find($venue_id)->getData();
					$seat_arr = pjSeatModel::factory()
						->select("t1.*, (SELECT GROUP_CONCAT( TS.price_id SEPARATOR '~:~') FROM `".pjShowModel::factory()->getTable()."` AS TS WHERE TS.event_id='".$_POST['event_id']."' AND TS.date_time = '". $_POST['date_time'] . "' AND TS.id IN (SELECT TSS.show_id FROM `".pjShowSeatModel::factory()->getTable()."` AS TSS WHERE TSS.seat_id=t1.id) ) AS price_id,
										(
											IFNULL((SELECT SUM(TBS.cnt)
											FROM `".pjBookingShowModel::factory()->getTable()."` AS `TBS`
											WHERE TBS.show_id IN (SELECT TS.id FROM `".pjShowModel::factory()->getTable()."` AS TS WHERE TS.event_id='".$_POST['event_id']."' AND TS.date_time = '". $_POST['date_time'] . "') AND TBS.seat_id=t1.id AND TBS.booking_id IN (SELECT TB.id FROM `".pjBookingModel::factory()->getTable()."` AS TB WHERE TB.event_id=".$_POST['event_id']." AND TB.date_time='".$_POST['date_time']."' AND TB.status='confirmed'$where)), 0)
										)
									AS cnt_booked ")
						->where('t1.venue_id', $venue_id)
						->where("t1.id IN (SELECT TSS.seat_id FROM `".pjShowSeatModel::factory()->getTable()."` AS TSS WHERE TSS.show_id IN (SELECT TS.id FROM `".pjShowModel::factory()->getTable()."` AS TS WHERE TS.event_id='".$_POST['event_id']."' AND TS.date_time = '". $_POST['date_time'] . "') )")
						->findAll()
						->getData();
						
					$seat_name_arr = array();
					foreach($seat_arr as $v)
					{
						$seat_name_arr[$v['id']] = $v['name'];
					}
					
					$this->set('seat_arr', $seat_arr);
					$this->set('seat_name_arr', $seat_name_arr);
					$this->set('ticket_name_arr', $ticket_name_arr);
					$this->set('venue_arr', $venue_arr);
				}
			}
		}
	}
}
?>