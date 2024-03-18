<script src="<?php echo base_url();?>assets/js/jsblong/jquery.table2excel.js"></script>
<script>
jQuery(document).ready(
		function()
		{
			jQuery("#export_xcel").click(function() 
			{ 
				window.open('data:application/vnd.ms-excel,' + encodeURIComponent(jQuery('#isexport_xcel').html()));
			});
		}
	);
</script>
<a class="button" href="javascript:void(0);" id="export_xcel">Export to Excel</a>
<p>
<div id="isexport_xcel">
<table width="100%" cellpadding="3" class="table sortable no-margin" style="margin: 3px;">
			<thead>
				<tr>
					<th style="text-align:center;">No.</th>
					<th style="text-align:center;">Owner</th>
					<th style="text-align:center;">Vehicle No</th>
					<th style="text-align:center;">Vehicle Name</th>
					<th style="text-align:center;">Area</th>
					<?php if($this->sess->user_level == 1){ ?>
						<th style="text-align:center;">Simcard</th>
					<?php } ?>
					<th style="text-align:center;">Last Position</th>
					<th style="text-align:center;">Last GPS Status</th>
					<th style="text-align:center;">Status</th>
				</tr>
			</thead>
			<tbody>
			<?php
			
			if(count($data) > 0){
			
			for($i=0; $i < count($data); $i++)
			{
			
			?>
				<tr <?=($i%2) ? "class='odd'" : "";?>>
					<td valign="top" align="center" style="text-align:center;"><?=$i+1+$offset?></td>
					<td valign="top" style="text-align:center;">
						<?php 
							if (isset($ruser))
							{
								foreach ($ruser as $use)
								{
									if ($use->user_id == $data[$i]->auto_user_id)
									{
										echo $use->user_name;
									}
								}
							}
						?>
					</td>
					<td valign="top" style="text-align:center;"><?=$data[$i]->auto_vehicle_no;?></td>
					<td valign="top" style="text-align:center;"><?=$data[$i]->auto_vehicle_name;?></td>
					<td valign="top" style="text-align:center;">
						<?php 
							if (isset($rcompany))
							{
								foreach ($rcompany as $com)
								{
									if ($com->company_id == $data[$i]->auto_vehicle_company)
									{
										echo $com->company_name;
									}
								}
							}
						?>
					</td>
					<?php if($this->sess->user_level == 1){ ?>
					<td valign="top" style="text-align:center;"><?=$data[$i]->auto_simcard;?></td>
					<?php } ?>
					<td valign="top" style="text-align:center;">
						<?=date("d-m-Y, H:i:s", strtotime($data[$i]->auto_last_update));?> <br />
						<?=$data[$i]->auto_last_position;?> <br />
						<small><?=$data[$i]->auto_last_lat;?> <?=$data[$i]->auto_last_long;?> <br />
						Engine : <?=$data[$i]->auto_last_engine;?>  </small>
					</td>
					<td valign="top" style="text-align:center;"><?=$data[$i]->auto_last_gpsstatus;?></td>
					<td valign="top" style="text-align:center;">
						<?php if($data[$i]->auto_status == "P") { ?>
							GPS Online
						<?php }else if ($data[$i]->auto_status == "K"){ ?>
							GPS Online (Delay)
						<?php }else if ($data[$i]->auto_status == "M"){ ?>
							GPS Offline
						<?php }else { ?>
							-
						<?php } ?>
					</td>
					
				</tr>
				
			<?php
			}
			}else{
			?>
			<tr><td colspan="15">No Available Data</td></tr>
			<?php } ?>
			</tbody>
			<tfoot>
					<tr>
						
						<td colspan="15"></td>
					</tr>
			</tfoot>
		</table>
</div>