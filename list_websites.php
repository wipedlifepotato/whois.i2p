<?php
	$LIMIT=10;
	$z = isset($_GET['z']) ? intval($_GET['z']) : 1;
	if($z != 1 && $z != 0) $z = 1;
	$offset = isset($_GET['i']) ? intval($_GET['i']) : 0;
	$db = new SQLite3('websites.db');

/*
	$db->exec('CREATE TABLE IF NOT EXISTS websites (b32 STRING, iswebsite tinyint(1));');
	$db->query("INSERT INTO websites VALUES('yxrubht2swhc6bjpt4xmtdal6ecb2vztiwri64x7hwgwwi5afsva.b32.i2p',1);");
	$db->query("INSERT INTO websites VALUES('5byikhvw3nwp7d67voq3nm6b6jlmytu733yqf4wxl5ry3txhjfgq.b32.i2p',1);");
	$db->query("INSERT INTO websites VALUES('kkq7u6i76236qbbpyyixvsci2jbqrmejeg45sus44ifpkhzcdskq.b32.i2p',1);");
*/
	$res = $db->query("SELECT * FROM websites WHERE iswebsite='$z' LIMIT $LIMIT OFFSET $offset");
	$count = $db->query("SELECT COUNT(*) FROM websites WHERE iswebsite='$z'");
	$count = $count->fetchArray()[0];

	$count_all = $db->query("SELECT COUNT(*) FROM websites");
	$count_all = $count_all->fetchArray()[0];


	while($row = $res->fetchArray()) {
		$website = $row[0];
		print("<a href=http://$website>$website</a></br>");
	}
	print("<hr/>");
	$count = intval($count);

	if($offset > 0){
		 $offset-=$LIMIT;
		 print("<a href=?i=$offset&z=$z>back</a>");
	}
	if($count > ($offset+$LIMIT) || ($offset ==0 && $count>$LIMIT) ){
		$offset+=$LIMIT*2;
		print("	<a href=?i=$offset&z=$z>next</a>");
	}
	
	print("<br/>");
	print("<a href='?z=0'>watch to NOT(?) websites</a>|");print("<a href='?z=1'>watch to (?) websites</a>");
	$per=intval($count/($count_all/100.));
	print("<hr/>A: $count_all N: $count P: $per");

?>
