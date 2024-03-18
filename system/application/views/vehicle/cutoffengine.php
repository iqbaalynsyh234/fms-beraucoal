			<script>
				function frmcutoffengine_onsubmit()
				{
					jQuery.post("<?php echo base_url(); ?>vehicle/cutoffengine/did", jQuery("#frmcutoffengine").serialize(),
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
			<form name="frmcutoffengine" id="frmcutoffengine" onsubmit="javascript: return frmcutoffengine_onsubmit()">
				<input type="hidden" name="id" id="id" value="<?php echo $vehicle->vehicle_id; ?>" />
				<input type="hidden" name="status" id="status" value="<?php echo $_POST['status']; ?>" />				
				<table width="100%" cellpadding="3" class="tablelist">
				<tr>
					<td colspan="3"><h2><?echo ($_POST['status'] == 1) ? $this->lang->line("lresumeengine") : $this->lang->line("lcutoffengine");?></h2></td>
				</tr>
    			<tr>
						<td width="130"><?=$this->lang->line("lvehicle");?></td>
						<td width="1">:</td>
						<td><?php echo $vehicle->vehicle_name; ?> <?php echo $vehicle->vehicle_no; ?></td>
					</tr>
    			<tr>
						<td><?=$this->lang->line("lcardno");?></td>
						<td>:</td>
						<td><?php echo $vehicle->vehicle_card_no; ?></td>
					</tr>
			<?php if ($_POST['status'] != 1) { ?>
    			<tr>
						<td><?=$this->lang->line("ldesclimer");?></td>
						<td>:</td>
						<td><textarea name="cutoffengine" id="cutoffengine" cols="60" rows="5" readonly><?php echo $desclimer; ?></textarea></td>
					</tr>
			<tr>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td><input type="checkbox" id="aggree" name="aggree" value="1" /> <?php echo $this->lang->line("laggree"); ?></td>
			</tr>
			<?php } else { ?>
			<input type="hidden" name="aggree" id="aggree" value="1" />
			<?php } ?>
    			<tr>
						<td><?=$this->lang->line("lpassword");?></td>
						<td>:</td>
						<td><input type="password" name="password" id="password" value="" /></td>
					</tr>
    			<tr>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>
							<input type="submit" name="btnsave" id="btnsave" value=" <?echo ($_POST['status'] == 1) ? $this->lang->line("lresumeengine") : $this->lang->line("lcutoffengine");?> " />
							<input type="button" name="btncancel" id="btncancel" value=" Cancel " onclick='javascript:jQuery("#dialog").dialog("close");' />
						</td>
					</tr>					
				</table>
			</form>		
