<?php
/**
 * Functions used
 */

/**
 * @param $url
 */

function redirectPage($url)
{
    header("Location: $url");
}

/**
 * @param $urlLink
 * @return string
 */
function getUrl($urlLink){
    $url = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') ? 'https://' : 'http://';
    $url .= $_SERVER['SERVER_NAME'];
    return $url . '/' . $urlLink;
}