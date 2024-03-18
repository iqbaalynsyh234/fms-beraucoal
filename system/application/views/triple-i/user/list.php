<script>
	jQuery(document).ready(
		function()
		{
			showclock();
			
			jQuery("#sortby").val('<?=$sortby?>');
			jQuery("#orderby").val('<?=$orderby?>');
			
			field_onchange();
			page(0);			
		}
	);
	
	function page(p)
	{
		jQuery("#offset").val(p);
		jQuery("#result").html("<?=$this->lang->line("lwait_loading_data");?>");
		
		jQuery.post("<?=base_url();?>transporter/user/search/"+p, jQuery("#frmsearch").serialize(),
			function(r)
			{
				jQuery("#result").html(r.html);		
				jQuery("#total").html(r.total);
				jQuery("#totalvehicle").html(r.totalvehicle);				
			}
			, "json"
		);
	}
	
	function field_onchange()
	{
		var v = jQuery("#field").val();

		jQuery("#keyword").hide();
		jQuery("#type").hide();
		jQuery("#status").hide();
		jQuery("#company").hide();
		jQuery("#vehicle_type").hide();
		jQuery("#usergroup1").hide();
		
		switch(v)
		{
			case "vexpired":
			case "vactive":
			case "vvisible":
			break;
			case "user_type":
				jQuery("#type").show();
				break;
			case "user_status":
				jQuery("#status").show();
				break;
			case "vehicle_type":
				jQuery("#vehicle_type").show();
				break;	
			case "user_company":
				jQuery("#company").show();
				loadgroup1();
			break;			
			default:
				jQuery("#keyword").show();			
		}
	}
	
	function frmsearch_onsubmit()
	{
		page(0);
		return false;
	}

	function order(by)
	{						
		if (by == jQuery("#sortby").val())
		{
			if (jQuery("#orderby").val() == "asc")
			{
				jQuery("#orderby").val("desc");
			}
			else
			{
				jQuery("#orderby").val("asc");
			}
		}
		else
		{
			jQuery("#orderby").val('asc')
		}
		
		jQuery("#sortby").val(by);
		page(0);
	}		

	function showGoogleEarth(txt)
	{
		
		showdialog('<h3><?=$this->lang->line('lgoogle_earth_network_link_desc')?></h3>' + txt, '<?=$this->lang->line('lgoogle_earth_network_link')?>', 1000, 150);
	}
	
        function loadgroup1()
        {
                jQuery.post("<?php echo base_url(); ?>group/options", {usersite: jQuery("#company").val(), showadmin: 0},
                        function(r)
                        {
                                if (r.empty)
                                {
                                       jQuery("#usergroup1").hide();
	                                 return;
                                }

                                jQuery("#usergroup1").show();
                                jQuery("#usergroup1").html(r.html);
                        }
                        , "json"
                );
        }	
</script>
<div style="position: absolute; margin: 0; padding: 0; z-index: 1000; width: 100%;"> 
<?=$navigation;?>
	<div id="main" style="margin: 20px;">
		<br />&nbsp;
		<h1><?=$this->lang->line("luser_list"); ?> (<span id="total"></span>), <?php echo "Public Vehicle";?> (<span id="totalvehicle"></span>)</h1>
		<h2><?=$this->lang->line("lsearch"); ?></h2>
		<form name="frmsearch" id="frmsearch" onsubmit="javascript:return frmsearch_onsubmit()">
			<input type="hidden" name="offset" id="offset" value="" />
			<input type="hidden" id="sortby" name="sortby" value="" />
			<input type="hidden" id="orderby" name="orderby" value="" />	
			<table width="100%" cellpadding="3" class="tablelist">
				<tr>
					<td width="10%"><?=$this->lang->line("lsearchby");?></td>
					<td>
						<select id="field" name="field" onchange="javascript:field_onchange()">
								<option value="user_login"><?=$this->lang->line("llogin");?></option>
								<option value="user_name"><?=$this->lang->line("lname");?></option>
								<!--<option value="vehicle"><?=$this->lang->line("lvehicle");?></option>
								<option value="vehicle_card_no"><?=$this->lang->line("lexpire_card_no");?></option>-->
						</select>
						<input type="text" name="keyword" id="keyword" value="" class="formdefault" />
						<input type="text" name="cp" id="cp" value="" class="formdefault" style="display: none;" />
						<select id="type" name="type" style="display: none;">
							<option value="1">Administrator</option>
							<option value="2">Regular</option>
							<option value="3">Agent</option>
						</select>
						<select id="status" name="status" style="display: none;">
							<option value="1"><?=$this->lang->line("lactive");?></option>
							<option value="2"><?=$this->lang->line("linactive");?></option>
						</select>
						<select name='vehicle_type' id='vehicle_type' style="display: none;">
							<?php 
								foreach($this->config->item("vehicle_type") as $key=>$val) { 
									if (! in_array($key, $this->config->item('vehicle_type_visible'))) continue;
							?>							
							<option value="<?php echo $key; ?>" <?php echo (isset($_POST['vehicle_type']) && ($key==$_POST['vehicle_type'])) ? " selected" : "";?>><?php echo $key; ?></option>
							<?php } ?>								
						</select>						
						<select id="company" name="company" onchange="javascript: loadgroup1()" style="display: none;">
							<?php for($i=0; $i < count($companies); $i++) { ?>
							<option value="<?php echo $companies[$i]->company_id; ?>"><?php echo $companies[$i]->company_name; ?></option>
							<?php } ?>
							</select>
							<span id="usergroup1"></span>
					</td>
				</tr>
				<tr>
					<td style="border: 0px;">&nbsp;</td>
					<td style="border: 0px;"><input type="submit" value="<?=$this->lang->line("lsearch");?>" /></td>
				</tr>
			</table>
		</form>
		[ <a href="<?=base_url();?>transporter/user/add"><font color="#0000ff"><?=$this->lang->line("ladd"); ?></font></a> ]
		<div id="result"></div>		
	</div>
</div>
