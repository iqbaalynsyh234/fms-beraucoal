		<font size="1"><b><?php echo $this->lang->line("lengine_1"); ?>: <?php echo $this->lang->line("lon"); ?><font color="green"> ( <?php echo $totalengine_on; ?> )</font> , <?php echo $this->lang->line("loff"); ?> <font color="red">( <?php echo $totalengine_off; ?> )</font><!--</h3>-->

		<p />
		<table width="100%" cellpadding="3" class="tablelist">
			<thead>
				<tr>
					<th width="2%"><font size="1"><b>No.</b></font></td>
					<th width="25%"><font size="1"><b><?=$this->lang->line("lperiod"); ?></b></font></th>
					<th width="30%" colspan="2" style="text-align: left"><font size="1"><b><?=$this->lang->line("lengine_1"); ?>&nbsp;&nbsp;</b></font></th>

					<th>&nbsp;</th>
				</tr>
			</thead>
			<tbody>
			<?php for($i=0; $i < count($rows); $i++) { ?>
				<tr <?=($i%2) ? "class='odd'" : "";?>>
					<td><font size="1"><b><?=$i+1+$offset?></b></font></td>
					<td style="text-align:center"><font size="1"><b><?=date('D M, jS Y H:i:s ', $rows[$i]->gps_info_time_t);?></b></font></td>
					<td style="text-align:left"><font size="1"><b><?php echo ($rows[$i]->status1) ? $this->lang->line('lon') : $this->lang->line('loff'); ?></b></font></td>

					<td>&nbsp;</td>
				</tr>
			<?php } ?>
			</tbody>
			<tfoot>
				<tr>
					<td colspan="5"><font size="1"><?=$paging;?></font></td>
				</tr>
			</tfoot>
		</table>