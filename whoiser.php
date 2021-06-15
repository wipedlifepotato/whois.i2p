<?php
class whoiser{
	public function __construct($max_on_page=50){
		$this->MAX_ON_PAGE = intval($max_on_page);
		$this->REQ_IPS_BY_CITY_NAME 
			= '/home/lialh4/localwhois/bin/geopip/get_all_ips_by_city_name "%s" %d %d'; // CityName Limit Offset
		$this->REQ_LOCAL_WHOIS 
			= '/home/lialh4/localwhois/bin/query %s';
		$this->REQ_LOCAL_WHOIS_GEO='/home/lialh4/localwhois/bin/geopip/geoipwhois %s';
		$this->REQ_LOCAL_EMAIL_WHOIS='/home/lialh4/localwhois/BreachCompilation/query.sh %s';
		$this->REQ_FACEBOOKLEAK='HOME="/var/www/" /home/lialh4/qgrep/vim/qgrep search facebookleak l L1 %s';

	}
	public function searchFaceBookLeak($what){
		$output = null;
		$what = escapeshellarg($what);
		$command = sprintf($this->REQ_FACEBOOKLEAK,$what);
		$command = escapeshellcmd($command);
		//ps xau | grep qgrep|wc -l
		$lmt=null;
		exec('ps xau | grep qgrep|wc -l', $lmt);
		if(intval($lmt[0]) > 3){
			system("pkill -9 qgrep");
			die("F5");	
		}
		$limit = intval(240)/(intval($lmt[0]));
		set_time_limit($limit);
		print($limit);
		//die(".'");
		exec($command,$output);	
		//print($command);
		//system($command);
		foreach($output as &$o){
			$o=str_replace("/var/www/kekulen.ru/boran.i2p/FaceBookLeak2019/","",$o);
			$o=explode(":",$o, 2)[1];
			
		}
		return $output;	
	}
	public function isCorrectIP($ip){
		return filter_var($ip, FILTER_VALIDATE_IP);
	}
	public function isCorrectEmail($email){
		return filter_var($email, FILTER_VALIDATE_EMAIL);
	}
	public function clear_ip($ip){
		if(!$this->isCorrectIP($ip)) die("Not correct IP");
		return escapeshellarg($ip);
	}
	public function whois_email($email){
		$output=null;
		if(!$this->isCorrectEmail($email)) die("Not correct email");
		$email = escapeshellarg($email);
		$command = sprintf($this->REQ_LOCAL_EMAIL_WHOIS, $email);
		$command = escapeshellcmd($command);
		exec($command,$output);
		//print($command." ");
		//print("Output?".sizeof($output));
		return $output;
	}
	public function whois($ip){
		$conn_string = "dbname=networkinfo user=networkinfo password=tmp";
		$dbconn = pg_connect($conn_string);
		$ip = pg_escape_string($ip);
		
		$result = pg_query($dbconn, "SELECT block.inetnum, block.netname, block.country, block.description, block.maintained_by, block.created, block.last_modified, block.source FROM block WHERE block.inetnum >> '{$ip}' ORDER BY block.inetnum DESC;");
		$about_ip=pg_fetch_all($result);
		$result = pg_query($dbconn, "SELECT network, city_name, country_name, country_iso_code, time_zone FROM geoip_blocks INNER JOIN geoip_locations ON geoip_blocks.geoname_id = geoip_locations.geoname_id WHERE network >>= '{$ip}'::inet;");
		$geoip_ip=pg_fetch_all($result);
		pg_close($dbconn);
/*
		$output = null;
		$ip = $this->clear_ip($ip);
		$command = sprintf($this->REQ_LOCAL_WHOIS, $ip);
		$command1 = sprintf($this->REQ_LOCAL_WHOIS_GEO, $ip);
		$command = escapeshellcmd($command);
		$command1 = escapeshellcmd($command1);
		exec($command, $output);
		exec($command1, $output);
*/
		return array("justinfo"=>$about_ip,"geoip"=>$geoip_ip);
		
	}
	public function getAllIPSByCityName($city_name, $offset){
		$output = null;
		$offset = intval($offset);
		$offset = escapeshellarg($offset);
		$command = sprintf($this->REQ_IPS_BY_CITY_NAME, $city_name, $this->MAX_ON_PAGE, $offset);
		$command = escapeshellcmd($command);
		exec($command, $output);
		$output = array_slice($output, 2, (sizeof($output)-4) );
		foreach ( $output as &$o)
 			$o = str_replace(" ", "", $o);
		return $output;
	}
};
?>
