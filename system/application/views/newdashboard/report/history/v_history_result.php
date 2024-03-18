<style media="screen">
#report{
  background-color: #221f1f;
  color: white;
}
</style>

<script src="<?php echo base_url();?>assets/js/jsblong/jquery.table2excel.js"></script>
<script>
jQuery(document).ready(
		function()
		{
			jQuery("#export_xcel").click(function()
			{
        var myBlob = new Blob([jQuery('#isexport_xcel').html()], {
            type: 'application/vnd.ms-excel'
        });
        var url    = window.URL.createObjectURL(myBlob);
        var a      = document.createElement("a");
        document.body.appendChild(a);
        a.href     = url;
        a.download = "History <?php echo $title; ?>.xls";
        a.click();
			});
		}
	);
</script>

<div class="row">
	<div class="col-md-12 col-sm-12">
		<div class="panel">
			<header class="panel-heading" style="background-color:#221f1f;color:white;" id="report">REPORT</header>
				<div class="panel-body" id="bar-parent10">
					<div class="row">
					<?php if (sizeof($data) == 0) {?>
							<p>Data is empty</p>
					<?php }else{ ?>
						<div class="col-md-12 col-sm-12">

							<div class="col-lg-4 col-sm-4">
								<a href="javascript:void(0);" id="export_xcel" type="button" class="btn btn-md btn-primary">Export Excel</a>
							</div>

							<div id="isexport_xcel">
                <h5>
                  <b>
                    <?php echo $title; ?>
                  </b>
                </h5>
                <table width="100%" class="table table-striped" style="font-size:11px;">
            			<thead>
            				<tr>
            					<th width="2%">No.</td>
            					<th width="15%" colspan="2"><?=$this->lang->line("ldatetime"); ?></th>
            					<th><?=$this->lang->line("lposition"); ?></th>
            					<th width="10%"><?=$this->lang->line("lcoordinate"); ?></th>
            					<th width="8%"><?=$this->lang->line("lstatus"); ?></th>
            					<th width="8%"><?=$this->lang->line("lspeed"); ?><br />(<?=$this->lang->line("lkph"); ?>)</th>
            					<?php if (isset($vehicle) && (in_array(strtoupper($vehicle->vehicle_type), $this->config->item("vehicle_gtp")))) { ?>
            					<th width="8%"><?php echo $this->lang->line('lengine_1'); ?></th>
            					<th width="8%"><?=$this->lang->line("lodometer"); ?> (km)</th>
            					<?php } ?>
            					<th>Fuel (L)</th>
            					<!-- <th width="18px;">&nbsp;</th> -->
            				</tr>
            			</thead>
            			<tbody>
            			<?php for($i=0; $i < count($data); $i++) { ?>
            				<tr <?=($i%2) ? "class='odd'" : "";?>>
            					<td><?=$i+1?></td>
            					<td><?=$data[$i]->gps_date_fmt;?></td>
            					<td><?=$data[$i]->gps_time_fmt;?></td>
            					<td><?=$data[$i]->georeverse->display_name;?></td>
            					<td><?=$data[$i]->gps_latitude_real_fmt;?> <?=$data[$i]->gps_longitude_real_fmt;?></td>
            					<td style="text-align: center"><?=$data[$i]->gps_status;?></td>
            					<td style="text-align: center"><?=$data[$i]->gps_speed_fmt;?></td>
            					<?php if (isset($vehicle) && (in_array(strtoupper($vehicle->vehicle_type), $this->config->item("vehicle_gtp")))) { ?>
            					<td><?php echo $data[$i]->status1; ?></td>
            					<td style="text-align: center;"><?php echo $data[$i]->odometer; ?></td>
            					<?php } ?>
            					<td style="text-align: center;"><?php echo round($data[$i]->gps_mvd); ?></td>
            					<!-- <td><a href="<?=base_url(); ?>map/history/<?=$gps_name?>/<?=$gps_host?>/<?=$data[$i]->gps_id;?>"><img src="<?=base_url();?>assets/images/zoomin.gif" border="0"></a></td> -->
            				</tr>
            			<?php } ?>
            			</tbody>
            		</table>
							</div>
						</div>

					<?php } ?>

					</div>
				</div>
		</div>
	</div>
</div>
