<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 *
 * @author kiran
 */
require_once '../Classes/Adspot.php';
require_once '../Classes/Content.php';
require_once '../Db/DataHandler.php';
require_once '../include/global.php';
$regions = array();
$genres = array();

if ($_GET['regions']) {
    
    $regions = explode(",", $_GET['regions']);
    if($regions == "All")
    {
        $regions = array();
    }
}
if ($_GET['genres']) {
    $genres = explode(",", $_GET['genres']);
    if($genres == "All"){
        $genres = array();
    }
}


$objContent = new Content();
//echo "<br>" . sizeof($regions) . " " . sizeof($genres);
//echo"<pre>";
//print_r($genres);
//echo"</pre>";
//
//echo"<pre>";
//print_r($regions);
//echo"</pre>";
echo $objContent->getAdSpots($regions, $genres);
?>
