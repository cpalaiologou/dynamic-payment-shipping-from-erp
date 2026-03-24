<?php
class ControllerExtensionPaymentCustomerDynamicPayment extends Controller {
	public function augment(&$route, &$args, &$output) {
		if (!$this->config->get('payment_customer_dynamic_payment_status')) {
			return;
		}

		if (!$this->customer->isLogged()) {
			return;
		}

		$address = isset($args[0]) ? $args[0] : array();

		if (!$this->addressMatchesGeoZone($address)) {
			return;
		}

		$cart_total = $this->cart->getTotal();
		$min = (float)$this->config->get('payment_customer_dynamic_payment_total_min');
		$max = (float)$this->config->get('payment_customer_dynamic_payment_total_max');

		if ($min > 0 && $cart_total < $min) {
			return;
		}

		if ($max > 0 && $cart_total > $max) {
			return;
		}

		$this->load->model('extension/payment/customer_dynamic_payment');
		$profiles = $this->model_extension_payment_customer_dynamic_payment->getProfilesForCustomer((int)$this->customer->getId());

		if (!$profiles) {
			return;
		}

		if (!is_array($output)) {
			$output = array();
		}

		$sort_base = (int)$this->config->get('payment_customer_dynamic_payment_sort_order');

		foreach ($profiles as $row) {
			$mid = (int)$row['method_id'];
			$key = 'customer_dynamic_payment_m' . $mid;
			$output[$key] = array(
				'code'       => 'customer_dynamic_payment.m' . $mid,
				'title'      => $row['name'],
				'terms'      => '',
				'sort_order' => $sort_base + $mid
			);
		}
	}

	private function addressMatchesGeoZone($address) {
		$geo_zone_id = (int)$this->config->get('payment_customer_dynamic_payment_geo_zone_id');

		if (!$geo_zone_id) {
			return true;
		}

		if (!isset($address['country_id']) || !isset($address['zone_id'])) {
			return false;
		}

		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "zone_to_geo_zone` WHERE `geo_zone_id` = '" . $geo_zone_id . "' AND `country_id` = '" . (int)$address['country_id'] . "' AND (`zone_id` = '" . (int)$address['zone_id'] . "' OR `zone_id` = '0')");

		return (bool)$query->num_rows;
	}

	public function index() {
		$this->response->redirect($this->url->link('checkout/cart'));
	}
}
