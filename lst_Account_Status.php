<?php
   
require_once 'resources/fnc_database.php';
require_once 'resources/fnc_general.php';


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
        
    $qstr = "SELECT SUM(trn.Amount) AS Trans FROM tbl_transactions AS trn LEFT JOIN tbl_occasions AS occ ON trn.Occasion = occ.ID WHERE occ.Date > ? AND occ.Date <= ? AND trn.Account = ?";
	
	$res = databasequery($db,$qstr,array(($start==null?"1900-01-01 00:00":$start),$end,$acc),false);
	
	return ($res===null?0:$res);
	
      
} // transTot

function lastStatment ($acc){
    
    // Use the database defined within the global scope
    global $db;
    
    $qstr = "SELECT MAX(Date) AS AccStated, Amount AS AccStat FROM tbl_statements WHERE Account = ?";
    
    $res = databasequery($db,$qstr,array($acc));
    
    if($res[0]["AccStated"]==null){ $res[0]["AccStated"]="1900-01-01 00:00:00"; $res[0]["AccStat"]=0; }
    
    return $res[0];
    
    
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

    
}

   
   // Connect to the database
   $db = databaseconnect('lofqvist.dynu.net','dev','Av4rak1n','dbit');
   
   // Prepare needed qureies 
   
   $qry_subAccs =   $db->prepare("SELECT AccountID, AccBDC, AccName FROM tbl_accounts WHERE Parent = ?");
   
 
   
   $activeAccountID = 10;
   $currentTime = timestamp();
   $accounts = array();
   
   // Get the main account information
   
   $activeAccount = databasequery($db, "SELECT AccBDC, AccName FROM tbl_accounts WHERE AccountID = ?", array($activeAccountID),true);
   
   
   
   echo "<h1>Welcome</h1>";
   echo "<div>Hello! The database is now open for business.</div>";
   
   /*echo "<br/>Konto: ".$activeAccountID." ".$activeAccount[0]["AccBDC"]." - ".$activeAccount[0]["AccName"];
   
   $stats = lastStatment($activeAccountID);
   
   if($stats["AccStated"]===null){ 
       echo "<br/>Ingen bekräftelse hittades för det enskilda kontot!"; 
       $stats["AccStat"] = 0;
   }
   else { echo "<br/>Bekräftat kontosaldo: ".$stats["AccStat"]." (".$stats["AccStated"].")"; }
   
   $curAccTot = transTot($activeAccountID,$stats["AccStated"],$currentTime);
      
   echo "<br/>Transaktioner sedan senaste bekräftelse: ".$curAccTot;
   
   echo "<br/>Beräknat kontosaldo (bekräftat saldo + transaktionsbelopp): ".($stats["AccStat"]+$curAccTot);
   
   echo "<br/>Aggregerat kontosaldo (inkludera kontosaldo från underställda konton): ".agrTot($activeAccountID,$stats["AccStated"],$currentTime);
   
   echo "<br/><br/>UNDERKONTON<hr/>";*/
   
   // Get the subaccount information
   
   $subAccs = getAccounts($activeAccountID);
     
   foreach ($subAccs as $acc){
       
       
      
       $stats = lastStatment($acc['AccountID']);
       
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
   

   
   echo "<TABLE><THEAD><TR><TD>ID</TD><TD>BDC</TD><TD>Name</TD><TD>Stated</TD><TD>Status</TD><TD>Trans</TD><TD>Total</TD><TD>AgrTot</TD></TR></THEAD><TBODY>"; 
   
   foreach($accounts as $acc){
       echo "<TR><TD>".$acc["AccID"]."</TD><TD>".$acc["BDC"]."</TD><TD>".$acc["Name"]."</TD><TD>".$acc["Stated"]."</TD><TD>".$acc["Status"]."</TD><TD>".$acc["Trans"]."</TD><TD>".$acc["Total"]."</TD><TD>".$acc["AgrTot"]."</TD></TR>";
   }

   
   echo "</TBODY>";
   echo "</TABLE>";   
   
   $db = null;
   
?>
