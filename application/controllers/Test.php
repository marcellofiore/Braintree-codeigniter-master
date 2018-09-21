<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Test extends CI_Controller
{
    function __construct() {
        parent::__construct();

        $this->load->helper('url');
        // load librarry Braintree
        $this->load->library("braintree_lib");
    }

    public function index() {
        $data['token'] = $this->get_token(); // get client Token
        $this->load->view('pay', $data); // token to viewController
    }

    public function createTransaction() {
        // get token from client (ViewController)
        $data = $this->input->post();
        echo "<br>";
        $result = $this->braintree_lib->createTransaction($data['token']); // create transaction payment with token
        // result transaction
        if ($result->success) {
            print_r("success!: TRANSATION ID: " . $result->transaction->id);
            echo "<br>";
            print("TRANSACTION INFOS: ");
            print_r($result->transaction);
            
		} else if ($result->transaction) {
			print_r("Error processing transaction:");
			print_r("\n  code: " . $result->transaction->processorResponseCode);
			print_r("\n  text: " . $result->transaction->processorResponseText);
		} else {
			print_r("Validation errors: \n");
			print_r($result->errors->deepAll());
		}

    }

    /* HELPER FUNCTIONS */
    private function get_token() {
        $token = $this->braintree_lib->create_client_token();
        return $token;
    }

}
?>
