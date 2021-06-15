<?php
	if (php_sapi_name() !== 'cli')die("run though cli");

	require_once('portsfunc.php');
	$LIMIT=10;
	//$z = isset($_GET['z']) ? intval($_GET['z']) : 1;
	//if($z != 1 && $z != 0) $z = 1;
	//$offset = isset($_GET['i']) ? intval($_GET['i']) : 0;
//	$db = new SQLite3('websites.db');
	$db = new SQLite3('/var/www/html/boran.i2p/lwhois/websites.db');
	$db->exec('CREATE TABLE IF NOT EXISTS openports (b32 STRING, openports STRING);');


$offset = 0;
$z = 0;
$count = $db->query("SELECT COUNT(*) FROM websites WHERE iswebsite='$z'");
$count = $count->fetchArray()[0];
$count_all = $db->query("SELECT COUNT(*) FROM websites");
$count_all = $count_all->fetchArray()[0];

while($offset < $count){
	$res = $db->query("SELECT * FROM websites WHERE iswebsite='$z' LIMIT $LIMIT OFFSET $offset");
	while($row = $res->fetchArray()) {
		$website = $row[0];
		check_open_ports($website, $db);
		
	}//while
	//print("<hr/>\n");
	$offset+=$LIMIT*2;
	//die("Done\n");

}
?>
