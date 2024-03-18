<table width="100%" cellpadding="3" class="table sortable no-margin" style="margin: 3px;">
			<thead>
				<tr>
					<th width="2%">No.</td>
					<th>Vehicle</th>
					<th>Date</th>
					<th>Filename</th>
					<th>Control</th>
				</tr>
			</thead>
			<tbody>
			<?php
			for($i=0; $i < count($data); $i++)
			{
			?>
				<tr <?=($i%2) ? "class='odd'" : "";?>>
					<td><?=$i+1+$offset?></td>
					<td><?=$data[$i]->autoreport_vehicle_name." ".$data[$i]->autoreport_vehicle_no;?></td>
					<td><?=date("d/m/Y",strtotime($data[$i]->autoreport_data_startdate));?></td>
					<td><?=$data[$i]->autoreport_filename;?></td>
					<td>
                        <a href="<?=$data[$i]->autoreport_download_path;?>"><img src="<?=base_url();?>assets/newfarrasindo/images/downarrow.png" border="0" alt="Download" title="Download"></a>
                    </td>					
				</tr>
			<?php
			}
			?>
			</tbody>
			<tfoot>
					<tr>
							<td colspan="10"></td>
					</tr>
			</tfoot>
		</table>