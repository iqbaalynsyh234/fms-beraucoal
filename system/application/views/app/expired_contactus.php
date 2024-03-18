			<script>
				function frmcontact_onsubmit()
				{
					jQuery.post("<?php echo base_url(); ?>home/savecontactus", jQuery("#frmcontact").serialize(),
						function (r)
						{
							if (r.error)
							{
								alert(r.message);
								return;
							}
							
							alert(r.message);
							jQuery("#dialog").dialog("close");
						}
						, "json"
					);				
					
					return false;
				}				
			</script>
			<form name="frmcontact" id="frmcontact" onsubmit="javascript: return frmcontact_onsubmit()">				
				<table width="100%" cellpadding="3" class="tablelist">
    			<tr>
						<td width="130"><?=$this->lang->line("lsubject");?></td>
						<td width="1">:</td>
						<td><?php echo $vehicle->vehicle_device; ?> <?php echo $this->lang->line("lexpired"); ?>							
								<input type="hidden" name="subject" id="subject" value="expired_<?php echo $vehicle->vehicle_device; ?>" />
						</td>
					</tr>
    			<tr>
    			<tr>
						<td width="130"><?=$this->lang->line("lmessage");?></td>
						<td width="1">:</td>
						<td>
								<textarea name="message" id="message" cols="100" rows="5"></textarea>
						</td>
					</tr>
    			<tr>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>
							<input type="submit" name="btnsave" id="btnsave" value=" Send " />
						</td>
					</tr>					
				</table>
			</form>		
