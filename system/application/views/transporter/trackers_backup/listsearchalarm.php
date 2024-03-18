        <div class="block-border">
            <table class="table sortable no-margin" cellspacing="0" width="100%">
                <thead>
				<tr>
					<th width="2%">No.</td>
					<th width="20%" style="text-align: center;"><?=$this->lang->line("ldatetime"); ?></th>
					<th width="50%" style="text-align: center;"><?=$this->lang->line("lalarm"); ?></th>
					<th style="text-align: center;"><?=ucfirst($this->lang->line("ldata")); ?></th>
				</tr>
			</thead>
			<tbody>
			<?php for($i=0; $i < count($data); $i++) { ?>
				<tr <?=($i%2) ? "class='odd'" : "";?>>
					<td><?=$i+1+$offset?></td>
					<td style="text-align: center;"><?php echo date("d/m/Y H:i:s", $data[$i]->gps_info_time_t+7*3600);?></td>
					<td style="text-align: center;">(<?php echo $data[$i]->gps_info_alarm_alert; ?>) <?php echo $data[$i]->gps_info_alarm_alert_name; ?></td>
					<td style="text-align: center;"><?php echo $data[$i]->gps_info_alarm_data; ?></td>
				</tr>
			<?php } ?>
			</tbody>
            </table>
            <?=$paging;?>
        </div>
<!-- End content -->
<!-- end new table -->				

