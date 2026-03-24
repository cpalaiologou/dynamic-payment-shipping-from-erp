<?php
class ControllerExtensionPaymentCustomerDynamicPayment extends Controller {
	public function index() {
		$this->response->redirect($this->url->link('checkout/cart'));
	}
}
