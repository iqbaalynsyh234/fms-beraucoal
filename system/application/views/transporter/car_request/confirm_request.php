<script>
function field_onchange()
{
    var v = jQuery("#request_status").val();

    if (v == 1 || v =="1")
    {
        jQuery("#available_vehicle").show();
    }
    else
    {
        jQuery("#available_vehicle").hide();
    }
}

function frmconfirm_onsubmit()
	{
		jQuery("#loader").show();
		jQuery.post("<?=base_url()?>transporter/car_request/save_confirm", jQuery("#frm_confirm").serialize(),	
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
	<div id="main" style="margin: 20px;">
    <h2>Confirm Request</h2>
    <hr />
    <form name="frm_confirm" id="frm_confirm" onsubmit="javascript:return frmconfirm_onsubmit()">
    <input type="hidden" name="request_id" id="request_id" value="<?php echo $data->request_id;?>" />
    <input type="hidden" name="request_company" id="request_company" value="<?php echo $data->request_company;?>" />
    <input type="hidden" name="request_group_company" id="request_group_company" value="<?php echo $data->request_group_company;?>" />
    <input type="hidden" name="request_user_id" id="request_user_id" value="<?php echo $data->request_user_id;?>" />
    <table style="font-size:12px;">
    <tr>
    <td>Company</td>
    <td>:</td>
    <td><?php echo $data->request_group_name;?></td>
    </tr>
    
    <tr>
    <td>PIC Name</td>
    <td>:</td>
    <td><?php echo $data->request_pic_name;?></td>
    </tr>
    
    <tr>
    <td>PIC Mobile</td>
    <td>:</td>
    <td><?php echo $data->request_pic_mobile;?></td>
    </tr>
    
    <tr>
    <td>PIC Email</td>
    <td>:</td>
    <td><?php echo $data->request_pic_email;?></td>
    </tr>
    
    <tr><td colspan="3"><h2>Vehicle Info</h2></td></tr>

    <tr>
    <td>Status</td>
    <td>:</td>
    <td>
    <select name="request_status" id="request_status" onchange="javascript:field_onchange()">
    <option value="blank">-</option>
    <?php 
    if (isset($request_status))
    {
        for ($i=0;$i<count($request_status);$i++)
        {
            if(($request_status[$i]->request_status_id != $this->config->item("new_order")) && ($request_status[$i]->request_status_id != $this->config->item("cancel_by_customer")))
            {
    ?>
    <option value="<?php echo $request_status[$i]->request_status_id;?>">
    <?php echo $request_status[$i]->request_status_name;?>
    </option>
    <?php
            }
        }
    }
    ?>
    </select>
    </td>
    </tr>
    
    <tr id="available_vehicle" style="display: none;">
    <td>Available Vehicle</td>
    <td>:</td>
    <td>
    <select name="vehicle_id" id="vehicle_id" >
    <option value="blank">-</option>
    <?php
        if (isset($available_vehicle))
        {
        for ($i=0;$i<count($available_vehicle);$i++)
        {
    ?>
    <option value="<?php echo $available_vehicle[$i]->vehicle_id."+".$available_vehicle[$i]->vehicle_name."+".$available_vehicle[$i]->vehicle_no;?>">
        <?php echo $available_vehicle[$i]->vehicle_name." ".$available_vehicle[$i]->vehicle_no;?>
    </option>
    <?php
        } }
     ?>
    </select>
    </td>
    </tr>
    
    <tr>
    <td colspan="3">
    <input type="submit" name="submit" id="submit" value="Confirm" />
    <img id="loader" src="<?=base_url();?>assets/images/ajax-loader.gif" border="0" alt="" title="" style="display:none;" />
    </td>
    </tr>
    </table>
    </form>
    </div>
</div>