<script>
	function frmadd_onsubmit()
	{
		jQuery.post("<?=base_url()?>user/savereqinfo", jQuery("#frmadd").serialize(),
			function(r)
			{
				if (r.error)
				{
					alert(r.message);
					return false;
				}
				
				jQuery("#dialog").dialog('close');
			}
			, "json"
		);
		return false;
	}	
</script>
<?php echo $header; ?>
<form name="frmadd" id="frmadd" onsubmit="javascript: return frmadd_onsubmit()">
	<table width="100%" cellpadding="3" class="tablelist">
		<tr>
			<td><?=$this->lang->line("lemail");?></td>
			<td>:</td>
			<td><input type="text" name="email" id="email" value="<?php echo $row->user_mail; ?>" class="formdefault" /></td>
		</tr>
		<tr>
			<td><?=$this->lang->line("lmobile");?></td>
			<td>:</td>
			<td><input type="text" name="mobile" id="mobile" value="<?php echo $row->user_mobile; ?>" class="formdefault" /></td>
		</tr>
		<tr>
			<td><?=$this->lang->line("laddress");?></td>
			<td>:</td>
			<td><textarea name="address" id="address" class="formdefault"><?php echo $row->user_address; ?></textarea></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>
				<input type="submit" name="btnsave" id="btnsave" value=" Save " />
				<input type="button" name="btncancel" id="btncancel" value=" Reset " onclick="document.frmadd.reset()" />
			</td>
		</tr>							
	</table>
</form>