<script>
	
	jQuery(document).ready(
		function()
		{
				showclock();
		}
	);
	
	/*function frmdosj_onsubmit()
	{
		jQuery("#loader").show();
		jQuery.post("<?=base_url()?>transporter/dosj_all/delete_dosj_hist", jQuery("#frmdosj").serialize(),
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
	} */
	
	function frmdosj_onsubmit()
	{
		jQuery("#loader").show();
		jQuery.post("<?=base_url()?>transporter/dosj_all/delete_dosj_hist", jQuery("#frmdosj").serialize(),
			function(r)
			{
				jQuery("#loader").hide();
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
<div id="main_data">
		<form id="frmdosj" onsubmit="javascript: return frmdosj_onsubmit(this)">		
		<table width="100%" cellpadding="3" class="table sortable no-margin" style="margin: 3px;">
				<input type="hidden" name="dosj_id" id="dosj_id" value="<?php echo $data->do_delivered_id; ?>" />
				<input type="hidden" name="dosj_no" id="dosj_no" value="<?php echo $data->do_delivered_do_number; ?>" />
				<tr>
					<td style="text-align:left"><h2>Delete SO Number ( <?php if (isset($data)) { echo "Block"." ".$data->do_delivered_do_block." "."Mortar :"." ".$data->do_delivered_do_mortar; } ?> )</h2></td>
				</tr>
				<tr>
					<td style="text-align:left">
                        Quantity : <br />
                        Block : <?php if (isset($data)) { echo $data->do_delivered_quantity; } ?>,
                        Mortar : <?php if (isset($data)) { echo $data->do_delivered_quantity_mortar; } ?>
                        
                    </td>
				</tr>
				<tr>
					<td style="text-align:left">Cost. <?php if (isset($data)) { echo $data->do_delivered_cost; } ?></td>
				</tr>
				<tr>
					<td style="text-align:left">Apakah anda yakin akan menghapus SO No. <?php if (isset($data)) { echo "Block"." ".$data->do_delivered_do_block." "."Mortar :"." ".$data->do_delivered_do_mortar; } ?> ?</td>
				</tr>
				<tr>
					<td>
						<input type="submit" name="submit" id="submit" value="Delete" />
						<input type="button" name="btn_cancel" id="btn_cancel" value="Cancel" onclick="location='<?=base_url()?>transporter/dosj_all/mn_manage_do';" />
						<img id="loader" src="<?=base_url();?>assets/images/ajax-loader.gif" border="0" alt="" title="" style="display:none;">
					</td>
				</tr>
		</table>
		</form>
</div>