<script type="text/javascript" src="js/script.js"></script>
<script src="<?php echo base_url()?>assets/dashboard/assets/js/jquery-1.7.1.min.js" type="text/javascript"></script>
	<link rel="stylesheet" type="text/css" href="<?=base_url();?>assets/css/maps.css" />
	<script type="text/javascript" src="<?=base_url();?>assets/js/openlayers/OpenLayers.js"></script> 
	<script type="text/javascript" src="<?=base_url();?>assets/js/openlayers/lib/OpenLayers/Layer/OpenStreetMap.js"></script>
	
	<?php
	$key = $this->config->item("GOOGLE_MAP_API_KEY");
	if(isset($key) && $key != "") { ?>
		<script async defer src="https://maps.googleapis.com/maps/api/js?key=<?php echo $key;?>&callback=initMap" type="text/javascript"></script>
	<?php } else { ?>
		<script src="http://maps.google.com/maps/api/js?V=3.3&amp;sensor=false"></script> 
	<? } ?>
	
 <script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-20131355-1']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>

<script>
			
			
	jQuery(document).ready(
		function()
		{
			//showclock();
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
		
		jQuery.post("<?=base_url();?>dashboard/maparea_page/"+<?=$companydata->company_id;?>, jQuery("#frmsearch").serialize(),
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
			redirect("<?php echo base_url();?>dashboard/maparea");
		}
		else
		{
			jQuery("#layerswitcher").html("");
			jQuery.post("<?=base_url();?>dashboard/followme/", jQuery("#frmfollowme").serialize(),
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
				jQuery("#map").css({"top":"1%"});					
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
		margin-top: 400px;
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

				
    <style> 
        .olControlPanZoomBar 
        {
            margin-top: 50px;
        }
 
        .maximizeDiv 
        {
            margin-top: 20px;
        }
        
        #pup 
        {
		    font-size: 5px;
		    font-weight: bold;
		    width:30%;
		    height:20%;
		    color:black;
		    text-align:center;
		    background-repeat:no-repeat;
		    background-image: url('<?=base_url();?>assets/images/pup.png');
		}

		
    </style> 
<?php

$totaldata = $this->dashboardmodel->gettotalengine($companydata->company_id);
$totalengine = explode("|", $totaldata);
							
?>
<!-- start sidebar menu -->
 			<div class="sidebar-container">
 				<?=$sidebar;?>
            </div>
			 <!-- end sidebar menu -->

<!-- start page content -->
            <div class="page-content-wrapper">
                <div class="page-content">
                    <div class="page-bar">
                        <div class="page-title-breadcrumb">
                            <div class=" pull-left">
                                <div class="page-title"><?=$companydata->company_name;?></div>
                            </div>
                            <ol class="breadcrumb page-breadcrumb pull-right">
                                <li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="<?=base_url();?>dashboard">Dashboard</a>&nbsp;<i class="fa fa-angle-right"></i>
                                </li>
                                <li>Area &nbsp;<i class="fa fa-angle-right"></i>
                                </li>
                                <li class="active"><a class="parent-item" href=""><?=$companydata->company_name;?></a></li>
                            </ol>
                        </div>
                    </div>
                    <div class="row">
						<div class="col-md-12 col-sm-12">
							<div class="panel">
								<header class="panel-heading panel-heading-blue">Total Vehicle (<?=count($data);?>)</header>
									
										<div class="row">	
							
											<div class="col-md-12 col-sm-12">
												
											
							
													<table width="100%">
														<td width="80%">
															<div id="result"></div>
														</td>
														
														<td width="20%"> 
															<form name="frmsearch" id="frmsearch" onsubmit="javascript:return frmsearch_onsubmit()">
																
																	<div id="divsearch">
																		<input type="hidden" name="offset" id="offset" value="" />
																		<input type="hidden" id="sortby" name="sortby" value="<?=$sortby?>" />
																		<input type="hidden" id="orderby" name="orderby" value="<?=$orderby?>" />
																		<table width="100%" cellpadding="1" class="tablelist" style="font-size:11px;">
																			<tr>
																				<td>
																					<!--<strong>Change Map Layer</strong>
																					<br />-->
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
																							<?php echo $data[$i]->vehicle_no." ".$data[$i]->vehicle_name; ?>
																							<br />
																						<?
																							}
																						}
																						?>
																					</div>
																					<input onclick="javascript:frmsearch_onsubmit();" class="button" type="button" name="btnshow" value="Show" style="font-size:11px;" />
																					

																</fieldset>
															
																				</td>
															</form>			
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
																								<?php echo $data[$y]->vehicle_no." ".$data[$y]->vehicle_name; ?>
																								</option>
																							<?
																								}
																							}
																							?>
																						</select>
																						<input class="button" type="submit" name="btngo" value="GO" style="font-size:11px;" />
																						<img id="loader2" src="<?=base_url();?>assets/images/ajax-loader.gif" border="0" alt="" title="" style="display:none;">
																					</form>
																					
																					<a href="javascript:s_map(true)" type="button" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect m-b-10 btn-circle btn-success">View Map</a>
																					<a href="javascript:s_map(false)" type="button" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect m-b-10 btn-circle btn-danger">View Table</a>
																					<!--<label><a href="javascript:s_all()">Show All ( Map & Table )</label>-->
																				</td>
																			</tr>
																		</table>
																	</div>
														</td>
													</table>
													
											</div>	
													
											
										</div>
									
							</div>
						</div>
					
					
					</div>
				</div>
			</div>
            <!-- end page content -->