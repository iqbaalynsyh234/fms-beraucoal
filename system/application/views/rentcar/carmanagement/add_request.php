<script>
function frmsave_onsubmit()
	{
		jQuery("#loader").show();
		jQuery.post("<?=base_url()?>carmanagement/savetenant", jQuery("#frmsave").serialize(),	
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
	
	function frmchange()
	{
		jQuery("#loader").show();
		jQuery.post("<?=base_url()?>carmanagement/getdata_customer", jQuery("#frmsave").serialize(),	
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
	
	jQuery("#start_date").datepicker(
				{
							dateFormat: 'yy-mm-dd'
						, 	startDate: '2010-10-01'
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
			
	jQuery("#end_date").datepicker(
				{
							dateFormat: 'yy-mm-dd'
						, 	startDate: '2010-10-01'
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
	
</script>

<form id="frmsave" onsubmit="javascript:return frmsave_onsubmit()">

<!--start table-->
<div class="section table_section">
<div class="title_wrapper">
<span class="title_wrapper_left"></span>
<span class="title_wrapper_right"></span>
</div>

<div class="section_content">
<div class="sct">
<div class="sct_left">
<div class="sct_right">
<div class="sct_left">
<div class="sct_right">  
	<form action="#">
    <fieldset>
    <div class="table_wrapper">
    <div class="table_wrapper_inner">
	<table width="100%" class="table sortable no-margin" style="font-size:12px">
        <tr>
          <td colspan="3"><h2>Sewa Mobil</h2></td>
		    <input type="hidden" name="vehicle_no" value="<?=$row->vehicle_no?>"  id="vehicle_no"/>
			<input type="hidden" name="vehicle_name" value="<?=$row->vehicle_name?>"  id="vehicle_name"/>
			<input type="hidden" name="vehicle_status" value="0" id="vehicle_status"/>
			<input type="hidden" name="expired_date" value="end_date" id="expired_date"/>
			<input type="hidden" name="settenant_company" id="settenant_company"  value="<?php echo $this->sess->user_company;?>" /></td>
			<input type="hidden" name="longtime" id="longtime"  value="longtime" /></td>
			<input type="hidden" name="settenant_flag" id="settenant_flag"  value="0" /></td>

		</tr>
		<tr>
          <td>Vehicle</td>
          <td>:</td>
		  <td>
			  <b><h3><?=$row->vehicle_name?> - <?=$row->vehicle_no?></b>
		  </td>
        </tr>
        <tr>
          <td>Penyewa</td>
		  <td>:</td>
          <td>
                <select id="settenant_name" name="settenant_name">
                <option value="" selected="selected">--Select Penyewa--</option>
								<?php 
									$ccustomer = count($rcustomer);
									
									
									for($i=0;$i<$ccustomer;$i++){
										if(isset($row) && $rcustomer[$i]->customer_name == $row->customer_status){
											
											$mselected = "selected='selected'";
										}else{
											$mselected = "";
										}
										
										echo "<option value='" . $rcustomer[$i]->customer_id ."' ". $mselected.">" . $rcustomer[$i]->customer_name . "</option>";
									}
								?>
								
							</select> *</td>
        </tr>
        <tr>
          <td>Start Date</td>
          <td>:</td>
		  <td>
          	<input type='text' readonly name="start_date"  id="start_date"  class="date-pick" value=""  maxlength='10' style="width:150px;"><i> Format : yyyy-mm-dd</i>
            <span style="color:red;font-size:14px;">*</span>
		  </td>
        </tr>
        <tr>
          <td>End Date</td>
          <td>:</td>
		  <td>
            <input type='text' readonly name="end_date" id="end_date" class="date-pick" value=""  maxlength='10' style="width:150px;"><i> Format : yyyy-mm-dd</i>
            <span style="color:red;font-size:14px;"> *</span>
		  </td>
        </tr>
		
		<tr>
          <td>&nbsp;</td>
		  <td>&nbsp;</td>
		  <td>
			  <input type="submit" name="submit" id="submit" value="Save" />
			  <input type="button" name="button" id="button" value="Cancel" onclick="javascript:jQuery('#dialog').dialog('close');" />
			  <img id="loader" src="<?php echo base_url();?>assets/images/ajax-loader.gif" style="display: none;" /></td>
        </tr>
	  
      </table>
  
</div>
</div>
</fieldset>
</form>
</div>
</div>
</div>
</div>
</div>
<span class="scb"><span class="scb_left"></span><span class="scb_right"></span></span>
</div>
</div>
</form>