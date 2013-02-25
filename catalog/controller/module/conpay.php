<?php

class ControllerModuleConpay extends Controller {
	protected function index($setting) {
		
		$this->language->load('module/conpay');
		
		$this->load->model('catalog/category');
		$this->load->model('catalog/product');
		$this->load->model('setting/setting');
		
		$route = $this->request->get['route'];
		
		$category = '';
		
		switch($route)
		{
			case 'product/product':
				
				$product_id = 0;
				
				if (isset($this->request->get['product_id'])) {
					$product_id = (int) $this->request->get['product_id'];
				}
				
				$results = array($this->model_catalog_product->getProduct($product_id));
				
				break;
			
			case 'product/category':
				
				if (isset($this->request->get['path'])) {
					$parts = explode('_', (string) $this->request->get['path']);
					$category_id = (int) array_pop($parts);
				} else {
					$category_id = 0;
				}
				
				$category_info = $this->model_catalog_category->getCategory($category_id);
				
				if (!$category_info) return false; 
				
				$category = html_entity_decode($category_info['name']);
				
				$this->data['credit_purchase'] = $this->language->get('conpay_credit_purchase');
				
				if (isset($this->request->get['filter'])) {
					$filter = $this->request->get['filter'];
				} else {
					$filter = '';
				}
				
				if (isset($this->request->get['sort'])) {
					$sort = $this->request->get['sort'];
				} else {
					$sort = 'p.sort_order';
				}
				
				if (isset($this->request->get['order'])) {
					$order = $this->request->get['order'];
				} else {
					$order = 'ASC';
				}
				
				if (isset($this->request->get['page'])) {
					$page = $this->request->get['page'];
				} else { 
					$page = 1;
				}
				
				if (isset($this->request->get['limit'])) {
					$limit = $this->request->get['limit'];
				} else {
					$limit = $this->config->get('config_catalog_limit');
				}
				
				$data = array(
					'filter_category_id' => $category_id,
					'filter_filter'      => $filter, 
					'sort'               => $sort,
					'order'              => $order,
					'start'              => ($page - 1) * $limit,
					'limit'              => $limit
				);
				
				$results = $this->model_catalog_product->getProducts($data);
				
				break;
		}
		
		$settings = $this->model_setting_setting->getSetting('conpay');
		
		$this->data['settings'] = $settings;
		$this->data['products'] = array();
		$price = 0;
		
		foreach ($results as $result) {
			
			if ($result['image']) {
				$image = $result['image'];
			} else {
				$image = false;
			}
			
			if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
				$price = $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax')));
			} else {
				$price = false;
			}
			
			if ((float) $result['special']) {
				$special = $this->currency->format($this->tax->calculate($result['special'], $result['tax_class_id'], $this->config->get('config_tax')));
			} else {
				$special = false;
			}
			
			$p = (float) preg_replace('/[$,]/s', '', ($special? $special : $price));
			
			if ($p < $settings['min_price']) continue;
			
			$this->data['products'][] = array(
				'id'  				=> $result['product_id'],
				'image'       => 'http://' . $_SERVER['HTTP_HOST'] . '/image/' . $image,
				'name'        => $result['name'],
				'price'       => $p,
				'category'		=> $category? $category : $this->_getCategoryNames($result['product_id']),
				'url'        	=> $this->url->link('product/product', 'product_id=' . $result['product_id'])
			);
			
			$price += $p;
		}
		
		//if ($price < $settings['min_price']) return false;
		$this->data['callback_url'] = $this->url->link('module/conpay/callback');
		
		$this->document->addScript('http://www.conpay.ru/public/api/btn.1.6.min.js');
		
		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/module/conpay.tpl')) {
			$this->template = $this->config->get('config_template') . '/template/module/conpay.tpl';
		} else {
			$this->template = 'default/template/module/conpay.tpl';
		}
		
		$this->response->setOutput($this->render());
	}
	
	public function callback()
	{
		$this->load->model('setting/setting');
		$settings = $this->model_setting_setting->getSetting('conpay');
		
		if (!$settings) $settings = array('merchant_id' => 0, 'api_key' => '');
		
		// Подключаем скрипт с классом ConpayProxyModel, выполняющим бизнес-логику
		require_once 'ConpayProxyModel.php';
		try
		{
			// Создаем объект класса ConpayProxyModel
			$proxy = new ConpayProxyModel;
			// Устанавливаем свой идентификатор продавца
			$proxy->setMerchantId($settings['merchant_id']);
			// Устанавливаем свой API-ключ
			$proxy->setApiKey($settings['api_key']);
			// Устанавливаем кодировку, используемую на сайте (по-умолчанию 'UTF-8')
			$proxy->setCharset('UTF-8');
			// Выполняем запрос, выводя его результат
			echo $proxy->sendRequest();
		}
		catch (Exception $e) {
			echo json_encode(array('error'=>$e->getMessage()));
		}
	}
	
	public function addorder()
	{
		if($_SERVER['REQUEST_METHOD'] != 'POST') die('Access denied');
		
		$post =& $this->request->post;
		
		$this->load->model('setting/setting');
		$settings = $this->model_setting_setting->getSetting('conpay');
		$ps_settings = $this->model_setting_setting->getSetting('conpay_payment_system');
		
		$response_pass = $settings['response_pass'];
		$merchant_id = $settings['merchant_id'];
		$total = 0;
		
		if (isset($post['goods']))
		{
			foreach ((array) $post['goods'] as $item)
			{
				$total += (float) $item['price'];
			}
		}
		
		if (!isset($post['delivery'])) $this->request->post['delivery'] = 0;
		
		$parts = array
		(
			$response_pass,
			is_numeric($post['delivery'])? $total + $post['delivery'] : $total,
			$merchant_id,
		);
		
		if (isset($post['custom']))
		{
			foreach ($post['custom'] as $v)
			{
				$parts[] = $v;
			}
		}
		
		$checksum = md5(implode('!', $parts));
		
		if ($post['checksum'] != $checksum
				|| $_SERVER['HTTP_REFERER'] != 'https://www.conpay.ru'
				|| $_SERVER['HTTP_USER_AGENT'] != 'Conpay')
		{
			die('Access denied.');
		}
		
		$customer = (array) $post['customer'];
		$custom = (array) $post['custom'];
		
		if ($custom['order_id']) die('Order already exists.');
		
		$this->load->model('checkout/order');
		
		$currency_code = 'RUB';
		$res = $this->db->query("SELECT currency_id FROM " . DB_PREFIX . "currency WHERE code = '" . $currency_code . "'");
		$currency_id = $res->row['currency_id'];
		
		// Add order
		
		$data = array
		(
			"invoice_prefix" 	=> $this->config->get('config_invoice_prefix'),
			"store_id" 				=> $this->config->get('config_store_id'),
			"store_name" 			=> $this->config->get('config_name'),
		);
		
		if ($data['store_id']) {
			$data['store_url'] = $this->config->get('config_url');		
		} else {
			$data['store_url'] = HTTP_SERVER;	
		}
		
		$data["customer_id"] 				= 0;
		$data["customer_group_id"] 	= 1;
		$data["firstname"] 					= $customer['FirstName'];
		$data["lastname"] 					= $customer['LastName'];
		$data["email"] 							= $customer['Email'];
		$data["telephone"] 					= ''; //$customer['Phone']
		$data["fax"] 								= '';
		
		$data['payment_firstname'] 			= $customer['FirstName'];
		$data['payment_lastname'] 			= $customer['LastName'];	
		$data['payment_company'] 				= '';	
		$data['payment_company_id'] 		= '';	
		$data['payment_tax_id'] 				= '';	
		$data['payment_address_1'] 			= '';
		$data['payment_address_2'] 			= '';
		$data['payment_city'] 					= '';
		$data['payment_postcode'] 			= '';
		$data['payment_zone'] 					= '';
		$data['payment_zone_id'] 				= 0;
		$data['payment_country'] 				= '';
		$data['payment_country_id'] 		= 0;
		$data['payment_address_format'] = "
{firstname} {lastname}
{company}
{address_1}
{address_2}
{city}, {zone} {postcode}
{country}";
		$data['payment_method'] 					= 'Conpay';
		$data['payment_code'] 						= 'conpay_payment_system';

		$data['shipping_firstname'] 			= '';
		$data['shipping_lastname'] 				= '';	
		$data['shipping_company'] 				= '';	
		$data['shipping_address_1'] 			= '';
		$data['shipping_address_2'] 			= '';
		$data['shipping_city'] 						= '';
		$data['shipping_postcode'] 				= '';
		$data['shipping_zone'] 						= '';
		$data['shipping_zone_id'] 				= '';
		$data['shipping_country'] 				= '';
		$data['shipping_country_id'] 			= '';
		$data['shipping_address_format'] 	= '';
		$data['shipping_method'] 					= '';
		$data['shipping_code'] 						= '';
		
		$data['products'] = array();
		
		foreach((array) $post['goods'] as $i => $item)
		{
			$data['products'][] = array
			(
				"product_id" 	=> $item['id'],
				"name" 				=> $item['name'],
				"model" 			=> '',
				"option" 			=> array(),
				"download" 		=> array(),
				"quantity" 		=> IntVal($item['quantity']),
				"subtract" 		=> '1', //???
				"price" 			=> $item['price'],
				"total" 			=> $item['price'],
				"tax" 				=> 0,
				"reward" 			=> 0,
			);
		}
		
		$data["vouchers"] = array();
		
		$data["totals"] = array
		(
			array
			(
				"code" 				=> "total",
				"title" 			=> "Total",
				"text" 				=> $total,
				"value" 			=> $total,
				"sort_order" 	=> 0,
			)
		);
		
		$data["comment"] 			= '';
		$data["total"] 				= (float) $total;
		$data['affiliate_id'] = 0;
		$data['commission'] 	= 0;
		
		$data["language_id"] 		= $this->config->get('config_language_id');
		$data["currency_id"] 		= $currency_id;
		$data["currency_code"] 	= $currency_code;
		$data["currency_value"] = $total;
		
		$data["ip"] 							= '';
		$data["forwarded_ip"] 		= '';
		$data["user_agent"] 			= '';
		$data["accept_language"] 	= '';
		
		$order_id = $this->model_checkout_order->addOrder($data);
		
		$this->model_checkout_order->confirm($order_id, $ps_settings['conpay_payment_system_order_status_id']);
		
		//$this->model_checkout_order->update($order_id, $order_status_id);
	}
	
	private function _getCategoryNames($product_id) {
		
		$sql = "
		SELECT name
		FROM " . DB_PREFIX . "category_description cd
		LEFT JOIN " . DB_PREFIX . "product_to_category p2c ON p2c.category_id = cd.category_id
		WHERE p2c.product_id = $product_id
		";
		
		$res = $this->db->query($sql);
		
		if (!$res->num_rows) return false;
		
		$categories = array();
		
		foreach ($res->rows as $row) {
			$categories[] = $row['name'];
		}
		
		return html_entity_decode(implode('; ', $categories));
	}
}

?>