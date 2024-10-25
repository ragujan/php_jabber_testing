<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjAdminOptions extends pjAdmin
{
	public function pjActionIndex()
	{
		$this->checkLogin();

		if ($this->isAdmin())
		{
			$arr = pjOptionModel::factory()
				->where('t1.foreign_id', $this->getForeignId())
				->orderBy('t1.order ASC, t1.`key` ASC')
				->findAll()
				->getData();
			
			$this->set('arr', $arr);
			
			$this->appendJs('jquery.tipsy.js', PJ_THIRD_PARTY_PATH . 'tipsy/');
			$this->appendCss('jquery.tipsy.css', PJ_THIRD_PARTY_PATH . 'tipsy/');
			$this->appendJs('pjAdminOptions.js');
		} else {
			$this->set('status', 2);
		}
	}
	
	public function pjActionUpdate()
	{
		$this->checkLogin();

		if ($this->isAdmin())
		{
			$post_max_size = pjUtil::getPostMaxSize();
			if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SERVER['CONTENT_LENGTH']) && (int) $_SERVER['CONTENT_LENGTH'] > $post_max_size)
			{
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminOptions&action=pjActionTicket&err=AO07");
			}
			if (isset($_POST['options_update']))
			{
				$OptionModel = new pjOptionModel();
			
				foreach ($_POST as $key => $value)
				{
					if (preg_match('/value-(string|text|int|float|enum|bool|color)-(.*)/', $key) === 1)
					{
						list(, $type, $k) = explode("-", $key);
						if (!empty($k))
						{
							$OptionModel
								->reset()
								->where('foreign_id', $this->getForeignId())
								->where('`key`', $k)
								->limit(1)
								->modifyAll(array('value' => $value));
						}
					}
				}
				if (isset($_POST['i18n']))
				{
					pjMultiLangModel::factory()->updateMultiLang($_POST['i18n'], 1, 'pjOption', 'data');
				}
				
				if (isset($_FILES['ticket_img']))
				{
					if($_FILES['ticket_img']['error'] == 0)
					{
						if (is_writable('app/web/upload/tickets'))
						{
							$Image = new pjImage();
							if ($Image->getErrorCode() !== 200)
							{
								$Image->setAllowedTypes(array('image/png', 'image/gif', 'image/jpg', 'image/jpeg', 'image/pjpeg'));
								if ($Image->load($_FILES['ticket_img']))
								{
									$resp = $Image->isConvertPossible();
									if ($resp['status'] === true)
									{
										$hash = md5(uniqid(rand(), true));
										$image_path = PJ_UPLOAD_PATH . 'tickets/' . $hash . '.' . $Image->getExtension();
				
										$Image->loadImage();
										$Image->saveImage($image_path);
										$data = array();
											
										$OptionModel
											->reset()
											->where('foreign_id', $this->getForeignId())
											->where('`key`', 'o_ticket_image')
											->limit(1)
											->modifyAll(array('value' => $image_path));
									}
								}
							}
						}else{
							$err = 'AE12';
						}
					}else if($_FILES['ticket_img']['error'] != 4){
						$err = 'AE09';
					}
				}
				
				if (isset($_POST['next_action']))
				{
					switch ($_POST['next_action'])
					{
						case 'pjActionIndex':
							$err = 'AO01';
							break;
						case 'pjActionBooking':
							$err = 'AO02';
							break;
						case 'pjActionNotification':
							$err = 'AO03&tab_id=' . $_POST['tab_id'];
							break;
						case 'pjActionBookingForm':
							$err = 'AO04';
							break;
						case 'pjActionTicket':
							$err = 'AO05';
							break;
						case 'pjActionTerm':
							$err = 'AO06';
							break;
					}
				}
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminOptions&action=" . @$_POST['next_action'] . "&err=$err");
			}
		} else {
			$this->set('status', 2);
		}
	}
	
	public function pjActionBooking()
	{
		$this->checkLogin();
	
		if ($this->isAdmin())
		{
			$pjOptionModel = pjOptionModel::factory()
				->where('t1.foreign_id', $this->getForeignId())
				->orderBy('t1.order ASC')
				->findAll();
				
			$this->set('arr', $pjOptionModel->getData());
			$this->set('o_arr', $pjOptionModel->getDataPair('key'));
				
			$this->appendJs('pjAdminOptions.js');
		} else {
			$this->set('status', 2);
		}
	}
	
	public function pjActionBookingForm()
	{
		$this->checkLogin();
	
		if ($this->isAdmin())
		{
			$arr = pjOptionModel::factory()
				->where('t1.foreign_id', $this->getForeignId())
				->orderBy('t1.order ASC')
				->findAll()
				->getData();
				
			$this->set('arr', $arr);
			$this->appendJs('pjAdminOptions.js');
		} else {
			$this->set('status', 2);
		}
	}
	
	public function pjActionTerm()
	{
		$this->checkLogin();
	
		if ($this->isAdmin())
		{
				
			$arr = pjOptionModel::factory()
				->where('t1.foreign_id', $this->getForeignId())
				->orderBy('t1.order ASC')
				->findAll()
				->getData();
				
			$arr['i18n'] = pjMultiLangModel::factory()->getMultiLang(1, 'pjOption');
	
			$this->set('arr', $arr);
				
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
				
			$this->appendJs('jquery.multilang.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
			$this->appendJs('jquery.tipsy.js', PJ_THIRD_PARTY_PATH . 'tipsy/');
			$this->appendCss('jquery.tipsy.css', PJ_THIRD_PARTY_PATH . 'tipsy/');
			$this->appendJs('pjAdminOptions.js');
				
		}
	}
	
	public function pjActionTicket()
	{
		$this->checkLogin();
	
		if ($this->isAdmin())
		{
	
			$arr = pjOptionModel::factory()
				->where('t1.foreign_id', $this->getForeignId())
				->orderBy('t1.order ASC')
				->findAll()
				->getData();
	
			$arr['i18n'] = pjMultiLangModel::factory()->getMultiLang(1, 'pjOption');
	
			$this->set('arr', $arr);
	
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
	
			$this->appendJs('jquery.multilang.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
			$this->appendJs('jquery.tipsy.js', PJ_THIRD_PARTY_PATH . 'tipsy/');
			$this->appendCss('jquery.tipsy.css', PJ_THIRD_PARTY_PATH . 'tipsy/');
			$this->appendJs('pjAdminOptions.js');
	
		}
	}
	
	public function pjActionNotification()
	{
		$this->checkLogin();
	
		if ($this->isAdmin())
		{
				
			$arr = pjOptionModel::factory()
				->where('t1.foreign_id', $this->getForeignId())
				->orderBy('t1.order ASC')
				->findAll()
				->getData();
				
			$arr['i18n'] = pjMultiLangModel::factory()->getMultiLang(1, 'pjOption');
	
			$this->set('arr', $arr);
				
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
				
			$this->appendJs('jquery.multilang.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
			$this->appendJs('jquery.tipsy.js', PJ_THIRD_PARTY_PATH . 'tipsy/');
			$this->appendCss('jquery.tipsy.css', PJ_THIRD_PARTY_PATH . 'tipsy/');
			$this->appendJs('tinymce.min.js', PJ_THIRD_PARTY_PATH . 'tinymce/');
			$this->appendJs('pjAdminOptions.js');
		}
	}
	
	public function pjActionInstall()
	{
		$this->checkLogin();
		
		if ($this->isAdmin())
		{
			$locale_arr = pjLocaleModel::factory()->select('t1.*, t2.title')
				->join('pjLocaleLanguage', 't2.iso=t1.language_iso', 'left outer')
				->orderBy('t1.sort ASC')->findAll()->getData();
			$this->set('locale_arr', $locale_arr);
					
			$this->appendJs('pjAdminOptions.js');
		} else {
			$this->set('status', 2);
		}
	}
		
	public function pjActionPreview()
	{
		$this->setLayout('pjActionEmpty');
	}
	

	public function pjActionDeleteTicket()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$response = array();
	
			if(!empty($this->option_arr['o_ticket_image']))
			{
				@unlink(PJ_INSTALL_PATH . $this->option_arr['o_ticket_image']);
			}
			
			pjOptionModel::factory()
				->where('foreign_id', $this->getForeignId())
				->where('`key`', 'o_ticket_image')
				->limit(1)
				->modifyAll(array('value' => ":NULL"));

			$response['code'] = 200;
			
			pjAppController::jsonResponse($response);
		}
	}
	public function pjActionUpdateTheme()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			pjOptionModel::factory()
			->where('foreign_id', $this->getForeignId())
			->where('`key`', 'o_theme')
			->limit(1)
			->modifyAll(array('value' => 'theme1|theme2|theme3|theme4|theme5|theme6|theme7|theme8|theme9|theme10::theme' . $_GET['theme']));
	
		}
	}
}
?>