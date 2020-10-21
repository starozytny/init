<?php


namespace App\Service;

class CalendarService
{
    public function getThisWeek()
    {
        date_default_timezone_set('Europe/Paris');

        $week = [];
        $firstDayOfWeek = getdate(strtotime("this week"));
        for ($i=0 ; $i<7 ; $i++){
            array_push($week, $this->getDayByWeek($firstDayOfWeek, $firstDayOfWeek['mday']+$i));
        }

        return $week;
    }

    private function getDayByWeek($firstDayOfWeek, $day){
        return getdate(mktime(0,0,0, $firstDayOfWeek['mon'], $day, $firstDayOfWeek['year']));
    }
}
