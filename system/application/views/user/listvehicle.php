					<?php 
						$vtype = $_POST['vehicle_type'];
						$vraplaces = $this->config->item("vehicle_type_replace");
						
						if (is_array($vraplaces)  && count($vraplaces))
						{
							foreach($vraplaces as $key=>$val)
							{
								if ($val != $vtype) continue;

								$vtype = $key;
								break;
							}
						}
						
						$vtype = $vtype ? $vtype : "T1";						
					?>
			
			<input type="hidden" name="vehicle_device[]" id="vehicle_device<?=str_replace("@", "_", $_POST['device']);?>" value="<?=$_POST['device'];?>" />
			<input type="hidden" name="vehicle_type[]" id="vehicle_type<?=str_replace("@", "_", $_POST['device']);?>" value="<?=$vtype;?>" />
			<input type="hidden" name="vehicle_no[]" id="vehicle_no<?=str_replace("@", "_", $_POST['device']);?>" value="<?=$_POST['no'];?>" />
			<input type="hidden" name="vehicle_name[]" id="vehicle_name<?=str_replace("@", "_", $_POST['device']);?>" value="<?=$_POST['name'];?>" />
			<input type="hidden" name="vehicle_expire_date1[]" id="vehicle_expire_date1<?=str_replace("@", "_", $_POST['device']);?>" value="<?=$_POST['expire_date1'];?>" />
			<input type="hidden" name="vehicle_expire_date2[]" id="vehicle_expire_date2<?=str_replace("@", "_", $_POST['device']);?>" value="<?=$_POST['expire_date2'];?>" />
			<input type="hidden" name="vehicle_card[]" id="vehicle_card<?=str_replace("@", "_", $_POST['device']);?>" value="<?=$_POST['card'];?>" />
			<input type="hidden" name="vehicle_card_op[]" id="vehicle_card_op<?=str_replace("@", "_", $_POST['device']);?>" value="<?=$_POST['card_op'];?>" />
			<input type="hidden" name="vehicle_expire_date[]" id="vehicle_expire_date<?=str_replace("@", "_", $_POST['device']);?>" value="<?=$_POST['expire_date'];?>" />
			<input type="hidden" name="vehicle_image[]" id="vehicle_image<?=str_replace("@", "_", $_POST['device']);?>" value="<?=$_POST['vehicle_image'];?>" />
			<input type="hidden" name="vehicle_maxspeed[]" id="vehicle_maxspeed<?=str_replace("@", "_", $_POST['device']);?>" value="<?=$_POST['vehicle_maxspeed'];?>" />
			<td id="tdvehicle_device<?=str_replace("@", "_", $_POST['device']);?>"><?=str_replace("_", "@", $_POST['device']);?></td>
			<td id="tdvehicle_type<?=str_replace("@", "_", $_POST['device']);?>"><?=$vtype?></td>
			<td id="tdvehicle_no<?=str_replace("@", "_", $_POST['device']);?>"><img src="<?php echo base_url();?>assets/images/<?php echo $_POST['vehicle_image'];?>/car1.png" border="0" height="32" width="32"  /><?=$_POST['no'];?></td>
			<td id="tdvehicle_name<?=str_replace("@", "_", $_POST['device']);?>"><?=$_POST['name'];?></td>
			<td id="tdvehicle_expire_date1<?=str_replace("@", "_", $_POST['device']);?>"><?=$_POST['expire_date1'];?> - <?=$_POST['expire_date2'];?></td>
			<td id="tdvehicle_card<?=str_replace("@", "_", $_POST['device']);?>"><?=$_POST['card'];?></td>
			<td id="tdvehicle_card_op<?=str_replace("@", "_", $_POST['device']);?>"><?=$_POST['card_op'];?></td>
			<td id="tdvehicle_expire_date<?=str_replace("@", "_", $_POST['device']);?>"><?=$_POST['expire_date'];?></td>
			<?php if (($this->sess->user_type == 1) || (($this->sess->user_type == 3) && ($this->sess->user_agent_admin == 1))) { ?>
			<td id="tdvehicle_link<?=str_replace("@", "_", $_POST['device']);?>">
				<a href="javascript:editvehicle('<?=str_replace('@', '_', $_POST['device']);?>')"><img src="<?=base_url();?>assets/images/edit.gif" border="0"></a>
				<a href="javascript:removevehicle('<?=str_replace('@', '_', $_POST['device']);?>')" onclick="javascript: return confirm('<?=$this->lang->line("lconfirm_delete"); ?>')"><img src="<?=base_url();?>assets/images/trash.gif" border="0"></a>				
			</td>
			<?php } else { ?>
			<td>&nbsp;</td>
			<?php } ?>
