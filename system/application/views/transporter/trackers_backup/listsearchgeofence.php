        <div class="block-border">
            <table class="table sortable no-margin" cellspacing="0" width="100%">
                <thead>
				<tr>
					<th width="2%">No.</td>
					<th width="20%" style="text-align: center;"><?=$this->lang->line("ldatetime"); ?></th>
					<th width="20%" style="text-align: center;"><?=$this->lang->line("lstatus"); ?></th>
					<th style="text-align: center;"><?=$this->lang->line("lgeofence"); ?></th>
					<th width="18px;">&nbsp;</th>
				</tr>
                </thead>
				<tbody>
			<?php 
			for($i=0; $i < count($data); $i++) { 
			if ($geoname != "All" && $data[$i]->geofence_name == $geoname) {
			?>
				<tr <?=($i%2) ? "class='odd'" : "";?>>
					<td><?=$i+1+$offset?></td>
					<td style="text-align: center;"><?php echo date("d/m/Y H:i:s", $data[$i]->geoalert_time_t);?></td>
					<td style="text-align: center;"><?php echo ($data[$i]->geoalert_direction == 2) ? $this->lang->line("lout") : $this->lang->line("lin"); ?></td>
					<td>
					<?php if ($data[$i]->geofence_name) { ?>
					<?php echo $data[$i]->geofence_name; ?>
					<?php } else { ?>
					<?php echo $data[$i]->geofence_coordinate; ?>
					<?php } ?>
					</td>
				</tr>
			<?php }
				if ($geoname == "All") { ?>
				<tr <?=($i%2) ? "class='odd'" : "";?>>
					<td><?=$i+1+$offset?></td>
					<td style="text-align: center;"><?php echo date("d/m/Y H:i:s", $data[$i]->geoalert_time_t);?></td>
					<td style="text-align: center;"><?php echo ($data[$i]->geoalert_direction == 2) ? $this->lang->line("lout") : $this->lang->line("lin"); ?></td>
					<td>
					<?php if ($data[$i]->geofence_name) { ?>
					<?php echo $data[$i]->geofence_name; ?>
					<?php } else { ?>
					<?php echo $data[$i]->geofence_coordinate; ?>
					<?php } ?>
					</td>
				</tr>
			<?php	} } ?>
			</tbody>
            </table>
            <?=$paging;?>
        </div>
<!-- End content -->
<!-- end new table -->				

