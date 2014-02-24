<?php
$id = $_GET['id'];

$logurl = "http://54.243.237.61/adex/public/log.php?t=".microtime()."&id=".$id."&debug=jwplayer_vast";


echo file_get_contents($logurl);

?>