<?php

/*
 *******************************************************************************
 * ServerConnection.php
 *******************************************************************************
 * ServerConnection ist eine externe datei.
 *
 * Sie enthält die benötige Informationen für das Aufbauen einer Verbindung
 *
 * serverName --> Der Server auf dem man zugreifen moechte
 *
 * user --> Benutzername
 *
 * password --> zugrifft rechte
 *
 * DatenbankName --> Die Datenbank auf der man zugreifen moechte
 *
 * Um die Tabelle skipperguide zu aktualizieren, braucht man 2 Tabellen
 *
 * 		Eine in der die aktuellste Daten gespeichert werden sein : sg_Export
 *
 *		eine zweite, die für die Logfile benutzt wird : logfile
 *
 *******************************************************************************
*/
	
global $serverName, $user, $password, $DatenbankName, $sg_Export, $logfile;

//Der Server auf dem man zugreifen moechte
$serverName = 'serverName';

//Benutzername
$user = 'user';

//zugrifft rechte auf der DB Server
$password = 'password';

//Die Datenbank auf der man zugreifen moechte
$DatenbankName = 'databasename';

// Exportierte Daten aus Skipperguide
$sg_Export = 'tabelname';

// logfile Tabelle
$logfile = 'logfilename';

?>


