<script>
	jQuery(document).ready(
		function()
		{
			showclock();
		}
	);
	
	function frmadd_onsubmit(frm)
	{
		jQuery.post("<?=base_url()?>announcement/save", jQuery("#frmadd").serialize(),
			function(r)
			{
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
<?=$navigation;?>
	<div id="main" style="margin: 20px;">
		<br />&nbsp;
		<h1><?=$title; ?></h1>
			<form id="frmadd" onsubmit="javascript: return frmadd_onsubmit(this)">				
				<table width="100%" cellpadding="3" class="tablelist">
					<?php if (isset($row)) { ?>					
					<input type="hidden" id="id" name="id" value="<?=$row->announcement_id;?>" />
					<tr style="border: 0px;">
						<td style="border: 0px;">ID</td>
						<td style="border: 0px;">:</td>
						<td style="border: 0px;"><?=$row->announcement_id;?></td>
					</tr>
					<?php } ?>
    			<tr style="border: 0px;">
						<td width="100" style="border: 0px;"><?php echo $this->lang->line("lmessage"); ?></td>
						<td width="1" style="border: 0px;">:</td>
						<td style="border: 0px;">
							<textarea name="message" id="message" style="height: 140px; width: 500px;"><?=isset($row) ? htmlspecialchars($row->announcement_message, ENT_QUOTES) : "";?></textarea>
						</td>
					</tr>
    			<tr style="border: 0px;">
						<td style="border: 0px;">&nbsp;</td>
						<td style="border: 0px;">&nbsp;</td>
						<td style="border: 0px;">
								<input type="submit" name="btnsave" id="btnsave" value=" Save " />
								<input type="button" name="btncancel" id="btncancel" value=" Cancel " onclick="location='<?=base_url()?>announcement/show';" />
						</td>
					</tr>					
				</table>
			</form>		
	</div>
</div>
			
