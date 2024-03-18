  			var gtminfo = null;
        	var glastlat = null;
        	var glastlon = null;        
        	var gcar = null;
        	var glasttime = 0;
        	var gtimer = <?=$this->config->item('timer_realtime')?>;
  			
  			function updateLocation_realtime()
  			{  		  	
				var device = "<?=$data->vehicle_device;?>";
  				jQuery.post("<?=base_url();?>map/lastinfo", {device: device, lasttime: glasttime},
  					function(r)
  					{ 
		        		if (gtminfo)
		        		{
		        			clearTimeout(gtminfo);
		        		}		        	

  						if (! r)
  						{
  							gtminfo = setTimeout("updateLocation_realtime('" + device + "')", gtimer);
  							return;
  						}
  						
  						
						var nextdevice = update_realtime(r);
						
  						if (nextdevice)
  						{
  							//gtminfo = setTimeout("updateLocation_realtime('" + nextdevice + "')", gtimer)
  							return;
  						}
  						
		        		gtminfo = setTimeout("updateLocation_realtime('" + device + "')", gtimer);
					
  					}
  					, "json"
  				);  				
	      	}
