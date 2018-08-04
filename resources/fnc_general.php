<?php

	/*
	*************************************************************************************************************
	
		GENERAL FUNCTIONS
		
		Generella funktioner skapade av Anders Löfqvist att användas i framförallt databasapplikationer:
		
		- TIMESTAMP()				Returnerar aktuell tid som en textsträng
		- LISTSORTED($list)			Kontrollerar om en lista är sorterad
		- BUBBLESORT($list)			Sorterar en lista
		- LEVREF					Skapar en nivåindeladnumrering
		- MESSAGEBOX($message)		Används för att visa ett meddelande i en dialog till användaren

		
	*************************************************************************************************************
	*/

	/*
	=============================================================================================================

		TIMESTAMP
	
		Syfte				Omvandlar den interna servertiden till ett tidstämplingsformat som kan registreras i 
							en mysql-databas.
		
		Resultat			return		sträng 		på formatet: YYYY-MM-DD TT:MM:SS
					
	=============================================================================================================
	*/
	function timestamp()
	{
		/*$now = getdate(time());
		$nowstring  = sprintf("%04d",$now[year])."-".sprintf("%02d",$now[mon])."-".sprintf("%02d",$now[mday])." ";
		$nowstring .= sprintf("%02d",$now[hours]).":".sprintf("%02d",$now[minutes]).":".sprintf("%02d",$now[seconds]);*/
		$now = new DateTime();
		return $now->format('Y-m-d H:i:s');
	} 
	// timestamp
	
		
	/*
	=============================================================================================================

		LISTSORTED 
	
		Syfte				Kontrollerar om en lista är sorterad. Är ännu ej testad på en flerdimensionell array 
							men klara både numeriska och alfanumeriska jämförelser.
		
		Parmetrar			$list		- 			Den lista som ska kontrolleras.
		
		Resultat			return		true 		om listan är sorterad
										false 		om listan är osorterad
					
	=============================================================================================================
	*/
	function listsorted_($list)
	{
		
		// Om listan är tom eller endast har ett element så är den sorterad.
		if (count($list) < 2) { $chk = true; } else {
							
			// Plocka ut första posten ur listan
			$item = array_shift($list);
			
			// Om den första posten är mindre än den andra och resten av listan är sorterad så är hela listan sorterad.	
			if (($item <= $list[0]) && listsorted($list)) { $chk = true; } else { $chk = false; }
		}
		
		return $chk; 
		
	}// End of LISTSORTED!
	
	
	/*
	=============================================================================================================

		BUBBLESORT 
	
			Syfte			Sorterar en list enligt bubblesort-principen
		
			Parametrar		$list		- 			Den lista som ska sorteras anges som referensparameter och 
													ändras alltså på ursprungsplatsen i minnet.
		
			Resultat		$list					Listan sorteras på ursprungsplatsen i minnet.
								
			Behov			fct_general.php			GENERAL FUNCTIONS
							--------------------
							listsorted				Kontrollera om listan är sorterad					
		
					
	=============================================================================================================
	*/
	function bubblesort(&$list){
			
		// Sätt en pekare på första posten
		$p = 0;
		
		// Avbryt sorteringen om listan är sorterad
		while ( ! listsorted($list)){
			
			// Är pekaren i slutet av listan så flytta den till början igen
			if ($p+1 == count($list)) { $p=0; }
			
			// Jämför nuvarande med nästa post och byt plats på dem om nuvarande post är större.
			else {
				if ($list[$p] > $list[$p+1]) { $post = $list[$p]; $list[$p]=$list[$p+1]; $list[$p+1]=$post; }
				$p = ++$p;
			}
		}
	
	}// End of BUBBLESORT!
	
	
	/*
	=============================================================================================================

		LEVELREF 
	
			Syfte			Skapar en nivåuppdelad numrering
		
			Parametrar		$lev		- 			Nivån på det objekt som ska numreras.
							$actlev		-			Den nivå som för senast användes vid numrering (skickas med som referens)
							$levC		-			Matris över räknare för samtliga nivåer (skickas som referens)
							$sepchar	-			Tecken eller text som ska separera nivåerna. Som förval används en punkt.
		
			Resultat		$levstr					Nivånumreringen som en text enl modellen "1.3.2.1" 
			
					
	=============================================================================================================
	*/	
	function levref($lev,&$aktlev,&$levC,$sepchar="."){
			
		// Om den aktuella nivån är en högre nivå en aktuellnivå så nollställ den högre nivåräknaren
		if ($lev > $aktlev){ $levC[$lev]=0; }
		
		// Sätt aktuellnivå till nivån på aktuellt objekt
		$aktlev = $lev;
		
		// Öka nivåräknaren för den aktuella nivån
		$levC[$aktlev]++;
		
		// Skapa numreringstexten genom att skriva ut alla nivåräknare ner till aktuell nivå separerde med separeringstecknet.
		$levstr = $levC[0];
		for ($i=1;$i<=$aktlev;$i++){
			$levstr .= $sepchar.$levC[$i];
		}
		
		return $levstr;	
		
	} // End of LEVREF
	
	
	/*
	=============================================================================================================

		MESSAGEBOX
	
			Syfte			Snabbt visa en dialog för användaren
		
			Parametrar		$message	- 			Meddelandet som ska visas
										
					
	=============================================================================================================
	*/	
	function messagebox($message){
	
		?>
			<script type="text/javascript">
				alert("<?php print $message ?>");
			</script>
			
		<?php

	}
	
	/*
	=============================================================================================================

		ARRAYPRINT
	
			Purpose			Print the content of an array in an ordered form
		
			Parameters		$mtrx	- 			The arrray being presented
										
					
	=============================================================================================================
	*/	
	function arrayprint($mtrx){
	
		print "<pre>"; print_r($mtrx); print "</pre>";

	}

?>