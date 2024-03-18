        <div class="block-border">
            <?php echo $this->lang->line("lpulse_total"); ?> <?php echo date("d/m/Y H:i:s", $this->period1); ?> - <?php echo date("d/m/Y H:i:s", $this->period2); ?>: <b>Rp. <?php echo number_format($total_pulse, 0, "", "."); ?></b>
            <table class="table sortable no-margin" cellspacing="0" width="100%">
                <thead>
				<tr>
					<th width="2%">No.</td>
					<th width="15%" colspan="2"><?=$this->lang->line("ldatetime"); ?></th>
					<th><?=$this->lang->line("lpulse"); ?></th>
					<th width="18px;">&nbsp;</th>
				</tr>
			</thead>
			<tbody>
			<?php for($i=0; $i < count($data); $i++) { ?>
				<tr <?=($i%2) ? "class='odd'" : "";?>>
					<td><?=$i+1+$offset?></td>
					<td><?=date("d/m/Y", $data[$i]->gps_info_time_t);?></td>
					<td><?=date("H:i:s", $data[$i]->gps_info_time_t);?></td>
					<td style="text-align: right;">Rp. <?=number_format($data[$i]->pulse, 0, "", ".");?></td>
					<td>&nbsp;</td>
				</tr>
			<?php } ?>
			</tbody>
            </table>
            <?=$paging;?>
        </div>
<!-- End content -->
<!-- end new table -->				
