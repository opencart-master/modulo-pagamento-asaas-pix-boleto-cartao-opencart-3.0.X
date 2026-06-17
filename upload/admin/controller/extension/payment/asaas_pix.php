<?php
class ControllerExtensionPaymentAsaasPix extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('extension/payment/asaas_pix');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		require_once(DIR_SYSTEM . 'library/asaas/asaas_api.php');
		$this->createDbCallback();
		$this->install();

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('payment_asaas_pix', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$asaas = new AsaasApi($this->config->get('payment_asaas_pix_api_key'));
			
			$sandbox = $asaas->checkSandbox($this->config->get('payment_asaas_pix_api_key'));

			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true));
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['key'])) {
			$data['error_key'] = $this->error['key'];
		} else {
			$data['error_key'] = '';
		}

		if (isset($this->error['doc'])) {
			$data['error_doc'] = $this->error['doc'];
		} else {
			$data['error_doc'] = '';
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/payment/asaas_pix', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['action'] = $this->url->link('extension/payment/asaas_pix', 'user_token=' . $this->session->data['user_token'], true);

		$data['webhook'] = $this->url->link('extension/payment/asaas_pix/webhook', 'user_token=' . $this->session->data['user_token'], true);

		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true);

		if (isset($this->request->post['payment_asaas_pix_api_key'])) {
			$data['payment_asaas_pix_api_key'] = $this->request->post['payment_asaas_pix_api_key'];
		} else {
			$data['payment_asaas_pix_api_key'] = $this->config->get('payment_asaas_pix_api_key');
		}

		if (!empty($this->config->get('payment_asaas_pix_api_key'))) {
            $data['show'] = true;
		} else {
		    $data['show'] = false;
		}

		if (isset($this->request->post['payment_asaas_pix_order_status_id'])) {
			$data['payment_asaas_pix_order_status_id'] = $this->request->post['payment_asaas_pix_order_status_id'];
		} else {
			$data['payment_asaas_pix_order_status_id'] = $this->config->get('payment_asaas_pix_order_status_id');
		}

		if (isset($this->request->post['payment_asaas_pix_order_status_id2'])) {
			$data['payment_asaas_pix_order_status_id2'] = $this->request->post['payment_asaas_pix_order_status_id2'];
		} else {
			$data['payment_asaas_pix_order_status_id2'] = $this->config->get('payment_asaas_pix_order_status_id2');
		}

		if (isset($this->request->post['payment_asaas_pix_order_status_id3'])) {
			$data['payment_asaas_pix_order_status_id3'] = $this->request->post['payment_asaas_pix_order_status_id3'];
		} else {
			$data['payment_asaas_pix_order_status_id3'] = $this->config->get('payment_asaas_pix_order_status_id3');
		}

		if (isset($this->request->post['payment_asaas_pix_order_status_id4'])) {
			$data['payment_asaas_pix_order_status_id4'] = $this->request->post['payment_asaas_pix_order_status_id4'];
		} else {
			$data['payment_asaas_pix_order_status_id4'] = $this->config->get('payment_asaas_pix_order_status_id4');
		}

		if (isset($this->request->post['payment_asaas_pix_order_status_id5'])) {
			$data['payment_asaas_pix_order_status_id5'] = $this->request->post['payment_asaas_pix_order_status_id5'];
		} else {
			$data['payment_asaas_pix_order_status_id5'] = $this->config->get('payment_asaas_pix_order_status_id5');
		}

		$this->load->model('localisation/order_status');

		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

		if (isset($this->request->post['payment_asaas_pix_status'])) {
			$data['payment_asaas_pix_status'] = $this->request->post['payment_asaas_pix_status'];
		} else {
			$data['payment_asaas_pix_status'] = $this->config->get('payment_asaas_pix_status');
		}

		if (isset($this->request->post['payment_asaas_pix_wb'])) {
			$data['payment_asaas_pix_wb'] = $this->request->post['payment_asaas_pix_wb'];
		} elseif(!empty($this->config->get('payment_asaas_pix_wb'))) {
			$data['payment_asaas_pix_wb'] = $this->config->get('payment_asaas_pix_wb');
		} else {
			$data['payment_asaas_pix_wb'] = token(32);
		}

		if (isset($this->request->post['payment_asaas_pix_sort_order'])) {
			$data['payment_asaas_pix_sort_order'] = $this->request->post['payment_asaas_pix_sort_order'];
		} else {
			$data['payment_asaas_pix_sort_order'] = $this->config->get('payment_asaas_pix_sort_order');
		}

		if (isset($this->request->post['payment_asaas_pix_doc'])) {
			$data['payment_asaas_pix_doc'] = $this->request->post['payment_asaas_pix_doc'];
		} else {
			$data['payment_asaas_pix_doc'] = $this->config->get('payment_asaas_pix_doc');
		}

		if (isset($this->request->post['payment_asaas_pix_doc1'])) {
			$data['payment_asaas_pix_doc1'] = $this->request->post['payment_asaas_pix_doc1'];
		} else {
			$data['payment_asaas_pix_doc1'] = $this->config->get('payment_asaas_pix_doc1');
		}

		if (isset($this->request->post['payment_asaas_pix_venc'])) {
			$data['payment_asaas_pix_venc'] = $this->request->post['payment_asaas_pix_venc'];
		} elseif (!empty($this->config->get('payment_asaas_pix_venc'))) {
		    $data['payment_asaas_pix_venc'] = $this->config->get('payment_asaas_pix_venc');
		} else {
			$data['payment_asaas_pix_venc'] = 1;
		}

		$this->load->model('customer/custom_field');
		
        $data['custom_fields'] = $this->model_customer_custom_field->getCustomFields();

		$data['success'] = isset($this->session->data['success']) ? $this->session->data['success'] : '';

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/payment/asaas_pix', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/payment/asaas_pix')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (empty($this->request->post['payment_asaas_pix_api_key'])) {
			$this->error['key'] = $this->language->get('error_key');
		}

		if (!isset($this->request->post['payment_asaas_pix_doc']) || $this->request->post['payment_asaas_pix_doc'] == 0 ) {
			$this->error['doc'] = $this->language->get('error_doc');
		}

		return !$this->error;
	}

	public function install() {
		require_once(DIR_SYSTEM . 'library/asaas/asaas_api.php');
        $asaas = new AsaasApi($this->config->get('payment_asaas_pix_api_key'));
	    $check = $asaas->check();
    }

    public function createDbCallback() {
        $this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "asaas_callback` (
        `order_id` int(11) NOT NULL AUTO_INCREMENT,
		`pay_id` varchar(255) NOT NULL,
		`type` varchar(30) NOT NULL,
        `date_create` datetime NOT NULL,
        PRIMARY KEY (`order_id`)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3; ");
    }

	public function webhook() {
		require_once(DIR_SYSTEM . 'library/asaas/asaas_api.php');
        $asaas = new AsaasApi($this->config->get('payment_asaas_pix_api_key'));
		$this->load->language('extension/payment/asaas_pix');

		$this->document->setTitle($this->language->get('heading_title'));

		$webhook = array(
		"events" => [
			"PAYMENT_AUTHORIZED",
    		"PAYMENT_CONFIRMED",
			"PAYMENT_APPROVED_BY_RISK_ANALYSIS",
			"PAYMENT_CHARGEBACK_DISPUTE",
			"PAYMENT_REFUNDED",
			"PAYMENT_AWAITING_RISK_ANALYSIS",
			"PAYMENT_REPROVED_BY_RISK_ANALYSIS",
			"PAYMENT_RECEIVED",
			"PAYMENT_OVERDUE",
    		"PAYMENT_CHARGEBACK_DISPUTE"
		],
		"name" => "opencart-webhook",
  		"url" =>  HTTPS_CATALOG. "index.php?route=extension/payment/asaas_callback",
 		"enabled" => true,
  		"apiVersion" => 3,
		"authToken" => $this->config->get('payment_asaas_pix_wb'),
		"sendType" => "SEQUENTIALLY",
		"interrupted" => false,
		"email" => $this->config->get('config_email')
		);

		$resposta = $asaas->createWebhooks($webhook);

		if(isset($resposta['errors'])) {
		$this->error['warning'] = $resposta['errors'][0]['description'];	
		} else {
		$this->session->data['success'] = "Webhook criado com sucesso!";
		}

		$this->index();
	}

}