<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Braintree_hooks extends CI_Controller {

    function __construct(){
        parent::__construct();
        // load librarry Braintree
        $this->load->library("braintree_lib");
    }

    public function index() {
        show_404();
    }

    public function return_webhoock() {
        $data = $this->input->post();
        if($data) {
            if (isset($_POST["bt_signature"]) && isset($_POST["bt_payload"])) {
                $message = $this->braintree_lib->webHoockDecoding($_POST["bt_signature"], $_POST["bt_payload"]);
                // Example values for webhook notification properties
                $type_notification = $message->kind; // "subscription_went_past_due" => "Tipo della Notifica"
                $message_time = $message->timestamp->format('Y-m-d H:i:s'); //format('D M j G:i:s T Y'); // da trasformare in datetime e poi in data come si preferisce
                
                // print_r($message);
                // $message_subject = $message->subject; // per accedere agli elementi presenti nella notifica

                // SUBSCRIPTION CASE
                switch ($type_notification) {
                    case 'subscription_canceled':
                        // NOTIFCATION CANCELL SUBSCRIPTION
                        $this->sendEmail($message, "TEST WebHoock Message Decode _CANCEL");

                        break;
                    case 'subscription_charged_successfully':
                        // Notification Pagamento Subscription Complete
                        $this->sendEmail($message, "TEST WebHoock Message Decode _CHARGE SUCCESS");

                        break;
                    case 'subscription_charged_unsuccessfully':
                        // pagamento Non andato a buon fine
                        $this->sendEmail($message, "TEST WebHoock Message Decode _CHARGE UNSUCCESS");

                        break;
                    case 'subscription_expired':
                        // Abbonamento Scaduto
                        $this->sendEmail($message, "TEST WebHoock Message Decode _SUBSCRIPTION EXPIRED");

                        break;
                    case 'subscription_trial_ended':
                        // Periodo trial Abonamento Completato
                        $this->sendEmail($message, "TEST WebHoock Message Decode _SUBSCRIPTION TRIAL ENDE");

                        break;
                    case 'subscription_went_active':
                        // A subscription's first authorized transaction is created, or a successful transaction moves a subscription from the Past Due status to the Active status.
                        // Subscriptions with trial periods will not trigger this notification when they move from the trial period into the first billing cycle.
                        $this->sendEmail($message, "TEST WebHoock Message Decode subscription_went_active");
                        break;
                    case 'subscription_went_past_due':
                        // A subscription has moved from the Active status to the Past Due status.
                        // This will only be triggered when the initial transaction in a billing cycle is declined.
                        // Once the status moves to past due, it will not be triggered again in that billing cycle.
                        $this->sendEmail($message, "TEST WebHoock Message Decode subscription_went_past_due");
                        break;
                    
                    default:
                        // caso non controllato
                        $this->sendEmail($message, "TEST WebHoock Message Decode _CASO NON CONTROLLATO");
                        break;
                }
                
                header("HTTP/1.1 200 OK");
                // send mail message Decode
                //$this->sendEmail($message, "TEST WebHoock Message Decode");

                //$this->sendEmail($message_kind, "TEST WebHoock Kind");
                //$this->sendEmail($message_time, "TEST WebHoock Time");

                // $this->sendEmail($message_subject, "TEST WebHoock Subject");

                // Altra docuemntazione dul funzionamento => https://developers.braintreepayments.com/guides/reports/webhooks/php
            }
        }
    }

    // HELPER => EMAIL SEND to Dev
    private function sendEmail($data_ipn, $oggetto) {
        // TEST EMAIL RICEZIONE DATI PAGAMENTO PAYPAL
        $this->load->library('email');
        //CONFIG EMAIL
        $config['protocol'] = 'mail';
        $config['smtp_host'] = 'smtp.1und1.de';
        $config['smtp_port'] = '465';
        $config['mailtype'] = 'html';
        $config['mailpath'] = '/usr/sbin/sendmail';
        $config['charset'] = 'utf-8';
        $config['wordwrap'] = TRUE;
        $this->email->initialize($config);

        // SEND EMAIL WITH POST DATA
        $this->email->from('abosupport@tuunes.co', 'WebHoock BrainTree Tuunes.co');
        $this->email->to('dev@whitepointprojects.com');
        //$oggetto = 'Subscription Paypal';
        $this->email->subject($oggetto);
        $html = ('
        <div style:"font-size: 16px">
        <p>NOTIFICA BRAINTREE<br><br><br>'.var_export($data_ipn, true).'</p>
        <p><br></p>
        <p><br></p>
        <p>JSON ENCODE <br><br></p>
        <p>'.json_encode($data_ipn).'</p>
        </div>
        ');
        $this->email->message($html);
        $this->email->send(); // SEND EMAIL
    }

}