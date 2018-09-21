<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<html>
    <head>
        <title>TEST BRAINTREE</title>
        <meta charset="utf-8">
        <script src="https://js.braintreegateway.com/web/dropin/1.13.0/js/dropin.min.js"></script>
    </head>
    <body>
        <div id="dropin-container"></div>
        <button id="submit-button">Request payment method</button>

        <form action="<?php echo site_url('test/createTransaction') ?>" method="post" id="pay">
            <input type="hidden" name="token" value="" id="token_braintree">
        </form>

        <script>
            var button = document.querySelector('#submit-button');

            braintree.dropin.create({
                authorization: '<?php echo $token ?>',
                container: '#dropin-container' // conteiner data Payment
            }, function (createErr, instance) {
                button.addEventListener('click', function () {
                    instance.requestPaymentMethod(function (err, payload) {
                        // Submit payload.nonce to your server
                        console.log(payload.nonce);
                        console.log("ERRRE: " + err);
                        document.querySelector('#token_braintree').value = payload.nonce;
                        document.getElementById("pay").submit();
                    });
                });
            });
        </script>
    </body>
</html>