<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjAdminVenues extends pjAdmin
{
    public $sessionHall = 'pjHall_session';
    
    public function pjActionBeforeSave()
    {
        $this->setAjax(true);
        
        if ($this->isXHR())
        {
            $response = array('status' => 'OK', 'code' => 200, 'text' => '');
            if (!isset($_SESSION[$this->sessionHall]) || !is_array($_SESSION[$this->sessionHall]))
            {
                $_SESSION[$this->sessionHall] = array();
            }
            
            $_SESSION[$this->sessionHall] = pjUtil::arrayMergeDistinct($_SESSION[$this->sessionHall], $_POST);
            pjAppController::jsonResponse($response);
        }
        exit;
    }
    
	public function pjActionCreate()
	{
		$this->checkLogin();
		
		if ($this->isAdmin() || $this->isEditor())
		{
			$post_max_size = pjUtil::getPostMaxSize();
			if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SERVER['CONTENT_LENGTH']) && (int) $_SERVER['CONTENT_LENGTH'] > $post_max_size)
			{
				pjUtil::redirect(PJ_INSTALL_URL . "index.php?controller=pjAdminVenues&action=pjActionIndex&err=AV05");
			}
			if (isset($_POST['venue_create']))
			{
				$pjVenueModel = pjVenueModel::factory();
				$pjSeatModel = pjSeatModel::factory();
				
				$id = $pjVenueModel->setAttributes($_POST)->insert()->getInsertId();
				if ($id !== false && (int) $id > 0)
				{
					if (isset($_POST['i18n']))
					{
						pjMultiLangModel::factory()->saveMultiLang($_POST['i18n'], $id, 'pjVenue', 'data');
					}
					if($_POST['use_seats_map'] == 'T')
					{
						if (isset($_FILES['seats_map']))
						{
							if($_FILES['seats_map']['error'] == 0)
							{
								if(getimagesize($_FILES['seats_map']["tmp_name"]) != false)
								{
									if (is_writable('app/web/upload/maps'))
									{
										$Image = new pjImage();
										if ($Image->getErrorCode() !== 200)
										{
											$Image->setAllowedTypes(array('image/png', 'image/gif', 'image/jpg', 'image/jpeg', 'image/pjpeg'));
											if ($Image->load($_FILES['seats_map']))
											{
												$resp = $Image->isConvertPossible();
												if ($resp['status'] === true)
												{
													$hash = md5(uniqid(rand(), true));
													$image_path = PJ_UPLOAD_PATH . 'maps/' . $id . '_' . $hash . '.' . $Image->getExtension();
													
													$Image->loadImage();
													$Image->saveImage($image_path);
													$data = array();
													$data['map_path'] = $image_path;
													$data['map_name'] = $_FILES['seats_map']['name'];
													$data['mime_type'] = $_FILES['seats_map']['type'];
																															
													$pjVenueModel->reset()->where('id', $id)->limit(1)->modifyAll($data);
												}
											}
										}
									}else{
										pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminVenues&action=pjActionUpdate&id=$id&err=AV11");
									}
								}else{
									pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminVenues&action=pjActionUpdate&id=$id&err=AV12");
								}
							}else if($_FILES['seats_map']['error'] != 4){
								pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminVenues&action=pjActionUpdate&id=$id&err=AV09");
							}
						}
					}else{
						if (isset($_SESSION[$this->sessionHall]) && !empty($_SESSION[$this->sessionHall]))
						{
						    $STORE = $_SESSION[$this->sessionHall];
						    $_SESSION[$this->sessionHall] = NULL;
						    unset($_SESSION[$this->sessionHall]);
						}else if(isset($_POST['number'])){
						    $STORE = array();
						    $STORE['number'] = $_POST['number'];
						}
						if(count($STORE['number']) > 0)
						{
						    $sdata = array();
						    $sdata['venue_id'] = $id;
						    foreach ($STORE['number'] as $key => $val)
						    {
						        $sdata['name'] = $val;
						        $sdata['seats'] = 1;
						        $pjSeatModel->reset()->setAttributes($sdata)->insert();
						    }
						}
					}
					
					$err = 'AV03';
					
					pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminVenues&action=pjActionUpdate&id=$id&err=$err");
				} else {
					$err = 'AV04';
					pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminVenues&action=pjActionIndex&err=$err");
				}
				
			} else {
				$locale_arr = pjLocaleModel::factory()->select('t1.*, t2.file')
					->join('pjLocaleLanguage', 't2.iso=t1.language_iso', 'left')
					->where('t2.file IS NOT NULL')
					->orderBy('t1.sort ASC')->findAll()->getData();
						
				$lp_arr = array();
				foreach ($locale_arr as $item)
				{
					$lp_arr[$item['id']."_"] = $item['file'];
				}
				$this->set('lp_arr', $locale_arr);
				$this->set('locale_str', pjAppController::jsonEncode($lp_arr));
				
				$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
				$this->appendJs('additional-methods.js', PJ_THIRD_PARTY_PATH . 'validate/');
				$this->appendJs('jquery.multilang.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
				$this->appendJs('jquery.tipsy.js', PJ_THIRD_PARTY_PATH . 'tipsy/');
				$this->appendCss('jquery.tipsy.css', PJ_THIRD_PARTY_PATH . 'tipsy/');
				$this->appendJs('pjAdminVenues.js');
			}
		} else {
			$this->set('status', 2);
		}
	}
	
	public function pjActionDeleteVenue()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$response = array();
			
			if (pjVenueModel::factory()->setAttributes(array('id' => $_GET['id']))->erase()->getAffectedRows() == 1)
			{
				pjMultiLangModel::factory()->where('model', 'pjVenue')->where('foreign_id', $_GET['id'])->eraseAll();
				pjSeatModel::factory()->where('venue_id', $_GET['id'])->eraseAll();
				
				$response['code'] = 200;
			} else {
				$response['code'] = 100;
			}
			
			pjAppController::jsonResponse($response);
		}
		exit;
	}
	
	public function pjActionDeleteVenueBulk()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			if (isset($_POST['record']) && count($_POST['record']) > 0)
			{
				pjVenueModel::factory()->whereIn('id', $_POST['record'])->eraseAll();
				pjMultiLangModel::factory()->where('model', 'pjVenue')->whereIn('foreign_id', $_POST['record'])->eraseAll();
				pjSeatModel::factory()->whereIn('venue_id', $_POST['record'])->eraseAll();
			}
		}
		exit;
	}
	
	public function pjActionExportVenue()
	{
		$this->checkLogin();
		
		if (isset($_POST['record']) && is_array($_POST['record']))
		{
			$arr = pjVenueModel::factory()
				->select('t1.id, t2.content as name')
				->join('pjMultiLang', "t2.model='pjVenue' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
				->whereIn('id', $_POST['record'])
				->findAll()
				->getData();
			$csv = new pjCSV();
			$csv
				->setHeader(true)
				->setName("Venues-".time().".csv")
				->process($arr)
				->download();
		}
		exit;
	}
	
	public function pjActionGetVenue()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$pjVenueModel = pjVenueModel::factory()
							->join('pjMultiLang', "t2.model='pjVenue' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer');
			
			if (isset($_GET['q']) && !empty($_GET['q']))
			{
				$q = pjObject::escapeString($_GET['q']);
				$pjVenueModel->where('t2.content LIKE', "%$q%");
			}
			if (isset($_GET['status']) && !empty($_GET['status']) && in_array($_GET['status'], array('T', 'F')))
			{
				$pjVenueModel->where('t1.status', $_GET['status']);
			}
			$column = 'name';
			$direction = 'ASC';
			if (isset($_GET['direction']) && isset($_GET['column']) && in_array(strtoupper($_GET['direction']), array('ASC', 'DESC')))
			{
				$column = $_GET['column'];
				$direction = strtoupper($_GET['direction']);
			}

			$total = $pjVenueModel->findCount()->getData();
			$rowCount = isset($_GET['rowCount']) && (int) $_GET['rowCount'] > 0 ? (int) $_GET['rowCount'] : 10;
			$pages = ceil($total / $rowCount);
			$page = isset($_GET['page']) && (int) $_GET['page'] > 0 ? intval($_GET['page']) : 1;
			$offset = ((int) $page - 1) * $rowCount;
			if ($page > $pages)
			{
				$page = $pages;
			}

			$data = array();
			
			$data = $pjVenueModel
				->select(" t1.id, t1.map_path, t1.seats_count, t1.status, t2.content as name")
				->orderBy("$column $direction")
				->limit($rowCount, $offset)
				->findAll()
				->getData();
				
			pjAppController::jsonResponse(compact('data', 'total', 'pages', 'page', 'rowCount', 'column', 'direction'));
		}
		exit;
	}
	
	public function pjActionIndex()
	{
		$this->checkLogin();
		
		if ($this->isAdmin() || $this->isEditor())
		{
			$this->appendJs('jquery.datagrid.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
			$this->appendJs('pjAdminVenues.js');
		} else {
			$this->set('status', 2);
		}
	}
	
	public function pjActionSaveVenue()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$pjVenueModel = pjVenueModel::factory();
			if (!in_array($_POST['column'], $pjVenueModel->i18n))
			{
				$value = $_POST['value'];
				
				$pjVenueModel->where('id', $_GET['id'])->limit(1)->modifyAll(array($_POST['column'] => $value));
			} else {
				pjMultiLangModel::factory()->updateMultiLang(array($this->getLocaleId() => array($_POST['column'] => $_POST['value'])), $_GET['id'], 'pjVenue', 'data');
			}
		}
		exit;
	}
	
	public function pjActionUpdate()
	{
		$this->checkLogin();
		
		if ($this->isAdmin() || $this->isEditor())
		{
			$post_max_size = pjUtil::getPostMaxSize();
			if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SERVER['CONTENT_LENGTH']) && (int) $_SERVER['CONTENT_LENGTH'] > $post_max_size)
			{
				pjUtil::redirect(PJ_INSTALL_URL . "index.php?controller=pjAdminVenues&action=pjActionIndex&err=AV06");
			}	
			if (isset($_POST['venue_update']))
			{
				$pjVenueModel = pjVenueModel::factory();
				$pjSeatModel = pjSeatModel::factory();
				
				$arr = $pjVenueModel->find($_POST['id'])->getData();
				if (empty($arr))
				{
					pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminVenues&action=pjActionIndex&err=AV08");
				}
				
				$data = array();
				
				if($_POST['use_seats_map'] == 'T')
				{
					if (isset($_FILES['seats_map']))
					{
						if($_FILES['seats_map']['error'] == 0)
						{
							if(getimagesize($_FILES['seats_map']["tmp_name"]) != false)
							{
								if (is_writable('app/web/upload/maps'))
								{
									$Image = new pjImage();
									if ($Image->getErrorCode() !== 200)
									{
										$Image->setAllowedTypes(array('image/png', 'image/gif', 'image/jpg', 'image/jpeg', 'image/pjpeg'));
										if ($Image->load($_FILES['seats_map']))
										{
											$resp = $Image->isConvertPossible();
											if ($resp['status'] === true)
											{
												$hash = md5(uniqid(rand(), true));
												$image_path = PJ_UPLOAD_PATH . 'maps/' . $_POST['id'] . '_' . $hash . '.' . $Image->getExtension();
												
												$Image->loadImage();
												$Image->saveImage($image_path);
												$data['map_path'] = $image_path;
												$data['map_name'] = $_FILES['seats_map']['name'];
												$data['mime_type'] = $_FILES['seats_map']['type'];
											}
										}
									}
												
									$pjSeatModel->where('venue_id', $_POST['id'])->eraseAll();
									if(isset($_POST['seats_count']))
									{
										unset($_POST['seats_count']);
									}
								}else{
									pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminVenues&action=pjActionUpdate&id=".$_POST['id']."&err=AV11");
								}
							}else{
								pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminVenues&action=pjActionUpdate&id=".$_POST['id']."&err=AV12");
							}
						}else if($_FILES['seats_map']['error'] != 4){
							pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminVenues&action=pjActionUpdate&id=".$_POST['id']."&err=AV10");
						}
					}
					
					if (isset($_POST['seats']))
					{
						$seat1_arr = array_values($pjSeatModel->where('venue_id', $_POST['id'])->findAll()->getDataPair('id', 'id'));
						$seat2_arr = array();
						$sdata = array();
						foreach ($_POST['seats'] as $seat)
						{
							list($id, $sdata['width'], $sdata['height'], $sdata['left'], $sdata['top'], $sdata['name'], $sdata['seats']) = explode("|", $seat);
							$seat2_arr[] = $id;
							$sdata['venue_id'] = $_POST['id'];
							$pjSeatModel->reset()->where('id', $id)->limit(1)->modifyAll($sdata);
						}
						$diff = array_diff($seat1_arr, $seat2_arr);
						if (count($diff) > 0)
						{
							$pjSeatModel->reset()->whereIn('id', $diff)->eraseAll();
						}
					}
					if (isset($_POST['seats_new']))
					{
						$sdata = array();
						foreach ($_POST['seats_new'] as $seat)
						{
							list(, $sdata['width'], $sdata['height'], $sdata['left'], $sdata['top'], $sdata['name'], $sdata['seats']) = explode("|", $seat);
							$sdata['venue_id'] = $_POST['id'];
							$pjSeatModel->reset()->setAttributes($sdata)->insert();
						}
					}
				}else{
				    if (isset($_SESSION[$this->sessionHall]) && !empty($_SESSION[$this->sessionHall]))
				    {
				        $STORE = $_SESSION[$this->sessionHall];
				        $_SESSION[$this->sessionHall] = NULL;
				        unset($_SESSION[$this->sessionHall]);
				    }else if(isset($_POST['number']) && !empty($_POST['number'])){
				        $STORE = array();
				        $STORE['number'] = $_POST['number'];
				    }
				    
				    if(count($STORE['number']) > 0)
				    {
				        $existing_id_arr = array();
				        $seat_arr = $pjSeatModel->reset()->where('t1.venue_id', $_POST['id'])->orderBy("t1.id ASC")->findAll()->getData();
				        foreach($seat_arr as $v)
				        {
				            $existing_id_arr[] = $v['id'];
				        }
				        
				        $seat_id_arr = array();
				        
				        foreach ($STORE['number'] as $key => $val)
				        {
				            $sdata = array();
				            $sdata['venue_id'] = $_POST['id'];
				            $sdata['name'] = $val;
				            $sdata['seats'] = 1;
				            if (strpos($key, 'new') !== false)
				            {
				                $pjSeatModel->reset()->setAttributes($sdata)->insert();
				            }else{
				                $seat_id_arr[] = $key;
				                $sdata['id'] = $key;
				                if(is_file($arr['map_path']))
				                {
				                    $sdata['width'] = ':NULL';
				                    $sdata['height'] = ':NULL';
				                    $sdata['top'] = ':NULL';
				                    $sdata['left'] = ':NULL';
				                }
				                $pjSeatModel->reset()->where('id', $key)->limit(1)->modifyAll($sdata);
				            }
				        }
				        $remove_id_arr = array_diff($existing_id_arr, $seat_id_arr);
				        foreach($remove_id_arr as $seat_id)
				        {
				            $pjSeatModel->reset()->setAttributes(array('id' => $seat_id))->erase();
				        }
				        $data['map_path'] = ':NULL';
				        $data['map_name'] = ':NULL';
				        $data['mime_type'] = ':NULL';
				    }
				}
				
				$_arr = $pjVenueModel->reset()
					->select("t1.*, (SELECT SUM(TS.seats) FROM `".$pjSeatModel->getTable()."` AS TS WHERE TS.venue_id='".$_POST['id']."') AS `cnt_seats`")
					->find($_POST['id'])
					->getData();
				
				if($_arr['cnt_seats'] > 0)
				{
					$data['seats_count'] = $_arr['cnt_seats'];
				}
				
				$pjVenueModel->reset()->where('id', $_POST['id'])->limit(1)->modifyAll(array_merge($_POST, $data));
				
				if (isset($_POST['i18n']))
				{
					pjMultiLangModel::factory()->updateMultiLang($_POST['i18n'], $_POST['id'], 'pjVenue', 'data');
				}
							
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminVenues&action=pjActionUpdate&id=".$_POST['id']."&err=AV01");
				
			} else {
				$pjMultiLangModel = pjMultiLangModel::factory();
				
				$arr = pjVenueModel::factory()->find($_GET['id'])->getData();
				
				if (count($arr) === 0)
				{
					pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminVenues&action=pjActionIndex&err=AV08");
				}
				$arr['i18n'] = $pjMultiLangModel->getMultiLang($arr['id'], 'pjVenue');
				
				$locale_arr = pjLocaleModel::factory()->select('t1.*, t2.file')
					->join('pjLocaleLanguage', 't2.iso=t1.language_iso', 'left')
					->where('t2.file IS NOT NULL')
					->orderBy('t1.sort ASC')->findAll()->getData();
						
				$lp_arr = array();
				foreach ($locale_arr as $item)
				{
					$lp_arr[$item['id']."_"] = $item['file'];
				}
				$this->set('seat_arr', pjSeatModel::factory()->where('venue_id', $_GET['id'])->orderBy("t1.id ASC")->findAll()->getData());
				
				$this->set('lp_arr', $locale_arr);
				$this->set('locale_str', pjAppController::jsonEncode($lp_arr));
				$this->set('arr', $arr);
			
				$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
				$this->appendJs('additional-methods.js', PJ_THIRD_PARTY_PATH . 'validate/');
				$this->appendJs('jquery.multilang.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
				$this->appendJs('jquery.tipsy.js', PJ_THIRD_PARTY_PATH . 'tipsy/');
				$this->appendCss('jquery.tipsy.css', PJ_THIRD_PARTY_PATH . 'tipsy/');
				$this->appendJs('pjAdminVenues.js');
			}
		} else {
			$this->set('status', 2);
		}
	}
	
	public function pjActionSector()
	{
		$this->checkLogin();
	
		if ($this->isAdmin() || $this->isEditor())
		{
			if (isset($_POST['venue_update']))
			{
				$pjVenueModel = pjVenueModel::factory();
				$pjSeatModel = pjSeatModel::factory();
	
				$arr = $pjVenueModel->find($_POST['id'])->getData();
				if (empty($arr))
				{
					pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminVenues&action=pjActionIndex&err=AV08");
				}
		
				if (isset($_POST['m_name']) && count($_POST['m_name']) > 0)
				{
					$sdata = array();
					foreach ($_POST['m_name'] as $k => $name)
					{
						$sdata['name'] = $name;
						$sdata['seats'] = $_POST['m_seats'][$k];
						
						$pjSeatModel->reset()->where('venue_id', $_POST['id'])->where('id', $k)->limit(1)->modifyAll($sdata);
					}
				}
				
				$_arr = $pjVenueModel->reset()
					->select("t1.*, (SELECT SUM(TS.seats) FROM `".$pjSeatModel->getTable()."` AS TS WHERE TS.venue_id='".$_POST['id']."') AS `cnt_seats`")
					->find($_POST['id'])
					->getData();
				if($_arr['cnt_seats'] > 0)
				{
					$pjVenueModel->reset()->where('id', $_POST['id'])->limit(1)->modifyAll(array('seats_count' => $_arr['cnt_seats']));
				}
				
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminVenues&action=pjActionSector&id=".$_POST['id']."&err=AV01");
	
			} else {
				$pjMultiLangModel = pjMultiLangModel::factory();
	
				$arr = pjVenueModel::factory()->find($_GET['id'])->getData();
	
				if (count($arr) === 0)
				{
					pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminVenues&action=pjActionIndex&err=AV08");
				}
				$arr['i18n'] = $pjMultiLangModel->getMultiLang($arr['id'], 'pjVenue');
	
				
				$this->set('seat_arr', pjSeatModel::factory()->where('venue_id', $_GET['id'])->orderBy("t1.id ASC")->findAll()->getData());
	
				$this->set('arr', $arr);
					
				$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
				$this->appendJs('pjAdminVenues.js');
			}
		} else {
			$this->set('status', 2);
		}
	}
	
	public function pjActionDeleteMap()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$pjVenueModel = pjVenueModel::factory();
			$arr = $pjVenueModel->find($_POST['id'])->getData(); 
			
			if(!empty($arr))
			{
				$map_path = $arr['map_path'];
				if (file_exists(PJ_INSTALL_PATH . $map_path)) {
					@unlink(PJ_INSTALL_PATH . $map_path);
				}
				$data = array();
				$data['seats_map'] = ':NULL';
				$pjVenueModel->reset()->where(array('id' => $_POST['id']))->limit(1)->modifyAll($data);
				pjSeatModel::factory()->where('venue_id', $_POST['id'])->eraseAll();
				
				$this->set('code', 200);
			}else{
				$this->set('code', 100);
			}
		}
	}
}
?>