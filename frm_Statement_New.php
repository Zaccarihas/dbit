<?php

    require_once 'resources/fnc_database.php';
    require_once 'resources/fnc_general.php';   // Uses Timestamp-function
    
    $db = databaseconnect("lofqvist.dynu.net","dev","Av4rak1n","dbit");


    // Handle submitted form

    if(isset($_POST['btnSubmit'])){
    
    
        $qrystr  = "INSERT INTO tbl_statements (Date,Account,Amount,Note) ";
        $qrystr .= "VALUES(?,?,?,?)";
    
        $qry = $db->prepare($qrystr);
        $qry->execute(array($_POST['fldDate'],$_POST['fldAccount'],$_POST['fldAmount'],$_POST['fldNote']));
    
    }




?>

<!DOCTYPE unspecified PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
		<TITLE>Inmatningsformulär - Account</TITLE>
	
	</head>
	<BODY>

		<FORM Method="post">
		
			<DIV>New</DIV>
			
			<!-- Date field -->
			<DIV><DIV>Datum</DIV><DIV>
			
			    <?php 
			    
			        echo "<INPUT Id='fldDate' Name='fldDate' Size='19' Type='text' Value='".timestamp()."'/>";
			    
			    ?>			    			    
			    
			</DIV></DIV>			
			
			<!-- Account field -->
			<DIV><DIV>Konto</DIV><DIV>
			
			    <SELECT Id="fldAccount" Name="fldAccount" Size="5">
			    
			    	<?php 
			    	
			    	    // Fetch all accounts ordered by BDC
			    	    
			    	    $qrystr  = "SELECT AccountID,AccBDC,AccName FROM tbl_Accounts ORDER BY AccBDC";
			    	    
			    	    $qry = $db->prepare($qrystr);
			    	    $qry->execute();
			    	    
			    	    $result = $qry->fetchAll(PDO::FETCH_ASSOC);
			    	    
			    	   // List each account as an option with the AccountID as value and BDC - AccountName as text
			    	    
			    	    foreach($result as $acc){
			    	        
			    	        echo "<OPTION Id='fldAccount' Value='".$add['AccountID']."'>".$acc['AccBDC']." - ".$acc['AccName']."</OPTION>";
			    	    }
			    	
					
					?>
					
				</SELECT>
			
			</DIV></DIV>
			
			<!-- Amount field -->
			<DIV><DIV>Belopp</DIV><DIV>
			
			    <INPUT Id="fldAmount" Name="fldAmount" Size="20" Type="text"/>
			    
			</DIV></DIV>
			
			
 			<!--  Note field -->
 			<DIV><DIV>Notering</DIV><DIV>
 			
 				<TEXTAREA Id="fldNote" Name="fldNote" rows="3" cols="80" style="resize: none"></TEXTAREA>
 				
 			</DIV></DIV>
			
			
			<!--  Submit button -->
			<DIV><button Name="btnSubmit" Id="btnSubmit">Submit</button></DIV>
			
		</FORM>

    </BODY>
</HTML>

<?php 

    $db = null;

?>

