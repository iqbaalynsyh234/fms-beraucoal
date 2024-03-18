		<font size="1"><b><?=$this->lang->line("llongtimetotal"); ?>: <?=$longtime;?></b></font><p />
		<table width="100%" cellpadding="3" class="tablelist">
			<thead>
				<tr>
					<th width="2%"><font size="1"><b>No.</b></font></td>
					<th colspan="2"><font size="1"><b><?=$this->lang->line("lperiod"); ?></b></font></th>
					<th width="30%" style="text-align: right;"><font size="1"><b><?=$this->lang->line("llongtime"); ?></b></font>&nbsp;&nbsp;</th>
				</tr>
			</thead>
			<tbody>
			<?php for($i=0; $i < count($data); $i++) { ?>
				<tr <?=($i%2) ? "class='odd'" : "";?>>
					<td><font size="1"><b><?=$i+1+$offset?></b></font></td>
					<td><font size="1"><b><?=date('M, jS Y H:i:s ', $data[$i][0]);?></b></font></td>
					<td><font size="1"><b><?=date('M, jS Y H:i:s ', $data[$i][1]);?></b></font></td>
					<td style="text-align: right;"><font size="1"><b><?=$data[$i][3];?></b></font>&nbsp;&nbsp;</td>
				</tr>
			<?php } ?>	
			</tbody>
			<tfoot>	
				<tr>
					<td colspan="7"><?=$paging;?></td>
				</tr>
			</tfoot>
		</table>