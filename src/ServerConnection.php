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
	
global $db_host, $db_user, $db_pw, $db_db, $sg_Export, $logfile;

//Der Server auf dem man zugreifen moechte
//$db_host = 'db.vm.smurf.noris.de';
$db_host = getenv('db_host');
//Benutzername
$db_user = getenv('db_user');
//zugrifft rechte auf der DB Server
$db_pw = getenv('db_pw');
//Die Datenbank auf der man zugreifen moechte
$db_db = getenv('db_db');

// Exportierte Daten aus Skipperguide
$sg_Export = getenv('sg_export');

// logfile Tabelle
$logfile = getenv('logfile');

?>


