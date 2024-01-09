<?php
	$maxSize=$_REQUEST["maxSize"];

	global $db_host, $db_user, $db_pw, $db_name;
	require('ServerConnection.php');
  
	$mysql = mysql_connect($db_host, $db_user, $db_pw);
	$b=mysql_real_escape_string($_REQUEST["b"],$mysql);
	$l=mysql_real_escape_string($_REQUEST["l"],$mysql);
	$t=mysql_real_escape_string($_REQUEST["t"],$mysql);
	$r=mysql_real_escape_string($_REQUEST["r"],$mysql);

	mysql_select_db($db_db, $mysql);

	if($maxSize>=5){
	  $recset = mysql_query("SELECT *,RAND() as rand FROM skipperguide WHERE $l<lon AND lon<$r AND $b<lat AND lat<$t ORDER BY rand LIMIT 100", $mysql);

	  while($rec = mysql_fetch_object($recset)) {
	      echo "putHarbourMarker($rec->id, $rec->lon, $rec->lat, '".addslashes($rec->name)."', '$rec->descr', -1);\n";
	  }
	}

	$recset = mysql_query("SELECT *,RAND() as rand FROM WPI WHERE $l<lon AND lon<$r AND $b<lat AND lat<$t AND size<>\"\" ORDER BY size, rand LIMIT 100", $mysql);

	while($rec = mysql_fetch_object($recset)) {
	    echo "putHarbourMarker($rec->World_Port_Index, $rec->lon, $rec->lat, '".addslashes($rec->Main_port)."', '', $rec->size);\n";
	}
?>
