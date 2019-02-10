<?php
    
    require_once 'resources/fnc_database.php';
    
    // Connect to the database
    //$db = databaseconnect('lofqvist.dynu.net','dev','Av4rak1n','dbit');
    $db = databaseconnect('localhost','dev','Av4rak1n','dbit');
    
?>

<!DOCTYPE unspecified PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
		<TITLE>Avstämningslistan</TITLE>
		
		<link href="resources/stylesheets/dbit_statementlist.css" rel="stylesheet" type="text/css">
	
	</head>
	<body>
	
		<H1>Avstämningslistan</H1>
		
		
		<?php 
		    
		    // Fetch all statements from the database
		    $qrystr  = "SELECT acc.AccBDC,acc.AccName,stat.Date,stat.Amount,stat.Note ";
		    $qrystr .= "FROM tbl_statements AS stat LEFT JOIN tbl_accounts AS acc ON stat.Account = acc.AccountID ";
		    $qrystr .= "ORDER BY acc.AccBDC ASC,stat.Date DESC";
		    
		    $qry = $db->prepare($qrystr);
		    $qry->execute();
		    
		    $result = $qry->fetchAll(PDO::FETCH_ASSOC);
		
		    // Present each statement 
		    
		    $bdc = '';
		    foreach($result as $stat){
		        if($stat['AccBDC']!=$bdc){
		            $bdc = $stat['AccBDC']; echo "<HR/><H2>".$bdc." - ".$stat['AccName']."</H2>";
		        }
		        echo "<DIV Class='lstrow'>";
		        echo "<DIV Class='lstcolDate'>".$stat['Date']."</DIV>";
		        echo "<DIV Class='lstcolAmount'>".$stat['Amount']."</DIV>";
		        echo "<DIV Class='lstcolNote'>".$stat['Note']."</DIV>";
		        echo "</DIV>";
		    }
		
		
		
		?>
	
	
	
	</body>
</html>

<?php 

    // Disconnect the database
    $db=null;

?>
    