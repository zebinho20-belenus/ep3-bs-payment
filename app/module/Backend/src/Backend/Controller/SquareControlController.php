<?php

namespace Backend\Controller;

use DateTime;
use Zend\Db\Adapter\Adapter;
use Zend\Mvc\Controller\AbstractActionController;
use GuzzleHttp\Client;

class SquareControlController extends AbstractActionController
{

    public function indexAction()
    {
        $this->authorize('admin.booking');

        $serviceManager = @$this->getServiceLocator();
        $squareControlService = $serviceManager->get('SquareControl\Service\SquareControlService');       

        $codes = array();

        // $dateStart = $this->params()->fromQuery('date-start');
        // $dateEnd = $this->params()->fromQuery('date-end');
        // $search = $this->params()->fromQuery('search');

        /*
        if ($dateStart) {
            $dateStart = new \DateTime($dateStart);
        }

        if ($dateEnd) {
            $dateEnd = new \DateTime($dateEnd);
        }
         */

        /*
        if (($dateStart && $dateEnd) || $search) {
            // $filters = $this->backendBookingDetermineFilters($search);

            try {
                $limit = 1000;

                if ($dateStart && $dateEnd) {
                    $codes = $squareControlService->getDoorCodes($dateStart, $dateEnd);
                } else {
                    $codes = $squareControlService->getAllDoorCodes();
                }

            } catch (\RuntimeException $e) {
                $codes = array();
            }
        }
        */


        $codes = $squareControlService->getInActiveBookingDoorCodes();

        // syslog(LOG_EMERG, json_encode($codes));

        return array(
            'codes' => $codes,
        );
    }

    public function removeinactivedoorcodesAction()
    {
        $this->authorize('admin.booking');

        $serviceManager = @$this->getServiceLocator();
        $squareControlService = $serviceManager->get('SquareControl\Service\SquareControlService');

        $squareControlService->removeInactiveBookingDoorCodes();

        return $this->redirect()->toRoute('backend/squarecontrol');
    }

    public function deletedoorcodeAction()
    {
        $this->authorize('admin.booking');

        $serviceManager = @$this->getServiceLocator();
        $squareControlService = $serviceManager->get('SquareControl\Service\SquareControlService');

        $dcid = $this->params()->fromRoute('dcid');

        $squareControlService->deleteDoorCodeByUuid($dcid);

        return $this->redirect()->toRoute('backend/squarecontrol');
    }

} 
