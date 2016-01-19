<?php

/**
 * Class LogMessage
 */
class LogMessage {

    public static function exception($message, $function, $class, $removeDate = false)
    {
        $error_file = dirname(__FILE__) ."/../logs/exceptions.log";
        $logMessage = "Function = ". $class . " -> " . $function . "\n";
        $logMessage .= "Error = ". $message . "\n";
        if(!$removeDate){
            $logMessage = date("Y-m-d H:i:s") . ": " . $logMessage . "\n";
        }

        error_log($logMessage, 3, $error_file);
    }
} 