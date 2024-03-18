        <div class="block-border">
            <p><?php echo $this->lang->line("lengine_1"); ?>: <?php echo $this->lang->line("lon"); ?>(<?php echo $totalengine_on; ?>) , <?php echo $this->lang->line("loff"); ?>(<?php echo $totalengine_off; ?>)</p><!--</h3>-->
		<!--<h3><?php //echo $this->lang->line("lengine_2"); ?>: -->&nbsp;&nbsp;&nbsp;<?php //echo $this->lang->line("lrelease"); ?><!--(--><?php //echo $totalengine_hold; ?><!--) ,--> <?php //echo $this->lang->line("lunrelease"); ?><!--(--><?php //echo $totalengine_release; ?><!--)--></h3>
            <table class="table sortable no-margin" cellspacing="0" width="100%">
           	    <thead>
				<tr>
					<th width="2%">No.</td>
					<th width="20%" style="text-align: center;"><?=$this->lang->line("lperiod"); ?></th>
					<th width="30%" colspan="2" style="text-align: center"><?=$this->lang->line("lengine_1"); ?>&nbsp;&nbsp;</th>
				</tr>
                </thead>
                <tbody>
                <?php for($i=0; $i < count($rows); $i++) { ?>
				<tr <?=($i%2) ? "class='odd'" : "";?>>
					<td><?=$i+1+$offset?></td>
					<td style="text-align: center;"><?=date('D M, jS Y H:i:s ', $rows[$i]->gps_info_time_t+7*3600);?></td>
					<td style="text-align: center;"><?php echo ($rows[$i]->status1) ? $this->lang->line('lon') : $this->lang->line('loff'); ?></td>
				</tr>
                <?php } ?>	
                </tbody>
            </table>
            <?=$paging;?>
        </div>
<!-- End content -->
<!-- end new table -->				
