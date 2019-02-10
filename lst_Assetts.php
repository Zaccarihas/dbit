<?php
    $accs = array(
        0 => array(
        "bdc"       =>  "TK",
        "name"      =>  "Kontanter",
        "status"    =>  "1 209",
        "stated"    =>  "2019-02-01 11:34:00",
        "trans"     =>  "-300",
        "total"     =>  "909",
        "sub"       =>  array(  0 => array(
                                        "bdc"       =>  "TKA",
                                        "name"      =>  "Plånbok",
                                        "status"    =>  "709",
                                        "stated"    =>  "2019-02-01 11:34",
                                        "trans"     =>  "-350",
                                        "total"     =>  "359"
                                    ),
                                1 => array(
                                        "bdc"       =>  "TKB",
                                        "name"      =>  "Myntburk",
                                        "status"    =>  "500",
                                        "stated"    =>  "2019-02-01 11:34",
                                        "trans"     =>  "50",
                                        "total"     =>  "550"
                                ))
        
        ),
        1 => array(
        "bdc"       =>  "TB",
        "name"      =>  "Bank",
        "status"    =>  "12 345",
        "stated"    =>  "2019-01-15 12:20",
        "trans"     =>  "-3 802",
        "total"     =>  "8 543"
        )
  );
    
    function printhead(){
        echo "<DIV id='listhead'><DIV class='tools'></DIV><DIV class='bdc'>BDC</DIV><DIV class='name'>Kontonamn</DIV><DIV class='status'>Status</DIV><DIV class='stated'>Kontrollerad</DIV><DIV class='trans'>Trans</DIV><DIV class='total'>Total</DIV></DIV>";
    }
  
    function printlist($accounts){
        
        printhead();
        foreach($accounts as $id => $acc){
            echo "<DIV class='assett' id='blk_".$id."'>";
            
            // TOOLS
            echo "<DIV class='tools'>";
            
            // Om det finns underkonton så skriv ut en knapp
            if($array_key_exists("sub",$acc)){ echo "<BUTTON Type='button' Id='btn_".$id."'>></BUTTON>"; }
            
            echo "</DIV>";
            
            
            // BDC
            echo "<DIV class='bdc'>".$acc["bdc"]."</DIV>";
            
            // NAME
            echo "<DIV class='name'>".$acc["name"]."</DIV>";
            
            // STATUS
            echo "<DIV class='status sum'>".$acc["status"]."</DIV>";
            
            // STATED
            echo "<DIV class='stated statedvalue datetime'>".$acc["stated"]."</DIV>";
            
            // TRANSACTIONS
            echo "<DIV class='trans sum'>".$acc["trans"]."</DIV>";
            
            // TOTAL
            echo "<DIV class='total sum'>".$acc["total"]."</DIV>";
            
            // Om det finns subkonton ska dessa skrivas ut nu
            
            if($array_key_exists("sub",$acc)){
                echo "<DIV class='sub' id='sub_".$id."'>";
                printlist($acc["sub"]);
            }
            
            echo "</DIV>";
            
            
        }
    }
  
?>

<!DOCTYPE html>
<html>
    
      
    <head>
        <meta charset="utf-8" />
        <link rel="stylesheet" type="text/css" href="resources/stylesheets/dbit_assetts.css" />
        <script type="text/javascript" src="resources/scripts/js_general.js"></script>
    </head>
    
    <body>
    
    	
    	
    	<DIV class="assettlist">
    	   <?php printlist($accs); ?>
    	   
    	   <!-- 
    	   <DIV class="assett" id="kontanter">
    	    	<DIV class="tools"><BUTTON Type="button" onClick="shVis('subkontanter')">></BUTTON></DIV>
				<DIV class="bdc"><?php echo $accs[0]["bdc"] ?></DIV>
				<DIV class="name"><?php echo $accs[0]["name"] ?></DIV>
				<DIV class="status sum"><?php echo $accs[0]["status"] ?></DIV>
				<DIV class="stated statedvalue datetime"><?php echo $accs[0]["stated"] ?></DIV>
				<DIV class="trans sum"><?php echo $accs[0]["trans"] ?></DIV>
				<DIV class="total sum"><?php echo $accs[0]["total"] ?></DIV>
				<DIV class="sub" id="subkontanter">
    	    		<?php printhead(); ?>
					<DIV class="assett" id="planbok">
						<DIV class="tools"></DIV>
						<DIV class="bdc"><?php echo $accs[0]["sub"][0]["bdc"] ?></DIV>
						<DIV class="name"><?php echo $accs[0]["sub"][0]["name"] ?></DIV>
						<DIV class="status sum"><?php echo $accs[0]["sub"][0]["status"] ?></DIV>
						<DIV class="stated statedvalue datetime"><?php echo $accs[0]["sub"][0]["stated"] ?></DIV>
						<DIV class="trans sum"><?php echo $accs[0]["sub"][0]["trans"] ?></DIV>
						<DIV class="total sum"><?php echo $accs[0]["sub"][0]["total"] ?></DIV>
    	    		</DIV>
    	    		<DIV class="assett" id="burk">
    	    			<DIV class="tools"></DIV>
    	    			<DIV class="bdc"><?php echo $accs[0]["sub"][1]["bdc"] ?></DIV>
    	    			<DIV class="name"><?php echo $accs[0]["sub"][1]["name"] ?></DIV>
    	    			<DIV class="status sum"><?php echo $accs[0]["sub"][1]["status"] ?></DIV>
    	    			<DIV class="stated statedvalue datetime"><?php echo $accs[0]["sub"][1]["stated"] ?></DIV>
    	    			<DIV class="trans sum"><?php echo $accs[0]["sub"][1]["trans"] ?></DIV>
    	    			<DIV class="total sum"><?php echo $accs[0]["sub"][1]["total"] ?></DIV>
    	    		</DIV>
    	    	</DIV>
    	    </DIV>
    	    <DIV class="assett" id="bank">
    	    	<DIV class="tools"><BUTTON Type="button">></BUTTON></DIV>
    	    	<DIV class="bdc"><?php echo $accs[1]["bdc"] ?></DIV>
    	    	<DIV class="name"><?php echo $accs[1]["name"] ?></DIV>
    	    	<DIV class="status sum"><?php echo $accs[1]["status"] ?></DIV>
    	    	<DIV class="stated statedvalue datetime"><?php echo $accs[1]["stated"] ?></DIV>
    	    	<DIV class="trans sum"><?php echo $accs[1]["trans"] ?></DIV>
    	    	<DIV class="total sum"><?php echo $accs[1]["total"] ?></DIV>
    	    </DIV>
    	    -->
    	     
    	</DIV>
    	
    	
    
    </body>

</html>