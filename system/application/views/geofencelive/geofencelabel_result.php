		<table width="100%" cellpadding="3" class="table sortable no-margin" style="margin: 3px;">
			<thead>
				<tr>
					<th valign="top" style="text-align:center;">No.</td>
					<th valign="top" style="text-align:center;"><a href="#" onclick="javascript:order('geofence_name')"><?if ($sortby == 'geofence_name') { echo '<u>'; }?>Geofence Name<?if ($sortby == 'geofence_name') { echo '</u>'; }?></a></th>
					<th valign="top" style="text-align:center;">Geofence Type</th>
					<?php if ($this->sess->user_group == 0){ ?>					
					<th valign="top" style="text-align:center;">Control</th>					
					<?php } ?> 
				</tr>
			</thead>
			<tbody>
			<?php
			if(count($data) > 0){
			for($i=0; $i < count($data); $i++)
			{
			?>
				<tr <?=($i%2) ? "class='odd'" : "";?>>
					<td valign="top" style="text-align:center;"><?=$i+1+$offset?></td>
					<td valign="top" style="text-align:left;"><?=$data[$i]->geofence_name;?></td>
					<td valign="top" style="text-align:center;">
						<?php 
						$geofencetype = "-";
						if (isset($data[$i]->geofence_type)) { 
							if($data[$i]->geofence_type == "ho"){
								$geofencetype = "Head Office";
							}
							if($data[$i]->geofence_type == "cust"){
								$geofencetype = "Customer";
							}
							if($data[$i]->geofence_type == "bo"){
								$geofencetype = "Branch Office";
							}
							if($data[$i]->geofence_type == "rest"){
								$geofencetype = "Rest Area";
							}
							if($data[$i]->geofence_type == "pom"){
								$geofencetype = "Pom Bensin";
							}
							if($data[$i]->geofence_type == "pl"){
								$geofencetype = "Pool";
							}
					
						}
						?>
						<?php echo $geofencetype;?>
					</td>
					<?php if ($this->sess->user_group == 0)  { ?>
					<td valign="top" style="text-align:center;">
						<a href="#" onclick="javascript:edit('<?=$data[$i]->geofence_id;?>')"><img src="<?=base_url();?>assets/images/edit.gif" border="0" alt="<?=$this->lang->line("ledit_data"); ?>" title="<?=$this->lang->line("ledit_data"); ?>"></a>
						<a href="#" onclick="javascript:delete_data(<?=$data[$i]->geofence_id;?>)"><img src="<?=base_url();?>assets/images/trash.gif" border="0" alt="<?=$this->lang->line("lremove_data"); ?>" title="<?=$this->lang->line("lremove_data"); ?>"></a>
					</td>
					<?php } ?>
				</tr>
			<?php
			}
			}else{
				echo "<tr><td colspan='5'>No Data Available</td></tr>";
			}
			?>
			</tbody>
			<tfoot>
					<tr>
						<td colspan="5"></td>
							<!--<td colspan="5"><?=$paging?></td>-->
					</tr>
			</tfoot>
		</table>
