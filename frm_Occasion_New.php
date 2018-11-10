<?php 

require_once 'resources/fnc_database.php';
require_once 'resources/fnc_general.php';

$db = databaseconnect("lofqvist.dynu.net","dev","Av4rak1n","dbit");

// Handle submitted form

if(isset($_POST['btnSubmit'])){
    

    // Create temporary table

    $qrystr  = "CREATE TABLE tmp_Occ (";
    $qrystr .= "OccDesc VARCHAR(64),";
    $qrystr .= "OccDate DATETIME,";                            // Skulle kunna lägga till DEFAULT för att sätta aktuell tid direkt i db
    $qrystr .= "OccCPart BIGINT(20) UNSIGNED,";
    $qrystr .= "OccNote VARCHAR(64)";
    $qrystr .= ")";

    $qry = $db->prepare($qrystr);
    $qry->execute();

    // Put data into the temporary table

    $qrystr  = "INSERT INTO tmp_Occ (OccDesc,OccDate,OccCPart,OccNote) ";
    $qrystr .= "VALUES(?,?,?,?)";
    
    $qry = $db->prepare($qrystr);
    $qry->execute(array($_POST['fldDesc'],$_POST['fldDate'],$_POST['fldCPart'],$_POST['fldNote']));
    
    // Transfer data from temporary table to permanent table
    
    $qrystr  = "INSERT INTO fixed_occ(OccDesc, OccDate, OccCPart, OccNote) ";
    $qrystr .= "SELECT OccDesc,OccDate,OccCPart,OccNote FROM tmp_occ";
    
    $qry = $db->prepare($qrystr);
    $qry->execute();
    

    // Drop temporary table.
    
    $qrystr  = "DROP TABLE tmp_occ";
    
    $qry = $db->prepare($qrystr);
    $qry->execute();

    
}


?>

<!DOCTYPE unspecified PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
		<TITLE>Inmatningsformulär - Occasion</TITLE>
	
	</head>
	<BODY>

		<FORM Method="post">
		
<?php 
    
    // Occasion identification field
    echo "</DIV>New</DIV>";

    // Date field
    echo "<DIV><DIV>Date</DIV><DIV><INPUT Id='fldDate' Name='fldDate' Size='20' Type='text' Value='".timestamp()."'/></DIV></DIV>"

    
?>
			
			<DIV><DIV>Description</DIV><DIV><INPUT Id="fldDesc" Name="fldDesc" Size="80" Type="text"/></DIV></DIV>
			<DIV>
				<DIV>Counterpart</DIV>
				<DIV>
					<SELECT Id="fldCPart" Name="fldCPart" Size="5">
					
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
						<OPTION Id="fldPartOpt_1" Value="61">Coop Konsum, Hörnett</OPTION>
						<OPTION Id="fldPartOpt_2" Value="189">OKQ8 Rondellen, Örnsköldsvik</OPTION>
					</SELECT>				
				</DIV>
			</DIV>
			<DIV><DIV>Note</DIV><DIV><TEXTAREA Id="fldNote" Name="fldNote" rows="5" cols="80" style="resize: none"></TEXTAREA></DIV></DIV>
			<DIV><button Name="btnSubmit" Id="btnSubmit">Submit</button></DIV>
		</FORM>

    </BODY>
</HTML>

<?php 

    $db = null;

?>