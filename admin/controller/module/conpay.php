<?php
class ControllerModuleConpay extends Controller
{
	private $error = array();

	public function index()
	{
		$this->language->load('module/conpay');
		$this->document->setTitle($this->language->get('heading_title'));
		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate())
		{
			$this->model_setting_setting->editSetting('conpay', $this->request->post);
			$this->session->data['success'] = $this->language->get('text_success');
			$this->redirect($this->url->link('extension/module', 'token='.$this->session->data['token'], 'SSL'));
		}

		$this->data['settings'] = $this->model_setting_setting->getSetting('conpay');

		if (!$this->data['settings'])
		{
			$this->data['settings'] = array(
				'merchant_id' => 0,
				'api_key' => '',
				'response_pass' => '',
				'button_container_id' => 'conpay-btn-container',
				'button_class_name' => 'conpay-btn',
				'button_tag_name' => 'a',
				'button_text' => '<span class="conpay-btn-credit"><b></b>Купить в кредит</span> от <b>{monthly}</b> р. в месяц',
				'min_price' => 3000,
			);
		}

		$this->data['heading_title'] = $this->language->get('heading_title');

		$this->data['text_enabled'] = $this->language->get('text_enabled');
		$this->data['text_disabled'] = $this->language->get('text_disabled');
		$this->data['text_content_top'] = $this->language->get('text_content_top');
		$this->data['text_content_bottom'] = $this->language->get('text_content_bottom');
		$this->data['text_column_left'] = $this->language->get('text_column_left');
		$this->data['text_column_right'] = $this->language->get('text_column_right');

		$this->data['entry_layout'] = $this->language->get('entry_layout');
		$this->data['entry_position'] = $this->language->get('entry_position');
		$this->data['entry_status'] = $this->language->get('entry_status');
		$this->data['entry_sort_order'] = $this->language->get('entry_sort_order');

		$this->data['merchant_id'] = $this->language->get('merchant_id');
		$this->data['api_key'] = $this->language->get('api_key');
		$this->data['response_pass'] = $this->language->get('response_pass');
		$this->data['button_container_id'] = $this->language->get('button_container_id');
		$this->data['button_class_name'] = $this->language->get('button_class_name');
		$this->data['button_tag_name'] = $this->language->get('button_tag_name');
		$this->data['button_text'] = $this->language->get('button_text');
		$this->data['min_price'] = $this->language->get('min_price');

		$this->data['button_save'] = $this->language->get('button_save');
		$this->data['button_cancel'] = $this->language->get('button_cancel');
		$this->data['button_add_module'] = $this->language->get('button_add_module');
		$this->data['button_remove'] = $this->language->get('button_remove');

		if (isset($this->error['warning']))
		{
			$this->data['error_warning'] = $this->error['warning'];
		}
		else
		{
			$this->data['error_warning'] = '';
		}

		if (isset($this->error['image']))
		{
			$this->data['error_image'] = $this->error['image'];
		}
		else
		{
			$this->data['error_image'] = array();
		}

		$this->data['breadcrumbs'] = array();

		$this->data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home', 'token='.$this->session->data['token'], 'SSL'),
			'separator' => false
		);

		$this->data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_module'),
			'href' => $this->url->link('extension/module', 'token='.$this->session->data['token'], 'SSL'),
			'separator' => ' :: '
		);

		$this->data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('module/conpay', 'token='.$this->session->data['token'], 'SSL'),
			'separator' => ' :: '
		);

		$this->data['action'] = $this->url->link('module/conpay', 'token='.$this->session->data['token'], 'SSL');
		$this->data['cancel'] = $this->url->link('extension/module', 'token='.$this->session->data['token'], 'SSL');
		$this->data['token'] = $this->session->data['token'];
		$this->data['modules'] = array();

		if (isset($this->request->post['conpay_module']))
		{
			$this->data['modules'] = $this->request->post['conpay_module'];
		}
		elseif ($this->config->get('conpay_module'))
		{
			$this->data['modules'] = $this->config->get('conpay_module');
		}

		$this->load->model('design/layout');
		$this->data['layouts'] = $this->model_design_layout->getLayouts();

		$this->template = 'module/conpay.tpl';
		$this->children = array('common/header', 'common/footer');

		$this->response->setOutput($this->render());
	}

	protected function validate()
	{
		if (!$this->user->hasPermission('modify', 'module/conpay'))
		{
			$this->error['warning'] = $this->language->get('error_permission');
		}
		if (!$this->error)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
}
