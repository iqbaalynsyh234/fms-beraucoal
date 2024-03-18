
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
    <div class="alert alert-success" id="notifnya2" style="display: none;"></div>
    <div class="row">
      <div class="col-md-12" >
        <div class="panel" id="panel_form">
          <header class="panel-heading panel-heading-blue">Geofence Data List (live)</header>
          <div class="panel-body" id="bar-parent10">
              <table id="example1" class="table table-striped">
                <thead>
          				<tr>
					<th>
                      <a href="<?=base_url()?>geofencedatalive" type="button" class="btn btn-success btn-xs" title="Add New Geofence">
                        <span class="fa fa-plus"></span>
                      </a>No
                    </th>

							<th style="text-align:center;">Geofence Name</th>
							<!--<th style="text-align:center;">Geofence Type</th>-->
							<th style="text-align:center;">Speed Limit Kosongan(kph)</th>
							<th style="text-align:center;">Speed Limit Muatan(kph)</th>
							<th style="text-align:center;">Type</th>
							<th style="text-align:center;">Creator</th>
							<th style="text-align:center;">Created</th>
							<th style="text-align:center;">Control</th>
          				</tr>
          			</thead>
                <tbody>
                  <?php for($i=0;$i<count($data);$i++) {
					$geofence_created = strtotime($data[$i]->geofence_created);
				  ?>
          				  <tr>
            					<td valign="top" align="center" style="text-align:center;"><?=$i+1+$offset?></td>
								<td valign="top" style="text-align:center;"><?=$data[$i]->geofence_name;?></td>
								<td valign="top" style="text-align:center;"><?=$data[$i]->geofence_speed;?></td>
								<td valign="top" style="text-align:center;"><?=$data[$i]->geofence_speed_muatan;?></td>
								<td valign="top" style="text-align:center;"><?=$data[$i]->geofence_type;?></td>
								<td valign="top" style="text-align:center;">
									<?php
									if (isset($ruser))
									{
										foreach ($ruser as $usr)
										{
											if ($usr->user_id == $data[$i]->geofence_user)
											{
												echo $usr->user_name;
											}
										}
									}
									?>
								</td>

								</td>
								<td valign="top" style="text-align:center;">
									<?=date('d-m-Y H:i:s', strtotime('+7 hour', $geofence_created));?>
									<?php if($data[$i]->geofence_name == ""){ ?>
										<small><b>New!</b></small>
									<?php } ?>
								</td>
								
								<?php if ($this->sess->user_group == 0 ){ ?>
								<td valign="top" style="text-align:center;">
									<a href="<?php echo base_url();?>geofencedatalistlive/edit/<?php echo $data[$i]->geofence_id;?>">
									  <img src="<?php echo base_url();?>assets/images/edit.gif" />
									</a>
									<a href="#" onclick="javascript:delete_data(<?=$data[$i]->geofence_id;?>)"><img src="<?=base_url();?>assets/images/trash.gif" alt="Delete Data" title="Delete Data"></a>
								</td>
								<?php } ?>
                    </tr>
                  <? } ?>
  							</tbody>
  						</table>
            </div>
      </div>
    </div>

  </div>
</div>
</div>

<script type="text/javascript">


  function frmadd_onsubmit()
	{
		jQuery("#loader").show();
		jQuery.post("<?=base_url()?>geofencedatalistlive/save", jQuery("#frmadd").serialize(),
			function(r)
			{
				jQuery("#loader").hide();
				if (r.error)
				{
					alert(r.message);
					return false;
				}

				alert(r.message);
				location = r.redirect;
			}
			, "json"
		);
		return false;
	}

	function delete_data(id)
		{
			if (confirm("Are you sure delete this data?")) {
				jQuery.post('<?=base_url()?>geofencedatalistlive/delete_geofence/' + id, {}, function(r){
					if (r.error) {
						alert(r.message);
						return;
					}else{
						alert(r.message);
						location = "<?=base_url()?>geofencedatalistlive";
						return;
					}
				}, "json");
			}
		}
</script>
