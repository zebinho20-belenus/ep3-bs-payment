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
        'database' => 'u216054559_prod',
        'username' => 'u216054559_prod',
        'password' => 'Profitbadminton123*',

        'hostname' => 'localhost',
        'port' => 3306,
    ),
    'mail' => array(
        'type' => 'sendmail', // or 'smtp' or 'smtp-tls'
        'address' => 'info@initiumtech.com.au',
            // Make sure 'bookings.example.com' matches the hosting domain when using type 'sendmail'

        'host' => '?', // for 'smtp' type only, otherwise remove or leave as is
        'user' => '?', // for 'smtp' type only, otherwise remove or leave as is
        'pw' => '?', // for 'smtp' type only, otherwise remove or leave as is

        'port' => 'auto', // for 'smtp' type only, otherwise remove or leave as is
        'auth' => 'plain', // for 'smtp' type only, change this to 'login' if you have problems with SMTP authentication
    ),
    'i18n' => array(
        'choice' => array(
            'en-US' => 'English',
            'de-DE' => 'Deutsch'

            // More possible languages:
            // 'fr-FR' => 'Français',
            // 'hu-HU' => 'Magyar',
        ),

        'currency' => 'AUD',

        // The language is usually detected from the user's web browser.
        // If it cannot be detected automatically and there is no cookie from a manual language selection,
        // the following locale will be used as the default "fallback":
        'locale' => 'en-US',
    ),
    'payum' => array(
        'token_storage' => new \Payum\Core\Storage\FilesystemStorage(
            __DIR__.'/../../data/payum',
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
                'publishable_key' => 'pk_live_51NyX5zKqJ6Dp5KQyn3M9CifMkxICL9TvGVGjlevtEOs46v71XSxjclr0S1wZxMOIlzWYjlQvWuTan8nr5eA8epVX00cXkBfnGo',
                'secret_key' => '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',
                'sca_flow' => true,
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
            $detailsClass => new \Payum\Core\Storage\FilesystemStorage(__DIR__.'/../../data/payum', $detailsClass, 'id'),
        )
    ),
));
