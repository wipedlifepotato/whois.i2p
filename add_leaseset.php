:<?php
ini_set('display_errors', '1');

ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
require_once('portsfunc.php');

function request($url,$proxy="127.0.0.1:4444"){
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL,$url);
	curl_setopt($ch, CURLOPT_PROXY, $proxy);

	//curl_setopt($ch, CURLOPT_PROXYUSERPWD, $proxyauth);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HEADER, 1);
	curl_setopt($ch, CURLOPT_NOBODY, true);    // we don't need body
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 45);
	curl_setopt($ch, CURLOPT_TIMEOUT, 120);
	$curl_scraped_page = curl_exec($ch);
	$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);
	return array("html"=>$curl_scraped_page, "code"=>$httpcode);
}



//while(true){
$doc = new DOMDocument();
$doc->loadHTMLFile('http://127.0.0.1:7070/?page=leasesets');

$db = new SQLite3('/var/www/html/boran.i2p/lwhois/websites.db');
$db->exec('CREATE TABLE IF NOT EXISTS websites (b32 STRING, iswebsite tinyint(1));');
$res = $db->query("SELECT COUNT(*) FROM websites WHERE iswebsite='0'");
$count = $res->fetchArray()[0];
//if($count > 5000){
//	$res = $db->query("DELETE FROM websites WHERE iswebsite='0'");
//}

$leasesets = explode("\n",$_GET['l']);
foreach( $leasesets  as $item){
    $item = trim($item);
    //$href =  $item->getAttribute('for');
    //var_dump($href);
    //print($item['textContent']);
    $likeToWebsite=false;
    $sitem = strlen($item);
    $link ="";
    if( $sitem != 52 && $sitem != 60) print("'$item'($sitem) It is not leaseset <s style=display:none>idiot</s>\n<br/>");
    if($sitem != 60)
    	$link = $item.".b32.i2p";
    else $link = $item;
    //die($link);
    $res = $db->query("SELECT * FROM websites WHERE b32='$link'");
    if ($row = $res->fetchArray()) {
	if($row['iswebsite']){
    		print("Website exists already: ");
        	print("<a href=http://$link>$link</a><br/>\n");
	}else{
		print("Is not website(exists already): ");
		print("<a href=http://$link>$link</a><br/>\n");
	}
	continue;
    }
	
    $r=request("$link");
    if($r['code'] != 500 && ($r['html'] !== null && $r['html'] !== FALSE && $r['html'] !== '') ) $likeToWebsite = true;
    $is=$likeToWebsite?"Like to website!":"is not website";
    if($likeToWebsite){
		$db->exec("INSERT INTO websites(b32,iswebsite) VALUES('$link',1);");
    }else{
		$db->exec("INSERT INTO websites(b32,iswebsite) VALUES('$link',0);");
    }
    //check_open_ports($link, $db);
    print("Found now $link $is\n<br/>");
    
    //die("");
}
//	sleep(30);
//}
?>
