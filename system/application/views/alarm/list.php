<script>
	jQuery.maxZIndex = jQuery.fn.maxZIndex = function(opt) {
	    /// <summary>
	    /// Returns the max zOrder in the document (no parameter)
	    /// Sets max zOrder by passing a non-zero number
	    /// which gets added to the highest zOrder.
	    /// </summary>    
	    /// <param name="opt" type="object">
	    /// inc: increment value, 
	    /// group: selector for zIndex elements to find max for
	    /// </param>
	    /// <returns type="jQuery" />
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
			alerttype_onchange();
			showclock();			
			page(0);		
			
			jQuery("#period1").datepicker(
				{
							dateFormat: 'dd/mm/yy'
						, 	startDate: '01/01/1900'
						, 	showOn: 'button'
						, 	buttonImage: '<?=base_url()?>assets/images/calendar.gif'
						, 	buttonImageOnly: true
						,	beforeShow: 
								function() 
								{	
									jQuery('#ui-datepicker-div').maxZIndex();
								}
				}
			);				
			
			jQuery("#period2").datepicker(
				{
							dateFormat: 'dd/mm/yy'
						, 	startDate: '01/01/1900'
						, 	showOn: 'button'
						, 	buttonImage: '<?=base_url()?>assets/images/calendar.gif'
						, 	buttonImageOnly: true
						,	beforeShow: 
								function() 
								{	
									jQuery('#ui-datepicker-div').maxZIndex();
								}
				}
			);				
		}				
	);
	
	function page(p)
	{
		if (! p) p = 0;
		jQuery("#offset").val(p);
		jQuery("#result").html("<?=$this->lang->line("lwait_loading_data");?>");
		
		jQuery.post("<?=base_url();?>alarm/search/"+p, jQuery("#frmsearch").serialize(),
			function(r)
			{
				jQuery("#result").html(r.html);		
				jQuery("#total").html(r.total);
				getAlarm();
			}
			, "json"
		);
	}
	
	function frmsearch_onsubmit()
	{
		page(0);
		return false;
	}
	
	function alerttype_onchange()
	{
		var option = jQuery("#alerttype").val();
		
		jQuery("#geofence").hide();
		jQuery("#speed").hide();
		jQuery("#parkir").hide();
		if (option.length > 0)
		{
			jQuery("#"+option).show();
		}
		
	}
	
	
</script>
<div style="position: absolute; margin: 0; padding: 0; z-index: 1000; width: 100%;"> 
<?=$navigation;?>
	<div id="main" style="margin: 20px;">
		<div class="block-border">
		<form  class="block-content form" name="frmsearch" id="frmsearch" onsubmit="javascript:return frmsearch_onsubmit()">
		<h1><?=$this->lang->line("lalarm_alert_list"); ?> (<span id="total"></span>)</h1>
		<fieldset class="grey-bg required">
		<legend><?=$this->lang->line("lsearchby");?></legend>
			<table width="100%" cellpadding="3" class="tablelist">
				<tr>
					<td style="border: 0px;" colspan="3">
						<select id="field" name="field">
							<?php if ($this->sess->user_type != 2) { ?>
							<option value="user_name"><?=$this->lang->line("lusername");?></option>
							<?php } ?>
							<option value="vehicle"><?=$this->lang->line("lvehicle");?></option>
							<option value="device"><?=$this->lang->line("ldevice_id");?></option>
						</select>
						<input type="text" name="keyword" id="keyword" value="" class="formdefault" />
					</td>
				</tr>
				<tr>
					<td style="border: 0px;"><?php echo $this->lang->line("lalert_type"); ?></td>
					<td style="border: 0px;" colspan="3">
						<select id="alerttype" name="alerttype" onchange="javascript:alerttype_onchange(this)">
							<option value=""><?php echo $this->lang->line("lall"); ?></option>
							<option value="geofence"<?php if ($alerttype == "geofence") echo "selected"; ?>><?php echo $this->lang->line("lgeofence"); ?></option>
							<option value="speed"<?php if ($alerttype == "speed") echo "selected"; ?>><?php echo $this->lang->line("lmax_speed"); ?></option>
							<option value="parkir"<?php if ($alerttype == "parkir") echo "selected"; ?>><?php echo $this->lang->line("lmax_parking_time"); ?></option>
						</select>					
						<select id="geofence" name="geofence">
							<option value=""><?php echo $this->lang->line("lall"); ?></option>
							<option value="1"><?php echo $this->lang->line("lenter"); ?></option>
							<option value="2"><?php echo $this->lang->line("lexit"); ?></option>
						</select>
						<span id="speed">
							<input type="text" name="speed" id="speed" value="" class="formdefault" size="3" maxlength="3" style="width: 60px;" /> <?php echo $this->lang->line("lkph"); ?>
						</span>
						<span id="parkir">
							<input type="text" name="parkir" id="parkir" value="" class="formdefault" size="3" maxlength="3" style="width: 60px;" /> <?php echo $this->lang->line("lminute"); ?>
						</span>						
					</td>
				</tr>				
				<tr id="tglperiod" style="border: 0px;">
					<td rowspan="2"><?=$this->lang->line("ldatetime"); ?></td>
					<td width="18%" style="border: 0px;">
						<input type='text' name="period1" id="period1"  class="date-pick" value="<?php echo ($period1) ? date("d/m/Y", $period1) : date("d/m/Y"); ?>"  maxlength='10'>
					</td>
					<td width="1%" align="middle" rowspan="2" style="border: 0px;"><?=$this->lang->line("luntil"); ?></td>
					<td style="border: 0px;"><input type='text' name="period2" id="period2"  class="date-pick" value="<?php echo ($period2) ? date("d/m/Y", $period2) : date("d/m/Y"); ?>"  maxlength='10'></td>
				</tr>	
				<tr id="jamperiod">
					<td>
						<select name="hperiod1" id="hperiod1">
						<?php $jam = isset($period1) ? date('G', $period1) : 0; ?>
						<?php $menit = isset($period1) ? date('i', $period1) : 0; ?>
						<?php $detik = isset($period1) ? date('s', $period1) : 0; ?>
						<?php for($i=0; $i < 24; $i++) { ?>							
								<option value="<?=$i?>"<?=($i == $jam) ? " selected" : ""?>><?=sprintf('%02d', $i)?></option>							
						<?php } ?>
						</select>
						<select name="mperiod1" id="mperiod1">
						<?php for($i=0; $i < 60; $i++) { ?>							
								<option value="<?=$i?>"<?=($i == $menit*1) ? " selected" : ""?>><?=sprintf('%02d', $i)?></option>							
						<?php } ?>
						</select>						
						<select name="speriod1" id="speriod1">
						<?php for($i=0; $i < 60; $i++) { ?>							
								<option value="<?=$i?>"<?=($i == $detik*1) ? " selected" : ""?>><?=sprintf('%02d', $i)?></option>							
						<?php } ?>
						</select>												
					</td>
					<td>
						<?php $jam = isset($period2) ? date('G', $period2) : 0; ?>
						<?php $menit = isset($period2) ? date('i', $period2) : 0; ?>
						<?php $detik = isset($period2) ? date('s', $period2) : 0; ?>
						<select name="hperiod2" id="hperiod2">
						<?php for($i=0; $i < 24; $i++) { ?>							
								<option value="<?=$i?>"<?php echo ($i == $jam) ? " selected" : ""?>><?=sprintf('%02d', $i)?></option>							
						<?php } ?>
						</select>
						<select name="mperiod2" id="mperiod2">
						<?php for($i=0; $i < 60; $i++) { ?>							
								<option value="<?=$i?>"<? echo ($i == $menit*1) ? " selected" : ""?>><?=sprintf('%02d', $i)?></option>							
						<?php } ?>
						</select>						
						<select name="speriod2" id="speriod2">
						<?php for($i=0; $i < 60; $i++) { ?>							
								<option value="<?=$i?>"<? echo ($i == $detik*1) ? " selected" : ""?>><?=sprintf('%02d', $i)?></option>							
						<?php } ?>
						</select>												
					</td>
				</tr>				
				<tr>
					<td style="border: 0px;">&nbsp;</td>
					<td style="border: 0px;"><input type="submit" value="<?=$this->lang->line("lsearch");?>" /></td>
				</tr>
			</table>
		</form>
		</fieldset>
		<div id="result"></div>		
	</div>
	</div>
</div>
