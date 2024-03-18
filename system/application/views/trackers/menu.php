			      		<?php
			      			$dev = str_replace("@", "/", $vehicle->vehicle_device);
			      		?>

			      		<?php if ($pid != "overspeed") { ?>
			      			<a href="<?php echo base_url()."trackers/overspeed/".$dev; ?>" class="button"><?php echo $this->lang->line("loverspeed_report"); ?></a>
			      		<?php } else { ?>
							<button type="button" ><?php echo $this->lang->line("loverspeed_report"); ?></button>
						<?php } ?>

			      		<?php if ($pid != "parkingtime") { ?>
			      			<a href="<?php echo base_url()."trackers/parkingtime/".$dev; ?>" class="button"><?php echo $this->lang->line("lparking_time_report"); ?></a>
			      		<?php } else { ?>
							<button type="button"><?php echo $this->lang->line("lparking_time_report"); ?></button>
			      		<?php } ?>

			      		<?php if ($pid != "history") { ?>
			      			<a href="<?php echo base_url()."trackers/history/".$dev; ?>" class="button"><?php echo $this->lang->line("lhistory_report"); ?></a>
			      		<?php } else { ?>
							<button type="button"><?php echo $this->lang->line("lhistory_report"); ?></button>
			      		<?php } ?>

			      		<?php
			      			$gtps = $this->config->item('vehicle_gtp');
			      			$gtpdoors = $this->config->item('vehicle_gtp_door');
			      			if (in_array(strtoupper($vehicle->vehicle_type), $gtps))
			      			{
						?>
			      				<?php if ($pid != "workhour") { ?>
			      				<a href="<?php echo base_url()."trackers/workhour/".$dev; ?>" class="button"><?php echo $this->lang->line("lworkhour_report"); ?></a>
			      				<?php } else { ?>
			      				<button type="button"><?php echo $this->lang->line("lworkhour_report"); ?></button>
			      				<?php } ?>
			      				<!--
			      				<?php if ($pid != "engine") { ?>
			      				<a href="<?php echo base_url()."trackers/engine/".$dev; ?>" class="button"><?php echo $this->lang->line("lengine_report"); ?></a>
			      				<?php } else { ?>
			      				<button type="button"><?php echo $this->lang->line("lengine_report"); ?></button>
			      				<?php } ?>
			      				-->

			      			<?php
							if ($this->sess->user_type != 2 && $this->sess->user_agent != "1") {
							if (in_array(strtoupper($vehicle->vehicle_type), $gtpdoors)) { ?>
			      				<?php if ($pid != "door") { ?>
			      				<a href="<?php echo base_url()."trackers/door/".$dev; ?>" class="button"><?php echo $this->lang->line("ldoor_status_report"); ?></a>
			      				<?php } else { ?>
			      				<button type="button"><?php echo $this->lang->line("ldoor_status_report"); ?></button>
			      				<?php } ?>
							<?php } } ?>

							<!--
			      				<?php if ($pid != "alarm") { ?>
			      				<a href="<?php echo base_url()."trackers/alarm/".$dev; ?>" class="button"><?php echo $this->lang->line("lalarm_report"); ?></a>
			      				<?php } else { ?>
			      				<button type="button"><?php echo $this->lang->line("lalarm_report"); ?></button>
			      				<?php } ?>
			      				-->
			      			<!--
			      				<?php if ($pid != "odometer") { ?>
			      				<a href="<?php echo base_url()."trackers/odometer/".$dev; ?>" class="button"><?php echo $this->lang->line("lodometer_report"); ?></a>
			      				<?php } else { ?>
			      				<button type="button"><?php echo $this->lang->line("lodometer_report"); ?></button>
			      				<?php } ?>
			      			-->
			      				<!--
							<?php if ($pid != "fuel") { ?>
			      				<a href="<?php echo base_url()."trackers/fuel/".$dev; ?>" class="button"><?php echo $this->lang->line("lfuel_report"); ?></a>
			      				<?php } else { ?>
			      				<button type="button"><?php echo $this->lang->line("lfuel_report"); ?></button>
			      				<?php } ?>
			      				-->

						<?php } ?>

						<?php
							$vehiclewithpulse = $this->config->item("vehicle_pulse");
							if ((($this->sess->user_type == 1) || (($this->sess->user_type == 3) && ($this->sess->user_agent_admin == 1)) || $this->sess->user_payment_pulsa) && in_array($vehicle->vehicle_type, $vehiclewithpulse))
							{
								if ($pid != "pulse")
								{
						?>
								<a href="<?php echo base_url()."trackers/pulse/".$dev; ?>" class="button"><?php echo $this->lang->line("lpulse_report"); ?></a>
						<?php 	}
								else
								{
						?>
								<button type="button"><?php echo $this->lang->line("lpulse_report"); ?></button>
						<?php 	}
							}
						?>

			      		<?php if ($pid != "geofence") { ?>
			      			<a href="<?php echo base_url()."trackers/geofence/".$dev; ?>" class="button"><?php echo $this->lang->line("lgeofence_report"); ?></a>
			      		<?php } else { ?>
							<button><?php echo $this->lang->line("lgeofence_report"); ?></button>
			      		<?php } ?>
