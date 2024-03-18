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
			jQuery("#date").datepicker(
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
	
function frm_onsubmit()
{

	jQuery("#loader2").show();
	jQuery.post("<?=base_url()?>tcont_jobfile/save_delivered", jQuery("#frm").serialize(),
	function(r)
	{
		jQuery("#loader2").hide();
		alert(r.message);
								
								if (r.error)
								{								
									return;									
								}								
								page();
								jQuery("#dialog").dialog("close");
							}
							, "json"
						);
						
						return false;
	
}
</script>
<div class="block-border">
<form class="block-content form" id="frm" onsubmit="javascript: return frm_onsubmit()">		
<input type="hidden" name="job_id" id="job_id" value="<?=isset($row) ? $row->transporter_job_id : 0;?>"/>

<table width="100%">
	<tr>
		<td colspan="7"><h2>Schedule Info [ Status: 
			<?php if($row->transporter_job_status == 1)
			{
				echo "";
			}
			if ($row->transporter_job_status == 2)
			{
				echo "Delivered";
			}
			
			?> ]</h2>
		</td>
	</tr>
	<tr><td>&nbsp;</td></tr>
	<tr>
		<td>Job Number</td>
		<td>:</td>
		<td><?=$row->transporter_job_number;?></td>
	</tr>
	<tr><td>&nbsp;</td></tr>
	<tr>
		<td width="14%">Start Date</td>
		<td width="1%">:</td>
		<td><?=date("d-m-Y H:i",strtotime($row->transporter_job_deliv_date." ".$row->transporter_job_deliv_time)) ?></td>
	</tr>
	<tr><td>&nbsp;</td></tr>
	<tr>
		<td>Vehicle</td>
		<td>:</td>
		<td>[<?=$row->transporter_job_vehicle_no;?>] <?=$row->transporter_job_vehicle_name;?></td>
	</tr>	
	<tr><td>&nbsp;</td></tr>
	<tr>
		<td>Driver</td>
		<td>:</td>
		<td><?=$row->driver_name;?></td>
	</tr>
	<tr><td>&nbsp;</td></tr>
	
	<tr>
		<td>Delivered Time</td>
        <td>:</td>
		<td>
          	<input type='text' readonly name="date"  id="date" value="<?=isset($row) ? htmlspecialchars($row->transporter_job_deliv_date, ENT_QUOTES) : "";?>"  maxlength='10' style="width:150px;">
			<select class="textgray" style="font-size: 11px; width: 65px;" id="time" name="time" >						                
						                    <option value="00:00" <? if (( isset($row)) &&  substr($row->transporter_job_deliv_time, 0, 5) == '00:00') { ?>selected<?php } ?>>00:00</option>
											<option value="00:15" <? if (( isset($row)) &&  substr($row->transporter_job_deliv_time, 0, 5) == '00:15') { ?>selected<?php } ?>>00:15</option>
											<option value="00:30" <? if (( isset($row)) &&  substr($row->transporter_job_deliv_time, 0, 5) == '00:30') { ?>selected<?php } ?>>00:30</option>
											<option value="00:45" <? if (( isset($row)) &&  substr($row->transporter_job_deliv_time, 0, 5) == '00:45') { ?>selected<?php } ?>>00:45</option>
						                    <option value="01:00" <? if (( isset($row)) &&  substr($row->transporter_job_deliv_time, 0, 5) == '01:00') { ?>selected<?php } ?>>01:00</option>						                
											<option value="01:15" <? if (( isset($row)) &&  substr($row->transporter_job_deliv_time, 0, 5) == '01:15') { ?>selected<?php } ?>>01:15</option>						              
											<option value="01:30" <? if (( isset($row)) &&  substr($row->transporter_job_deliv_time, 0, 5) == '01:30') { ?>selected<?php } ?>>01:30</option>						                
											<option value="01:45" <? if (( isset($row)) &&  substr($row->transporter_job_deliv_time, 0, 5) == '01:45') { ?>selected<?php } ?>>01:45</option>
						                    <option value="02:00" <? if (( isset($row)) &&  substr($row->transporter_job_deliv_time, 0, 5) == '02:00') { ?>selected<?php } ?>>02:00</option>						                
											<option value="02:15" <? if (( isset($row)) &&  substr($row->transporter_job_deliv_time, 0, 5) == '02:15') { ?>selected<?php } ?>>02:15</option>
											<option value="02:30" <? if (( isset($row)) &&  substr($row->transporter_job_deliv_time, 0, 5) == '02:30') { ?>selected<?php } ?>>02:30</option>
											<option value="02:45" <? if (( isset($row)) &&  substr($row->transporter_job_deliv_time, 0, 5) == '02:45') { ?>selected<?php } ?>>02:45</option>
						                    <option value="03:00" <? if (( isset($row)) &&  substr($row->transporter_job_deliv_time, 0, 5) == '03:00') { ?>selected<?php } ?>>03:00</option>						                
											<option value="03:15" <? if (( isset($row)) &&  substr($row->transporter_job_deliv_time, 0, 5) == '03:15') { ?>selected<?php } ?>>03:15</option>
											<option value="03:30" <? if (( isset($row)) &&  substr($row->transporter_job_deliv_time, 0, 5) == '03:30') { ?>selected<?php } ?>>03:30</option>
											<option value="03:45" <? if (( isset($row)) &&  substr($row->transporter_job_deliv_time, 0, 5) == '03:45') { ?>selected<?php } ?>>03:45</option>
						                    <option value="04:00" <? if (( isset($row)) &&  substr($row->transporter_job_deliv_time, 0, 5) == '04:00') { ?>selected<?php } ?>>04:00</option>						                
											<option value="04:15" <? if (( isset($row)) &&  substr($row->transporter_job_deliv_time, 0, 5) == '04:15') { ?>selected<?php } ?>>04:15</option>
											<option value="04:30" <? if (( isset($row)) &&  substr($row->transporter_job_deliv_time, 0, 5) == '04:30') { ?>selected<?php } ?>>04:30</option>
											<option value="04:45" <? if (( isset($row)) &&  substr($row->transporter_job_deliv_time, 0, 5) == '04:45') { ?>selected<?php } ?>>04:45</option>
						                    <option value="05:00" <? if (( isset($row)) &&  substr($row->transporter_job_deliv_time, 0, 5) == '05:00') { ?>selected<?php } ?>>05:00</option>						                
											<option value="05:15" <? if (( isset($row)) &&  substr($row->transporter_job_deliv_time, 0, 5) == '05:15') { ?>selected<?php } ?>>05:15</option>
											<option value="05:30" <? if (( isset($row)) &&  substr($row->transporter_job_deliv_time, 0, 5) == '05:30') { ?>selected<?php } ?>>05:30</option>
											<option value="05:45" <? if (( isset($row)) &&  substr($row->transporter_job_deliv_time, 0, 5) == '05:45') { ?>selected<?php } ?>>05:45</option>
						                    <option value="06:00" <? if (( isset($row)) &&  substr($row->transporter_job_deliv_time, 0, 5) == '06:00') { ?>selected<?php } ?>>06:00</option>						                
											<option value="06:15" <? if (( isset($row)) &&  substr($row->transporter_job_deliv_time, 0, 5) == '06:15') { ?>selected<?php } ?>>06:15</option>
											<option value="06:30" <? if (( isset($row)) &&  substr($row->transporter_job_deliv_time, 0, 5) == '06:30') { ?>selected<?php } ?>>06:30</option>
											<option value="06:45" <? if (( isset($row)) &&  substr($row->transporter_job_deliv_time, 0, 5) == '06:45') { ?>selected<?php } ?>>06:45</option>
						                    <option value="07:00" <? if (( isset($row)) &&  substr($row->transporter_job_deliv_time, 0, 5) == '07:00') { ?>selected<?php } ?>>07:00</option>						                
											<option value="07:15" <? if (( isset($row)) &&  substr($row->transporter_job_deliv_time, 0, 5) == '07:15') { ?>selected<?php } ?>>07:15</option>
											<option value="07:30" <? if (( isset($row)) &&  substr($row->transporter_job_deliv_time, 0, 5) == '07:30') { ?>selected<?php } ?>>07:30</option>
											<option value="07:45" <? if (( isset($row)) &&  substr($row->transporter_job_deliv_time, 0, 5) == '07:45') { ?>selected<?php } ?>>07:45</option>
						                    <option value="08:00" <? if (( isset($row)) &&  substr($row->transporter_job_deliv_time, 0, 5) == '08:00') { ?>selected<?php } ?>>08:00</option>						                
											<option value="08:15" <? if (( isset($row)) &&  substr($row->transporter_job_deliv_time, 0, 5) == '08:15') { ?>selected<?php } ?>>08:15</option>
											<option value="08:30" <? if (( isset($row)) &&  substr($row->transporter_job_deliv_time, 0, 5) == '08:30') { ?>selected<?php } ?>>08:30</option>
											<option value="08:45" <? if (( isset($row)) &&  substr($row->transporter_job_deliv_time, 0, 5) == '08:45') { ?>selected<?php } ?>>08:45</option>
						                    <option value="09:00" <? if (( isset($row)) &&  substr($row->transporter_job_deliv_time, 0, 5) == '09:00') { ?>selected<?php } ?>>09:00</option>						                
											<option value="09:15" <? if (( isset($row)) &&  substr($row->transporter_job_deliv_time, 0, 5) == '09:15') { ?>selected<?php } ?>>09:15</option>
											<option value="09:30" <? if (( isset($row)) &&  substr($row->transporter_job_deliv_time, 0, 5) == '09:30') { ?>selected<?php } ?>>09:30</option>
											<option value="09:45" <? if (( isset($row)) &&  substr($row->transporter_job_deliv_time, 0, 5) == '09:45') { ?>selected<?php } ?>>09:45</option>
						                    <option value="10:00" <? if (( isset($row)) &&  substr($row->transporter_job_deliv_time, 0, 5) == '10:00') { ?>selected<?php } ?>>10:00</option>						                
											<option value="10:15" <? if (( isset($row)) &&  substr($row->transporter_job_deliv_time, 0, 5) == '10:15') { ?>selected<?php } ?>>10:15</option>
											<option value="10:30" <? if (( isset($row)) &&  substr($row->transporter_job_deliv_time, 0, 5) == '10:30') { ?>selected<?php } ?>>10:30</option>
											<option value="10:45" <? if (( isset($row)) &&  substr($row->transporter_job_deliv_time, 0, 5) == '10:45') { ?>selected<?php } ?>>10:45</option>
						                    <option value="11:00" <? if (( isset($row)) &&  substr($row->transporter_job_deliv_time, 0, 5) == '11:00') { ?>selected<?php } ?>>11:00</option>						                
											<option value="11:15" <? if (( isset($row)) &&  substr($row->transporter_job_deliv_time, 0, 5) == '11:15') { ?>selected<?php } ?>>11:15</option>
											<option value="11:30" <? if (( isset($row)) &&  substr($row->transporter_job_deliv_time, 0, 5) == '11:30') { ?>selected<?php } ?>>11:30</option>
											<option value="11:45" <? if (( isset($row)) &&  substr($row->transporter_job_deliv_time, 0, 5) == '11:45') { ?>selected<?php } ?>>11:45</option>
						                    <option value="12:00" <? if (( isset($row)) &&  substr($row->transporter_job_deliv_time, 0, 5) == '12:00') { ?>selected<?php } ?>>12:00</option>						                
											<option value="12:15" <? if (( isset($row)) &&  substr($row->transporter_job_deliv_time, 0, 5) == '12:15') { ?>selected<?php } ?>>12:15</option>
											<option value="12:30" <? if (( isset($row)) &&  substr($row->transporter_job_deliv_time, 0, 5) == '12:30') { ?>selected<?php } ?>>12:30</option>
											<option value="12:45" <? if (( isset($row)) &&  substr($row->transporter_job_deliv_time, 0, 5) == '12:45') { ?>selected<?php } ?>>12:45</option>
						                    <option value="13:00" <? if (( isset($row)) &&  substr($row->transporter_job_deliv_time, 0, 5) == '13:00') { ?>selected<?php } ?>>13:00</option>						                
											<option value="13:15" <? if (( isset($row)) &&  substr($row->transporter_job_deliv_time, 0, 5) == '13:15') { ?>selected<?php } ?>>13:15</option>
											<option value="13:30" <? if (( isset($row)) &&  substr($row->transporter_job_deliv_time, 0, 5) == '13:30') { ?>selected<?php } ?>>13:30</option>
											<option value="13:45" <? if (( isset($row)) &&  substr($row->transporter_job_deliv_time, 0, 5) == '13:45') { ?>selected<?php } ?>>13:45</option>
						                    <option value="14:00" <? if (( isset($row)) &&  substr($row->transporter_job_deliv_time, 0, 5) == '14:00') { ?>selected<?php } ?>>14:00</option>						                
											<option value="14:15" <? if (( isset($row)) &&  substr($row->transporter_job_deliv_time, 0, 5) == '14:15') { ?>selected<?php } ?>>14:15</option>
											<option value="14:30" <? if (( isset($row)) &&  substr($row->transporter_job_deliv_time, 0, 5) == '14:30') { ?>selected<?php } ?>>14:30</option>
											<option value="14:45" <? if (( isset($row)) &&  substr($row->transporter_job_deliv_time, 0, 5) == '14:45') { ?>selected<?php } ?>>14:45</option>
						                    <option value="15:00" <? if (( isset($row)) &&  substr($row->transporter_job_deliv_time, 0, 5) == '15:00') { ?>selected<?php } ?>>15:00</option>						                
											<option value="15:15" <? if (( isset($row)) &&  substr($row->transporter_job_deliv_time, 0, 5) == '15:15') { ?>selected<?php } ?>>15:15</option>
											<option value="15:30" <? if (( isset($row)) &&  substr($row->transporter_job_deliv_time, 0, 5) == '15:30') { ?>selected<?php } ?>>15:30</option>
											<option value="15:45" <? if (( isset($row)) &&  substr($row->transporter_job_deliv_time, 0, 5) == '15:45') { ?>selected<?php } ?>>15:45</option>
						                    <option value="16:00" <? if (( isset($row)) &&  substr($row->transporter_job_deliv_time, 0, 5) == '16:00') { ?>selected<?php } ?>>16:00</option>						                
											<option value="16:15" <? if (( isset($row)) &&  substr($row->transporter_job_deliv_time, 0, 5) == '16:15') { ?>selected<?php } ?>>16:15</option>
											<option value="16:30" <? if (( isset($row)) &&  substr($row->transporter_job_deliv_time, 0, 5) == '16:30') { ?>selected<?php } ?>>16:30</option>
											<option value="16:45" <? if (( isset($row)) &&  substr($row->transporter_job_deliv_time, 0, 5) == '16:45') { ?>selected<?php } ?>>16:45</option>
						                    <option value="17:00" <? if (( isset($row)) &&  substr($row->transporter_job_deliv_time, 0, 5) == '17:00') { ?>selected<?php } ?>>17:00</option>						                
											<option value="17:15" <? if (( isset($row)) &&  substr($row->transporter_job_deliv_time, 0, 5) == '17:15') { ?>selected<?php } ?>>17:15</option>
											<option value="17:30" <? if (( isset($row)) &&  substr($row->transporter_job_deliv_time, 0, 5) == '17:30') { ?>selected<?php } ?>>17:30</option>
											<option value="17:45" <? if (( isset($row)) &&  substr($row->transporter_job_deliv_time, 0, 5) == '17:45') { ?>selected<?php } ?>>17:45</option>
						                    <option value="18:00" <? if (( isset($row)) &&  substr($row->transporter_job_deliv_time, 0, 5) == '18:00') { ?>selected<?php } ?>>18:00</option>						                
											<option value="18:15" <? if (( isset($row)) &&  substr($row->transporter_job_deliv_time, 0, 5) == '18:15') { ?>selected<?php } ?>>18:15</option>
											<option value="18:30" <? if (( isset($row)) &&  substr($row->transporter_job_deliv_time, 0, 5) == '18:30') { ?>selected<?php } ?>>18:30</option>
											<option value="18:45" <? if (( isset($row)) &&  substr($row->transporter_job_deliv_time, 0, 5) == '18:45') { ?>selected<?php } ?>>18:45</option>
						                    <option value="19:00" <? if (( isset($row)) &&  substr($row->transporter_job_deliv_time, 0, 5) == '19:00') { ?>selected<?php } ?>>19:00</option>						                
											<option value="19:15" <? if (( isset($row)) &&  substr($row->transporter_job_deliv_time, 0, 5) == '19:15') { ?>selected<?php } ?>>19:15</option>
											<option value="19:30" <? if (( isset($row)) &&  substr($row->transporter_job_deliv_time, 0, 5) == '19:30') { ?>selected<?php } ?>>19:30</option>
											<option value="19:45" <? if (( isset($row)) &&  substr($row->transporter_job_deliv_time, 0, 5) == '19:45') { ?>selected<?php } ?>>19:45</option>
						                    <option value="20:00" <? if (( isset($row)) &&  substr($row->transporter_job_deliv_time, 0, 5) == '20:00') { ?>selected<?php } ?>>20:00</option>						                
											<option value="20:15" <? if (( isset($row)) &&  substr($row->transporter_job_deliv_time, 0, 5) == '20:15') { ?>selected<?php } ?>>20:15</option>
											<option value="20:30" <? if (( isset($row)) &&  substr($row->transporter_job_deliv_time, 0, 5) == '20:30') { ?>selected<?php } ?>>20:30</option>
											<option value="20:45" <? if (( isset($row)) &&  substr($row->transporter_job_deliv_time, 0, 5) == '20:45') { ?>selected<?php } ?>>20:45</option>
						                    <option value="21:00" <? if (( isset($row)) &&  substr($row->transporter_job_deliv_time, 0, 5) == '21:00') { ?>selected<?php } ?>>21:00</option>						                
											<option value="21:15" <? if (( isset($row)) &&  substr($row->transporter_job_deliv_time, 0, 5) == '21:15') { ?>selected<?php } ?>>21:15</option>
											<option value="21:30" <? if (( isset($row)) &&  substr($row->transporter_job_deliv_time, 0, 5) == '21:30') { ?>selected<?php } ?>>21:30</option>
											<option value="21:45" <? if (( isset($row)) &&  substr($row->transporter_job_deliv_time, 0, 5) == '21:45') { ?>selected<?php } ?>>21:45</option>
						                    <option value="22:00" <? if (( isset($row)) &&  substr($row->transporter_job_deliv_time, 0, 5) == '22:00') { ?>selected<?php } ?>>22:00</option>						                
											<option value="22:15" <? if (( isset($row)) &&  substr($row->transporter_job_deliv_time, 0, 5) == '22:15') { ?>selected<?php } ?>>22:15</option>
											<option value="22:30" <? if (( isset($row)) &&  substr($row->transporter_job_deliv_time, 0, 5) == '22:30') { ?>selected<?php } ?>>22:30</option>
											<option value="22:45" <? if (( isset($row)) &&  substr($row->transporter_job_deliv_time, 0, 5) == '22:45') { ?>selected<?php } ?>>22:45</option>
						                    <option value="23:00" <? if (( isset($row)) &&  substr($row->transporter_job_deliv_time, 0, 5) == '23:00') { ?>selected<?php } ?>>23:00</option>
											<option value="23:15" <? if (( isset($row)) &&  substr($row->transporter_job_deliv_time, 0, 5) == '23:15') { ?>selected<?php } ?>>23:15</option>
											<option value="23:30" <? if (( isset($row)) &&  substr($row->transporter_job_deliv_time, 0, 5) == '23:30') { ?>selected<?php } ?>>23:30</option>
											<option value="23:45" <? if (( isset($row)) &&  substr($row->transporter_job_deliv_time, 0, 5) == '23:45') { ?>selected<?php } ?>>23:45</option>
											<option value="23:59" <? if (( isset($row)) &&  substr($row->transporter_job_deliv_time, 0, 5) == '23:59') { ?>selected<?php } ?>>23:59</option>
						             </select>  
            
		</td>
	</tr>
	<tr><td>&nbsp;</td></tr>
	<tr>
		<td colspan="3">
			<input type="submit" value="Delivered" name="Delivered" id="Delivered"/>		
			<input type="button" value="Close" name="close" id="close" onclick="javascript:jQuery('#dialog').dialog('close');" /> 
			<img id="loader2" src="<?php echo base_url();?>assets/images/ajax-loader.gif" border="0" alt="" title="" style="display:none;">
		</td>
	</tr>
</table>
</form>
</div>