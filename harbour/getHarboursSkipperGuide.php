<?php
	global $db_host, $db_user, $db_pw, $db_name;
	require('ServerConnection.php');
  
	$mysql = mysql_connect($db_host, $db_user, $db_pw);
	$b=mysql_real_escape_string($_REQUEST["b"],$mysql);
	$l=mysql_real_escape_string($_REQUEST["l"],$mysql);
	$t=mysql_real_escape_string($_REQUEST["t"],$mysql);
	$r=mysql_real_escape_string($_REQUEST["r"],$mysql);

	mysql_select_db($db_db, $mysql);

	$recset = mysql_query("SELECT * FROM skipperguide WHERE $l<lon AND lon<$r AND $b<lat AND lat<$t", $mysql);

	while($rec = mysql_fetch_object($recset)) {
	    echo "putAJAXMarker($rec->id, $rec->lon, $rec->lat, '$rec->name', '$rec->descr', -1);\n";
	}
?>
