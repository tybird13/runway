<?php

/**
 * Created by PhpStorm.
 * User: tjdal
 * Date: 7/16/2017
 * Time: 4:03 PM
 */
class DateTimeLogger
{

    private $timezone;
    private $log_file_path;

    public function __construct($UIN, string $log_file_path){
        $this->timezone = new DateTimeZone('America/New York');
        $this->log_file_path = $log_file_path;

    }

    public function clock_in(){
        $date_time = new DateTime();
        $date_time->setTimezone($this->timezone);
        $str = sprintf("\n%s,", $date_time->format("m/d/Y h:i A"));
        echo $str;
    }

    public function clock_out(){

    }

    public function run_report(){

    }
}