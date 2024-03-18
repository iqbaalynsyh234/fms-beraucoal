<script>
    function checkall()
    {
        <?php for($j=0; $j < count($data); $j++) { ?>
        jQuery("#vehicle<?php echo $data[$j]->autoreport_vehicle_id; ?>").attr("checked", jQuery("#vehicleall").attr("checked"));
        <?php } ?>
    }
    
    function download_checklist()
    {
        var serialize = "";
        <?php for($j=0; $j < count($data); $j++) { ?>
            if (jQuery("#vehicle<?php echo $data[$j]->autoreport_vehicle_id; ?>").attr("checked"))
            {
                serialize += "&vid[]="+<?php echo $data[$j]->autoreport_vehicle_id; ?>;
            }
        <?php } ?> 
        
        jQuery.post('<?php echo base_url(); ?>download/download_checklist_trip', serialize,
        function(r)
        {
            if (r.error)
            {                    
                return;
                }
            }
            , "json"
        );   
    }
    
        
</script>

<!--<input type="checkbox" id="vehicleall" name="vehicleall" value="1" onclick="javascript:checkall()" />
<input class="button" type="button" value="Download All (CheckList)" onclick="javascript:download_checklist()" />-->
<table width="100%" cellpadding="3" class="table sortable no-margin" style="margin: 3px;">
			<thead>
				<tr>
					<th width="2%">No.</td>
					<th>Vehicle</th>
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
					<td><?=$data[$i]->autoreport_filename;?></td>
					<td>
                        <a href="<?=base_url();?>download/download_history_report/<?=$data[$i]->autoreport_id;?>"><img src="<?=base_url();?>assets/newfarrasindo/images/downarrow.png" border="0" alt="Download" title="Download"></a>
                        <!--<input type="checkbox" id="vehicle<?php echo $data[$i]->autoreport_vehicle_id; ?>" name="vehicle[]" value="<?php echo $data[$i]->autoreport_vehicle_id; ?>" />-->
					</td>					
				</tr>
			<?php
			}
			?>
			</tbody>
			<tfoot>
					<tr>
							<td colspan="6"><?=$paging?></td>
					</tr>
			</tfoot>
		</table>
