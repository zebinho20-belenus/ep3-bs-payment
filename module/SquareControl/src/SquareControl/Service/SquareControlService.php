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

    public function deactivateDoorCode($bid) {

        $booking = $this->bookingManager->get($bid);

        $doorCodeUuid = $booking->getMeta('doorCodeUuid');

        $doorCodeRequest = $this->configManager->get('deactivateDoorCodeRequest');

        $request = str_replace("%%bid%%", $bid, $doorCodeRequest);
        $request = str_replace("%%doorCodeUuid%%", $doorCodeUuid, $request);

        if ($result['LL']['Code'] == '200') {
           return true;
        }

        return false; 

    }

    public function getDoorCode($bid) {

        $request = $this->configManager->get('getDoorCodeRequest');
        $result = $this->sendDoorCodeRequest($request);

        // search for bid in result


    }

    private function sendDoorCodeRequest($request) {

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
    
    public function activateDoorCode($bid, $doorCode) {

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

        $result = $this->sendDoorCodeRequest($request);

        if ($result['LL']['Code'] == '200') {
           return true;
        }

        return false;

    }    

}
