<?php
if (php_sapi_name() !== 'cli')die("run though cli");
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
require_once('whoiser.php');
$db = new SQLite3('/var/www/html/boran.i2p/lwhois/netdb.db');
//$db->exec('drop table hosts;');
$db->exec('CREATE TABLE IF NOT EXISTS hosts (ip STRING, country_iso_code STRING, city_name STRING, netname STRING);');
$dir = 'netdb_raw';
$files = scandir($dir);
$whois = new whoiser;

foreach($files as $file){
	$ips = array();
	if($file == '.' or $file == '..') continue;
	//print($file);
	$f = fopen($dir."/".$file,"r");
	while( ($data=fread($f,1024)) ){
		$ips=array_merge($ips,explode("\n", $data));
	}
	foreach($ips as &$ip){
		$ip_match=array();
		if (preg_match('/\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}/', $ip, $ip_match)) {
   			$ip=$ip_match[0];
			
		}else continue;
		
		//print($ip."<br/>");
		//подключиться к базе "mary" на хосте "sheep", используя имя пользователя и пароль

		


		$ret = $whois->whois($ip);
		if( sizeof($ret) > 0  &&  is_array($ret) ){
			$geoip=$ret['geoip'][0];
			$justinfo=$ret['justinfo'][0];

			$city_name=$geoip['city_name'];
			$country_iso_code=$geoip['country_iso_code'];
			$city_name=$geoip['city_name'];
			$netname=$justinfo['netname'];

			$check_before_req = "SELECT COUNT(*) FROM hosts where ip='$ip';";
			$count = $db->query("$check_before_req")->fetchArray();
	
			if($count[0]) {
			//	print("ip:$ip already added\n");
				continue;
			}	
			print("get info about ip:$ip\n");
		
			$req = "INSERT INTO hosts values('$ip', '$country_iso_code','$city_name','$netname');";
			$db->query($req);
			print($req);

		}
		//var_dump($ret);
		//die("");
	}//foreach
}





?>
