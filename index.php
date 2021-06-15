<?php
//$LOCALGEOIP='/home/lialh4/localwhois/bin/geopip';
///home/lialh4/localwhois/bin/geopip/get_all_about_by_city_name
require_once("whoiser.php");
$whois = new whoiser;
//$ret = $whois->getAllIPSByCityName("Toronto", 0);
//$ret = $whois->whois("109.111.178.181");
//print("done\n");

$ret = array();
$city="";
if ( isset($_GET['IP']) ){
	$IP = $_GET['IP'];
	$ret = $whois->whois($IP);
}else if( isset($_GET['City']) ){
	$city = $_GET['City'];
	$ret = $whois->getAllIPSByCityName($city, 0);
}else if(isset($_GET['email']) ){
	$email = $_GET['email'];
	$ret = $whois->whois_email($email);
}else if(isset($_GET['facebook']) ){
	$what = $_GET['facebook'];
	$ret = $whois->searchFaceBookLeak($what);

}
if( sizeof($ret) > 0 ){
	if( is_array($ret) && isset($_GET['IP']) ){
		$geoip=$ret['geoip'][0];
		$justinfo=$ret['justinfo'][0];
		print("<p>GeoIP:</p>");
		foreach($geoip as $info=>$key){
			print("<b>".$info." = ".$key."</b></br>");
		}
		print("<hr/>");
		print("<p>About network:</p>");
		foreach($justinfo as $info=>$key){
			print("<b>".$info." = ".$key."</b></br>");
		}	
	}
	else
	foreach($ret as $r){
		if( is_array($r) && isset($_GET['IP']) ){
			$first = $r[0];
			foreach($first as $info=>$key){
				if( is_array($key) ){
					foreach($element as $info=>$key){
						print($info." = ".$key."</br>");
					 }
				}
				print($info." = ".$key."</br>");
				
			}
			print("<hr/>");

		}else
			print($r."</br>");
	}
	if($city != "" && isset($_GET['City']) ){
		$offset = isset($_GET['offset']) ? intval($_GET['offset'])+$whois->MAX_ON_PAGE : $whois->MAX_ON_PAGE;
		$offsetb = $offset-($whois->MAX_ON_PAGE*2);
		print("<a href=index.php?City=$city&offset=$offset>next</a>");
		if($offsetb > 0)
			print("|<a href=index.php?City=$city&offset=$offsetb>back</a>");
		if($offset < 0) return header("Location: index.php");
	}
	print("</br><a href='index.php'>Return to first page</a>");
	exit(0);
}
?>

<html>
	<head><title>IP Whois service for clearnet</title><meta charset=utf-8>
	<link rel="icon" href="favicon.ico" type="image/x-icon" />

	</head>
	<body>
		<form action=index.php method=GET>
			IP: <input type="text" name='IP' placeholder='127.0.0.1' /></br>
			<input type=submit value='whois' />
		</form></br>
		<form action=index.php method=GET>
			City: <input type="text" name='City' placeholder='Toronto'/></br>
			<input type=submit value='get_all_ips_by_city' />
		<a href='toronto_project.txt'>toronto_project.txt</a>
		</form></br>
		<form action=index.php method=GET>
			email: <input type="text" name='email' placeholder='pavel@mail.ru'/></br>
			<input type=submit value='check if email smells funny' />
		</form></br>
		<form action=index.php method=GET>
			<i>boran.i2p/FaceBookLeak2019 (not fast search for now. later will be to fixed.)</i></br>
			facebookSearch(30 lines limit. 120/users second for command limit): <input type="text" name='facebook' placeholder='+44'/></br>
			<input type=submit value='start very long search in galactic' />
		</form></br>
		<a href='list_websites.php'>random websites in i2p</a><i>(later will be added check to ssh/rdp/irc/etc) </i>
		<a href='upload_leasesets.php'>Upload own leasesets</a> <b>|</b> <a href=leasesets.pl>Script for add leasets automatic</a> | <a href='websites.db'>Download the db</a></br>
		<a href='netdb.db'>NetDB.db for anyone!</a></br>
		<a href='netdb_searcher.php'>*WillBeLater*</a>
<pre>

TODO: boran.i2p/snowdenarchive
Donate:
    	3GPPQhyhpsFG8SZ3MZdu9HEwbQawdVjHbR - BTC
    	M8ufSAQQP9sWt2DDfAuirWWVMHWsJQCi1G - LTC
    	32hX8GzSS3265WwKZHvP2sG62avRDvMNU6 - LTC
    	rnXyVQzgxZe7TR1EPzTkGj2jxH4LMJYh66 - XRP (Destination tag: 68607)
    	36vQmyu3eBH1J8BaXN42xQEVjVu1pdVaD8 - BCH
    	ANMDb4B1tzXCbwWhRAyHDWpgfZZmTBrGyC - BTG
    	7agfCGLERJYeB1eYj2jU7pig9Yu6UD5sH4 - DASH
    	0x1c19B2bFC4A0f911D489954458768Cb25589735e - ETH
    	0x81e5D45E499c151D683a0fFC51de004FDBCA2631 - USDT
    	EQCVLaGrLCj49bs5ShZlDBoUmVqf1RFSA14OBDnzIpovjJ0L - TON_CRYSTAL
	0x81e5D45E499c151D683a0fFC51de004FDBCA2631 - USDC
Send data:
	postmaster@kekulen.ru

</pre>

	</body>
</html>
