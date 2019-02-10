<?php

    require_once 'resources/fnc_database.php';
    
    
    // Connect to the database
    //$db = databaseconnect('lofqvist.dynu.net','dev','Av4rak1n','dbit');
    $db = databaseconnect('localhost','dev','Av4rak1n','dbit');
    
?>

<!DOCTYPE unspecified PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
		<TITLE>Transaktionslista</TITLE>
		
		<link href="resources/stylesheets/dbit_transactionlist.css" rel="stylesheet" type="text/css">
	
	</head>
	<body>
	
		<H1>Transaktionslista</H1>
		
<?php 
		
		// Fetch all transactions from the database
		$qrystr  = "SELECT * FROM tbl_transactions ORDER BY TrnDate DESC";
		
		$qry = $db->prepare($qrystr);
		$qry->execute();
		    
		$result = $qry->fetchAll(PDO::FETCH_ASSOC);
		
		foreach($result as $trn){
			echo "<DIV Class='lstrow'>";
		    echo "<DIV Class='lstcolDate'>".$trn['TrnDate']."</DIV>";
		    echo "<DIV Class='lstcolCPart'>".$trn['TrnCPart']."</DIV>";
		    echo "<DIV Class='lstcolDesc'>".$trn['TrnDesc']." [".$trn['TrnID']."]</DIV>";
		    echo "<DIV Class='lstcolNote'><I>".$trn['TrnNote']."</I></DIV>";
		    echo "</DIV>";
		}
		
?>
		
	</body>
</html>

<?php 

    //Disconnect from the database
    $db = null; 
    
?>


		