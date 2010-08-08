<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<html>
    <head>
        <title>Aktualisierung der Tabelle Skipperguide</title>
    </head>
   
<body>
        <FORM name="essai" method="Post" action="OseaMExportCSV.php">
        <br/><br/> <input type="Submit" value="SG Aktuel" name= "ok"  />   
                   <input type="Submit" value="Logfile" name= "nein"  /><br/>
        </FORM>
<?php
    global $serverName, $user, $password, $DatenbankName;
    require('ServerConnection.php');
	
   //Verbindung zur Datenbank auf dem Server			
	function DatabaseConnection($server, $user, $pwd, $Db){ 
		//Verbindung zum Server
		$conn = mysql_connect($server, $user, $pwd)or die("No connection possible");
		mysql_select_db($Db) or die("Error of connection to the database");
		return $conn;
	}

	function ConnectionClose($conn){
		// Verbindung schliessen
		mysql_close($conn);
	}

    function skipperGuideAktuel(){
        global $hafenCpt, $conn, $hafenDiff;       
       
            $fichier = "http://www.skipperguide.de/extension/OSeaMExportCSV.php";
           
        if ($fichier)
            {   
               
                $fp = fopen ("http://www.skipperguide.de/extension/OSeaMExportCSV.php", "r");
            }
            else
                {
                    echo 'URL does not exist';
                    exit();
                }                           
                               
        $cpt=0;   
        while (!feof($fp))
            {
                $ligne = mb_convert_encoding(fgets($fp,4096),"UTF-8","UTF-8");
                $liste = explode(";",$ligne);
                   
                for($i=0; $i<9; $i++)
                {
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
                   
                if ($champs0!='')
                {
                    // z�hl die Anzahl der Zeile
                    $cpt++;
                                       
                    //*****export********
                    $sql= "INSERT INTO export(id, name, descr, lon, lat, pageID) VALUES('$cpt','$champs1','$champs3','$champs4','$champs5', '$champs8') ";
                    $requete = mysql_query($sql, $conn) or die( mysql_error() ) ;
                   
                    //Beseitige die Linie, die den Spaltenamen umfasst    
                    mysql_query("DELETE FROM export WHERE (id='1') ");
                }
               
            }               
           
        // neue H�fen in der Tabelle sg_diff hinzuf�gen
        $hinzugefuegteHafen = "insert into sg_diff(id, name, descr, lon, lat, Flag)".
							  "(select export.id, export.name, export.descr, export.lon, export.lat, "+" from export".
							  " where export.name not in (select ".$sg_Export.".name from ".$sg_Export."))";
							  
        mysql_query( $hinzugefuegteHafen, $conn);
       
        //zahlt die Anzahl der neuen H�fen
        $hafenNeu = mysql_affected_rows();   

        // gel�schte H�fen in der Tabelle sg_diff hinzuf�gen
        // gel�schte H�fen sind H�fen, die aus der Site "http://www.skipperguide.de" gel�scht gewesen sind
        $geloeschteHafen = "insert into sg_diff(id, name, descr, lon, lat, Flag)".
						   "(select ".$sg_Export.".id, ".$sg_Export.".name, ".$sg_Export.".descr, ".$sg_Export.".lon, ".$sg_Export.".lat, "-" from".
						   " ".$sg_Export." where ".$sg_Export.".name not in (select export.name from export))";
						   
        mysql_query( $geloeschteHafen, $conn);

        //zahlt die Anzahl der gel�schten H�fen
        $hafengeloescht = mysql_affected_rows();

        // Tabelle tanta_sg1 leeren   
        mysql_query(" DELETE FROM ".$sg_Export."", $conn);
       
        // aktualisiert die Tabelle tanta_sg1 mit den neuen H�fen Daten
        mysql_query("insert into ".$sg_Export."(lon, lat, name, descr, id)".
					"(select export.lon, export.lat, export.name, export.descr, export.id from export) ", $conn);
       
        // gibt die Anzahl der gesamten H�fen zur�ck
        $hafenCpt = mysql_affected_rows();
       
        // gibt die Anzahl den neuen  und der gel�schten H�fen zur�ck
        $hafenDiff = "+ ".$hafenNeu." / - ". $hafengeloescht;
       
        // Die Tabelle tanta_logfile ist hier als logfile benutzt.
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
                    // Tabelle Anfang
                    echo '<table bgcolor="#FFFFFF" width="600" border="1" align="center" cellpadding="2" cellspacing="0">'."\n";
                        echo '<tr>';
                        echo '<td bgcolor="#669999"><b><u>Datum</u></b></td>';
                        echo '<td bgcolor="#669999"><b><u>Uhrzeit</u></b></td>';
                        echo '<td bgcolor="#669999"><b><u>Skipperguide</u></b></td>';
                        echo '<td bgcolor="#669999"><b><u>Hafen Difference</u></b></td>';
                        echo '</tr>'."\n";
                        // einzeige der Daten aus der Tabelle tanta_logfile
                    while($row = mysql_fetch_array($rueckgabe)) {
                        echo '<tr>';
                        echo '<td bgcolor="#CCCCCC">'.$row["Datum"].'</td>';
                        echo '<td bgcolor="#CCCCCC">'.$row["Uhrzeit"].'</td>';
                        echo '<td bgcolor="#CCCCCC">'.$row["Skipperguide"].'</td>';
                        echo '<td bgcolor="#CCCCCC">'.$row["Difference"].'</td>';
                        echo '</tr>'."\n";
                    }
                    echo '</table>'."\n";
                }
                else echo "Tabelle ".$logfile." ist leer...";                                   
        mysql_free_result($rueckgabe);   
                           
    }
// Programm aufruf

//Verbindung zur Datenbank "openseamap" auf dem Server
$conn = DatabaseConnection($serverName, $user, $password, $DatenbankName);

// zwei temporaire Tabelle
$tableSgExport = "CREATE TEMPORARY TABLE `export` (`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT, PRIMARY KEY (`id`), `name` TEXT NOT NULL, `descr` TEXT, `lon` DOUBLE NOT NULL, `lat` DOUBLE NOT NULL,`pageID` INT(10)) ENGINE=INNODB DEFAULT CHARSET=utf8";
mysql_query($tableSgExport, $conn);
$tableSgDiff = "CREATE TEMPORARY TABLE `sg_diff` (`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT, PRIMARY KEY (`id`), `name` TEXT NOT NULL, `descr` TEXT, `lon` DOUBLE NOT NULL, `lat` DOUBLE NOT NULL,`flag` char(1)) ENGINE=INNODB DEFAULT CHARSET=utf8";
mysql_query($tableSgDiff, $conn);


if (isset($_POST["ok"])){
   
    //ruft die Funktion skipperGuideAktuel() auf
    skipperGuideAktuel();
        echo 'Hafenstand: '. $hafenCpt;
        echo '<br><br>';
        echo 'Hafen Difference: '.$hafenDiff;   
    }else if (isset($_POST["nein"])){
       
    //ruft die Funktion archivierung() auf
        archivierung();
        }

    //Verbindung schliessen
    ConnectionClose($conn); 
?>
</body>   
</html>