<?php

	global $db_host, $db_user, $db_pw, $db_name;
	require('ServerConnection.php');

	$mysql = new mysqli($db_host, $db_user, $db_pw, $db_db);

	$b=$mysql->real_escape_string($_REQUEST["b"] ?? 48.88692);
	$l=$mysql->real_escape_string($_REQUEST["l"] ?? -0.89535);
	$t=$mysql->real_escape_string($_REQUEST["t"] ?? 50.93125);
	$r=$mysql->real_escape_string($_REQUEST["r"] ?? 2.92239);
	$maxSize=$_REQUEST["maxSize"] ?? 3;

	if($maxSize>=5){
	  $recset = $mysql->query("SELECT *,RAND() as rand FROM skipperguide WHERE $l<lon AND lon<$r AND $b<lat AND lat<$t ORDER BY rand LIMIT 100");

	  while($rec = $recset->fetch_object()) {
	      echo "putHarbourMarker($rec->id, $rec->lon, $rec->lat, '".addslashes($rec->name)."', '$rec->descr', -1);\n";
	  }
	  $recset->close();
	}

	$recset = $mysql->query("SELECT *,RAND() as rand FROM wpi WHERE $l<lon AND lon<$r AND $b<lat AND lat<$t AND size<>\"\" ORDER BY size, rand LIMIT 100");

	while($rec = $recset->fetch_object()) {
	    echo "putHarbourMarker($rec->World_Port_Index, $rec->lon, $rec->lat, '".addslashes($rec->Main_port)."', '', $rec->size);\n";
	}
	$recset->close();
	$mysql->close();
?>
