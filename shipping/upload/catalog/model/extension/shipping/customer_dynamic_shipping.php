<?php
class ModelExtensionShippingCustomerDynamicShipping extends Model {
	public function getQuote($address) {
		$this->load->language('extension/shipping/customer_dynamic_shipping');

		if (!$this->config->get('shipping_customer_dynamic_shipping_status')) {
			return false;
		}

		if ($this->config->get('shipping_customer_dynamic_shipping_geo_zone_id')) {
			$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "zone_to_geo_zone` WHERE `geo_zone_id` = '" . (int)$this->config->get('shipping_customer_dynamic_shipping_geo_zone_id') . "' AND `country_id` = '" . (int)$address['country_id'] . "' AND (`zone_id` = '" . (int)$address['zone_id'] . "' OR `zone_id` = '0')");
			$status = $query->num_rows ? true : false;
		} else {
			$status = true;
		}

		if (!$status) {
			return false;
		}

		if (!$this->customer->isLogged()) {
			return false;
		}

		$customer_id = (int)$this->customer->getId();
		$methods = $this->getMethodsForCustomer($customer_id);

		if (!$methods) {
			return false;
		}

		$cost = (float)$this->config->get('shipping_customer_dynamic_shipping_cost');
		$tax_class_id = (int)$this->config->get('shipping_customer_dynamic_shipping_tax_class_id');
		$quote_data = array();

		foreach ($methods as $row) {
			$key = 'm' . (int)$row['method_id'];
			$quote_data[$key] = array(
				'code'         => 'customer_dynamic_shipping.' . $key,
				'title'        => $row['name'],
				'cost'         => $cost,
				'tax_class_id' => $tax_class_id,
				'text'         => $this->currency->format($this->tax->calculate($cost, $tax_class_id, $this->config->get('config_tax')), $this->session->data['currency'])
			);
		}

		$method_data = array(
			'code'       => 'customer_dynamic_shipping',
			'title'      => $this->language->get('text_title'),
			'quote'      => $quote_data,
			'sort_order' => $this->config->get('shipping_customer_dynamic_shipping_sort_order'),
			'error'      => false
		);

		return $method_data;
	}

	private function getMethodsForCustomer($customer_id) {
		$query = $this->db->query("SELECT m.* FROM `" . DB_PREFIX . "customer_dynamic_shipping_bind` b INNER JOIN `" . DB_PREFIX . "customer_dynamic_shipping_method` m ON (b.method_id = m.method_id) WHERE b.customer_id = '" . (int)$customer_id . "' ORDER BY m.name ASC");
		return $query->rows;
	}
}
