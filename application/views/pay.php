<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<html>
    <head>
        <title>TEST BRAINTREE</title>
        <meta charset="utf-8">
        <!-- <script src="https://js.braintreegateway.com/web/dropin/1.13.0/js/dropin.min.js"></script> -->
        <script src="https://js.braintreegateway.com/web/dropin/1.14.1/js/dropin.min.js"></script>
        <style>
            button.pay-button {
                padding: 10px 10px;
                box-sizing: border-box;
                background-color: green;
                color: white;
            }
        </style>
    </head>
    <body>
        <div id="dropin-container"></div>
        <button id="submit-button" locale="<?php echo trim(Locale::acceptFromHttp($_SERVER['HTTP_ACCEPT_LANGUAGE'])) ?>">Request payment method</button>

        <!-- <?php echo site_url('test/createTransaction') ?> --> <!-- createSubscription --> <!-- createSubscriptionNoTrial -->
        <form action="<?php echo site_url('test/createSubscriptionNoTrial') ?>" method="post" id="pay">
            <input type="hidden" name="token" value="" id="token_braintree">
        </form>

        <script type="text/javascript">
            var button = document.querySelector('#submit-button');
            var localeString = button.getAttribute('locale');
            var node;

            braintree.dropin.create({
                authorization: '<?php echo $token ?>',
                container: '#dropin-container', // conteiner data Payment from Bryantree
                locale: localeString, // set locale in modo automatico
                paypal: {
                    flow: 'vault'
                }
            }, function (createErr, instance) {

                button.addEventListener('click', function () {

                    instance.requestPaymentMethod(function (err, payload) {
                        // Submit payload.nonce to your server
                        console.log("TOKEN Payment: " + payload.nonce);
                        console.log("ERRRE: " + err);
                        document.querySelector('#token_braintree').value = payload.nonce;
                        button.parentNode.removeChild(button);
                        //document.getElementById("pay").submit(); // send payment Request

                        // create New Element
                        node = document.createElement("button");
                        node.className = "pay-button";
                        var textnode = document.createTextNode("PAY NOW");
                        node.appendChild(textnode);
                        document.getElementById("dropin-container").appendChild(node); 

                        node.addEventListener('click', function () {
                            // Send payment request
                            document.getElementById("pay").submit();
                        })

                    });

                });
            });
        </script>
    </body>
</html>