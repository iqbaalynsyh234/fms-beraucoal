<script>
function frmadd_onsubmit()
	{
		jQuery("#loader").show();
		jQuery.post("<?=base_url()?>carmanagement/updateblacklist", jQuery("#frmedit").serialize(),	
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
			jQuery("#startdate").datepicker(
				{
							dateFormat: 'yy/mm/dd'
						, 	startDate: '1900/01/01'
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
			jQuery("#enddate").datepicker(
				{
							dateFormat: 'yy/mm/dd'
						, 	startDate: '1900/01/01'
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
  
  <form class="block-content form" id="frmedit" name="frmedit" method="post" action="<?=base_url()?>carmanagement/updateblacklist">
      <h1>Edit Tenant Blaclist</h1>
	  <fieldset class="grey-bg required">
      <legend>Information</legend>
      <!--
      <input type="hidden" name="orderdate" value="<?php echo date("Y-m-d");?>" />
      -->
      <table width="100%" cellpadding="3" class="table sortable no-margin">
        <tr>
          <input type="hidden" name="customer_id" id="customer_id" value="<?php echo $row->customer_id?>" />
        </tr>
        <tr>
          <td>Name</td>
		  <td>:</td>
          <td>
            <input type="text" name="customer_name" id="customer_name"  style="width:300px;" value="<?=isset($row) ? htmlspecialchars($row->customer_name, ENT_QUOTES) : "";?>" />
            <span style="color:red;font-size:12px;"> *</span>
		  </td>
        </tr>
        <tr>
          <td>Address</td>
		  <td>:</td>
          <td>
			<textarea style="height:100px;width:300px;" name="customer_address" id="customer_address" class="formdefault">
				<?=isset($row) ? htmlspecialchars($row->customer_address, ENT_QUOTES) : "";?>
			</textarea><span style="color:red;font-size:12px;"> *</span>
		  </td>
		</tr>
        <tr>
          <td>Telp</td>
		  <td>:</td>
          <td>
            <input type="text" name="customer_phone" id="customer_phone"  style="width:150px;" value="<?=isset($row) ? htmlspecialchars($row->customer_phone, ENT_QUOTES) : "";?>" />
            <span style="color:red;font-size:12px;"> *</span>
		  </td>
        </tr>
        <tr>
          <td>Mobile</td>
		  <td>:</td>
          <td>
            <input type="text" name="customer_mobile" id="customer_mobile"  style="width:150px;" value="<?=isset($row) ? htmlspecialchars($row->customer_mobile, ENT_QUOTES) : "";?>" />
            <span style="color:red;font-size:12px;"> *</span>
		  </td>
        </tr>
        <tr>
          <td>Email</td>
		  <td>:</td>
          <td>
		    <input type="text" name="customer_email" id="customer_email"  style="width:300px;" value="<?=isset($row) ? htmlspecialchars($row->customer_email, ENT_QUOTES) : "";?>" value="<?php echo $row->customer_email?>"/>
            <span style="color:red;font-size:12px;"> *</span>
		  </td>
        </tr>
        <tr>
          <td>Idcard/Passport</td>
		  <td>:</td>
          <td><input type="text" name="customer_idcard" id="customer_idcard"  style="width:300px;" value="<?=isset($row) ? htmlspecialchars($row->customer_idcard, ENT_QUOTES) : "";?>" />
            <span style="color:red;font-size:12px;"> *</span>
		  </td>
        </tr>
        <tr>
          <td>Status</td>
		  <td>:</td>
          <td>        
            <select id="customer_status" name="customer_status"> 
			    <option value="Blacklist" <? if ((isset($row)) && ($row->customer_status == $row->customer_status)) { ?>selected<?php } ?>>Blacklist</option>
			    <option value="Recommended" <? if ((isset($row)) && ($row->customer_status == $row->customer_status)) { ?>selected<?php } ?>>Recommended</option>
            </select><span style="color:red;font-size:12px;"> *</span>
		  </td>
			<!--
			<select id="expense_code" name="expense_code"> 
				        <?php 
									$ccode = count($rcode);
									
									
									for($i=0;$i<$ccode;$i++){
										if(isset($row) && $rcode[$i]->expense_code_id == $row->expense_code){
											
											$mselected = "selected='selected'";
										}else{
											$mselected = "";
										}
										
										echo "<option value='" . $rcode[$i]->expense_code_id ."' ". $mselected.">" . $rcode[$i]->expense_code_name . "</option>";
									}
								?>
                    </select> * -->
        </tr>
        <tr>
          <td>Keterangan</td>
		  <td>:</td>
          <td>
			  <textarea name="customer_keterangan" id="customer_keterangan" style="height:100px;width:300px;">
				<?=isset($row) ? htmlspecialchars($row->customer_keterangan, ENT_QUOTES) : "";?>
			  </textarea>
		  </td>
        </tr>
        <tr>
          <td>&nbsp;</td>
		  <td>:</td>
          <td><input type="submit" name="submit" id="submit" value="Update" />
            <input type="button" name="button" id="button" value="Cancel" onclick="location='<?php echo base_url();?>carmanagement/tenant_blacklist';" />
          <img id="loader" src="<?php echo base_url();?>assets/images/ajax-loader.gif" style="display: none;" /></td>
        </tr>
		
      </table>
    </form>
  </div>
 </div>
</div>