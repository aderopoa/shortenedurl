<?php
/**
 * Handle creation of tiny url.
 */
require_once "../Library/Tiny.php";
require_once "../Library/functions.php";

if(isset($_POST["url"])){
    $url = rtrim($_POST["url"], "/");
    if(!filter_var($url, FILTER_VALIDATE_URL))
    {
        $response = array("status" => 'error', "message" => "Submitted URL is not a valid URL", "error_code" => 20);
        echo json_encode($response);
    }
    else
    {
        try{
            $tiny = new Tiny();
            echo ($tiny->createUrl($url));
        }
        catch (Exception $e){
            $response = array("status" => 'error', "message" => "Error connecting. Try again", "error_code" => 50);
            echo json_encode($response);
        }
    }
}
else redirectPage("index.php");
