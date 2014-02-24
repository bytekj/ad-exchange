<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

require_once 'GeoIP/geoip.inc';
require_once 'GeoIP/geoipcity.inc';

$gi = geoip_open("/var/www/adex/include/GeoIP/db/GeoIPCity.dat",GEOIP_STANDARD);

define("PATH", '/adex/public/');
define("DB", "adsparx");
define("DB_USERNAME", "root");
define("DB_PASS", "sam123");
define("LOGO_STORE", "/var/www/adex/resource/brand_logo/");
define("LOGO_URL", "http://localhost/adex/resource/brand_logo/");
define("AD_STORE", "/var/www/adex/resource/ads/");
define("AD_URL", "http://localhost/adex/resource/ads/");
define("TRANSCODE", "/var/www/adex/transcoder/transcode");
define("TRANSCODE_CONF", "/var/www/adex/transcoder/");
define("ORIG_AD_FILES", "/var/www/adex/resource/ads/orig/");
define("ENCODED_AD_FILES","/var/www/adex/resource/ads/encoded/");
define("SERVER_TZ", '+00:00');
define("KEY","qwertyui");

function _debug($stmt){
	echo "<pre>";
	print_r($stmt);
	echo "</pre>";
}
?>
