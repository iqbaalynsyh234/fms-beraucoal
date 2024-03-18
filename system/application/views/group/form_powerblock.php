<script>
	jQuery(document).ready(
		function()
		{
			showclock();
		}
	);
	
	function frmgroup_onsubmit(frm)
	{
		jQuery.post("<?=base_url()?>transporter/customer_powerblock/save", jQuery("#frmgroup").serialize(),
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
		<div class="block-border">
			<form class="block-content form" id="frmgroup" onsubmit="javascript: return frmgroup_onsubmit(this)">				
			<?php if (isset($row)) { ?>
			<h1><?php echo "EDIT Customer"; ?></h1>
			<?php } else { ?>
			<h1><?php echo "ADD Customer"; ?></h1>
			<?php } ?>
			
				<table width="100%" cellpadding="3" class="tablelist">
					<?php if (isset($row)) { ?>					
					<input type="hidden" id="id" name="id" value="<?=$row->group_id;?>" />
					<tr style="border: 0px;">
						<td style="border: 0px;">ID</td>
						<td style="border: 0px;">:</td>
						<td style="border: 0px;"><?=$row->group_id;?></td>
					</tr>
					<?php } ?>
    			<tr style="border: 0px;">
						<td width="100" style="border: 0px;">Customer</td>
						<td width="1" style="border: 0px;">:</td>
						<td style="border: 0px;"><input type="text" name="groupname" id="groupname" value="<?=isset($row) ? htmlspecialchars($row->group_name, ENT_QUOTES) : "";?>" class="formdefault" /></td>
					</tr>
                        <tr style="border: 0px;">
                                                <td style="display:none;border: 0px;"><?php echo "Group Company";?></td>
                                                <td style="display:none;border: 0px;">:</td>
                                                <td style="display:none;border: 0px;">
                                                        <select name="usersite" id="usersite">          
                                                        <?php for($i=0; $i < count($rows); $i++) { ?>
                                                                <option value="<?php echo $rows[$i]->company_id; ?>"<?php if (isset($row) && ($row->group_company == $rows[$i]->company_id)) { echo " selected"; } ?>><?php echo $rows[$i]->company_name; ?></option>
                                                        <?php } ?>
                                                        </select>               
                                                </td>
                        </tr>
                        
						<!-- 
						<tr>
							<td><?=$this->lang->line("lparent");?></td>
							<td>:</td>
							<td>
								<select name="parent" id="parent">
									<option value=""></option>
									<?php echo $parentoptions; ?>
								</select>
							</td>
                        </tr>
						-->
						
    			<tr style="border: 0px;">
						<td style="border: 0px;">&nbsp;</td>
						<td style="border: 0px;">&nbsp;</td>
						<td style="border: 0px;">
								<input type="submit" name="btnsave" id="btnsave" value=" Save " />
								<input type="button" name="btncancel" id="btncancel" value=" Cancel " onclick="location='<?=base_url()?>transporter/customer_powerblock';" />
						</td>
					</tr>					
				</table>
			</form>
		</div>
	</div>
</div>
			
