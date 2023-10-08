<?php

namespace SquareControl\Service;

use Base\Manager\ConfigManager;
use Base\Manager\OptionManager;
use Base\Service\AbstractService;
use Booking\Manager\BookingManager;
use Booking\Manager\ReservationManager;

class SquareControlService extends AbstractService
{

    protected $configManager;
    protected $optionManager;
    protected $bookingManager;
    protected $reservationManager;

    public function __construct(ConfigManager $configManager, OptionManager $optionManager, BookingManager $bookingManager, ReservationManager $reservationManager)
    {
        $this->configManager = $configManager;
        $this->optionManager = $optionManager;
        $this->bookingManager = $bookingManager;
        $this->reservationManager = $reservationManager;
    }

    public function deleteDoorCode($bid) {

        $doorCodeUuid = $this->getDoorCodeUuid($bid);

        if ($doorCodeUuid != null) {
            $this->deleteDoorCodeByUuid($doorCodeUuid);
        }

        return false; 

    }

    public function deleteDoorCodeByUuid($doorCodeUuid) {

        $request = $this->configManager->get('deleteDoorCodeRequest');

        $request = str_replace("%%doorCodeUuid%%", $doorCodeUuid, $request);

        // syslog(LOG_EMERG, $request);

        $result = $this->sendRequest($request);

        if ($result['LL']['Code'] == '200') {
           return true;
        }

        return false;

    }

    public function deactivateDoorCode($bid) {

        $doorCodeUuid = $this->getDoorCodeUuid($bid);

        if ($doorCodeUuid != null) {
            $this->deactivateDoorCodeByUuid($doorCodeUuid);
        }

        return false;

    }

    private function deactivateDoorCodeByUuid($doorCodeUuid) {

        $request = $this->configManager->get('deactivateDoorCodeRequest');

        $request = str_replace("%%doorCodeUuid%%", $doorCodeUuid, $request);

        $result = $this->sendRequest($request);

        if ($result['LL']['Code'] == '200') {
           return true;
        }

        return false;

    }

    public function updateDoorCode($bid) {

        $doorCodeUuid = $this->getDoorCodeUuid($bid);

        if ($doorCodeUuid != null) {
            $this->updateDoorCodeByUuid($bid, $doorCodeUuid);
        }

        return false;

    }

    private function updateDoorCodeByUuid($bid, $doorCodeUuid) {

        $request = $this->configManager->get('updateDoorCodeRequest');

        $reservations = $this->reservationManager->getBy(['bid' => $bid], 'date ASC', 1);

        $timebuffer = $this->configManager->get('doorCodeTimeBuffer');
        $doorCodeRequest = $this->configManager->get('createDoorCodeRequest');

        $reservation = current($reservations);

        $reservationTimeStart = explode(':', $reservation->need('time_start'));
        $reservationTimeEnd = explode(':', $reservation->need('time_end'));

        $reservationStart = new \DateTime($reservation->need('date'));
        $reservationStart->setTime($reservationTimeStart[0], $reservationTimeStart[1]);
        $reservationStart->modify('-' . $timebuffer);
        $reservationStart->setTimezone(new \DateTimeZone("UTC"));
        $reservationEnd = new \DateTime($reservation->need('date'));
        $reservationEnd->setTime($reservationTimeEnd[0], $reservationTimeEnd[1]);
        $reservationEnd->modify('+' . $timebuffer);
        $reservationEnd->setTimezone(new \DateTimeZone("UTC"));

        $timeFrom = $reservationStart->getTimestamp();
        $timeTo = $reservationEnd->getTimestamp();

        $request = str_replace("%%bid%%", $bid, $doorCodeRequest);
        $request = str_replace("%%timeFrom%%", $timeFrom, $request);
        $request = str_replace("%%timeTo%%", $timeTo, $request);
        $request = str_replace("%%doorCodeUuid%%", $doorCodeUuid, $request);

        $result = $this->sendRequest($request);

        if ($result['LL']['Code'] == '200') {
           return true;
        }

        return false;

    }

    public function getAllDoorCodes() {

        $request = $this->configManager->get('getDoorCodesRequest');
        $result = $this->sendRequest($request);


        if ($result['LL']['Code'] == '200') {
            $codes = json_decode($result['LL']['value']);
            return array_reverse($codes);
        }
        return null;
    }

    public function getInactiveBookingDoorCodes() {

        $codes = $this->getInactiveDoorCodes();

        $inActiveBookingDoorCodes = array();

        foreach($codes as $code) {

            if (strpos($code->name, 'booking-') === 0) {
                $inActiveBookingDoorCodes[] = $code;
            }
        }

        return $inActiveBookingDoorCodes;

    }    

     public function getInactiveDoorCodes() {

        $codes = $this->getAllDoorCodes();
        // syslog(LOG_EMERG, json_encode($codes));
        
        $timest = time();

        $inActiveDoorCodes = array();

        foreach($codes as $code) {

            if ($code->isActive == false && $timest > $code->timeTo) {
                $inActiveDoorCodes[] = $code;
                // syslog(LOG_EMERG, json_encode($code));
            }
        }

        return $inActiveDoorCodes;

    }

    private function getDoorCodeUuid($bid) {

        $codes = $this->getAllDoorCodes();

        // search for bid in result
        foreach($codes as $code) {
            if ($code->name === 'booking-' . $bid) {
                // syslog(LOG_EMERG, $code->uuid);
                return $code->uuid;        
            }    
        }    
        return null;
    }

    public function removeInActiveDoorCodes() {

        $codes = $this->getInActiveDoorCodes();

        foreach($codes as $code) {
            // syslog(LOG_EMERG, $code->name);
            $this->deleteDoorCodeByUuid($code->uuid);
        }

    }

    public function removeInActiveBookingDoorCodes() {

        $codes = $this->getInActiveBookingDoorCodes();

        foreach($codes as $code) {
            // syslog(LOG_EMERG, $code->name);
            $this->deleteDoorCodeByUuid($code->uuid);
        }

    } 

    private function sendRequest($request) {

        # senden mit guzzle
        try {
            $client = new \GuzzleHttp\Client();
            $http_res = $client->get($request);
            $http_status = $http_res->getStatusCode();
            if ($http_status == 200) {
                $result = json_decode($http_res->getBody(), true);
                return $result;   
            }
        }
        catch (\Exception $e) {
            # catch all
            // syslog(LOG_EMERG, $e->getMessage());
        }

        return false;

    }    
    
    public function createDoorCode($bid, $doorCode) {

        $reservations = $this->reservationManager->getBy(['bid' => $bid], 'date ASC', 1);

        $timebuffer = $this->configManager->get('doorCodeTimeBuffer');
        $doorCodeRequest = $this->configManager->get('createDoorCodeRequest');

        $reservation = current($reservations);

        $reservationTimeStart = explode(':', $reservation->need('time_start'));
        $reservationTimeEnd = explode(':', $reservation->need('time_end'));

        $reservationStart = new \DateTime($reservation->need('date'));
        $reservationStart->setTime($reservationTimeStart[0], $reservationTimeStart[1]);
        $reservationStart->modify('-' . $timebuffer);
        $reservationStart->setTimezone(new \DateTimeZone("UTC"));
        $reservationEnd = new \DateTime($reservation->need('date'));
        $reservationEnd->setTime($reservationTimeEnd[0], $reservationTimeEnd[1]);
        $reservationEnd->modify('+' . $timebuffer);
        $reservationEnd->setTimezone(new \DateTimeZone("UTC"));

        $timeFrom = $reservationStart->getTimestamp();
        $timeTo = $reservationEnd->getTimestamp();

        $request = str_replace("%%bid%%", $bid, $doorCodeRequest);
        $request = str_replace("%%doorCode%%", $doorCode, $request);
        $request = str_replace("%%timeFrom%%", $timeFrom, $request);
        $request = str_replace("%%timeTo%%", $timeTo, $request);

        $result = $this->sendRequest($request);

        if ($result['LL']['Code'] == '200') {
           return true;
        }

        return false;

    }    

}
