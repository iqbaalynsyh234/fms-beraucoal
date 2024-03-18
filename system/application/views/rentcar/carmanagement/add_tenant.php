<script>
function frmadd_onsubmit()
	{
		jQuery("#loader").show();
		jQuery.post("<?=base_url()?>carmanagement/savenew", jQuery("#frmadd").serialize(),	
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
  
    <form class ="block-content form" name="frmadd" id="frmadd" onsubmit="javascript:return frmadd_onsubmit()">
      <fieldset class="grey-bg required">
	  <legend>Required Information</legend>
	  <h1>Tambah Penyewa Baru</h1>
	  
      <table width="100%" cellpadding="2" class="table sortable no-margin" >
        <tr>
            <input type="hidden" name="customer_company" id="customer_company"  value="<?php echo $this->sess->user_company;?>" /></td>
	    </tr>
		<tr>
          <td>Name</td>
          <td>:</td>
          <td><input type="text" name="customer_name" id="customer_name"  style="width:300px;"/><span style="color:red;font-size:14px;"> *</span></td>
        </tr>
        <tr>
          <td>Address</td>
		  <td>:</td>
          <td><textarea name="customer_address" id="customer_address" style="height:100px;width:300px;"></textarea>
            <span style="color:red;font-size:14px;"> *</span>
          </td>
        </tr>
        <tr>
          <td>Telp</td>
		  <td>:</td>
          <td>
            <input type="text" name="customer_phone" id="customer_phone"  style="width:150px;" size="20" maxlength="20"/>
            <span style="color:red;font-size:14px;"> *</span>
		  </td>
        </tr>
        <tr>
          <td>Mobile</td>
          <td>:</td>
		  <td>
            <input type="text" name="customer_mobile" id="customer_mobile"  style="width:150px;" size="20" maxlength="20"/>
            <span style="color:red;font-size:14px;"> *</span>
		  </td>
        </tr>
        <tr>
          <td>Email</td>
		  <td>:</td>
          <td>
			<input type="text" name="customer_email" id="customer_email"  style="width:300px;"/>
			<span style="color:red;font-size:14px;"> *</span>
		  </td>
        </tr>
        <tr>
          <td>Idcard/Passport</td>
          <td>:</td>
		  <td>
		    <input type="text" name="customer_idcard" id="customer_idcard"  style="width:300px;"/>
            <span style="color:red;font-size:14px;"> *</span>
		  </td>
        </tr>
        <tr>
          <td>Status</td>
		  <td>:</td>
          <td>
            <select name="customer_status" id="customer_status">
              <option value="" selected="selected">---Select Status---</option>
              <option value="1">Recommended</option>
			  <option value="0">Blacklist</option>
            </select><span style="color:red;font-size:14px;"> *</span>
		  </td>
        </tr>
        <tr>
          <td>Keterangan</td>
		  <td>:</td>
          <td><textarea name="customer_keterangan" id="customer_keterangan" style="height:100px;width:300px;"></textarea></td>
        </tr>
        <tr>
          <td>&nbsp;</td>
		  <td>:</td>
          <td>
			<input type="submit" name="submit" id="submit" value="Save" />
            <input type="button" name="button" id="button" value="Cancel" onclick="location='<?php echo base_url();?>carmanagement/data_tenant';" />
            <img id="loader" src="<?php echo base_url();?>assets/images/ajax-loader.gif" style="display: none;" />
		  </td>
        </tr>
      </table>
    </form>
  </div>
</div>