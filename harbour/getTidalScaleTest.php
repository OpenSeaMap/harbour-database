<?php
	global $db_host, $db_user, $db_pw, $db_name;
	require('ServerConnection.php');

	$lat = "Hochwert_Dez";
	$lon = "Rechtswert_Dez";

	$mysql = mysql_connect($db_host, $db_user, $db_pw);
	$b=mysql_real_escape_string($_REQUEST["b"],$mysql);
	$l=mysql_real_escape_string($_REQUEST["l"],$mysql);
	$t=mysql_real_escape_string($_REQUEST["t"],$mysql);
	$r=mysql_real_escape_string($_REQUEST["r"],$mysql);

	mysql_select_db($db_db, $mysql);

	$recset = mysql_query("SELECT *,RAND() as rand FROM Wasserstaende WHERE $l<$lon AND $lon<$r AND $b<$lat AND $lat<$t ORDER BY rand LIMIT 100", $mysql);

	while($rec = mysql_fetch_object($recset)) {
	      echo "putTidalScaleMarker($rec->ID, $rec->Rechtswert_Dez, $rec->Hochwert_Dez, '".addslashes($rec->Name)."', '$rec->PnP');\n";
	}
	//echo "putTidalScaleMarker();\n";
?>
