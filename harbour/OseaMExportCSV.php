<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
       "http://www.w3.org/TR/html4/loose.dtd">

<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<title>Aktualisierung der Tabelle Skipperguide</title>
	</head>
   
	<body>
		<form name="essai" method="Post" action="OseaMExportCSV.php">
			<br><br> 
			<input type="Submit" value="SG Aktuel" name= "ok">   
			<input type="Submit" value="Logfile" name= "nein"><br>
		</form>
		<?php
			global $serverName, $user, $password, $DatenbankName;
			require('ServerConnection.php');

			function DatabaseConnection($server, $user, $pwd, $Db){ 
				$conn = mysql_connect($server, $user, $pwd)or die("No connection possible");
				mysql_select_db($Db) or die("Error of connection to the database");
				return $conn;
			}

			function ConnectionClose($conn){
				mysql_close($conn);
			}

			function skipperGuideAktuel(){
				global $hafenCpt, $conn, $hafenDiff;       
		
				$fichier = "http://www.skipperguide.de/extension/OSeaMExportCSV.php";
				if ($fichier) {   
					$fp = fopen ("http://www.skipperguide.de/extension/OSeaMExportCSV.php", "r");
				} else {
					echo 'URL does not exist';
					exit();
				}                           
								
				$cpt=0;   
				while (!feof($fp)) {
					$ligne = mb_convert_encoding(fgets($fp,4096),"UTF-8","UTF-8");
					$liste = explode(";",$ligne);
					for($i=0; $i<9; $i++) {
						$liste[$i] = ( isset($liste[$i]) ) ? $liste[$i] : Null;
						$liste[$i]=str_replace('"','',$liste[$i]);             
					}                       
					$champs0 = mb_convert_encoding($liste[0],"UTF-8","UTF-8"); // id
					$champs1 = mb_convert_encoding($liste[1],"UTF-8","UTF-8"); // Name
					$champs2 = mb_convert_encoding($liste[2],"UTF-8","UTF-8"); // Descr
					$champs3 = mb_convert_encoding($liste[3],"UTF-8","UTF-8"); // Link
					$champs4 = mb_convert_encoding($liste[4],"UTF-8","UTF-8"); // lon
					$champs5 = mb_convert_encoding($liste[5],"UTF-8","UTF-8"); // lat
					$champs6 = mb_convert_encoding($liste[6],"UTF-8","UTF-8"); // coord
					$champs7 = mb_convert_encoding($liste[7],"UTF-8","UTF-8"); // type
					$champs8 = mb_convert_encoding($liste[8],"UTF-8","UTF-8"); // LID
					
					if ($champs0!='') {
						// get row count
						$cpt++;

						//*****export********
						$sql= "INSERT INTO export(id, name, descr, lon, lat, pageID) VALUES('$cpt','$champs1','$champs3','$champs4','$champs5', '$champs8') ";
						$requete = mysql_query($sql, $conn) or die( mysql_error() ) ;

						//delete column names    
						mysql_query("DELETE FROM export WHERE (id='1') ");
					}
				}               
			
				// add new harbours to sg_diff tabel
				$hinzugefuegteHafen = "insert into sg_diff(id, name, descr, lon, lat, Flag)".
					"(select export.id, export.name, export.descr, export.lon, export.lat, "+" from export".
					" where export.name not in (select ".$sg_Export.".name from ".$sg_Export."))";
	
				mysql_query( $hinzugefuegteHafen, $conn);
		
				// get the count of new harbours
				$hafenNeu = mysql_affected_rows();   

				// add deleted harbours to sg_diff tabel
				$geloeschteHafen = "insert into sg_diff(id, name, descr, lon, lat, Flag)".
				"(select ".$sg_Export.".id, ".$sg_Export.".name, ".$sg_Export.".descr, ".$sg_Export.".lon, ".$sg_Export.".lat, "-" from".
				" ".$sg_Export." where ".$sg_Export.".name not in (select export.name from export))";
	
				mysql_query( $geloeschteHafen, $conn);

				// get the count of deleted harbours
				$hafengeloescht = mysql_affected_rows();

				// clear export tabel
				mysql_query(" DELETE FROM ".$sg_Export."", $conn);

				// write changed items into export tabel
				mysql_query("insert into ".$sg_Export."(lon, lat, name, descr, id)".
					"(select export.lon, export.lat, export.name, export.descr, export.id from export) ", $conn);

				// returns the count of all harbours
				$hafenCpt = mysql_affected_rows();

				// returns the count of changed harbours
				$hafenDiff = "+ ".$hafenNeu." / - ". $hafengeloescht;
		
				// create logfile.
				$monate = array("", "Januar", "Februar", "Maerz", "April", "Mai", "Juni", "Juli", "August", "September", "Oktober", "November", "Dezember");
				$tage = array("Sonntag", "Montag", "Dienstag", "Mittwoch", "Donnerstag", "Freitag", "Samstag");
				$tag = $tage[date("w")];
				$monat = $monate[date("n")];
				$uhrzeit = date("H:i:s");
				$datum = $tag." ".date("d")." ".$monat." ".date("Y");       
				$sqlArchiv = "insert into tanta_logfile(Datum, Uhrzeit, Skipperguide, Difference) values ('$datum','$uhrzeit','$hafenCpt','$hafenDiff')";
				mysql_query($sqlArchiv, $conn);
			}   
	
			//logfile
			function archivierung()    {
				global $hafenCpt, $hafenDiff, $conn;

				$rueckgabe = mysql_query("select * from ".$logfile, $conn);
				$i = mysql_num_rows($rueckgabe);

				if($i) {
					echo '<table bgcolor="#FFFFFF" width="600" border="1" align="center" cellpadding="2" cellspacing="0">'."\n";
						echo '<tr>';
						echo '<td bgcolor="#669999"><b><u>Datum</u></b></td>';
						echo '<td bgcolor="#669999"><b><u>Uhrzeit</u></b></td>';
						echo '<td bgcolor="#669999"><b><u>Skipperguide</u></b></td>';
						echo '<td bgcolor="#669999"><b><u>Hafen Difference</u></b></td>';
						echo '</tr>'."\n";
					while($row = mysql_fetch_array($rueckgabe)) {
						echo '<tr>';
						echo '<td bgcolor="#CCCCCC">'.$row["Datum"].'</td>';
						echo '<td bgcolor="#CCCCCC">'.$row["Uhrzeit"].'</td>';
						echo '<td bgcolor="#CCCCCC">'.$row["Skipperguide"].'</td>';
						echo '<td bgcolor="#CCCCCC">'.$row["Difference"].'</td>';
						echo '</tr>'."\n";
					}
					echo '</table>'."\n";
				} else {
					echo "Tabelle ".$logfile." ist leer...";
				}
				mysql_free_result($rueckgabe);   
			}

			// connect to database
			$conn = DatabaseConnection($serverName, $user, $password, $DatenbankName);

			// create 2 temporary tabels
			$tableSgExport = "CREATE TEMPORARY TABLE `export` (`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT, PRIMARY KEY (`id`), `name` TEXT NOT NULL, `descr` TEXT, `lon` DOUBLE NOT NULL, `lat` DOUBLE NOT NULL,`pageID` INT(10)) ENGINE=INNODB DEFAULT CHARSET=utf8";
			mysql_query($tableSgExport, $conn);
			$tableSgDiff = "CREATE TEMPORARY TABLE `sg_diff` (`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT, PRIMARY KEY (`id`), `name` TEXT NOT NULL, `descr` TEXT, `lon` DOUBLE NOT NULL, `lat` DOUBLE NOT NULL,`flag` char(1)) ENGINE=INNODB DEFAULT CHARSET=utf8";
			mysql_query($tableSgDiff, $conn);

			if (isset($_POST["ok"])){
				skipperGuideAktuel();
				echo 'Hafenstand: '. $hafenCpt;
				echo '<br><br>';
				echo 'Hafen Difference: '.$hafenDiff;
			} else if (isset($_POST["nein"])) {
				archivierung();
			}

			// close database connection
			ConnectionClose($conn); 
		?>
	</body>   
</html>