<?php 

require_once 'resources/fnc_database.php';
require_once 'resources/fnc_general.php';

$db = databaseconnect("lofqvist.dynu.net","dev","Av4rak1n","dbit");

// Handle submitted form

if(isset($_POST['btnAdd'])){
    
    // Create temporary table
    
    $qrystr  = "CREATE TABLE IF NOT EXISTS tmp_ents (";
    $qrystr .= "EntID BIGINT UNSIGNED AUTO_INCREMENT,";
    $qrystr .= "EntDesc VARCHAR(64),";                            
    $qrystr .= "EntAmount DECIMAL(10,2),";
    $qrystr .= "EntAcc BIGINT UNSIGNED,";
    $qrystr .= "EntQty FLOAT,";
    $qrystr .= "EntUnit ENUM('st','frp','l','kg','par','tim'),";
    $qrystr .= "EntObj BIGINT UNSIGNED,";
    $qrystr .= "PRIMARY KEY (ID))";
    
    $qry = $db->prepare($qrystr);
    $qry->execute();

    // Här ska jag fortsätta att lägga in uppdatering av tabellen men hur gör jag en temporär tabell.
    // Det kommer ju bli problem om jag bara gör en egen temporärtabell ifall flera användare är inne samtidigt
    
    // Put data into the temporary table
    
    $qrystr  = "INSERT INTO tmp_ents (EntDesc,EntAmount,EntAcc,EntQty,EntUnit,EntObj) ";
    $qrystr .= "VALUES(?,?,?,?,?,?)";
    
    $qry = $db->prepare($qrystr);
    $qry->execute(array($_POST['fldEntDesc'],$_POST['fldEntAmount'],$_POST['fldEntAcc'],$_POST['fldEntQty'],$_POST['fldEntUnit'],$_POST['fldEntObj']));
    
    // Måste jag nollställa $_POST så jag inte hamnar här automatiskt nästa uppdatering av sidan
    
}

if(isset($_POST['btnSubmit'])){
    
    
    // Is there a temporary registered transaction
    
    $qry = $db->prepare("SHOW TABLES LIKE '%tmp_trn%'");
    $qry->execute();
    $res = $qry->fetch(PDO::FETCH_NUM);
    
    if(isset($res[0])){ 
        
        // Transfer the transaction info from the temporary table to the permanent table
        
        $qrystr  = "INSERT INTO fixed_trans(TrnDesc, TrnDate, TrnCPart, TrnNote) ";
        $qrystr .= "SELECT TrnDesc,TrnDate,TrnCPart,TrnNote FROM tmp_trn";
        $qry = $db->prepare($qrystr);
        $qry->execute();
        
        $qrystr  = "SELECT LAST_INSERT_ID()";
        $qry = $db->prepare($qrystr);
        $qry->execute();
                
        $newTrnID = $qry->fetch(PDO::FETCH_COLUMN);
        
                
        // Transfer registered entries from the temporary table to the permanent table
        
        $qrystr  = "INSERT INTO tst_entries(EntDesc, EntAmount, EntTrans, EntAcc, EntQty, EntUnit, EntObj) ";
        $qrystr .= "SELECT EntDesc,EntAmount,?,EntAcc,EntQty,EntUnit,EntObj FROM tmp_ents";
        
        $qry = $db->prepare($qrystr);
        $qry->execute(array($newTrnID));
        
        
        // Drop temporary tables.
        
        $qrystr  = "DROP TABLE tmp_ents; DROP TABLE tmp_trn;";
        
        $qry = $db->prepare($qrystr);
        $qry->execute();  

    }
    
    else {
        
         // Create temporary table
         
         $qrystr  = "CREATE TABLE tmp_trn (";
         $qrystr .= "ID BIGINT UNSIGNED AUTO_INCREMENT,";
         $qrystr .= "TrnDesc VARCHAR(64),";
         $qrystr .= "TrnDate DATETIME,";
         $qrystr .= "TrnCPart BIGINT UNSIGNED,";
         $qrystr .= "TrnNote VARCHAR(64),";
         $qrystr .= "PRIMARY KEY (ID))";
         
         $qry = $db->prepare($qrystr);
         $qry->execute();
         
         // Put data into the temporary table
         
         $qrystr  = "INSERT INTO tmp_trn (TrnDesc,TrnDate,TrnCPart,TrnNote) ";
         $qrystr .= "VALUES(?,?,?,?)";
         
         $qry = $db->prepare($qrystr);
         $qry->execute(array($_POST['fldTrnDesc'],$_POST['fldTrnDate'],$_POST['fldTrnCPart'],$_POST['fldTrnNote']));
                 
    }
     
}


?>

<!DOCTYPE unspecified PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
		<TITLE>Add new Transaction</TITLE>
		<LINK  Rel="stylesheet" Type="text/css" Href="resources/stylesheets/dbit_transaction.css" />
		
	
	</head>
	<BODY>

		
		<H1>Register new Transaction</H1>
		<P>Please enter a description of the transaction being registered in the following fields before adding entries</P> 
		
		<FORM Name="frmTrn" Method="post">
		
		
		
		<DIV Class = "dbit_fldGrp" Id="fldGrp_TrnID">New</DIV>
		 
		<INPUT Type="submit" Value="Submit" Name="btnSubmit" Id="btnSubmit" />
		 
		<DIV Class = "dbit_frmPanel" Id="pnl_TrnFrmTop">
		
<?php 

    // Date field
    echo "<DIV Class='dbit_fldGrp'><LABEL For='fldTrnDate'>Date</LABEL><INPUT Id='fldTrnDate' Name='fldTrnDate' Size='20' Type='text' Value='".timestamp()."'/></DIV>"
    
?>		
		
		    <DIV Class="dbit_fldGrp"><LABEL For="fldTrnDesc">Description</LABEL><INPUT Id="fldTrnDesc" Name="fldTrnDesc" Size="80" Type="text"/></DIV>
		
			<DIV Class="dbit_fldGrp">
				<LABEL For="fldTrnCPart">Counterpart</LABEL>
				<SELECT Id="fldTrnCPart" Name="fldTrnCPart" Size="1">
					
<?php 
    // Fetch the counterpart options from the database
    
    $qrystr  = "SELECT ParticipantID, PartName, PartLocation FROM tbl_counterparts";
    $qry = $db->prepare($qrystr);
    $qry->execute();
    $result = $qry->fetchAll(PDO::FETCH_ASSOC);
    
    foreach($result as $counterpart){
        echo "<OPTION Value = '".$counterpart["ParticipantID"]."'>".$counterpart["PartName"].", ".$counterpart["PartLocation"]."</OPTION>";
    }

?>
						
				</SELECT>				
			</DIV>
			
        </DIV> <!-- pnl_TrnFrmTop -->
		
		<DIV Class="fldGrp"><LABEL For="fldTrnNote">Note</LABEL><TEXTAREA Id="fldTrnNote" Name="fldTrnNote" rows="1" cols="113" style="resize: none"></TEXTAREA></DIV>
		
		
		
		</FORM>
		
<?php 
    // If the transaction is registered then we can go on to add entries
    
    


?>
		
		<FORM Name="frmEntries" Method="post">
		
			<!-- Lägg in ett formulär för att lägga till ett nytt entry. Föreslå ett belopp som gör transaktionen balanserad -->

			<DIV Class = "dbit_frmPanel" Id="pnl_NewEnt">
			
			   <DIV Class = "dbit_fldGrp">
			       <LABEL For = "fldEntDesc">Beskrivning</LABEL>
			       <INPUT Id="fldEntDesc" Name="fldEntDesc" Size="64" Type="text"/>
			   </DIV>
			   
			   <DIV Class = "dbit_fldGrp">
			       <LABEL For="fldEntAcc">Konto</LABEL>
			       <SELECT Id="fldEntAcc" Name="fldEntAcc" Size="1">
			    
<?php 
    $qrystr  = "SELECT AccountID, AccBDC, AccName FROM tbl_accounts ORDER BY AccBDC ASC";
    $qry = $db->prepare($qrystr);
    $qry->execute();
    $result = $qry->fetchAll(PDO::FETCH_ASSOC);
    
    foreach($result as $acc){
        echo "<OPTION Value='".$acc["AccountID"]."'>".$acc["AccBDC"]." - ".$acc["AccName"]."</OPTION>";
    }
?>

				  </SELECT>			
			   </DIV>
			   
			   <DIV Class="dbit_fldGrp">
			       <LABEL For="fldEntAmount">Belopp</LABEL>
			       <INPUT Id="fldEntAmount" Name="fldEntAmount" Size="16" Type="text"/>
			   </DIV>
			   
			   <DIV Class="dbit_fldGrp">
			       <LABEL For="fldEntQty">Kvantitet</LABEL>
			       <SPAN><INPUT Type="text" Name="fldEntQty" Id="fldEntQty" Size="12" /></SPAN>
			        <SPAN>
			            <SELECT Id="fldEntUnit" Name="fldEntUnit" Size="1">
			                <OPTION Value="st">st</OPTION>
			                <OPTION Value="frp">frp</OPTION>
			                <OPTION Value="l">l</OPTION>
			                <OPTION Value="kg">kg</OPTION>
			                <OPTION Value="par">par</OPTION>
			                <OPTION Value="tim">tim</OPTION>			    	
					    </SELECT>  			    
			       </SPAN>
			  </DIV>
			
			   <DIV Class="dbit_fldGrp">
			       <LABEL For ="fldEntObj">Tillhör</LABEL>
			       <SELECT Id="fldEntObj" Name="fldEntObj" Size="1">

<?php 
    $qrystr  = "SELECT AccObjID, AccObjName, Parent FROM tbl_accountingobjects ORDER BY Parent ASC, AccObjName ASC";
    $qry = $db->prepare($qrystr);
    $qry->execute();
    $result = $qry->fetchAll(PDO::FETCH_ASSOC);
    
    foreach($result as $accobj){
        echo "<OPTION Value='".$accobj["AccObjID"]."'>".$accobj["AccObjName"]."</OPTION>";
    }
?>			    	

                  </SELECT>			      
			  </DIV>
  			
			<DIV><INPUT Name="btnAdd" Id="btnAdd" Type="submit" Value="Add"/></DIV>
			   
			</DIV>
			
<?php 

    // List all temporary registered transactions 

    $qrystr  = "SELECT EntDesc,EntAcc,EntAmount,EntQty,EntUnit,EntObj FROM tmp_ents";
     $qry = $db->prepare($qrystr);
     $qry->execute();
     $result = $qry->fetchAll(PDO::FETCH_ASSOC);
     
     foreach($result as $ent){
         echo "<DIV Class='EntRow'><DIV>".$ent['EntDesc']."</DIV><DIV>".$ent['EntAcc']."</DIV><DIV>".$ent['EntAmount']."</DIV><DIV>".$ent['EntQty']."</DIV><DIV>".$ent['EntUnit']."</DIV><DIV>".$ent['EntObj']."</DIV></DIV>";
     }


?>
		
			
		</FORM>	

    </BODY>
</HTML>

<?php 

    $db = null;

?>