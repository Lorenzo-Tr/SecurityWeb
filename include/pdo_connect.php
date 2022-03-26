<?php
// création des variable des identifiant de connection à la BDD
CONST HOST = "localhost";
CONST USER1 = "securityweb";
CONST USER2 = "securityweb_encrypt";
CONST PASSWORD1 = 'pM|26A5Mi$Q:77zK';
CONST PASSWORD2 = 'm7JD##r+}SXUC<e[';
CONST DBNAME = "security_web";
CONST DBENCRYPTNAME = "security_web_encryption";

// création du pdo de connection à la BDD
function pdo_connect($dbname){
	try {
		switch ($dbname) {
			case DBNAME:
				return new PDO('mysql:host=' . HOST . ';dbname=' . $dbname . ";charset=utf8", USER1, PASSWORD1);
			case DBENCRYPTNAME:
				return new PDO('mysql:host=' . HOST . ';dbname=' . $dbname . ";charset=utf8", USER2, PASSWORD2);
		}
	} catch (Exception $e) {
		die('Erreur : ' . $e->getMessage());
	}
}
