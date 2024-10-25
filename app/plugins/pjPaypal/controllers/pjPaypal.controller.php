<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjPaypal extends pjPaypalAppController
{
    protected static $logPrefix = "Payments | pjPaypal plugin<br>";
    
	public function pjActionConfirm()
	{
		$this->setAjax(true);
		$this->setLayout('pjActionEmpty');
		
		$params = $this->getParams();
		$request = $params['request'];
		
		if (!isset($params['key']) || $params['key'] != md5($this->option_arr['private_key'] . PJ_SALT))
		{
		    $this->log(self::$logPrefix . "Missing or invalid 'key' parameter.");
		    $response = array('status' => 'FAIL', 'redirect' => false);
		    return $response;
		}
		if (!isset($request['paypal_order_id']) || (isset($request['paypal_order_id']) && empty($request['paypal_order_id']))) {
		    $this->log(self::$logPrefix . "Missing or invalid 'Paypal Order ID' parameter.");
		    $response = array('status' => 'FAIL', 'redirect' => false);
		    return $response;
		}
		if (isset($request['cancel_hash']) && $request['cancel_hash'] == $params['cancel_hash'])
		{
		    $this->log(self::$logPrefix . "Payment was cancelled.");
		    $response = array('status' => 'CANCEL', 'redirect' => false);
		    return $response;
		}
		
		$response = array('status' => 'FAIL', 'redirect' => false);

		$options = $this->option_arr;
		$access_token = $this->getAccessToken($options);
		
		if (PJ_TEST_MODE)
		{
		    $url  = 'https://api-m.sandbox.paypal.com/v2/checkout/orders/'.$request['paypal_order_id'].'/capture';
		} else {
		    $url  = 'https://api-m.paypal.com/v2/checkout/orders/'.$request['paypal_order_id'].'/capture';
		}
		$http = new pjHttp();
		$headers = array(
		    "Content-Type: application/json",
		    "Authorization: Bearer ".$access_token,
		    "PayPal-Request-Id: ".time()
		);
		
		$http->setHeaders($headers);
		$http->setMethod('POST');
		$resp = $http->request($url);
		$result = json_decode($resp->getResponse(), true);
		if (isset($result['status'])) {
		    if (in_array($result['status'], array('COMPLETED','APPROVED'))) {
		        if (isset($result['purchase_units'][0]['payments']['captures'][0]['id']) && !empty($result['purchase_units'][0]['payments']['captures'][0]['id'])) {
		            $txn_id = $result['purchase_units'][0]['payments']['captures'][0]['id'];
		            if (isset($result['purchase_units'][0]['payments']['captures'][0]['status']) && $result['purchase_units'][0]['payments']['captures'][0]['status'] == 'COMPLETED') {
		                if (isset($result['purchase_units'][0]['payments']['captures'][0]['amount']['value']) && $result['purchase_units'][0]['payments']['captures'][0]['amount']['value'] == $params['deposit']) {
		                    $this->log(self::$logPrefix . "TXN ID: {$txn_id}<br>AMOUNT is OK");
		                    if (isset($result['purchase_units'][0]['payments']['captures'][0]['amount']['currency_code']) && $result['purchase_units'][0]['payments']['captures'][0]['amount']['currency_code'] == $params['currency'])
		                    {
		                        $response['status'] = 'OK';
		                        $response['txn_id'] = $txn_id;
		                        $response['transaction_id'] = $txn_id;
		                        $this->log(self::$logPrefix . "Payment was successful. TXN ID: {$txn_id}.");
		                        return $response;
		                    } else {
		                        $this->log(self::$logPrefix . "TXN ID: {$txn_id}<br>CURRENCY didn't match");
		                    }
		                } else {
		                    $this->log(self::$logPrefix . "TXN ID: {$txn_id}<br>AMOUNT didn't match");
		                }
		            } else {
		                $this->log(self::$logPrefix . "TXN ID: {$txn_id}<br>Not Completed");
		            }
		        } else {
		            $this->log(self::$logPrefix . "TXN ID not found");
		        }
		    } else {
		        $this->log(self::$logPrefix . "Order status: ".$result['status']);
		        $response['status'] = $result['status'];
		    }
		} else {
		    $this->log(self::$logPrefix . "Unknow payment status.");
		    $response['status'] = 'UNKNOW';
		}
		return $response;
	}
	
	public function pjActionSave($foreign_id, $data=array())
	{
		$this->setLayout('pjActionEmpty');
		
		$params = $this->getParams();
		if (!isset($params['key']) || $params['key'] != md5($this->option_arr['private_key'] . PJ_SALT))
		{
			return FALSE;
		}
		
		return $this->pjActionSaveIpn($params['foreign_id'], $params['data']);
	}
	
	private function pjActionSaveIpn($foreign_id, $data)
	{
		return pjPaypalModel::factory()
			->setAttributes(array(
				'foreign_id' => $foreign_id,
				'subscr_id' => @$data['subscr_id'],
				'txn_id' => @$data['txn_id'],
				'txn_type' => @$data['txn_type'],
				'mc_gross' => @$data['mc_gross'],
				'mc_currency' => @$data['mc_currency'],
				'payer_email' => @$data['payer_email'],
				'dt' => date("Y-m-d H:i:s", strtotime(@$data['payment_date']))
			))
			->insert()
			->getInsertId();
	}
	
	public function pjActionForm()
	{
		$this->setLayout('pjActionEmpty');
		
		$this->setAjax(true);
		//KEYS:
		//-------------
		//name
		//id
		//business
		//item_name
		//custom
		//amount
		//currency_code
		//return
		//notify_url
		//submit
		//submit_class
		//target
		$this->set('arr', $this->getParams());
	}
/**
 * @link https://developer.paypal.com/docs/classic/paypal-payments-standard/integration-guide/Appx_websitestandard_htmlvariables/#id08A6HI00JQU
 */
	public function pjActionSubscribe()
	{
		$this->setAjax(true);
		// KEYS:
		//-------------
		//name
		//id
		//class
		//target
		//business
		//item_name => 127 chars
		//currency_code => 3 chars
		//custom => 255 chars
		//a1_price
		//p1_duration => 1-90 or 1-52 or 1-24 or 1-5 (depend of duration_unit)
		//t1_duration_unit => D,W,M,Y
		//a2_price
		//p2_duration => 1-90 or 1-52 or 1-24 or 1-5 (depend of duration_unit)
		//t2_duration_unit => D,W,M,Y
		//a3_price
		//p3_duration => 1-90 or 1-52 or 1-24 or 1-5 (depend of duration_unit)
		//t3_duration_unit => D,W,M,Y
		//recurring_payments => 0,1
		//recurring_times => 2-52
		//reattempt_on_failure => 0,1
		//return
		//cancel_return
		//notify_url
		//submit
		//submit_class
		$this->set('arr', $this->getParams());
	}

	public function pjActionGetDetails()
	{
		$this->setAjax(true);
	
		if ($this->isXHR() && $this->isLoged() && $this->isAdmin())
		{
			if (isset($_GET['id']) && (int) $_GET['id'] > 0)
			{
				$this->set('arr', pjPaypalModel::factory()->find($_GET['id'])->getData());
			}
		}
	}
	
	public function pjActionGetPaypal()
	{
		$this->setAjax(true);
	
		if ($this->isXHR() && $this->isLoged() && $this->isAdmin())
		{
			$pjPaypalModel = pjPaypalModel::factory();
			
			if (isset($_GET['q']) && !empty($_GET['q']))
			{
				$q = $pjPaypalModel->escapeStr($_GET['q']);
				$q = str_replace(array('%', '_'), array('\%', '\_'), $q);
				$pjPaypalModel->where('t1.filename LIKE', "%$q%");
			}
				
			$column = 'dt';
			$direction = 'DESC';
			if (isset($_GET['direction']) && isset($_GET['column']) && in_array(strtoupper($_GET['direction']), array('ASC', 'DESC')))
			{
				$column = $_GET['column'];
				$direction = strtoupper($_GET['direction']);
			}

			$total = $pjPaypalModel->findCount()->getData();
			$rowCount = isset($_GET['rowCount']) && (int) $_GET['rowCount'] > 0 ? (int) $_GET['rowCount'] : 10;
			$pages = ceil($total / $rowCount);
			$page = isset($_GET['page']) && (int) $_GET['page'] > 0 ? intval($_GET['page']) : 1;
			$offset = ((int) $page - 1) * $rowCount;
			if ($page > $pages)
			{
				$page = $pages;
			}

			$data = $pjPaypalModel->select('t1.*')
				->orderBy("`$column` $direction")->limit($rowCount, $offset)->findAll()->getData();
						
			pjAppController::jsonResponse(compact('data', 'total', 'pages', 'page', 'rowCount', 'column', 'direction'));
		}
		exit;
	}
	
	public function pjActionIndex()
	{
		$this->checkLogin();

		if ($this->isAdmin())
		{
			$this->appendJs('jquery.datagrid.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
			$this->appendJs('pjPaypal.js', $this->getConst('PLUGIN_JS_PATH'));
			$this->appendJs('index.php?controller=pjAdmin&action=pjActionMessages', PJ_INSTALL_URL, true);
		} else {
			$this->set('status', 2);
		}
	}
	
	private function getAccessToken($option_arr) {
	    if (PJ_TEST_MODE) {
	        $url = 'https://api-m.sandbox.paypal.com/v1/oauth2/token';
	       
	    } else {
	        $url = 'https://api-m.paypal.com/v1/oauth2/token';
	    }
	    $client_id = $option_arr['o_paypal_client_id'];
	    $client_secret = $option_arr['o_paypal_client_secret'];
	    
	    $http = new pjHttp();
	    $http->setUsername($client_id);
	    $http->setPassword($client_secret);
	    $data = array(
	        'grant_type' => 'client_credentials'
	    );
	    $http->setMethod('POST');
	    $http->setData($data);
	    $resp = $http->request($url);
	    $result = json_decode($resp->getResponse(), true);
	    $access_token = $result['access_token'];
	    
	    return $access_token;
	}
	
	public function pjActionCreateOrder() {
	    $this->setAjax(true);
	    
	    $access_token = $this->getAccessToken($this->option_arr);
	    $input = file_get_contents('php://input');
	    $post = json_decode($input, true);
	    
	    $http = new pjHttp();
	    $headers = array(
	        "Content-Type: application/json",
	        "Authorization: Bearer ".$access_token,
	        "PayPal-Request-Id: ".time()
	    );
	    
	    $data = array(
	        'intent' => 'CAPTURE',
	        'purchase_units' => array(
	            array(
	                'reference_id' => $post['cart']['reference_id'],
	                'amount' => array(
	                    'currency_code' => $post['cart']['currency'],
	                    'value' => $post['cart']['amount']
	                ),
	                'description' => $post['cart']['description']
	            )
	        )
	    );
	    
	    if (PJ_TEST_MODE) {
	        $url = 'https://api-m.sandbox.paypal.com/v2/checkout/orders';
	    } else {
	        $url = 'https://api-m.paypal.com/v2/checkout/orders';
	    }
	    
	    $http->setHeaders($headers);
	    $http->setMethod('POST');
	    $http->setData(json_encode($data), false);
	    $resp = $http->request($url);
	    $result = json_decode($resp->getResponse(), true);
	    pjAppController::jsonResponse($result);
	}
}
?>