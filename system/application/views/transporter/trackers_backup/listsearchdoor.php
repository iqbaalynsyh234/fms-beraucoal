        <div class="block-border">
            <p><?php echo $this->lang->line("ldoor_status"); ?>: <?php echo $this->lang->line("lopened"); ?>(<?php echo $totalengine_opened; ?>) , <?php echo $this->lang->line("lclosed"); ?>(<?php echo $totalengine_closed; ?>)</p>
        </div>
        <div class="block-border">
            <table class="table sortable no-margin" cellspacing="0" width="100%">
                <thead>
				<tr>
					<th width="2%" style="text-align: center;">No.</td>
					<th width="20%" style="text-align: center;"><?=$this->lang->line("lperiod"); ?></th>
					<th colspan="2" style="text-align: center"><?=$this->lang->line("ldoor_status"); ?>&nbsp;&nbsp;</th>
				</tr>
			</thead>
			<tbody>
			<?php for($i=0; $i < count($rows); $i++) { ?>
				<tr <?=($i%2) ? "class='odd'" : "";?>>
					<td style="text-align: center;"><?=$i+1+$offset?></td>
					<td style="text-align: center;"><?=date('D M, jS Y H:i:s ', $rows[$i]->gps_info_time_t);?></td>
					<td style="text-align: center;"><?php echo ($rows[$i]->status1) ? $this->lang->line('lopened') : $this->lang->line('lclosed'); ?></td>
				</tr>
			<?php } ?>	
			</tbody>
            </table>
            <?=$paging;?>
        </div>
<!-- End content -->
<!-- end new table -->				
