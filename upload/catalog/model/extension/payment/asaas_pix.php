<?php
class ModelExtensionPaymentAsaasPix extends Model {
	public function getMethod($address, $total) {
		$this->load->language('extension/payment/asaas_pix');


		$status = $this->config->get('payment_asaas_pix_status');

		$method_data = array();

		if ($status) {
			$method_data = array(
				'code'       => 'asaas_pix',
				'title'      => $this->language->get('heading_title'),
				'terms'      => '',
				'sort_order' => $this->config->get('payment_asaas_pix_sort_order')
			);
		}

		return $method_data;
	}
}