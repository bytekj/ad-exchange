<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

require_once '../include/global.php';
require_once '../Classes/Utils.php';

$objUtil = new Utils();

$remoteIpAddress = $_SERVER['REMOTE_ADDR'];
$serverIpAddress = $_SERVER['SERVER_ADDR'];

_Print("Server IP:".$serverIpAddress);

$serverDetail = $objUtil->getIpDetails($serverIpAddress);
if($serverDetail == FALSE){
    echo "<br>cant get server detail";
}
_Print($serverDetail);

_Print("Remote addr ".$remoteIpAddress);

$remoteDetail = $objUtil->getIpDetails($remoteIpAddress);

_Print($remoteDetail);



function _Print($toPrint){
    echo "<pre>";
    print_r($toPrint);
    echo "</pre>";
}
?>
