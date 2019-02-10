/****************************************************************************************

	General Functions

****************************************************************************************/

/*-------------------------------------------------------------------------------------
shiftVisibility

	Purpose			Shift between hide and revealed mode of a selected block item
	
	Parameters		block		- 	The ID of the block-item to be either hidden or 
									revealed
							  
					image		- 	The ID of the image-element that is used to trigger 
									the shift (button)
					
					shiftrevd	-	URL to the image file used for the reveal version
									of the image-element.
									
					shifthide	- 	URL to the image file used for the hide version
									of the image-element.
								
---------------------------------------------------------------------------------------*/
function shiftVisibility(block,image,shiftrevd,shifthide) {
	
	   // Shift the visibility of the element with the selected id
	    var e = document.getElementById(block);
       if(e.style.display == 'block')
          e.style.display = 'none';
       else
          e.style.display = 'block';
	   	   
	   var e = document.getElementById(image);
       
	   if(e.src == shiftrevd)
		   e.src = shifthide;
       else
          e.src = shiftrevd;
	  
} // shiftVisibility

/*-------------------------------------------------------------------------------------
shVis

	Purpose			Shift between hide and revealed mode of a selected block item
	
	Parameters		block		- 	The ID of the block-item to be either hidden or 
									revealed
							  
								
---------------------------------------------------------------------------------------*/
function shVis(block) {
	
	   // Shift the visibility of the element with the selected id
	   var e = document.getElementById(block);
       if(e.style.display == 'block')
          e.style.display = 'none';
       else
          e.style.display = 'block';
	  
} // shVis

/*-------------------------------------------------------------------------------------
getHTTPReq

	Purpose			Create an object for HTTP-request disregarding the current browser
	
--------------------------------------------------------------------------------------*/
function getHTTPReq(){
		
	var httpReq;
	
	if (window.XMLHttpRequest){			// code for IE7+, Firefox, Chrome, Opera, Safari
		httpReq=new XMLHttpRequest();
	} else {							// code for IE6, IE5
		httpReq=new ActiveXObject("Microsoft.XMLHTTP");
	}
	
	return httpReq;
}


/*-------------------------------------------------------------------------------------
callAjax

	Purpose			Creates a HTTP-request call with a response handlar.
	
	Parameters		url	-	The url to the page containing the AJAX-method
							A random seed t will be added in the GET-call to prevent
							the result being added from the cashed pages
							
					cfunc-	The function that handles the response from the request.
	
--------------------------------------------------------------------------------------*/
function callAjax(url,cfunc)
{
	httpReq = getHTTPReq();
	httpReq.onreadystatechange = function() {
		if(httpReq.readyState==4 && httpReq.status==200){	
			cfunc().call;
		}
	}
	
	var sepsign;
	sepsign = (url.indexOf('?')===-1)?"?":"&";
	
	httpReq.open("GET",url + sepsign + "t=" + Math.random(),true);
	httpReq.send();
}

function testcall_to_general(){
	document.write('This is printed from within a script in the js_general.js file');
	alert('This is printed from within a script in the js_general.js file');
}
