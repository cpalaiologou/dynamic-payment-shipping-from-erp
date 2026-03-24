<?php
class ModelExtensionPaymentCustomerDynamicPayment extends Model {
	public function install() {
		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "customer_dynamic_payment_method` (
			`method_id` int(11) NOT NULL AUTO_INCREMENT,
			`name` varchar(255) NOT NULL,
			`code` varchar(128) NOT NULL,
			`api_id` varchar(255) NOT NULL DEFAULT '',
			PRIMARY KEY (`method_id`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci");

		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "customer_dynamic_payment_bind` (
			`bind_id` int(11) NOT NULL AUTO_INCREMENT,
			`customer_id` int(11) NOT NULL,
			`method_id` int(11) NOT NULL,
			PRIMARY KEY (`bind_id`),
			UNIQUE KEY `customer_method` (`customer_id`,`method_id`),
			KEY `customer_id` (`customer_id`),
			KEY `method_id` (`method_id`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci");

		$this->load->model('setting/event');
		$this->model_setting_event->deleteEventByCode('customer_dynamic_payment');
	}

	public function uninstall() {
		$this->load->model('setting/event');
		$this->model_setting_event->deleteEventByCode('customer_dynamic_payment');

		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "customer_dynamic_payment_bind`");
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "customer_dynamic_payment_method`");
	}

	public function addMethod($data) {
		$this->db->query("INSERT INTO `" . DB_PREFIX . "customer_dynamic_payment_method` SET `name` = '" . $this->db->escape($data['name']) . "', `code` = '" . $this->db->escape($data['code']) . "', `api_id` = '" . $this->db->escape(isset($data['api_id']) ? $data['api_id'] : '') . "'");
	}

	public function editMethod($method_id, $data) {
		$this->db->query("UPDATE `" . DB_PREFIX . "customer_dynamic_payment_method` SET `name` = '" . $this->db->escape($data['name']) . "', `code` = '" . $this->db->escape($data['code']) . "', `api_id` = '" . $this->db->escape(isset($data['api_id']) ? $data['api_id'] : '') . "' WHERE `method_id` = '" . (int)$method_id . "'");
	}

	public function deleteMethod($method_id) {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "customer_dynamic_payment_bind` WHERE `method_id` = '" . (int)$method_id . "'");
		$this->db->query("DELETE FROM `" . DB_PREFIX . "customer_dynamic_payment_method` WHERE `method_id` = '" . (int)$method_id . "'");
	}

	public function getMethod($method_id) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "customer_dynamic_payment_method` WHERE `method_id` = '" . (int)$method_id . "'");
		return $query->row;
	}

	public function getMethods() {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "customer_dynamic_payment_method` ORDER BY `name` ASC");
		return $query->rows;
	}

	public function addBinding($customer_id, $method_id) {
		$this->db->query("INSERT IGNORE INTO `" . DB_PREFIX . "customer_dynamic_payment_bind` SET `customer_id` = '" . (int)$customer_id . "', `method_id` = '" . (int)$method_id . "'");
	}

	public function deleteBinding($bind_id) {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "customer_dynamic_payment_bind` WHERE `bind_id` = '" . (int)$bind_id . "'");
	}

	public function getBindings($data = array()) {
		$sql = "SELECT b.*, c.firstname, c.lastname, c.email, m.name AS method_name, m.code AS method_code, m.api_id FROM `" . DB_PREFIX . "customer_dynamic_payment_bind` b LEFT JOIN `" . DB_PREFIX . "customer` c ON (c.customer_id = b.customer_id) LEFT JOIN `" . DB_PREFIX . "customer_dynamic_payment_method` m ON (m.method_id = b.method_id) WHERE 1=1";

		if (!empty($data['filter_search'])) {
			$search = $this->db->escape($data['filter_search']);
			$sql .= " AND (c.email LIKE '%" . $search . "%' OR c.firstname LIKE '%" . $search . "%' OR c.lastname LIKE '%" . $search . "%' OR CONCAT(c.firstname, ' ', c.lastname) LIKE '%" . $search . "%' OR m.name LIKE '%" . $search . "%' OR m.code LIKE '%" . $search . "%' OR m.api_id LIKE '%" . $search . "%')";
		}

		$sql .= " ORDER BY b.bind_id DESC";

		if (isset($data['start']) || isset($data['limit'])) {
			$start = isset($data['start']) ? (int)$data['start'] : 0;
			$limit = isset($data['limit']) ? (int)$data['limit'] : 20;
			if ($limit < 1) {
				$limit = 20;
			}
			$sql .= " LIMIT " . $start . "," . $limit;
		}

		$query = $this->db->query($sql);
		return $query->rows;
	}

	public function getTotalBindings($data = array()) {
		$sql = "SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "customer_dynamic_payment_bind` b LEFT JOIN `" . DB_PREFIX . "customer` c ON (c.customer_id = b.customer_id) LEFT JOIN `" . DB_PREFIX . "customer_dynamic_payment_method` m ON (m.method_id = b.method_id) WHERE 1=1";

		if (!empty($data['filter_search'])) {
			$search = $this->db->escape($data['filter_search']);
			$sql .= " AND (c.email LIKE '%" . $search . "%' OR c.firstname LIKE '%" . $search . "%' OR c.lastname LIKE '%" . $search . "%' OR CONCAT(c.firstname, ' ', c.lastname) LIKE '%" . $search . "%' OR m.name LIKE '%" . $search . "%' OR m.code LIKE '%" . $search . "%' OR m.api_id LIKE '%" . $search . "%')";
		}

		$query = $this->db->query($sql);
		return (int)$query->row['total'];
	}
}
