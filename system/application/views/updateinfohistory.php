  			var gtminfo = null;
        	var glastlat = null;
        	var glastlon = null;        
        	var gcar = null;
        	 			
  			function updateLocation(device, timer)
  			{  		  					  				
  				jQuery.post("<?=base_url();?>map/historyinfo/", {device: device, gpsid: <?=$gpsid?>},
  					function(r)
  					{ 
  						update(r);
  					}
  					, "json"
  				);  				
	      	}