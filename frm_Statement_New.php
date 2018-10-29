<?php

    require_once 'resources/fnc_database.php';
    require_once 'resources/fnc_general.php';   // Uses Timestamp-function
    
    $db = databaseconnect("lofqvist.dynu.net","dev","Av4rak1n","dbit");


    // Handle submitted form

    
    if(isset($_POST['btnSubmit'])){
    
    
        $qrystr  = "INSERT INTO tbl_statements_tst (Date,Account,Amount,Note) ";
        $qrystr .= "VALUES(?,?,?,?)";
    
        $qry = $db->prepare($qrystr);
        $qry->execute(array($_POST['fldDate'],$_POST['fldAccount'],$_POST['fldAmount'],$_POST['fldNote']));
    
    }
    




?>

<!DOCTYPE unspecified PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
		<TITLE>Inmatningsformulär - Account</TITLE>
		
		<link href="resources/stylesheets/dbit_statements.css" rel="stylesheet" type="text/css">
	
	</head>
	<BODY Class="dbitpage">

		<FORM Id="frmNewStat" Method="post">
		
			<DIV>New</DIV>
			
			<!-- Account field -->
			<DIV Id="fldblkAccount">
			
				<LABEL Id="lblAccount" For="fldAccount">Konto</LABEL>
				<SELECT Id="fldAccount" Name="fldAccount" Size="3">
			    
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
			
			</DIV>
			
			<!-- The center panel of fields -->   
			<DIV Id="pnlCenter"> 
			
			    <!-- Date field -->
			    <DIV Id="fldblkDate">
			    
			         <LABEL Id="lblDate" For="fldDate">Datum</LABEL>
			         <?php echo "<INPUT Id='fldDate' Name='fldDate' Size='19' Type='text' Value='".timestamp()."'/>"; ?>			    			    
			    
			    </DIV>			
				
			    <!-- Amount field -->
			    <DIV Id="fldblkAmount">
			
				    <LABEL Id="lblAmount" For="fldAmount">Belopp</LABEL>
			        <INPUT Id="fldAmount" Name="fldAmount" Size="19" Type="text"/>
			    
			    </DIV>
			    
			</DIV> <!-- End of panel -->
			    
			    
			<!--  Note field -->
 			<DIV Id="fldblkNote">
 			
 			    <LABEL Id="lblNote" For="fldNote">Notering</LABEL>
 				<TEXTAREA Id="fldNote" Name="fldNote" rows="2" cols="30" style="resize: none"></TEXTAREA>
 				
 			</DIV>
 
			<!--  Submit button -->
			<DIV><button Name="btnSubmit" Id="btnSubmit">Submit</button></DIV>
			
		</FORM>

    </BODY>
</HTML>

<?php 

    $db = null;

?>

