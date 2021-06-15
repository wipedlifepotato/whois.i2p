<html>
	<head><title>NetDB searcher</title></head>
<body>
<?php
	$LIMIT=50;

	$offset = isset($_GET['i']) ? intval($_GET['i']) : 0;
	$db = new SQLite3('netdb.db');


	$count_all = $db->query("SELECT COUNT(*) FROM hosts");
	$count_all = $count_all->fetchArray()[0];
	print("<p>Known netdb hosts: $count_all</p>");
/*
$db->exec('CREATE TABLE IF NOT EXISTS hosts (ip STRING, country_iso_code STRING, city_name STRING, netname STRING);');

Добролюбова 10/2
*/
if(isset($_GET['data']) && isset($_GET['searchtype']) ){

	$data=$_GET['data'];
	$type=$_GET['searchtype'];
	$data = SQLite3::escapeString($data);
	//print($data);
	$req = 'SELECT * FROM hosts where ';
	$t="";
	if($type == "IP"){
		$t="ip LIKE '%$data%'";
	}else if($type == "city_name"){
		$t="city_name LIKE '%$data%'";
	}else if($type == "country_iso_code"){
		$t="country_iso_code LIKE '%$data%'";
	}else if($type == "netname"){
		$t="netname LIKE '%$data%'";
	}else{
		die("Unknown type of search");
	}

	
	//$count_req = str_replace("SELECT COUNT(*) FROM", "SELECT * FROM", $req);
	$count_req="SELECT COUNT(*) FROM hosts where $t;";
	$req.=$t;
	$req.= "LIMIT $LIMIT OFFSET $offset";
	$count = $db->query($count_req);
	$count = $count->fetchArray()[0];
	$count = intval($count);


	$ret = $db->query($req);
	while($row = $ret->fetchArray()) {
		var_dump($row);
		print("<hr/>");
	}

	if($offset > 0){
		 $offset-=$LIMIT;
		 print("<a href=?i=$offset&data=$data&searchtype=$type>back</a>");
	}
	if($count > ($offset+$LIMIT) || ($offset ==0 && $count>$LIMIT) ){
		$offset+=$LIMIT*2;
		print("	<a href=?i=$offset&data=$data&searchtype=$type>next</a>");
	}print("|Count: $count");
	
}
/*
	$res = $db->query("SELECT * FROM hosts WHERE iswebsite='$z' LIMIT $LIMIT OFFSET $offset");
	$count = $db->query("SELECT COUNT(*) FROM websites WHERE iswebsite='$z'");
	$count = $count->fetchArray()[0];




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
			$req = "INSERT INTO hosts values('$ip', '$country_iso_code','$city_name','$netname');";

*/
?>
<p></p>
<form action=netdb_searcher.php method=GET />
	search:<input type=textarea name=data placeholder=Moscow /><input type=submit value="start search" /></br>
	<input type="radio" id="searchType1"
     	name="searchtype" value="IP" />
    	<label for="searchType1">IP</label>
	<input type="radio" id="searchType2"
     	name="searchtype" checked value="city_name" />
    	<label for="searchType2">City Name(Moscow)</label>
	<input type="radio" id="searchType3"
     	name="searchtype" value="country_iso_code" />
    	<label for="searchType3">contry iso code (RU)</label>
	<input type="radio" id="searchType4"
     	name="searchtype" value="netname" />
    	<label for="searchType4">netname</label>
</form>
</body>
</html>
