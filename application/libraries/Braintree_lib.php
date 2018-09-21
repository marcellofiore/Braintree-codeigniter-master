<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH.'third_party/Braintree/vendor/autoload.php'; // braintree lib 3.35.0 install via Composer

/*
 *  Braintree_lib by Marcello Fiore
 *	Braintree PHP SDK v3.35.0
 *  Codeigniter 3.1.9
 */

class Braintree_lib{

	protected $gateway;

		function __construct() {
			$CI = &get_instance();
			$CI->config->load('braintree', TRUE);
			$braintree = $CI->config->item('braintree');
			// configutation Lib => application/config/braintree => SET YOUR DATA
			$config = new Braintree_Configuration([
				'environment' => $braintree['braintree_environment'],
				'merchantId' => $braintree['braintree_merchant_id'],
				'publicKey' => $braintree['braintree_public_key'],
				'privateKey' => $braintree['braintree_private_key']
			]);
			$this->gateway = new Braintree\Gateway($config);

		}

    function create_client_token(){
		//$clientToken = Braintree_ClientToken::generate();
		$clientToken = $this->gateway->clientToken()->generate();
    	return $clientToken;
	}

	function createCustomer() {
		// params for customer
		$result = $this->gateway->customer()->create([
			'firstName' => 'Mike',
			'lastName' => 'Jones',
			'company' => 'Jones Co.',
			'email' => 'mike.jones@example.com',
			'phone' => '281.330.8004',
			'fax' => '419.555.1235',
			'website' => 'http://example.com'
		]);
		
		if($result->success) {
			echo "Success, new customer added: ". $result->customer->id; // return customer ID
			echo "<br>";
			echo "DATA CUSTOMER: " . $result->customer;
		}else {
			echo "Errore creation Customer";
		}
		
	}

	function createCustomerWithPaymentMethode($dataNonceFromClient) {
		// params for customer
		$result = $this->gateway->customer()->create([
			'firstName' => 'Mike',
			'lastName' => 'Jones',
			'company' => 'Jones Co.',
			'paymentMethodNonce' => $dataNonceFromClient
		]);

		if ($result->success) {
			echo($result->customer->id . "<br>");
			echo($result->customer->paymentMethods[0]->token);
		} else {
			foreach($result->errors->deepAll() AS $error) {
				echo($error->code . ": " . $error->message . "\n");
			}
		}
	}
	// MORE FUNCTION CUSTOMER => https://developers.braintreepayments.com/guides/customers/php
	function updateCustomer($customerID) {
		// params for Update Customer
		$updateResult = $gateway->customer()->update(
			$customerID,
			[
			  'firstName' => 'New First',
			  'lastName' => 'New Last',
			  'company' => 'New Company',
			  'email' => 'new.email@example.com',
			  'phone' => 'new phone',
			  'fax' => 'new fax',
			  'website' => 'http://new.example.com'
			]
		);
		
		if($updateResult->success) {
			echo "UPDATED CUSTOMER SUCCESS";
		}
		
	}

	function createTransaction($dataNonceFromClient) {
		// Then, create a transaction
		$result = $this->gateway->transaction()->sale([
			'amount' => '10.00',
			'paymentMethodNonce' => $dataNonceFromClient,
			'options' => [ 'submitForSettlement' => true ]
		]);
		return $result;
	}
	
}
