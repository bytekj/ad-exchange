<?php
$filename = $_GET['filename'];
$cid = rand ( 100000000 , 9999999999 );

$url = 'http://54.243.237.61/adex/public/getad.php?proto=RTMP&cid='.$cid.'&ip='.$_SERVER['REMOTE_ADDR'].'&chname='.$filename.'&t=1234&dev=android&path=/usr/local/WowzaMediaServer/content/&ssid=1234&pubid=zenga_pub&profid=3&n=1&ua='.$_SERVER['HTTP_USER_AGENT'].'_demo';
$json = file_get_contents($url);

echo $json
?>