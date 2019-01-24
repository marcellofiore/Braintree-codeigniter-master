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
		$CI->config->load('braintree', TRUE); // load configuration from file
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
	// CREATE Customer, more info on Doc....
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

	// Crea transazione ed esegui quindi il pagamento
	function createTransaction($nonceFromTheClient) {
		// Then, create a transaction
		$result = $this->gateway->transaction()->sale([
			'amount' => '10.00',
			'paymentMethodNonce' => $nonceFromTheClient,
			'options' => [ 'submitForSettlement' => true, 'verifyCard' => true ] // verifica la carta di credito in caso (questa opzione va testata)
		]);
		return $result;

		/*
		$result->success;
		// false

		$verification = $result->creditCardVerification;
		$verification->status;
		// "processor_declined"

		$verification->processorResponseType;
		// "soft_declined"

		$verification->processorResponseCode;
		// "2000"

		$verification->processorResponseText;
		// "Do Not Honor"
		*/
	}

	// 1 - create Customer with PaymentData
	function createCustomerWithPaymentMethode($nonceFromTheClient, $userData) {
		$result = $this->gateway->customer()->create([
			'firstName' => $userData['firstName'] ?? '',
			'lastName' => $userData['lastName'] ?? '',
			'company' => $userData['company'] ?? '',
			'email' => $userData['email'] ?? '',
			'phone' => $userData['phone'] ?? '',
			'fax' => $userData['fax'] ?? '',
			'website' => $userData['website'] ?? '',
			'paymentMethodNonce' => $nonceFromTheClient
			//'options' => [ 'verifyCard' => true ]
		]);

		if ($result->success) {
			return $result;
		} else {
			foreach($result->errors->deepAll() AS $error) {
				echo($error->code . ": " . $error->message . "\n");
			}
			return null;
		}
	}

	// 2 - DOPO AVER OTTENUTO IL TOKEN CREA LA SUBSCRIPTION
	function createSubscription($tokenPaymentCustomerMethode, $idProduct) {
		$result = $this->gateway->subscription()->create([
			'paymentMethodToken' => $tokenPaymentCustomerMethode,
			'planId' => $idProduct // ID Plan Subscription creato nel pannello di Braintree
		]);
		return $result;
	}
	// 2 - Dopo Aver ottunto token per creazione subscription...! NO TRIAL
	function createSubscriptionNoTrial($tokenPaymentCustomerMethode, $idProduct) {
		$result = $this->gateway->subscription()->create([
			'paymentMethodToken' => $tokenPaymentCustomerMethode,
			'planId' => $idProduct, // ID Plan Subscription creato nel pannello di Braintree
			'options' => ['startImmediately' => true]
		]);
		return $result;
	}

	// 3 - Cancel Subscription
	function cancelSubscriptionWithId($idSubscription) {
		$result = $this->gateway->subscription()->cancel($idSubscription);
		return $result;
	}

	/*
	 * WEBHOOCK SECTION => LEGGI LE NOTIFICHE INVIATE DAL SISTEMA
	 */
	// Optionale - Decoding WebHock
	public function webHoockDecoding($bt_signature, $bt_payload) {
		$webhookNotification = $this->gateway->webhookNotification()->parse(
			$bt_signature, $bt_payload
		);
		return $webhookNotification;
	}
	// Opzionale - Genera finta WebHock notification
	public function generaWebHockNotification() {

		// => tutti i tipi di notifiche esistenti che si possono testare => https://developers.braintreepayments.com/guides/webhooks/testing-go-live/php
		$sampleNotification = $this->gateway->webhookTesting()->sampleNotification(
			//Braintree_WebhookNotification::SUBSCRIPTION_CANCELED,
			//Braintree_WebhookNotification::SUBSCRIPTION_CHARGED_SUCCESSFULLY,
			//Braintree_WebhookNotification::SUBSCRIPTION_CHARGED_UNSUCCESSFULLY,
			//Braintree_WebhookNotification::SUBSCRIPTION_EXPIRED,
			//Braintree_WebhookNotification::SUBSCRIPTION_TRIAL_ENDED,
			//Braintree_WebhookNotification::SUBSCRIPTION_WENT_ACTIVE,
			Braintree_WebhookNotification::SUBSCRIPTION_WENT_PAST_DUE,
            'my_id' // id subscription demo
        );
		
		$webhookNotification = $this->webHoockDecoding($sampleNotification['bt_signature'], $sampleNotification['bt_payload']);

		print_r($webhookNotification);
		echo "<br><br>";

		print_r($webhookNotification->subscription->id);
		
        # => "my_id"
	}
	/*
	 * FINE WEBHOOK SECTION
	 */
	
}
