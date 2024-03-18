		<table width="100%" cellpadding="3" class="table sortable no-margin" style="margin: 3px;">
			<thead>
				<tr>
					<th width="2%">No.</td>
					<th style="text-align:center;">DateTime</th>
					<th style="text-align:center;">Position</th>
					<th style="text-align:center;">Speed</th>
					<th style="text-align:center;">Map</th>
				</tr>
			</thead>
			<tbody>
			<?php
			for($i=0; $i < count($data); $i++)
			{
			?>
				<tr <?=($i%2) ? "class='odd'" : "";?>>
					<td style="text-align:center;"><?=$i+1+$offset?></td>
					<td><?=$data[$i]->gps_date_fmt." ".$data[$i]->gps_time_fmt;?></td>
					  <td><?=$data[$i]->georeverse->display_name;?></td>
					<td><?=$data[$i]->gps_speed_fmt;?> <?=$this->lang->line("lkph"); ?></td>
					<td><a href="<?=base_url(); ?>map/history/<?=$gps_name?>/<?=$gps_host?>/<?=$data[$i]->gps_id;?>"><img src="<?=base_url();?>assets/images/zoomin.gif" border="0"></a></td>
				</tr>
			<?php
			}
			?>
			</tbody>
			<tfoot>
					<tr>
							<td colspan="10"><?=$paging?></td>
					</tr>
			</tfoot>
		</table>
