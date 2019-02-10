<?php
   
require_once 'resources/fnc_database.php';
require_once 'resources/fnc_general.php';


function accIDformatter($accID){
    
    return sprintf("%'.03d %'.03d",intdiv($accID,1000),$accID%1000);

} // accIDFormatter

function getAccounts ($parent=null){
    
    // Use the database defined within the global scope
    global $db;
    
    //Perform the query based on the provided parent account
    // OBS!   Inte en optimallösning eftersom databasfrågan inte har skyddats tillräckligt mot sql-insertion
    $qstr = "SELECT AccountID, AccBDC, AccName FROM tbl_accounts WHERE Parent ".($parent == null ? "IS NULL":"=".$parent);
    
	$qry = $db->prepare($qstr);
	$qry->execute();
	    
	$result = $qry->fetchAll();
    
	
	// Return the result of the query as an array 
    return $result;

    
} // getAccounts

function transTot ($acc,$start,$end){
    
    // Use the database defined within the global scope
    global $db;
        
    //$qstr = "SELECT SUM(trn.Amount) AS Trans FROM tbl_transactions AS trn LEFT JOIN tbl_occasions AS occ ON trn.Occasion = occ.ID WHERE occ.Date > ? AND occ.Date <= ? AND trn.Account = ?";
	$qstr  = "SELECT SUM(ent.EntAmount) AS Trans ";
	$qstr .= "FROM tbl_entries AS ent LEFT JOIN tbl_transactions AS trn ON ent.EntTrans = trn.TrnID ";
	$qstr .= "WHERE trn.TrnDate > ? AND trn.TrnDate <= ? AND ent.EntAcc = ?"; 
	    
    $res = databasequery($db,$qstr,array(($start==null?"1900-01-01 00:00":$start),$end,$acc),false);
	
	return ($res===null?0:$res);
	
      
} // transTot

function lastStatment ($acc){
    
    // Use the database defined within the global scope
    global $db;
    
    $qrystr  = "SELECT Date AS AccStated, Amount AS AccStat FROM tbl_statements ";
    $qrystr .= "WHERE Account=? AND Date=(SELECT Max(Date) FROM tbl_statements WHERE Account = ?)";
    
    //$qstr = "SELECT MAX(Date) AS AccStated, Amount AS AccStat FROM tbl_statements WHERE Account = ?";
    
    $qry = $db->prepare($qrystr);
    $qry->execute(array($acc,$acc));
    $res = $qry->fetch(PDO::FETCH_ASSOC);
    
    //$res = databasequery($db,$qrystr,array($acc,$acc));
        
    if (!isset($res)){
        $res["AccStated"] = "1900-01-01 00:00:00"; $res["AccStat"]=0;
    }
    
    // if($res[0]["AccStated"]==null){ $res[0]["AccStated"]="1900-01-01 00:00:00"; $res[0]["AccStat"]=0; }
    
    return $res; //$res[0]
    
    
} // lastStatement

function recAgrTot ($acc,$start,$end){
    
    $stat = lastStatment($acc);
    $trnTot = transTot($acc,$stat["AccStated"],$end);
    $agrTot = $stat["AccStat"] + $trnTot;
    
    $subAccs = getAccounts($acc);
    
    foreach($subAccs as $sub){
        $agrTot += recAgrTot($sub['AccountID'],$start,$end);
    }
    
    return $agrTot;
    
} // recAgrTot

function agrTot ($acc = null,$startDate = null,$endDate = null){
    
       
    global $db;
    
    // If no startDate has been provided, set the startDate to the date and time of the latest statement
    if($startDate==null){ 
        $startDate = databasequery($db, "SELECT MAX(Date) AS AccStated, Amount AS AccStat FROM tbl_statements WHERE Account = ?", array($acc),false);
    }
    
    // If no endDate has been provided, set the endDate to the date and time to the current date and time
    if($endDate==null){
        $endDate = timestamp();
    }
    
    return  recAgrTot ($acc,$startDate,$endDate);

    
} // agrTot

   
   // Connect to the database
   //$db = databaseconnect('lofqvist.dynu.net','dev','Av4rak1n','dbit');
   $db = databaseconnect('localhost','dev','Av4rak1n','dbit');
   
   
   // Get the selected account to present
   $activeAccountID = 24;
   if(isset($_GET['selAcc'])) { $activeAccountID = $_GET['selAcc']; }
   
   // Get current time
   $currentTime = timestamp();
     
?>

<HTML>
    <HEAD>
        <TITLE>Kontolista</TITLE>
        
        <LINK Rel="stylesheet" Type="text/css" href="resources/stylesheets/dbit_accountlist.css" />
        
        
    </HEAD>
    
    <BODY>


<?php 
   
   
    
    
    // Get the main account information
    $qrystr = "SELECT AccBDC, AccName, Parent FROM tbl_accounts WHERE AccountID = ?";
    $qry = $db->prepare($qrystr);
    $qry->execute(array($activeAccountID));
    $activeAccount = $qry->fetch(PDO::FETCH_ASSOC);
    
    echo "<br/>Konto: ".accIDFormatter($activeAccountID)." ".$activeAccount["AccBDC"]." - ".$activeAccount["AccName"];
    echo "<br/>Parent: ".($activeAccount["Parent"]===null?"Top account":"<a href='".$_SERVER['PHP_SELF']."?selAcc=".$activeAccount["Parent"]."'>".$activeAccount["Parent"]."</a>");
   
   $stats = lastStatment($activeAccountID);
   
   
   
   if($stats["AccStated"]===null){ 
       echo "<br/>Ingen bekräftelse hittades för det enskilda kontot!"; 
       $stats["AccStat"] = 0; $stats["AccStated"] = "1900-01-01 12:00:00";
   }
   else { echo "<br/>Bekräftat kontosaldo: ".$stats["AccStat"]." (".$stats["AccStated"].")"; }
   
   $curAccTot = transTot($activeAccountID,$stats["AccStated"],$currentTime);
      
   echo "<br/>Transaktioner sedan senaste bekräftelse: ".$curAccTot;
   
   echo "<br/>Beräknat kontosaldo (bekräftat saldo + transaktionsbelopp): ".($stats["AccStat"]+$curAccTot);
   
   echo "<br/>Aggregerat kontosaldo (inkludera kontosaldo från underställda konton): ".agrTot($activeAccountID,$stats["AccStated"],$currentTime);
   
   
  
   echo "<br/><br/>UNDERKONTON<hr/>";
   
   // Get the subaccount information
   
   $subAccs = getAccounts($activeAccountID);
   
   foreach ($subAccs as $acc){
       
       
      
       $stats = lastStatment($acc['AccountID']);
       //$stats = lastStatment(24);
       
       $accInfo = array();
       $accInfo["AccID"] = $acc['AccountID'];
       $accInfo["BDC"] = $acc['AccBDC'];
       $accInfo["Name"] = $acc['AccName'];
       $accInfo["Stated"] = $stats["AccStated"];
       $accInfo["Status"] = $stats["AccStat"];
       $accInfo["Trans"] = transTot($acc["AccountID"],$accInfo["Stated"],$currentTime);
       $accInfo["Total"] = $accInfo["Trans"] + $accInfo["Status"];
       $accInfo["AgrTot"] = agrTot($acc["AccountID"],$accInfo["Stated"],$currentTime);
       
       
       $accounts[] = $accInfo;
            
   }
   
      
   // Present the list on the page  
   

   //print "<pre>"; print_r($accounts); print "</pre>";
   
   echo "<TABLE Id='Acclist'>\n";
   echo "<THEAD><TR><TD>ID</TD><TD>BDC</TD><TD>Name</TD><TD>Stated</TD><TD>Status</TD><TD>Trans</TD><TD>Total</TD><TD>AgrTot</TD></TR></THEAD>\n";
   echo "<TBODY>\n"; 
   
   foreach($accounts as $acc){
       echo "<TR><TD Class='colID'><A Href='".$_SERVER['PHP_SELF']."?selAcc=".$acc["AccID"]."'>".accIDFormatter($acc["AccID"])."</A></TD>";
       echo "<TD Class='colBDC'>".$acc["BDC"]."</TD>";
       echo "<TD Class='colName'>".$acc["Name"]."</TD>";
       echo "<TD Class='colStated'>".$acc["Stated"]."</TD>";
       echo "<TD Class='colStatus currency'>".$acc["Status"]."</TD>";
       echo "<TD Class='colTrans currency'>".$acc["Trans"]."</TD>";
       echo "<TD Class='colTotal currency'>".$acc["Total"]."</TD>";
       echo "<TD Class='colAgrTot currency'>".$acc["AgrTot"]."</TD></TR>";
   }

   
   echo "</TBODY>";
   echo "</TABLE>";  
 
   
   
   echo "<br/><br/>Transaktioner<hr/>";
   
   // Get the transactions made from the last statement uptil current time
   $qrystr  = "SELECT tbl1.TrnID,tbl1.TrnDesc,tbl1.TrnDate,tbl1.CPart AS Part,tbl2.TrnValue ";
   $qrystr .= "FROM ( SELECT trn.TrnID,trn.TrnDesc,trn.TrnDate,cp.PartName AS CPart ";
   $qrystr .= "FROM tbl_transactions AS trn LEFT JOIN tbl_counterparts AS cp ON trn.TrnCPart = cp.ParticipantID ";
   $qrystr .= "WHERE trn.TrnID IN ( ";
   $qrystr .= "SELECT DISTINCT ent.EntTrans as Trans FROM tbl_entries AS ent LEFT JOIN tbl_transactions AS trn2 ON ent.EntTrans=trn2.TrnID ";
   $qrystr .= "WHERE ent.EntAcc = ? AND trn2.TrnDate > ";
   $qrystr .= "(SELECT Date FROM tbl_statements WHERE Account=? AND Date= (SELECT Max(st.Date) FROM tbl_statements as st WHERE Account = ?)) ";
   $qrystr .= ")) AS tbl1 LEFT JOIN (SELECT EntTrans as TrnID, SUM(ABS(EntAmount))/2 AS TrnValue FROM tbl_entries GROUP BY EntTrans) AS tbl2 ";
   $qrystr .= "ON tbl1.TrnID = tbl2.TrnID ORDER BY tbl1.TrnDate DESC";
   
   $qry = $db->prepare($qrystr); 
   $qry->execute(array($activeAccountID,$activeAccountID,$activeAccountID));
   $res = $qry->fetchAll(PDO::FETCH_ASSOC);
   
   echo "<TABLE Id='Trnlist'>\n";
   echo "<THEAD><TR><TD>Date</TD><TD>ID</TD><TD>Description</TD><TD>Part</TD><TD>Amount</TD></TR></THEAD>\n";
   echo "<TBODY>\n";
   
   foreach($res as $trn){
       echo "<TR><TD Class='colDate'>".$trn["TrnDate"]."</TD>";
       echo "<TD Class='colID'>".$trn["TrnID"]."</TD>";
       echo "<TD Class='colDesc'>".$trn["TrnDesc"]."</TD>";
       echo "<TD Class='colPart'>".$trn["Part"]."</TD>";
       echo "<TD Class='colAmount currency'>".$trn["TrnValue"]."</TD></TR>";
   }
   
   
   echo "</TBODY>";
   echo "</TABLE>";
   
   
   
   
?>

    </BODY>
</HTML>




<?php 
   
   $db = null;
   
?>
