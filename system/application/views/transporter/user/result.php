		<script>
			function changeport(vid)
			{
				if (! confirm("<?php echo $this->lang->line('lchangeport_confirm');?>")) return;
				
				jQuery.post("<?=base_url();?>user/changeport/"+vid, {},
					function(r)
					{
						alert(r.message);
						if (r.error) return;
						
						jQuery("#changeport"+vid).hide();
					}
					, "json"
				);				
			}
			
			function showlink(id)
			{
				jQuery("[id]").filter(function() {
    				if (this.id.match(/^link\d+/))
    				{
    					if (this.id != ("link"+id))
    					{
    						jQuery("#"+this.id).hide();
    					}
    				}
				});				

				var disp = jQuery("#link"+id).css('display');
				if (disp == "none")
				{
					jQuery("#link"+id).show();
				}
				else
				{
					jQuery("#link"+id).hide();
				}				
			}
			
			function vform(v)
			{
				showdialog();
				jQuery.post('<?php echo base_url(); ?>user/formvehicle/', {id: v},
					function(r)
					{
						showdialog(r.html, "<?=$this->lang->line("lupdate_vehicle"); ?>");
					}
					, "json"
				);
			}
			
			function vtype(v)
			{
				showdialog();
				jQuery.post('<?php echo base_url(); ?>vehicle/formtype/', {id: v},
					function(r)
					{
						if (r.error) 
						{
							alert(r.message);
							return;
						}
						
						showdialog(r.html, "<?=$this->lang->line("lupdate_vehicle_type"); ?>");
					}
					, "json"
				);
			}			
			
			function uservform(uid)
			{
				showdialog();
				jQuery.post('<?php echo base_url(); ?>user/formvehicle/', {uid: uid},
					function(r)
					{
						showdialog(r.html, "<?=$this->lang->line("ladd_vehicle"); ?>");
					}
					, "json"
				);				
			}
			
			</script>
		<table width="100%" cellpadding="3" class="table sortable no-margin" style="margin: 3px;">
			<thead>
				<tr>
					<th width="2%">No.</td>
					<th><a href="#" onclick="javascript:order('user_login')"><?if ($sortby == 'user_login') { echo '<u>'; }?><?=$this->lang->line("llogin"); ?><?if ($sortby == 'user_login') { echo '</u>'; }?></a></th>
					<th><a href="#" onclick="javascript:order('user_name')"><?if ($sortby == 'user_name') { echo '<u>'; }?><?=$this->lang->line("lname"); ?><?if ($sortby == 'user_name') { echo '</u>'; }?></a></th>
					<th><?=$this->lang->line("lvehicle"); ?></th>
					<th><?=$this->lang->line("lstatus"); ?></th>
					<th width="150px;">&nbsp;</th>
				</tr>
			</thead>
			<tbody>
			<?php
			for($i=0; $i < count($data); $i++)
			{
			?>
				<tr <?=($i%2) ? "class='odd'" : "";?>>
					<td valign="top"><?=$i+1+$offset?></td>
					<td valign="top"><?=$data[$i]->user_login;?></td>
					<td valign="top"><?=$data[$i]->user_name;?></td>
					<td valign="top">
						<?php if (! isset($data[$i]->vehicles)) { ?>
							&nbsp;
						<?php } else { ?>
						<table width="100%" cellpadding="1" cellspacing="1" class="tablelist">
							<tbody>
						<?php for ($j=0; $j < count($data[$i]->vehicles); $j++) { ?>										
											<tr <?=($i%2) ? "class='odd'" : "";?>>
												<td width="3%" style="text-align: right; border: 0px;" valign="top"><?=$j+1?>.</td>
												<td valign="top" width="1%" style="border: 0px;">
													<a href="javascript: showlink(<?php echo $data[$i]->vehicles[$j]->vehicle_id; ?>)"><img src="<?php echo base_url();?>assets/images/<?=$data[$i]->vehicles[$j]->vehicle_image?>/car1.png" border="0" height="32" width="32"  /></a>
												</td>
												<td valign="top" style="border: 0px;">														
														<a href="javascript: showlink(<?php echo $data[$i]->vehicles[$j]->vehicle_id; ?>)"><font color="#0000ff"><?=$data[$i]->vehicles[$j]->vehicle_name?> - <?=$data[$i]->vehicles[$j]->vehicle_no?></font></a>
												</td>
											<tr id="link<?php echo $data[$i]->vehicles[$j]->vehicle_id; ?>" style="display: none;">
												<td colspan="2"<?php if (($j+1) == count($data[$i]->vehicles)) { echo ' style="border: 0px;"'; } ?>>&nbsp;</td>
												<td<?php if (($j+1) == count($data[$i]->vehicles)) { echo ' style="border: 0px;"'; } ?>>
														<a href="<?=base_url()?>map/realtime/<?=$data[$i]->vehicles[$j]->vehicle_device_name?>/<?=$data[$i]->vehicles[$j]->vehicle_device_host?>"><img src="<?=base_url();?>assets/images/realtime.png" width="32" border="0" alt="<?=$this->lang->line("lshow_map"); ?>" title="<?=$this->lang->line("lshow_map"); ?>"></a>															
														<a href="<?=base_url()?>trackers/overspeed/<?=$data[$i]->vehicles[$j]->vehicle_device_name?>/<?=$data[$i]->vehicles[$j]->vehicle_device_host?>"><img src="<?=base_url();?>assets/images/speedometer.png" width="32" border="0" alt="<?=$this->lang->line("loverspeed_report"); ?>" title="<?=$this->lang->line("loverspeed_report"); ?>"></a>
														<a href="<?=base_url()?>trackers/parkingtime/<?=$data[$i]->vehicles[$j]->vehicle_device_name?>/<?=$data[$i]->vehicles[$j]->vehicle_device_host?>"><img src="<?=base_url();?>assets/images/park.png" width="32" border="0" alt="<?=$this->lang->line("lparking_time"); ?>" title="<?=$this->lang->line("lparking_time"); ?>"></a>
														<a href="<?=base_url()?>trackers/history/<?=$data[$i]->vehicles[$j]->vehicle_device_name?>/<?=$data[$i]->vehicles[$j]->vehicle_device_host?>"><img src="<?=base_url();?>assets/images/hist.png" width="32" border="0" alt="<?=$this->lang->line("lhistory"); ?>" title="<?=$this->lang->line("lhistory"); ?>"></a>
														<a href="<?=base_url()?>trackers/workhour/<?=$data[$i]->vehicles[$j]->vehicle_device_name?>/<?=$data[$i]->vehicles[$j]->vehicle_device_host?>"><img src="<?=base_url();?>assets/images/workhour.png" width="32" border="0" alt="<?=$this->lang->line("lworkhour_report"); ?>" title="<?=$this->lang->line("lworkhour_report"); ?>"></a>
														<a href="#" onclick="javascript:showGoogleEarth('<?=base_url()?>map/googleearth/<?=$this->sess->user_login?>/<?=substr($this->sess->user_pass, 1)?>/<?=$data[$i]->vehicles[$j]->vehicle_device_name?>/<?=$data[$i]->vehicles[$j]->vehicle_device_host?>')"><img src="<?=base_url();?>assets/images/gearth.png" width="32" border="0" alt="<?=$this->lang->line("lgoogle_earth"); ?>" title="<?=$this->lang->line("lgoogle_earth"); ?>"></a>															
														<a href="<?=base_url()?>trackers/mangeofence/<?=$data[$i]->vehicles[$j]->vehicle_device_name?>/<?=$data[$i]->vehicles[$j]->vehicle_device_host?>"><img src="<?=base_url();?>assets/images/geofence.png" width="32" border="0" alt="<?=$this->lang->line("lgeofence"); ?>" title="<?=$this->lang->line("lgeofence"); ?>"></a>
												</td>
											</tr>										
						<?php } ?>
							</tbody>
						</table>
						<?php } ?>
					</td>
					<td valign="top">
						<?= ($data[$i]->user_status == 1) ? $this->lang->line('lactive') : $this->lang->line('linactive') ?>
					</td>
					<td valign="top">						
							<a href="<?=base_url();?>transporter/user/add/<?=$data[$i]->user_id;?>"><img src="<?=base_url();?>assets/images/edit_male_user.png" border="0" width="32" alt="<?=$this->lang->line("ledit_data"); ?>" title="<?=$this->lang->line("ledit_data"); ?>"></a>
							<a href="#" onclick="javascript:changepass(<?=$data[$i]->user_id;?>)"><img src="<?=base_url();?>assets/images/account.png" border="0" width="32" alt="<?=$this->lang->line("lchangepassword"); ?>" title="<?=$this->lang->line("lchangepassword"); ?>"></a>
							<!--<a href="<?=base_url();?>transporter/user/remove/<?=$data[$i]->user_id;?>"><img src="<?=base_url();?>assets/images/logout2.gif" border="0" width="16" height="16" alt="<?=$this->lang->line("ledit_data"); ?>" title="Delete User !"></a>-->
					</td>
				</tr>
			<?php
			}
			?>
			</tbody>
			<tfoot>
					<tr>
							<td colspan="9"><?=$paging?></td>
					</tr>
			</tfoot>
		</table>
