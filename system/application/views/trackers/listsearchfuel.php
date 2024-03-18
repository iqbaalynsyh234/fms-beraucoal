        <div class="block-border">
            <table class="table sortable no-margin" cellspacing="0" width="100%">
                <thead>
				<tr>
					<th width="2%">No.</td>
					<th style="text-align: center;" width="15%" colspan="2"><?=$this->lang->line("ldatetime"); ?></th>
					<th style="text-align: center;"><?=$this->lang->line("lposition"); ?></th>
					<th width="10%" style="text-align: center;"><?=$this->lang->line("lcoordinate"); ?></th>
					
					<th width="8%" style="text-align: center;"><?=$this->lang->line("lfuel"); ?></th>
					<th width="18px;">&nbsp;</th>
				</tr>
                </thead>
			<tbody>
			<?php for($i=0; $i < count($data); $i++) { ?>
			<?php
				$t = dbmaketime($data[$i]->gps_info_time)+7*3600;				
				
			?>
				<tr <?=($i%2) ? "class='odd'" : "";?>>
					<td><?=$i+1+$offset?></td>
					<td style="text-align: center;"><?php echo date("d/m/Y", $t); ?></td>
					<td style="text-align: center;"><?php echo date("H:i:s", $t);;?></td>
					<?php if (isset($data[$i]->gpsinfo)) { ?>
					<td style="text-align: center;"><?php echo $data[$i]->gpsinfo->georeverse->display_name;?></td>										
					<td style="text-align: center;"><?php echo $data[$i]->gpsinfo->gps_latitude_real_fmt;?> <?php echo $data[$i]->gpsinfo->gps_longitude_real_fmt;?></td>
					
					<?php } else { ?>
					<td style="text-align: center;">-</td>					
					<td style="text-align: center;">-</td>
				
					<?php } ?>
					<td style="text-align: center;">
						<?php 
							echo $data[$i]->fuel; 
						?>
                    </td>
				</tr>
			<?php } ?>
			</tbody>
            </table>
            <?=$paging;?>
        </div>
<!-- End content -->
<!-- end new table -->				
