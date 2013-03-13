<?php
class ControllerPaymentConpayPaymentSystem extends Controller
{
	protected function index()
	{
		$this->language->load('payment/conpay_payment_system');
		$this->load->model('checkout/order');
		$this->load->model('setting/setting');

		$this->data['button_text'] = $this->config->get('conpay_payment_system_button_text');
		$this->data['order_confirmed'] = $this->language->get('order_confirmed');

		$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);

		if ($order_info)
		{
			$this->data['settings'] = $this->model_setting_setting->getSetting('conpay');
			$this->data['callback_url'] = $this->url->link('module/conpay/callback');
			$this->data['products'] = array();

			foreach ($this->cart->getProducts() as $product)
			{
				$categories = $this->_getCategoryNames($product['product_id']);

				$price = $this->currency->format(
					$this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax')),
					$order_info['currency_code'],
					false,
					false
				);

				$this->data['products'][] = array(
					'name' => $product['name'],
					'category' => $categories ? $categories : '',
					'price' => $price,
					'image' => ($host = 'http://'.$_SERVER['HTTP_HOST']).'/image/'.$product['image'],
					'quantity' => $product['quantity'],
					'url' => $host.'/index.php?route=product/product&product_id='.$product['product_id'],
					'id' => $product['product_id'],
				);
			}

			$total = $this->currency->format($order_info['total'] - $this->cart->getSubTotal(), $order_info['currency_code'], false, false);
			$this->data['total'] = $total;

			$this->data['custom'] = array('order_id' => $this->session->data['order_id'],);
			if ($v = html_entity_decode($order_info['payment_firstname'], ENT_QUOTES, 'UTF-8'))
			{
				$this->data['custom']['user_name'] = $v;
			}
			if ($v = html_entity_decode($order_info['payment_lastname'], ENT_QUOTES, 'UTF-8'))
			{
				$this->data['custom']['user_lastname'] = $v;
			}
			if ($v = $order_info['email'])
			{
				$this->data['custom']['user_email'] = $v;
			}

			if (file_exists(DIR_TEMPLATE.$this->config->get('config_template').'/template/payment/conpay_payment_system.tpl'))
			{
				$this->template = $this->config->get('config_template').'/template/payment/conpay_payment_system.tpl';
			}
			else
			{
				$this->template = 'default/template/payment/conpay_payment_system.tpl';
			}

			/// !!!
			$this->model_checkout_order->confirm($this->session->data['order_id'], $this->config->get('conpay_payment_system_order_status_id'));
			$this->cart->clear();

			$this->render();
		}
	}

	private function _getCategoryNames($product_id)
	{
		$sql = "
		SELECT name
		FROM ".DB_PREFIX."category_description cd
		LEFT JOIN ".DB_PREFIX."product_to_category p2c ON p2c.category_id = cd.category_id
		WHERE p2c.product_id = $product_id
		";

		$res = $this->db->query($sql);

		if (!$res->num_rows)
		{
			return false;
		}

		$categories = array();

		foreach ($res->rows as $row)
		{
			$categories[] = $row['name'];
		}

		return html_entity_decode(implode('; ', $categories));
	}
}
