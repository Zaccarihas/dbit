<?php 

require_once 'resources/fnc_database.php';
require_once 'resources/fnc_general.php';

//$db = databaseconnect("lofqvist.dynu.net","dev","Av4rak1n","dbit");
$db = databaseconnect("localhost","dev","Av4rak1n","dbit");

// Handle submitted form

if(isset($_POST['btnAdd'])){
 
    // Create temporary table
    
    $qrystr  = "CREATE TABLE IF NOT EXISTS tmp_ents (";
    $qrystr .= "EntID BIGINT UNSIGNED AUTO_INCREMENT,";
    $qrystr .= "EntDesc VARCHAR(64),";                            
    $qrystr .= "EntAmount DECIMAL(10,2),";
    $qrystr .= "EntAcc BIGINT UNSIGNED,";
    $qrystr .= "EntQty FLOAT DEFAULT NULL,";
    $qrystr .= "EntUnit ENUM('st','frp','l','kg','par','tim') DEFAULT NULL,";
    $qrystr .= "EntObj BIGINT UNSIGNED DEFAULT NULL,";
    $qrystr .= "PRIMARY KEY (EntID))";
    
    $qry = $db->prepare($qrystr);
    $qry->execute();
       
    // Här ska jag fortsätta att lägga in uppdatering av tabellen men hur gör jag en temporär tabell.
    // Det kommer ju bli problem om jag bara gör en egen temporärtabell ifall flera användare är inne samtidigt
    
    $entvalues = array();
    $entvalues[] = $_POST['fldEntDesc'];
    $entvalues[] = str_replace(",",".",$_POST['fldEntAmount']);
    $entvalues[] = $_POST['fldEntAcc'];
    
    // Check if the account is an Assett or a Liability
    $qrystr  = "SELECT AccType FROM tbl_accounts WHERE AccountID = ?";
    $qry = $db->prepare($qrystr);
    $qry->execute(array($_POST["fldEntAcc"]));
    $accType = $qry->fetchColumn();
    
    echo $accType."<br/>";
    
    if($accType != 'Assett'){
        $entvalues[] = $_POST['fldEntQty'];
        $entvalues[] = $_POST['fldEntUnit'];
        $entvalues[] = $_POST['fldEntObj'];
    }
    
     print_r($entvalues); echo("<br/>");
    
    // Put data into the temporary table
    
    $qrystr  = "INSERT INTO tmp_ents (EntDesc,EntAmount,EntAcc".($accType != 'Assett'?",EntQty,EntUnit,EntObj":"").") ";
    $qrystr .= "VALUES(?,?,?".($accType != 'Assett'?",?,?,?":"").")";
    
    echo "<br/>".$qrystr."<br/>";
    
    $qry = $db->prepare($qrystr);
    $qry->execute($entvalues);
    
    
    
}

if(isset($_POST['btnSubmit'])){
    
   
    // Is there a temporary registered transaction
    
    $qry = $db->prepare("SHOW TABLES LIKE '%tmp_trn%'");
    $qry->execute();
    $res = $qry->fetch(PDO::FETCH_NUM);
    
    if(isset($res[0])){ 
        
        // Check if the sum of all entries is in balance
        $qrystr  = "SELECT SUM(EntAmount) AS ChkAmount FROM tmp_ents";
        $qry = $db->prepare($qrystr);
        $qry->execute();
        $checkSum = $qry->fetchColumn();
        
        if($checkSum=='0'){
        
            // Transfer the transaction info from the temporary table to the permanent table
            
            //$trnTab = "tst_trans"; $entTab = "tst_entries"; // The test tables
            $trnTab = "tbl_transactions"; $entTab = "tbl_entries"; // The live tables       
            
            $qrystr  = "INSERT INTO ".$trnTab."(TrnDesc, TrnDate, TrnCPart, TrnNote) ";
            $qrystr .= "SELECT TrnDesc,TrnDate,TrnCPart,TrnNote FROM tmp_trn";
            $qry = $db->prepare($qrystr);
            $qry->execute();
            
            $qrystr  = "SELECT LAST_INSERT_ID()";
            $qry = $db->prepare($qrystr);
            $qry->execute();
                    
            $newTrnID = $qry->fetch(PDO::FETCH_COLUMN);
            
                    
            // Transfer registered entries from the temporary table to the permanent table
            
            $qrystr  = "INSERT INTO ".$entTab."(EntDesc, EntAmount, EntTrans, EntAcc, EntQty, EntUnit, EntObj) ";
            $qrystr .= "SELECT EntDesc,EntAmount,?,EntAcc,EntQty,EntUnit,EntObj FROM tmp_ents";
            
            $qry = $db->prepare($qrystr);
            $qry->execute(array($newTrnID));
            
            
            // Drop temporary tables.
            
            $qrystr  = "DROP TABLE tmp_ents; DROP TABLE tmp_trn;";
            
            $qry = $db->prepare($qrystr);
            $qry->execute(); 
        }
        else {
            echo "<BR/>The transaction is not in balanced. Registered difference between entries: ".$checkSum;
        }
        
         

    }
    
    else {
        
           
        
         
         // Create temporary table
         
         $qrystr  = "CREATE TABLE tmp_trn (";
         $qrystr .= "TrnID BIGINT UNSIGNED AUTO_INCREMENT,";
         $qrystr .= "TrnDesc VARCHAR(64),";
         $qrystr .= "TrnDate DATETIME,";
         $qrystr .= "TrnCPart BIGINT UNSIGNED,";
         $qrystr .= "TrnNote VARCHAR(64) DEFAULT NULL,";
         $qrystr .= "PRIMARY KEY (TrnID))";
         
         $qry = $db->prepare($qrystr);
         $qry->execute();
         
         
         
         // Put data into the temporary table
         
         $qrystr  = "INSERT INTO tmp_trn (TrnDesc,TrnDate,TrnCPart".($_POST['fldTrnNote']==""?"":",TrnNote").") ";
         $qrystr .= "VALUES(?,?,?".($_POST['fldTrnNote']==""?"":",?").")";
         
         $qry = $db->prepare($qrystr);
         
         $inArg = array($_POST['fldTrnDesc'],$_POST['fldTrnDate'],$_POST['fldTrnCPart']);
         if($_POST['fldTrnNote']!=''){ $inArg[] = $_POST['fldTrnNote']; }
         $qry->execute($inArg);
         
        
         
         
                 
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
		
		<FORM Name="frmTrn" Method="post">
				
		<?php 
		
		    // Check if a transaction has been temporary stored
		    
			$qry = $db->prepare("SHOW TABLES LIKE '%tmp_trn%'");
		    $qry->execute();
		    $res = $qry->fetch(PDO::FETCH_NUM);
		    
		    $entPhase = isset($res[0]);
		
		    // Instructions
		    if($entPhase){
		        echo "<P>Register entries related to this transaction by pressing Add or submit the Transaction by pressing Submit</P>";
		    }
		    else {
		        echo "<P>Enter a transaction description using the following fields before adding the related entries</P>";
		    }
		    
		    
		    // Transaction ID and Submit- and Reset buttons
		    echo "<DIV Class = 'dbit_fldGrp' Id='fldGrp_TrnID'>New</DIV>";
		    echo "<INPUT Type='submit' Value='Register' Name='btnSubmit' Id='btnSubmit' />";
		    echo "<INPUT Type='reset' Value='Cancel' Name='btnReset' Id='btnReset' />";
		    		    
		    // If a temporary transaction is stored, then fetch info to present
		    if($entPhase){
		        $qrystr  = "SELECT TrnDesc, TrnDate, TrnCPart, TrnNote from tmp_trn";
		        $qry = $db->prepare($qrystr);
		        $qry->execute();
    	        $trn = $qry->fetch(PDO::FETCH_ASSOC);
    	    }
		        
    	    
		    // The Top Panel
    	    echo "<DIV Class = 'dbit_frmPanel' Id='pnl_TrnFrmTop'>";
		        
		        // Date field
		        echo "<DIV Class='dbit_fldGrp'><LABEL For='fldTrnDate'>Date</LABEL>";
		        if($entPhase){ echo "<DIV Id='fldTrnDate'>".$trn["TrnDate"]."</DIV>"; }
		        else { echo "<INPUT Id='fldTrnDate' Name='fldTrnDate' Size='20' Type='text' Value='".timestamp()."'/>"; }
		        echo "</DIV>";
		        		        
		        // Description field
		        echo "<DIV Class='dbit_fldGrp'><LABEL For='fldTrnDesc'>Description</LABEL>";
		        if($entPhase){ echo "<DIV Id='fldTrnDesc'>".$trn["TrnDesc"]."</DIV>"; }
		        else { echo "<INPUT Id='fldTrnDesc' Name='fldTrnDesc' Size='80' Type='text'/>"; }
		        echo "</DIV>";
		        
		        // Counterpart
		        
		        // Prepare the query depending on if a transaction already is registered or not.
		        $qrystr  = "SELECT ParticipantID, PartName, PartLocation FROM tbl_counterparts ORDER BY PartName ASC";
		        if($entPhase){ $qrystr .= " WHERE ParticipantID = ?"; }
		        $qry = $db->prepare($qrystr);
		        
		        // Get the needed counterpart info from the database
		        if($entPhase){
		            $qry->execute(array($trn["TrnCPart"])); $cPart = $qry->fetch(PDO::FETCH_ASSOC); }
		        else {
		            $qry->execute(); $res = $qry->fetchAll(PDO::FETCH_ASSOC); }
		            		        
		        // Present the field
		        echo "<DIV Class='dbit_fldGrp'><LABEL For='fldTrnCPart'>Counterpart</LABEL>";
		        
		        // ... with the already stored info ...
		        if($entPhase){ echo "<DIV Id='fldTrnCPart'>".$cPart["PartName"].", ".$cPart["PartLocation"]."</DIV>"; }
		        
		        // ... or as a selection field where all counterparts are presented as options
		        else { 
		            echo "<SELECT Id='fldTrnCPart' Name='fldTrnCPart' Size='1'>";
		            foreach($res as $cPart){
		                echo "<OPTION Value = '".$cPart["ParticipantID"]."'>".$cPart["PartName"].", ".$cPart["PartLocation"]."</OPTION>";
		            }
		            echo "</SELECT>"; }
		        echo "</DIV>";
		        
		    echo "</DIV>"; //pnl_TrnFrmTop
		    
		    
		    // Notes field
		    echo "<DIV Class='fldGrp'><LABEL For='fldTrnNote'>Note</LABEL>";
		    if($entPhase){ echo "<DIV Id='fldTrnNote'>".$trn["TrnNote"]."</DIV>"; }
		    else { echo "<TEXTAREA Id='fldTrnNote' Name='fldTrnNote' rows='1' cols='113' style='resize: none'></TEXTAREA>"; }
		    echo "</DIV>";
		    
		    
		    // Present fields for adding an entry to an already stored transaction
		    if($entPhase){
		        
		        // Add button
		        echo "<DIV><INPUT Name='btnAdd' Id='btnAdd' Type='submit' Value='Add'/></DIV>";
		        
		        // The formp panel holding all the fields related to a new entry
		        echo "<DIV Class = 'dbit_frmPanel' Id='pnl_NewEnt'>";
		        
		        // Description field
		        echo "<DIV Class = 'dbit_fldGrp'><LABEL For = 'fldEntDesc'>Beskrivning</LABEL><INPUT Id='fldEntDesc' Name='fldEntDesc' Size='64' Type='text'/></DIV>";
	        
		        // Account field
		        echo "<DIV Class = 'dbit_fldGrp'><LABEL For='fldEntAcc'>Konto</LABEL><SELECT Id='fldEntAcc' Name='fldEntAcc' Size='1'>";
		        $qrystr  = "SELECT AccountID, AccBDC, AccName FROM tbl_accounts ORDER BY AccBDC ASC";
		        $qry = $db->prepare($qrystr);
		        $qry->execute();
		        $result = $qry->fetchAll(PDO::FETCH_ASSOC);
		        
		        foreach($result as $acc){
		            echo "<OPTION Value='".$acc["AccountID"]."'>".$acc["AccBDC"]." - ".$acc["AccName"]."</OPTION>";
		        }
		        echo "</SELECT></DIV>";
		        
		        // Amount field
		        echo "<DIV Class='dbit_fldGrp'><LABEL For='fldEntAmount'>Belopp</LABEL><INPUT Id='fldEntAmount' Name='fldEntAmount' Size='16' Type='text'/></DIV>";
		        
		        // Quantity and Unit field
		        echo "<DIV Class='dbit_fldGrp'><LABEL For='fldEntQty'>Kvantitet</LABEL><SPAN><INPUT Type='text' Name='fldEntQty' Id='fldEntQty' Size='12' /></SPAN>";
		        echo "<SPAN><SELECT Id='fldEntUnit' Name='fldEntUnit' Size='1'>";
		        echo "<OPTION Value='st'>st</OPTION>";
		        echo "<OPTION Value='frp'>frp</OPTION>";
		        echo "<OPTION Value='l'>l</OPTION>";
		        echo "<OPTION Value='kg'>kg</OPTION>";
		        echo "<OPTION Value='par'>par</OPTION>";
		        echo "<OPTION Value='tim'>tim</OPTION>";
		        echo "<OPTION Value='m'>m</OPTION>";
		        echo "</SELECT></SPAN></DIV>";
		        
		        // Accounting Object field
		        echo "<DIV Class='dbit_fldGrp'><LABEL For ='fldEntObj'>Tillhör</LABEL><SELECT Id='fldEntObj' Name='fldEntObj' Size='1'>";
		        $qrystr  = "SELECT AccObjID, AccObjName, Parent FROM tbl_accountingobjects ORDER BY Parent ASC, AccObjName ASC";
		        $qry = $db->prepare($qrystr);
		        $qry->execute();
		        $result = $qry->fetchAll(PDO::FETCH_ASSOC);
		        
		        foreach($result as $accobj){
		            echo "<OPTION Value='".$accobj["AccObjID"]."'>".$accobj["AccObjName"]."</OPTION>";
		        }
		        
		        echo "</SELECT></DIV>";
		        
		        echo "</DIV>"; // Ending the form panel
		        
		        // List all temporary registered entries
		        
		        $qrystr  = "SELECT EntDesc,EntAcc,EntAmount,EntQty,EntUnit,EntObj FROM tmp_ents";
		        $qry = $db->prepare($qrystr);
		        $qry->execute();
		        $result = $qry->fetchAll(PDO::FETCH_ASSOC);
		        
		        foreach($result as $ent){
		            echo "<DIV Class='EntRow'>";
		            
		            // Description
		            echo "<DIV Class='EntDesc'>".$ent['EntDesc']."</DIV>";
		            
		            // Look up Account name
		            $qrystr  = "SELECT AccBDC, AccName FROM tbl_accounts WHERE AccountID = ?";
		            $qry = $db->prepare($qrystr);
		            $qry->execute(array($ent["EntAcc"]));
		            $acc = $qry->fetch(PDO::FETCH_ASSOC);
		            echo "<DIV Class='EntAcc'>".$acc['AccBDC']." - ".$acc['AccName']."</DIV>";
		            
		            // Amount
		            echo "<DIV Class='EntAmount'>".$ent['EntAmount']."</DIV>";
		            
		            // Quantity
		            echo "<DIV Class='EntQty'>".$ent['EntQty']." ".$ent['EntUnit']."</DIV>";
		            
		            // Look up Object
		            $qrystr  = "SELECT AccObjName AS ObjName FROM tbl_accountingobjects WHERE AccObjID = ?";
		            $qry = $db->prepare($qrystr);
		            $qry->execute(array($ent["EntObj"]));
		            $obj = $qry->fetch(PDO::FETCH_ASSOC);
		            echo "<DIV Class='EntObj'>".$obj['ObjName']."</DIV>";
		            
		            echo "</DIV>";
		            
		        } // Presentation of all already stored entries
		        		        
		    } // Presentation of entris if a transaction is already stored
		
		?>

		</FORM>	
    </BODY>
</HTML>

<?php 

    $db = null;

?>