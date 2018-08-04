<?php
   
require_once 'resources/fnc_database.php';
   
   $db = databaseconnect('lofqvist.dynu.net','dev','Av4rak1n','dbit');
   
   echo "<h1>Welcome</h1>";
   echo "<div>The database is now open for business.</div><div>This is the requested information:</div>";
   
   
   $qry  = "SELECT tbl1.AccBDC, tbl1.AccName,tbl1.AccStated, tbl1.AccStat, tbl2.Trans, tbl1.AccStat + tbl2.Trans AS Total ";
   $qry .= "FROM (SELECT acc.AccountID AS Account, acc.AccBDC, acc.AccName, sts.AccStated, sts.AccStat FROM tbl_accounts AS acc ";
   $qry .= "LEFT JOIN (SELECT Account, MAX(Date) AS AccStated, Amount AS AccStat FROM tbl_statements GROUP BY Account) AS sts ";
   $qry .= "ON acc.AccountID = sts.Account) AS tbl1 ";
   $qry .= "LEFT JOIN (SELECT trn.Account,SUM(trn.Amount) AS Trans ";
   $qry .= "FROM tbl_transactions AS trn LEFT JOIN tbl_occasions AS occ ON trn.Occasion = occ.ID ";
   $qry .= "WHERE occ.Date > (SELECT AccStated FROM (SELECT Account, MAX(Date) AS AccStated FROM tbl_statements GROUP BY Account) AS sts ";
   $qry .= "WHERE trn.Account = sts.Account) GROUP BY trn.Account) AS tbl2 ON tbl1.Account = tbl2.Account";   
   
   
   $sth = $db->prepare($qry);
   $sth->execute();
   $res = $sth->fetchAll();
   
   
   //print_r($res);
   
   echo "<TABLE><TBODY>";   
   
   
   Foreach ($res as $acc){
       echo "<TR><TD>".$acc['AccBDC']."</TD><TD>".$acc['AccName']."</TD><TD>".$acc['AccStated']."</TD><TD>".$acc["AccStat"]."</TD><TD>".$acc["Trans"]."</TD><TD>".$acc["Total"]."</TD></TR>";
   }
   
   echo "</TBODY>";
   echo "</TABLE>";   
   
   $db = null;
   
?>
