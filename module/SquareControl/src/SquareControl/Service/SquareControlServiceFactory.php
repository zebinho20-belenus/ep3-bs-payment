<?php

namespace SquareControl\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class SquareControlServiceFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $sm)
    {
        $configManager = $sm->get('Base\Manager\ConfigManager');
        $optionManager = $sm->get('Base\Manager\OptionManager');

        return new SquareControlService($configManager, $optionManager);
    }

}
