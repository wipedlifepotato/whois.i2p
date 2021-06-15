<?php
//require_once('sam.php');
$CommonPorts = array(
	"ftp" => 21,
	"ssh" => 22,
	"telnet" => 23,
	"domain" => 53,
	"pop3" => 110,
	"netbios-ns" => 137,
	"netbios-dgm" => 138,
	"netbios-ssn" => 139,
	"miscrosoft-ds" => 445,
	"submisison" => 587,
	"imaps" => 993,
	"pops3s" => 995,
	"socks" => 1080,
	"ms-sql-s" => 1433,
	"l2f" => 1701,
	"pptp" => 1723,
	"mysql" => 3306,
	"postgresql" => 5432,
	"httpalt"=>8000,
	"http-proxy"=>8080,
	"xmpp"=>array(522,5269,5280,5281),
	"irc"=>array(6667,6668,6669,7000),
	"RDP"=>3389,
	"IRC"=>194
);

function fsocks4asockopen($proxyHostname, $proxyPort, $targetHostname, $targetPort)
{
    $sock = fsockopen($proxyHostname, $proxyPort);
    if($sock === false)
        return false;

    fwrite($sock, pack("CCnCCCCC", 0x04, 0x01, $targetPort, 0x00, 0x00, 0x00, 0x01, 0x00).$targetHostname.pack("C", 0x00));
    $response = fread($sock, 16);
    $values = unpack("xnull/Cret/nport/Nip", $response); // https://www.w3schools.com/php/func_misc_pack.asp
    if($values["ret"] == 0x5a){
  	  stream_set_timeout($sock, 15);
  	  fwrite($sock, "GET / HTTP/1.0\r\n\r\n");
  	  $retval = fread($sock, 1024);
	  fclose($sock);
          return $retval;
    }else{
	fclose($sock); return false;
    }
}
function check_returns(string $ret,string $ret1,float $needpeerc=85.0){
	if($ret == "" && $ret1 == "") return true;
	$perc=0.0;
	$sim = similar_text($ret,$ret1, $perc);

	if( $perc >= $needpeerc ) return true;
	return false;
}

function getDefRetOfDestination($website,$from=65535,$needpeerc=50.0){
	$ret = fsocks4asockopen("localhost",4447,"$website", $from);
	$ret1 = fsocks4asockopen("localhost",4447,"$website", $from-1);
	if($ret === false || $ret1 === false) return false;
	if( check_returns($ret,$ret1) ) return $ret;
	if(($ret === "" && $ret1 === "") || $from < 65533) return $ret;
	print("Resursive");
	//print($ret."\n");
	//print($ret1."\n");
        return getDefRetOfDestination($website, $from-2);
}

function check_open_ports($website,$db,$clear_all=false){


	global $CommonPorts;
	if($clear_all)
		$db->exec('DELETE FROM openports where b32  LIKE "%";');
	
	$db->exec('CREATE TABLE IF NOT EXISTS openports (b32 STRING, openports STRING);');
	$open_ports="";
	$res1 = $db->query("SELECT COUNT(*) FROM openports WHERE b32='$website'");
	$tmp=$res1->fetchArray();
	var_dump($tmp);
	if($tmp[0]>0) return;
	if($res1 != false && $res1->fetchArray() != false)return false;
	$count_open=0;
	print("Check common ports on $website \n");
	print("Get default return (of def port) \n");
	$def=getDefRetOfDestination($website);
	if($def === false) {
		print("LeaseSet not found, delete it from DB\n");
		$db->query("DELETE FROM websites WHERE b32='$website'");
		return;
	}
        print("Check the destination!\n");
	foreach($CommonPorts as $key=>$port){
		if( is_array($port) ){
			foreach($port as $key1=>$p){//foreach
				$ret = fsocks4asockopen("localhost",4447,"$website", $p);
				if($ret !== false){
					 if( check_returns($def,$ret)  ) {
						print("Port not found, is just def return, continue\n");
						continue;
					 }
					 print("Port $key $p on $website is open!\n");
					 $open_ports.="$p,";
					 $count_open++;

				}
			}//foreach				
		}else{//else
				$ret = fsocks4asockopen("localhost",4447,"$website", $port);
				if($ret !== false){
					if( check_returns($def,$ret) ) {
						print("Port not found, is just def return, continue\n");
						continue;
					 }
				 	print("Port $port on $key $website is open!\n");
			         	$open_ports.="$port,";
				 	$count_open++;

				}//ret
		}//if
	}//foreach
	if($count_open >= sizeof($CommonPorts) ){
		print("Not straight open ports!\n");
		$res = $db->query("INSERT INTO openports values('$website', 'OpenedAll?');");
		return false;
	}
	else if($count_open === 0 ){
		print("only default port, unknown which\n");
		$res = $db->query("INSERT INTO openports values('$website', 'onedef?');");
		return true;
	}
	print("add open_ports $open_ports on $website\n");
	$res = $db->query("INSERT INTO openports values('$website', '$open_ports');");
	return true;
}
