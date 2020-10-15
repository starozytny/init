<?php


namespace App\Service;

use DateTime;

class DateService
{
    public function getDateFormat($date, $format='d-m-Y\\TH:i:s')
    {
        return new DateTime(date($format, strtotime($date)));
    }
}
