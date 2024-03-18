
<link rel="stylesheet" type="text/css" href="<?=base_url();?>assets/css/maps.css" />
<script type="text/javascript" src="<?=base_url();?>assets/js/openlayers/OpenLayers.js"></script>
<script type="text/javascript" src="<?=base_url();?>assets/js/openlayers/lib/OpenLayers/Layer/OpenStreetMap.js"></script>

<?php
	$key = $this->config->item("GOOGLE_MAP_API_KEY");

	if(isset($key) && $key != "") { ?>
		<script src="https://maps.googleapis.com/maps/api/js?key=<?php echo $key;?>" type="text/javascript"></script>
	<?php } else { ?>
		<script src="http://maps.google.com/maps/api/js?V=3.3&amp;sensor=false"></script>
	<? } ?>

<script>
	<?php if (isset($initmap)) echo $initmap; ?>

	var map = null;

</script>

<!-- start sidebar menu -->
<div class="sidebar-container">
  <?=$sidebar;?>
</div>
<!-- end sidebar menu -->

<!-- start page content -->
<div class="page-content-wrapper">
  <div class="page-content">
    <br>
    <?php if ($this->session->flashdata('notif')) {?>
      <div class="alert alert-success" id="notifnya" style="display: none;"><?php echo $this->session->flashdata('notif');?></div>
    <?php }?>
    <!--<div class="alert alert-success" id="notifnya2" style="display: none;"></div>-->
    <div class="row">
      <div class="col-md-12" id="tablevehicleforgeofence">
          <div class="card-box">
            <div class="card-body">
			<div id="main">
				<div class="block-border">
					<?=$contentstreet?>
				</div>
			</div>
            </div>
		 </div>
    </div>
	</div>
  </div>
</div>
