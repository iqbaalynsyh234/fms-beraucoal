		<table width="100%" cellpadding="3" class="table sortable no-margin" style="margin: 3px;">
			<thead>
				<tr>
					<th width="2%" align="right">No.</td>
					<?php if ($this->sess->user_type != 2) { ?>					
					<th width="20%" align="left"><?=$this->lang->line("lusername"); ?></a></th>
					<?php } ?>
					<th width="25%" align="left"><?=$this->lang->line("lvehicle"); ?></th>
					<th width="15%" align="center"><?=$this->lang->line("lalert_time"); ?></th>														
					<th width="10%" align="left"><?=$this->lang->line("lalert_type"); ?></th>
					<th width="40%" align="left"><?=$this->lang->line("ldescription"); ?></th>
					<th>&nbsp;</th>
				</tr>
			</thead>
			<tbody>
			<?php
			for($i=0; $i < count($data); $i++)
			{
				list($devicehost, $devicename) = explode("@", $data[$i]->vehicle_device);
				$t = dbmaketime($data[$i]->alerttime);

				if ($data[$i]->alerttype == "geofence")
				{
				}
				else
				if ($data[$i]->alerttype == "maxspeed")
				{
					list($max, $curr) = explode("_", $data[$i]->alertdesc);
					$desc = sprintf("Max speed alert ( %.2f kph / %.2f kph )", $curr, $max);
				}
				else
				if ($data[$i]->alerttype == "maxparking")
				{
					list($begin, $sett) = explode("_", $data[$i]->alertdesc);
					$desc = sprintf("Max parking time ( > %d m )", $sett);
				}
				else
				{
					$desc = $data[$i]->alertdesc;
				}
				
			?>
				<tr <?=($i%2) ? "class='odd'" : "";?>>
					<td valign="top" align="right"><?=$i+1+$offset?></td>
					<?php if ($this->sess->user_type != 2) { ?>	
					<td valign="top" align="left"><?=$data[$i]->user_name;?></td>
					<?php } ?>
					<td valign="top" align="left"><a href="<?=base_url()?>map/realtime/<?=$devicehost;?>/<?=$devicename;?>"><font color="#0000ff"><?=$data[$i]->vehicle_name;?> - <?=$data[$i]->vehicle_no;?></font></a></td>
					<td valign="top" align="center"><?php echo date("d/m/Y H:i:s", $t); ?></td>
					<td valign="top" align="left"><?php echo $data[$i]->alerttype;?></td>
					<td valign="top" align="left"><?php echo $data[$i]->alertdesc;?></td>
					<td>&nbsp;</td>
				</tr>
			<?php
			}
			?>
			</tbody>
			<?php if (isset($paging)) { ?>
			<tfoot>
					<tr>
							<td colspan="7"><?=$paging?></td>
					</tr>
			</tfoot>
			<?php } ?>
		</table>
