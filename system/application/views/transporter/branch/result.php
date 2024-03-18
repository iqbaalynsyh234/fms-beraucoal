<div style="position: absolute; margin: 0; padding: 0; z-index: 1000; width: 100%;">
	<?=$navigation;?>
	<div id="main" style="margin: 20px;">
    <div class="block-border">
        <form class="block-content form">
		<h1>Branch Office List (<?php echo $total;?>)</h1>
        <br />
        [<a href="<?php echo base_url();?>transporter/branchoffice/add">Add Branch Office</a>]
        <br /><br />
        <table width="100%" cellpadding="3" class="table sortable no-margin">
			<thead>
				<tr>
					<th width="1%">&nbsp;</td>
					<th width="2%"><?=$this->lang->line("lno"); ?></td>
					<th width="10%"><?php echo "Name" ?></td>
					<th width="10%"><?php echo "Telegram Group ID (SOS Alert)" ?></td>
					<th width="10%"><?php echo "Telegram Group ID (Parking Alert)" ?></td>
					<th width="10%"><?php echo "Telegram Group ID (Speed Alert)" ?></td>
					<th width="10%"><?php echo "Telegram Group ID (Geofence Alert)" ?></td>
					<th width="10%"><?php echo "Control" ?></td>
				</tr>
			</thead>
			<tbody>
                <?php for($i=0;$i<count($data);$i++) { ?>
				<tr <?=($i%2) ? "class='odd'" : "";?>>
					<th width="1%">&nbsp;</td>
					<td width="2%"><?=$i+1+$offset?></td>
                    <td><?php echo $data[$i]->company_name;?></td>
                    <td><?php echo $data[$i]->company_telegram_sos;?></td>
					<td><?php echo $data[$i]->company_telegram_parkir;?></td>
					<td><?php echo $data[$i]->company_telegram_speed;?></td>
					<td><?php echo $data[$i]->company_telegram_geofence;?></td>
                    <td>
                    <a href="<?php echo base_url();?>transporter/branchoffice/edit/<?php echo $data[$i]->company_id;?>">
                    <img src="<?php echo base_url();?>assets/images/edit.gif" />
                    </a>
                    </td>
                </tr>
                <? } ?>
			</tbody>
			
			<tfoot>
					<tr>
						<td colspan="12"><?=$paging?></td>
					</tr>
			</tfoot>
			
		</table>
        </form>
        </div>
	</div>
</div>
