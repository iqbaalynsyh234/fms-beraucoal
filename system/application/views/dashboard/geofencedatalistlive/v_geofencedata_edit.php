
<!-- start sidebar menu -->
<div class="sidebar-container">
	<?=$sidebar;?>
</div>
<!-- end sidebar menu -->

<!-- start page content -->
<div class="page-content-wrapper" style="width:200%;">


  <div class="page-content">
    <br>
    <?php if ($this->session->flashdata('notif')) {?>
      <div class="alert alert-success" id="notifnya" style="display: none;"><?php echo $this->session->flashdata('notif');?></div>
    <?php }?>
    <div class="row">
      <div class="col-md-12">
          <div class="card-body">
            <form class="block-content form" id="frmgroup" onsubmit="javascript: return frmgeofence_onsubmit(this)">				
				<div class="card-box">
					<div class="card-header">
					  Edit Geofence Data (live)
					</div>	
									
				<table width="100%" cellpadding="3" class="tablelist">
					<?php if (isset($row)) { 
						$geofence_created = strtotime($row->geofence_created);
					?>					
					<input type="hidden" id="id" name="id" value="<?=$row->geofence_id;?>" />
					<tr style="border: 0px;">
						<td style="border: 0px;">ID</td>
						<td style="border: 0px;">:</td>
						<td style="border: 0px;"><?=$row->geofence_id;?></td>
					</tr>
					<tr style="border: 0px;">
						<td style="border: 0px;">Created</td>
						<td style="border: 0px;">:</td>
						<td style="border: 0px;"><?=date('d-m-Y H:i:s', strtotime('+7 hour', $geofence_created));?></td>
					</tr>
					<?php } ?>
					<tr style="border: 0px;">
						<td width="100" style="border: 0px;">Geofence Name</td>
						<td width="1" style="border: 0px;">:</td>
						<td style="border: 0px;">
							<input type="text" class="form-control" name="name" id="name" value="<?=isset($row) ? htmlspecialchars($row->geofence_name, ENT_QUOTES) : "";?>" maxlength="100" class="formdefault" />
							</td>
					</tr>
                    <tr style="border: 0px;">
						<td width="100" style="border: 0px;">Speed Limit Kosongan(kph)</td>
						<td width="1" style="border: 0px;">:</td>
						<td style="border: 0px;">
							<input type="text" class="form-control" name="speed" id="name" value="<?=isset($row) ? htmlspecialchars($row->geofence_speed, ENT_QUOTES) : "";?>" maxlength="4" class="formdefault" />
							</td>
					</tr> 
					<tr style="border: 0px;">
						<td width="100" style="border: 0px;">Speed Limit Muatan(kph)</td>
						<td width="1" style="border: 0px;">:</td>
						<td style="border: 0px;">
							<input type="text" class="form-control" name="speed_muatan" id="name" value="<?=isset($row) ? htmlspecialchars($row->geofence_speed_muatan, ENT_QUOTES) : "";?>" maxlength="4" class="formdefault" />
							</td>
					</tr> 
					<tr style="border: 0px;">
						<td width="100" style="border: 0px;">Geofence Type</td>
						<td width="1" style="border: 0px;">:</td>
						<td style="border: 0px;">
							<select class="select2" name="type" id="type">
								<option value="">Choose Type</option>
								<option value="pit" <? if ((! isset($row)) || ($row->geofence_type == 'pit')) { ?>selected<?php } ?>>Pit</option>
								<option value="port" <? if ((! isset($row)) || ($row->geofence_type == 'port')) { ?>selected<?php } ?>>Port</option>
								<option value="pool" <? if ((! isset($row)) || ($row->geofence_type == 'pool')) { ?>selected<?php } ?>>Pool</option>
								<option value="road" <? if ((! isset($row)) || ($row->geofence_type == 'road')) { ?>selected<?php } ?>>Road</option>
								<option value="site" <? if ((! isset($row)) || ($row->geofence_type == 'site')) { ?>selected<?php } ?>>Site</option>
								<option value="stop" <? if ((! isset($row)) || ($row->geofence_type == 'stop')) { ?>selected<?php } ?>>Stop</option>
								<option value="other" <? if ((! isset($row)) || ($row->geofence_type == 'other')) { ?>selected<?php } ?>>Other</option>
							</select>
						</td>
					</tr>   					
					
					
                        
    			<tr style="border: 0px;">
						<td style="border: 0px;">&nbsp;</td>
						<td style="border: 0px;">&nbsp;</td>
						<td style="border: 0px;">
								<input type="button" name="btncancel" id="btncancel" value=" Cancel " onclick="location='<?=base_url()?>geofencedatalistlive';" />
								<input type="submit" class="btn btn-success" name="btnsave" id="btnsave" value=" Save " />
						</td>
					</tr>					
				</table>
			</form>
          </div>
		  </div>
      </div>

    </div>
  </div>
</div>

<script type="text/javascript" src="js/script.js"></script>
<script src="<?php echo base_url()?>assets/dashboard/assets/js/jquery-1.7.1.min.js" type="text/javascript"></script>

<script type="text/javascript">
$("#notifnya").fadeIn(1000);
$("#notifnya").fadeOut(5000);

function btncancel(){
  $("#formaddcustomermaster").hide();
  $("#formtablecustomermaster").show();
}

// FOR DISABLE SUBMIT FORM
$(window).keydown(function(event){
  if(event.keyCode == 13) {
    event.preventDefault();
    return false;
  }
});
	

	function frmgeofence_onsubmit()
	{
			$.post("<?=base_url()?>geofencedatalistlive/save", $("#frmgroup").serialize(),
			function(r)
			{
				if (r.error)
				{
					alert(r.message);
					return false;
				}

				alert(r.message);
				location = "<?=base_url()?>geofencedatalistlive";
			}
			, "json"
		);
		return false;
	}

</script>
