<?php
class ModelPaymentConpayPaymentSystem extends Model
{
	public function getMethod($address, $total)
	{
		$this->language->load('payment/conpay_payment_system');

		$query = $this->db->query("SELECT * FROM ".DB_PREFIX."zone_to_geo_zone WHERE geo_zone_id = '".(int)$this->config->get('conpay_payment_system_geo_zone_id')."' AND country_id = '".(int)$address['country_id']."' AND (zone_id = '".(int)$address['zone_id']."' OR zone_id = '0')");

		if ($this->config->get('conpay_payment_system_total') > 0 && $this->config->get('conpay_payment_system_total') > $total)
		{
			$status = false;
		}
		elseif (!$this->config->get('conpay_payment_system_geo_zone_id'))
		{
			$status = true;
		}
		elseif ($query->num_rows)
		{
			$status = true;
		}
		else
		{
			$status = false;
		}

		$currencies = array('RUB',);

		if (!in_array(strtoupper($this->currency->getCode()), $currencies))
		{
			$status = false;
		}

		$method_data = array();

		if ($status)
		{
			$method_data = array(
				'code' => 'conpay_payment_system',
				'title' => $this->language->get('text_title'),
				'sort_order' => $this->config->get('conpay_payment_system_sort_order')
			);
		}

		return $method_data;
	}
}
