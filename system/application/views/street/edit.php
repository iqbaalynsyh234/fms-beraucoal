<script>
function edit_street_onsubmit()
{
	jQuery.post("<?=base_url()?>street/update", jQuery("#frmeditstreet").serialize(),
	function(r)
	{
		alert(r.message);
								
								if (r.error)
								{								
									return;									
								}								
								
								jQuery("#dialog").dialog("close");
								window.location.reload();
							}
							, "json"
						);
						
						return false;
}


</script>
			<form id="frmeditstreet" onsubmit="javascript: return edit_street_onsubmit()">		
			<input type="hidden" name="street_id" id="id" value="<?=isset($row) ? $row->street_id : 0;?>"/>		
				<table width="100%" cellpadding="3" class="tablelist">
    				<tr>
						<td>ID</td>
						<td>:</td>
						<td><?=isset($row) ? $row->street_id : '';?></td>
					</tr>
					
				<tr>
						<td>Street Name</td>
						<td>:</td>
						<td><input type="text" name="street_name" id="street_name" value="<?=isset($row) ? $row->street_name : '';?>" class="formdefault" /></td>
					</tr>
				
    			<tr>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>
								<input type="submit" name="btnsave" id="btnsave" value=" Save " />
								<input type="button" name="btncancel" id="btncancel" value=" Cancel " onclick="javascript:jQuery('#dialog').dialog('close');" />
						</td>
					</tr>					
				</table>
			</form>		

