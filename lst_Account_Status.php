<?php
   
require_once 'resources/fnc_database.php';
require_once 'resources/fnc_general.php';

function recAgrTotal ($q_subAccs,$q_latestStat,$q_transTot,$acc,$start,$end){
    
    $q_transTot->execute(array($start,$end,$acc));
    $agrTot = $q_transTot->fetch();
    
    $q_subAccs->execute(array($acc));
    $subAccs = $q_subAccs->fetchAll();
    
    foreach($subAccs as $sub){
        $agrTot += recAgrTotal($q_subAccs,$q_latestStat,$q_transTot,$acc,$start,$end);
    }
    
    return $agrTot;
}

function agrTotal ($q_subAccs,$q_latestStat,$q_transTot,$acc = null,$startDate = null,$endDate=null){
    
    // If no startDate has been provided, set the startDate to the date and time of the latest statement
    if($startDate=null){
        $q_latestStat->execute(array($acc));
        $datelimit = $q_latestStat->fetch();
    }
    
    // If no endDate has been provided, set the endDate to the date and time to the current date and time
    if($endDate=null){
        $endDate = timestamp();
    }
    
    return  recAgrTotal ($q_subAccs,$q_latestStat,$q_transTot,$acc,$startDate,$endDate);
}

/*function getAccounts($q_subAccs,$q_latestStat,$q_transTot,$parent = null){
    
    $q_subAccs->execute(array($parent));
    $subAccs = $q_subAccs->fetchAll();
    
    $agrTot = 0;
    foreach($subAccs as $acc){
        echo "<TR><TD>".$acc['AccBDC']."</TD><TD>".$acc['AccName']."</TD>";
        
        $q_latestStat->execute(array($acc['AccountID']));
        $latestStat = $q_latestStat->fetchAll();
        
        echo "<TD>".$latestStat[0]['AccStated']."</TD><TD>".$latestStat[0]['AccStat']."</TD>";
        
        $q_transTot->execute(array($latestStat[0]['AccStated'],$acc['AccountID']));
        $transTot = $q_transTot->fetchAll();
        
        // detta kommer inte att fungera eftersom jag inte kan anropa rekursiviteten här då detta skulle innebära att nästa kontorad
        // skrivs in i cellan för agrTot. Jag måste beräkna Agrtot för sig och presentera hela raden i ett svep.
        
        
    }
    
    return $agrTot;
}*/
   
   $db = databaseconnect('lofqvist.dynu.net','dev','Av4rak1n','dbit');
   
   // Prepare needed qureies 
   
   $qry_subAccs =   $db->prepare("SELECT AccountID, AccBDC, AccName FROM tbl_accounts WHERE Parent = ?");
   $qry_latestStat= $db->prepare("SELECT MAX(Date) AS AccStated, Amount AS AccStat FROM tbl_statements WHERE Account = ?");
   $qry_transTot =  $db->prepare("SELECT SUM(trn.Amount) AS Trans FROM tbl_transactions AS trn LEFT JOIN tbl_occasions AS occ ON trn.Occasion = occ.ID WHERE occ.Date > ? AND occ.Date <= ? AND trn.Account = ?");
   
   echo "<h1>Welcome</h1>";
   echo "<div>The database is now open for business.</div><div>This is the requested information:</div>";
   
   echo "<TABLE><TBODY>"; 
   
   //presentAcc($qry_subAccs,$qry_latestStat,$qry_transTot);

   
   echo "</TBODY>";
   echo "</TABLE>";   
   
   $db = null;
   
?>
