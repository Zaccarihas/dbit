<?php
   
require_once 'resources/fnc_database.php';

function getAccounts($q_subAccs,$q_latestStat,$q_transTot,$parent = null){
    
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
}
   
   $db = databaseconnect('lofqvist.dynu.net','dev','Av4rak1n','dbit');
   
   echo "<h1>Welcome</h1>";
   echo "<div>The database is now open for business.</div><div>This is the requested information:</div>";
   
   
   
   /* Alternativ ett - Att hämta ut så mycket som möjligt med en stor SQL-fråga.
    * 
    *    Detta verkar dock inte som en bra ide då frågan blir svår att tyda och min nuvarande
    *    databas inte stöd för rekursiva frågor (mariadb 10.1)    * 
    * 
   
   $qry  = "SELECT tbl1.AccBDC, tbl1.AccName,tbl1.AccStated, tbl1.AccStat, tbl2.Trans, tbl1.AccStat + tbl2.Trans AS Total ";
   $qry .= "FROM (SELECT acc.AccountID AS Account, acc.AccBDC, acc.AccName, sts.AccStated, sts.AccStat FROM tbl_accounts AS acc ";
   $qry .= "LEFT JOIN (SELECT Account, MAX(Date) AS AccStated, Amount AS AccStat FROM tbl_statements GROUP BY Account) AS sts ";
   $qry .= "ON acc.AccountID = sts.Account) AS tbl1 ";
   $qry .= "LEFT JOIN (SELECT trn.Account,SUM(trn.Amount) AS Trans ";
   $qry .= "FROM tbl_transactions AS trn LEFT JOIN tbl_occasions AS occ ON trn.Occasion = occ.ID ";
   $qry .= "WHERE occ.Date > (SELECT AccStated FROM (SELECT Account, MAX(Date) AS AccStated FROM tbl_statements GROUP BY Account) AS sts ";
   $qry .= "WHERE trn.Account = sts.Account) GROUP BY trn.Account) AS tbl2 ON tbl1.Account = tbl2.Account";    */
   
   
   /* Alternativ två - Hämta ut alla toppkonton och iterera över dessa.
    * 
    *    Inom iterationen så beräknas aggregerat resultat från under konton
    * 
    */
   
   // Prepare needed qureies 
   
   $qry_subAccs =   $db->prepare("SELECT AccountID, AccBDC, AccName FROM tbl_accounts WHERE Parent = ?");
   $qry_latestStat= $db->prepare("SELECT MAX(Date) AS AccStated, Amount AS AccStat FROM tbl_statements WHERE Account = ?");
   $qry_transTot =  $db->prepare("SELECT SUM(trn.Amount) AS Trans FROM tbl_transactions AS trn LEFT JOIN tbl_occasions AS occ ON trn.Occasion = occ.ID WHERE occ.Date > ? AND trn.Account = ?";
   
   echo "<TABLE><TBODY>"; 
   
   presentAcc($qry_subAccs,$qry_latestStat,$qry_transTot);

   
   echo "</TBODY>";
   echo "</TABLE>";   
   
   $db = null;
   
?>
