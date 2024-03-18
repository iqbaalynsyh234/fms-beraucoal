<!-- BEGIN OF MAIN MENU --> 
<style>
	.ui-dialog .ui-dialog-titlebar { padding: .5em .3em .3em 1em; position: relative; font-size:12px; background:#def0fa; color:#0066ff; border:none}
	.olControlLayerSwitcher
	{
		font-size: 12px;
		width: 440px;
	}
.olLayerGoogleCopyright
{
  display: none;
}
.olLayerGooglePoweredBy{
	display:none;
}
#dvalert_geofence{
height: 90px;
width:250px;
-moz-border-radius-bottomright: 15px;
-moz-border-radius-bottomleft: 15px;
-moz-border-radius-topleft: 15px;
-moz-border-radius-topright: 15px;
border-bottom-right-radius: 15px;
border-bottom-left-radius: 15px;
border-top-right-radius: 15px;
border-top-left-radius: 15px;
}
</style>
<script>
var interval_notification = <?php echo $this->config->item("interval_notification");?>;	
var interval_geofence_alert = <?php echo $this->config->item("interval_geofence_alert");?>;	
var interval_get_geofence_alert = <?php echo $this->config->item("interval_get_geofence_alert");?>;	
var interval_warning_alarm = <?php echo $this->config->item("interval_warning_alarm");?>;	
setInterval("get_kumis_alert();", interval_warning_alarm);
//setInterval("alert_geofence();", interval_geofence_alert);
//setInterval("get_alert_geofence();", interval_get_geofence_alert);
function get_notification()
        {
            jQuery.post('<?=base_url()?>transporter/car_request/get_notification',{},
				function(r)
				{   
				    jQuery("#total_notification").html(" " + "(" + r.total + ")");
				}
				, "json"
			);
        }
		
<!-- Warning Alarm -->
		jQuery.post("<?=base_url()?>transporter/cron_alert/get_kumis_alert", {},
				function(data)
				{
					jQuery("#alert_notification").html(data.notification);
					g_notifready = true;
				}
				, "json"
			
			);

function get_kumis_alert()
    {
		jQuery.post("<?=base_url()?>transporter/cron_alert/get_kumis_alert", {},
				function(data)
				{
					jQuery("#alert_notification").html(data.notification);
					g_notifready = true;
				}
				, "json"
			
			);
	}
</script>	
<script>
	<!--
	<?php if ($this->uri->segment(1) == "alarm") { ?>
		var t_checkalarm = 1;
	<?php } else { ?>
		var t_checkalarm = 0;
<?php } ?>		
		var g_alarmready = true;
			
		jQuery(document).ready(
			function()
			{
				jQuery('#dialog').dialog(
					{ 
						 autoOpen: false 						
					}
				);
				
				jQuery('#dialogannouncement').dialog(
					{ 
						 autoOpen: false 						
					}
				);				
								
				var bg = jQuery("ul.topnav").css("background-color");
				var cl = jQuery("ul.topnav li a").css("color");
				
				jQuery(".ui-dialog .ui-dialog-titlebar").css("background-color", bg);
				jQuery(".ui-dialog .ui-dialog-titlebar").css("color", cl);
				
				<?php if (($this->sess->user_type != 4) && $loaddialog)  { ?>				
				//showinforequest();
				<?php if ($this->session->flashdata('showannounce')) { ?>
					//showalertmessage();
				<?php } ?>				
				<?php } ?>
				//getInvoiceTotal();
			}
		);
		
		function getInvoiceTotal()
		{
			jQuery.post('<?=base_url()?>invoice/getTotal', {},
				function(r)
				{

					jQuery("#dvpayments").html(r.html);
					if(r.total != 0)
					{
						jQuery("#dvpayments_info").html(r.html);
						jQuery("#tblinvoice_alert").show();
					}
				}
				, "json"
			);
		}

		function showalertmessage()
		{
			jQuery.post('<?=base_url()?>announcement/', {},
				function(r)
				{
					if (r.isempty)
					{
						return;
					}		
					
					showdialog(r.html, r.title, 500, 200, "#dialogannouncement");	
				}
				, "json"
			);
		}
		
		function showinforequest()
		{
			jQuery.post('<?=base_url()?>user/cekreqinfo', {},
				function(r)
				{
					if (r.iscomplete)
					{
						return;
					}
					
					inforequest();
				}
				, "json"
			);
		}
		
		function inforequest()
		{
			jQuery("#autoscroll").attr("checked", false);
			showdialog();
			jQuery.post('<?=base_url()?>user/reqinfo', {},
				function(r)
				{
					showdialog(r.html, r.title);
				}
				, "json"
			);			
		}
	
		function config()
		{
			showdialog();
			jQuery.post('<?=base_url()?>home/config/', {},
				function(r)
				{
					showdialog(r.html, r.title, 900, 500);
				}
				, "json"
			);			
		}
	
		function report(id)
		{

			showdialog();
			jQuery.post('<?=base_url()?>member/showvehicle/'+id, {},
				function(r)
				{
					showdialog(r.html, r.title);
				}
				, "json"
			);			
		}	
	
		function user(agent)
		{
			showdialog();
			jQuery.post('<?=base_url()?>member/showvehicle4agent', {agent: agent},
				function(r)
				{
					showdialog(r.html, r.title);
				}
				, "json"
			);			
		}	
		
		function vehicle(userid)
		{			
			showdialog();
			jQuery.post('<?=base_url()?>member/showvehicle4user', {userid: userid},
				function(r)
				{
					showdialog(r.html, r.title);
				}
				, "json"
			);						
		}	
		
		function changepass(userid)
		{
			showdialog();
			jQuery.post('<?=base_url()?>user/changepass/' + userid, {},
				function(r)
				{
					if (r.error)
					{
						alert("Retry");
						return;
					}
					
					showdialog(r.html, '<?=$this->lang->line("lchangepassword"); ?>');
				}
				, "json"
			);			
		}
		
		function showdialog(html, title, w, h, id, initclose)
		{
			if (! id) id = "#dialog";
			
			if (! initclose)
			{
				jQuery(id).dialog('close');
			}
			
			if (!html) html = "<?=$this->lang->line('lwait_loading_data');?>";
			if (!title) title = "<?=$this->lang->line('lwait_loading_data');?>";
			if (!w) w = 800;
			if (!h) h = 400;
			
			jQuery(id).dialog('option', 'width', w);
			jQuery(id).dialog('option', 'height', h);
			jQuery(id).dialog('option', 'modal', (id == "#dialog"));
			jQuery(id).html(html);
			jQuery(id).dialog('option', 'title', title);
			jQuery(id).dialog('open');			
		}
		
		function showclock()
		{
			jQuery.post("<?=base_url()?>member/clock", {},
				function(r)
				{
					jQuery("#clock").css("top", 10);
					jQuery("#clock").css("left", jQuery(document).width()-400);
					jQuery("#clock").html(r);						
				}
			);			
		}
		
		function runclock()
		{
			if (gtclock) clearTimeout(gtclock);
			
			if (t_checkalarm%<?php echo $this->config->item('alarmtimer'); ?> == 0)
			{
				t_checkalarm = 0;
				getAlarm();
			}
			t_checkalarm++;
			
			jQuery("#myclock").html(gclock.getDate() + " " + gmonths[gclock.getMonth()] + " " + gclock.getFullYear() + " " + lead2zero(gclock.getHours()) + ":" + lead2zero(gclock.getMinutes()) + ":" + lead2zero(gclock.getSeconds()));
			
			var s = gclock.getSeconds();
			gclock.setSeconds(s+1);
			
			gtclock = setTimeout("runclock()", 1000);	
		}
		
		function getAlarm()
		{
			if (! g_alarmready) return;
			
			g_alarmready = false;
			
			jQuery.post("<?=base_url()?>alarm/getcount", {},
				function(r)
				{
					jQuery("#dvgeofencealert").html(r.geofencelink);
					jQuery("#dvparkalert").html(r.parklink);
					jQuery("#dvspeedalert").html(r.speedlink);

					g_alarmready = true;
				}
				, "json"
			
			);
		}
			
		function lead2zero(n)
		{
			if (n < 10) return '0'+n;
			
			return n;
		}	

		//LALIN
		function lalin()
		{
			showdialog();
			jQuery.post('<?=base_url()?>lalin/info', {},
				function(r)
				{
					showdialog(r.html, 'Informasi Seputar Arus Lalu lintas');
				}
				, "json"
			);			
		}
		
		function alert_geofence()
		{
			var start = new Date().getTime();
			jQuery.post('<?=base_url()?>transporter/cron_alert/geo_alert_show', {},
				function(r)
				{   
					
					if(r.total != 0){
						var dir = r.data.alert_geo_direction;
						var status = "";
						if (dir==1)
						{
							status = "Masuk";
						}
						else
						{
							status = "Keluar";
						}
						jQuery("#message_geo_alert").html("Pada" + " " + r.data.alert_geo_time + "<br />" + r.vehicle + " " + status + " " + "Area:" + " " + r.geo_location);
						jQuery("#dvalert_geofence").show('bounce');
						setTimeout(function() {
						jQuery('#dvalert_geofence').fadeOut('fast');
						}, 10000); 
						
					}
                    else {
                        jQuery("#dvalert_geofence").hide();
                    }
				}
				, "json"
			);
		}
		
		function get_alert_geofence()
		{
			jQuery.post('<?=base_url()?>transporter/cron_alert/geo_alert', {},
				function(r)
				{   
				}
			);
		}
		
		function mnwrite(v)
        {
        switch (v)
        {
            case "inactive":
                jQuery("#write_inactive").hide();
                jQuery("#write_active").show();
                jQuery("#users_active").hide();
                jQuery("#users_inactive").show();
                jQuery("#config_active").hide();
                jQuery("#config_inactive").show();
                jQuery("#alert_active").hide();
                jQuery("#alert_inactive").show();
                jQuery("#download_active").hide();
                jQuery("#download_inactive").show();
                jQuery("#home_active").hide();
                jQuery("#home_inactive").show();
            break;    
            case "active":
                jQuery("#write_inactive").show();
                jQuery("#write_active").hide();
            break;
        }
        }
        
        function mnprofile(v)
        {
            switch(v)
            {
            case "inactive":
                jQuery("#users_active").show();
                jQuery("#write_active").hide();
                jQuery("#write_inactive").show();
                jQuery("#users_inactive").hide();
                jQuery("#config_active").hide();
                jQuery("#config_inactive").show();
                jQuery("#alert_active").hide();
                jQuery("#alert_inactive").show();
                jQuery("#download_active").hide();
                jQuery("#download_inactive").show();
            break;
            case "active":
                jQuery("#users_active").hide();
                jQuery("#users_inactive").show();
            break;
            }
        }
        
        function mnconfiguration(v)
        {
            switch(v)
            {
            case "inactive":
                jQuery("#config_active").show();
                jQuery("#config_inactive").hide();
                jQuery("#users_active").hide();
                jQuery("#write_active").hide();
                jQuery("#write_inactive").show();
                jQuery("#users_inactive").show();
                jQuery("#alert_active").hide();
                jQuery("#alert_inactive").show();
                jQuery("#download_active").hide();
                jQuery("#download_inactive").show();
            break;
            case "active":
                jQuery("#config_active").hide();
                jQuery("#config_inactive").show();
            break;
            }
        }
        
        function mnalert(v)
        {
            switch(v)
            {
            case "inactive":
                jQuery("#alert_active").show();
                jQuery("#alert_inactive").hide();
                jQuery("#config_active").hide();
                jQuery("#config_inactive").show();
                jQuery("#users_active").hide();
                jQuery("#write_active").hide();
                jQuery("#write_inactive").show();
                jQuery("#users_inactive").show();
                jQuery("#download_active").hide();
                jQuery("#download_inactive").show();
            break;
            case "active":
                jQuery("#alert_active").hide();
                jQuery("#alert_inactive").show();
            break;
            }
        }
        
        function mndownload(v)
        {
            switch(v)
            {
            case "inactive":
                jQuery("#download_active").show();
                jQuery("#download_inactive").hide();
                jQuery("#alert_active").hide();
                jQuery("#alert_inactive").show();
                jQuery("#config_active").hide();
                jQuery("#config_inactive").show();
                jQuery("#users_active").hide();
                jQuery("#users_inactive").show();
                jQuery("#write_active").hide();
                jQuery("#write_inactive").show();
            break;
            case "active":
                jQuery("#download_active").hide();
                jQuery("#download_inactive").show();
            break;
            }
        }
		
		var gmonths = new Array();
		var gtclock = null;
		var gclock = new Date();				
	-->
</script>
		<?php if (isset($globaljs)) echo $globaljs; ?>
		
		<!--new menu-->
		<!-- Server status -->
		<header>
			<div class="container_12">
				<p id="skin-name"><small>Intelligent Transportation System<br />Analyze The Performance Of Vehicles</small>
				&nbsp; 
				<strong>
					<!--<img src="<?php echo base_url();?>assets/newfarrasindo/images/iconfarrasindo.png" />-->
				</strong>
				</p>
				<div id="clock" class="server-info"></div>
				<div class="server-info">&copy; <strong>lacak-mobil.com</strong></div>
		
			</div>
		</header>
		<!-- End server status -->
		
		<nav id="main-nav">
		<a name="top"></a>
        <a name="atop"></a>
			<ul class="container_12">
			<?php if ($this->sess->user_type != 4)  { ?>
			    <li class="home">
					<a href="<?=base_url();?>trackers"><?=$this->lang->line("lhome"); ?></a>
				</li> 			    
			    
				<?php if ($this->sess->user_group == 0) { ?>
			    <li id="write_inactive" class="write">
					<a href="javascript: mnwrite('inactive')" title="Report"><?=$this->lang->line("lreport"); ?></a>
				</li>            
				<li id="write_active" class="write current" style="display: none;">
					<a href="javascript: mnwrite('active')" title="Report"><?=$this->lang->line("lreport"); ?></a>
			        <ul class="subnav"> 
			            <li><a href="#" onclick="javascript: report('overspeed')"><?=$this->lang->line("loverspeed"); ?></a></li> 
			            <li><a href="#" onclick="javascript: report('parkingtime')"><?=$this->lang->line("lparking_time"); ?></a></li> 
			            <li><a href="#" onclick="javascript: report('history')"><?=$this->lang->line("lhistory"); ?></a></li> 
			            <?php if ($totalGTP) { ?>
			            <li><a href="#" onclick="javascript: report('workhour')"><?=$this->lang->line("lworkhour"); ?></a></li> 
			            <!--<li><a href="#" onclick="javascript: report('engine')"><?=$this->lang->line("lengine_1"); ?></a></li>-->
						<?php if ($this->sess->user_type != 2 && $this->sess->user_agent != "1") { ?>
			            <li><a href="#" onclick="javascript: report('door')"><?=$this->lang->line("ldoor_status"); ?></a></li> 
						<?php } ?>
			            <!--<li><a href="#" onclick="javascript: report('alarm')"><?=$this->lang->line("lalarm"); ?></a></li>-->
			            <li><a href="#" onclick="javascript: report('odometer')"><?=$this->lang->line("lodometer"); ?></a></li>
			        	<?php } ?>			        	
			            <li><a href="#" onclick="javascript: report('geofence')"><?=$this->lang->line("lgeofence"); ?></a></li>
						<li><a href="<?=base_url();?>report"><?="Trip Mileage";?></a></li>
						<li><a href="<?=base_url();?>report/mn_history_trip_mileage"><?="History Trip Mileage";?></a></li>
						<li><a href="<?=base_url();?>report/mn_playback"><?="Playback Report";?></a></li>
						<li><a href="<?=base_url();?>report/mn_inout_geofence"><?="In Out Geofence Duration";?></a></li>
						<li><a href="<?=base_url();?>report/mn_driver_hist"><?="Driver History";?></a></li>
						<li><a href="<?=base_url();?>transporter/ritase/menu_ritase_report"><?="Ritase Report";?></a></li>
			        </ul> 
			    </li>
				
			    <li class="users" id="users_inactive">
					<a href="javascript:mnprofile('inactive')" title="Manage Profiles"><?=$this->lang->line("lmanage_user"); ?></a>
				</li>
				<li class="users current" id="users_active" style="display: none;">
				<a href="javascript:mnprofile('active')" title="Manage">
				<?php echo "Manage"; ?></a>
			        <ul class="subnav">
                        <li><a href="<?=base_url();?>user/add/<?=$this->sess->user_id;?>"><?=$this->lang->line("lprivate_info"); ?></a></li> 
                        <li><a href="<?=base_url();?>transporter/branchoffice"><?php echo "Branch Office" ?></a></li>
			            <li><a href="<?=base_url();?>transporter/user"><?=$this->lang->line("luser"); ?></a></li>
						<li><a href="<?=base_url();?>transporter/mod_vehicle_maintenance">Vehicle</a></li>
						<li><a href="<?php echo base_url();?>transporter/driver"><?php echo "Driver"; ?></a></li>
						<li><a href="<?php echo base_url();?>transporter/route"><?php echo "Route"; ?></a></li>
						<li><a href="<?php echo base_url();?>transporter/timesheet"><?php echo "Time Sheet"; ?></a></li>
                        <li><a href="<?=base_url();?>transporter/customer"><?php echo "Customer" ?></a></li>
						<li><a href="<?=base_url();?>transporter/ritase"><?php echo "Ritase" ?></a></li>
                        <?php if ((! isset($this->sess->user_manage_password)) || $this->sess->user_manage_password) { ?>
			            <li><a href="#" onclick="javascript:changepass(<?=$this->sess->user_id?>)"><?=$this->lang->line("lchangepassword"); ?></a></li> 
			            <?php } ?>
			            <?php  if (($this->sess->user_type == 1) || (($this->sess->user_type == 3) && ($this->sess->agent_canedit_vactive))) { ?>
                        <?php if ($ncompany) { ?>
                            <li><a href="<?=base_url();?>group"><?=$this->lang->line("lgroup"); ?></a></li> 
                        <?php } ?>
			            <li style="border-bottom: #cccccc solid 1px; height: 5px;">&nbsp;</li>
						
						<?php if ($this->sess->user_type == 4) { ?>
			            <li id='dvpayments'><a href="<?php printf("%sinvoice", base_url()); ?>"><?=$this->lang->line("linvoice"); ?></a></li>
			        	<?php } ?>
						
						<?php } ?>
			            <?php if (($this->config->item("contact_joomla") == 1) && ($this->sess->user_type != 2)) { ?>
			            <li><a href="<?=base_url();?>contactus"><?=$this->lang->line("lcontact_us"); ?></a></li> 
			        	<?php } ?>
			        </ul> 
			    </li>
			<!--
			<?php if ($this->sess->user_change_profile == 1) { ?>
			    <li> 
			        <a href="#"><?=$this->lang->line("laccount_info"); ?></a> 
			        <ul class="subnav">
			            <li><a href="<?=base_url();?>user/add/<?=$this->sess->user_id;?>"><?=$this->lang->line("lprivate_info"); ?></a></li> 
			            <?php if ((! isset($this->sess->user_manage_password)) || $this->sess->user_manage_password) { ?>
			            <li><a href="#" onclick="javascript:changepass(<?=$this->sess->user_id?>)"><?=$this->lang->line("lchangepassword"); ?></a></li> 
			            <?php } ?>
			        </ul> 
			    </li> 
			<?php } ?>
			-->
			
			    <li class="stats" id="config_inactive"><a href="javascript:mnconfiguration('inactive')" title="Configuration">
					<?=$this->lang->line("lconfiguration"); ?></a>
				</li>
				<li class="stats current" id="config_active" style="display: none;">
					<a href="javascript:mnconfiguration('active')" title="Configuration"><?=$this->lang->line("lconfiguration"); ?></a>
				<ul>
			        	<?php if ($this->sess->user_type != 2) { ?>
			            <li><a href="<?=base_url()?>poi/category"><?=$this->lang->line("lpoi_category"); ?></a></li> 
			            <?php } ?>
			            <li><a href="<?=base_url()?>poi/"><?=$this->lang->line("lpoi"); ?></a></li> 
			            <?php if ($this->sess->user_type != 2) { ?>
			            <li style="border-bottom: #cccccc solid 1px; height: 5px;">&nbsp;</li>
			        	<?php } ?>
			            <li><a href="<?=base_url()?>street/"><?=$this->lang->line("lstreet"); ?></a></li> 
			            <li><a href="#" onclick="javascript: report('mangeofence')"><?=$this->lang->line("lgeofence"); ?></a></li> 
			            <li style="border-bottom: #cccccc solid 1px; height: 5px;">&nbsp;</li>
			            <?php if ($this->sess->user_type == 1) { ?>			            
			            <li><a href="#" onclick="javascript: config()" ><?=$this->lang->line("lapplication"); ?></a></li> 
			        	<?php } ?>
			        	<li><a href="<?php echo base_url(); ?>announcement/show"><?=$this->lang->line("lannouncement"); ?></a></li> 
			        </ul>     	
			    </li> 
			   <?php } ?>	
				
				<!--
				<?php if (base_url() == "http://www.transporter.lacak-mobil.com/" || base_url() == "http://transporter.lacak-mobil.com/") { 
				      if ((($this->sess->user_type == 2) && (in_array($this->sess->user_agent, $this->config->item("INVOICE_AGENT")))) || ($this->sess->user_type == 4)) { ?>
				<li class="backup" id='dvpayments'><a href="<?php printf("%sinvoice", base_url()); ?>"><?=$this->lang->line("linvoice"); ?></a></li>					
				<?php } 
					} ?>
						<?php } ?>
				-->
				
				<?php if ($this->sess->user_group == 0) { ?>		
				<li class="backup" id="download_inactive"><a href="javascript:mndownload('inactive')" title="Download">
					<?=$this->lang->line("ldownload"); ?></a>
				</li>
				<li class="backup current" id="download_active" style="display: none;">
					<a href="javascript:mndownload('active')" title="Download"><?=$this->lang->line("ldownload"); ?></a>
					<ul>
						<li><a href="<?=base_url();?>download/tutorial"><?=$this->lang->line("ltutorial"); ?></a></li>
							<li><a href="<?=base_url();?>download/smsCommand"><?=$this->lang->line("lsms_command"); ?></a></li>
							<li><a href="<?=base_url();?>download/mn_download">Daily Report</a></li>
					</ul>
				</li>
				<?php } ?>
			
				<li class="settings"><a href="#" onclick="javascript: lalin()"><?=$this->lang->line("linfo_lain"); ?></a></li>
			    
			</ul> 
		</nav>
<!-- End main nav -->
 
 <!-- Sub nav -->
<div id="sub-nav">
	<div class="container_12">		
	
	</div>
</div>
<!-- End sub nav -->
	
<!-- END OF MAIN MENU --> 
 <!-- Status bar -->
	<div id="status-bar"><div class="container_12">
	
		<ul id="status-infos">
		<li class="spaced"><strong>Alert : </strong></li>
            <!-- Panic Alert -->
            <li class="spaced"></li>
			<li>
				<a class="button" title="Warning Alarm">
                <img src="<?php echo base_url();?>assets/newfarrasindo/images/icons/fugue/balloon.png" width="16" height="16" />
                <label id="alert_notification"></label>
				</a>
			</li>
            <li><a href="<?=base_url();?>member/logout" class="button red" title="Logout"><span class="smaller">LOGOUT</span></a></li>
		</ul>
        
        <ul id="breadcrumb">
			<li>Live Support</li>
				<a href="ymsgr:sendIM?nedi_lacakmobil" class="button" title="Nedi Lacakmobil">
				<img border="0" src="http://opi.yahoo.com/online?u=nedi_lacakmobil&amp;m=g&amp;t=1"/>
				</a>
				<a href="ymsgr:sendIM?bayu_lacakmobil" class="button" title="Bayu Lacakmobil">
				<img border="0" src="http://opi.yahoo.com/online?u=bayu_lacakmobil&amp;m=g&amp;t=1"/>
				</a>
				<a href="ymsgr:sendIM?robi_lacakmobil" class="button" title="Robi Lacakmobil">
				<img border="0" src="http://opi.yahoo.com/online?u=robi_lacakmobil&amp;m=g&amp;t=1"/>
				</a>
				<a href="ymsgr:sendIM?hadi_lacakmobil" class="button" title="Hadi Lacakmobil">
				<img border="0" src="http://opi.yahoo.com/online?u=hadi_lacakmobil&amp;m=g&amp;t=1"/>
				</a>
				<a href="ymsgr:sendIM?erlan_lacakmobil" class="button" title="Erlan Lacakmobil">
				<img border="0" src="http://opi.yahoo.com/online?u=erlan_lacakmobil&amp;m=g&amp;t=1"/>
				</a>
				<a href="ymsgr:sendIM?ika_lacakmobil" class="button" title="Ika Lacakmobil">
				<img border="0" src="http://opi.yahoo.com/online?u=ika_lacakmobil&amp;m=g&amp;t=1"/>
				</a>
		</ul>
		<ul id="breadcrumb">
			<li>Call Support</li>
			<a href="#" class="button" title="Monitoring Hotline">
       	       <b>08558208484</b>
			</a>
			<a href="#" class="button" title="Monitoring Hotline">
             		<b>021-82434946</b>
			</a>
		</ul>
        
	</div></div>
	<!-- End status bar -->
	
	<div id="header-shadow"></div>
	<!-- End header -->

<div id="dialog" style='font-size: 12px; font-face: Tahoma;'></div>
<div id="dialogannouncement" style='font-size: 12px; font-face: Tahoma;'></div>

<div id="dvalert_geofence" style="top:0px; right:0; position: absolute; display: none;">
<div class="block-border">
<table style="font-size:12px;color:white;">
<tr>
	<td>
		<img src="<?php echo base_url();?>assets/transporter/images/info.png" width="24px" height="24px" />
		<b><font size="4px">Geofence Alert !</font></b>
	</td>
</tr>
<tr><td><span id="message_geo_alert"></span></td></tr>
</table>
</div>
</div>

