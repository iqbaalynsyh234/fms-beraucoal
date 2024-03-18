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
              <table id="example1" class="table table-striped" style="width:100%;">
                <thead>
          				<tr>
          					<th>
                      <!-- <button type="button" class="btn btn-success btn-xs">
                        <span class="fa fa-plus"></span>
                      </button> -->
                      No
                    </th>
          					<th>Vehicle</th>
          					<th>Control</th>
          				</tr>
          			</thead>
                <tbody>
                  <?php for($i=0;$i<count($datavehicle);$i++) { ?>
          				  <tr>
            					<td width="2%"><?=$i+1?></td>
                      <td><?=$datavehicle[$i]['vehicle_name'].' - '.$datavehicle[$i]['vehicle_no'];?></td>
      							  <td>
                        <?php
                          $explodedevice = explode("@", $datavehicle[$i]['vehicle_device']);
                         ?>
                        <a href="<?php echo base_url()?>geofencedatalive/manage/<?php echo $explodedevice[0].'/'.$explodedevice[1]?>">
                          <img src="<?=base_url();?>assets/images/zoomin.gif" width="20px" height="20px" border="0" title="Geofence Setup">
                        </a>
      							  </td>
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
