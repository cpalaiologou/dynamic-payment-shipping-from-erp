<?php
class ModelExtensionPaymentCustomerDynamicPayment extends Model {
	public function getMethod($address, $total = 0) {
		return false;
	}

	public function getProfilesForCustomer($customer_id) {
		$query = $this->db->query("SELECT m.* FROM `" . DB_PREFIX . "customer_dynamic_payment_bind` b INNER JOIN `" . DB_PREFIX . "customer_dynamic_payment_method` m ON (b.method_id = m.method_id) WHERE b.customer_id = '" . (int)$customer_id . "' ORDER BY m.name ASC");
		return $query->rows;
	}
}
