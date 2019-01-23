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
        $data['token'] = $this->get_token(); // getToken from Server and send to client!
        $this->load->view('pay', $data); // token to viewController
    }

    // PAGAMENTO NORMALE - CheckOut
    public function createTransaction() {
        // get token from client (ViewController)
        $data = $this->input->post();
        echo "<br>";
        $result = $this->braintree_lib->createTransaction($data['token']); // create transaction payment with token
        // result transaction
        if ($result->success) {
            print_r($result);
            echo "<br><br>";
            print_r("success!: TRANSATION ID: " . $result->transaction->id);
            echo "<br><br>";
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

    // 1 - Crea Subscription - Prodotto ID con FreeTrial TRIAL
    public function createSubscription() {
        $data = $this->input->post();
        //print_r($data['token']);
        if($data['token']) {
            // 1 Step - Create Customer + Payment Methode
            $userData = array(
                'firstName' => 'Marcello',
                'lastName' => 'Fiore',
                'email' => 'mf@lauschmedia.de',
                'phone' => '',
            );
            $result = $this->braintree_lib->createCustomerWithPaymentMethode($data['token'], $userData);
            /*
            echo "CUSTOMER AND PAYMENT Creation response:<br>";
            print_r($result);
            echo "<br><br><br>";
            */

            if($result && $result->success) {
                //print_r($result);
                $customerId = $result->customer->id;
                $paymentMethodeCustomerToken = $result->customer->paymentMethods[0]->token;
                /*
                echo($customerId);
                echo "<br>";
                echo($paymentMethodeCustomerToken);
                echo "<br><br><br><br>";
                */

                // IMPOSTA SUBSCRIPTION
                // 2 Step - crea Subscription with Payment Methode + Customer
                $sub_result = $this->braintree_lib->createSubscription($paymentMethodeCustomerToken, 'snng');
                if($sub_result && $sub_result->success) {
                    // print_r($sub_result);
                    echo "<br><br>JSON RESPONSE SUBSCRIPTION TRIAL<br><br>";
                    /*
                    echo json_encode($sub_result);
                    echo "<br><br>";
                    */

                    $data_subscription = $sub_result->subscription;
                    $arr["date_subscription_created"] = $this->convertDate( $data_subscription->createdAt->format('Y-m-d H:i:s') ); //format('D M j G:i:s T Y'); // transforma direttametne la data nel formato desiderato
                    $arr["subscription_id"] = $data_subscription->id;
                    $arr["next_billing_date_subscription"] = $this->convertDate( $data_subscription->nextBillingDate->format('Y-m-d H:i:s') ); // Data formattata Nel formato desiderato
                    $arr["plan_id_subscription"] = $data_subscription->planId;
                    $arr["price_subscription"] = $data_subscription->price;
                    $arr["status_subscription"] = $data_subscription->status;
                    $arr["trial_period"] = $data_subscription->trialPeriod;

                    // DATI TRANSAZIONI
                    $dd = array();
                    if($data_subscription->transactions) {
                        $transation_payment = $data_subscription->transactions[0]; // se esiste allora verifica lo stato (conta quanti elementi esistono,ci spossono essere piu transazioni)
                        $dd["stato_transation"] = $transation_payment->status ?? "no_status"; // Se lo stato è submitted_for_settlement allora il pagamento è stato eseguito con successo
                        $dd["currencyCode"] = $transation_payment->currencyIsoCode;
                        $dd["amount_transation"] = $transation_payment->amount;
                    }
                    

                    // stampa array con data subscription
                    echo "<p>DATA ABO</p>"; // $key => $value
                    foreach ($arr as $key => $value) {
                        echo( "Key: ".$key." Value: ".$value);
                        echo "<br>";
                    }

                    echo "<p>DATA TRANSAZIONI:</p>";
                    foreach ($dd as $key => $value) {
                        echo( "Key: ".$key." Value: ".$value);
                        echo "<br>";
                    }

                }
                
            }
        }
        
    }

    public function createSubscriptionNoTrial() {
        $data = $this->input->post();
        //print_r($data['token']);
        if($data['token']) {

            $userData = array(
                'firstName' => 'Marcello',
                'lastName' => 'Fiore',
                'email' => 'mf@lauschmedia.de',
                'phone' => '',
            );
            $result = $this->braintree_lib->createCustomerWithPaymentMethode($data['token'], $userData);
            /*
            echo "Create Customer with PaymentMethode RESULT:<br>";
            print_r($result);
            echo "<br><br><br><br>";
            */
            if($result && $result->success) {
                //print_r($result);
                $customerId = $result->customer->id;
                $paymentMethodeCustomerToken = $result->customer->paymentMethods[0]->token;
    
                //echo("ID Customer creato tramite API: ", $customerId);
                //echo("Token customer with PaymentMethode: ", $paymentMethodeCustomerToken);
                echo "<br><br><br>result Create Subscription NO TRIAL:<br><br>";
    
                // IMPOSTA SUBSCRIPTION
                $sub_result = $this->braintree_lib->createSubscriptionNoTrial($paymentMethodeCustomerToken, 'tuunes_subscription_year_noTrial');
                if($sub_result && $sub_result->success) {
                    
                    //print_r($sub_result);
                    // echo "<br><br>JSON RESPONSE SUBSCRIPTION<br><br>";
                    // echo json_encode($sub_result);
    
    
                    $data_subscription = $sub_result->subscription;
                    $arr["date_subscription_created"] = $this->convertDate( $data_subscription->createdAt->format('Y-m-d H:i:s') ); //format('D M j G:i:s T Y'); // transforma direttametne la data nel formato desiderato
                    $arr["subscription_id"] = $data_subscription->id;
                    $arr["next_billing_date_subscription"] = $this->convertDate( $data_subscription->nextBillingDate->format('Y-m-d H:i:s') ); // Data formattata Nel formato desiderato
                    $arr["plan_id_subscription"] = $data_subscription->planId;
                    $arr["price_subscription"] = $data_subscription->price;
                    $arr["status_subscription"] = $data_subscription->status;
                    $arr["trial_period"] = $data_subscription->trialPeriod;
    
                    // DATI TRANSAZIONI
                    $dd = array();
                    if($data_subscription->transactions) {
                        $transation_payment = $data_subscription->transactions[0]; // se esiste allora verifica lo stato (conta quanti elementi esistono,ci spossono essere piu transazioni)
                        $dd["stato_transation"] = $transation_payment->status ?? "no_status"; // Se lo stato è submitted_for_settlement allora il pagamento è stato eseguito con successo
                        $dd["currencyCode"] = $transation_payment->currencyIsoCode;
                        $dd["amount_transation"] = $transation_payment->amount;
                    }
    
                    // stampa array con data subscription
                    echo "<p>DATA ABO</p>"; // $key => $value
                    foreach ($arr as $key => $value) {
                        echo( "Key: ".$key." Value: ".$value);
                        echo "<br>";
                    }
    
                    echo "<p>DATA TRANSAZIONI:</p>";
                    foreach ($dd as $key => $value) {
                        echo( "Key: ".$key." Value: ".$value);
                        echo "<br>";
                    }
    
    
                }
                
            }
        }
        
    }

    // Cancella la subscription
    public function cancel() {
        $idSubscription = "kh3fdm"; // ID DELLA SUBSCRIPTION DA CANCELLARE
        $cancellata = $this->braintree_lib->cancelSubscriptionWithId($idSubscription);
        echo json_encode($cancellata); // risultato della cancellazione della subscription
    }

    /* HELPER FUNCTIONS - Genera Token Lato server e passalo al client */
    private function get_token() {
        $token = $this->braintree_lib->create_client_token();
        return $token;
    }

    /***************/
    /*** WEBHOOK ***/
    public function decode_webhoock() {

        if (isset($_POST["bt_signature"]) && isset($_POST["bt_payload"])) {
            $message = $this->braintree_lib->webHoockDecoding($_POST["bt_signature"], $_POST["bt_payload"]);
            // Example values for webhook notification properties
            $type_notification = $message->kind; // "subscription_went_past_due"
            //$message = $message->timestamp->format('D M j G:i:s T Y'); // "Sun Jan 1 00:00:00 UTC 2012"
            print_r($message);

            switch ($type_notification) {
                case 'subscription_canceled':
                    // NOTIFCATION CANCELL SUBSCRIPTION
                    break;
                case 'subscription_charged_successfully':
                    // Notification Pagamento Subscription Complete
                    break;
                case 'subscription_charged_unsuccessfully':
                    // pagamento Non andato a buon fine
                    break;
                case 'subscription_expired':
                    // Abbonamento Scaduto
                    break;
                case 'subscription_trial_ended':
                    // Periodo trial Abonamento Completato
                    break;
                case 'subscription_went_active':
                    // A subscription's first authorized transaction is created, or a successful transaction moves a subscription from the Past Due status to the Active status.
                    // Subscriptions with trial periods will not trigger this notification when they move from the trial period into the first billing cycle.
                    break;
                case 'subscription_went_past_due':
                    // A subscription has moved from the Active status to the Past Due status.
                    // This will only be triggered when the initial transaction in a billing cycle is declined.
                    // Once the status moves to past due, it will not be triggered again in that billing cycle.
                    break;
                
                default:
                    // caso non controllato
                    break;
            }

            header("HTTP/1.1 200 OK");
		}

    }
    public function executeTestWebSock() {
        // execute test WebHock
        $this->braintree_lib->generaWebHockNotification();
    }
    /* FINE WEBHOOK */
    /****************/

    /* HELPER */
    // converti la data ricevuta da paypal in data formato Server corrente
    private function convertDate($data) {
        // CONVERTI FORMATO DATA
        $data_ricevuta = strtotime($data); // convert string to timestamp
        $data_convertita = date("Y-m-d H:i:s", $data_ricevuta); // convert timestamp to Server Format
        return $data_convertita;
    }
    // converti la data in dateShop
    private function createDateShop($data) {
        $data_ricevuta = strtotime($data); // convert string to timestamp
        $data_convertita = date("d/m/Y", $data_ricevuta); // convert timestamp to Server Format 31/08/2018
        return $data_convertita;
    }
}
?>
