<script>
jQuery.maxZIndex = jQuery.fn.maxZIndex = function(opt) {
	    var def = { inc: 10, group: "*" };
	    jQuery.extend(def, opt);
	    var zmax = 0;
	    jQuery(def.group).each(function() {
	        var cur = parseInt(jQuery(this).css('z-index'));
	        zmax = cur > zmax ? cur : zmax;
	    });
	    if (!this.jquery)

	        return zmax;
	
	    return this.each(function() {
	        zmax += def.inc;
	        jQuery(this).css("z-index", zmax);
	    });
	}
jQuery(document).ready(
		function()
		{
			showclock();
			jQuery("#startdate").datepicker(
				{
							dateFormat: 'yy-mm-dd'
						, 	startDate: '1900-01-01'
						, 	showOn: 'button'
						, 	changeYear: true
						,	changeMonth: true
						, 	buttonImage: '<?php echo base_url();?>assets/images/calendar.gif'
						, 	buttonImageOnly: true
						,	beforeShow: 
								function() 
								{	
									jQuery('#ui-datepicker-div').maxZIndex();
								}
				}
			);
	
		}
	);
	
function frmsave_onsubmit()
	{
		jQuery("#loader").show();
		jQuery.post("<?=base_url()?>bgn_muatan/update_muatan", jQuery("#frmsave").serialize(),	
			function(r)
			{
				jQuery("#loader").hide();
				if (r.error)
				{
					alert(r.message);
					return false;
				}
				
				alert(r.message);
				location = r.redirect;
			}
			, "json"	
		);
		return false;
	}
	
</script>
<div style="position: absolute; margin: 0; padding: 0; z-index: 1000; width: 100%;"> 
<?=$navigation;?>
	<div id="main" style="margin: 20px;">
		<div class="block-border">
		<form class="block-content form" name="frmsave" id="frmsave" onsubmit="javascript: return frmsave_onsubmit()">		
		<h1>Edit Data Muatan</h1>		
		<table width="100%" cellpadding="3" class="table sortable no-margin">
		<tr>
			<td colspan="4"><h2>Form Information</h2></td>
			<input type="hidden" name="id" id="id" value="<?php echo $row->muatan_id; ?>" />
			
		</tr>
		<tr>
			  <td>Vehicle</td>
			  <td>:</td>
			  <td>
                <select id="mobil_id" name="mobil_id" >
                <option value="" selected="selected">--Select Vehicle--</option>
								<?php 
									$cvehicle = count($rvehicle);
									for($i=0;$i<$cvehicle;$i++){
										if(isset($row) && $rvehicle[$i]->vehicle_id == $row->muatan_vehicle_id){
											
											$mselected = "selected='selected'";
										}else{
											$mselected = "";
										}
										
										echo "<option value='" . $rvehicle[$i]->vehicle_id ."' ". $mselected.">" . $rvehicle[$i]->vehicle_no ." - ".$rvehicle[$i]->vehicle_name . "</option>";
						
									} ?>
									
							</select></td>
		</tr>
		<tr>
			  <td>Driver</td>
			  <td>:</td>
			  <td>
                <select id="driver" name="driver" >
                <option value="" selected="selected">--Select Driver--</option>
								<?php 
									$cdriver = count($rdriver);
									for($i=0;$i<$cdriver;$i++){
										if(isset($row) && $rdriver[$i]->driver_id == $row->muatan_driver){
											
											$mselected = "selected='selected'";
										}else{
											$mselected = "";
										}
										
										echo "<option value='" . $rdriver[$i]->driver_id ."' ". $mselected.">" . $rdriver[$i]->driver_name."</option>";
						
									} ?>
									
							</select></td>
		</tr>
		
        <tr>
          <td>Datetime</td>
          <td>:</td>
		  <td>
          	<input type='text' readonly name="startdate"  id="startdate" value="<?=isset($row) ? htmlspecialchars($row->muatan_startdate, ENT_QUOTES) : "";?>"  maxlength='10' style="width:150px;">
			<select class="textgray" style="font-size: 11px; width: 65px;" id="starttime" name="starttime" >						                
						                    <option value="00:00" <? if (( isset($row)) &&  substr($row->muatan_starttime, 0, 5) == '00:00') { ?>selected<?php } ?>>00:00</option>
											<option value="00:15" <? if (( isset($row)) &&  substr($row->muatan_starttime, 0, 5) == '00:15') { ?>selected<?php } ?>>00:15</option>
											<option value="00:30" <? if (( isset($row)) &&  substr($row->muatan_starttime, 0, 5) == '00:30') { ?>selected<?php } ?>>00:30</option>
											<option value="00:45" <? if (( isset($row)) &&  substr($row->muatan_starttime, 0, 5) == '00:45') { ?>selected<?php } ?>>00:45</option>
						                    <option value="01:00" <? if (( isset($row)) &&  substr($row->muatan_starttime, 0, 5) == '01:00') { ?>selected<?php } ?>>01:00</option>						                
											<option value="01:15" <? if (( isset($row)) &&  substr($row->muatan_starttime, 0, 5) == '01:15') { ?>selected<?php } ?>>01:15</option>						              
											<option value="01:30" <? if (( isset($row)) &&  substr($row->muatan_starttime, 0, 5) == '01:30') { ?>selected<?php } ?>>01:30</option>						                
											<option value="01:45" <? if (( isset($row)) &&  substr($row->muatan_starttime, 0, 5) == '01:45') { ?>selected<?php } ?>>01:45</option>
						                    <option value="02:00" <? if (( isset($row)) &&  substr($row->muatan_starttime, 0, 5) == '02:00') { ?>selected<?php } ?>>02:00</option>						                
											<option value="02:15" <? if (( isset($row)) &&  substr($row->muatan_starttime, 0, 5) == '02:15') { ?>selected<?php } ?>>02:15</option>
											<option value="02:30" <? if (( isset($row)) &&  substr($row->muatan_starttime, 0, 5) == '02:30') { ?>selected<?php } ?>>02:30</option>
											<option value="02:45" <? if (( isset($row)) &&  substr($row->muatan_starttime, 0, 5) == '02:45') { ?>selected<?php } ?>>02:45</option>
						                    <option value="03:00" <? if (( isset($row)) &&  substr($row->muatan_starttime, 0, 5) == '03:00') { ?>selected<?php } ?>>03:00</option>						                
											<option value="03:15" <? if (( isset($row)) &&  substr($row->muatan_starttime, 0, 5) == '03:15') { ?>selected<?php } ?>>03:15</option>
											<option value="03:30" <? if (( isset($row)) &&  substr($row->muatan_starttime, 0, 5) == '03:30') { ?>selected<?php } ?>>03:30</option>
											<option value="03:45" <? if (( isset($row)) &&  substr($row->muatan_starttime, 0, 5) == '03:45') { ?>selected<?php } ?>>03:45</option>
						                    <option value="04:00" <? if (( isset($row)) &&  substr($row->muatan_starttime, 0, 5) == '04:00') { ?>selected<?php } ?>>04:00</option>						                
											<option value="04:15" <? if (( isset($row)) &&  substr($row->muatan_starttime, 0, 5) == '04:15') { ?>selected<?php } ?>>04:15</option>
											<option value="04:30" <? if (( isset($row)) &&  substr($row->muatan_starttime, 0, 5) == '04:30') { ?>selected<?php } ?>>04:30</option>
											<option value="04:45" <? if (( isset($row)) &&  substr($row->muatan_starttime, 0, 5) == '04:45') { ?>selected<?php } ?>>04:45</option>
						                    <option value="05:00" <? if (( isset($row)) &&  substr($row->muatan_starttime, 0, 5) == '05:00') { ?>selected<?php } ?>>05:00</option>						                
											<option value="05:15" <? if (( isset($row)) &&  substr($row->muatan_starttime, 0, 5) == '05:15') { ?>selected<?php } ?>>05:15</option>
											<option value="05:30" <? if (( isset($row)) &&  substr($row->muatan_starttime, 0, 5) == '05:30') { ?>selected<?php } ?>>05:30</option>
											<option value="05:45" <? if (( isset($row)) &&  substr($row->muatan_starttime, 0, 5) == '05:45') { ?>selected<?php } ?>>05:45</option>
						                    <option value="06:00" <? if (( isset($row)) &&  substr($row->muatan_starttime, 0, 5) == '06:00') { ?>selected<?php } ?>>06:00</option>						                
											<option value="06:15" <? if (( isset($row)) &&  substr($row->muatan_starttime, 0, 5) == '06:15') { ?>selected<?php } ?>>06:15</option>
											<option value="06:30" <? if (( isset($row)) &&  substr($row->muatan_starttime, 0, 5) == '06:30') { ?>selected<?php } ?>>06:30</option>
											<option value="06:45" <? if (( isset($row)) &&  substr($row->muatan_starttime, 0, 5) == '06:45') { ?>selected<?php } ?>>06:45</option>
						                    <option value="07:00" <? if (( isset($row)) &&  substr($row->muatan_starttime, 0, 5) == '07:00') { ?>selected<?php } ?>>07:00</option>						                
											<option value="07:15" <? if (( isset($row)) &&  substr($row->muatan_starttime, 0, 5) == '07:15') { ?>selected<?php } ?>>07:15</option>
											<option value="07:30" <? if (( isset($row)) &&  substr($row->muatan_starttime, 0, 5) == '07:30') { ?>selected<?php } ?>>07:30</option>
											<option value="07:45" <? if (( isset($row)) &&  substr($row->muatan_starttime, 0, 5) == '07:45') { ?>selected<?php } ?>>07:45</option>
						                    <option value="08:00" <? if (( isset($row)) &&  substr($row->muatan_starttime, 0, 5) == '08:00') { ?>selected<?php } ?>>08:00</option>						                
											<option value="08:15" <? if (( isset($row)) &&  substr($row->muatan_starttime, 0, 5) == '08:15') { ?>selected<?php } ?>>08:15</option>
											<option value="08:30" <? if (( isset($row)) &&  substr($row->muatan_starttime, 0, 5) == '08:30') { ?>selected<?php } ?>>08:30</option>
											<option value="08:45" <? if (( isset($row)) &&  substr($row->muatan_starttime, 0, 5) == '08:45') { ?>selected<?php } ?>>08:45</option>
						                    <option value="09:00" <? if (( isset($row)) &&  substr($row->muatan_starttime, 0, 5) == '09:00') { ?>selected<?php } ?>>09:00</option>						                
											<option value="09:15" <? if (( isset($row)) &&  substr($row->muatan_starttime, 0, 5) == '09:15') { ?>selected<?php } ?>>09:15</option>
											<option value="09:30" <? if (( isset($row)) &&  substr($row->muatan_starttime, 0, 5) == '09:30') { ?>selected<?php } ?>>09:30</option>
											<option value="09:45" <? if (( isset($row)) &&  substr($row->muatan_starttime, 0, 5) == '09:45') { ?>selected<?php } ?>>09:45</option>
						                    <option value="10:00" <? if (( isset($row)) &&  substr($row->muatan_starttime, 0, 5) == '10:00') { ?>selected<?php } ?>>10:00</option>						                
											<option value="10:15" <? if (( isset($row)) &&  substr($row->muatan_starttime, 0, 5) == '10:15') { ?>selected<?php } ?>>10:15</option>
											<option value="10:30" <? if (( isset($row)) &&  substr($row->muatan_starttime, 0, 5) == '10:30') { ?>selected<?php } ?>>10:30</option>
											<option value="10:45" <? if (( isset($row)) &&  substr($row->muatan_starttime, 0, 5) == '10:45') { ?>selected<?php } ?>>10:45</option>
						                    <option value="11:00" <? if (( isset($row)) &&  substr($row->muatan_starttime, 0, 5) == '11:00') { ?>selected<?php } ?>>11:00</option>						                
											<option value="11:15" <? if (( isset($row)) &&  substr($row->muatan_starttime, 0, 5) == '11:15') { ?>selected<?php } ?>>11:15</option>
											<option value="11:30" <? if (( isset($row)) &&  substr($row->muatan_starttime, 0, 5) == '11:30') { ?>selected<?php } ?>>11:30</option>
											<option value="11:45" <? if (( isset($row)) &&  substr($row->muatan_starttime, 0, 5) == '11:45') { ?>selected<?php } ?>>11:45</option>
						                    <option value="12:00" <? if (( isset($row)) &&  substr($row->muatan_starttime, 0, 5) == '12:00') { ?>selected<?php } ?>>12:00</option>						                
											<option value="12:15" <? if (( isset($row)) &&  substr($row->muatan_starttime, 0, 5) == '12:15') { ?>selected<?php } ?>>12:15</option>
											<option value="12:30" <? if (( isset($row)) &&  substr($row->muatan_starttime, 0, 5) == '12:30') { ?>selected<?php } ?>>12:30</option>
											<option value="12:45" <? if (( isset($row)) &&  substr($row->muatan_starttime, 0, 5) == '12:45') { ?>selected<?php } ?>>12:45</option>
						                    <option value="13:00" <? if (( isset($row)) &&  substr($row->muatan_starttime, 0, 5) == '13:00') { ?>selected<?php } ?>>13:00</option>						                
											<option value="13:15" <? if (( isset($row)) &&  substr($row->muatan_starttime, 0, 5) == '13:15') { ?>selected<?php } ?>>13:15</option>
											<option value="13:30" <? if (( isset($row)) &&  substr($row->muatan_starttime, 0, 5) == '13:30') { ?>selected<?php } ?>>13:30</option>
											<option value="13:45" <? if (( isset($row)) &&  substr($row->muatan_starttime, 0, 5) == '13:45') { ?>selected<?php } ?>>13:45</option>
						                    <option value="14:00" <? if (( isset($row)) &&  substr($row->muatan_starttime, 0, 5) == '14:00') { ?>selected<?php } ?>>14:00</option>						                
											<option value="14:15" <? if (( isset($row)) &&  substr($row->muatan_starttime, 0, 5) == '14:15') { ?>selected<?php } ?>>14:15</option>
											<option value="14:30" <? if (( isset($row)) &&  substr($row->muatan_starttime, 0, 5) == '14:30') { ?>selected<?php } ?>>14:30</option>
											<option value="14:45" <? if (( isset($row)) &&  substr($row->muatan_starttime, 0, 5) == '14:45') { ?>selected<?php } ?>>14:45</option>
						                    <option value="15:00" <? if (( isset($row)) &&  substr($row->muatan_starttime, 0, 5) == '15:00') { ?>selected<?php } ?>>15:00</option>						                
											<option value="15:15" <? if (( isset($row)) &&  substr($row->muatan_starttime, 0, 5) == '15:15') { ?>selected<?php } ?>>15:15</option>
											<option value="15:30" <? if (( isset($row)) &&  substr($row->muatan_starttime, 0, 5) == '15:30') { ?>selected<?php } ?>>15:30</option>
											<option value="15:45" <? if (( isset($row)) &&  substr($row->muatan_starttime, 0, 5) == '15:45') { ?>selected<?php } ?>>15:45</option>
						                    <option value="16:00" <? if (( isset($row)) &&  substr($row->muatan_starttime, 0, 5) == '16:00') { ?>selected<?php } ?>>16:00</option>						                
											<option value="16:15" <? if (( isset($row)) &&  substr($row->muatan_starttime, 0, 5) == '16:15') { ?>selected<?php } ?>>16:15</option>
											<option value="16:30" <? if (( isset($row)) &&  substr($row->muatan_starttime, 0, 5) == '16:30') { ?>selected<?php } ?>>16:30</option>
											<option value="16:45" <? if (( isset($row)) &&  substr($row->muatan_starttime, 0, 5) == '16:45') { ?>selected<?php } ?>>16:45</option>
						                    <option value="17:00" <? if (( isset($row)) &&  substr($row->muatan_starttime, 0, 5) == '17:00') { ?>selected<?php } ?>>17:00</option>						                
											<option value="17:15" <? if (( isset($row)) &&  substr($row->muatan_starttime, 0, 5) == '17:15') { ?>selected<?php } ?>>17:15</option>
											<option value="17:30" <? if (( isset($row)) &&  substr($row->muatan_starttime, 0, 5) == '17:30') { ?>selected<?php } ?>>17:30</option>
											<option value="17:45" <? if (( isset($row)) &&  substr($row->muatan_starttime, 0, 5) == '17:45') { ?>selected<?php } ?>>17:45</option>
						                    <option value="18:00" <? if (( isset($row)) &&  substr($row->muatan_starttime, 0, 5) == '18:00') { ?>selected<?php } ?>>18:00</option>						                
											<option value="18:15" <? if (( isset($row)) &&  substr($row->muatan_starttime, 0, 5) == '18:15') { ?>selected<?php } ?>>18:15</option>
											<option value="18:30" <? if (( isset($row)) &&  substr($row->muatan_starttime, 0, 5) == '18:30') { ?>selected<?php } ?>>18:30</option>
											<option value="18:45" <? if (( isset($row)) &&  substr($row->muatan_starttime, 0, 5) == '18:45') { ?>selected<?php } ?>>18:45</option>
						                    <option value="19:00" <? if (( isset($row)) &&  substr($row->muatan_starttime, 0, 5) == '19:00') { ?>selected<?php } ?>>19:00</option>						                
											<option value="19:15" <? if (( isset($row)) &&  substr($row->muatan_starttime, 0, 5) == '19:15') { ?>selected<?php } ?>>19:15</option>
											<option value="19:30" <? if (( isset($row)) &&  substr($row->muatan_starttime, 0, 5) == '19:30') { ?>selected<?php } ?>>19:30</option>
											<option value="19:45" <? if (( isset($row)) &&  substr($row->muatan_starttime, 0, 5) == '19:45') { ?>selected<?php } ?>>19:45</option>
						                    <option value="20:00" <? if (( isset($row)) &&  substr($row->muatan_starttime, 0, 5) == '20:00') { ?>selected<?php } ?>>20:00</option>						                
											<option value="20:15" <? if (( isset($row)) &&  substr($row->muatan_starttime, 0, 5) == '20:15') { ?>selected<?php } ?>>20:15</option>
											<option value="20:30" <? if (( isset($row)) &&  substr($row->muatan_starttime, 0, 5) == '20:30') { ?>selected<?php } ?>>20:30</option>
											<option value="20:45" <? if (( isset($row)) &&  substr($row->muatan_starttime, 0, 5) == '20:45') { ?>selected<?php } ?>>20:45</option>
						                    <option value="21:00" <? if (( isset($row)) &&  substr($row->muatan_starttime, 0, 5) == '21:00') { ?>selected<?php } ?>>21:00</option>						                
											<option value="21:15" <? if (( isset($row)) &&  substr($row->muatan_starttime, 0, 5) == '21:15') { ?>selected<?php } ?>>21:15</option>
											<option value="21:30" <? if (( isset($row)) &&  substr($row->muatan_starttime, 0, 5) == '21:30') { ?>selected<?php } ?>>21:30</option>
											<option value="21:45" <? if (( isset($row)) &&  substr($row->muatan_starttime, 0, 5) == '21:45') { ?>selected<?php } ?>>21:45</option>
						                    <option value="22:00" <? if (( isset($row)) &&  substr($row->muatan_starttime, 0, 5) == '22:00') { ?>selected<?php } ?>>22:00</option>						                
											<option value="22:15" <? if (( isset($row)) &&  substr($row->muatan_starttime, 0, 5) == '22:15') { ?>selected<?php } ?>>22:15</option>
											<option value="22:30" <? if (( isset($row)) &&  substr($row->muatan_starttime, 0, 5) == '22:30') { ?>selected<?php } ?>>22:30</option>
											<option value="22:45" <? if (( isset($row)) &&  substr($row->muatan_starttime, 0, 5) == '22:45') { ?>selected<?php } ?>>22:45</option>
						                    <option value="23:00" <? if (( isset($row)) &&  substr($row->muatan_starttime, 0, 5) == '23:00') { ?>selected<?php } ?>>23:00</option>
											<option value="23:15" <? if (( isset($row)) &&  substr($row->muatan_starttime, 0, 5) == '23:15') { ?>selected<?php } ?>>23:15</option>
											<option value="23:30" <? if (( isset($row)) &&  substr($row->muatan_starttime, 0, 5) == '23:30') { ?>selected<?php } ?>>23:30</option>
											<option value="23:45" <? if (( isset($row)) &&  substr($row->muatan_starttime, 0, 5) == '23:45') { ?>selected<?php } ?>>23:45</option>
											<option value="23:59" <? if (( isset($row)) &&  substr($row->muatan_starttime, 0, 5) == '23:59') { ?>selected<?php } ?>>23:59</option>
						             </select>  
            
		  </td>
        </tr>
		<tr>
			  <td>Muatan</td>
			  <td>:</td>
			  <td>
                <select id="datamuatan" name="datamuatan" >
                <option value="" selected="selected">-- Muatan --</option>
								<?php 
									$cdatamuatan = count($rdatamuatan);
									for($i=0;$i<$cdatamuatan;$i++){
										if(isset($row) && $rdatamuatan[$i]->muatan_data_id == $row->muatan_data){
											
											$mselected = "selected='selected'";
										}else{
											$mselected = "";
										}
										
										echo "<option value='" . $rdatamuatan[$i]->muatan_data_id ."' ". $mselected.">" . $rdatamuatan[$i]->muatan_data_name."</option>";
						
									} ?>
									
							</select></td>
		</tr>
		<tr>
          <td>Weight (ex: 3 ton / 10 kg)</td>
          <td>:</td>
		  <td>
			<input type="text" name="weight" id="weight" size="45" value= "<?=isset($row) ? htmlspecialchars($row->muatan_weight, ENT_QUOTES) : "";?>" /></input>
		  </td>
        </tr>
		
		<tr>
          <td>Destination</td>
          <td>:</td>
		  <td>
			<input type="text" name="dest" id="dest" size="45" value= "<?=isset($row) ? htmlspecialchars($row->muatan_dest, ENT_QUOTES) : "";?>" /></input>
		  </td>
        </tr>
		
		<tr>
          <td>Notes</td>
          <td>:</td>
		  <td>
          	<input type='text' name="note" id="note" size="45" value="<?=isset($row) ? htmlspecialchars($row->muatan_note, ENT_QUOTES) : "";?>">
		  </td>
        </tr>
		
    			<tr>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>
								<input type="submit" name="btnsave" id="btnsave" value=" Update " />
								<input type="button" name="btncancel" id="btncancel" value=" Cancel " onclick="location='<?=base_url()?>bgn_muatan';" />
								<img id="loader" src="<?=base_url();?>assets/images/ajax-loader.gif" border="0" alt="" title="" style="display:none;">
						</td>
					</tr>					
				</table>
			</form>		
		</div>
	</div>
</div>
			
