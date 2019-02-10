<?php

    require_once 'resources/fnc_database.php';
    require_once 'resources/fnc_general.php';


?>

<HTML>
    <HEAD>

        <TITLE>Kontolista</TITLE>
        
        <LINK Rel="stylesheet" Type="text/css" href="resources/stylesheets/dbit_accountshortlist.css" />
        
    </HEAD>
    
    <BODY>
    
    	<H1>Account list - shortlist</H1>
    
    	<DIV Class="account" id="1">
    		<DIV Class="field bdc">TB</DIV>
    		<DIV Class="field name">Bank</DIV>
    		<DIV Class="field accid">12</DIV>
    		<DIV Class="field stated">2019-01-09 14:15:30</DIV>
    		<DIV Class="field status currency">4 251,23 SEK</DIV>
    		<DIV Class="field trans currency">-1255,00 SEK</DIV>
    		<DIV Class="field total currency">2 996,23 SEK</DIV>
    	
    		<DIV Class="account" id="2">
    			<DIV Class="field bdc">TBC</DIV>
    			<DIV Class="field name">Service konto</DIV>
    			<DIV Class="field accid">2</DIV>
    			<DIV Class="field stated">2019-01-05 23:09:04</DIV>
    			<DIV Class="field status currency">10 234,10 SEK</DIV>
    			<DIV Class="field trans currency">-3 412,54 SEK</DIV>
    			<DIV Class="field total currency">6 745,23 SEK</DIV>
    		</DIV>
    		
    		<DIV Class="account" id="17">
    			<DIV Class="field bdc">TBM</DIV>
    			<DIV Class="field name">Minuten</DIV>
    			<DIV Class="field accid">17</DIV>
    			<DIV Class="field stated">2019-01-04 12:05:38</DIV>
    			<DIV Class="field status currency">12 222,12 SEK</DIV>
    			<DIV Class="field trans currency">-93,00 SEK</DIV>
    			<DIV Class="field total currency">4 000,23 SEK</DIV>
    		</DIV>
      	
    	</DIV>
    	
    	
    	

    
    </BODY>
    
</HTML>

<?php $db = null; ?>