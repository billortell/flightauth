<?php

namespace FlightAuth\Helpers;

class Date {

    const mysqlFormat = 'Y-m-d h:i:sA';
    const dayFormat = 'Y-m-d';

    static public function getDateTime()
    {
        $timestamp = new \DateTime();
        return $timestamp->format(self::getMysqlFormat());
    }

    static public function getDate()
    {
        $timestamp = new \DateTime();
        return $timestamp->format(self::getDayformat());
    }

    static public function getMysqlFormat()
    {
        return self::mysqlFormat;
    }

    static public function getDayFormat()
    {
        return self::dayFormat;
    }

}