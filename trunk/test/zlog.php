<?php
$id = $_GET['id'];
//call log

echo file_get_contents('http://54.243.237.61/adex/public/log.php?id='.$id);
?>