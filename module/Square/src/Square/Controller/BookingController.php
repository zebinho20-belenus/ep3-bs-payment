<?php

namespace Square\Controller;

use Booking\Entity\Booking\Bill;
use RuntimeException;
use Zend\Json\Json;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\ServiceManager\ServiceLocator;
use Zend\ServiceManager\ServiceManagerAwareInterface;
use Zend\View\Model\JsonModel;
use Zend\Http\Response;
use Payum\Core\Request\GetHumanStatus;
use Payum\Core\Reply\HttpResponse;
use Payum\Core\Reply\HttpRedirect;
use Payum\Core\Reply\ReplyInterface;
use Payum\Stripe\Request\Confirm;
use Stripe;
use GuzzleHttp\Client; 


class BookingController extends AbstractActionController
{

    public function customizationAction()
    {
        $dateStartParam = $this->params()->fromQuery('ds');
        $dateEndParam = $this->params()->fromQuery('de');
        $timeStartParam = $this->params()->fromQuery('ts');
        $timeEndParam = $this->params()->fromQuery('te');
        $squareParam = $this->params()->fromQuery('s');

        $serviceManager = $this->getServiceLocator();
        $squareValidator = $serviceManager->get('Square\Service\SquareValidator');

        $byproducts = $squareValidator->isBookable($dateStartParam, $dateEndParam, $timeStartParam, $timeEndParam, $squareParam);

        $user = $byproducts['user'];

        if (! $user) {
            $query = $this->getRequest()->getUri()->getQueryAsArray();
            $query['ajax'] = 'false';

            $this->redirectBack()->setOrigin('square/booking/customization', [], ['query' => $query]);

            return $this->redirect()->toRoute('user/login');
        }

        if (! $byproducts['bookable']) {
            throw new RuntimeException(sprintf($this->t('This %s is already occupied'), $this->option('subject.square.type')));
        }

        return $this->ajaxViewModel($byproducts);
    }

    public function confirmationAction()
    {
        $dateStartParam = $this->params()->fromQuery('ds');
        $dateEndParam = $this->params()->fromQuery('de');
        $timeStartParam = $this->params()->fromQuery('ts');
        $timeEndParam = $this->params()->fromQuery('te');
        $squareParam = $this->params()->fromQuery('s');
        $quantityParam = $this->params()->fromQuery('q', 1);
        $productsParam = $this->params()->fromQuery('p', 0);
        $playerNamesParam = $this->params()->fromQuery('pn', 0);

        $serviceManager = $this->getServiceLocator();
        $squareValidator = $serviceManager->get('Square\Service\SquareValidator');

        $byproducts = $squareValidator->isBookable($dateStartParam, $dateEndParam, $timeStartParam, $timeEndParam, $squareParam);

        $user = $byproducts['user'];

        $query = $this->getRequest()->getUri()->getQueryAsArray();
        $query['ajax'] = 'false';

        if (! $user) {
            $this->redirectBack()->setOrigin('square/booking/confirmation', [], ['query' => $query]);

            return $this->redirect()->toRoute('user/login');
        } else {
            $byproducts['url'] = $this->url()->fromRoute('square/booking/confirmation', [], ['query' => $query]);
        }

        if (! $byproducts['bookable']) {
            throw new RuntimeException(sprintf($this->t('This %s is already occupied'), $this->option('subject.square.type')));
        }

        /* Check passed quantity */

        if (! (is_numeric($quantityParam) && $quantityParam > 0)) {
            throw new RuntimeException(sprintf($this->t('Invalid %s-amount choosen'), $this->option('subject.square.unit')));
        }

        $square = $byproducts['square'];

        if ($square->need('capacity') - $byproducts['quantity'] < $quantityParam) {
            throw new RuntimeException(sprintf($this->t('Too many %s for this %s choosen'), $this->option('subject.square.unit.plural'), $this->option('subject.square.type')));
        }

        $byproducts['quantityChoosen'] = $quantityParam;

        /* Check passed products */

        $products = array();

        if (! ($productsParam === '0' || $productsParam === 0)) {
            $productManager = $serviceManager->get('Square\Manager\SquareProductManager');
            $productTuples = explode(',', $productsParam);

            foreach ($productTuples as $productTuple) {
                $productTupleParts = explode(':', $productTuple);

                if (count($productTupleParts) != 2) {
                    throw new RuntimeException('Malformed product parameter passed');
                }

                $spid = $productTupleParts[0];
                $amount = $productTupleParts[1];

                if (! (is_numeric($spid) && $spid > 0)) {
                    throw new RuntimeException('Malformed product parameter passed');
                }

                if (! is_numeric($amount)) {
                    throw new RuntimeException('Malformed product parameter passed');
                }

                $product = $productManager->get($spid);

                $productOptions = explode(',', $product->need('options'));

                if (! in_array($amount, $productOptions)) {
                    throw new RuntimeException('Malformed product parameter passed');
                }

                $product->setExtra('amount', $amount);

                $products[$spid] = $product;
            }
        }

        $byproducts['products'] = $products;

        /* Check passed player names */

        if ($playerNamesParam) {
            $playerNames = Json::decode($playerNamesParam, Json::TYPE_ARRAY);
        } else {
            $playerNames = null;
        }

        /* display payment checkout */
        if ($this->config('paypal') != null && $this->config('paypal') == true) {  
            $byproducts['paypal'] = true;
        }
        if ($this->config('stripe') != null && $this->config('stripe') == true) {
            $byproducts['stripe'] = true;
            $byproducts['stripePaymentMethods'] = $this->config('stripePaymentMethods');
            $byproducts['stripeIcon'] = $this->config('stripeIcon');

        }
        if ($this->config('klarna') != null && $this->config('klarna') == true) {
            $byproducts['klarna'] = true;
        }
        if ($this->config('billing') != null && $this->config('billing') == true) {
            $byproducts['billing'] = true;
        }
        if ($this->config('payment_default') != null) {
            $byproducts['payment_default'] = $this->config('payment_default');
        }

        $payable = false;
        $total = 0;

        $member = 0;
        if ($user != null && $user->getMeta('member') != null) {
           $member = $user->getMeta('member');
        }

        $squarePricingManager = $serviceManager->get('Square\Manager\SquarePricingManager');
        $finalPricing = $squarePricingManager->getFinalPricingInRange($byproducts['dateStart'], $byproducts['dateEnd'], $square, $quantityParam, $member);
        if ($finalPricing != null && $finalPricing['price']) {
            $total+=$finalPricing['price'];
        }

        foreach ($products as $product) {

            $bills[] = new Bill(array(
               'description' => $product->need('name'),
               'quantity' => $product->needExtra('amount'),
               'price' => $product->need('price') * $product->needExtra('amount'),
               'rate' => $product->need('rate'),
               'gross' => $product->need('gross'),
            ));

            $total+=$product->need('price') * $product->needExtra('amount');
        }

        if ($total > 0 ) {
            $payable = true;
        }

        $byproducts['payable'] = $payable;

        /* Check booking form submission */

        $acceptRulesDocument = $this->params()->fromPost('bf-accept-rules-document');
        $acceptRulesText = $this->params()->fromPost('bf-accept-rules-text');
        $confirmationHash = $this->params()->fromPost('bf-confirm');
        $confirmationHashOriginal = sha1('Quick and dirty' . floor(time() / 1800));

        if ($confirmationHash) {

            if ($square->getMeta('rules.document.file') && $acceptRulesDocument != 'on') {
                $byproducts['message'] = sprintf($this->t('%sNote:%s Please read and accept the "%s".'),
                    '<b>', '</b>', $square->getMeta('rules.document.name', 'Rules-document'));
            }

            if ($square->getMeta('rules.text') && $acceptRulesText != 'on') {
                $byproducts['message'] = sprintf($this->t('%sNote:%s Please read and accept our rules and notes.'),
                    '<b>', '</b>');
            }

            if ($confirmationHash != $confirmationHashOriginal) {
                $byproducts['message'] = sprintf($this->t('%We are sorry:%s This did not work somehow. Please try again.'),
                    '<b>', '</b>');
            }

            if (! isset($byproducts['message'])) {

		       $bills = array();
/*
               $total = 0;

               $member = 0;
               if ($user != null && $user->getMeta('member') != null) {
                  $member = $user->getMeta('member'); 
               } 

               $squarePricingManager = $serviceManager->get('Square\Manager\SquarePricingManager');               
               $finalPricing = $squarePricingManager->getFinalPricingInRange($byproducts['dateStart'], $byproducts['dateEnd'], $square, $quantityParam, $member);
               if ($finalPricing['price']) {
                   $total+=$finalPricing['price'];
               }    

		       foreach ($products as $product) {
                        
			     $bills[] = new Bill(array(
			     'description' => $product->need('name'),
			     'quantity' => $product->needExtra('amount'),
			     'price' => $product->need('price') * $product->needExtra('amount'),
			     'rate' => $product->need('rate'),
			     'gross' => $product->need('gross'),
			     ));

                 $total+=$product->need('price') * $product->needExtra('amount'); 
		       }

               if ($total > 0 ) {
                  $payable = true;
               }
*/

            $bookingService = $serviceManager->get('Booking\Service\BookingService');
            $bookingManager = $serviceManager->get('Booking\Manager\BookingManager');

            $payservice = $this->params()->fromPost('paymentservice');
            $meta = array('player-names' => serialize($playerNames)); 
            
            if (($payservice == 'paypal' || $payservice == 'stripe' || $payservice == 'klarna') && $payable) {
                   $meta['directpay'] = 'true';
            }

            $booking = $bookingService->createSingle($user, $square, $quantityParam, $byproducts['dateStart'], $byproducts['dateEnd'], $bills, $meta);
            
            if (($payservice == 'paypal' || $payservice == 'stripe' || $payservice == 'klarna') && $payable) {
            # payment checkout
                if($payable) {
                   $basepath = $this->config('basepath');
                   if (isset($basepath) && $basepath != '' && $basepath != ' ') {
                       $basepath = '/'.$basepath;  
                   } 
                   $projectShort = $this->option('client.name.short');
                   $baseurl = $this->config('baseurl');
                   $proxyurl = $this->config('proxyurl');
		           $storage = $this->getServiceLocator()->get('payum')->getStorage('Application\Model\PaymentDetails');
                   $tokenStorage = $this->getServiceLocator()->get('payum.options')->getTokenStorage(); 
                   $captureToken = null;
                   $model = $storage->create();
                   $booking->setMeta('paymentMethod', $payservice);
                   $bookingManager->save($booking);
                   $userName = $user->getMeta('firstname') . ' ' . $user->getMeta('lastname');
                   $companyName = $this->option('client.name.full');

                   $locale = $this->config('i18n.locale');

                   $description = $projectShort.' booking-'.$booking->get('bid');
                   if (isset($locale) && ($locale == 'de-DE' || $locale == 'de_DE')) {
                        $description = $projectShort.' Buchung-'.$booking->get('bid');
                   }

                   #paypal checkout            
                   if ($payservice == 'paypal') {
    		           $model['PAYMENTREQUEST_0_CURRENCYCODE'] = 'EUR';
    		           $model['PAYMENTREQUEST_0_AMT'] = $total/100;
                       $model['PAYMENTREQUEST_0_BID'] = $booking->get('bid');
                       $model['PAYMENTREQUEST_0_DESC'] = $description;
                       $model['PAYMENTREQUEST_0_EMAIL'] = $user->get('email');
                       $storage->update($model);
     		           $captureToken = $this->getServiceLocator()->get('payum.security.token_factory')->createCaptureToken(
                           'paypal_ec', $model, $proxyurl.$basepath.'/square/booking/payment/done');
                   }				    
                   #paypal checkout
                   #stripe checkout
                   if ($payservice == 'stripe') {
                       $model["payment_method_types"] = $this->config('stripePaymentMethods');                       
                       $model["amount"] = $total;
                       $model["currency"] = 'EUR';
                       $model["description"] = $description;
                       $model["receipt_email"] = $user->get('email');
                       $model["metadata"] = array('bid' => $booking->get('bid'), 'productName' => $this->option('subject.type'), 'locale' => $locale, 'instance' => $basepath, 'projectShort' => $projectShort, 'userName' => $userName, 'companyName' => $companyName, 'stripeDefaultPaymentMethod' => $this->config('stripeDefaultPaymentMethod'), 'stripeAutoConfirm' => var_export($this->config('stripeAutoConfirm'), true), 'stripePaymentRequest' => var_export($this->config('stripePaymentRequest'), true));
                       $storage->update($model);
                       $captureToken = $this->getServiceLocator()->get('payum.security.token_factory')->createCaptureToken(
                           'stripe', $model, $proxyurl.$basepath.'/square/booking/payment/confirm');
                   }
                   #stripe checkout
                   #klarna checkout
                   if ($payservice == 'klarna') {
                       $model['purchase_country'] = 'DE';
                       $model['purchase_currency'] = 'EUR';
                       $model['locale'] = 'de-DE';
                       $storage->update($model); 
                       $captureToken = $this->getServiceLocator()->get('payum.security.token_factory')->createAuthorizeToken('klarna_checkout', $model, $proxyurl.$basepath.'/square/booking/payment/done');
                       $notifyToken = $this->getServiceLocator()->get('payum.security.token_factory')->createNotifyToken('klarna_checkout', $model);
                   }
                   #klarna checkout
                   
                   $targetUrl = str_replace($baseurl, $proxyurl, $captureToken->getTargetUrl());
                   $captureToken->setTargetUrl($targetUrl);
                   $tokenStorage->update($captureToken);

                   #klarna checkout update merchant details
                   if ($payservice == 'klarna') {
                       $model['merchant'] = array(
                           'terms_uri' => 'http://example.com/terms',
                           'checkout_uri' => $captureToken->getTargetUrl(),
                           'confirmation_uri' => $captureToken->getTargetUrl(),
                           'push_uri' => $notifyToken->getTargetUrl()
                       );
                       $model['cart'] = array(
                           'items' => array(
                                array(
                                   'reference' => $booking->get('bid'),
                                   'name' => $description,
                                   'quantity' => 1,
                                   'unit_price' => $total,
                                )
                           )
                       );
                       $storage->update($model);
                   }
                   #klarna checkout

                   return $this->redirect()->toUrl($captureToken->getTargetUrl());
                   }
                else {
                   $bookingService->cancelSingle($booking);
                   $this->flashMessenger()->addErrorMessage(sprintf($this->t('%sSorry online booking not possible at the moment!%s'),
                       '<b>', '</b>'));
                   return $this->redirectBack()->toOrigin();  
                }    
                # payment checkout
            } else {
                # no paymentservice
                if ($this->config('genDoorCode') != null && $this->config('genDoorCode') == true && $square->getMeta('square_control') == true) {
                    $doorCode = $booking->getMeta('doorCode');
                    $squareControlService = $serviceManager->get('SquareControl\Service\SquareControlService');
                    if ($squareControlService->activateDoorCode($booking->need('bid'), $doorCode) == true) {
                        $this->flashMessenger()->addSuccessMessage(sprintf($this->t('Your %s has been booked! The doorcode is: %s'),
                            $this->option('subject.square.type'), $doorCode));
                    } else {
                        $this->flashMessenger()->addErrorMessage(sprintf($this->t('Your %s has been booked! But the doorcode could not be send. Please contact admin by phone - %s'),
                            $this->option('subject.square.type'), $this->option('client.contact.phone')));
                    }
                }
                else{
                    $this->flashMessenger()->addSuccessMessage(sprintf($this->t('%sCongratulations:%s Your %s has been booked!'),
                        '<b>', '</b>',$this->option('subject.square.type')));
                }  

                if ($this->config('tmpBookingAt') != null) {    
                    $this->flashMessenger()->addSuccessMessage(sprintf($this->t('%sPayment and admittance temporarily at %s!%s'),
                        '<b>', $this->config('tmpBookingAt'), '</b>'));
                }

                return $this->redirectBack()->toOrigin();
            }                
          }               
        }

       return $this->ajaxViewModel($byproducts);
    }

    public function cancellationAction()
    {
        $bid = $this->params()->fromQuery('bid');

        if (! (is_numeric($bid) && $bid > 0)) {
            throw new RuntimeException('This booking does not exist');
        }

        $serviceManager = $this->getServiceLocator();
        $bookingManager = $serviceManager->get('Booking\Manager\BookingManager');
        $squareValidator = $serviceManager->get('Square\Service\SquareValidator');

        $booking = $bookingManager->get($bid);

        $cancellable = $squareValidator->isCancellable($booking);

        if (! $cancellable) {
            throw new RuntimeException('This booking cannot be cancelled anymore online.');
        }

        $origin = $this->redirectBack()->getOriginAsUrl();

        /* Check cancellation confirmation */

        $confirmed = $this->params()->fromQuery('confirmed');

        if ($confirmed == 'true') {

            $bookingService = $serviceManager->get('Booking\Service\BookingService');
            $bookingService->cancelSingle($booking);

            $this->flashMessenger()->addErrorMessage(sprintf($this->t('Your booking has been %scancelled%s.'),
                '<b>', '</b>'));

            return $this->redirectBack()->toOrigin();
        }

        return $this->ajaxViewModel(array(
            'bid' => $bid,
            'origin' => $origin,
        ));
    }

    public function confirmAction()
    {

        $token = $this->getServiceLocator()->get('payum.security.http_request_verifier')->verify($this);
        $gateway = $this->getServiceLocator()->get('payum')->getGateway($token->getGatewayName());
        $tokenStorage = $this->getServiceLocator()->get('payum.options')->getTokenStorage();
        $gateway->execute($status = new GetHumanStatus($token));

        $payment = $status->getFirstModel();

        // syslog(LOG_EMERG, $payment['status']);

        if ($payment['status'] === "requires_action" && !(array_key_exists('error',$payment))) {
            
           $payment['doneAction'] = $token->getTargetUrl();

           try {
               $gateway->execute(new Confirm($payment));

           } catch (ReplyInterface $reply) {
               if ($reply instanceof HttpRedirect) {
                  return $this->redirect()->toUrl($reply->getUrl());
               }
               if ($reply instanceof HttpResponse) {
                  $this->getResponse()->setContent($reply->getContent());
                  $response = new Response();
                  $response->setStatusCode(200);
                  $response->setContent($reply->getContent());
                  return $response;
               }
            throw new \LogicException('Unsupported reply', null, $reply);
            }

        }
   
        if ($payment['status'] != "requires_action" || array_key_exists('error',$payment)) {
           $doneAction = str_replace("confirm", "done", $token->getTargetUrl());

           $token->setTargetUrl($doneAction);
           $tokenStorage->update($token);
           return $this->redirect()->toUrl($doneAction);
        }

    }    

    public function doneAction()
    {
        // syslog(LOG_EMERG, 'doneAction');
        
        $serviceManager = $this->getServiceLocator();
        $bookingManager = $serviceManager->get('Booking\Manager\BookingManager');
        $squareManager = $serviceManager->get('Square\Manager\SquareManager');

        $token = $serviceManager->get('payum.security.http_request_verifier')->verify($this);

        $gateway = $serviceManager->get('payum')->getGateway($token->getGatewayName());

        $gateway->execute($status = new GetHumanStatus($token));

        $payment = $status->getFirstModel();

        $origin = $this->redirectBack()->getOriginAsUrl();
        
        $bid = -1;
        $notes = '';
#paypal
        if ($token->getGatewayName() == 'paypal_ec') {       
            $bid = $payment['PAYMENTREQUEST_0_BID'];
            $notes = 'direct pay with paypal - ';
        }
#paypal        
#stripe
        if ($token->getGatewayName() == 'stripe') {
            $bid = $payment['metadata']['bid'];
            $notes = 'direct pay with stripe ' . $payment['charges']['data'][0]['payment_method_details']['type'] . ' - ';
        }    
#stripe
#klarna
        if ($token->getGatewayName() == 'klarna') {
            $bid = $payment['items']['reference'];
            $notes = 'direct pay with klarna - ';
        }
#klarna

        if (! (is_numeric($bid) && $bid > 0)) {
            throw new RuntimeException('This booking does not exist');
        }
        $bookingService = $serviceManager->get('Booking\Service\BookingService');

        $booking = $bookingManager->get($bid);
        $square = $squareManager->get($booking->need('sid'));

        if ($status->isCaptured() || $status->isAuthorized() || $status->isPending() || ($status->isUnknown() && $payment['status'] == 'processing') || $status->getValue() === "success" || $payment['status'] === "succeeded" ) {

            if (!$booking->getMeta('directpay_pending') == 'true') {
                if ($this->config('genDoorCode') != null && $this->config('genDoorCode') == true && $square->getMeta('square_control') == true) {
                   $doorCode = $booking->getMeta('doorCode');  
                   $squareControlService = $serviceManager->get('SquareControl\Service\SquareControlService'); 
                   if ($squareControlService->activateDoorCode($bid, $doorCode) == true) {
                       $this->flashMessenger()->addSuccessMessage(sprintf($this->t('Your %s has been booked! The doorcode is: %s'),
                           $this->option('subject.square.type'), $doorCode));
                   } else {
                       $this->flashMessenger()->addErrorMessage(sprintf($this->t('Your %s has been booked! But the doorcode could not be send. Please contact admin by phone - %s'),
                           $this->option('subject.square.type'), $this->option('client.contact.phone')));
                   }
                }
                else {
                    // syslog(LOG_EMERG, 'success not pending');
                    $this->flashMessenger()->addSuccessMessage(sprintf($this->t('%sCongratulations:%s Your %s has been booked!'),
                        '<b>', '</b>',$this->option('subject.square.type')));
                }
            }

            if($status->isPending() || ($status->isUnknown() && $payment['status'] == 'processing')) {
                // syslog(LOG_EMERG, 'success pending/processing');
                $booking->set('status_billing', 'pending');
                $booking->setMeta('directpay', 'false');
                $booking->setMeta('directpay_pending', 'true');
            }
            else {
                // syslog(LOG_EMERG, 'success paid');
                $booking->set('status_billing', 'paid');
                $booking->setMeta('directpay', 'true');
                $booking->setMeta('directpay_pending', 'false');
            }

            // $notes = $notes . "payment_status: " . $status->getValue() . ' ' . $payment['status'];
            // $booking->setMeta('notes', $notes);
            $bookingService->updatePaymentSingle($booking);
	    }
	    else
        {
            if (!$booking->getMeta('directpay_pending') == 'true') {
                if(isset($payment['error']['message'])) {
                    $this->flashMessenger()->addErrorMessage(sprintf($payment['error']['message'],
                                            '<b>', '</b>'));
                }
                $this->flashMessenger()->addErrorMessage(sprintf($this->t('%sError during payment: Your booking has been cancelled.%s'),
                    '<b>', '</b>'));
            }
            $bookingService->cancelSingle($booking);
        }  

        return $this->redirectBack()->toOrigin();
   
    }

}
