<!-- BEGIN OF MAIN MENU -->
<style>
	.ui-dialog .ui-dialog-titlebar { padding: .5em .3em .3em 1em; position: relative; font-size:12px; background:#def0fa; color:#0066ff; border:none}
</style>
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

				var bg = jQuery("ul.topnav").css("background-color");
				var cl = jQuery("ul.topnav li a").css("color");

				jQuery(".ui-dialog .ui-dialog-titlebar").css("background-color", bg);
				jQuery(".ui-dialog .ui-dialog-titlebar").css("color", cl);

				//showinforequest();
			}
		);

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
			showdialog();
			jQuery.post('<?=base_url()?>user/reqinfo', {},
				function(r)
				{
					showdialog(r.html, r.title);
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
					jQuery("#clock").css("left", jQuery(document).width()-650);
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
					jQuery("#dvalarm").html(r.html);
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

		var gmonths = new Array();
		var gtclock = null;
		var gclock = new Date();
	-->
</script>
			<ul class="topnav">
			    <li><a href="<?=base_url();?>trackers"><?=$this->lang->line("lhome"); ?></a></li>
			    <li>
			        <a href="#"><?=$this->lang->line("lvehicle"); ?></a>
			        <ul class="subnav">
			        		<?php
			        		if ($this->sess->user_type == 1)
			        		{
								for($i=0; $i < count($agents); $i++)
								{
							?>
									<li><a href="#" onclick="javascript:user(<?=$agents[$i]->agent_id?>)"><?=$agents[$i]->agent_name?></a></li>
							<?php
								}
			        		}
			        		else
			        		if ($this->sess->user_type == 2)
			        		{
								for($i=0; $i < count($vehicles); $i++)
								{
							?>
								<li><a href="<?=base_url()?>map/realtime/<?=$vehicles[$i]->vehicle_device_name?>/<?=$vehicles[$i]->vehicle_device_host?>"><?=$vehicles[$i]->vehicle_no?> - <?=$vehicles[$i]->vehicle_name?></a></li>
							<?php
								}

								if ($total > count($vehicles))
								{
							?>
								<li><a href="#" onclick="javascript:vehicle(<?=$this->sess->user_id?>)"><?=$this->lang->line('lother_vehicle')?></a></li>
							<?php
								}
			        		}
			        		else
			        		{
								for($i=0; $i < count($users); $i++)
								{
							?>
								<li onclick="javascript:vehicle(<?=$users[$i]->user_id?>)"><a href="#"><?=$users[$i]->user_name?></a></li>
							<?php
								}

								if ($total > count($users))
								{
							?>
								<li><a href="#" onclick="javascript:user(<?=$this->sess->user_agent?>)"><?=$this->lang->line('lother_users')?></a></li>
							<?php

								}
			        		}
			        		?>
			        </ul>
			    </li>
			    <li>
			        <a href="#"><?=$this->lang->line("lreport"); ?></a>
			        <ul class="subnav">
			            <li><a href="#" onclick="javascript: report('overspeed')"><?=$this->lang->line("loverspeed"); ?></a></li>
			            <li><a href="#" onclick="javascript: report('parkingtime')"><?=$this->lang->line("lparking_time"); ?></a></li>
			            <li><a href="#" onclick="javascript: report('history')"><?=$this->lang->line("lhistory"); ?></a></li>
			            <?php if ($totalGTP) { ?>
			            <li><a href="#" onclick="javascript: report('workhour')"><?=$this->lang->line("lworkhour"); ?></a></li>
			            <li><a href="#" onclick="javascript: report('engine')"><?=$this->lang->line("lengine_1"); ?></a></li>
			        	<?php } ?>
			            <li><a href="#" onclick="javascript: report('geofence')"><?=$this->lang->line("lgeofence"); ?></a></li>
			        </ul>
			    </li>
			    <?php if ($this->sess->user_type != 2) { ?>
			    <li>
			        <a href="#"><?=$this->lang->line("lmanage_user"); ?></a>
			        <ul class="subnav">
			        	<?php if ($this->sess->user_type == 1) { ?>
			            <li><a href="<?=base_url();?>agent"><?=$this->lang->line("lagent"); ?></a></li>
			            <?php } ?>
			            <li><a href="<?=base_url();?>user"><?=$this->lang->line("luser"); ?></a></li>
			        </ul>
			    </li>
			<?php } else { ?>
			    <li>
			        <a href="#"><?=$this->lang->line("laccount_info"); ?></a>
			        <ul class="subnav">
			            <li><a href="<?=base_url();?>user/add/<?=$this->sess->user_id;?>"><?=$this->lang->line("lprivate_info"); ?></a></li>
			            <li><a href="#" onclick="javascript:changepass(<?=$this->sess->user_id?>)"><?=$this->lang->line("lchangepassword"); ?></a></li>
			        </ul>
			    </li>
			<?php } ?>
			    <li>
			        <a href="#"><?=$this->lang->line("lconfiguration"); ?></a>
			        <?php //if (($this->sess->user_type != 2) || ($this->sess->user_login != "demo")) { ?>
			        <ul class="subnav">
			        	<?php if ($this->sess->user_type != 2) { ?>
			            <li><a href="<?=base_url()?>poi/category"><?=$this->lang->line("lpoi_category"); ?></a></li>
			            <?php } ?>
			            <li><a href="<?=base_url()?>poi/"><?=$this->lang->line("lpoi"); ?></a></li>
			            <li><a href="<?=base_url()?>street/"><?=$this->lang->line("lstreet"); ?></a></li>
			            <li><a href="#" onclick="javascript: report('mangeofence')"><?=$this->lang->line("lgeofence"); ?></a></li>
			        </ul>
			    </li>
			    <li id='dvalarm'><a href="<?=base_url();?>alarm"><?=$this->lang->line("lalarm_alert"); ?></a></li>
			    <li><a href="<?=base_url();?>member/logout"><?=$this->lang->line("llogout"); ?></a></li>

			</ul>
<!-- END OF MAIN MENU -->
<div id="dialog" style='font-size: 12px; font-face: Tahoma;'></div>
<div id="clock" style="position: absolute;"></div>