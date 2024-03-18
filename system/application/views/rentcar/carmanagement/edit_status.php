<script>
function frmupdate_onsubmit()
	{
		jQuery("#loader").show();
		jQuery.post("<?=base_url()?>carmanagement/updatestatus", jQuery("#frmupdate").serialize(),	
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
			jQuery("#start_date").datepicker(
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
		
			showclock();
			jQuery("#end_date").datepicker(
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
			
			showclock();
			jQuery("#expired_date").datepicker(
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
	
</script>

<div style="position: absolute; margin: 0; padding: 0; z-index: 1000; width: 100%;">
  <?=$navigation;?>
  <div id="main" style="margin: 20px;">
  <div class="block-border">
  <form class="block-content form" id="frmupdate" name="frmupdate" method="post" action="<?=base_url()?>carmanagement/updatestatus">
      <input type="hidden" name="settenant_id" id="settenant_id" value="<?php echo $row->settenant_id?>" />
      <!--<input type="hidden" name="orderdate" value="<?php echo date("Y-m-d");?>" />-->
      <h1>Edit Status Vehicle</h1>
	  <fieldset class="grey-bg required">			
      <legend>Vehicle Information</legend>
      <table width="100%" cellpadding="3" class="table sortable no-margin">
		
        <tr>
          <td>Vehicle</td>
          <td>:</td>
          <td><b><?php echo $row->vehicle_name?> - <?php echo $row->vehicle_no?></b>
			<input type="hidden" name="vehicle_name" id="vehicle_name"  style="width:150px;" value="<?php echo $row->vehicle_name?>"/>
			<input type="hidden" name="vehicle_no" id="vehicle_no"  style="width:150px;" value="<?php echo $row->vehicle_no?>"/>
			<input type="hidden" name="settenant_company" id="settenant_company"  value="<?php echo $this->sess->user_company;?>" /></td>
			<input type="hidden" name="settenant_name" id="settenant_name"  value="<?php echo $row->settenant_name;?>" /></td>
			<input type="hidden" name="longtime" id="longtime"  value="<?php echo $row->longtime;?>" /></td>
			
		  </td>
		</tr>
		<tr>
          <td>Nama Penyewa</td>
          <td>:</<td>
		  <td>
			<select id="settenant_name" name="settenant_name"> 
				        <?php 
									$ccustomer = count($rcustomer);
									
									
									for($i=0;$i<$ccustomer;$i++){
										if(isset($row) && $rcustomer[$i]->customer_id == $row->settenant_name){
											
											$mselected = "selected='selected'";
										}else{
											$mselected = "";
										}
										
										echo "<option value='" . $rcustomer[$i]->customer_id ."' ". $mselected.">" . $rcustomer[$i]->customer_name . "</option>";
									}
								?>
            </select>
		</td>
		
        </tr>
        <tr>
          <td>Start Date</td>
		  <td>:</td>
          <td><input type="text" readonly name="start_date" id="start_date"  style="width:100px;" value="<?php echo $row->start_date?>"/></td>
        </tr>
        <tr>
          <td>End Date</td>
		  <td>:</td>
          <td><input type="text" readonly name="end_date" id="end_date"  style="width:100px;" value="<?php echo $row->end_date ?>"/></td>
        </tr>
        <tr>
          <td>Expired Date</td>
		  <td>:</td>
          <td><input type="text" readonly name="expired_date" id="expired_date"  style="width:100px;" value="<?php echo  $row->expired_date ?>"/></td>
        </tr>
        <tr>
          <td>Status</td>
          <td>:</td>
		  <td>
            <select id="vehicle_status" name="vehicle_status"> 
			    <option value="0" <? if (( isset($row)) && ($row->vehicle_status == '0')) { ?>selected<?php } ?>>Rent</option>
			    <option value="1" <? if (( isset($row)) && ($row->vehicle_status == '1')) { ?>selected<?php } ?>>Complete</option>
            </select>
		  </td>
        </tr>
		<tr>
          <td>&nbsp;</td>
		  <td>&nbsp;</td>
		  <td>&nbsp;</td>
		 </tr>
        <tr>
          <td>&nbsp;</td>
		  <td>&nbsp;</td>
          <td>
			<input type="submit" name="submit" id="submit" value="Update" />
            <input type="button" name="button" id="button" value="Cancel" onclick="location='<?php echo base_url();?>carmanagement/status_vehicle';" />
			<img id="loader" src="<?php echo base_url();?>assets/images/ajax-loader.gif" style="display: none;" />
		  </td>
        </tr>
        
      </table>
    </form>
  </div>
 </div>
</div>