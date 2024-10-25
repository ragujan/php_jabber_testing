<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjAdminSchedule extends pjAdmin
{                  
	public function _getSchedule($date)
	{
		$arr = pjEventModel::factory()
			->join('pjMultiLang', "t2.model='pjEvent' AND t2.foreign_id=t1.id AND t2.field='title' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
			->select('t1.*, t2.content as title')
			->where('status', 'T')
			->where("t1.id IN(SELECT TS.event_id FROM `".pjShowModel::factory()->getTable()."` AS TS WHERE TS.date_time LIKE '%".$date."%')")
			->findAll()
			->getData();
		
		$_arr = pjShowModel::factory()
			->join('pjMultiLang', "t2.model='pjVenue' AND t2.foreign_id=t1.venue_id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
			->select("t1.*, t2.content as venue")
			->where("t1.date_time LIKE '%$date%'")
			->findAll()
			->getData();
			
		$booking_arr = pjBookingModel::factory()
			->select("t1.event_id, t1.date_time, (SELECT COUNT(`TBT`.ticket_id) FROM `".pjBookingTicketModel::factory()->getTable()."` AS `TBT` WHERE `TBT`.booking_id=t1.id ) as cnt")
			->where("t1.date_time LIKE '%$date%'")
			->where('t1.status', 'confirmed')
			->findAll()
			->getData();

		$grid = $this->getShowsInGrid($_arr);
			
		$time_arr = array();
		$show_arr = array();
		$detail_arr = array();
		$venue_arr = array();
		$booking_cnt_arr = array();
		foreach($_arr as $v)
		{
			$venue_arr[$v['date_time']][$v['event_id']] = $v['venue'];
		}
		
		foreach($booking_arr as $v)
		{
			$booking_cnt_arr[$v['event_id'] . '-' . strtotime($v['date_time'])] = isset($booking_cnt_arr[$v['event_id'] . '-' . strtotime($v['date_time'])]) ? $booking_cnt_arr[$v['event_id'] . '-' . strtotime($v['date_time'])] + $v['cnt'] : $v['cnt'];
		}
		
		return compact('arr', 'grid','time_arr', 'show_arr', 'venue_arr', 'booking_cnt_arr');
	}
	
	public function pjActionIndex()
	{
		$this->checkLogin();
		
		if ($this->isAdmin() || $this->isEditor())
		{
			$this->appendJs('pjAdminSchedule.js');
		} else {
			$this->set('status', 2);
		}
	}
	
	public function pjActionGetSchedule()
	{
		$this->setAjax(true);
		
		if ($this->isXHR())
		{
			$date = date('Y-m-d');
			if(isset($_GET['date']) && !empty($_GET['date']))
			{
				$date = pjUtil::formatDate($_GET['date'], $this->option_arr['o_date_format']);
			}
			$result = $this->_getSchedule($date);	
			
			$this->set('date', $date);
			$this->set('arr', $result['arr']);
			$this->set('time_arr', $result['grid']['time_arr']);
			$this->set('show_arr', $result['grid']['show_arr']);
			$this->set('venue_arr', $result['venue_arr']);
			$this->set('booking_cnt_arr', $result['booking_cnt_arr']);
		}
	}
	
	public function pjActionPrint()
	{
		$this->setLayout('pjActionEmpty');
	
		$this->checkLogin();
	
		if ($this->isAdmin())
		{
			if(pjUtil::checkFormatDate($_GET['date'], $this->option_arr['o_date_format']) == TRUE)
			{
				$date = pjUtil::formatDate($_GET['date'], $this->option_arr['o_date_format']);
					
				$result = $this->_getSchedule($date);	
				
				$this->set('date', $date);
				$this->set('arr', $result['arr']);
				$this->set('time_arr', $result['grid']['time_arr']);
				$this->set('show_arr', $result['grid']['show_arr']);
				$this->set('venue_arr', $result['venue_arr']);
				$this->set('booking_cnt_arr', $result['booking_cnt_arr']);	
			}
		} else {
			$this->set('status', 2);
		}
	}
}
?>