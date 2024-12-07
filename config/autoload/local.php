<?php
/**
 * Local application configuration
 *
 * Insert your local database credentials here
 * and provide the email address the system should use.
 */

$detailsClass = 'Application\Model\PaymentDetails';
$project_config = require 'config/autoload/project.php';

return array_merge($project_config, array(
    'db' => array(
        'database' => 'mycourt',
        'username' => 'root',
        'password' => 'root',

        'hostname' => '172.17.0.2',
        'port' => '3306'
    ),
    'mail' => array(
        'type' => 'smtp', // or 'smtp' or 'smtp-tls'
        'address' => 'platzbuchung@tcn-kail.de',
        // Make sure 'bookings.example.com' matches the hosting domain when using type 'sendmail'

        'host' => '127.0.0.1', // for 'smtp' type only, otherwise remove or leave as is
        'user' => '?', // for 'smtp' type only, otherwise remove or leave as is
        'pw' => '?', // for 'smtp' type only, otherwise remove or leave as is

        'port' => '1025', // for 'smtp' type only, otherwise remove or leave as is
        'auth' => 'plain' // for 'smtp' type only, change this to 'login' if you have problems with SMTP authentication
    ),
    'i18n' => array(
        'choice' => array(
            'en-US' => 'English',
            'de-DE' => 'Deutsch'

            // More possible languages:
            // 'fr-FR' => 'Français',
            // 'hu-HU' => 'Magyar',
        ),

        'currency' => 'EUR',

        // The language is usually detected from the user's web browser.
        // If it cannot be detected automatically and there is no cookie from a manual language selection,
        // the following locale will be used as the default "fallback":
        'locale' => 'de-DE',
    ),
    'payum' => array(
        'token_storage' => new \Payum\Core\Storage\FilesystemStorage(
            __DIR__ . '/../../data/payum',
            'Application\Model\PaymentSecurityToken',
            'hash'
        ),
        'gateways' => array(
            'paypal_ec' => (new \Payum\Paypal\ExpressCheckout\Nvp\PaypalExpressCheckoutGatewayFactory())->create(array(
                'username' => '?',
                'password' => '?',
                'signature' => '?',
                'sandbox' => false
            )),
            'stripe' => (new \Payum\Stripe\StripeCheckoutGatewayFactory())->create(array(
                'publishable_key' => '?',
                'secret_key' => '?',
                'sca_flow' => false,
                'payum.template.obtain_token' => '@PayumStripe/Action/stripe_js.html.twig',
                'payum.template.require_confirmation' => '@PayumStripe/Action/stripe_confirm.html.twig'
            )),
            'klarna_checkout' => (new \Payum\Klarna\Checkout\KlarnaCheckoutGatewayFactory())->create(array(
                'secret' => '?',
                'merchant_id' => '?',
                'sandbox' => false
            )),
        ),
        'storages' => array(
            $detailsClass => new \Payum\Core\Storage\FilesystemStorage(__DIR__ . '/../../data/payum', $detailsClass, 'id'),
        )
    ),
));
