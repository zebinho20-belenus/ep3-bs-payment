# ep-3 Bookingsystem with direct payment via payum  
fork of 1.6.4 from tkrebs/ep3-bs

modified with payum / payumModule (https://github.com/Payum/PayumModule) for direct payment via paypal and stripe (credit cards, apple pay, google pay, SEPA direct debit) 

runnnig with PHP 7+ 

vendor path completely from our instance (tennis-rudolstadt.de) with extended payumModule and payumStripe

css, images (logo) from our instance (to be changed if somebody want's to use this version of ep3-bs)

in addition to the original config there is a projetc.php in config/autoload and the extended local.php for the payment provider options 

in addition to the original project there is a manifest.json, js/sw.js and modified layout.phtml for pwa abbility and the hammer.js for swiping left/right in the calendar



# Payment

## paypal
create an account at paypal.com - first sandbox for developing later live - get the NVP/SOAP credentials (username,password,signature) and put them in your config/autoload/local.php

## stripe
create an account at stripe.com - get the API keys (publishable and secret key) - first test later live - and put them in your config/autoload/local.php

## apple pay via stripe
verify your domain for apple pay

https://stripe.com/docs/stripe-js/elements/payment-request-button#verifying-your-domain-with-apple-pay

## removing unpaid booking try's
cancelling bookings is not allowed in our version - so we remove unpaid user online bookings automatically if they are not completed during the payment process - we remove that bookings after 3 hours (the standard lifetime of a paypal token) in the db with following sql
```
DROP EVENT remove_unpaid_bookings;
SET GLOBAL event_scheduler = ON;
CREATE EVENT remove_unpaid_bookings ON SCHEDULE EVERY 10 MINUTE STARTS '2019-11-14 00:00:00' ON COMPLETION PRESERVE DO delete from bs_bookings where `status` = 'single' and `status_billing` = 'pending' and created < (NOW() - INTERVAL 3 HOUR) and bid in (select bid from bs_bookings_meta where `key` = 'directpay' and `value` = 'true');
```
if a user is actively cancelling the payment via paypal or the stripe checkout - the booking is automatically cancelled too 

## stripe payment site
can be changed via the twig templates of payumStripe - for other language support than German and English you have to extend these templates too

vendor/payum/stripe/Payum/Stripe/Resources/views/Action/stripe_js.html.twig
