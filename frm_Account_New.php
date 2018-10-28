<?php

    require_once 'resources/fnc_database.php';
    
    $db = databaseconnect("lofqvist.dynu.net","dev","Av4rak1n","dbit");
    
    
    // Handle submitted form
    
    if(isset($_POST['btnSubmit'])){
        
        
        $qrystr  = "INSERT INTO tbl_accounts (AccName,AccBDC,AccDesc,AccCurrency,Parent,AccType) ";
        $qrystr .= "VALUES(?,?,?,?,?,?)";
        
        $qry = $db->prepare($qrystr);
        $qry->execute(array($_POST['fldName'],$_POST['fldBDC'],$_POST['fldDesc'],$_POST['fldCurrency'],$_POST['fldParent'],$_POST['fldType']));
        
    }
    
    
    $db = null;
    
?>

<!DOCTYPE unspecified PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
		<TITLE>Inmatningsformulär - Account</TITLE>
	
	</head>
	<BODY>

		<FORM Method="post">
		
			<DIV>New</DIV>
			<DIV><DIV>Kontonamn</DIV><DIV><INPUT Id="fldName" Name="fldName" Size="40" Type="text"/></DIV></DIV>
			<DIV><DIV>Struktur Ref</DIV><DIV><INPUT Id="fldBDC" Name="fldBDC" Size="5" Type="text"/></DIV></DIV>
			<DIV><DIV>Underställs</DIV><DIV><INPUT Id="fldParent" Name="fldParent" Size="5" Type="text"/></DIV></DIV>
			<DIV><DIV>Valuta</DIV><DIV>
			    	<SELECT Id="fldCurrency" Name="fldCurrency" Size="3">
						<OPTION Id="fldPartOpt_1" Value="SEK">SEK Svenska kronor</OPTION>
						<OPTION Id="fldPartOpt_2" Value="NOR">NOR Norska kronor</OPTION>
						<OPTION Id="fldPartOpt_1" Value="EUR">EUR Euro</OPTION>
						<OPTION Id="fldPartOpt_2" Value="USD">USD Amerikanska dollar</OPTION>
					</SELECT>
			</DIV>
			<DIV><DIV>Typ</DIV><DIV>
			      <INPUT Type="radio" Name="fldType" id="fldType" value="Assett"> Tillgång<BR/>
			      <INPUT Type="radio" Name="fldType" id="fldType" value="Dividend"> Skuld<BR/>
			      <INPUT Type="radio" Name="fldType" id="fldType" value="Expense" checked> Utgift<BR/>
			      <INPUT Type="radio" Name="fldType" id="fldType" value="Income"> Inkomst<BR/>
			</DIV>
  			<DIV><DIV>Beskrivning</DIV><DIV><TEXTAREA Id="fldDesc" Name="fldDesc" rows="3" cols="80" style="resize: none"></TEXTAREA></DIV></DIV>
			
			<DIV><button Name="btnSubmit" Id="btnSubmit">Submit</button></DIV>
			
		</FORM>

    </BODY>
</HTML>

