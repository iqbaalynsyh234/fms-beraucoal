<script>
jQuery.maxZIndex = jQuery.fn.maxZIndex = function(opt) {
	    var def = { inc: 10, group: "*" };
	    jQuery.extend(def, opt);
	    var zmax = 0;
	    jQuery(def.group).each(function() {
	        var cur = parseInt(jQuery(this).css('z-index'));
	        zmax = cur > zmax ? cur : zmax;
	    });
	    if (!this.jquery)
	        return zmax;
	
	    return this.each(function() {
	        zmax += def.inc;
	        jQuery(this).css("z-index", zmax);
	    });
	}
jQuery(document).ready(
		function()
		{
			//showclock();
			<?php 
			if(count($droppoint) > 0){
			for($i=0;$i<count($droppoint);$i++)
			{
			
			?>
			jQuery("#startdate_<?=$droppoint[$i]->droppoint_id;?>").datepicker(
				{
							dateFormat: 'dd-mm-yy'
						, 	startDate: '1900-01-01'
						, 	showOn: 'button'
						//, 	changeYear: true
						//,	changeMonth: true
						, 	buttonImage: '<?=base_url()?>assets/images/calendar.gif'
						, 	buttonImageOnly: true
						,	beforeShow: 
								function() 
								{	
									jQuery('#ui-datepicker-div').maxZIndex();
								}
				}
			);
			jQuery("#enddate_<?=$droppoint[$i]->droppoint_id;?>").datepicker(
				{
							dateFormat: 'dd-mm-yy'
						, 	startDate: '1900-01-01'
						, 	showOn: 'button'
						//, 	changeYear: true
						//,	changeMonth: true
						, 	buttonImage: '<?=base_url()?>assets/images/calendar.gif'
						, 	buttonImageOnly: true
						,	beforeShow: 
								function() 
								{	
									jQuery('#ui-datepicker-div').maxZIndex();
								}
				}
			);
			
		<?php } } ?>
			
		}
		
	);
function frmadd_onsubmit()
	{
		/*var dist = jQuery("#startdate_2181").val();
		var dist1 = jQuery("#enddate_2181").val();
		var dist2 = jQuery("#hour_2181").val();
		var dist3 = jQuery("#minute_2181").val();
		alert(dist);
		alert(dist1);
		alert(dist2);
		alert(dist3);*/
		
		jQuery("#loader_save").show();
		jQuery("#button_save").hide();
		jQuery.post("<?=base_url()?>target_perdistrep/save_target_all", jQuery("#frmadd").serialize(),	
			function(r)
			{
				jQuery("#loader_save").hide();
				jQuery("#button_save").show();
				if (r.error)
				{
					alert(r.message);
					return false;
				}
				
				alert(r.message);
				page(0);
			}
			, "json"
		);
		return false;
	}
</script>

<?php 
if(isset($distrep_name) && ($distrep_name->distrep_type == 1)){
	$type_name = "COMBINE";
}else{
	$type_name = "REGULAR";
}

if(isset($distrep_name) && ($distrep_name->distrep_report_status == 1)){
	$report_status = "TDS - A";
}else if($distrep_name->distrep_report_status == 2){
	$report_status = "TDS - B";
}else{
	$report_status = "ODS";
}
?>



<h3>Periode <?php echo date('d-m-Y', strtotime($sdate));?> - <?php echo date('d-m-Y', strtotime($edate));?></h3>
<h3><?=$company_name->company_name;?> - <?=$parent_name->parent_code;?> - <?=$distrep_name->distrep_vehicle_no;?></h3>
<h3>Kode Distrep: <?=$distrep_name->distrep_code;?></h3>
<h3>Tipe: <?=$type_name;?> (<?=$report_status;?>)</h3>
<!--<h3>DB TABLE: <?=$dbtable;?></h3>-->

<?php 
$this->dbtransporter = $this->load->database("transporter", true);
?>
<form id="frmadd" onsubmit="javascript: return frmadd_onsubmit()">	
<table width="100%" cellpadding="3" class="table sortable no-margin" style="margin: 3px;">
			<thead>
				<tr>
					<th style="text-align:center"><?=$this->lang->line("lno"); ?></td>
					<th style="text-align:center">Droppoint</th>
					<th style="text-align:center">Total Target (Month)</th>
					<th style="text-align:center">Target OTA</th>
					<th style="text-align:center">Target OTA (Update)</th>
					<th style="text-align:center">Periode</th>
					<th style="text-align:center">Periode (Update)</th>
				</tr>
			</thead>
			<tbody>
			<?php
			if(count($droppoint) > 0){
				
				$nowdate = date("Y-m-d");
				$m = date("m");
				$Y = date("Y");
				$d = "1";
				$firstdate = date("Y-m-d", strtotime($Y."-".$m."-".$d));
				
			?>
			<input type="hidden" name="distrep_data" id="distrep_data" value="<?=$distrep_name->distrep_id;?>" size="10" />
			<input type="hidden" name="type_data" id="type_data" value="<?=$distrep_name->distrep_type;?>" size="10" />
			<input type="hidden" name="company_data" id="company_data" value="<?=$company_name->company_id;?>" size="10" />
			<?php 
			for($i=0;$i<count($droppoint);$i++)
			{
				$total_rtarget = 0;
			?>
				
				<tr>
					<td valign="top" align="center" style="text-align:center;"><?=$i+1?></td>
					<td valign="top" align="left" style="text-align:left;">
						<small>(<?=$droppoint[$i]->droppoint_id;?>)</small> <?=$droppoint[$i]->droppoint_name;?> 
						<br /><small><?=$droppoint[$i]->droppoint_geofence;?> </small>
					</td>
					<?php 
						//count all droppoint
						$this->dbtransporter->select("target_id,target_time,target_startdate,target_enddate");						
						$this->dbtransporter->order_by("target_startdate", "desc");
						$this->dbtransporter->where("target_droppoint", $droppoint[$i]->droppoint_id);
						$this->dbtransporter->where("target_flag", 0);
						$this->dbtransporter->where("target_startdate >=",$sdate);
						$this->dbtransporter->where("target_enddate <=",$edate);
						$qtarget_all = $this->dbtransporter->get("droppoint_target");
						$rtarget_all = $qtarget_all->result();
						$total_rtarget_all = count($rtarget_all);
					?>	
					<td valign="top" align="center" style="text-align:center;">
						<?=$total_rtarget_all;?>
					</td>
					<?php 
						//count droppoint
						$this->dbtransporter->select("target_id,target_time,target_startdate,target_enddate");						
						$this->dbtransporter->order_by("target_startdate", "desc");
						$this->dbtransporter->where("target_droppoint", $droppoint[$i]->droppoint_id);
						$this->dbtransporter->where("target_flag", 0);
						$this->dbtransporter->where("target_startdate",$sdate);
						$this->dbtransporter->where("target_enddate",$edate);
						$qtarget = $this->dbtransporter->get("droppoint_target");
						$rtarget = $qtarget->result();
						$total_rtarget = count($rtarget);
					?>	
					<!-- time master -->
					<td valign="top" align="center" style="text-align:center;">
						<?php 
						if($total_rtarget > 0){
							for($j=0;$j<count($rtarget);$j++)
							{ ?>
								<?php echo date("H:i", strtotime($rtarget[$j]->target_time)); ?>
								
						<?php }}?>
					</td>
					
					<!-- time input -->
					<td valign="top" align="left" style="text-align:left;">
						<?php if($total_rtarget > 0){
							for($j=0;$j<count($rtarget);$j++)
							{ ?>
								<input type="text" name="hour_<?=$droppoint[$i]->droppoint_id;?>" id="hour_<?=$droppoint[$i]->droppoint_id;?>" value="<?=isset($rtarget[$j]) ? htmlspecialchars(date("H", strtotime($rtarget[$j]->target_time)), ENT_QUOTES) : "";?>" size="2" maxlength="2" placeholder="Hour"/>
								<input type="text" name="minute_<?=$droppoint[$i]->droppoint_id;?>" id="minute_<?=$droppoint[$i]->droppoint_id;?>" value="<?=isset($rtarget[$j]) ? htmlspecialchars(date("i", strtotime($rtarget[$j]->target_time)), ENT_QUOTES) : "";?>" size="2" maxlength="2" placeholder="Minutes"/>
						<?php }} ?>
						<?php if($total_rtarget == 0){ ?>
							<input type="text" name="hour_<?=$droppoint[$i]->droppoint_id;?>" id="hour_<?=$droppoint[$i]->droppoint_id;?>" value="" size="2" maxlength="2" placeholder="Hour"/>
							<input type="text" name="minute_<?=$droppoint[$i]->droppoint_id;?>" id="minute_<?=$droppoint[$i]->droppoint_id;?>" value="" size="2" maxlength="2" placeholder="Min"/>	
						<?php } ?>
					</td>
					
					<!-- date master -->
					<td valign="top" align="left" style="text-align:left;">
						<?php 
						if($total_rtarget > 0){
							for($j=0;$j<count($rtarget);$j++)
							{ ?>
								<?php echo date("d-m-Y", strtotime($rtarget[$j]->target_startdate));?> ~ <br /> <?php echo date("d-m-Y", strtotime($rtarget[$j]->target_enddate));?>
						<?php }} ?>
					</td>
					
					<!-- Date input-->
					<td valign="top" align="left" style="text-align:left;">
					<?php if($total_rtarget == 0){ ?>
						<input type='text' name="startdate_<?=$droppoint[$i]->droppoint_id;?>" id="startdate_<?=$droppoint[$i]->droppoint_id;?>"  class="date-pick" value="<?=date("d-m-Y", strtotime($firstdate));?>"  maxlength='10'> ~ <input type='text' name="enddate_<?=$droppoint[$i]->droppoint_id;?>" id="enddate_<?=$droppoint[$i]->droppoint_id;?>" class="date-pick" value="<?=date("t-m-Y");?>"  maxlength='10'>
					<?php } ?>
					<?php if($total_rtarget > 0){?>
					<?php for($j=0;$j<count($rtarget);$j++){ ?>
						<input type='text' name="startdate_<?=$droppoint[$i]->droppoint_id;?>" id="startdate_<?=$droppoint[$i]->droppoint_id;?>" class="date-pick" value="<?=isset($rtarget[$j]) ? htmlspecialchars(date("d-m-Y", strtotime($rtarget[$j]->target_startdate)), ENT_QUOTES) : "";?>"  maxlength='10' readonly > ~ <br />
						<input type='text' name="enddate_<?=$droppoint[$i]->droppoint_id;?>" id="enddate_<?=$droppoint[$i]->droppoint_id;?>" class="date-pick" value="<?=isset($rtarget[$j]) ? htmlspecialchars(date("d-m-Y", strtotime($rtarget[$j]->target_enddate)), ENT_QUOTES) : "";?>"  maxlength='10' readonly >
					<?php } }?>
					</td>
				</tr>
				
			<?php
			}
			}else{
			?>
			<tr><td colspan="14">No Available Data</td></tr>
			<?php
			}
			?>
			</tbody>
			<td colspan="10" valign="top" align="center" style="text-align:center;">
			<?php if(count($droppoint) > 0){ ?>
			<div id="button_save">
				<input type="submit" value="Save" name="submit" id="submit"/>
			</div>
				<img id="loader_save" src="<?=base_url();?>assets/images/ajax-loader.gif" border="0" alt="loading" title="loading" style="display:none;">
			<?php }?>
			</td>
			<tfoot>
				
						
			</tfoot>
		</table>
</form>