<?php

namespace Payment\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class PaymentServiceFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $sm)
    {
        $configManager = $sm->get('Base\Manager\ConfigManager');
        $optionManager = $sm->get('Base\Manager\OptionManager');
        $bookingManager = $sm->get('Booking\Manager\BookingManager');
        $reservationManager = $sm->get('Booking\Manager\ReservationManager');

        return new PaymentService($configManager, $optionManager, $bookingManager, $reservationManager);
    }

}
