<style media="screen">
  .material-icons {
    font-size: 50px;
    padding: 10px;
  }

  .info-box-icon.push-bottom {
    margin-top: 5px;
  }

  div#modallistvehicle {
    /* margin-top: -69%;
    margin-left: -11%; */
    max-height: 500px;
    max-width: 950px;
    overflow-x: auto;
    position: fixed;
    z-index: 9;
    background-color: #f1f1f1;
    text-align: left;
    border: 1px solid #d3d3d3;
  }

  #mydivheader {
    padding: 10px;
    cursor: move;
    z-index: 10;
    background-color: #2196F3;
    color: #fff;
  }

  div#modalcommandcenter {
    /* margin-top: -69%;
    margin-left: -11%; */
    height: auto;
    width: 50%;
    position: fixed;
    z-index: 9;
    /* background-color: #f1f1f1; */
    text-align: left;
    /* border: 1px solid #d3d3d3; */
  }
</style>
<!-- start sidebar menu -->
<div class="sidebar-container">
  <?=$sidebar;?>
</div>
<!-- end sidebar menu -->

<!-- start page content -->
<div class="page-content-wrapper">
  <div class="page-content">

    <div id="modalcommandcenter" style="display: none;">
      <div class="col-md-12">
        <div class="card">
        <div class="card-topline-green">
            <div class="card-head">
                <h4 id="titlemodal">Command Center</h4>
                <div class="tools" style="margin-top: -40px;">
                  <!-- <a class="fa fa-repeat btn-color box-refresh" href="javascript:;"></a> -->
                  <!-- <a class="t-collapse btn-color fa fa-chevron-down" href="javascript:;"></a> -->
                  <button type="button" class="btn btn-danger" name="button" onclick="closemodalcommandcenter();">X</button>
                </div>
            </div>
            <div class="card-body" id="bodymodal">
              <form class="form-horizontal" name="frmsubmitcommand" id="frmsubmitcommand" onsubmit="submitcommandcenter();">
                <div class="form-group row" id="mn_vehicle">
                  <label class="col-lg-3 col-md-3 control-label">Command Text
                  </label>
                  <div class="col-lg-4 col-md-3">
                    <textarea name="commandtext" id="commandtext" rows="8" cols="60"></textarea>
                  </div>
                </div>
              </form>
              <div class="form-group row  text-right">
                <label class="col-lg-3 col-md-4 control-label"></label>
                <div class="col-md-12">
                  <img src="<?php echo base_url();?>assets/transporter/images/loader2.gif" style="display: none;" id="loadernya">
                  <button class="btn btn-circle btn-primary" id="btnsubmitcommandcenter" type="button" onclick="submitcommandcenter();"/>Submit Command</button>
                </div>
              </div>
            </div>
        </div>
      </div>
      </div>
    </div>

    <div id="modallistvehicle" style="display: none;">
      <div id="mydivheader"></div>
      <div class="row" >
        <div class="col-md-12">
            <div class="card card-topline-yellow">
                <div class="card-head">
                    <h4 id="titlemodal">Vehicle List</h4>
                    <div class="tools" style="margin-top: -40px;">
                      <!-- <a class="fa fa-repeat btn-color box-refresh" href="javascript:;"></a> -->
                      <!-- <a class="t-collapse btn-color fa fa-chevron-down" href="javascript:;"></a> -->
                      <button type="button" class="btn btn-danger" name="button" onclick="closemodallistofvehicle();">X</button>
                    </div>
                </div>
                <div class="card-body" id="bodymodal">
                  <table class="table" class="display full-width" id="tablerom" style="font-size:12px;">
                      <thead>
                          <tr>
                            <th>No</th>
                            <th>Vehicle</th>
                            <th>Engine</th>
                            <th>GPS Time</th>
                            <th>Speed (Kph)</th>
                            <th>Location</th>
                            <th>Coordinate</th>
                            <th>Duration In Rom</th>
                          </tr>
                      </thead>
                      <tbody id="autoupdaterow">
                        <?php for ($i=0; $i < sizeof($vehicleinromfix); $i++) {?>
                          <tr>
                            <td><?php echo $i+1; ?></td>
                            <td><?php echo $vehicleinromfix[$i]['vehicle'] ?></td>
                            <td><?php echo $vehicleinromfix[$i]['engine'] ?></td>
                            <td><?php echo $vehicleinromfix[$i]['gps_time'] ?></td>
                            <td><?php echo $vehicleinromfix[$i]['speed'] ?></td>
                            <td><?php echo $vehicleinromfix[$i]['location'] ?></td>
                            <td><?php echo $vehicleinromfix[$i]['coordinate'] ?></td>
                            <td><?php echo $vehicleinromfix[$i]['duration'] ?></td>
                          </tr>
                        <?php } ?>
                      </tbody>
                  </table>

                  <!-- Port -->
                  <table class="table" class="display full-width" id="tableport" style="font-size:12px;">
                      <thead>
                          <tr>
                            <th>No</th>
                            <th>Vehicle</th>
                            <th>Engine</th>
                            <th>GPS Time</th>
                            <th>Speed (Kph)</th>
                            <th>Location</th>
                            <th>Coordinate</th>
                            <th>Duration In Port</th>
                          </tr>
                      </thead>
                      <tbody id="autoupdaterow">
                        <?php for ($i=0; $i < sizeof($vehicleinportfix); $i++) {?>
                          <tr>
                            <td><?php echo $i+1; ?></td>
                            <td><?php echo $vehicleinportfix[$i]['vehicle'] ?></td>
                            <td><?php echo $vehicleinportfix[$i]['engine'] ?></td>
                            <td><?php echo $vehicleinportfix[$i]['gps_time'] ?></td>
                            <td><?php echo $vehicleinportfix[$i]['speed'] ?></td>
                            <td><?php echo $vehicleinportfix[$i]['location'] ?></td>
                            <td><?php echo $vehicleinportfix[$i]['coordinate'] ?></td>
                            <td><?php echo $vehicleinportfix[$i]['duration'] ?></td>
                          </tr>
                        <?php } ?>
                      </tbody>
                  </table>

                  <!-- HAULING MUATAN-->
                  <table class="table" class="display full-width" id="tablehaulingmuatan" style="font-size:12px;">
                      <thead>
                          <tr>
                            <th>No</th>
                            <th>Vehicle</th>
                            <th>Engine</th>
                            <th>GPS Time</th>
                            <th>Speed (Kph)</th>
                            <th>Location</th>
                            <th>Coordinate</th>
                            <!-- <th>Path</th> -->
                          </tr>
                      </thead>
                      <tbody id="autoupdaterow">
                        <?php for ($i=0; $i < sizeof($vehicleinhaulingmuatan); $i++) {?>
                          <tr>
                            <td><?php echo $i+1; ?></td>
                            <td><?php echo $vehicleinhaulingmuatan[$i]['vehicle'] ?></td>
                            <td><?php echo $vehicleinhaulingmuatan[$i]['engine'] ?></td>
                            <td><?php echo $vehicleinhaulingmuatan[$i]['gps_time'] ?></td>
                            <td><?php echo $vehicleinhaulingmuatan[$i]['speed'] ?></td>
                            <td><?php echo $vehicleinhaulingmuatan[$i]['location'] ?></td>
                            <td><?php echo $vehicleinhaulingmuatan[$i]['coordinate'] ?></td>
                            <!-- <td><?php echo $vehicleinhaulingmuatan[$i]['jalur'] ?></td> -->
                          </tr>
                        <?php } ?>
                      </tbody>
                  </table>

                  <!-- HAULING KOSONGAN-->
                  <table class="table" class="display full-width" id="tablehaulingkosongan" style="font-size:12px;">
                      <thead>
                          <tr>
                            <th>No</th>
                            <th>Vehicle</th>
                            <th>Engine</th>
                            <th>GPS Time</th>
                            <th>Speed (Kph)</th>
                            <th>Location</th>
                            <th>Coordinate</th>
                            <!-- <th>Path</th> -->
                          </tr>
                      </thead>
                      <tbody id="autoupdaterow">
                        <?php for ($i=0; $i < sizeof($vehicleinhaulingkosongan); $i++) {?>
                          <tr>
                            <td><?php echo $i+1; ?></td>
                            <td><?php echo $vehicleinhaulingkosongan[$i]['vehicle'] ?></td>
                            <td><?php echo $vehicleinhaulingkosongan[$i]['engine'] ?></td>
                            <td><?php echo $vehicleinhaulingkosongan[$i]['gps_time'] ?></td>
                            <td><?php echo $vehicleinhaulingkosongan[$i]['speed'] ?></td>
                            <td><?php echo $vehicleinhaulingkosongan[$i]['location'] ?></td>
                            <td><?php echo $vehicleinhaulingkosongan[$i]['coordinate'] ?></td>
                            <!-- <td><?php echo $vehicleinhaulingkosongan[$i]['jalur'] ?></td> -->
                          </tr>
                        <?php } ?>
                      </tbody>
                  </table>

                  <!-- POOL -->
                  <table class="table" class="display full-width" id="tablepool" style="font-size:12px;">
                      <thead>
                          <tr>
                            <th>No</th>
                            <th>Vehicle</th>
                            <th>Engine</th>
                            <th>GPS Time</th>
                            <th>Speed (Kph)</th>
                            <th>Location</th>
                            <th>Coordinate</th>
                            <th>Duration In Pool</th>
                          </tr>
                      </thead>
                      <tbody id="autoupdaterow">
                        <tr>
                          <td>1</td>
                          <td>BIB-KMB-895</td>
                          <td>ON</td>
                          <td>12-11-2020 08:14:53</td>
                          <td>0</td>
                          <td>POOL KMB</td>
                          <td>-3.6007,115.6313</td>
                          <td>1 Hours 45 Min</td>
                        </tr>
                      </tbody>
                  </table>

                  <!-- LUAR HAULING  -->
                  <table class="table" class="display full-width" id="tableluarhauling" style="font-size:12px;">
                      <thead>
                          <tr>
                            <th>No</th>
                            <th>Vehicle</th>
                            <th>Engine</th>
                            <th>GPS Time</th>
                            <th>Speed (Kph)</th>
                            <th>Location</th>
                            <th>Coordinate</th>
                          </tr>
                      </thead>
                      <tbody id="autoupdaterow">
                        <tr>
                          <td>1</td>
                          <td>PPA-DT-4175</td>
                          <td>OFF</td>
                          <td>07-10-2020 08:49:55</td>
                          <td>0</td>
                          <td>KOTA BARU KALIMANTAN SELATAN</td>
                          <td>-3.6007,115.6313</td>
                        </tr>
                        <tr>
                          <td>2</td>
                          <td>BIB-RAM-851</td>
                          <td>OFF</td>
                          <td>12-11-2020 08:14:53</td>
                          <td>0</td>
                          <td>KOTA BARU KALIMANTAN SELATAN</td>
                          <td>-3.6007,115.6313</td>
                        </tr>
                        <tr>
                          <td>3</td>
                          <td>BIB-MKS-179</td>
                          <td>OFF</td>
                          <td>12-11-2020 08:14:53</td>
                          <td>0</td>
                          <td>KOTA BARU KALIMANTAN SELATAN</td>
                          <td>-3.6007,115.6313</td>
                        </tr>
                        <tr>
                          <td>4</td>
                          <td>BIB-KMB-895</td>
                          <td>OFF</td>
                          <td>12-11-2020 08:14:53</td>
                          <td>0</td>
                          <td>RAYA BATULICIN - BANJARMASIN</td>
                          <td>-3.6007,115.6313</td>
                        </tr>
                      </tbody>
                  </table>
                </div>
            </div>
        </div>
      </div>
    </div>

    <div class="page-bar">
      <div class="page-title-breadcrumb">
        <div class=" pull-left">
          <div class="page-title">Tracking Board (Today) V2</div>
        </div>
      </div>
    </div>

    <button type="button" name="btncommandcenter" id="btncommandcenter" class="btn btn-circle btn-success" onclick="showmodalcommandcenter();">
      <span class="fa fa-microphone"></span>
      Command Center
    </button>

    <!-- ROM -->
    <div class="row">
      <div class="col-md-12">
          <div class="panel" id="panel_form">
            <header class="panel-heading panel-heading-blue">ROM / PIT</header>
            <div class="panel-body" id="bar-parent10">
              <div class="row">
                <!-- <?php
                $overcapacity_rom = 1;
                $vehicleinromtest = 2;
                for ($i=0; $i < 1; $i++) {?>
                   <?php
                   if ($vehicleinromtest == $overcapacity_rom) {?>
                     <div class="col-xl-3 col-md-6 col-12" style="cursor:pointer;" onclick="showmodalrom();">
                       <div class="info-box bg-danger">
                         <span class="info-box-icon push-bottom"><i class="material-icons">my_location</i></span>
                         <div class="info-box-content">
                           <span class="info-box-text">ROM 1</span>
                             <span class="progress-description" style="font-size:15px;">
                               <?php echo $overcapacity_rom; ?>
                             </span>
                         </div>
                       </div>
                     </div>
                   <?php }else {?>
                     <div class="col-xl-3 col-md-6 col-12" style="cursor:pointer;" onclick="showmodalrom();">
                       <div class="info-box bg-blue">
                         <span class="info-box-icon push-bottom"><i class="material-icons">my_location</i></span>
                         <div class="info-box-content">
                           <span class="info-box-text">ROM 1</span>
                             <span class="progress-description" style="font-size:15px;">
                               <?php echo $vehicleinromtest; ?>
                             </span>
                         </div>
                       </div>
                     </div>
                   <?php }?>
                <?php }?> -->


                <!--  INI YANG PERULANGAN -->
                <?php
                $overcapacity_rom = 1;
                 for ($i=0; $i < sizeof($romlist); $i++) {?>
      							 <?php
                     // $testarray = array("ROM 01", "ROM 02", "ROM 01", "ROM 03", "ROM 02", "ROM 01");
                     // $vehicleinrom;
                     $vals = array_count_values($vehicleinrom);
                     if (isset($vals[$romlist[$i]])) {
                       if ($romlist[$i] == $vals[$romlist[$i]]) {?>
                         <div class="col-xl-3 col-md-6 col-12" style="cursor:pointer;" onclick="showmodalrom();">
                           <div class="info-box bg-blue">
                             <span class="info-box-icon push-bottom">
                               <i class="material-icons">my_location</i>
                             </span>
                             <div class="info-box-content">
                               <span class="info-box-text"><?php echo $romlist[$i]; ?></span>
                                 <span class="progress-description" style="font-size:15px;">
                                   <?php echo $vals[$romlist[$i]]; ?>
                                 </span>
                             </div>
                           </div>
                         </div>
                       <?php }else {?>
                         <?php if ($vals[$romlist[$i]] >= $overcapacity_rom) {?>
                           <div class="col-xl-3 col-md-6 col-12" style="cursor:pointer;" onclick="showmodalrom();">
                             <div class="info-box bg-danger">
                               <span class="info-box-icon push-bottom">
                                 <i class="material-icons">my_location</i>
                               </span>
                               <div class="info-box-content">
                                 <span class="info-box-text"><?php echo $romlist[$i]; ?></span>
                                   <span class="progress-description" style="font-size:15px;">
                                     <?php echo $vals[$romlist[$i]]; ?>
                                   </span>
                               </div>
                             </div>
                           </div>
                         <?php }else {?>
                           <div class="col-xl-3 col-md-6 col-12" style="cursor:pointer;" onclick="showmodalrom();">
                             <div class="info-box bg-blue">
                               <span class="info-box-icon push-bottom">
                                 <i class="material-icons">my_location</i>
                               </span>
                               <div class="info-box-content">
                                 <span class="info-box-text"><?php echo $romlist[$i]; ?></span>
                                   <span class="progress-description" style="font-size:15px;">
                                     <?php echo $vals[$romlist[$i]]; ?>
                                   </span>
                               </div>
                             </div>
                           </div>
                         <?php } ?>
                       <?php }?>
                     <?php }else {?>
                       <div class="col-xl-3 col-md-6 col-12" style="cursor:pointer;">
                         <div class="info-box bg-blue">
                           <span class="info-box-icon push-bottom">
                             <i class="material-icons">my_location</i>
                           </span>
                           <div class="info-box-content">
                             <span class="info-box-text"><?php echo $romlist[$i]; ?></span>
                               <span class="progress-description" style="font-size:15px;">
                                <?php echo "0"; ?>
                               </span>
                           </div>
                         </div>
                       </div>
                     <?php }

                 	   // echo "<pre>";
                     // var_dump($vals);die();
                     // echo "<pre>";
                      ?>
                <?php } ?>
              </div>
            </div>
          </div>
      </div>
    </div>

    <!-- HAULING & TIMBANGAN -->
    <div class="row">
      <div class="col-md-12">
          <div class="panel" id="panel_form">
            <header class="panel-heading panel-heading-yellow">HAULING & JEMBATAN TIMBANG</header>
            <div class="panel-body" id="bar-parent10">
              <div class="row">
                <?php
                $totalinhaulingkosongan = sizeof($vehicleinhaulingkosongan);
                  if ($totalinhaulingkosongan >= 2) {?>
                    <div class="col-xl-3 col-md-6 col-12" style="cursor:pointer;" onclick="showmodalhaulingkosongan();">
                      <div class="info-box bg-danger">
                        <span class="info-box-icon push-bottom"><i class="material-icons">layers</i></span>
                        <div class="info-box-content">
                          <span class="info-box-text">Kosongan</span>
                            <span class="progress-description" style="font-size:15px;">
                              <?php echo $totalinhaulingkosongan; ?>
                            </span>
                        </div>
                      </div>
                    </div>
                  <?php }else {?>
                    <div class="col-xl-3 col-md-6 col-12" style="cursor:pointer;" onclick="showmodalhaulingkosongan();">
                      <div class="info-box bg-success">
                        <span class="info-box-icon push-bottom"><i class="material-icons">layers</i></span>
                        <div class="info-box-content">
                          <span class="info-box-text">Kosongan</span>
                            <span class="progress-description" style="font-size:15px;">
                              <?php echo $totalinhaulingkosongan; ?>
                            </span>
                        </div>
                      </div>
                    </div>
                  <?php }
                ?>

                <?php
                $totalinhaulingmuatan = sizeof($vehicleinhaulingmuatan);
                  if ($totalinhaulingmuatan >= 2) {?>
                    <div class="col-xl-3 col-md-6 col-12" style="cursor:pointer;"  onclick="showmodalhaulingmuatan()">
                      <div class="info-box bg-danger">
                        <span class="info-box-icon push-bottom"><i class="material-icons">layers</i></span>
                        <div class="info-box-content">
                          <span class="info-box-text">Muatan</span>
                            <span class="progress-description" style="font-size:15px;">
                              <?php echo $totalinhaulingmuatan; ?>
                            </span>
                        </div>
                      </div>
                    </div>
                  <?php }else {?>
                    <div class="col-xl-3 col-md-6 col-12" style="cursor:pointer;"  onclick="showmodalhaulingmuatan()">
                      <div class="info-box bg-primary">
                        <span class="info-box-icon push-bottom"><i class="material-icons">layers</i></span>
                        <div class="info-box-content">
                          <span class="info-box-text">Muatan</span>
                            <span class="progress-description" style="font-size:15px;">
                              <?php echo $totalinhaulingmuatan; ?>
                            </span>
                        </div>
                      </div>
                    </div>
                  <?php }
                ?>

                <?php for ($i=0; $i < sizeof($timbanganlist); $i++) {?>
                    <div class="col-xl-3 col-md-6 col-12" style="cursor:pointer;">
                      <div class="info-box bg-warning">
                        <span class="info-box-icon push-bottom"><i class="material-icons">local_movies</i></span>
                        <div class="info-box-content">
                          <span class="info-box-text"><?php echo $timbanganlist[$i]; ?></span>
                            <span class="progress-description" style="font-size:15px;">
                							 <?php
                               // $testarray = array("ROM 01", "ROM 02", "ROM 01", "ROM 03", "ROM 02", "ROM 01");
                               // $vehicleinrom;
                               $vals = array_count_values($vehicleintimbangan);
                               if (isset($vals[$timbanganlist[$i]])) {
                                 if ($timbanganlist[$i] == $vals[$timbanganlist[$i]]) {
                                   echo "0";
                                 }else {
                                   echo $vals[$timbanganlist[$i]];
                                 }
                               }else {
                                 echo "0";
                               }

                           	   // echo "<pre>";
                               // var_dump($vals);die();
                               // echo "<pre>";
                                ?>
              							</span>
                        </div>
                      </div>
                    </div>
                <?php } ?>
              </div>
            </div>
          </div>
      </div>
    </div>

    <!-- PORT -->
    <div class="row">
      <div class="col-md-12">
          <div class="panel" id="panel_form">
            <header class="panel-heading panel-heading-green">PORT</header>
            <div class="panel-body" id="bar-parent10">
              <div class="row">
                <!-- <div class="col-xl-3 col-md-6 col-12" style="cursor:pointer;" onclick="showmodalport();">
                  <div class="info-box bg-success">
                    <span class="info-box-icon push-bottom"><i class="material-icons">directions_boat</i></span>
                    <div class="info-box-content">
                      <span class="info-box-text">PORT BIB</span>
                        <span class="progress-description" style="font-size:15px;">
                           1
                        </span>
                    </div>
                  </div>
                </div> -->
                <?php
                $overcapacity_port = 1;
                 for ($i=0; $i < sizeof($portlist); $i++) {?>
                  <?php
                  $vals = array_count_values($vehicleinport);
                  if (isset($vals[$portlist[$i]])) {
                    if ($portlist[$i] == $vals[$portlist[$i]]) {?>
                      <div class="col-xl-3 col-md-6 col-12" style="cursor:pointer;" onclick="showmodalport();">
                        <div class="info-box bg-success">
                          <span class="info-box-icon push-bottom"><i class="material-icons">directions_boat</i></span>
                          <div class="info-box-content">
                            <span class="info-box-text"><?php echo $portlist[$i]; ?></span>
                              <span class="progress-description" style="font-size:15px;">
                                <?php echo $vals[$portlist[$i]]; ?>
                              </span>
                           </div>
                         </div>
                       </div>
                     <?php }else {?>
                       <?php if ($vals[$portlist[$i]] >= $overcapacity_port) {?>
                         <div class="col-xl-3 col-md-6 col-12" style="cursor:pointer;" onclick="showmodalport();">
                           <div class="info-box bg-danger">
                             <span class="info-box-icon push-bottom"><i class="material-icons">directions_boat</i></span>
                             <div class="info-box-content">
                               <span class="info-box-text"><?php echo $portlist[$i]; ?></span>
                                 <span class="progress-description" style="font-size:15px;">
                                   <?php echo $vals[$portlist[$i]]; ?>
                                 </span>
                              </div>
                            </div>
                          </div>
                       <?php }else {?>
                         <div class="col-xl-3 col-md-6 col-12" style="cursor:pointer;" onclick="showmodalport();">
                           <div class="info-box bg-success">
                             <span class="info-box-icon push-bottom"><i class="material-icons">directions_boat</i></span>
                             <div class="info-box-content">
                               <span class="info-box-text"><?php echo $portlist[$i]; ?></span>
                                 <span class="progress-description" style="font-size:15px;">
                                  <?php echo $vals[$portlist[$i]]; ?>
                                 </span>
                              </div>
                            </div>
                          </div>
                       <?php } ?>
                     <?php } ?>
                   <?php }else {?>
                     <div class="col-xl-3 col-md-6 col-12" style="cursor:pointer;">
                       <div class="info-box bg-success">
                         <span class="info-box-icon push-bottom"><i class="material-icons">directions_boat</i></span>
                         <div class="info-box-content">
                           <span class="info-box-text"><?php echo $portlist[$i]; ?></span>
                             <span class="progress-description" style="font-size:15px;">
                               <?php echo "0"; ?>
                             </span>
                          </div>
                        </div>
                      </div>
                   <?php } ?>
                <?php } ?>
              </div>
            </div>
          </div>
      </div>
    </div>

    <!-- POOL -->
    <div class="row">
      <div class="col-md-12">
          <div class="panel" id="panel_form">
            <header class="panel-heading panel-heading-yellow">POOL / WORKSHOP</header>
            <div class="panel-body" id="bar-parent10">
              <div class="row">
                <!-- <div class="col-xl-3 col-md-6 col-12" style="cursor:pointer;" onclick="showmodalpool();">
                  <div class="info-box bg-danger">
                    <span class="info-box-icon push-bottom"><i class="material-icons">store_mall_directory</i></span>
                    <div class="info-box-content">
                      <span class="info-box-text">POOL KMB</span>
                        <span class="progress-description" style="font-size:15px;">
                           1
                        </span>
                    </div>
                  </div>
                </div> -->
                <?php
                $overcapacity_pool = 1;
                 for ($i=0; $i < sizeof($poollist)-1; $i++) {?>
      							 <?php
                     $vals = array_count_values($vehicleinpool);
                     if (isset($vals[$poollist[$i]])) {
                       if ($poollist[$i] == $vals[$poollist[$i]]) {?>
                         <div class="col-xl-3 col-md-6 col-12" style="cursor:pointer;" onclick="getdatainpool('<?php echo $poollist[$i]; ?>');">
                           <input type="text" id="idpool" value="<?php echo $poollist[$i]; ?>" hidden>
                           <div class="info-box bg-warning">
                             <span class="info-box-icon push-bottom"><i class="material-icons">store_mall_directory</i></span>
                             <div class="info-box-content">
                               <span class="info-box-text"><?php echo $poollist[$i]; ?></span>
                                 <span class="progress-description" style="font-size:15px;">
                                   <?php echo $vals[$poollist[$i]]; ?>
                                 </span>
                             </div>
                           </div>
                         </div>
                       <?php }else {?>
                         <?php if ($vals[$poollist[$i]] >= $overcapacity_pool) {?>
                           <div class="col-xl-3 col-md-6 col-12" style="cursor:pointer;" onclick="getdatainpool('<?php echo $poollist[$i]; ?>');">
                             <input type="text" id="idpool" value="<?php echo $poollist[$i]; ?>" hidden>
                             <div class="info-box bg-danger">
                               <span class="info-box-icon push-bottom"><i class="material-icons">store_mall_directory</i></span>
                               <div class="info-box-content">
                                 <span class="info-box-text"><?php echo $poollist[$i]; ?></span>
                                   <span class="progress-description" style="font-size:15px;">
                                     <?php echo $vals[$poollist[$i]]; ?>
                                   </span>
                               </div>
                             </div>
                           </div>
                         <?php }else {?>
                           <div class="col-xl-3 col-md-6 col-12" style="cursor:pointer;" onclick="getdatainpool('<?php echo $poollist[$i]; ?>');">
                             <input type="text" id="idpool" value="<?php echo $poollist[$i]; ?>" hidden>
                             <div class="info-box bg-warning">
                               <span class="info-box-icon push-bottom"><i class="material-icons">store_mall_directory</i></span>
                               <div class="info-box-content">
                                 <span class="info-box-text"><?php echo $poollist[$i]; ?></span>
                                   <span class="progress-description" style="font-size:15px;">
                                     <?php echo "0"; ?>
                                   </span>
                               </div>
                             </div>
                           </div>
                         <?php } ?>
                       <?php }
                     }else {?>
                       <div class="col-xl-3 col-md-6 col-12" style="cursor:pointer;" onclick="getdatainpool('<?php echo $poollist[$i]; ?>');">
                         <input type="text" id="idpool" value="<?php echo $poollist[$i]; ?>" hidden>
                         <div class="info-box bg-warning">
                           <span class="info-box-icon push-bottom"><i class="material-icons">store_mall_directory</i></span>
                           <div class="info-box-content">
                             <span class="info-box-text"><?php echo $poollist[$i]; ?></span>
                               <span class="progress-description" style="font-size:15px;">
                                 <?php echo "0"; ?>
                               </span>
                           </div>
                         </div>
                       </div>
                     <?php }
                 	   // echo "<pre>";
                     // var_dump($vals);die();
                     // echo "<pre>";
                      ?>
                <?php } ?>
              </div>
            </div>
          </div>
      </div>
    </div>

    <!-- OUT OF HAULING -->
    <div class="row">
      <div class="col-md-12">
          <div class="panel" id="panel_form">
            <header class="panel-heading panel-heading-blue">LUAR HAULING</header>
            <div class="panel-body" id="bar-parent10">
              <div class="row">
                    <div class="col-xl-3 col-md-6 col-12" style="cursor:pointer;">
                      <!-- onclick="showmodalluarhauling();" -->
                      <div class="info-box bg-primary">
                        <span class="info-box-icon push-bottom"><i class="material-icons">layers_clear</i></span>
                        <div class="info-box-content">
                          <span class="info-box-text">Luar Hauling</span>
                            <span class="progress-description" style="font-size:15px;">
                							 <?php echo sizeof($vehicleoutofhauling); ?>
              							</span>
                        </div>
                      </div>
                    </div>
              </div>
            </div>
          </div>
      </div>
    </div>
  </div>



  <script type="text/javascript" src="js/script.js"></script>
  <script src="<?php echo base_url()?>assets/dashboard/assets/js/jquery-1.7.1.min.js" type="text/javascript"></script>
<script type="text/javascript">
setInterval(function(){
   window.location.reload(1);
}, 30000);
$("#modallistvehicle").hide();
  function getdatainpool(idpool){
      console.log("idpool : ", idpool);
  }

  dragElement(document.getElementById("modallistvehicle"));

  function dragElement(elmnt) {
    var pos1 = 0, pos2 = 0, pos3 = 0, pos4 = 0;
    if (document.getElementById(elmnt.id + "header")) {
      // if present, the header is where you move the DIV from:
      document.getElementById(elmnt.id + "header").onmousedown = dragMouseDown;
    } else {
      // otherwise, move the DIV from anywhere inside the DIV:
      elmnt.onmousedown = dragMouseDown;
    }

    function dragMouseDown(e) {
      e = e || window.event;
      e.preventDefault();
      // get the mouse cursor position at startup:
      pos3 = e.clientX;
      pos4 = e.clientY;
      document.onmouseup = closeDragElement;
      // call a function whenever the cursor moves:
      document.onmousemove = elementDrag;
    }

    function elementDrag(e) {
      e = e || window.event;
      e.preventDefault();
      // calculate the new cursor position:
      pos1 = pos3 - e.clientX;
      pos2 = pos4 - e.clientY;
      pos3 = e.clientX;
      pos4 = e.clientY;
      // set the element's new position:
      elmnt.style.top = (elmnt.offsetTop - pos2) + "px";
      elmnt.style.left = (elmnt.offsetLeft - pos1) + "px";
    }

    function closeDragElement() {
      // stop moving when mouse button is released:
      document.onmouseup = null;
      document.onmousemove = null;
    }
  }

  function showmodalrom() {
    $("#tableport").hide();
    $("#tablehaulingmuatan").hide();
    $("#tablepool").hide();
    $("#tableluarhauling").hide();
    $("#tablehaulingkosongan").hide();
    $("#tablerom").show();
    $("#modallistvehicle").animate({width: 'toggle'}, "slow");
    // $("#modallistvehicle").show();
  }

  function showmodalport(){
    $("#tablerom").hide();
    $("#tablepool").hide();
    $("#tablehaulingmuatan").hide();
    $("#tableluarhauling").hide();
    $("#tablehaulingkosongan").hide();
    $("#tableport").show();
    $("#modallistvehicle").animate({width: 'toggle'}, "slow");
  }

  function showmodalhaulingmuatan(){
    $("#tablerom").hide();
    $("#tableport").hide();
    $("#tablepool").hide();
    $("#tableluarhauling").hide();
    $("#tablehaulingkosongan").hide();
    $("#tablehaulingmuatan").show();
    $("#modallistvehicle").animate({width: 'toggle'}, "slow");
  }

  function showmodalpool(){
    $("#tablerom").hide();
    $("#tableport").hide();
    $("#tablehaulingmuatan").hide();
    $("#tableluarhauling").hide();
    $("#tablehaulingkosongan").hide();
    $("#tablepool").show();
    $("#modallistvehicle").animate({width: 'toggle'}, "slow");
  }

  function showmodalluarhauling(){
    $("#tablerom").hide();
    $("#tableport").hide();
    $("#tablehaulingmuatan").hide();
    $("#tablepool").hide();
    $("#tablehaulingkosongan").hide();
    $("#tableluarhauling").show();
    $("#modallistvehicle").animate({width: 'toggle'}, "slow");
  }

  function showmodalhaulingkosongan(){
    $("#tablerom").hide();
    $("#tableport").hide();
    $("#tablehaulingmuatan").hide();
    $("#tablepool").hide();
    $("#tableluarhauling").hide();
    $("#tablehaulingkosongan").show();
    $("#modallistvehicle").animate({width: 'toggle'}, "slow");
  }

  function closemodallistofvehicle(){
    $("#modallistvehicle").animate({width: 'toggle'}, "slow");
    // $("#modallistvehicle").hide();
  }

  function closemodalcommandcenter(){
    $("#modalcommandcenter").animate({width: 'toggle'}, "slow");
    // $("#modallistvehicle").hide();
  }

  function showmodalcommandcenter(){
    $("#modalcommandcenter").animate({width: 'toggle'}, "slow");
  }

  function submitcommandcenter(){
    $("#loadernya").show();
    $.post("<?php echo base_url() ?>trackingboard/submitcommand", jQuery("#frmsubmitcommand").serialize(), function(response){
      console.log("response : ", response);
        $("#loadernya").hide();
        var code   = response.code;
        var msg    = response.msg;
        $("#commandtext").val("");
          if (code == 400) {
            $("#modalcommandcenter").animate({width: 'toggle'}, "slow");
            alert(""+msg);
          }else {
            $("#modalcommandcenter").animate({width: 'toggle'}, "slow");
            alert(""+msg);
          }
    }, "json");
  }
</script>
