<?php

	function databaseconnect($host,$user,$pwd,$database,$charset='utf8',$buffered=true,$errorhandling='none'){
	
		/*	Upprätta en förbindelse med databasen genom att ange var ifrån anropet sker, vem som anropar och lösenord */
		$databaseid = "mysql:host=$host;dbname=$database;charset=$charset";
		$conn = new PDO($databaseid, $user, $pwd);
		
		// Ska frågorna buffras i minnet innan hanteringen påbörjas?
		if ($buffered) { $conn->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, 1);}
		
		// Ska det vara någon form av felhantering kopplad mot databasen?
		switch ($errorhandling) {
		
			case "warning"	: $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING); break;
			case "exception": $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); break;
			
		}
	
		return $conn;
	
	}
	

?>