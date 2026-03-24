<?php
class ControllerExtensionShippingCustomerDynamicShipping extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('extension/shipping/customer_dynamic_shipping');
		$this->document->setTitle($this->language->get('heading_title'));
		$this->load->model('extension/shipping/customer_dynamic_shipping');

		if ($this->request->server['REQUEST_METHOD'] == 'POST') {
			if (isset($this->request->post['action'])) {
				$this->handlePostAction();
				return;
			}

			$this->load->model('setting/setting');

			if ($this->validateSettings()) {
				$this->model_setting_setting->editSetting('shipping_customer_dynamic_shipping', $this->request->post);
				$this->session->data['success'] = $this->language->get('text_success');
				$this->response->redirect($this->url->link('extension/shipping/customer_dynamic_shipping', 'user_token=' . $this->session->data['user_token'], true));
			}
		}

		$this->getForm();
	}

	protected function handlePostAction() {
		if (!$this->user->hasPermission('modify', 'extension/shipping/customer_dynamic_shipping')) {
			$this->session->data['error_warning'] = $this->language->get('error_permission');
			$this->response->redirect($this->url->link('extension/shipping/customer_dynamic_shipping', 'user_token=' . $this->session->data['user_token'], true));
			return;
		}

		$action = $this->request->post['action'];
		$redirect = $this->url->link('extension/shipping/customer_dynamic_shipping', 'user_token=' . $this->session->data['user_token'], true);

		if (isset($this->request->get['filter_search'])) {
			$redirect .= '&filter_search=' . urlencode(html_entity_decode($this->request->get['filter_search'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['page'])) {
			$redirect .= '&page=' . (int)$this->request->get['page'];
		}

		switch ($action) {
			case 'add_method':
				if ($this->validateMethodPost()) {
					$this->model_extension_shipping_customer_dynamic_shipping->addMethod(array(
						'name'   => $this->request->post['method_name'],
						'code'   => $this->request->post['method_code'],
						'api_id' => isset($this->request->post['method_api_id']) ? $this->request->post['method_api_id'] : ''
					));
					$this->session->data['success'] = $this->language->get('text_method_added');
				}
				break;

			case 'edit_method':
				$method_id = isset($this->request->post['method_id']) ? (int)$this->request->post['method_id'] : 0;
				if ($method_id && $this->validateMethodPost()) {
					$this->model_extension_shipping_customer_dynamic_shipping->editMethod($method_id, array(
						'name'   => $this->request->post['method_name'],
						'code'   => $this->request->post['method_code'],
						'api_id' => isset($this->request->post['method_api_id']) ? $this->request->post['method_api_id'] : ''
					));
					$this->session->data['success'] = $this->language->get('text_method_updated');
				}
				break;

			case 'delete_method':
				$method_id = isset($this->request->post['method_id']) ? (int)$this->request->post['method_id'] : 0;
				if ($method_id) {
					$this->model_extension_shipping_customer_dynamic_shipping->deleteMethod($method_id);
					$this->session->data['success'] = $this->language->get('text_method_deleted');
				}
				break;

			case 'add_binding':
				$customer_id = isset($this->request->post['customer_id']) ? (int)$this->request->post['customer_id'] : 0;
				$method_id = isset($this->request->post['bind_method_id']) ? (int)$this->request->post['bind_method_id'] : 0;
				if ($customer_id && $method_id) {
					$this->model_extension_shipping_customer_dynamic_shipping->addBinding($customer_id, $method_id);
					$this->session->data['success'] = $this->language->get('text_binding_added');
				} else {
					$this->session->data['error_warning'] = $this->language->get('error_binding');
				}
				break;

			case 'delete_binding':
				$bind_id = isset($this->request->post['bind_id']) ? (int)$this->request->post['bind_id'] : 0;
				if ($bind_id) {
					$this->model_extension_shipping_customer_dynamic_shipping->deleteBinding($bind_id);
					$this->session->data['success'] = $this->language->get('text_binding_deleted');
				}
				break;
		}

		$this->response->redirect($redirect);
	}

	protected function getForm() {
		$data['heading_title'] = $this->language->get('heading_title');
		$data['text_edit'] = $this->language->get('text_edit');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_all_zones'] = $this->language->get('text_all_zones');
		$data['text_none'] = $this->language->get('text_none');
		$data['entry_cost'] = $this->language->get('entry_cost');
		$data['entry_tax'] = $this->language->get('entry_tax');
		$data['entry_geo_zone'] = $this->language->get('entry_geo_zone');
		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_sort_order'] = $this->language->get('entry_sort_order');
		$data['entry_method_name'] = $this->language->get('entry_method_name');
		$data['entry_method_code'] = $this->language->get('entry_method_code');
		$data['entry_method_api_id'] = $this->language->get('entry_method_api_id');
		$data['entry_customer'] = $this->language->get('entry_customer');
		$data['entry_bind_method'] = $this->language->get('entry_bind_method');
		$data['entry_search'] = $this->language->get('entry_search');
		$data['column_method_id'] = $this->language->get('column_method_id');
		$data['column_name'] = $this->language->get('column_name');
		$data['column_code'] = $this->language->get('column_code');
		$data['column_api_id'] = $this->language->get('column_api_id');
		$data['column_bind_id'] = $this->language->get('column_bind_id');
		$data['column_customer'] = $this->language->get('column_customer');
		$data['column_email'] = $this->language->get('column_email');
		$data['column_method'] = $this->language->get('column_method');
		$data['column_action'] = $this->language->get('column_action');
		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');
		$data['button_add_method'] = $this->language->get('button_add_method');
		$data['button_update_method'] = $this->language->get('button_update_method');
		$data['button_delete'] = $this->language->get('button_delete');
		$data['button_add_binding'] = $this->language->get('button_add_binding');
		$data['button_filter'] = $this->language->get('button_filter');
		$data['tab_general'] = $this->language->get('tab_general');
		$data['tab_methods'] = $this->language->get('tab_methods');
		$data['tab_bindings'] = $this->language->get('tab_bindings');
		$data['help_methods'] = $this->language->get('help_methods');
		$data['help_bindings'] = $this->language->get('help_bindings');
		$data['text_confirm_delete'] = $this->language->get('text_confirm_delete');
		$data['text_no_methods'] = $this->language->get('text_no_methods');
		$data['text_no_bindings'] = $this->language->get('text_no_bindings');

		$data['breadcrumbs'] = array();
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=shipping', true)
		);
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/shipping/customer_dynamic_shipping', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['user_token'] = $this->session->data['user_token'];
		$data['action'] = $this->url->link('extension/shipping/customer_dynamic_shipping', 'user_token=' . $this->session->data['user_token'], true);
		$data['filter_action'] = $data['action'];
		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=shipping', true);
		$data['autocomplete_customer'] = $this->url->link('customer/customer/autocomplete', 'user_token=' . $this->session->data['user_token'], true);

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];
			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		if (isset($this->session->data['error_warning'])) {
			$data['error_warning'] = $this->session->data['error_warning'];
			unset($this->session->data['error_warning']);
		}

		if (isset($this->request->post['shipping_customer_dynamic_shipping_cost'])) {
			$data['shipping_customer_dynamic_shipping_cost'] = $this->request->post['shipping_customer_dynamic_shipping_cost'];
		} else {
			$data['shipping_customer_dynamic_shipping_cost'] = $this->config->get('shipping_customer_dynamic_shipping_cost');
		}

		if (isset($this->request->post['shipping_customer_dynamic_shipping_tax_class_id'])) {
			$data['shipping_customer_dynamic_shipping_tax_class_id'] = $this->request->post['shipping_customer_dynamic_shipping_tax_class_id'];
		} else {
			$data['shipping_customer_dynamic_shipping_tax_class_id'] = $this->config->get('shipping_customer_dynamic_shipping_tax_class_id');
		}

		if (isset($this->request->post['shipping_customer_dynamic_shipping_geo_zone_id'])) {
			$data['shipping_customer_dynamic_shipping_geo_zone_id'] = $this->request->post['shipping_customer_dynamic_shipping_geo_zone_id'];
		} else {
			$data['shipping_customer_dynamic_shipping_geo_zone_id'] = $this->config->get('shipping_customer_dynamic_shipping_geo_zone_id');
		}

		if (isset($this->request->post['shipping_customer_dynamic_shipping_status'])) {
			$data['shipping_customer_dynamic_shipping_status'] = $this->request->post['shipping_customer_dynamic_shipping_status'];
		} else {
			$data['shipping_customer_dynamic_shipping_status'] = $this->config->get('shipping_customer_dynamic_shipping_status');
		}

		if (isset($this->request->post['shipping_customer_dynamic_shipping_sort_order'])) {
			$data['shipping_customer_dynamic_shipping_sort_order'] = $this->request->post['shipping_customer_dynamic_shipping_sort_order'];
		} else {
			$data['shipping_customer_dynamic_shipping_sort_order'] = $this->config->get('shipping_customer_dynamic_shipping_sort_order');
		}

		$this->load->model('localisation/tax_class');
		$data['tax_classes'] = $this->model_localisation_tax_class->getTaxClasses();

		$this->load->model('localisation/geo_zone');
		$data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

		$data['methods'] = $this->model_extension_shipping_customer_dynamic_shipping->getMethods();

		$filter_search = '';
		if (isset($this->request->get['filter_search'])) {
			$filter_search = $this->request->get['filter_search'];
		}

		$data['filter_search'] = $filter_search;

		if (isset($this->request->get['page'])) {
			$page = (int)$this->request->get['page'];
		} else {
			$page = 1;
		}

		$limit = 20;
		$filter = array(
			'filter_search' => $filter_search,
			'start'         => ($page - 1) * $limit,
			'limit'         => $limit
		);

		$data['bindings'] = $this->model_extension_shipping_customer_dynamic_shipping->getBindings($filter);
		$binding_total = $this->model_extension_shipping_customer_dynamic_shipping->getTotalBindings(array('filter_search' => $filter_search));

		$bindings_query = '';
		if ($filter_search !== '') {
			$bindings_query .= '&filter_search=' . urlencode(html_entity_decode($filter_search, ENT_QUOTES, 'UTF-8'));
		}
		$bindings_query .= '&page=' . $page;
		$data['action_bindings'] = $this->url->link('extension/shipping/customer_dynamic_shipping', 'user_token=' . $this->session->data['user_token'] . $bindings_query, true);

		$pagination = new Pagination();
		$pagination->total = $binding_total;
		$pagination->page = $page;
		$pagination->limit = $limit;
		$pagination->url = $this->url->link('extension/shipping/customer_dynamic_shipping', 'user_token=' . $this->session->data['user_token'] . '&page={page}' . ($filter_search !== '' ? '&filter_search=' . urlencode(html_entity_decode($filter_search, ENT_QUOTES, 'UTF-8')) : ''), true);

		$data['pagination'] = $pagination->render();
		$data['results'] = sprintf($this->language->get('text_pagination'), ($binding_total) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($binding_total - $limit)) ? $binding_total : ((($page - 1) * $limit) + $limit), $binding_total, ceil($binding_total / $limit));

		$data['edit_method_id'] = 0;
		$data['edit_method_name'] = '';
		$data['edit_method_code'] = '';
		$data['edit_method_api_id'] = '';

		if (isset($this->request->get['edit_method'])) {
			$edit = $this->model_extension_shipping_customer_dynamic_shipping->getMethod((int)$this->request->get['edit_method']);
			if ($edit) {
				$data['edit_method_id'] = $edit['method_id'];
				$data['edit_method_name'] = $edit['name'];
				$data['edit_method_code'] = $edit['code'];
				$data['edit_method_api_id'] = $edit['api_id'];
			}
		}

		$data['button_edit'] = $this->language->get('button_edit');

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/shipping/customer_dynamic_shipping', $data));
	}

	protected function validateSettings() {
		if (!$this->user->hasPermission('modify', 'extension/shipping/customer_dynamic_shipping')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		return !$this->error;
	}

	protected function validateMethodPost() {
		if (!$this->user->hasPermission('modify', 'extension/shipping/customer_dynamic_shipping')) {
			$this->session->data['error_warning'] = $this->language->get('error_permission');
			return false;
		}

		if (!isset($this->request->post['method_name']) || utf8_strlen(trim($this->request->post['method_name'])) < 1) {
			$this->session->data['error_warning'] = $this->language->get('error_method_name');
			return false;
		}

		if (!isset($this->request->post['method_code']) || utf8_strlen(trim($this->request->post['method_code'])) < 1) {
			$this->session->data['error_warning'] = $this->language->get('error_method_code');
			return false;
		}

		return true;
	}

	public function install() {
		$this->load->model('extension/shipping/customer_dynamic_shipping');
		$this->model_extension_shipping_customer_dynamic_shipping->install();

		$this->load->model('setting/setting');
		$this->model_setting_setting->editSetting('shipping_customer_dynamic_shipping', array(
			'shipping_customer_dynamic_shipping_cost'           => '0',
			'shipping_customer_dynamic_shipping_tax_class_id'   => 0,
			'shipping_customer_dynamic_shipping_geo_zone_id'    => 0,
			'shipping_customer_dynamic_shipping_status'         => 0,
			'shipping_customer_dynamic_shipping_sort_order'     => 0
		));
	}

	public function uninstall() {
		$this->load->model('extension/shipping/customer_dynamic_shipping');
		$this->model_extension_shipping_customer_dynamic_shipping->uninstall();
	}
}
