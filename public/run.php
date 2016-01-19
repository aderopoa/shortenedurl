<?php
/**
 * Where redirect happens
 */

require_once "../Library/Tiny.php";
require_once "../Library/functions.php";
require_once "../Library/LogMessage.php";

$url = isset($_GET['url']) ? $_GET['url'] : null;
$url = rtrim($url, '/');
$url = explode('/', $url);

if(count($url) == 1){
    try{
        $tiny = new Tiny();
        $tiny->registerRequest($url[0]);
        $request = $tiny->getRequestUrl($url[0]);
        $urlObj = json_decode($request);
        if($urlObj->status == 'success'){
            redirectPage($urlObj->url);
        }
        else{
            redirectPage("error.php");
        }
    }
    catch (Exception $e){
        LogMessage::exception($e->getMessage(),"run.php", "run.php");
        redirectPage("404.php");
    }

}
else {
    redirectPage("error.php");
}
