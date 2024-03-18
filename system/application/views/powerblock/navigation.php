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

//setInterval("get_notification();", interval_notification);
//setInterval("alert_geofence();", interval_geofence_alert);
//setInterval("get_alert_geofence();", interval_get_geofence_alert);

//setInterval("get_service_alert();", 20000);
//setInterval("get_service_alert_show();", 10000);
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

function get_service_alert()
{
	 jQuery.post('<?=base_url()?>transporter/mod_vehicle_maintenance/get_service_alert',{},
				function(r)
				{
				}
				, "json"
			);
}

function get_service_alert_show()
{
	jQuery.post('<?=base_url()?>transporter/mod_vehicle_maintenance/get_service_alert_show',{},
				function(r)
				{
					jQuery("#dvservicealert").html("<font color="+r.color+">"+r.total+"</font>");
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

				get_service_alert();
				get_service_alert_show();
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
			            <!--<li><a href="#" onclick="javascript: report('workhour')"><?=$this->lang->line("lworkhour"); ?></a></li> -->
			            <!--<li><a href="#" onclick="javascript: report('engine')"><?=$this->lang->line("lengine_1"); ?></a></li>-->
						<?php if ($this->sess->user_type != 2 && $this->sess->user_agent != "1") { ?>
			            <li><a href="#" onclick="javascript: report('door')"><?=$this->lang->line("ldoor_status"); ?></a></li>
						<?php } ?>
			            <!--<li><a href="#" onclick="javascript: report('alarm')"><?=$this->lang->line("lalarm"); ?></a></li>-->
			            <!--<li><a href="#" onclick="javascript: report('odometer')"><?=$this->lang->line("lodometer"); ?></a></li>-->
			        	<?php } ?>
			            <li><a href="#" onclick="javascript: report('geofence')"><?=$this->lang->line("lgeofence"); ?></a></li>
						<li><a href="<?=base_url();?>report"><?="Trip Mileage Report";?></a></li>
						<li><a href="<?=base_url();?>report/mn_playback"><?="Playback Report";?></a></li>
						<li><a href="<?=base_url();?>report/mn_inout_geofence"><?="In Out Geofence Duration";?></a></li>
						<!--<li><a href="<?=base_url();?>report/mn_driver_hist"><?="Driver History";?></a></li>-->
						<li><a href="<?=base_url();?>pbi_report/mn_dataoperational"><?="Operational Report";?></a></li>
						<!-- Khusus APP DO/SJ -->
						<li><a href="<?=base_url();?>maxreport/mn_driver_hist"><?="Driver History";?></a>
						<!--<li><a href="<?=base_url();?>transporter/dosj/mn_driver_hist_dosj"><?="Driver History";?></a>-->
						<!-- **************** -->
						<!--<li><a href="<?=base_url();?>transporter/ritase/menu_ritase_report"><?="Ritase Report";?></a></li>-->
						<li><a href="<?=base_url();?>transporter/newritase/powerblock_ritase"><?="Ritase Report";?></a></li>
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
						<!--<li><a href="<?=base_url();?>transporter/dosj">SO</a></li>
						<li><a href="<?=base_url();?>transporter/dosj/cost">Cost Management</a></li>-->
						<li><a href="<?=base_url();?>transporter/mod_vehicle_maintenance">Vehicle</a></li>
						<li><a href="<?php echo base_url();?>transporter/driver"><?php echo "Driver"; ?></a></li>
                        			<li><a href="<?=base_url();?>transporter/customer"><?php echo "Data Customer" ?></a></li>
						<li><a href="<?=base_url();?>transporter/ritase"><?php echo "Ritase" ?></a></li>
						  <li>
							<a href="<?=base_url();?>transporter/maintenancemanagement">
							  <?="Maintenance Management";?>
							</a>
						  </li>
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
			            <li><a href="<?=base_url()?>geofence_label/"><?php echo "Geofence Label"; ?></a></li>
			            <?php if ($this->sess->user_type == 1) { ?>
			            <li><a href="#" onclick="javascript: config()" ><?=$this->lang->line("lapplication"); ?></a></li>
			        	<?php } ?>
			        	<!--<li><a href="<?php echo base_url(); ?>announcement/show"><?=$this->lang->line("lannouncement"); ?></a></li>-->
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

            <!-- Geofence Alert -->
			<!--
            <li class="spaced"></li>
			<li>
				<a href="" class="button" title="Geofence Alert">
                <img src="<?php echo base_url();?>assets/newfarrasindo/images/icons/fugue/balloon.png" width="16" height="16" />
                <label id="dvgeofencealert"></label></a>

			</li>
			-->
            <!-- end Geofence alert -->

            <!-- Parking Alert -->
			<!--
			<li>
				<a href="" class="button" title="Parking Alert">
                <img src="<?php echo base_url();?>assets/newfarrasindo/images/icons/fugue/balloon.png" width="16" height="16" />
                <label id="dvparkalert"></label></a></a>

			</li>
			-->
            <!-- end park alert-->

            <!-- Speed Alert -->
			<!--
			<li>
				<a href="" class="button" title="Parking Alert">
                <img src="<?php echo base_url();?>assets/newfarrasindo/images/icons/fugue/balloon.png" width="16" height="16" />
                <label id="dvspeedalert"></label></a></a>

			</li>
			-->
            <!-- end Speed alert-->

			<!-- Service Alert -->
			<li>
				<a href="<?php echo base_url();?>transporter/mod_vehicle_maintenance/showalert_service" class="button" title="Service Alert">
                <img src="<?php echo base_url();?>assets/newfarrasindo/images/icons/fugue/balloon.png" width="16" height="16" />
                <label id="dvservicealert"></label></a></a>

			</li>
            <!-- end Speed alert-->

			<li><a href="<?=base_url();?>member/logout" class="button red" title="Logout"><span class="smaller">LOGOUT</span></a></li>
		</ul>
		<style media="screen">
        .notification {
          background-color: #555;
          color: white;
          text-decoration: none;
          padding: 1px 4px;
          position: relative;
          display: inline-block;
          border-radius: 2px;
        }

        .notification:hover {
          background: red;
        }

        .notification .badge {
          position: absolute;
          top: -10px;
          right: -20px;
          padding: 4px 6px;
          border-radius: 50%;
          background: red;
          color: white;
        }

        div#ultooltip {
          background: whitesmoke;
          width: 380px;
          max-height: 200px;
          border: none;
          bordler-radius: 4px;
          margin-left: 30%;
          display: none;
          z-index: 1000;
          overflow-x: auto;
        }
      </style>

      <script type="text/javascript">
        function btnNotif() {
          jQuery("#ultooltip").toggle('slow');
        }

        // FOR GET THE NOTIFICATION
        window.onload = function(){
         // function fornotif() {
          var url = '<?php echo base_url()?>transporter/maintenancemanagement/getfornotif';
          jQuery.post(url, {}, function(response) {
            // FOR KIR
            var totalkirexpdate = response.total_kirexpdate;
            if (totalkirexpdate > 0) {
              jQuery("#tablekir").show();
              for (var i = 0; i < totalkirexpdate; i++) {
                var stnk = '<tr>';
                stnk += '<td>' + (i+1) + '. </td>';
                stnk += '<td> ' +response.data_notifkir[i].vehicle_no + ' <br> '+response.data_notifkir[i].vehicle_name+'</td>';
                stnk += '<td> ' +response.data_notifkir[i].vehicle_kirexpdate + '</td>';
                stnk += '</tr>';
                jQuery("#kirexpdate").before(stnk);
              }
            }else {
              jQuery("#tablekir").hide();
                  var stnk = '<tr>';
                  stnk += '<td>Data Not Available </td>';
                  stnk += '<td>Data Not Available </td>';
                  stnk += '<td>Data Not Available </td>';
                  stnk += '</tr>';
                  jQuery("#kirexpdate").before(stnk);
            }
            // FOR STNK
            var totalstnkexp = response.total_stnkexpdate;
            if (totalstnkexp > 0) {
              jQuery("#tablestnk").show();
              for (var i = 0; i < totalstnkexp; i++) {
                var stnk = '<tr>';
                stnk += '<td>' + (i+1) + '. </td>';
                stnk += '<td> ' +response.data_notifstnk[i].vehicle_no + ' <br> '+response.data_notifstnk[i].vehicle_name+'</td>';
                stnk += '<td> ' +response.data_notifstnk[i].vehicle_stnkexpdate + '</td>';
                stnk += '</tr>';
                jQuery("#stnkexpdate").before(stnk);
              }
            }else {
              jQuery("#tablestnk").hide();
                  var stnk = '<tr>';
                  stnk += '<td>Data Not Available </td>';
                  stnk += '<td>Data Not Available </td>';
                  stnk += '<td>Data Not Available </td>';
                  stnk += '</tr>';
                  jQuery("#stnkexpdate").before(stnk);
            }

            // FOR SERVICE PERKM
            var total_notifserviceperkm = response.total_notifserviceperkm;
            if (total_notifserviceperkm > 0) {
              jQuery("#tableserviceperkm").show();
              for (var i = 0; i < total_notifserviceperkm; i++) {
                var serviceperkm = '<tr>';
                serviceperkm += '<td>' + (i+1) + '. </td>';
                serviceperkm += '<td> ' +response.data_notifserviceperkm[i].vehicle_no + ' <br> '+response.data_notifserviceperkm[i].vehicle_name+'</td>';
                serviceperkm += '<td> ' +response.data_notifserviceperkm[i].lastodometerfromgps + '</td>';
                serviceperkm += '<td> ' +response.data_notifserviceperkm[i].odometerforservice + '</td>';
                serviceperkm += '</tr>';
                jQuery("#serviceperkm").before(serviceperkm);
              }
            }else {
              jQuery("#tableserviceperkm").hide();
                  var serviceperkm = '<tr>';
                  serviceperkm += '<td>Data Not Available </td>';
                  serviceperkm += '<td>Data Not Available </td>';
                  serviceperkm += '<td>Data Not Available </td>';
                  serviceperkm += '</tr>';
                  jQuery("#serviceperkm").before(serviceperkm);
            }

            // FOR SERVICE PERMONTH
            var total_notifservicepermonth = response.total_notifservicepermonth;
            if (total_notifservicepermonth > 0) {
              jQuery("#tableservicepermonth").show();
              for (var i = 0; i < total_notifservicepermonth; i++) {
                var servicepermonth = '<tr>';
                servicepermonth += '<td>' + (i+1) + '. </td>';
                servicepermonth += '<td> ' +response.data_notifservicepermonth[i].vehicle_no + ' <br> '+response.data_notifservicepermonth[i].vehicle_name+'</td>';
                servicepermonth += '<td> ' +response.data_notifservicepermonth[i].last_service + '</td>';
                servicepermonth += '<td> ' +response.data_notifservicepermonth[i].next_service + '</td>';
                servicepermonth += '</tr>';
                jQuery("#servicepermonth").before(servicepermonth);
              }
            }else {
              jQuery("#tableservicepermonth").hide();
                  var servicepermonth = '<tr>';
                  servicepermonth += '<td>Data Not Available </td>';
                  servicepermonth += '<td>Data Not Available </td>';
                  servicepermonth += '<td>Data Not Available </td>';
                  servicepermonth += '</tr>';
                  jQuery("#servicepermonth").before(servicepermonth);
            }

						// FOR OOG PBI
            var total_oogpbi = response.total_oogpbi;
            if (total_oogpbi > 0) {
              jQuery("#tablenotifoog").show();
							jQuery("#total_notifoog").html(total_oogpbi);
              // for (var i = 0; i < total_oogpbi; i++) {
              //   var notifoog = '<tr>';
              //   notifoog += '<td>' + (i+1) + '. </td>';
              //   notifoog += '<td> ' +response.data_oogpbi[i].transporter_alert_vehicleno + ' <br> '+response.data_oogpbi[i].transporter_alert_vehiclename+'</td>';
              //   notifoog += '<td> ' +response.data_oogpbi[i].transporter_alert_gpstime + '</td>';
              //   notifoog += '</tr>';
              //   jQuery("#oogforpbi").before(notifoog);
              // }
            }else {
              jQuery("#tablenotifoog").show();
                  // var notifoog = '<tr>';
                  // notifoog += '<td>Data Not Available </td>';
                  // notifoog += '<td>Data Not Available </td>';
                  // notifoog += '</tr>';
									jQuery("#total_notifoog").html(total_oogpbi);
									jQuery("#total_notifoog").html("0");
                  // jQuery("#oogforpbi").before(notifoog);
            }

            var totalallnotif = totalstnkexp + totalkirexpdate + total_notifserviceperkm + total_notifservicepermonth + total_oogpbi;
              if (totalallnotif == 0) {
                jQuery("#total_notif").hide();
              }else {
                jQuery("#total_notif").html(totalallnotif);
              }
          }, 'json');
        };
      </script>

		<ul id="breadcrumb">
			<li>WhatsApp</li>
			<a class="button" target="_blank" href="https://web.whatsapp.com/send?phone=<?=$this->config->item('wa_monitoring1');?>&amp;text=<?=$this->config->item('wa_hallo');?>">
				<img src="<?=base_url()?>assets/images/walogo.png" width="16px" height="16px" /><b>MONITORING 1</b>
			</a>
			<a class="button" target="_blank" href="https://web.whatsapp.com/send?phone=<?=$this->config->item('wa_monitoring2');?>&amp;text=<?=$this->config->item('wa_hallo');?>">
				<img src="<?=base_url()?>assets/images/walogo.png" width="16px" height="16px" /><b>MONITORING 2</b>
			</a>
			<a class="button" target="_blank" href="https://web.whatsapp.com/send?phone=<?=$this->config->item('wa_monitoring3');?>&amp;text=<?=$this->config->item('wa_hallo');?>">
				<img src="<?=base_url()?>assets/images/walogo.png" width="16px" height="16px" /><b>MONITORING 3</b>
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

		<ul id="breadcrumb">
        <li>Notification</li>
        <button class="button notification" title="Your Notification" id="clicknotif" onclick="btnNotif()">
          <img src="<?=base_url();?>assets/images/addvehicle.png" width="30px" height="15px" border="0">
          <span class="badge" id="total_notif"></span>
        </button>
      </ul>

	</div></div>
	<!-- End status bar -->








  <!-- <button type="button" onclick="fornotif()">TEKAN INI</button> -->

  <!-- End status bar -->
  <div id="header-shadow">
    <div id="ultooltip">

			<div id="tablenotifoog">
        <a href="<?php echo base_url()?>transporter/maintenancemanagement/oogreport">
          <button type="button">Out Of Geofence > 1.5 Jam = <p id="total_notifoog"></p> (Trail)</button>
        </a>
        <!-- <thead>
            <th>No</th>
            <th>Vehicle</th>
            <th>GPS Time</th>
        </thead>
        <tbody id="oogforpbi">

        </tbody> -->
    </div>

      <div id="tableserviceperkm">
      <table width="100%" cellspacing="1px" cellpadding="3px" border="solid 1px black" class="table">
        <a href="<?php echo base_url()?>transporter/maintenancemanagement">
          <button type="button">SERVICE/ KM</button>
        </a>
        <thead>
            <th>No</th>
            <th>Vehicle</th>
            <th>Actual Odometer</th>
            <th>Odometer For Service</th>
        </thead>
        <tbody id="serviceperkm">

        </tbody>
      </table>
    </div>

      <div id="tableservicepermonth">
        <table width="100%" cellspacing="1px" cellpadding="3px" border="solid 1px black" class="table">
          <a href="<?php echo base_url()?>transporter/maintenancemanagement">
            <button type="button">SERVICE / MONTH</button>
          </a>
          <thead>
              <th>No</th>
              <th>Vehicle</th>
              <th>Last Service</th>
              <th>Next Service</th>
          </thead>
          <tbody id="servicepermonth">
          </tbody>
        </table>
      </div>




      <div id="tablekir">
        <table width="100%" cellspacing="1px" cellpadding="3px" border="solid 1px black" class="table">
          <a href="<?php echo base_url()?>transporter/maintenancemanagement">
            <button type="button">KIR</button>
          </a>
          <thead>
              <th>No</th>
              <th>Vehicle</th>
              <th>Exp. Date</th>
          </thead>
          <tbody id="kirexpdate">

          </tbody>
        </table>
      </div>

      <div id="tablestnk">
        <table width="100%" cellspacing="1px" cellpadding="3px" border="solid 1px black" class="table">
          <a href="<?php echo base_url()?>transporter/maintenancemanagement">
            <button type="button">STNK</button>
          </a>
          <thead>
              <th>No</th>
              <th>Vehicle</th>
              <th>Exp. Date</th>
          </thead>
          <tbody id="stnkexpdate">

          </tbody>
        </table>
      </div>
    </div>
  </div>
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
