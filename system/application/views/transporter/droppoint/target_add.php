<!--<script type="text/javascript" src="<?php echo base_url();?>assets/js/jquery.maskx.js"></script>-->
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
			//jQuery('#amount').maskx({maskx: 'money'});
			jQuery("#startdate2").datepicker(
				{
							dateFormat: 'yy-mm-dd'
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
			jQuery("#enddate2").datepicker(
				{
							dateFormat: 'yy-mm-dd'
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
			
		}
		
	);
	
function frmadd_onsubmit()
{
	jQuery("#loader2").show();
	jQuery.post("<?=base_url()?>droppoint/save_target", jQuery("#frmadd").serialize(),
	function(r)
	{
		jQuery("#loader2").hide();
		alert(r.message);
								
								if (r.error)
								{								
									return;									
								}								
								page();
								jQuery("#dialog").dialog("close");
							}
							, "json"
						);
						
						return false;
	
}

function company_onchange(){
		var data_company = jQuery("#company").val();
		if(data_company == 0){
			alert('Please Select Group!!');
			jQuery("#parent").html("<option value='0' selected='selected'>--Select Group--</option>");
		}else{
			var site = "<?=base_url()?>droppoint/get_parent_by_company/" + data_company;
		    jQuery.ajax({
		        url: site,
		        success: function(response){
					jQuery("#parent").html("");
		            jQuery("#parent").html(response);
		        },
		    	dataType:"html"
		    });

		}

	}
	
	function parent_onchange(){
		var data_parent = jQuery("#parent").val();
		if(data_parent == 0){
			alert('Please Select Group!!');
			jQuery("#distrep").html("<option value='0' selected='selected'>--Select Distrep--</option>");
		}else{
			var site = "<?=base_url()?>droppoint/get_distrep_by_parent/" + data_parent;
		    jQuery.ajax({
		        url: site,
		        success: function(response){
					jQuery("#distrep").html("");
		            jQuery("#distrep").html(response);
		        },
		    	dataType:"html"
		    });

		}

	}
	
	function distrep_onchange(){
		var data_distrep = jQuery("#distrep").val();
		if(data_distrep == 0){
			alert('Please Select Distrep !!');
			jQuery("#droppoint").html("<option value='0' selected='selected'>--Select Distrep--</option>");
		}else{
			var site = "<?=base_url()?>droppoint/get_droppoint_by_distrep/" + data_distrep;
		    jQuery.ajax({
		        url: site,
		        success: function(response){
					jQuery("#droppoint").html("");
		            jQuery("#droppoint").html(response);
		        },
		    	dataType:"html"
		    });

		}

	}
	
	


</script>
<?php 
	$nowdate = date("Y-m-d");
	$m = date("m");
	$Y = date("Y");
	$d = "1";
	$firstdate = date("Y-m-d", strtotime($Y."-".$m."-".$d));
?>
<div class="block-border">
<form id="frmadd" onsubmit="javascript: return frmadd_onsubmit()">		

<table width="100%" cellpadding="3" class="table sortable no-margin">
	<tr>
		<td colspan="3"><h2>Add Target Time</h2></td>
	</tr>
	<tr>
		<td colspan="2">Type</td>
		<td>:</td>
		<td>
			<input name="type" type="radio" value="0" checked> REGULAR</input>
			<input name="type" type="radio" value="1"> COMBINE</input>
		</td>
	</tr>
	<tr>
		<td colspan="2">Periode Start</td>
		<td>:</td>
		<td><input type='text' name="startdate" id="startdate2"  class="date-pick" value="<?=date("Y-m-d", (strtotime($firstdate)));?>"  maxlength='10'>
	</tr>
	<tr>
		<td colspan="2">Periode End</td>
		<td>:</td>
		<td><input type='text' name="enddate" id="enddate2"  class="date-pick" value="<?=date('Y-m-t')?>"  maxlength='10'>
	</tr>
	
	<tr>
		<td colspan="2">Area</td>
		<td>:</td>
		<td>
			<select id="company" name="company" onchange="javascript:company_onchange()">
				<option value="" selected='selected'>--Select Area--</option>
				<?php 
					$ccompany = count($rcompany);
					for($i=0;$i<$ccompany;$i++){
					if (isset($rcompany)&&($row->parent_company == $rcompany[$i]->company_id)){
						$selected = "selected"; 
						}else{
							$selected = "";
						}
						echo "<option value='" . $rcompany[$i]->company_id ."' " . $selected . ">" . $rcompany[$i]->company_name . "</option>";
					}
				?>
			</select>
		</td>
	</tr>
	
	<tr>
		<td colspan="2">Group</td>
		<td>:</td>
		<td>
			<select id="parent" name="parent" onchange="javascript:parent_onchange()">
				<option value="" selected='selected'>--Select Group--</option>
				<?php 
					$cparent = count($rparent);
					for($i=0;$i<$cparent;$i++){
						if (isset($row)&&($row->distrep_parent == $rparent[$i]->parent_id)){
						$selected = "selected"; 
					}else{
						$selected = "";
					}
						echo "<option value='" . $rparent[$i]->parent_id ."' " . $selected . ">" . $rparent[$i]->parent_name . "</option>";
					}
				?>
			</select>
		</td>
	</tr>
	
	<tr>
		<td colspan="2">Distrep</td>
		<td>:</td>
			<td>
				<select id="distrep" name="distrep" onchange="javascript:distrep_onchange()">
				<option value="" selected='selected'>--Select Distrep--</option>
				<?php 
					$cdistrep= count($rdistrep);
					for($i=0;$i<$cdistrep;$i++){
						if (isset($row)&&($row->distrep_parent == $rdistrep[$i]->distrep_id)){
							$selected = "selected"; 
							}else{
							$selected = "";
							}
							echo "<option value='" . $rdistrep[$i]->distrep_id ."' " . $selected . ">" . $rdistrep[$i]->distrep_name . "</option>";
						}
				?>
				</select>
			</td>
	</tr>
	
	<tr>
		<td colspan="2">Droppoint</td>
		<td>:</td>
			<td>
				<select id="droppoint" name="droppoint">
				<option value="" selected='selected'>--Select Droppoint--</option>
				<?php 
					$cdroppoint= count($rdroppoint);
					for($i=0;$i<$cdroppoint;$i++){
						if (isset($row)&&($row->distrep_parent == $rdroppoint[$i]->droppoint_id)){
							$selected = "selected"; 
							}else{
							$selected = "";
							}
							echo "<option value='" . $rdroppoint[$i]->droppoint_id ."' " . $selected . ">" . $rdroppoint[$i]->droppoint_id . "</option>";
						}
				?>
				</select>
			</td>
	</tr>
		
	
	<tr>
		<td colspan="2">Target (Hour)</td>
		<td>:</td>
		<td>
			<input type="text" name="hour" id="hour" value="" size="10" placeholder="ex: 07"/>
		</td>
	</tr>
	<tr>
		<td colspan="2">Target (Minute)</td>
		<td>:</td>
		<td>
			<input type="text" name="minute" id="minute" value="" size="10" placeholder="ex: 30"/>	
		</td>
	</tr>
	
	<tr>
		<td colspan="5">
			<input type="submit" value="Save " name="submit" id="submit"/>
			<input type="button" value=" Close " name="close" id="close" onclick="javascript:jQuery('#dialog').dialog('close');" /> 
			<img id="loader2" src="<?=base_url();?>assets/images/anim_wait.gif" border="0" alt="loading" title="loading" style="display:none;">
		</td>
	</tr>
	
</table>
</form>
</div>