<?php
class ModelExtensionPaymentCustomerDynamicPayment extends Model {
	public function getMethod($address, $total = 0) {
		if (!$this->config->get('payment_customer_dynamic_payment_status')) {
			return false;
		}

		if (!$this->customer->isLogged()) {
			return false;
		}

		if (!$this->addressMatchesGeoZone($address)) {
			return false;
		}

		$cart_total = $this->cart->getTotal();
		$min = (float)$this->config->get('payment_customer_dynamic_payment_total_min');
		$max = (float)$this->config->get('payment_customer_dynamic_payment_total_max');

		if ($min > 0 && $cart_total < $min) {
			return false;
		}

		if ($max > 0 && $cart_total > $max) {
			return false;
		}

		$query = $this->db->query("SELECT m.* FROM `" . DB_PREFIX . "customer_dynamic_payment_bind` b INNER JOIN `" . DB_PREFIX . "customer_dynamic_payment_method` m ON (b.method_id = m.method_id) WHERE b.customer_id = '" . (int)$this->customer->getId() . "' ORDER BY m.name ASC LIMIT 1");

		if (!$query->num_rows) {
			return false;
		}

		$row = $query->row;
		$mid = (int)$row['method_id'];
		return array(
			'code'       => 'customer_dynamic_payment',
			'title'      => $row['name'],
			'terms'      => '',
			'sort_order' => (int)$this->config->get('payment_customer_dynamic_payment_sort_order'),
			'cdp_method_id' => $mid,
			'cdp_erp_code'  => $row['code'],
			'cdp_api_id'    => $row['api_id']
		);
	}

	private function addressMatchesGeoZone($address) {
		$geo_zone_id = (int)$this->config->get('payment_customer_dynamic_payment_geo_zone_id');

		if (!$geo_zone_id) {
			return true;
		}

		if (!is_array($address) || !isset($address['country_id']) || !isset($address['zone_id'])) {
			return false;
		}

		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "zone_to_geo_zone` WHERE `geo_zone_id` = '" . $geo_zone_id . "' AND `country_id` = '" . (int)$address['country_id'] . "' AND (`zone_id` = '" . (int)$address['zone_id'] . "' OR `zone_id` = '0')");

		return (bool)$query->num_rows;
	}
}
