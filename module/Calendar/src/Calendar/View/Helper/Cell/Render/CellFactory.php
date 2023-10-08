<?php

namespace Calendar\View\Helper\Cell\Render;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class CellFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $sm)
    {
        return new Cell($sm->getServiceLocator()->get('Base\Manager\OptionManager'));
    }

}
