<?php 

require_once 'resources/fnc_database.php';
require_once 'resources/fnc_general.php';

$db = databaseconnect("lofqvist.dynu.net","dev","Av4rak1n","dbit");

// Handle submitted form

if(isset($_POST['btnAdd'])){
    
    // Create temporary table
    
    $qrystr  = "CREATE TABLE IF NOT EXIST tmp_trans (";
    $qrystr .= "ID BIGINT UNSIGNED AUTOINCREMENT,";
    $qrystr .= "TransDesc VARCHAR(64),";                            
    $qrystr .= "Amount DECIMAL(10,2),";
    $qrystr .= "Account BIGINT UNSIGNED,";
    $qrystr .= "Qty FLOAT,";
    $qrystr .= "Unit ENUM('st','frp','l','kg','par','tim'),";
    $qrystr .= "Object BIGINT UNSIGNED";
    $qrystr .= ")";
    
    $qry = $db->prepare($qrystr);
    $qry->execute();

    // Här ska jag fortsätta att lägga in uppdatering av tabellen men hur gör jag en temporär tabell.
    // Det kommer ju bli problem om jag bara gör en egen temporärtabell ifall flera användare är inne samtidigt
    
    // Put data into the temporary table
    
    $qrystr  = "INSERT INTO tmp_trans (TransDesc,Amount,Account,Qty,Unit,Object) ";
    $qrystr .= "VALUES(?,?,?,?,?,?)";
    
    $qry = $db->prepare($qrystr);
    $qry->execute(array($_POST['fldTransDesc'],$_POST['fldAmount'],$_POST['fldAcc'],$_POST['fldQty'],$_POST['fldUnit'],$_POST['fldObj']));
    
    // Måste jag nollställa $_POST så jag inte hamnar här automatiskt nästa uppdatering av sidan
    
}

if(isset($_POST['btnSubmit'])){
    

    // Put data into the temporary table

    $qrystr  = "INSERT INTO fixed_Occ (OccDesc,OccDate,OccCPart,OccNote) ";
    $qrystr .= "VALUES(?,?,?,?)";
    
    $qry = $db->prepare($qrystr);
    $qry->execute(array($_POST['fldDesc'],$_POST['fldDate'],$_POST['fldCPart'],$_POST['fldNote']));
    
    $newOccID = 0; // Get the ID-number for the new created occasion
    
    // Transfer registered transaction from the temporary table to the permanent table
    
    $qrystr  = "INSERT INTO tst_transactions(TransDesc, Amount, Occasion, Account, Qty, Unit, Object) ";
    $qrystr .= "SELECT TransDesc,Amount,?,Account,Qty,Unit,Object FROM tmp_trans";
    
    $qry = $db->prepare($qrystr);
    $qry->execute(array($newOccID));
    
    
    // Drop temporary table.
    
    $qrystr  = "DROP TABLE tmp_trans";
    
    $qry = $db->prepare($qrystr);
    $qry->execute();    
    
}


?>

<!DOCTYPE unspecified PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
		<TITLE>Add new Occasion</TITLE>
		<LINK  Rel="stylesheet" Type="text/css" Href="resources/stylesheets/dbit_occasion.css" />
		
	
	</head>
	<BODY>

		<FORM Name="frmOcc" Method="post">
		
		<DIV Class = "dbit_fldGrp" Id="fldGrp_OccID">New</DIV>
		
		<!-- Om det inte finns några transaktioner registrerade eller   -->
		<!-- om verifikatet inte är i balans så ska knappen inaktiveras -->
		
		<BUTTON Name="btnSubmit" Id="btnSubmit">Submit</BUTTON>
		
		<DIV Class = "dbit_frmPanel" Id="pnl_OccFrmTop">
		
<?php 

    // Date field
    echo "<DIV Class='dbit_fldGrp'><LABEL For='fldDate'>Date</LABEL><INPUT Id='fldDate' Name='fldDate' Size='20' Type='text' Value='".timestamp()."'/></DIV>"
    
?>		
		
		    <DIV Class="dbit_fldGrp"><LABEL For="fldDesc">Description</LABEL><INPUT Id="fldDesc" Name="fldDesc" Size="80" Type="text"/></DIV>
		
			<DIV Class="dbit_fldGrp">
				<LABEL For="fldCPart">Counterpart</LABEL>
				<SELECT Id="fldCPart" Name="fldCPart" Size="1">
					
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
			
        </DIV> <!-- pnl_OccFrmTop -->
		
		<DIV Class="fldGrp"><LABEL For="fldNote">Note</LABEL><TEXTAREA Id="fldNote" Name="fldNote" rows="1" cols="113" style="resize: none"></TEXTAREA></DIV>
		
		
		
		</FORM>
		
		<FORM Name="frmTrans">
		
			<!-- Lägg in ett formulär för att lägga till en ny transaktion. Föreslå ett blopp som gör verifikatet balanserat -->

			<DIV Class = "dbit_frmPanel" Id="pnl_NewTrans">
			
			   <DIV Class = "dbit_fldGrp">
			       <LABEL For = "fldTransDesc">Beskrivning</LABEL>
			       <INPUT Id="fldTransDesc" Name="fldTransDesc" Size="64" Type="text"/>
			   </DIV>
			   
			   <DIV Class = "dbit_fldGrp">
			       <LABEL For="fldAcc">Konto</LABEL>
			       <SELECT Id="fldAcc" Name="fldAcc" Size="1">
			    
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
			       <LABEL For="fldAmount">Belopp</LABEL>
			       <INPUT Id="fldAmount" Name="fldAmount" Size="16" Type="text"/>
			   </DIV>
			   
			   <DIV Class="dbit_fldGrp">
			       <LABEL For="fldQty">Kvantitet</LABEL>
			       <SPAN><INPUT Type="text" Name="fldQty" Id="fldQty" Size="12" /></SPAN>
			        <SPAN>
			            <SELECT Id="fldUnit" Name="fldUnit" Size="1">
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
			       <LABEL For ="fldObj">Tillhör</LABEL>
			       <SELECT Id="fldObj" Name="fldObj" Size="1">

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
  			
			  <DIV><button Name="btnAdd" Id="btnAdd">Add</button></DIV>
			   
			   
			</DIV>
			
<?php 

    // List all temporary registered transactions 

    $qrystr  = "SELECT TransDesc,Account,Amount,Qty,Unit,Object FROM tmp_trans";
     $qry = $db->prepare($qrystr);
     $qry->execute();
     $result = $qry->fetchAll(PDO::FETCH_ASSOC);
     
     foreach($result as $trans){
         echo "<DIV Class='transRow'><DIV>".$trans['TransDesc']."</DIV><DIV>".$trans['Account']."</DIV><DIV>".$trans['Amount']."</DIV><DIV>".$trans['Qty']."</DIV><DIV>".$trans['Unit']."</DIV><DIV>".$trans['Object']."</DIV></DIV>";
     }


?>
		
			
		</FORM>	

    </BODY>
</HTML>

<?php 

    $db = null;

?>