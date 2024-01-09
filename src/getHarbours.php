<?php
	$maxSize=$_REQUEST["maxSize"];

	global $db_host, $db_user, $db_pw, $db_name;
	require('ServerConnection.php');
  
	$mysql = new mysqli($db_host, $db_user, $db_pw, $db_db);
	if (array_key_exists("b",$_REQUEST)) {
		$b=$mysql->real_escape_string($_REQUEST["b"]);
		$l=$mysql->real_escape_string($_REQUEST["l"]);
		$t=$mysql->real_escape_string($_REQUEST["t"]);
		$r=$mysql->real_escape_string($_REQUEST["r"]);
	} else {
		$b="48.88692";
		$l="-0.89535";
		$t="50.93125";
		$r="=2.92239";
		$maxsize="3";
	}

	if($maxSize>=5){
	  $recset = $mysql->query("SELECT *,RAND() as rand FROM skipperguide WHERE $l<lon AND lon<$r AND $b<lat AND lat<$t ORDER BY rand LIMIT 100");

	  while($rec = $recset->fetch_object()) {
	      echo "putHarbourMarker($rec->id, $rec->lon, $rec->lat, '".addslashes($rec->name)."', '$rec->descr', -1);\n";
	  }
	  $recset->close();
	}

	$recset = $mysql->query("SELECT *,RAND() as rand FROM WPI WHERE $l<lon AND lon<$r AND $b<lat AND lat<$t AND size<>\"\" ORDER BY size, rand LIMIT 100");

	while($rec = $recset->fetch_object()) {
	    echo "putHarbourMarker($rec->World_Port_Index, $rec->lon, $rec->lat, '".addslashes($rec->Main_port)."', '', $rec->size);\n";
	}
	$recset->close();
	$mysql->close();
?>
