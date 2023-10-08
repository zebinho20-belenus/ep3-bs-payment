<?php

namespace Base\View\Helper;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class PriceFormatPlainFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $sm)
    {
        return new PriceFormatPlain($sm->getServiceLocator()->get('Base\Manager\OptionManager'));
    }

}
