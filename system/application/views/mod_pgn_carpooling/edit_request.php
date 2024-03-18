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
		}
	);
	
	function frmrequest_onsubmit()
	{
		jQuery("#loader2").show();
		jQuery.post("<?=base_url()?>transporter/pgn_carpool/saveedit_request", jQuery("#frmrequest").serialize(),
			function(r)
			{
				jQuery("#loader2").hide();
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
<div id="main_data">
    <form id="frmrequest" onsubmit="javascript: return frmrequest_onsubmit(this)">		
        <table width="100%" cellpadding="3" class="table sortable no-margin" style="margin: 3px;">
            <input type="hidden" name="request_id" id="request_id" value="<?php echo $data->request_id; ?>" />
            <tr>
                <td style="text-align:left" colspan="2"><h2>Request Edit</h2></td>
            </tr>
			<tr style="border: 0px;">
					<td width="100" style="border: 0px;">
						NIK
					</td>
					<td style="border: 0px;">
						<input type="text" name="request_nik" id="request_nik" value="<?php if(isset($data)) { echo $data->request_nik; };?>"  />
					</td>
				</tr>

				<tr style="border: 0px;">
					<td width="100" style="border: 0px;">
						Name
					</td>
					<td style="border: 0px;">
						<input type="text" name="request_name" id="request_name" value="<?php if(isset($data)) { echo $data->request_name; };?>" />
					</td>
				</tr>

				<tr style="border: 0px;">
					<td width="100" style="border: 0px;">
						Date
					</td>
					<td style="border: 0px;">
						<input type="text" name="request_date" id="request_date" class="date-pick" value="<?php if(isset($data)) { echo date("d-m-Y",strtotime($data->request_date)); };?>" />
					</td>
				</tr>

				<tr style="border: 0px;">
					<td width="100" style="border: 0px;">
						Time
					</td>
					<td style="border: 0px;">
						<select style="font-size: 11px; width: 65px;" id="request_time" name="request_time">	
						<?php
						if(isset($data->request_time))
						{
						$mselected = "selected='selected'";
						}
						else
						{
						$mselected = "";
						}
						echo "<option value='" . $data->request_time ."' ". $mselected.">" . $data->request_time . "</option>";
						?>
						<option value="00:00">00:00</option>						                
						<option value="00:15">00:15</option>
						<option value="00:30">00:30</option>
						<option value="00:45">00:45</option>
						                    <option value="01:00">01:00</option>						                
											<option value="01:15">01:15</option>						              
											<option value="01:30">01:30</option>						                
											<option value="01:45">01:45</option>
						                    <option value="02:00">02:00</option>						                
											<option value="02:15">02:15</option>
											<option value="02:30">02:30</option>
											<option value="02:45">02:45</option>
						                    <option value="03:00">03:00</option>						                
											<option value="03:15">03:15</option>
											<option value="03:30">03:30</option>
											<option value="03:45">03:45</option>
						                    <option value="04:00">04:00</option>						                
											<option value="04:15">04:15</option>
											<option value="04:30">04:30</option>
											<option value="04:45">04:45</option>
						                    <option value="05:00">05:00</option>						                
											<option value="05:15">05:15</option>
											<option value="05:30">05:30</option>
											<option value="05:45">05:45</option>
						                    <option value="06:00">06:00</option>						                
											<option value="06:15">06:15</option>
											<option value="06:30">06:30</option>
											<option value="06:45">06:45</option>
						                    <option value="07:00">07:00</option>						                
											<option value="07:15">07:15</option>
											<option value="07:30">07:30</option>
											<option value="07:45">07:45</option>
						                    <option value="08:00">08:00</option>						                
											<option value="08:15">08:15</option>
											<option value="08:30">08:30</option>
											<option value="08:45">08:45</option>
						                    <option value="09:00">09:00</option>						                
											<option value="09:15">09:15</option>
											<option value="09:30">09:30</option>
											<option value="09:45">09:45</option>
						                    <option value="10:00">10:00</option>						                
											<option value="10:15">10:15</option>
											<option value="10:30">10:30</option>
											<option value="10:45">10:45</option>
						                    <option value="11:00">11:00</option>						                
											<option value="11:15">11:15</option>
											<option value="11:30">11:30</option>
											<option value="11:45">11:45</option>
						                    <option value="12:00">12:00</option>						                
											<option value="12:15">12:15</option>
											<option value="12:30">12:30</option>
											<option value="12:45">12:45</option>
						                    <option value="13:00">13:00</option>						                
											<option value="13:15">13:15</option>
											<option value="13:30">13:30</option>
											<option value="13:45">13:45</option>
						                    <option value="14:00">14:00</option>						                
											<option value="14:15">14:15</option>
											<option value="14:30">14:30</option>
											<option value="14:45">14:45</option>
						                    <option value="15:00">15:00</option>						                
											<option value="15:00">15:00</option>
											<option value="15:15">15:15</option>
											<option value="15:30">15:30</option>
											<option value="15:45">15:45</option>
						                    <option value="16:00">16:00</option>						                
											<option value="16:15">16:15</option>
											<option value="16:30">16:30</option>
											<option value="16:45">16:45</option>
						                    <option value="17:00">17:00</option>						                
											<option value="17:15">17:15</option>
											<option value="17:30">17:30</option>
											<option value="17:45">17:45</option>
						                    <option value="18:00">18:00</option>						                
											<option value="18:15">18:15</option>
											<option value="18:30">18:30</option>
											<option value="18:45">18:45</option>
						                    <option value="19:00">19:00</option>						                
											<option value="19:15">19:15</option>
											<option value="19:30">19:30</option>
											<option value="19:45">19:45</option>
						                    <option value="20:00">20:00</option>						                
											<option value="20:15">20:15</option>
											<option value="20:30">20:30</option>
											<option value="20:45">20:45</option>
						                    <option value="21:00">21:00</option>						                
											<option value="21:15">21:15</option>
											<option value="21:30">21:30</option>
											<option value="21:45">21:45</option>
						                    <option value="22:00">22:00</option>						                
											<option value="22:15">22:15</option>
											<option value="22:30">22:30</option>
											<option value="22:45">22:45</option>
						                    <option value="23:00">23:00</option>
											<option value="23:15">23:15</option>
											<option value="23:30">23:30</option>
											<option value="23:45">23:45</option>
						             </select>   
					</td>
				</tr>

				<tr style="border: 0px;">
					<td width="100" style="border: 0px;">
						Passenger
					</td>
					<td style="border: 0px;">
						<input type="text" name="request_passenger" id="request_passenger" value="<?php if(isset($data)) { echo $data->request_passenger; }?>" />
					</td>
				</tr>

				<tr style="border: 0px;">
					<td width="100" style="border: 0px;">
						Destination
					</td>
					<td style="border: 0px;">
						<input type="text" name="request_destination" id="request_destination" value="<?php if(isset($data)) { echo $data->request_destination; }?>" />
					</td>
				</tr>

				<tr style="border: 0px;">
					<td width="100"  style="border: 0px;vertical-align: middle;">
						Notes
					</td>
					<td style="border: 0px;" colspan="2">
						<textarea cols="50" rows="5" name="request_note" ><?php if(isset($data)) { echo $data->request_note; }?></textarea>
					</td>
				</tr>
				
				<tr><td>&nbsp;</td></tr>
    			<tr style="border: 0px;">
						<td style="border: 0px;" colspan="2">
								<input type="submit" name="btnsave" id="btnsave" value=" Save " />
								<input type="button" name="btncancel" id="btncancel" value=" Cancel " onclick="location='<?=base_url()?>transporter/pgn_carpool/mn_request';" />
								<img id="loader" src="<?=base_url();?>assets/images/ajax-loader.gif" border="0" alt="" title="" style="display:none;">
						</td>
				</tr>					
        </table>
    </form>
</div>