 <script>
			function vform(v)
			{
				showdialog();
				jQuery.post('<?php echo base_url(); ?>transporter/user/formvehicle/', {id: v},
					function(r)
					{
						showdialog(r.html, "<?=$this->lang->line("lupdate_vehicle"); ?>");
					}
					, "json"
				);
			}
			
			function driver_profile(v)
			{
				showdialog();
				jQuery.post('<?php echo base_url(); ?>transporter/driver/upload_image/', {id: v},
					function(r)
					{
						showdialog(r.html, "Driver Profile");
					}
					, "json"
				);
			}
			
	jQuery(document).ready(
		function()
		{
			showclock();
			<?php if (isset($_POST['field'])) { ?>
				jQuery("#field").val('<?=$_POST['field']?>')
			<?php } ?>
			<?php if (isset($_POST['keyword'])) { ?>
				jQuery("#keyword").val('<?=$_POST['keyword']?>')
			<?php } ?>			
			field_onchange();	
			location = "#atop";
			mypage(0);
		}
	);
	
	function page()
	{
		document.frmsearch.submit();
	}
	
	function field_onchange()
	{
		var v = jQuery("#field").val();

		jQuery("#keyword").hide();
		jQuery("#type").hide();
		jQuery("#status").hide();
		jQuery("#vehicle_type").hide();
		jQuery("#company").hide();
		jQuery("#usergroup1").hide();
		jQuery("#server").hide();
		jQuery("#branch_office").hide();
		
		var s = "delayed";
		if (v.substring(0, s.length) == s)
		{
			v = "delayed";
		}

		switch(v)
		{
			case "vexpired":
			case "vactive":
			case "delayed":
			break;
			case "vehicle_type":
				jQuery("#vehicle_type").show();
			break;
			case "user_company":
				jQuery("#company").show();
				loadgroup1();
			break;
			case "server":
				jQuery("#server").show();
			break;
			case "branch":
				jQuery("#branch_office").show();
			break;
			default:
				jQuery("#keyword").show();			
		}
	}
	
	function loadgroup1()
	{
		jQuery.post("<?php echo base_url(); ?>group/options<?php if (isset($_POST['group'])) { echo "/".$_POST['group']; } ?>", {usersite: jQuery("#company").val(), showadmin: 0},
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
		document.frmsearch.submit();
	}		

	function showGoogleEarth(txt)
	{
		
		showdialog('<h3><?=$this->lang->line('lgoogle_earth_network_link_desc')?></h3>' + txt, '<?=$this->lang->line('lgoogle_earth_network_link')?>', 1000, 150);
	}
	
	function contactus(id)
	{
		jQuery("#autoscroll").attr("checked", false)
		
		showdialog();
		jQuery.post("<?php echo base_url(); ?>home/contactus/"+id, {},
			function(r)
			{
				showdialog(r.html, r.title);
			}
			, "json"
		);		
	}
	
	function renew(id)
	{
		jQuery("#autoscroll").attr("checked", false)
		
		showdialog();
		jQuery.post("<?php echo base_url(); ?>vehicle/renew/"+id, {},
			function(r)
			{
				showdialog(r.html, r.title);
			}
			, "json"
		);
	}
	
	function paymentconfirmation(id)
	{
		jQuery("#autoscroll").attr("checked", false)
		showdialog();
		
		jQuery.post("<?php echo base_url(); ?>payment/confirmation/"+id, {},
			function(r)
			{
				showdialog(r.html, r.title);
			}
			, "json"
		);		
	}
	
	function sendsms(message, hp, success)
	{
		jQuery.post("<?php echo base_url(); ?>smsserver/send/", {message: message, hp: hp},
			function(r)
			{
				alert(success);
			}
		);		
	}
	
	function navsearch(val)
	{
		if (val)
		{
			jQuery("#plus").hide();
			jQuery("#minus").show();
			//jQuery("#divsearch").show("slow");
			jQuery("#map").css({"top":"20%"});
			return;
		}
		
		jQuery("#plus").show();
		//jQuery("#divsearch").hide("slow");
		jQuery("#minus").hide();
		jQuery("#layerswitcher").hide('slow');
		jQuery("#map").css({"top":"11%"});
	}
	
	function showmaplayer(val)
	{
		if (val)
		{
			jQuery("#showmaplayer").hide();
			jQuery("#hidemaplayer").show();
			jQuery("#layerswitcher").show('bounce');
			return;
		}
		
		jQuery("#showmaplayer").show();
		jQuery("#hidemaplayer").hide();
		jQuery("#layerswitcher").hide('slow');
	}
	
	function mypage(p)
	{
		if(p==undefined){
			p=0;
		}
		jQuery("#offset").val(p);
		jQuery("#loader").show();
		jQuery("#layerswitcher").html("");
		
		jQuery.post("<?=base_url();?>trackers/smartviewtest/", jQuery("#frmsearch").serialize(),
			function(r)
			{
				jQuery("#loader").hide();
				jQuery("#result").html(r.html);		
				jQuery("#total").html(r.total);	
			}
			, "json"
		);
	}
	
	function mypage2(p)
	{
		jQuery("#loader2").show();
		jQuery("#table-switcher").hide('slow');
		jQuery("#layerswitcher").html("Loading....");
		
		var x = jQuery("#slfollow").val();
		if (x == "all")
		{
			jQuery("#loader2").hide();
			jQuery("#layerswitcher").html("");
			redirect("<?php echo base_url();?>trackers/smartview");
		}
		else
		{
			jQuery("#layerswitcher").html("");
			jQuery.post("<?=base_url();?>trackers/followme/", jQuery("#frmfollowme").serialize(),
			function(r)
			{
				//jQuery("#showmaplayer").hide();
				//jQuery("#hidemaplayer").hide();
				//jQuery("#layerswitcher").hide();
				//jQuery("#tdsearch").hide();
				jQuery("#loader2").hide();
				jQuery("#result").html(r.html);	
				jQuery("#plus").show();
				jQuery("#minus").hide();
				jQuery("#map").css({"top":"11%"});					
			}
			, "json"
		);
		}
		
	}
	
	function frmfollowme_onsubmit()
	{
		jQuery("#layerswitcher").html("Loading....");
		uncheckall();
		mypage2(0);
		return false;
	}
	
	function frmsearch_onsubmit()
	{
		jQuery("#layerswitcher").html("Loading....");
		mypage(0);
		return false;
	}
	
	function checkall()
	{
		<?php 
			for($z=0; $z < count($data); $z++) 
			{ ?>
				jQuery("#vehicle<?php echo $data[$z]->vehicle_id; ?>").attr("checked", jQuery("#vehicleall").attr("checked"));
		<?php } ?>
	}
	
	function uncheckall()
	{
		<?php 
			for($z=0; $z < count($data); $z++) 
			{ ?>
				jQuery("#vehicle<?php echo $data[$z]->vehicle_id; ?>").attr("checked", false);
		<?php } ?>
		jQuery("#vehicleall").attr("checked",false);
	}
	
	function s_map(v)
	{
		jQuery('#map').hide('slow');
		jQuery('#tblrealtime').hide('slow');
		jQuery('#boxtable').hide('slow');
		
		if (v)
		{
			jQuery('#map').show('slow');
		}
		else
		{
			jQuery('#tblrealtime').show('slow');
			jQuery('#boxtable').css({"top":"50px"});
			jQuery('#boxtable').show('slow');
		}
		
	}	
	
	function s_all()
	{
		jQuery('#map').hide('slow');
		jQuery('#tblrealtime').hide('slow');
		jQuery('#boxtable').hide('slow');
		//
		jQuery('#map').show('slow');
		jQuery('#tblrealtime').show('slow');
		jQuery('#boxtable').show('slow');
		jQuery('#boxtable').css({"top":"500px"});
		
	}
	
	
</script>

<script>
function feature_member() {
}
</script>
<style>
	#box_layerswitcher {
		border:0px solid black;
		background-repeat: repeat -x; 
		height: 100px;
		overflow:auto;
		overflow-y:hidden;
		overflow-x:hidden;
	}
	
	#layerswitcher {
		color: black;
		font-family: sans-serif;
		font-size: 11px;
		
	}
	
	#boxtable {
		background-color:white;
		font-size: 12px;
		margin-top: 500px;
		border:1px solid #D8D8D8;
	}
	
	.toprow {
		font-style: italic;
		text-align: center;
		background-color: #FFFFCC;
	}
	
	#listvehicle {
		height:200px;
		overflow:auto;
	}
	
	label a:link {color: blue;}
	label a:visited {color: red;}
	label a:hover {font-size:12; font-weight:bold; color: green;}
	
</style>
<div style="position: absolute; margin: 0; padding: 0; z-index: 1000; width: 100%; height: 100%;">
    <div id="nav" style="display:none"><?=$navigation;?></div>
    <!-- Start Content -->
    <div id="main">
	
	<center>
		<label class="button">Intelligent Transportation System &copy; www.lacak-mobil.com</label>
		All List Vehicles ( <?=count($data);?> )
		<br />
		| <label><a href="<?php echo base_url();?>trackers">Home</a></label> |
		<label><a href="<?php echo base_url();?>member/logout">Logout</a></label> |
		<hr />
	</center>
	<table width="100%" class="tablelist">
		<tr>
			<td width="70%" style="text-align:left;">
				<div id="result"></div>
			</td>
			<td width="30%" style="position:absolute;">
			<div class="block-border">
				<form class="block-content form" name="frmsearch" id="frmsearch" onsubmit="javascript:return frmsearch_onsubmit()">
					<fieldset class="grey-bg required">
						<div id="divsearch">
							<input type="hidden" name="offset" id="offset" value="" />
							<input type="hidden" id="sortby" name="sortby" value="<?=$sortby?>" />
							<input type="hidden" id="orderby" name="orderby" value="<?=$orderby?>" />
							<table width="100%" cellpadding="1" class="tablelist" style="font-size:11px;">
								<tr >
									<td>
										<strong>Change Map Layer</strong>
										<br />
										<div id="box_layerswitcher">
											<div id="layerswitcher"></div>
										</div>
									</td>
								</tr>
									<td id="tdsearch">
										<strong><?=$this->lang->line("lsearchby");?> :</strong>
										<select id="field" name="field" onchange="javascript:field_onchange()" style="font-size:11px;">
											<?php if ($this->sess->user_type != 2) { ?>
											<option value="user_login"><?=$this->lang->line("llogin");?></option>
											<option value="user_name"><?=$this->lang->line("lname");?></option>
											<option value="user_agent"><?=$this->lang->line("lagent");?></option>
											<option value="user_company"><?=$this->lang->line("lcompany");?></option>
											<?php } ?>							
											<option value="vehicle"><?=$this->lang->line("lvehicle");?></option>
											<?php if($this->sess->user_group == 0) { ?>
											<option value="location"><?php echo "Location";?></option>
											<option value="device"><?=$this->lang->line("ldevice_id");?></option>
											<option value="vehicle_card_no"><?=$this->lang->line("lcardno");?></option>
											<?php } ?>
											<option value="branch"><?php echo "Pool";?></option>
										</select>						
										<br />
										<input name="keyword" id="keyword" value="" class="formdefault" style="font-size:11px;"  />
										
										<select id="company" name="company" onchange="javascript: loadgroup1()" style="display: none;" style="font-size:11px;">
											<?php for($i=0; $i < count($companies); $i++) { ?>
											<option value="<?php echo $companies[$i]->company_id; ?>"><?php echo $companies[$i]->company_name; ?></option>
											<?php } ?>
										</select>
							
										<span id="usergroup1"></span>
							
										<select id="branch_office" name="branch_office" style="display: none;" style="font-size:11px;">	
											<?php
												if (isset($branch))
												{
													for ($z=0;$z<count($branch);$z++)
													{
											?>
													<option value="<?php echo $branch[$z]->company_id;?>"><?php echo $branch[$z]->company_name;?></option>
											<?php
													}
												}
											?>
										</select>
										<input class="button" type="submit" name="btnsearch" value="<?=$this->lang->line("lsearch");?>" style="font-size:11px;" />
										<img id="loader" src="<?=base_url();?>assets/images/ajax-loader.gif" border="0" alt="" title="" style="display:none;">
										
										<br /><br />
										<strong>List Vehicle :</strong>
										<br />
										<div id="listvehicle">
											<input type="checkbox" id="vehicleall" name="vehicleall" value="-1" onclick="javascript:checkall()" />Check All
											<br />
											<?php 
											if (isset($data))
											{
												for($i=0;$i<count($data);$i++)
												{
											?>
												<input type="checkbox" id="vehicle<?php echo $data[$i]->vehicle_id; ?>" name="vehicle[]" value="<?php echo $data[$i]->vehicle_id; ?>" />
												<?php echo $data[$i]->vehicle_name." ".$data[$i]->vehicle_no; ?>
												<br />
											<?
												}
											}
											?>
										</div>
										<input onclick="javascript:frmsearch_onsubmit();" class="button" type="button" name="btnshow" value="Show" style="font-size:11px;" />

					</fieldset>
				</form>
									</td>
								</tr>
								<tr><td>&nbsp;</td></tr>
								<tr>
									<td>
										
										<form class="block-content form" name="frmfollowme" id="frmfollowme" onsubmit="javascript:return frmfollowme_onsubmit()">
											<strong>Follow Me</strong>
											<select id="slfollow" name="slfollow" style="font-size:11px;">
												<option value="all">All</option>
												<?php 
												if (isset($data))
												{
													for($y=0;$y<count($data);$y++)
													{
												?>
													<option value="<?php echo $data[$y]->vehicle_id;?>">
													<?php echo $data[$y]->vehicle_name." ".$data[$y]->vehicle_no; ?>
													</option>
												<?
													}
												}
												?>
											</select>
											<input class="button" type="submit" name="btngo" value="GO" style="font-size:11px;" />
											<img id="loader2" src="<?=base_url();?>assets/images/ajax-loader.gif" border="0" alt="" title="" style="display:none;">
										</form>
										
										<label><a href="javascript:s_map(true)">Show Map</label></a> |
										<label><a href="javascript:s_map(false)">Show Table</label></a> |
										<label><a href="javascript:s_all()">Show All ( Map & Table )</label>
									</td>
								</tr>
							</table>
						</div>
				</div>
			</td>
			
		</tr>
	<table>	
</div>		
				
