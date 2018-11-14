<?php

require_once 'resources/fnc_database.php';



$db = databaseconnect("lofqvist.dynu.net","dev","Av4rak1n","dbit");


// Handle submitted form

if(isset($_POST['btnSubmit'])){
    
    
    echo "<br/>Vi har mottagit följande information";
    print_r($_POST);
    
    
    $qrystr  = "INSERT INTO tst_transactions (TransDesc,Amount,Occasion,Account,Qty,Unit,Object) ";
    $qrystr .= "VALUES(?,?,?,?,?,?,?)";
    
    $qry = $db->prepare($qrystr);
    $qry->execute(array($_POST['fldDesc'],$_POST['fldAmount'],$_POST['fldOcc'],$_POST['fldAcc'],$_POST['fldQty'],$_POST['fldUnit'],$_POST['fldObj']));
    
}

?>

<!DOCTYPE unspecified PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
		<TITLE>Add new Transaction</TITLE>
	
	</head>
	<BODY>

		<FORM Method="post">
		
			<DIV>New</DIV>
			<DIV><DIV>Beskrivning</DIV><DIV><INPUT Id="fldDesc" Name="fldDesc" Size="64" Type="text"/></DIV></DIV>
			<DIV><DIV>Belopp</DIV><DIV><INPUT Id="fldAmount" Name="fldAmount" Size="16" Type="text"/></DIV></DIV>
			<DIV><DIV>Konto</DIV><DIV>
			    <SELECT Id="fldAcc" Name="fldAcc" Size="5">
			    
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
			</DIV></DIV>
			<DIV><DIV>Occasion</DIV><DIV>
			    	<SELECT Id="fldOcc" Name="fldOcc" Size="3">

<?php 
    $qrystr  = "SELECT ID, OccDate, OccDesc FROM tbl_occasions ORDER BY OccDate DESC";
    $qry = $db->prepare($qrystr);
    $qry->execute();
    $result = $qry->fetchAll(PDO::FETCH_ASSOC);
    
    foreach($result as $occ){
        echo "<OPTION Value='".$occ["ID"]."'>".$occ["OccDate"]." ".$occ["OccDesc"]."</OPTION>";
    }
?>			    	
			    	
					</SELECT>
			</DIV></DIV>
			
			<DIV><DIV>Kvantitet</DIV><DIV>
			    <SPAN><INPUT Type="text" Name="fldQty" Id="fldQty" Size="12" /></SPAN>
			    <SPAN>
			        <SELECT Id="fldUnit" Name="fldUnit" Size="5">
			            <OPTION Value="st">st</OPTION>
			            <OPTION Value="frp">frp</OPTION>
			            <OPTION Value="l">l</OPTION>
			            <OPTION Value="kg">kg</OPTION>
			            <OPTION Value="par">par</OPTION>
			            <OPTION Value="tim">tim</OPTION>			    	
					</SELECT>  			    
			    </SPAN>
			</DIV></DIV>
			<DIV><DIV>Tillhör</DIV><DIV>
			    <SELECT Id="fldObj" Name="fldObj" Size="5">

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
			</DIV></DIV>
  			
			<DIV><button Name="btnSubmit" Id="btnSubmit">Submit</button></DIV>
			
		</FORM>

    </BODY>
</HTML>

<?php 

    $db = null;

?>