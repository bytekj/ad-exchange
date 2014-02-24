<?php

/*
 * To change this template, choose Tools | Templates
* and open the template in the editor.
*/

/**
 * Description of Utils
 *
 * @author kj
 */
class Utils {

	//put your code here
	public static function getMonth($no) {
		$month = "";
		switch ($no) {
			case 1:
			$month = "JAN";
			break;
			case 2:
			$month = "FEB";
			break;
			case 3:
			$month = "MAR";
			break;
			case 4:
			$month = "APR";
			break;
			case 5:
			$month = "MAY";
			break;
			case 6:
			$month = "JUN";
			break;
			case 7:
			$month = "JUL";
			break;
			case 8:
			$month = "AUG";
			break;
			case 9:
			$month = "SEP";
			break;
			case 10:
			$month = "OCT";
			break;
			case 11:
			$month = "NOV";
			break;
			case 12:
			$month = "DEC";
			break;
		}
		return $month;
	}
	public static function getPrevYears(){
		$sql = "SELECT DISTINCT (YEAR( dt ))YEAR
		FROM calendar
		WHERE YEAR( dt ) <= YEAR( NOW( ) ) 
		ORDER BY YEAR DESC ";
		//_debug($sql);
		$objData = new DataHandler();
		$res = $objData->GetQuery($sql);
		if($res == -1){
			return false;
		}
		else{
			return $res;
		}
	}

	public static function getAllCityTiers() {
		$sql = "select tier_name,tier from city_tier_master where 1";
		$objData = new DataHandler();
		$ret = $objData->GetQuery($sql);
		if ($ret == -1) {
			return false;
		} else {
			return $ret;
		}
	}

	public static function getAllGenres() {
		$sql = "select id,genre_name,genre from genre_master where 1";
		$objData = new DataHandler();
		$ret = $objData->GetQuery($sql);
		if ($ret == -1) {
			return false;
		} else {
			return $ret;
		}
	}

	public static function getAllPlatforms() {
		$sql = "select platform_name,platform from platform_master where 1";
		$objData = new DataHandler();
		$ret = $objData->GetQuery($sql);
		if ($ret == -1) {
			return false;
		} else {
			return $ret;
		}
	}

	public static function getAllRegions() {
		$sql = "select id,region_name,region from region_master";
		$objData = new DataHandler();
		$ret = $objData->GetQuery($sql);
		if ($ret == -1) {
			return false;
		} else {
			return $ret;
		}
	}
	
	public static function getAllLanguages(){
		$sql = "SELECT `id`, `lang_name`, `lang_code` FROM `language` WHERE 1";
		$objData = new DataHandler();
		$ret = $objData->GetQuery($sql);
		if($ret == -1){
			return false;
		}
		else {
			return $ret;
		}
	}

	public static function http($req, $port = 80) {

		// create a new cURL resource
		$ch = curl_init();

		// set URL and other appropriate options
		curl_setopt($ch, CURLOPT_URL, $req);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_PORT, $port);

		// grab URL and pass it to the browser
		$ret = curl_exec($ch);

		// close cURL resource, and free up system resources
		curl_close($ch);
		return $ret;
	}

	public static function getDeviceType($userAgent){
		$device = "";
		$haystack = $userAgent;
		if(strstr($haystack, 'Bada')){
			$device = 'bada';
		}else if(strstr($haystack, 'Windows') || strstr($haystack, 'WIN') || strstr($haystack, 'WMPlayer')){
			$device = 'wphone';
		}
		else if(strstr($haystack, 'RealMedia') || strstr($haystack, 'Series 60')){
			$device = 'symb';
		}
		else if(strstr($haystack, 'BlackBerry')){
			$device = 'bb';
		}
		else if(strstr($haystack, 'midp') || strstr($haystack, 'MIDP') || strstr($haystack, 'SonyEricsson/') || strstr($haystack, 'Sony Ericsson/')) {
			$device = "java";
		}
		else if(strstr($haystack, 'Android') || strstr($haystack, 'HTC Streaming Player')){
			$device = "android";
		}
		else if(strstr($haystack, 'AppleCoreMedia') || strstr($haystack, 'QuickTime')){
			$device = "ios";
		}
		else if(strstr($haystack, 'QtvPlayer') || strstr($haystack, 'SAMSUNG-GT') || strstr($haystack, 'SAMSUNG GT') || strstr($haystack, 'Samsung GT')){
			$device = 'others';
		}
		else {
			$device = 'others';
		}
		return $device;
	}

	public static function getDevice($userAgent) {
		$device = "";
		$haystack = $userAgent;
		if ($_GET['debug']) {
			echo "<pre>";
			echo "<br>" . $haystack;
			echo "<br>Needle found: " . strstr($haystack, "SymbianOS");
			echo "</pre>";
		}

		if (strstr($haystack, "Android")) {
			$device = "android";
		}  else if (strstr($haystack, "Blackberry")) {
			$device = "bb";
		} else if (strstr($haystack, "IEMobile")) {
			$device = "wphone";
		} else if (strstr($haystack, "SymbianOS") != "") {
			if ($_GET['debug']) {
				echo "<pre>";
				echo __LINE__;
				echo "</pre>";
			}
			$device = "symb";
		} else if (strstr($haystack, "iPhone")) {
			$device = "ios";
		} else if (strstr($haystack, "LibVLC")) {
			$device = "others";
		} else {
			if ($_GET['debug']) {
				echo "<pre>";
				echo $haystack;
				echo __LINE__;
				echo "</pre>";
			}
			$device = "others";
		}
		if ($_GET['debug']) {
			echo "<pre>";
			echo $device;
			echo "</pre>";
		}
		return $device;
	}

	public static function getIpDetails($ipaddress) {
		global $gi;

		/*
		 * Array
		(
				[continent_code] => AS
				[country_code] => IN
				[country_code3] => IND
				[country_name] => India
				[region] => 16
				[city] => Mumbai
				[postal_code] =>
				[latitude] => 18.97500038147
				[longitude] => 72.825798034668
				[dma_code] => 0
				[area_code] => 0
		)
		*
		*/
		if ($_GET['debug'] == 1) {
			echo "<br>" . $ipaddress;
		}
		$result = GeoIP_record_by_addr($gi, $ipaddress);

		if ($result == NULL) {
			return false;
		} else {
			return $result;
		}
	}

	public static function GetCountryCodeByCountryName($countryName) {
		global $gi;
		$index = array_search($countryName, $gi->GEOIP_COUNTRY_NAMES);
		if ($index == FALSE) {
			return FALSE;
		}
		return $gi->GEOIP_COUNTRY_CODES[$index];
	}

	public static function GetCountryNameByCountryCode($countryCode){
		global $gi;
		$index = array_search($countryCode, $gi->GEOIP_COUNTRY_CODES);
		if($index == FALSE){
			return "";
		}
		else{
			return $gi->GEOIP_COUNTRY_NAMES[$index];
		}


	}

	public static function encrypt ($pwd, $data, $ispwdHex = 0)
	{
		if ($ispwdHex)
			$pwd = @pack('H*', $pwd); // valid input, please!

		$key[] = '';
		$box[] = '';
		$cipher = '';

		$pwd_length = strlen($pwd);
		$data_length = strlen($data);

		for ($i = 0; $i < 256; $i++)
		{
			$key[$i] = ord($pwd[$i % $pwd_length]);
			$box[$i] = $i;
		}
		for ($j = $i = 0; $i < 256; $i++)
		{
			$j = ($j + $box[$i] + $key[$i]) % 256;
			$tmp = $box[$i];
			$box[$i] = $box[$j];
			$box[$j] = $tmp;
		}
		for ($a = $j = $i = 0; $i < $data_length; $i++)
		{
			$a = ($a + 1) % 256;
			$j = ($j + $box[$a]) % 256;
			$tmp = $box[$a];
			$box[$a] = $box[$j];
			$box[$j] = $tmp;
			$k = $box[(($box[$a] + $box[$j]) % 256)];
			$cipher .= chr(ord($data[$i]) ^ $k);
		}
		return $cipher;
	}
	public static function decrypt ($pwd, $data, $ispwdHex = 0)
	{
		return encrypt($pwd, $data, $ispwdHex);
	}

}

?>
