<style media="screen">
.timeline {
  list-style: none;
  padding: 20px 0 20px;
  position: relative;
}

.timeline:before {
  top: 30px;
  bottom: 30px;
  position: absolute;
  content: " ";
  width: 5px;
  background-color: #000000;
  left: 65%;
  /* margin-left: -1.5px; */
}

.timeline > li {
  margin-bottom: 2px;
  position: relative;
}

.timeline > li:before,
.timeline > li:after {
  content: " ";
  display: table;
}

.timeline > li:after {
  clear: both;
}

.timeline > li:before,
.timeline > li:after {
  content: " ";
  display: table;
}

.timeline > li:after {
  clear: both;
}

.timeline > li > .timeline-panel {
  width: 46%;
  float: left;
  /* border: 1px solid #d4d4d4; */
  /* border-radius: 2px; */
  padding: 20px;
  position: relative;
  /* -webkit-box-shadow: 0 1px 6px rgba(0, 0, 0, 0.175); */
  /* box-shadow: 0 1px 6px rgba(0, 0, 0, 0.175); */
}

.timeline > li > .timeline-badge {
  color: #fff;
  width: 100px;
  height: 25px;
  line-height: 2px;
  font-size: 14px;
  text-align: center;
  position: absolute;
  left: 50%;
  margin-left: -25px;
  background-color: green;
  z-index: 100;
  /* border-top-right-radius: 50%;
  border-top-left-radius: 50%;
  border-bottom-right-radius: 50%;
  border-bottom-left-radius: 50%; */
}

.timeline > li > .timeline-badge2 {
  color: #fff;
  width: 40px;
  height: 40px;
  line-height: 25px;
  font-size: 14px;
  text-align: center;
  position: absolute;
  left: 50%;
  margin-left: -25px;
  top: -20%;
  padding: 7px;
  background-color: green;
  z-index: 100;
  border-top-right-radius: 50%;
  border-top-left-radius: 50%;
  border-bottom-right-radius: 50%;
  border-bottom-left-radius: 50%;
}

.timeline > li > .timeline-badge3 {
  color: #fff;
  width: 40px;
  height: 40px;
  line-height: 25px;
  font-size: 14px;
  text-align: center;
  position: absolute;
  left: 90%;
  margin-left: -25px;
  top: -20%;
  padding: 7px;
  background-color: green;
  z-index: 100;
  border-top-right-radius: 50%;
  border-top-left-radius: 50%;
  border-bottom-right-radius: 50%;
  border-bottom-left-radius: 50%;
}

.timeline > li.timeline-inverted > .timeline-panel {
  float: center;
}

.timeline > li.timeline-inverted > .timeline-panel:before {
  border-left-width: 0;
  border-right-width: 15px;
  left: -15px;
  right: auto;
}

.timeline > li.timeline-inverted > .timeline-panel:after {
  border-left-width: 0;
  border-right-width: 14px;
  left: -14px;
  right: auto;
}

.timeline-heading {
  width: 70%;
}
.timeline-clock {
  width: 25%;
}

.timeline-title {
  margin-top: 0;
  color: inherit;
}

.timeline-body > p,
.timeline-body > ul {
  margin-bottom: 0;
}

.timeline-body > p + p {
  margin-top: 5px;
}


  #valueTitle{
    margin: 5%;
    margin-left: 25%;
    font-weight: 400;
    font-size: 14px;
    color: white;
  }

  #valueonsite{
    margin: 5%;
    margin-left: 25%;
    font-weight: 400;
    font-size: 14px;
    color: white;
  }

  .custom-map-control-button {
    margin : 10px;
    height: 40px;
    cursor: pointer;
    direction: ltr;
    overflow: hidden;
    text-align: center;
    position: relative;
    color: rgb(0, 0, 0);
    font-family: "Roboto", Arial, sans-serif;
    -webkit-user-select: none;
    font-size: 18px !important;
    background-color: rgb(255, 255, 255);
    padding: 1px 6px;
    border-bottom-left-radius: 2px;
    border-top-left-radius: 2px;
    -webkit-background-clip: padding-box;
    background-clip: padding-box;
    border: 1px solid rgba(0, 0, 0, 0.14902);
    -webkit-box-shadow: rgba(0, 0, 0, 0.298039) 0px 1px 4px -1px;
    box-shadow: rgba(0, 0, 0, 0.298039) 0px 1px 4px -1px;
    min-width: 100px;
    font-weight: 500;
  }
</style>

<script type="text/javascript">
  function saveMapSetting(){
    jQuery("#loader2").show();
    jQuery.post("<?=base_url();?>maps/mapSetting", jQuery("#frmadd").serialize(),
      function(r)
      {
        jQuery("#loader2").hide();
        console.log("response : ", r);
          if (r.msg == "success") {
            if (confirm("Map Setting Successfully Updated")) {
              window.location = '<?php echo base_url() ?>maps/heatmap2';
            }
          }else {
            if (confirm("Map Setting Failed Updated")) {
              window.location = '<?php echo base_url() ?>maps/heatmap2';
            }
          }
      }, "json");
      return false;
  }
</script>

<div class="sidebar-container">
  <?=$sidebar;?>
</div>

<div class="page-content-wrapper">
  <div class="page-content">
    <div class="col-sm-12 col-md-4 col-lg-3">
      <button class="btn btn-info" id="notifdevicestatus" style="display:none;"></button>
    </div>
    <div class="row">
      <div class="col-sm-12">
        <div class="card">
          <div class="card-header">
            <h5>BIB MAPS OVERLAY</h5>
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-md-3">
                <select class="form-control select2" name="contractor" id="contractor" onchange="changevehiclelist()">
                </select>
              </div>

              <div class="col-md-3">
                <select class="form-control select2" name="searchnopol" id="searchnopol" onchange="forsearchinput()">
                </select>
              </div>

              <div class="col-md-3">
                <select class="form-control select2" name="mapsOptions" id="mapsOptions" onchange="mapsOptions()">
                  <option value="0">--Maps Option</option>
                  <option value="showHeatmap">Maps</option>
                  <option value="showTableMuatan1">Quick Count Data</option>
                  <!-- <option value="showTableMuatan2">Consolidated Data</option> -->
                  <option value="showTableRom">ROM</option>
                  <option value="showTablePort">PORT</option>
                  <option value="showTablePool">POOL / WS</option>
                  <option value="outofhauling">Out Of Hauling</option>
                </select>
              </div>

              <?php
              $privilegecode = $this->sess->user_id_role;
                if ($privilegecode == 0 || $privilegecode == 1) {?>
                  <div class="col-md-3">
                    <button type="button" name="button" class="btn btn-danger btn-md" id="mapSetting" style="margin-left:2%;" onclick="customMymodal('modalMapSetting');">
                      <span class="fa fa-cogs"></span>
                    </button>
                  </div>
                <?php }?>


            </div> <br>

            <div id="modalMapSetting" class="modal">
              <div class="modal-content">
                <div class="row">
                  <div class="col-md-10">
                    <p class="modalCustomTitle">
                      Pengaturan Map
                    </p>
                  </div>
                  <div class="col-md-2">
                    <div class="closethismodal btn btn-danger btn-sm">X</div>
                  </div>
                </div>
                <form class="form-horizontal" name="frmadd" id="frmadd" onsubmit="javascript:return saveMapSetting()">
                  <table class="table table-striped">
                    <tr>
                      <td>Batas Tengah (Kuning)</td>
                      <td>
                        <input type="number" name="middle_limit" id="middle_limit" class="form-control" value="<?php echo $mapsetting[0]['mapsetting_middle_limit'] ?>">
                      </td>
                    </tr>
                    <tr>
                      <td>Batas Atas (Merah)</td>
                      <td>
                        <input type="number" name="top_limit" id="top_limit" class="form-control" value="<?php echo $mapsetting[0]['mapsetting_top_limit'] ?>">
                      </td>
                    </tr>
                    <tr>
                      <td></td>
                      <td class="text-right">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                      </td>
                    </tr>
                  </table>
                </form>
              </div>
            </div>

            <div id="modalState" class="modal">
              <div class="modal-content">
                <div class="row">
                  <div class="col-md-10">
                    <p class="modalTitleforAll" id="modalStateTitle">
                    </p>
                    <div id="lastcheckpoolws" style="font-size:12px; color:black"></div>
                  </div>
                  <div class="col-md-2">
                    <div class="closethismodalall btn btn-danger btn-sm">X</div>
                  </div>
                </div>
                  <div id="modalStateContent"></div>
              </div>
            </div>

            <div id="modalKmListQuickCount" class="modalkmlist">
              <div class="modal-content-kmlist">
                <div class="row">
                  <div class="col-md-10">
                    <p class="modalTitleforAll" id="modalKmListQuickCountTitle">
                    </p>
                    <div id="lastcheckKmListQuickCount" style="font-size:12px; color:black"></div>
                  </div>
                  <div class="col-md-2">
                    <div class="closethismodalkm btn btn-danger btn-sm">X</div>
                  </div>
                </div>
                <div class="row" id="modalKmAll">
                  <div class="col-md-6">
                    <p style="color: black; font-style: bold;">
                      Kosongan
                    </p>
                    <div id="modalStateContentKosongan"></div>
                  </div>

                  <div class="col-md-6">
                    <p style="color: black; font-style: bold;">
                      Muatan
                    </p>
                    <div id="modalStateContentMuatan"></div>
                  </div>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-12">
                <div id="tableShowMuatan" style="width: 100%; max-height: 450px; display:none;">
                  <!-- display:none; -->

                  <div class="row">
                    <div class="col-md-6">
                      <p style="margin-left: 1%; font-size:12px;">Kosongan || Muatan</p>
                    </div>

                    <div class="col-md-6">
                      <p style="position: absolute;
                        right: 0px;
                        padding: 3px;"
                        id="lastupdateconsolidated">
                      </p>
                    </div>
                  </div>

                  <div class="row" style="margin-top:-3%; margin-left: -5%; overflow-y:hidden; overflow-x:auto;">
                    <div class="col-md-2">
                      <ul class="timeline">
                        <?php for ($i=30; $i > 25; $i--) {?>
                          <li class="timeline-inverted">
                            <div class="" id="labelvehicleonmuatan<?php echo $i ?>" onclick="listVehicleOnKm('<?php echo $i; ?>')">KM <?php echo $i; ?></div>
                            <div class="timeline-panel"></div>
                          </li>
                          <li class="timeline-inverted">
                            <div class="" id="vehicleonkosongan<?php echo $i ?>"></div>
                            <div class="" id="vehicleonmuatan<?php echo $i ?>"></div>
                            <div class="timeline-panel"></div>
                          </li>
                        <?php } ?>
                      </ul>
                    </div>

                    <div class="col-md-2">
                      <ul class="timeline">
                        <?php for ($i=25; $i > 20; $i--) {?>
                          <li class="timeline-inverted">
                            <div class="" id="labelvehicleonmuatan<?php echo $i ?>" onclick="listVehicleOnKm('<?php echo $i; ?>')">KM <?php echo $i; ?></div>
                            <div class="timeline-panel"></div>
                          </li>
                          <li class="timeline-inverted">
                            <div class="" id="vehicleonkosongan<?php echo $i ?>"></div>
                            <div class="" id="vehicleonmuatan<?php echo $i ?>"></div>
                            <div class="timeline-panel"></div>
                          </li>
                        <?php } ?>
                      </ul>
                    </div>

                    <div class="col-md-2">
                      <ul class="timeline">
                        <?php for ($i=20; $i > 15; $i--) {?>
                          <li class="timeline-inverted">
                            <div class="" id="labelvehicleonmuatan<?php echo $i ?>" onclick="listVehicleOnKm('<?php echo $i; ?>')">KM <?php echo $i; ?></div>
                            <div class="timeline-panel"></div>
                          </li>
                          <li class="timeline-inverted">
                            <div class="" id="vehicleonkosongan<?php echo $i ?>"></div>
                            <div class="" id="vehicleonmuatan<?php echo $i ?>"></div>
                            <div class="timeline-panel"></div>
                          </li>
                        <?php } ?>
                      </ul>
                    </div>

                    <div class="col-md-2">
                      <ul class="timeline">
                        <?php for ($i=15; $i > 10; $i--) {?>
                          <li class="timeline-inverted">
                            <div class="" id="labelvehicleonmuatan<?php echo $i ?>" onclick="listVehicleOnKm('<?php echo $i; ?>')">KM <?php echo $i; ?></div>
                            <div class="timeline-panel"></div>
                          </li>
                          <li class="timeline-inverted">
                            <div class="" id="vehicleonkosongan<?php echo $i ?>"></div>
                            <div class="" id="vehicleonmuatan<?php echo $i ?>"></div>
                            <div class="timeline-panel"></div>
                          </li>
                        <?php } ?>
                      </ul>
                    </div>

                    <div class="col-md-2">
                      <ul class="timeline">
                        <?php for ($i=10; $i > 5; $i--) {?>
                          <li class="timeline-inverted">
                            <div class="" id="labelvehicleonmuatan<?php echo $i ?>" onclick="listVehicleOnKm('<?php echo $i; ?>')">KM <?php echo $i; ?></div>
                            <div class="timeline-panel"></div>
                          </li>
                          <li class="timeline-inverted">
                            <div class="" id="vehicleonkosongan<?php echo $i ?>"></div>
                            <div class="" id="vehicleonmuatan<?php echo $i ?>"></div>
                            <div class="timeline-panel"></div>
                          </li>
                        <?php } ?>
                      </ul>
                    </div>

                    <div class="col-md-2">
                      <ul class="timeline">
                        <?php for ($i=5; $i > 0; $i--) {?>
                          <li class="timeline-inverted">
                            <div class="" id="labelvehicleonmuatan<?php echo $i ?>" onclick="listVehicleOnKm('<?php echo $i; ?>')">KM <?php echo $i; ?></div>
                            <div class="timeline-panel"></div>
                          </li>
                          <li class="timeline-inverted">
                            <div class="" id="vehicleonkosongan<?php echo $i ?>"></div>
                            <div class="" id="vehicleonmuatan<?php echo $i ?>"></div>
                            <div class="timeline-panel"></div>
                          </li>
                        <?php } ?>
                      </ul>
                    </div>
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-md-12">
                  <div id="tableShowRom" style="width: 100%; max-height: 400px; display:none;">
                    <div class="row">
                      <div class="col-md-12">
                        <p style="position: absolute;
                          right: 0px;
                          padding: 3px;"
                          id="jumlahtotalInRom">
                        </p>
                      </div>
                    </div>

                    <div class="row" style="margin-top:3%; margin-left:1%;">
                      <div class="col-md-2">
                        <table class="table table-bordered" style="font-size:12px;">
                            <tr>
                              <td>
                                <div class="btn-group">
                                  <div type="button" class="" id="labelvehicleinrom_1" onclick="listVehicleOnRom(9312)"></div>
                                  <div type="button" class="" id="vehicleinRom_1" onclick="listVehicleOnRom(9312)"></div>
                                </div>
                              </td>

                              <td>
                                <div class="btn-group">
                                  <div type="button" class="btn btn-primary btn-lg" id="labelvehicleinrom_1_2_road" onclick="listVehicleOnRom(9387)"></div>
                                  <div type="button" class="btn btn-primary btn-lg dropdown-toggle m-r-20" id="vehicleinRom_1_2_road" onclick="listVehicleOnRom(9387)"></div>
                                </div>
                              </td>

                              <td>
                                <div class="btn-group">
                                  <div type="button" class="btn btn-primary btn-lg" id="labelvehicleinrom_2" onclick="listVehicleOnRom(9313)"></div>
                                  <div type="button" class="btn btn-primary btn-lg dropdown-toggle m-r-20" id="vehicleinRom_2" onclick="listVehicleOnRom(9313)"></div>
                                </div>
                              </td>

                              <td>
                                <div class="btn-group">
                                  <div type="button" class="btn btn-primary btn-lg" id="labelvehicleinrom_3" onclick="listVehicleOnRom(9315)"></div>
                                  <div type="button" class="btn btn-primary btn-lg dropdown-toggle m-r-20" id="vehicleinRom_3" onclick="listVehicleOnRom(9315)"></div>
                                </div>
                              </td>
                            </tr>

                            <tr>
                              <td>
                                <div class="btn-group">
                                  <div type="button" class="btn btn-primary btn-lg" id="labelvehicleinrom_3_4_road" onclick="listVehicleOnRom(9388)"></div>
                                  <div type="button" class="btn btn-primary btn-lg dropdown-toggle m-r-20" id="vehicleinRom_3_4_road" onclick="listVehicleOnRom(9388)"></div>
                                </div>
                              </td>

                              <td>
                                <div class="btn-group">
                                  <div type="button" class="btn btn-primary btn-lg" id="labelvehicleinrom_4" onclick="listVehicleOnRom(9316)"></div>
                                  <div type="button" class="btn btn-primary btn-lg dropdown-toggle m-r-20" id="vehicleinRom_4" onclick="listVehicleOnRom(9316)"></div>
                                </div>
                              </td>

                              <td>
                                <div class="btn-group">
                                  <div type="button" class="btn btn-primary btn-lg" id="labelvehicleinrom_6_road" onclick="listVehicleOnRom(9389)"></div>
                                  <div type="button" class="btn btn-primary btn-lg dropdown-toggle m-r-20" id="vehicleinRom_6_road" onclick="listVehicleOnRom(9389)"></div>
                                </div>
                              </td>

                              <td>
                                <div class="btn-group">
                                  <div type="button" class="btn btn-primary btn-lg" id="labelvehicleinrom_6" onclick="listVehicleOnRom(9317)"></div>
                                  <div type="button" class="btn btn-primary btn-lg dropdown-toggle m-r-20" id="vehicleinRom_6" onclick="listVehicleOnRom(9317)"></div>
                                </div>
                              </td>
                            </tr>

                            <tr>
                              <td>
                                <div class="btn-group">
                                  <div type="button" class="btn btn-primary btn-lg" id="labelvehicleinrom_7" onclick="listVehicleOnRom(9318)"></div>
                                  <div type="button" class="btn btn-primary btn-lg dropdown-toggle m-r-20" id="vehicleinRom_7" onclick="listVehicleOnRom(9318)"></div>
                                </div>
                              </td>

                              <td>
                                <div class="btn-group">
                                  <div type="button" class="btn btn-primary btn-lg" id="labelvehicleinrom_7_8_road" onclick="listVehicleOnRom(9390)"></div>
                                  <div type="button" class="btn btn-primary btn-lg dropdown-toggle m-r-20" id="vehicleinRom_7_8_road" onclick="listVehicleOnRom(9390)"></div>
                                </div>
                              </td>

                              <td>
                                <div class="btn-group">
                                  <div type="button" class="btn btn-primary btn-lg" id="labelvehicleinrom_8" onclick="listVehicleOnRom(9319)"></div>
                                  <div type="button" class="btn btn-primary btn-lg dropdown-toggle m-r-20" id="vehicleinRom_8" onclick="listVehicleOnRom(9319)"></div>
                                </div>
                              </td>
                            </tr>
                        </table>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-md-12">
                  <div id="tableShowOutOfHauling" style="width: 100%; max-height: 400px; display:none;">
                    <!-- <div class="row">
                      <div class="col-md-12">
                        <p style="position: absolute;
                          right: 0px;
                          padding: 3px;"
                          id="jumlahtotaloutofhauling">
                        </p>
                      </div>
                    </div> -->

                    <div class="row" style="margin-top:3%; margin-left:1%;">
                      <div class="col-md-2">
                        <table class="table table-bordered" style="font-size:12px;">
                            <tr>
                              <td>
                                <div class="btn-group">
                                  <div type="button" class="" id="labelvehicleoutofhauling" onclick="listOutOfHauling();"></div>
                                  <div type="button" class="" id="vehicleoutofhauling" onclick="listOutOfHauling();"></div>
                                </div>
                              </td>
                            </tr>
                        </table>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-md-12">
                  <div id="tableShowPort" style="width: 100%; max-height: 400px; display:none;">
                    <div class="row">
                      <div class="col-md-12">
                        <p style="position: absolute;
                          right: 0px;
                          padding: 3px;"
                          id="jumlahtotalInPort">
                        </p>
                      </div>
                    </div>

                    <div class="row" style="margin-top:3%; margin-left: 2%;">
                      <div class="col-md-12">
                        <table class="table table-bordered" style="font-size:12px;">
                            <tr>
                              <td>
                                <div class="btn-group">
                                  <div type="button" class="btn btn-primary" id="labelvehicleinport_bbc" onclick="listVehicleOnPort(9334);"></div>
                                  <div type="button" class="btn btn-primary dropdown-toggle m-r-20" id="vehicleinPort_bbc" onclick="listVehicleOnPort(9334);"></div>
                                </div>
                              </td>

                              <td>
                                <div class="btn-group">
                                  <div type="button" class="btn btn-primary" id="labelvehicleinport_bib" onclick="listVehicleOnPort(9333);"></div>
                                  <div type="button" class="btn btn-primary dropdown-toggle m-r-20" id="vehicleinPort_bib" onclick="listVehicleOnPort(9333);"></div>
                                </div>
                              </td>

                              <td>
                                <div class="btn-group">
                                  <div type="button" class="btn btn-primary" id="labelvehicleinport_bir" onclick="listVehicleOnPort(9335);"></div>
                                  <div type="button" class="btn btn-primary dropdown-toggle m-r-20" id="vehicleinPort_bir" onclick="listVehicleOnPort(9335);"></div>
                                </div>
                              </td>

                              <td>
                                <div class="btn-group">
                                  <div type="button" class="btn btn-primary" id="labelvehicleinport_tia" onclick="listVehicleOnPort(9332);"></div>
                                  <div type="button" class="btn btn-primary dropdown-toggle m-r-20" id="vehicleinPort_tia" onclick="listVehicleOnPort(9332);"></div>
                                </div>
                              </td>
                            </tr>

                        </table>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="col-md-12">
                <div id="tableShowPool" style="width: 100%; max-height: 400px; display:none;">
                  <div class="row">
                    <div class="col-md-11">
                      <p style="position: absolute;
                        right: 0px;
                        padding: 3px;"
                        id="jumlahtotalinpool">
                      </p>
                    </div>
                  </div>

                  <div class="row" style="margin-top:3%;">
                    <div class="col-md-2">
                      <table class="table table-bordered" style="font-size:12px;">
                          <tr>
                            <td>
                              <div class="btn-group">
                                <div type="button" class="btn btn-primary" id="labelvehicleinpool_bbs" onclick="getVehicleByPool(9761);"></div>
                                <div type="button" class="btn btn-primary dropdown-toggle m-r-20" id="vehicleinpool_bbs" onclick="getVehicleByPool(9761);"></div>
                              </div>
                            </td>

                            <td>
                              <div class="btn-group">
                                <div type="button" class="btn btn-primary" id="labelvehicleinpool_bka" onclick="getVehicleByPool(9398);"></div>
                                <div type="button" class="btn btn-primary dropdown-toggle m-r-20" id="vehicleinpool_bka" onclick="getVehicleByPool(9398);"></div>
                              </div>
                            </td>

                            <td>
                              <div class="btn-group">
                                <div type="button" class="btn btn-primary" id="labelvehicleinpool_bsl" onclick="getVehicleByPool(9305);"></div>
                                <div type="button" class="btn btn-primary dropdown-toggle m-r-20" id="vehicleinpool_bsl" onclick="getVehicleByPool(9305);"></div>
                              </div>
                            </td>

                            <td>
                              <div class="btn-group">
                                <div type="button" class="btn btn-primary" id="labelvehicleinpool_gecl" onclick="getVehicleByPool(9306);"></div>
                                <div type="button" class="btn btn-primary dropdown-toggle m-r-20" id="vehicleinpool_gecl" onclick="getVehicleByPool(9306);"></div>
                              </div>
                            </td>
                          </tr>

                          <tr>
                            <td>
                              <div class="btn-group">
                                <div type="button" class="btn btn-primary" id="labelvehicleinpool_kusan_bawah" onclick="getVehicleByPool(9323);"></div>
                                <div type="button" class="btn btn-primary dropdown-toggle m-r-20" id="vehicleinpool_kusan_bawah" onclick="getVehicleByPool(9323);"></div>
                              </div>
                            </td>

                            <td>
                              <div class="btn-group">
                                <div type="button" class="btn btn-primary" id="labelvehicleinpool_kusan" onclick="getVehicleByPool(9311);"></div>
                                <div type="button" class="btn btn-primary dropdown-toggle m-r-20" id="vehicleinpool_kusan" onclick="getVehicleByPool(9311);"></div>
                              </div>
                            </td>

                            <td>
                              <div class="btn-group">
                                <div type="button" class="btn btn-primary" id="labelvehicleinpool_mks" onclick="getVehicleByPool(9307);"></div>
                                <div type="button" class="btn btn-primary dropdown-toggle m-r-20" id="vehicleinpool_mks" onclick="getVehicleByPool(9307);"></div>
                              </div>
                            </td>

                            <td>
                              <div class="btn-group">
                                <div type="button" class="btn btn-primary" id="labelvehicleinpool_ram" onclick="getVehicleByPool(9747);"></div>
                                <div type="button" class="btn btn-primary dropdown-toggle m-r-20" id="vehicleinpool_ram" onclick="getVehicleByPool(9747);"></div>
                              </div>
                            </td>
                          </tr>

                          <tr>
                            <td>
                              <div class="btn-group">
                                <div type="button" class="btn btn-primary" id="labelvehicleinpool_rbt" onclick="getVehicleByPool(9403);"></div>
                                <div type="button" class="btn btn-primary dropdown-toggle m-r-20" id="vehicleinpool_rbt" onclick="getVehicleByPool(9403);"></div>
                              </div>
                            </td>

                            <td>
                              <div class="btn-group">
                                <div type="button" class="btn btn-primary" id="labelvehicleinpool_stli" onclick="getVehicleByPool('stli');"></div>
                                <div type="button" class="btn btn-primary dropdown-toggle m-r-20" id="vehicleinpool_stli" onclick="getVehicleByPool('stli');"></div>
                              </div>
                            </td>

                            <td>
                              <div class="btn-group">
                                <div type="button" class="btn btn-primary" id="labelvehicleinws_gecl" onclick="getVehicleByPool('ws_gecl');"></div>
                                <div type="button" class="btn btn-primary dropdown-toggle m-r-20" id="vehicleinws_gecl" onclick="getVehicleByPool('ws_gecl');"></div>
                              </div>
                            </td>

                            <td>
                              <div class="btn-group">
                                <div type="button" class="btn btn-primary" id="labelvehicleinws_kmb" onclick="getVehicleByPool(9760);"></div>
                                <div type="button" class="btn btn-primary dropdown-toggle m-r-20" id="vehicleinws_kmb" onclick="getVehicleByPool(9760);"></div>
                              </div>
                            </td>
                          </tr>

                          <tr>
                            <td>
                              <div class="btn-group">
                                <div type="button" class="btn btn-primary" id="labelvehicleinws_mks" onclick="getVehicleByPool(9495);"></div>
                                <div type="button" class="btn btn-primary dropdown-toggle m-r-20" id="vehicleinws_mks" onclick="getVehicleByPool(9495);"></div>
                              </div>
                            </td>

                            <td>
                              <div class="btn-group">
                                <div type="button" class="btn btn-primary" id="labelvehicleinws_rbt" onclick="getVehicleByPool(9762);"></div>
                                <div type="button" class="btn btn-primary dropdown-toggle m-r-20" id="vehicleinws_rbt" onclick="getVehicleByPool(9762);"></div>
                              </div>
                            </td>
                          </tr>

                      </table>
                    </div>
                  </div>
                </div>
              </div>

              <div class="col-md-8">
                <input type="hidden" id="valueMode" value="0">
                  <div id="mapShow">
                     <!-- display:none; -->
                     <div id="mapsnya" style="width: 100%; height: 400px;"></div>
                  </div>
              </div>

              <div class="col-md-4" id="realtimealertshowhide">
                 <!-- style="display:none;" -->
                <div class="panel" id="panel_form">
                  <header class="panel-heading panel-heading-red">
                    Realtime Alert
                    Show :
                    <select class="select" name="changelimitrealtimalert" id="changelimitrealtimalert" onchange="changelimit();">
                      <option value="10">10</option>
                      <option value="20">20</option>
                      <option value="30">30</option>
                    </select>

                    <button type="button" name="button" id="activatesound" class="btn btn-flat btn-sm" title="Sound" onclick="activatesound();" style="display:none;">
                      <span class="fa fa-volume-up"></span>
                    </button>

                    <button type="button" name="button" id="activatesound2" class="btn btn-flat btn-sm" title="Sound" onclick="activatesound2();">
                      <span class="fa fa-volume-off"></span>
                    </button>
                  </header>
                  <div class="panel-body" id="bar-parent10">
                    <div id="realtimealertcontent"></div>
                      <table class="table">
                        <!-- <div id="newalertcontent" style="font-size:10px; margin-top: 2%; background: yellow; display:none;"></div> -->
                        <div id="summaryalertcontent" style="font-size:10px; margin-top: 1%; overflow-x: auto; height: 270px; max-height: 270px;"></div>
                      </table>
                  </div>
                  <div class="panel-footer">
                    <div class="row">
                      <div class="col-md-12">
                        <a href="<?php echo base_url() ?>overspeedreport" type="button" class="btn btn-flat form-control">View More</a>
                      </div>
                    </div>
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

<script>
  $(document).ready(function() {
    setTimeout(function(){
      appendthevehiclelist();
      appendthecontractorlist();
    }, 3000);

    function appendthecontractorlist(){
      $.post("<?php echo base_url() ?>maps/getdatacontractor", {}, function(response){
        // console.log("response : ", response);
        var data = response.data;
        var html = "";

            html += '<option value="0">--Contractor List</option>';
            for (var i = 0; i < data.length; i++) {
              html += '<option value="'+data[i].company_id+'">'+data[i].company_name+'</option>';
            }
          $("#contractor").html(html);
      },"json");
    }

    function appendthevehiclelist(){
      // console.log("masuk");
      var html = "";

          html += '<option value="0">--Vehicle List</option>';
          html += '<?php for ($i=0; $i < sizeof($vehicledata); $i++) {?>';
            html += '<option value="<?php echo $vehicledata[$i]['vehicle_no'] ?>"><?php echo $vehicledata[$i]['vehicle_no'] ?></option>';
          html += '<?php } ?>';

        $("#searchnopol").html(html);
    }
  });

  function changevehiclelist(){
    // console.log("masuk gan");
    var companyid = $("#contractor").val();
    $.post("<?php echo base_url() ?>maps/getvehiclebycontractor", {companyid : companyid}, function(response){
      // console.log("response : ", response);
      var data = response.data;
      var html = "";

          html += '<option value="0">--Vehicle List</option>';
          for (var i = 0; i < data.length; i++) {
            html += '<option value="'+data[i].vehicle_no+'">'+data[i].vehicle_no+'</option>';
          }
        $("#searchnopol").html(html);
    },"json");
  }

  // $("#btnmaptable").show();
  $("#showtable").hide();
  $("#modallistvehicle").hide();
  $("#modalfivereport").hide();

  var datafixnya        = "";
  var dataposition      = [];
  var overlaystatus     = 0;
  var overlaysarray     = [];
  var arraypointheatmap = [];
  var marker            = [];
  var markernya         = [];
  var markers           = [];
  var markerss          = [];
  var markerpools       = [];
  var intervalstart;
  var camdevices        = ["TK510CAMDOOR", "TK510CAM", "GT08", "GT08DOOR", "GT08CAM", "GT08CAMDOOR"];
  var bibarea           = ["KM", "POOL", "ST", "ROM", "PIT", "PORT", "POOl", "WS", "WB", "PT.BIB"];
  // var car = "M17.402,0H5.643C2.526,0,0,3.467,0,6.584v34.804c0,3.116,2.526,5.644,5.643,5.644h11.759c3.116,0,5.644-2.527,5.644-5.644 V6.584C23.044,3.467,20.518,0,17.402,0z M22.057,14.188v11.665l-2.729,0.351v-4.806L22.057,14.188z M20.625,10.773 c-1.016,3.9-2.219,8.51-2.219,8.51H4.638l-2.222-8.51C2.417,10.773,11.3,7.755,20.625,10.773z M3.748,21.713v4.492l-2.73-0.349 V14.502L3.748,21.713z M1.018,37.938V27.579l2.73,0.343v8.196L1.018,37.938z M2.575,40.882l2.218-3.336h13.771l2.219,3.336H2.575z M19.328,35.805v-7.872l2.729-0.355v10.048L19.328,35.805z";

  var car = "M 2 2 C 2 1 3 0 5 0 H 19 C 21 0 22 1 22 2 V 17 H 2 Z M 3 2 C 3.6667 2.6667 4.3333 3.3333 5 4 H 19 C 19.6667 3.3333 20.3333 2.6667 21 2 C 21 1 20.3333 1.3333 20 1 H 4 V 1 C 3.6667 1.3333 3 1 3 2 M 19 5 V 13 C 19.6667 13.3333 20.3333 13.6667 21 14 V 4 Z M 5 5 H 5 C 4.3333 4.6667 3.6667 4.3333 3 4 V 14 C 3.6667 13.6667 4.3333 13.3333 5 13 Z M 6 16 H 18 V 15 H 6 Z M 7 8 V 13 V 13 H 8 V 8 Z M 10 8 V 13 H 11 V 8 M 17 8 H 16 V 13 H 17 Z M 13 8 V 13 V 13 V 13 H 14 V 8 Z M 0 4 C 0 4 0 3 1 3 H 2 V 4 Z M 22 4 V 3 V 3 H 23 C 24 3 24 4 24 4 H 24 Z M -1 19 H 3 V 18 H 4 V 17 H 20 V 18 H 21 H 21 V 19 H 25 V 61 H -1 Z Z M 1 21 V 54 C 1.6667 43.6667 2.3333 33.3333 2 23 H 22 C 21.6667 33.3333 22.3333 43.6667 23 54 V 21 V 21 Z Z M 5 27 V 53 H 6 V 27 Z M 19 27 H 18 V 53 V 53 H 19 Z M 15 27 H 14 V 53 V 53 V 53 H 15 Z M 9 27 V 53 H 10 V 27 Z";

  var middle_limit = '<?php echo $mapsetting[0]['mapsetting_middle_limit'] ?>';
  var top_limit    = '<?php echo $mapsetting[0]['mapsetting_top_limit'] ?>';

  function initMap() {
    var vehicle           = '<?php echo json_encode($vehicledata); ?>';
    var poolmaster        = '<?php echo json_encode($poolmaster); ?>';

    var bounds            = new google.maps.LatLngBounds();
    var boundspool        = new google.maps.LatLngBounds();

    if (datafixnya == "") {
      try {
        var datacode  = JSON.parse(vehicle);
        objpoolmaster = JSON.parse(poolmaster);
        // console.log("disini objpoolmaster: ", objpoolmaster);
      } catch (e) {
        // console.log("e : ", e);
      }
    } else {
      var datacode  = vehicle;
      objpoolmaster = poolmaster;
    }

    obj              = datacode;
    objpoolmasterfix = objpoolmaster;
    console.log("obj : ", obj);

    for (var i = 0; i < obj.length; i++) {
      arraypointheatmap.push(new google.maps.LatLng(obj[i].auto_last_lat, obj[i].auto_last_long));
    }

    var gradientdefault = ["rgba(102, 255, 0, 0)",
                          "rgba(102, 255, 0, 1)",
                          "rgba(147, 255, 0, 1)",
                          "rgba(193, 255, 0, 1)",
                          "rgba(238, 255, 0, 1)",
                          "rgba(244, 227, 0, 1)",
                          "rgba(249, 198, 0, 1)",
                          "rgba(255, 170, 0, 1)",
                          "rgba(255, 113, 0, 1)",
                          "rgba(255, 57, 0, 1)",
                          "rgba(255, 0, 0, 1)"];

    map = new google.maps.Map(document.getElementById("mapsnya"), {
      zoom: 14,
      // center: { lat: parseFloat(-3.7288), lng: parseFloat(115.6452)},
      center: { lat: parseFloat(-3.577068), lng: parseFloat(115.655054)},
      mapTypeId: "satellite",
      options: {
        gestureHandling: 'greedy'
      }
    });
  heatmap = new google.maps.visualization.HeatmapLayer({
    data: arraypointheatmap,
    // gradient : gradientdefault
    // dissipating: true,
    // radius: 20,
    opacity: 50,
    maxIntensity: 8
    // data: dataposition,
  });
  heatmap.setMap(map);

  // TOOGEL BUTTON BIB MAP
    var toggleButton = document.createElement("button");
    toggleButton.textContent = "BIB Maps";
    toggleButton.classList.add("custom-map-control-button");
    map.controls[google.maps.ControlPosition.TOP_CENTER].push(toggleButton);

     toggleButton.addEventListener("click", () => {
      addoverlay(map);
    });

    // addoverlay(map);

    // var changegradient = document.createElement("button");
    // changegradient.textContent = "Change Gradient";
    // changegradient.classList.add("custom-map-control-button");
    // map.controls[google.maps.ControlPosition.TOP_CENTER].push(changegradient);
    //
    //  changegradient.addEventListener("click", () => {
    //   changeGradient();
    // });

    intervalstart = setInterval(simultango, 10000);
}

  function simultango() {
    // console.log("simultan Started");
      // arraypointheatmap.setMap(null);
    jQuery.post("<?=base_url();?>map/lastinfoall", {},function(r) {
        console.log("response : ", r);
        heatmap.setMap(null);
        var arraypointheatmap2 = [];
        for (var i = 0; i < r.data.length; i++) {
          arraypointheatmap2.push(new google.maps.LatLng(r.data[i].gps_latitude_real, r.data[i].gps_longitude_real));
        }

      heatmap = new google.maps.visualization.HeatmapLayer({
        data: arraypointheatmap2,
        dissipating: true,
        // radius: 20,
        // opacity: 1,
        // data: dataposition,
        opacity: 50,
        maxIntensity: 8,
        map: map,
      });
      }, "json");
  }

  function changeGradient() {
    const gradient = [
      "rgba(0, 255, 255, 0)",
      "rgba(0, 255, 255, 1)",
      "rgba(0, 191, 255, 1)",
      "rgba(0, 127, 255, 1)",
      "rgba(0, 63, 255, 1)",
      "rgba(0, 0, 255, 1)",
      "rgba(0, 0, 223, 1)",
      "rgba(0, 0, 191, 1)",
      "rgba(0, 0, 159, 1)",
      "rgba(0, 0, 127, 1)",
      "rgba(63, 0, 91, 1)",
      "rgba(127, 0, 63, 1)",
      "rgba(191, 0, 31, 1)",
      "rgba(255, 0, 0, 1)",
    ];

    heatmap.set("gradient", heatmap.get("gradient") ? null : gradient);
    // heatmap.set("gradient", gradient);
  }

// Heatmap data: 500 Points
function getPoints() {
  return [
    new google.maps.LatLng(-3.7015, 115.5621),
    new google.maps.LatLng(-3.7030, 115.5643),
    new google.maps.LatLng(-3.7032, 115.5645),
    new google.maps.LatLng(-3.7034, 115.5647),
    new google.maps.LatLng(-3.7036, 115.5649),
    new google.maps.LatLng(-3.7038, 115.5651),
    new google.maps.LatLng(-3.7040, 115.5653),
    new google.maps.LatLng(-3.7042, 115.5655),
    new google.maps.LatLng(-3.7044, 115.5657),
    new google.maps.LatLng(-3.7046, 115.5659),
    new google.maps.LatLng(-3.7215, 115.6370),
    new google.maps.LatLng(-3.7217, 115.6373),
    new google.maps.LatLng(-3.7219, 115.6376),
    new google.maps.LatLng(-3.7221, 115.6379),
    new google.maps.LatLng(-3.7223, 115.6382),
    new google.maps.LatLng(-3.7225, 115.6385),
    new google.maps.LatLng(-3.7228, 115.6388),
    new google.maps.LatLng(-3.7267, 115.6454),
    new google.maps.LatLng(-3.7280, 115.6454),
    new google.maps.LatLng(-3.7290, 115.6454),
    new google.maps.LatLng(-3.7100, 115.6454),
    new google.maps.LatLng(-3.7110, 115.6454),
    new google.maps.LatLng(-3.7120, 115.6454),
    new google.maps.LatLng(-3.7130, 115.6454),
    new google.maps.LatLng(-3.6331, 115.6540),
    new google.maps.LatLng(-3.6332, 115.6540),
    new google.maps.LatLng(-3.6333, 115.6540),
    new google.maps.LatLng(-3.6334, 115.6540),
    new google.maps.LatLng(-3.6345, 115.6540),
    new google.maps.LatLng(-3.5955, 115.6550),
    new google.maps.LatLng(-3.5966, 115.6550),
    new google.maps.LatLng(-3.5977, 115.6550),
    new google.maps.LatLng(-3.5988, 115.6550),
    new google.maps.LatLng(-3.5999, 115.6550),
    new google.maps.LatLng(-3.6010, 115.6550),
    new google.maps.LatLng(-3.6020, 115.6550),
    new google.maps.LatLng(-3.6030, 115.6550),
    new google.maps.LatLng(-3.6040, 115.6550),
    new google.maps.LatLng(-3.7382, 115.6451),
    new google.maps.LatLng(-3.7387, 115.6451),
    new google.maps.LatLng(-3.7391, 115.6451),
    new google.maps.LatLng(-3.7396, 115.6451),
    new google.maps.LatLng(-3.7399, 115.6451),
    new google.maps.LatLng(-3.7402, 115.6451),
    new google.maps.LatLng(-3.7404, 115.6451),
    new google.maps.LatLng(-3.7406, 115.6451),
    new google.maps.LatLng(-3.7411, 115.6451),
    new google.maps.LatLng(-3.7414, 115.6451),
    new google.maps.LatLng(-3.7415, 115.6451),
    new google.maps.LatLng(-3.7417, 115.6450),
    new google.maps.LatLng(-3.7415, 115.6450),
    new google.maps.LatLng(-3.7417, 115.6452),
    new google.maps.LatLng(-3.7419, 115.6452),
    new google.maps.LatLng(-3.7421, 115.6452),
    new google.maps.LatLng(-3.7423, 115.6452),
    new google.maps.LatLng(-3.7426, 115.6452),
    new google.maps.LatLng(-3.7426, 115.6452),
    new google.maps.LatLng(-3.7430, 115.6452),
    new google.maps.LatLng(-3.7433, 115.6452),
    new google.maps.LatLng(-3.7435, 115.6452),
    new google.maps.LatLng(-3.7438, 115.6451),
    new google.maps.LatLng(-3.7438, 115.64521),
    new google.maps.LatLng(-3.7440, 115.6452),
    new google.maps.LatLng(-3.7442, 115.6452),
    new google.maps.LatLng(-3.7444, 115.6452),
    new google.maps.LatLng(-3.7447, 115.6452),
    new google.maps.LatLng(-3.7449, 115.6452),
    new google.maps.LatLng(-3.7449, 115.6454),
    new google.maps.LatLng(-3.7449, 115.6456),
    new google.maps.LatLng(-3.7448, 115.6458),
    new google.maps.LatLng(-3.7448, 115.6460),
    new google.maps.LatLng(-3.7444, 115.6463),
    new google.maps.LatLng(-3.7443, 115.6465),
    new google.maps.LatLng(-3.7442, 115.6466),
    new google.maps.LatLng(-3.7440, 115.6465),
    new google.maps.LatLng(-3.7438, 115.6465),
    new google.maps.LatLng(-3.7438, 115.6463),
    new google.maps.LatLng(-3.7438, 115.6462),
    new google.maps.LatLng(-3.7438, 115.6461),
    new google.maps.LatLng(-3.7437, 115.6460),
    new google.maps.LatLng(-3.7437, 115.6458),
    new google.maps.LatLng(-3.7437, 115.6458),
    new google.maps.LatLng(-3.7437, 115.6457),
    new google.maps.LatLng(-3.7436, 115.6456),
    new google.maps.LatLng(-3.7436, 115.6455),
    new google.maps.LatLng(-3.7436, 115.6454),
    new google.maps.LatLng(-3.7436, 115.6454),
  ];
}

function addoverlay(map){
  console.log("masuk overlay");
  if (overlaystatus == 0) {
    console.log(map.getMapTypeId());
    map.setMapTypeId((map.getMapTypeId() === 'satellite') ? 'hidden' : google.maps.MapTypeId.satellite);

    overlaystatus = 1;
    // INI BUAT KOORDINAT PORTBUNATI3_SMALL.PNG

    // BIB MAPS UPDATE 18 12 2021
    var latlng_roma1_1     = new google.maps.LatLng(-3.524913, 115.634923);
    var latlng_roma1_2     = new google.maps.LatLng(-3.516356, 115.650076);
    var image_roma1        = "<?php echo base_url()?>assets/images/bibmaps/rom_a1.png";
    var image_latlng_roma1 = new google.maps.LatLngBounds(latlng_roma1_1, latlng_roma1_2);
    var overlay_roma1      = new google.maps.GroundOverlay(image_roma1, image_latlng_roma1);
    overlay_roma1.setMap(map);
    overlaysarray.push(overlay_roma1);

    var latlng_romb1_1      = new google.maps.LatLng(-3.605500, 115.620359);
    var latlng_romb1_2      = new google.maps.LatLng(-3.596698, 115.634840);
    var image_romb1         = "<?php echo base_url()?>assets/images/bibmaps/ROM-B1b.png";
    var image_latlng_romb1 = new google.maps.LatLngBounds(latlng_romb1_1, latlng_romb1_2);
    var overlay_romb1      = new google.maps.GroundOverlay(image_romb1, image_latlng_romb1);
    overlay_romb1.setMap(map);
    overlaysarray.push(overlay_romb1);
	
	var latlng_port1b_1      = new google.maps.LatLng(-3.751666, 115.643549);
    var latlng_port1b_2      = new google.maps.LatLng(-3.739291, 115.653206);
    var image_port1b         = "<?php echo base_url()?>assets/images/bibmaps/port_1b.png";
    var image_latlng_port1b = new google.maps.LatLngBounds(latlng_port1b_1, latlng_port1b_2);
    var overlay_port1b      = new google.maps.GroundOverlay(image_port1b, image_latlng_port1b);
    overlay_port1b.setMap(map);
    overlaysarray.push(overlay_port1b);
	
	var latlng_port2b_1      = new google.maps.LatLng(-3.751698, 115.633984);
    var latlng_port2b_2      = new google.maps.LatLng(-3.739304, 115.644362);
    var image_port2b         = "<?php echo base_url()?>assets/images/bibmaps/Port-2b.png";
    var image_latlng_port2b = new google.maps.LatLngBounds(latlng_port2b_1, latlng_port2b_2);
    var overlay_port2b      = new google.maps.GroundOverlay(image_port2b, image_latlng_port2b);
    overlay_port2b.setMap(map);
    overlaysarray.push(overlay_port2b);
	
	var latlng_port3b_1      = new google.maps.LatLng(-3.751607, 115.625526);
    var latlng_port3b_2      = new google.maps.LatLng(-3.739215, 115.634854);
    var image_port3b         = "<?php echo base_url()?>assets/images/bibmaps/Port-3b.png";
    var image_latlng_port3b = new google.maps.LatLngBounds(latlng_port3b_1, latlng_port3b_2);
    var overlay_port3b      = new google.maps.GroundOverlay(image_port3b, image_latlng_port3b);
    overlay_port3b.setMap(map);
    overlaysarray.push(overlay_port3b);
	
	var latlng_romb2_1      = new google.maps.LatLng(-3.575043, 115.626236);
    var latlng_romb2_2      = new google.maps.LatLng(-3.566604, 115.638872);
    var image_romb2         = "<?php echo base_url()?>assets/images/bibmaps/ROM-B2b.png";
    var image_latlng_romb2 = new google.maps.LatLngBounds(latlng_romb2_1, latlng_romb2_2);
    var overlay_romb2      = new google.maps.GroundOverlay(image_romb2, image_latlng_romb2);
    overlay_romb2.setMap(map);
    overlaysarray.push(overlay_romb2);
	
	var latlng_haulbaru12cb_1      = new google.maps.LatLng(-3.605164, 115.615232);
    var latlng_haulbaru12cb_2      = new google.maps.LatLng(-3.591731, 115.628002);
    var image_haulbaru12cb         = "<?php echo base_url()?>assets/images/bibmaps/haul-baru-12cb.png";
    var image_latlng_haulbaru12cb = new google.maps.LatLngBounds(latlng_haulbaru12cb_1, latlng_haulbaru12cb_2);
    var overlay_haulbaru12cb      = new google.maps.GroundOverlay(image_haulbaru12cb, image_latlng_haulbaru12cb);
    overlay_haulbaru12cb.setMap(map);
    overlaysarray.push(overlay_haulbaru12cb);
	
	var latlng_haulbaru12bb_1      = new google.maps.LatLng(-3.606645, 115.626917);
    var latlng_haulbaru12bb_2      = new google.maps.LatLng(-3.593165, 115.636931);
    var image_haulbaru12bb         = "<?php echo base_url()?>assets/images/bibmaps/haul-baru-12bb.png";
    var image_latlng_haulbaru12bb = new google.maps.LatLngBounds(latlng_haulbaru12bb_1, latlng_haulbaru12bb_2);
    var overlay_haulbaru12bb      = new google.maps.GroundOverlay(image_haulbaru12bb, image_latlng_haulbaru12bb);
    overlay_haulbaru12bb.setMap(map);
    overlaysarray.push(overlay_haulbaru12bb);
	
	var latlng_haulbaru1b_1      = new google.maps.LatLng(-3.740537, 115.644033);
    var latlng_haulbaru1b_2      = new google.maps.LatLng(-3.727921, 115.653558);
    var image_haulbaru1b         = "<?php echo base_url()?>assets/images/bibmaps/haul-baru-1b.png";
    var image_latlng_haulbaru1b = new google.maps.LatLngBounds(latlng_haulbaru1b_1, latlng_haulbaru1b_2);
    var overlay_haulbaru1b      = new google.maps.GroundOverlay(image_haulbaru1b, image_latlng_haulbaru1b);
    overlay_haulbaru1b.setMap(map);
    overlaysarray.push(overlay_haulbaru1b);
	
	var latlng_haulbaru2b_1      = new google.maps.LatLng(-3.728990, 115.642171);
    var latlng_haulbaru2b_2      = new google.maps.LatLng(-3.716184, 115.651708);
    var image_haulbaru2b         = "<?php echo base_url()?>assets/images/bibmaps/haul-baru-2b.png";
    var image_latlng_haulbaru2b = new google.maps.LatLngBounds(latlng_haulbaru2b_1, latlng_haulbaru2b_2);
    var overlay_haulbaru2b      = new google.maps.GroundOverlay(image_haulbaru2b, image_latlng_haulbaru2b);
    overlay_haulbaru2b.setMap(map);
    overlaysarray.push(overlay_haulbaru2b);
	
	var latlng_haulbaru3b_1      = new google.maps.LatLng(-3.717038, 115.638428);
    var latlng_haulbaru3b_2      = new google.maps.LatLng(-3.704264, 115.648521);
    var image_haulbaru3b         = "<?php echo base_url()?>assets/images/bibmaps/haul-baru-3b.png";
    var image_latlng_haulbaru3b = new google.maps.LatLngBounds(latlng_haulbaru3b_1, latlng_haulbaru3b_2);
    var overlay_haulbaru3b      = new google.maps.GroundOverlay(image_haulbaru3b, image_latlng_haulbaru3b);
    overlay_haulbaru3b.setMap(map);
    overlaysarray.push(overlay_haulbaru3b);
	
	var latlng_haulbaru4b_1      = new google.maps.LatLng(-3.705034, 115.638673);
    var latlng_haulbaru4b_2      = new google.maps.LatLng(-3.692296, 115.647988);
    var image_haulbaru4b         = "<?php echo base_url()?>assets/images/bibmaps/haul-baru-4b.png";
    var image_latlng_haulbaru4b = new google.maps.LatLngBounds(latlng_haulbaru4b_1, latlng_haulbaru4b_2);
    var overlay_haulbaru4b      = new google.maps.GroundOverlay(image_haulbaru4b, image_latlng_haulbaru4b);
    overlay_haulbaru4b.setMap(map);
    overlaysarray.push(overlay_haulbaru4b);
	
	var latlng_haulbaru5b_1      = new google.maps.LatLng(-3.693190, 115.640424);
    var latlng_haulbaru5b_2      = new google.maps.LatLng(-3.680628, 115.650006);
    var image_haulbaru5b         = "<?php echo base_url()?>assets/images/bibmaps/haul-baru-5b.png";
    var image_latlng_haulbaru5b = new google.maps.LatLngBounds(latlng_haulbaru5b_1, latlng_haulbaru5b_2);
    var overlay_haulbaru5b      = new google.maps.GroundOverlay(image_haulbaru5b, image_latlng_haulbaru5b);
    overlay_haulbaru5b.setMap(map);
    overlaysarray.push(overlay_haulbaru5b);
	
	var latlng_haulbaru6b_1      = new google.maps.LatLng(-3.681373, 115.643783);
    var latlng_haulbaru6b_2      = new google.maps.LatLng(-3.668517, 115.653257);
    var image_haulbaru6b         = "<?php echo base_url()?>assets/images/bibmaps/haul-baru-6b.png";
    var image_latlng_haulbaru6b = new google.maps.LatLngBounds(latlng_haulbaru6b_1, latlng_haulbaru6b_2);
    var overlay_haulbaru6b      = new google.maps.GroundOverlay(image_haulbaru6b, image_latlng_haulbaru6b);
    overlay_haulbaru6b.setMap(map);
    overlaysarray.push(overlay_haulbaru6b);
	
	var latlng_haulbaru7b_1      = new google.maps.LatLng(-3.669606, 115.641915);
    var latlng_haulbaru7b_2      = new google.maps.LatLng(-3.656844, 115.651896);
    var image_haulbaru7b         = "<?php echo base_url()?>assets/images/bibmaps/haul-baru-7b.png";
    var image_latlng_haulbaru7b = new google.maps.LatLngBounds(latlng_haulbaru7b_1, latlng_haulbaru7b_2);
    var overlay_haulbaru7b      = new google.maps.GroundOverlay(image_haulbaru7b, image_latlng_haulbaru7b);
    overlay_haulbaru7b.setMap(map);
    overlaysarray.push(overlay_haulbaru7b);
	
	var latlng_haulbaru8b_1      = new google.maps.LatLng(-3.657590, 115.641814);
    var latlng_haulbaru8b_2      = new google.maps.LatLng(-3.644907, 115.651758);
    var image_haulbaru8b         = "<?php echo base_url()?>assets/images/bibmaps/haul-baru-8b.png";
    var image_latlng_haulbaru8b = new google.maps.LatLngBounds(latlng_haulbaru8b_1, latlng_haulbaru8b_2);
    var overlay_haulbaru8b      = new google.maps.GroundOverlay(image_haulbaru8b, image_latlng_haulbaru8b);
    overlay_haulbaru8b.setMap(map);
    overlaysarray.push(overlay_haulbaru8b);
	
	var latlng_haulbaru9b_1      = new google.maps.LatLng(-3.646049, 115.641726);
    var latlng_haulbaru9b_2      = new google.maps.LatLng(-3.632953, 115.651698);
    var image_haulbaru9b         = "<?php echo base_url()?>assets/images/bibmaps/haul-baru-9b.png";
    var image_latlng_haulbaru9b = new google.maps.LatLngBounds(latlng_haulbaru9b_1, latlng_haulbaru9b_2);
    var overlay_haulbaru9b      = new google.maps.GroundOverlay(image_haulbaru9b, image_latlng_haulbaru9b);
    overlay_haulbaru9b.setMap(map);
    overlaysarray.push(overlay_haulbaru9b);
	
	var latlng_haulbaru10b_1      = new google.maps.LatLng(-3.641766, 115.649965);
    var latlng_haulbaru10b_2      = new google.maps.LatLng(-3.628476, 115.659876);
    var image_haulbaru10b         = "<?php echo base_url()?>assets/images/bibmaps/haul-baru-10b.png";
    var image_latlng_haulbaru10b = new google.maps.LatLngBounds(latlng_haulbaru10b_1, latlng_haulbaru10b_2);
    var overlay_haulbaru10b      = new google.maps.GroundOverlay(image_haulbaru10b, image_latlng_haulbaru10b);
    overlay_haulbaru10b.setMap(map);
    overlaysarray.push(overlay_haulbaru10b);
	
	var latlng_haulbaru11b_1      = new google.maps.LatLng(-3.629299, 115.646989);
    var latlng_haulbaru11b_2      = new google.maps.LatLng(-3.617203, 115.656511);
    var image_haulbaru11b         = "<?php echo base_url()?>assets/images/bibmaps/haul-baru-11b.png";
    var image_latlng_haulbaru11b = new google.maps.LatLngBounds(latlng_haulbaru11b_1, latlng_haulbaru11b_2);
    var overlay_haulbaru11b      = new google.maps.GroundOverlay(image_haulbaru11b, image_latlng_haulbaru11b);
    overlay_haulbaru11b.setMap(map);
    overlaysarray.push(overlay_haulbaru11b);
	
	var latlng_haulbaru12b_1      = new google.maps.LatLng(-3.617917, 115.644804);
    var latlng_haulbaru12b_2      = new google.maps.LatLng(-3.605271, 115.654404);
    var image_haulbaru12b         = "<?php echo base_url()?>assets/images/bibmaps/haul-baru-12b.png";
    var image_latlng_haulbaru12b = new google.maps.LatLngBounds(latlng_haulbaru12b_1, latlng_haulbaru12b_2);
    var overlay_haulbaru12b      = new google.maps.GroundOverlay(image_haulbaru12b, image_latlng_haulbaru12b);
    overlay_haulbaru12b.setMap(map);
    overlaysarray.push(overlay_haulbaru12b);
	
	var latlng_haulbaru12ab_1      = new google.maps.LatLng(-3.611290, 115.636175);
    var latlng_haulbaru12ab_2      = new google.maps.LatLng(-3.600336, 115.645520);
    var image_haulbaru12ab         = "<?php echo base_url()?>assets/images/bibmaps/haul-baru-12ab.png";
    var image_latlng_haulbaru12ab = new google.maps.LatLngBounds(latlng_haulbaru12ab_1, latlng_haulbaru12ab_2);
    var overlay_haulbaru12ab      = new google.maps.GroundOverlay(image_haulbaru12ab, image_latlng_haulbaru12ab);
    overlay_haulbaru12ab.setMap(map);
    overlaysarray.push(overlay_haulbaru12ab);
	
	var latlng_haulbaru13b_1      = new google.maps.LatLng(-3.610264, 115.649310);
    var latlng_haulbaru13b_2      = new google.maps.LatLng(-3.597485, 115.659609);
    var image_haulbaru13b         = "<?php echo base_url()?>assets/images/bibmaps/haul-road-13b.png";
    var image_latlng_haulbaru13b = new google.maps.LatLngBounds(latlng_haulbaru13b_1, latlng_haulbaru13b_2);
    var overlay_haulbaru13b      = new google.maps.GroundOverlay(image_haulbaru13b, image_latlng_haulbaru13b);
    overlay_haulbaru13b.setMap(map);
    overlaysarray.push(overlay_haulbaru13b);
	
	var latlng_haulbaru13b_1      = new google.maps.LatLng(-3.610296, 115.649353);
    var latlng_haulbaru13b_2      = new google.maps.LatLng(-3.597541, 115.659512);
    var image_haulbaru13b         = "<?php echo base_url()?>assets/images/bibmaps/haul-road-13b.png";
    var image_latlng_haulbaru13b = new google.maps.LatLngBounds(latlng_haulbaru13b_1, latlng_haulbaru13b_2);
    var overlay_haulbaru13b      = new google.maps.GroundOverlay(image_haulbaru13b, image_latlng_haulbaru13b);
    overlay_haulbaru13b.setMap(map);
    overlaysarray.push(overlay_haulbaru13b);
	
	var latlng_haulbaru14b_1      = new google.maps.LatLng(-3.598286, 115.649202);
    var latlng_haulbaru14b_2      = new google.maps.LatLng(-3.585498, 115.660162);
    var image_haulbaru14b         = "<?php echo base_url()?>assets/images/bibmaps/haul-road-14b.png";
    var image_latlng_haulbaru14b = new google.maps.LatLngBounds(latlng_haulbaru14b_1, latlng_haulbaru14b_2);
    var overlay_haulbaru14b      = new google.maps.GroundOverlay(image_haulbaru14b, image_latlng_haulbaru14b);
    overlay_haulbaru14b.setMap(map);
    overlaysarray.push(overlay_haulbaru14b);
	
	var latlng_haulbaru15b_1      = new google.maps.LatLng(-3.586371, 115.649267);
    var latlng_haulbaru15b_2      = new google.maps.LatLng(-3.573542, 115.659822);
    var image_haulbaru15b         = "<?php echo base_url()?>assets/images/bibmaps/haul-road-15b.png";
    var image_latlng_haulbaru15b = new google.maps.LatLngBounds(latlng_haulbaru15b_1, latlng_haulbaru15b_2);
    var overlay_haulbaru15b      = new google.maps.GroundOverlay(image_haulbaru15b, image_latlng_haulbaru15b);
    overlay_haulbaru15b.setMap(map);
    overlaysarray.push(overlay_haulbaru15b);
	
	var latlng_haulbaru16b_1      = new google.maps.LatLng(-3.574349, 115.649297);
    var latlng_haulbaru16b_2      = new google.maps.LatLng(-3.561528, 115.659364);
    var image_haulbaru16b         = "<?php echo base_url()?>assets/images/bibmaps/haul-road-16b.png";
    var image_latlng_haulbaru16b = new google.maps.LatLngBounds(latlng_haulbaru16b_1, latlng_haulbaru16b_2);
    var overlay_haulbaru16b      = new google.maps.GroundOverlay(image_haulbaru16b, image_latlng_haulbaru16b);
    overlay_haulbaru16b.setMap(map);
    overlaysarray.push(overlay_haulbaru16b);
	
	var latlng_haulbaru16ab_1      = new google.maps.LatLng(-3.574005, 115.640451);
    var latlng_haulbaru16ab_2      = new google.maps.LatLng(-3.561480, 115.650092);
    var image_haulbaru16ab         = "<?php echo base_url()?>assets/images/bibmaps/haul-road-16ab.png";
    var image_latlng_haulbaru16ab = new google.maps.LatLngBounds(latlng_haulbaru16ab_1, latlng_haulbaru16ab_2);
    var overlay_haulbaru16ab      = new google.maps.GroundOverlay(image_haulbaru16ab, image_latlng_haulbaru16ab);
    overlay_haulbaru16ab.setMap(map);
    overlaysarray.push(overlay_haulbaru16ab);
	
	var latlng_haulbaru16bb_1      = new google.maps.LatLng(-3.573718, 115.631792);
    var latlng_haulbaru16bb_2      = new google.maps.LatLng(-3.561473, 115.641361);
    var image_haulbaru16bb         = "<?php echo base_url()?>assets/images/bibmaps/haul-road-16bb.png";
    var image_latlng_haulbaru16bb = new google.maps.LatLngBounds(latlng_haulbaru16bb_1, latlng_haulbaru16bb_2);
    var overlay_haulbaru16bb      = new google.maps.GroundOverlay(image_haulbaru16bb, image_latlng_haulbaru16bb);
    overlay_haulbaru16bb.setMap(map);
    overlaysarray.push(overlay_haulbaru16bb);
	
	var latlng_haulbaru16cb_1      = new google.maps.LatLng(-3.573699, 115.626143);
    var latlng_haulbaru16cb_2      = new google.maps.LatLng(-3.561356, 115.632515);
    var image_haulbaru16cb         = "<?php echo base_url()?>assets/images/bibmaps/haul-road-16cb.png";
    var image_latlng_haulbaru16cb = new google.maps.LatLngBounds(latlng_haulbaru16cb_1, latlng_haulbaru16cb_2);
    var overlay_haulbaru16cb      = new google.maps.GroundOverlay(image_haulbaru16cb, image_latlng_haulbaru16cb);
    overlay_haulbaru16cb.setMap(map);
    overlaysarray.push(overlay_haulbaru16cb);
	
	var latlng_haulbaru17b_1      = new google.maps.LatLng(-3.563110, 115.645952);
    var latlng_haulbaru17b_2      = new google.maps.LatLng(-3.550431, 115.655632);
    var image_haulbaru17b         = "<?php echo base_url()?>assets/images/bibmaps/haul-road-17b.png";
    var image_latlng_haulbaru17b = new google.maps.LatLngBounds(latlng_haulbaru17b_1, latlng_haulbaru17b_2);
    var overlay_haulbaru17b      = new google.maps.GroundOverlay(image_haulbaru17b, image_latlng_haulbaru17b);
    overlay_haulbaru17b.setMap(map);
    overlaysarray.push(overlay_haulbaru17b);
	
	var latlng_haulbaru18b_1      = new google.maps.LatLng(-3.551394, 115.646235);
    var latlng_haulbaru18b_2      = new google.maps.LatLng(-3.537727, 115.655472);
    var image_haulbaru18b         = "<?php echo base_url()?>assets/images/bibmaps/haul-road-18b.png";
    var image_latlng_haulbaru18b = new google.maps.LatLngBounds(latlng_haulbaru18b_1, latlng_haulbaru18b_2);
    var overlay_haulbaru18b      = new google.maps.GroundOverlay(image_haulbaru18b, image_latlng_haulbaru18b);
    overlay_haulbaru18b.setMap(map);
    overlaysarray.push(overlay_haulbaru18b);
	
	var latlng_haulbaru19ab_1      = new google.maps.LatLng(-3.528162, 115.641481);
    var latlng_haulbaru19ab_2      = new google.maps.LatLng(-3.515174, 115.651329);
    var image_haulbaru19ab         = "<?php echo base_url()?>assets/images/bibmaps/haul-road-19ab.png";
    var image_latlng_haulbaru19ab = new google.maps.LatLngBounds(latlng_haulbaru19ab_1, latlng_haulbaru19ab_2);
    var overlay_haulbaru19ab      = new google.maps.GroundOverlay(image_haulbaru19ab, image_latlng_haulbaru19ab);
    overlay_haulbaru19ab.setMap(map);
    overlaysarray.push(overlay_haulbaru19ab);
	
	var latlng_haulbaru19b_1      = new google.maps.LatLng(-3.538697, 115.644070);
    var latlng_haulbaru19b_2      = new google.maps.LatLng(-3.526777, 115.653758);
    var image_haulbaru19b         = "<?php echo base_url()?>assets/images/bibmaps/haul-road-19b.png";
    var image_latlng_haulbaru19b = new google.maps.LatLngBounds(latlng_haulbaru19b_1, latlng_haulbaru19b_2);
    var overlay_haulbaru19b      = new google.maps.GroundOverlay(image_haulbaru19b, image_latlng_haulbaru19b);
    overlay_haulbaru19b.setMap(map);
    overlaysarray.push(overlay_haulbaru19b);
	
	var latlng_haulbaru20b_1      = new google.maps.LatLng(-3.531550, 115.636986);
    var latlng_haulbaru20b_2      = new google.maps.LatLng(-3.519104, 115.646871);
    var image_haulbaru20b         = "<?php echo base_url()?>assets/images/bibmaps/haul-road-20b.png";
    var image_latlng_haulbaru20b = new google.maps.LatLngBounds(latlng_haulbaru20b_1, latlng_haulbaru20b_2);
    var overlay_haulbaru20b      = new google.maps.GroundOverlay(image_haulbaru20b, image_latlng_haulbaru20b);
    overlay_haulbaru20b.setMap(map);
    overlaysarray.push(overlay_haulbaru20b);
	
	var latlng_haulbaru21b_1      = new google.maps.LatLng(-3.524798, 115.635698);
    var latlng_haulbaru21b_2      = new google.maps.LatLng(-3.511573, 115.645818);
    var image_haulbaru21b         = "<?php echo base_url()?>assets/images/bibmaps/haul-road-21b.png";
    var image_latlng_haulbaru21b = new google.maps.LatLngBounds(latlng_haulbaru21b_1, latlng_haulbaru21b_2);
    var overlay_haulbaru21b      = new google.maps.GroundOverlay(image_haulbaru21b, image_latlng_haulbaru21b);
    overlay_haulbaru21b.setMap(map);
    overlaysarray.push(overlay_haulbaru21b);
	
	var latlng_roma2_1     = new google.maps.LatLng(-3.520902, 115.623965);
    var latlng_roma2_2     = new google.maps.LatLng(-3.511866, 115.638778);
    var image_roma2        = "<?php echo base_url()?>assets/images/bibmaps/rom_a2.png";
    var image_latlng_roma2 = new google.maps.LatLngBounds(latlng_roma2_1, latlng_roma2_2);
    var overlay_roma2      = new google.maps.GroundOverlay(image_roma2, image_latlng_roma2);
    overlay_roma2.setMap(map);
    overlaysarray.push(overlay_roma2);
	
	
	
    // BIB MAPS UPDATE 18 12 2021
  }else {
    console.log(map.getMapTypeId());
    map.setMapTypeId((map.getMapTypeId() === 'hidden') ? google.maps.MapTypeId.satellite : 'satellite');

    clearOverlays();
    overlaystatus = 0;
  }
}

function clearOverlays() {
 while(overlaysarray.length) {
   overlaysarray.pop().setMap(null);
 }
  overlaysarray.length = 0;
}

// REALTIME ALERT START
var realtimealertarray        = [];
var realtimealertarraysummary = [];
var userid                    = '<?php echo $this->sess->user_id?>';
var intervalalert2, intervalalert1, intervalalert;
var soundisactive             = 0; // VALUE DEFAULTNYA == 1. GANTI JADI 1 KALAU MAU DIBUNYIKAN
var alertloop                 = 0;

var intervalalert = setInterval(dataalert, 25000);
var lasttimealert = 0;
var limitalert    = 10;

function dataalert(){
  $.post("<?php echo base_url() ?>securityevidence/realtimealert", {lasttimealert : lasttimealert, limitalert : limitalert}, function(response){
    // localStorage.setItem(obj[looplasttime].vehicle_device, obj[looplasttime].auto_last_update);
    console.log("response : ", response);
    if (response.code == 200) {
      // console.log("array length before : ", realtimealertarraysummary.length);

      if (realtimealertarraysummary.length >= limitalert) {
        realtimealertarraysummary.shift();
        // realtimealertarraysummary.slice(Math.max(realtimealertarraysummary.length - limitalert, 0))
        // realtimealertarraysummary = [];
      }
      // console.log("array length after : ", realtimealertarraysummary.length);

      var data = response.data;

      for (var i = 0; i < data.length; i++) {
        var positionalert   = data[i].position.includes("KM");
        var gps_speed       = data[i].gps_speed;
        var gps_speed_limit = data[i].gps_speed_limit;

          if (positionalert) {
            if (gps_speed_limit > 0) {
              if (gps_speed >= gps_speed_limit) {
                realtimealertarraysummary.push(data[i]);
              }
            }
          }
      }

      var realtimealertarraysummaryreverse       = realtimealertarraysummary.reverse();

      var htmlsummary = "";
      for (var i = 0; i < realtimealertarraysummaryreverse.length-1; i++) {
        // htmlsummary += '<span style="font-size:12px; color:green;">'+ realtimealertarraysummaryreverse[i].vehicle_no +'</span> <span style="font-size:12px; color:red;">'+ realtimealertarraysummaryreverse[i].gps_alert +'</span> <span style="font-size:12px;">'+ realtimealertarraysummaryreverse[i].gps_time +'</span> <span style="font-size:12px; color:black;">'+ 'Geofence : '+ realtimealertarraysummaryreverse[i].geofence +'</span> <a style="font-size:12px;" href="http://maps.google.com/maps?z=12&t=m&q=loc'+realtimealertarraysummaryreverse[i].gps_latitude_real+','+realtimealertarraysummaryreverse[i].gps_longitude_real+'" target="_blank">'+ " "+realtimealertarraysummaryreverse[i].position +' </a> <span style="font-size:12px; color:black;">'+ 'Speed : '+ realtimealertarraysummaryreverse[i].gps_speed +' Kph </span> <span style="font-size:12px; color:red;">'+ 'Limit : '+ realtimealertarraysummaryreverse[i].gps_speed_limit +' Kph </span> </br>';

        htmlsummary += '<span style="font-size:11px; color:green;">'+ realtimealertarraysummaryreverse[i].vehicle_no +'</span> <span style="font-size:11px; color:red;">'+ realtimealertarraysummaryreverse[i].gps_alert +'</span> <span style="font-size:11px;">'+ realtimealertarraysummaryreverse[i].gps_time +'</span> </br> <a style="font-size:11px;" href="http://maps.google.com/maps?z=12&t=m&q=loc'+realtimealertarraysummaryreverse[i].gps_latitude_real+','+realtimealertarraysummaryreverse[i].gps_longitude_real+'" target="_blank">'+ " "+realtimealertarraysummaryreverse[i].position +' </a> <span style="font-size:11px; color:black;">'+ 'Speed : '+ realtimealertarraysummaryreverse[i].gps_speed +
        ' Kph </span> <span style="font-size:11px; color:red;">'+ 'Limit : '+ realtimealertarraysummaryreverse[i].gps_speed_limit +' Kph </span> '+
        '<span style="font-size:11px; color:black;">'+ realtimealertarraysummaryreverse[i].jalur_name +'</span> </br><hr>';
      }
      $("#summaryalertcontent").html(htmlsummary);

      // var alertsimultan = "";
      // if (data[0].datasimultan == "yes") {
      //   // alertsimultan += '<span style="font-size:12px; color:green;">'+ data[0].vehicle_no +'</span> <span style="font-size:12px; color:red;">'+ data[0].gps_alert +'</span> <span style="font-size:12px;">'+ data[0].gps_time +'</span> <span style="font-size:12px; color:black;">'+ 'Geofence : '+ data[0].geofence +'</span> <a style="font-size:12px;" href="http://maps.google.com/maps?z=12&t=m&q=loc'+data[0].gps_latitude_real+','+data[0].gps_longitude_real+'" target="_blank">'+ " "+data[0].position +' </a> <span style="font-size:12px; color:black;">'+ 'Speed : '+ data[0].gps_speed +' Kph </span> <span style="font-size:12px; color:red;">'+ 'Limit : '+ data[0].gps_speed_limit +' Kph </span> </br>';
      //
      //   alertsimultan += '<span style="font-size:12px; color:green;">'+ data[0].vehicle_no +'</span> <span style="font-size:12px; color:red;">'+ data[0].gps_alert +'</span> <span style="font-size:12px;">'+ data[0].gps_time +'</span> <a style="font-size:12px;" href="http://maps.google.com/maps?z=12&t=m&q=loc'+data[0].gps_latitude_real+','+data[0].gps_longitude_real+'" target="_blank">'+ " "+data[0].position +' </a> <span style="font-size:12px; color:black;">'+ 'Speed : '+ data[0].gps_speed +' Kph </span> <span style="font-size:12px; color:red;">'+ 'Limit : '+ data[0].gps_speed_limit +' Kph </span> </br>';
      //
      //   $("#newalertcontent").html(alertsimultan);
      //   $("#newalertcontent").show();
      // }else {
      //   $("#newalertcontent").hide();
      // }

      lasttimealert = data[0].gps_time;
      if (soundisactive == 1) {
        playsound();
      }
    }
  }, "json");

}



function getalertnowsample(devid){
    var sampledataalert = [
      {
        gps_geofence: "Hauling 19 Road 23/27",
        gps_speed: "31",
        gps_speed_limit: "30",
        gps_speed_status: "1",
        position: "KM 25",
        vehicle_alert: "Overspeed",
        vehicle_alert_datetime: "2021-10-25 13:47:12",
        vehicle_alert_time: "13:47",
        vehicle_lat: "-3.5216",
        vehicle_lng: "115.6481",
        vehicle_name: "Hino 500",
        vehicle_no: "MKS 179"
      },
    ];

    var sampledataalert2 = [
      {
        gps_geofence: "Hauling 19 Road 23/27",
        gps_speed: "31",
        gps_speed_limit: "30",
        gps_speed_status: "1",
        position: "KM 10",
        vehicle_alert: "Overspeed",
        vehicle_alert_datetime: "2021-10-25 13:50:12",
        vehicle_alert_time: "13:50",
        vehicle_lat: "-3.5216",
        vehicle_lng: "115.6481",
        vehicle_name: "Hino 500",
        vehicle_no: "BKA 101"
      },
    ];

    // if (realtimealertarraysummary.length % 2 == 0) {
    //   realtimealertarraysummary.push(sampledataalert[0]);
    //   console.log("sikon 1");
    // }else {
    //   realtimealertarraysummary.push(sampledataalert2[0]);
    //   console.log("sikon 2");
    // }
    realtimealertarraysummary.push(sampledataalert[0]);

    var reversearraysummary = realtimealertarraysummary.reverse();

    // console.log("reversearraysummary : ", reversearraysummary);

    var htmlsummary = "";
    for (var i = 0; i < reversearraysummary.length; i++) {
      htmlsummary += '<span style="font-size:12px; color:green;">'+ reversearraysummary[0].vehicle_no +'</span> <span style="font-size:12px; color:red;">'+ reversearraysummary[0].vehicle_alert +'</span> <span style="font-size:12px;">'+ reversearraysummary[0].vehicle_alert_time +'</span> <a style="font-size:12px;" href="http://maps.google.com/maps?z=12&t=m&q=loc'+"-302321"+','+"1000023123"+'" target="_blank">'+ " "+reversearraysummary[i].position +' </a> <span style="font-size:12px; color:red;">'+ 'Limit : '+ reversearraysummary[0].gps_speed_limit +'</span> </br>';
    }
    $("#summaryalertcontent").html(htmlsummary);

    if (realtimealertarraysummary.length >= limitalert) {
      realtimealertarraysummary = [];
    }

    // if (soundisactive == 1) {
    //   playsound();
    // }
}


function closemodalalertrealtime(){
  // $("#modalalertrealtime").hide();
  $("#realtimealertshow").hide();
}

function alertsummary(){
  $("#modalalertsummry").show();
}

function closemodalsummaryalert(){
  $("#modalalertsummry").hide();
}

function playsound() {
  var audio = new Audio('<?php echo base_url() ?>assets/sounds/alert1.mp3');
    audio.play();
  // var sound = document.getElementById(soundObj);
  // sound.Play();
}

function activatesound(){
  if (soundisactive == 1) {
    soundisactive = 0;
    $("#activatesound2").show();
    $("#activatesound").hide();
  }else {
    soundisactive = 1;
    $("#activatesound2").hide();
    $("#activatesound").show();
  }
}

function activatesound2(){
  if (soundisactive == 1) {
    soundisactive = 0;
    $("#activatesound2").show();
    $("#activatesound").hide();
  }else {
    soundisactive = 1;
    $("#activatesound2").hide();
    $("#activatesound").show();
  }
}

function changelimit(){
  var limit = $("#changelimitrealtimalert").val();
    if (limit == 10) {
      limitalert = 10;
    }else if (limit == 20) {
      limitalert = 20;
    }else {
      limitalert = 30;
    }
}

var infowindowkedua, infowindow, infowindow2, infowindowonsimultan;
function forsearchinput(){
  var deviceid = $("#searchnopol").val();
    if (deviceid == 0) {
      alert("Silahkan pilih kendaraan terlebih dahulu");
    }else {
      console.log("device id forsearchinput : ", deviceid);

      var data = {key : deviceid};

      // if (infowindowkedua) {
      //     infowindowkedua.close();
      // }
      //
      // if (infowindow) {
      //     infowindow.close();
      // }
      //
      // if (infowindow2) {
      //     infowindow2.close();
      // }

      $.post("<?php echo base_url() ?>maps/forsearchvehicle", data, function(response){
        console.log("ini respon pencarian : ", response);
        if (response.code == 400) {
          alert("Data tidak ditemukan");
        }else {
          // DEVICE STATUS (CAMERA ONLINE / OFFLINE)
          if (response[0].devicestatusfixnya) {
            // console.log("devicestatus ada : ");
            if (response[0].devicestatusfixnya == 1) {
              var devicestatus = "Camera : Online <br>" ;
            }else {
              var devicestatus = "Camera : Offline <br>" ;
            }
          }else {
            if (response[0].devicestatusfixnya == "") {
              var devicestatus = "";
            }else if (response[0].devicestatusfixnya == 0) {
              var devicestatus = "Camera : Offline </br>";
            }else {
              var devicestatus = "";
            }
          }

          if (response[0].drivername) {
            var drivername = response[0].drivername;
           if (response[0].driverimage) {
             console.log("sikon 1");
             if (response[0].driverimage != 0) {
               console.log("sikon 2");
               // var showdriver   = "<a href='#' onclick='getmodaldriver("+datadriver[0]+");'>"+ datadriver[1] +"</a>";
               var detaildriver = '<img src="<?php echo base_url().$this->config->item("dir_photo");?>'+response[0].driverimage+'" width="100px;" height="100px;">';
             }else {
               var detaildriver = drivername + ' </br> No Driver Image';
             }
           }else {
             var detaildriver = drivername + ' </br> No Driver Image';
           }
          }

          // console.log("devicestatus : ", response[0].devicestatusfixnya);
          // console.log("devicestatus : ", devicestatus);

          var center = {lat : parseFloat(response[0].auto_last_lat), lng: parseFloat(response[0].auto_last_long)};
          // console.log("center : ", center);
          var num         = Number(response[0].auto_last_speed);
          var roundstring = num.toFixed(0);
          var rounded     = Number(roundstring);

          var addresssplit = response[0].auto_last_position.split(" ");
          var inarea       = response[0].auto_last_position.split(",");
          // console.log("addresssplit : ", addresssplit);

          var addressfix = bibarea.includes(addresssplit[0]);
          if (addressfix) {
            var addressfix = inarea[0];
          }else {
            var addressfix = response[0].auto_last_position;
          }

          titlemarker = "";
          titlemarker += '<table class="table" style="font-size:12px;">';
            titlemarker += '<tr>';
              titlemarker += '<td>'+detaildriver+'</td>';
              titlemarker += '<td>';
                titlemarker += response[0].vehicle_no + ' - ' + response[0].vehicle_name +'</br>';
                titlemarker += 'Driver : ' + drivername +'</br>';
                titlemarker += 'Gps Time : ' + response[0].auto_last_update+ '</br>';
                titlemarker += 'Position : ' + addressfix + '</br>';
                titlemarker += 'Coord : ' + response[0].auto_last_lat + ", " + response[0].auto_last_long + '</br>';
                titlemarker += 'Engine : ' + response[0].auto_last_engine + '</br>';
                titlemarker += 'Fuel : ' + response[0].auto_last_mvd + ' Ltr</br>';
                titlemarker += 'Speed : ' + rounded + ' kph </br>';
                titlemarker +=  devicestatus;
                titlemarker += 'Ritase : ' + response[0].auto_last_ritase + '</br>';
                // titlemarker += '<a href="<?php echo base_url()?>maps/tracking/"' + response[0].vehicle_id + '"target="_blank">Tracking</a> </br>';
                // titlemarker +=   lct + imglct;
              titlemarker += '</td>';
            titlemarker += '</tr>';
          titlemarker += '</table>';

          // var string = response[0].vehicle_no + ' - ' + response[0].vehicle_name + "<br>" +
          //   "GPS Time : " + response[0].auto_last_update + "<br>Position : " + response[0].auto_last_position + "<br>Coord : " + response[0].auto_last_lat + ", " + response[0].auto_last_long + "<br>" +
          //   "Engine : " + response[0].auto_last_engine + "<br>" +
          //   "Speed : " + rounded + " kph </br>"+ devicestatus + "Ritase : " + response[0].auto_last_ritase + "</br>" +
          //   "<a href='<?php echo base_url()?>maps/tracking/" + response[0].vehicle_id + "' target='_blank'>Tracking</a>";

           infowindowkedua = new google.maps.InfoWindow({
            content: titlemarker,
            maxWidth: 300
          });
          DeleteMarkers(response[0].vehicle_device);
          DeleteMarkerspertama(response[0].vehicle_device);


          if (response[0].auto_last_road == "muatan") {
            laststatus = 'GPS Online';
            laststatus2 = '<h5 class="text-medium full-width"><span class="label label-sm label-success">GPS Online</span></h5>';
            if (rounded == 0 && response[0].auto_last_engine == "ON") {
              // console.log("muatan : sikon 1");
              // ICON UNGU
              var icon = {
                // url: "<?php echo base_url()?>assets/images/car_biru/car1.png", // url
                path: car,
                scale: .5,
                // anchor: new google.maps.Point(25,10),
                // scaledSize: new google.maps.Size(30,20),
                strokeColor: 'white',
                strokeWeight: .10,
                fillOpacity: 1,
                fillColor: '#ffff00',
                offset: '5%'
              };
            }else if (rounded > 0 && response[0].auto_last_engine == "ON") {
              // console.log("muatan : sikon 2");
              laststatus = 'GPS Online';
              laststatus2 = '<h5 class="text-medium full-width"><span class="label label-sm label-success">GPS Online</span></h5>';
              // ICON HIJAU
              var icon = {
                // url: "<?php echo base_url()?>assets/images/car_biru/car1.png", // url
                path: car,
                scale: .5,
                // anchor: new google.maps.Point(25,10),
                // scaledSize: new google.maps.Size(30,20),
                strokeColor: 'white',
                strokeWeight: .10,
                fillOpacity: 1,
                fillColor: '#00b300',
                offset: '5%'
              };
            }else {
              // console.log("muatan : sikon 3");
              laststatus = 'GPS Online';
              laststatus2 = '<h5 class="text-medium full-width"><span class="label label-sm label-success">GPS Online</span></h5>';
              // ICON BIRU
              var icon = {
                // url: "<?php echo base_url()?>assets/images/car_biru/car1.png", // url
                path: car,
                scale: .5,
                // anchor: new google.maps.Point(25,10),
                // scaledSize: new google.maps.Size(30,20),
                strokeColor: 'white',
                strokeWeight: .10,
                fillOpacity: 1,
                fillColor: '#ff0040',
                offset: '5%'
              };
            }
          }else {
            laststatus = 'GPS Online';
            laststatus2 = '<h5 class="text-medium full-width"><span class="label label-sm label-success">GPS Online</span></h5>';
            if (rounded > 0 && response[0].auto_last_engine == "ON") {
              // console.log("kosongan : sikon 1");
              // ICON HIJAU
              var icon = {
                // url: "<?php echo base_url()?>assets/images/car_biru/car1.png", // url
                path: car,
                scale: .5,
                // anchor: new google.maps.Point(25,10),
                // scaledSize: new google.maps.Size(30,20),
                strokeColor: 'white',
                strokeWeight: .10,
                fillOpacity: 1,
                fillColor: '#0000FF',
                offset: '5%'
              };
            }else if (rounded == 0 && response[0].auto_last_engine == "ON") {
              // console.log("kosongan : sikon 2");
              laststatus = 'GPS Online';
              laststatus2 = '<h5 class="text-medium full-width"><span class="label label-sm label-success">GPS Online</span></h5>';
              // ICON UNGU
              var icon = {
                // url: "<?php echo base_url()?>assets/images/car_biru/car1.png", // url
                path: car,
                scale: .5,
                // anchor: new google.maps.Point(25,10),
                // scaledSize: new google.maps.Size(30,20),
                strokeColor: 'white',
                strokeWeight: .10,
                fillOpacity: 1,
                fillColor: '#ffff00',
                offset: '5%'
              };
            }else {
              // console.log("kosongan : sikon 3");
              // ICON BIRU
              var icon = {
                // url: "<?php echo base_url()?>assets/images/car_biru/car1.png", // url
                path: car,
                scale: .5,
                // anchor: new google.maps.Point(25,10),
                // scaledSize: new google.maps.Size(30,20),
                strokeColor: 'white',
                strokeWeight: .10,
                fillOpacity: 1,
                fillColor: '#ff0040',
                offset: '5%'
              };
            }
          }

          // showmapmode();

          markernya = new google.maps.Marker({
            map: map,
            icon: icon,
            position: new google.maps.LatLng(parseFloat(response[0].auto_last_lat), parseFloat(response[0].auto_last_long)),
            title: response[0].vehicle_no,
            // + ' - ' + value.vehicle_name + value.driver + "\n" +
            //   "GPS Time : " + value.gps.gps_date_fmt + " " + value.gps.gps_time_fmt + "\n" + value.gps.georeverse.display_name + "\n" + value.gps.gps_latitude_real_fmt + ", " + value.gps.gps_longitude_real_fmt + "\n" +
            //   "Speed : " + value.gps.gps_speed + " kph",
            id: response[0].vehicle_device
          });
          markerss.push(markernya);
          icon.rotation = Math.ceil(response[0].auto_last_course);
          markernya.setIcon(icon);


          // map.setZoom(18);
          infowindowkedua.open(map, markernya);
          map.setCenter(center);
          markernya.setPosition(center);

          // ON HOVER START
          google.maps.event.addListener(markernya, 'mouseover', function(){
            var varthis = this;
            setTimeoutConst = setTimeout(function() {
              console.log("mouseover 2second on search key");
                infowindowonsimultan = new google.maps.InfoWindow({
                  content: titlemarker,
                  maxWidth: 300
                });

               infowindowonsimultan.setContent(titlemarker);
               infowindowonsimultan.open(map, varthis);
            }, 2000);
          });

          // assuming you also want to hide the infowindow when user mouses-out
          google.maps.event.addListener(markernya, 'mouseout', function(){
            console.log("mouseout on search key");
            clearTimeout(setTimeoutConst);
              infowindowonsimultan.close();
          });
          // ON HOVER END

          google.maps.event.addListener(markernya, 'click', function(evt){
            console.log("icon map di klik from search key");
            // infowindow2.close();
            // infowindowkedua.close();
            // infowindow.close();

            var num         = Number(response[0].auto_last_speed);
            var roundstring = num.toFixed(0);
            var rounded     = Number(roundstring);

            // var string = response[0].vehicle_no + ' - ' + response[0].vehicle_name + "<br>" +
            //   "GPS Time : " + response[0].auto_last_update + "<br>Position : " + response[0].auto_last_position + "<br>Coord : " + response[0].auto_last_lat + ", " + response[0].auto_last_long + "<br>" +
            //   "Engine : " + response[0].auto_last_engine + "<br>" +
            //   "Speed : " + rounded + " kph" + "<br>" +
            //   "<a href='<?php echo base_url()?>maps/tracking/" + response[0].vehicle_id + "' target='_blank'>Tracking</a>";

             infowindowkedua = new google.maps.InfoWindow({
              content: titlemarker,
              maxWidth: 300
            });
            // DeleteMarkers(response[0].vehicle_device);
            // DeleteMarkerspertama(response[0].vehicle_device);

              var center = {lat : parseFloat(response[0].auto_last_lat), lng: parseFloat(response[0].auto_last_long)};
              infowindowkedua.setContent(titlemarker);
              map.setCenter(markernya.position);
              markernya.setPosition(markernya.position);
              infowindowkedua.open(map, this);
          });
        }
      }, "json");
      $("#mapShow").show();
      $("#heatmapbutton").hide();
      $("#realtimealertshowhide").show();
      $("#tableShowMuatan").hide();
      $("#tableShowKosongan").hide();
      $("#tableShowPort").hide();
      $("#tableShowPool").hide();
      $("#tableShowOutOfHauling").hide();
      $("#tableShowRom").hide();
      $("#valueMode").val(0);
    }
}

function DeleteMarkers(id) {
  //Loop through all the markers and remove
  // console.log("marker pertama id yg dihapus : ", id);
  for (var i = 0; i < markerss.length; i++) {
    if (markerss[i].id == id) {
      //Remove the om Map
      markerss[i].setMap(null);

      //Remove the marker from array.
      markerss.splice(i, 1);
      return;
    }
  }
}

function DeleteMarkerspertama(id) {
  //Loop through all the markers and remove
  // console.log("marker kedua id yg dihapus : ", id);
  for (var i = 0; i < markers.length; i++) {
    if (markers[i].id == id) {
      //Remove the marker from Map
      markers[i].setMap(null);

      //Remove the marker from array.
      markers.splice(i, 1);
      return;
    }
  }
}

function mapsOptions(){
  var mapsOptionsValue = $("#mapsOptions").val();
  console.log("mapsOptionsValue : ", mapsOptionsValue);
    if (mapsOptionsValue == "showHeatmap") {
      showHeatmap();
    }else if (mapsOptionsValue == "showTableMuatan1") {
      showTableMuatan(0);
    }else if (mapsOptionsValue == "showTableMuatan2") {
      showTableMuatan(1);
    }else if (mapsOptionsValue == "showTableKosongan") {
      showTableKosongan();
    }else if (mapsOptionsValue == "showTableRom") {
      showTableRom();
    }else if (mapsOptionsValue == "showTablePort") {
      showTablePort();
    }else if (mapsOptionsValue == "showTablePool") {
      showTablePool();
    }else if (mapsOptionsValue == "outofhauling") {
      showOutOfHauling();
    }else {
      showHeatmap();
    }
}

function showHeatmap(){
  $("#heatmapbutton").hide();
  $("#mapShow").show();
  $("#realtimealertshowhide").show();
  $("#tableShowMuatan").hide();
  $("#tableShowKosongan").hide();
  $("#tableShowPort").hide();
  $("#tableShowRom").hide();
  $("#tableShowPool").hide();
  $("#tableShowOutOfHauling").hide();
  // soundisactive = 0;
}

function showTableMuatan(sikon){
  $("#heatmapbutton").show();
  $("#kosonganbutton").show();
  $("#mapShow").hide();
  $("#realtimealertshowhide").hide();
  $("#tableShowKosongan").hide();
  $("#tableShowPort").hide();
  $("#tableShowRom").hide();
  $("#tableShowPool").hide();
  $("#tableShowOutOfHauling").hide();
  $("#tableShowMuatan").show();
  soundisactive = 0;

    if (sikon == 0) {
      $.post("<?php echo base_url() ?>maps/km_quickcount", {}, function(response){
        console.log("response : ", response);
        var datamuatan        = response.dataMuatan;
        var datakosongan      = response.dataKosongan;
        var arraydatamuatan   = Object.keys(datamuatan).map((key) => [String(key), datamuatan[key]]);
        var arraydatakosongan = Object.keys(datakosongan).map((key) => [String(key), datakosongan[key]]);
        var sizemuatan        = arraydatamuatan.length;
        var sizekosongan      = arraydatakosongan.length;
        var totalMuatan       = 0;
        var totalKosongan     = 0;

        // console.log("sizemuatan : ", arraydatamuatan);
        // console.log("sizekosongan : ", arraydatakosongan);

          for (var i = 0; i < sizekosongan; i++) {
            var jumlahfix = arraydatakosongan[i][1]; //arraydatamuatan[i][1]; //30+i; //arraydatamuatan[i][1];
            totalKosongan += jumlahfix;
            if (jumlahfix >= middle_limit && jumlahfix < top_limit) {
              $("#labelvehicleonmuatan"+i).addClass('timeline-badge btn btn-primary btn-lg');
              $("#vehicleonkosongan"+i).addClass('timeline-badge2 btn btn-warning btn-md btn-circle');
            }else if(jumlahfix >= top_limit){
              $("#labelvehicleonmuatan"+i).addClass('timeline-badge btn btn-primary btn-lg');
              $("#vehicleonkosongan"+i).addClass('timeline-badge2 btn btn-danger btn-md btn-circle');
            }else {
              $("#labelvehicleonmuatan"+i).addClass('timeline-badge btn btn-primary btn-lg');
              $("#vehicleonkosongan"+i).addClass('timeline-badge2 btn btn-success btn-md btn-circle');
            }
            $("#vehicleonkosongan"+i).html(jumlahfix);
          }

          for (var i = 0; i < sizemuatan; i++) {
            var jumlahfix = arraydatamuatan[i][1]; //arraydatamuatan[i][1]; //30+i; //arraydatamuatan[i][1];
            totalMuatan += jumlahfix;
            if (jumlahfix >= middle_limit && jumlahfix < top_limit) {
              $("#labelvehicleonmuatan"+i).addClass('timeline-badge btn btn-primary btn-lg');
              $("#vehicleonmuatan"+i).addClass('timeline-badge3 btn btn-warning btn-md btn-circle');
            }else if(jumlahfix >= top_limit){
              $("#labelvehicleonmuatan"+i).addClass('timeline-badge btn btn-primary btn-lg');
              $("#vehicleonmuatan"+i).addClass('timeline-badge3 btn btn-danger btn-md btn-circle');
            }else {
              $("#labelvehicleonmuatan"+i).addClass('timeline-badge btn btn-primary btn-lg');
              $("#vehicleonmuatan"+i).addClass('timeline-badge3 btn btn-success btn-md btn-circle');
            }
            $("#vehicleonmuatan"+i).html(jumlahfix);
          }

            $("#lastupdateconsolidated").html("Kosongan : " +totalKosongan+" || Muatan : " +totalMuatan + " || Total : " + (totalKosongan + totalMuatan));
            $("#lastupdateconsolidated").show();
      },"json");
    }else {
      $.post("<?php echo base_url() ?>maps/dataconsolidated", {}, function(response){
        console.log("response : ", response);
        var lastupdate        = response.datamuatan[0].minidashboard_created_date;
        var datamuatan        = JSON.parse(response.datamuatan[0].minidashboard_json);
        var datakosongan      = JSON.parse(response.datakosongan[0].minidashboard_json);
        var arraydatamuatan   = Object.keys(datamuatan).map((key) => [String(key), datamuatan[key]]);
        var arraydatakosongan = Object.keys(datakosongan).map((key) => [String(key), datakosongan[key]]);
        var sizemuatan        = arraydatamuatan.length;
        var sizekosongan      = arraydatakosongan.length;
        var totalMuatan       = 0;
        var totalKosongan     = 0;

        // console.log("sizemuatan : ", sizemuatan);
        // console.log("sizekosongan : ", sizekosongan);

          for (var i = 0; i < sizekosongan; i++) {
            var jumlahfix = arraydatakosongan[i][1]; //arraydatamuatan[i][1]; //30+i; //arraydatamuatan[i][1];
            totalKosongan += jumlahfix;
            if (jumlahfix >= middle_limit && jumlahfix < top_limit) {
              $("#labelvehicleonmuatan"+i).addClass('timeline-badge btn btn-primary btn-lg');
              $("#vehicleonkosongan"+i).addClass('timeline-badge2 btn btn-warning btn-md btn-circle');
            }else if(jumlahfix >= top_limit){
              $("#labelvehicleonmuatan"+i).addClass('timeline-badge btn btn-primary btn-lg');
              $("#vehicleonkosongan"+i).addClass('timeline-badge2 btn btn-danger btn-md btn-circle');
            }else {
              $("#labelvehicleonmuatan"+i).addClass('timeline-badge btn btn-primary btn-lg');
              $("#vehicleonkosongan"+i).addClass('timeline-badge2 btn btn-success btn-md btn-circle');
            }
            $("#vehicleonkosongan"+i).html(jumlahfix);
          }

          for (var i = 0; i < sizemuatan; i++) {
            var jumlahfix = arraydatamuatan[i][1]; //arraydatamuatan[i][1]; //30+i; //arraydatamuatan[i][1];
            totalMuatan += jumlahfix;
            if (jumlahfix >= middle_limit && jumlahfix < top_limit) {
              $("#labelvehicleonmuatan"+i).addClass('timeline-badge btn btn-primary btn-lg');
              $("#vehicleonmuatan"+i).addClass('timeline-badge3 btn btn-warning btn-md btn-circle');
            }else if(jumlahfix >= top_limit){
              $("#labelvehicleonmuatan"+i).addClass('timeline-badge btn btn-primary btn-lg');
              $("#vehicleonmuatan"+i).addClass('timeline-badge3 btn btn-danger btn-md btn-circle');
            }else {
              $("#labelvehicleonmuatan"+i).addClass('timeline-badge btn btn-primary btn-lg');
              $("#vehicleonmuatan"+i).addClass('timeline-badge3 btn btn-success btn-md btn-circle');
            }
            $("#vehicleonmuatan"+i).html(jumlahfix);
          }

            $("#lastupdateconsolidated").html("Kosongan : " +totalKosongan+" || Muatan : " +totalMuatan+ " || Total : "+(totalKosongan + totalMuatan)+" || Last Update : "+lastupdate);
            $("#lastupdateconsolidated").show();
      },"json");
    }
}

function showTableRom(){
  $("#heatmapbutton").show();
  $("#mapShow").hide();
  $("#realtimealertshowhide").hide();
  $("#tableShowMuatan").hide();
  $("#tableShowKosongan").hide();
  $("#tableShowPort").hide();
  $("#tableShowPool").hide();
  $("#tableShowOutOfHauling").hide();
  $("#tableShowRom").show();
  soundisactive = 0;
  var limittengahrom = 1000;
  var limitatasrom   = 1000;

  $.post("<?php echo base_url() ?>maps/rom_quickcount", {}, function(response){
    console.log("response : ", response);
    var datainrom      = response.data;

    var labelvehicleinrom_1        = "ROM 01";
    var vehicleinRom_1             = datainrom.rom_1;
      $("#labelvehicleinrom_1").html(labelvehicleinrom_1);
      $("#vehicleinRom_1").html(vehicleinRom_1);
        if (vehicleinRom_1 > limittengahrom && vehicleinRom_1 < limitatasrom) {
          $("#labelvehicleinrom_1").addClass('btn btn-warning btn-lg');
          $("#vehicleinRom_1").addClass('btn btn-warning btn-lg dropdown-toggle m-r-20');
        }else if(vehicleinRom_1 > limitatasrom){
          $("#labelvehicleinrom_1").addClass('btn btn-danger btn-lg');
          $("#vehicleinRom_1").addClass('btn btn-danger btn-lg dropdown-toggle m-r-20');
        }else {
          $("#labelvehicleinrom_1").addClass('btn btn-primary btn-lg');
          $("#vehicleinRom_1").addClass('btn btn-primary btn-lg dropdown-toggle m-r-20');
        }

    var labelvehicleinrom_1_2_road = "ROM 01/02 ROAD";
    var vehicleinRom_1_2_road      = datainrom.rom_1_2_road;
      $("#labelvehicleinrom_1_2_road").html(labelvehicleinrom_1_2_road);
      $("#vehicleinRom_1_2_road").html(vehicleinRom_1_2_road);
        if (vehicleinRom_1_2_road > limittengahrom && vehicleinRom_1_2_road < limitatasrom) {
          $("#labelvehicleinrom_1_2_road").addClass('btn btn-warning btn-lg');
          $("#vehicleinRom_1_2_road").addClass('btn btn-warning btn-lg dropdown-toggle m-r-20');
        }else if(vehicleinRom_1_2_road > limitatasrom){
          $("#labelvehicleinrom_1_2_road").addClass('btn btn-danger btn-lg');
          $("#vehicleinRom_1_2_road").addClass('btn btn-danger btn-lg dropdown-toggle m-r-20');
        }else {
          $("#labelvehicleinrom_1_2_road").addClass('btn btn-primary btn-lg');
          $("#vehicleinRom_1_2_road").addClass('btn btn-primary btn-lg dropdown-toggle m-r-20');
        }

    var labelvehicleinrom_2        = "ROM 02";
    var vehicleinRom_2             = datainrom.rom_2;
      $("#labelvehicleinrom_2").html(labelvehicleinrom_2);
      $("#vehicleinRom_2").html(vehicleinRom_2);
        if (vehicleinRom_2 > limittengahrom && vehicleinRom_2 < limitatasrom) {
          $("#labelvehicleinrom_2").addClass('btn btn-warning btn-lg');
          $("#vehicleinRom_2").addClass('btn btn-warning btn-lg dropdown-toggle m-r-20');
        }else if(vehicleinRom_2 > limitatasrom){
          $("#labelvehicleinrom_2").addClass('btn btn-danger btn-lg');
          $("#vehicleinRom_2").addClass('btn btn-danger btn-lg dropdown-toggle m-r-20');
        }else {
          $("#labelvehicleinrom_2").addClass('btn btn-primary btn-lg');
          $("#vehicleinRom_2").addClass('btn btn-primary btn-lg dropdown-toggle m-r-20');
        }

    var labelvehicleinrom_3        = "ROM 03";
    var vehicleinRom_3             = datainrom.rom_3;
      $("#labelvehicleinrom_3").html(labelvehicleinrom_3);
      $("#vehicleinRom_3").html(vehicleinRom_3);
        if (vehicleinRom_3 > limittengahrom && vehicleinRom_3 < limitatasrom) {
          $("#labelvehicleinrom_3").addClass('btn btn-warning btn-lg');
          $("#vehicleinRom_3").addClass('btn btn-warning btn-lg dropdown-toggle m-r-20');
        }else if(vehicleinRom_3 > limitatasrom){
          $("#labelvehicleinrom_3").addClass('btn btn-danger btn-lg');
          $("#vehicleinRom_3").addClass('btn btn-danger btn-lg dropdown-toggle m-r-20');
        }else {
          $("#labelvehicleinrom_3").addClass('btn btn-primary btn-lg');
          $("#vehicleinRom_3").addClass('btn btn-primary btn-lg dropdown-toggle m-r-20');
        }

    var labelvehicleinrom_3_4_road = "ROM 03/04 ROAD";
    var vehicleinRom_3_4_road      = datainrom.rom_3_4_road;
      $("#labelvehicleinrom_3_4_road").html(labelvehicleinrom_3_4_road);
      $("#vehicleinRom_3_4_road").html(vehicleinRom_3_4_road);
        if (vehicleinRom_3_4_road > limittengahrom && vehicleinRom_3_4_road < limitatasrom) {
          $("#labelvehicleinrom_3_4_road").addClass('btn btn-warning btn-lg');
          $("#vehicleinRom_3_4_road").addClass('btn btn-warning btn-lg dropdown-toggle m-r-20');
        }else if(vehicleinRom_3_4_road > limitatasrom){
          $("#labelvehicleinrom_3_4_road").addClass('btn btn-danger btn-lg');
          $("#vehicleinRom_3_4_road").addClass('btn btn-danger btn-lg dropdown-toggle m-r-20');
        }else {
          $("#labelvehicleinrom_3_4_road").addClass('btn btn-primary btn-lg');
          $("#vehicleinRom_3_4_road").addClass('btn btn-primary btn-lg dropdown-toggle m-r-20');
        }

    var labelvehicleinrom_4        = "ROM 04";
    var vehicleinRom_4             = datainrom.rom_4;
      $("#labelvehicleinrom_4").html(labelvehicleinrom_4);
      $("#vehicleinRom_4").html(vehicleinRom_4);
        if (vehicleinRom_4 > limittengahrom && vehicleinRom_4 < limitatasrom) {
          $("#labelvehicleinrom_4").addClass('btn btn-warning btn-lg');
          $("#vehicleinRom_4").addClass('btn btn-warning btn-lg dropdown-toggle m-r-20');
        }else if(vehicleinRom_4 > limitatasrom){
          $("#labelvehicleinrom_4").addClass('btn btn-danger btn-lg');
          $("#vehicleinRom_4").addClass('btn btn-danger btn-lg dropdown-toggle m-r-20');
        }else {
          $("#labelvehicleinrom_4").addClass('btn btn-primary btn-lg');
          $("#vehicleinRom_4").addClass('btn btn-primary btn-lg dropdown-toggle m-r-20');
        }

    var labelvehicleinrom_6_road   = "ROM 06 ROAD";
    var vehicleinRom_6_road        = datainrom.rom_6_road;
      $("#labelvehicleinrom_6_road").html(labelvehicleinrom_6_road);
      $("#vehicleinRom_6_road").html(vehicleinRom_6_road);
        if (vehicleinRom_6_road > limittengahrom && vehicleinRom_6_road < limitatasrom) {
          $("#labelvehicleinrom_6_road").addClass('btn btn-warning btn-lg');
          $("#vehicleinRom_6_road").addClass('btn btn-warning btn-lg dropdown-toggle m-r-20');
        }else if(vehicleinRom_6_road > limitatasrom){
          $("#labelvehicleinrom_6_road").addClass('btn btn-danger btn-lg');
          $("#vehicleinRom_6_road").addClass('btn btn-danger btn-lg dropdown-toggle m-r-20');
        }else {
          $("#labelvehicleinrom_6_road").addClass('btn btn-primary btn-lg');
          $("#vehicleinRom_6_road").addClass('btn btn-primary btn-lg dropdown-toggle m-r-20');
        }

    var labelvehicleinrom_6        = "ROM 06";
    var vehicleinRom_6             = datainrom.rom_6;
      $("#labelvehicleinrom_6").html(labelvehicleinrom_6);
      $("#vehicleinRom_6").html(vehicleinRom_6);
        if (vehicleinRom_6 > limittengahrom && vehicleinRom_6 < limitatasrom) {
          $("#labelvehicleinrom_6").addClass('btn btn-warning btn-lg');
          $("#vehicleinRom_6").addClass('btn btn-warning btn-lg dropdown-toggle m-r-20');
        }else if(vehicleinRom_6 > limitatasrom){
          $("#labelvehicleinrom_6").addClass('btn btn-danger btn-lg');
          $("#vehicleinRom_6").addClass('btn btn-danger btn-lg dropdown-toggle m-r-20');
        }else {
          $("#labelvehicleinrom_6").addClass('btn btn-primary btn-lg');
          $("#vehicleinRom_6").addClass('btn btn-primary btn-lg dropdown-toggle m-r-20');
        }

    var labelvehicleinrom_7        = "ROM 07";
    var vehicleinRom_7             = datainrom.rom_7;
      $("#labelvehicleinrom_7").html(labelvehicleinrom_7);
      $("#vehicleinRom_7").html(vehicleinRom_7);
        if (vehicleinRom_7 > limittengahrom && vehicleinRom_7 < limitatasrom) {
          $("#labelvehicleinrom_7").addClass('btn btn-warning btn-lg');
          $("#vehicleinRom_7").addClass('btn btn-warning btn-lg dropdown-toggle m-r-20');
        }else if(vehicleinRom_7 > limitatasrom){
          $("#labelvehicleinrom_7").addClass('btn btn-danger btn-lg');
          $("#vehicleinRom_7").addClass('btn btn-danger btn-lg dropdown-toggle m-r-20');
        }else {
          $("#labelvehicleinrom_7").addClass('btn btn-primary btn-lg');
          $("#vehicleinRom_7").addClass('btn btn-primary btn-lg dropdown-toggle m-r-20');
        }

    var labelvehicleinrom_7_8_road = "ROM 07/08 ROAD";
    var vehicleinRom_7_8_road      = datainrom.rom_7_8_road;
      $("#labelvehicleinrom_7_8_road").html(labelvehicleinrom_7_8_road);
      $("#vehicleinRom_7_8_road").html(vehicleinRom_7_8_road);
        if (vehicleinRom_7_8_road > limittengahrom && vehicleinRom_7_8_road < limitatasrom) {
          $("#labelvehicleinrom_7_8_road").addClass('btn btn-warning btn-lg');
          $("#vehicleinRom_7_8_road").addClass('btn btn-warning btn-lg dropdown-toggle m-r-20');
        }else if(vehicleinRom_7_8_road > limitatasrom){
          $("#labelvehicleinrom_7_8_road").addClass('btn btn-danger btn-lg');
          $("#vehicleinRom_7_8_road").addClass('btn btn-danger btn-lg dropdown-toggle m-r-20');
        }else {
          $("#labelvehicleinrom_7_8_road").addClass('btn btn-primary btn-lg');
          $("#vehicleinRom_7_8_road").addClass('btn btn-primary btn-lg dropdown-toggle m-r-20');
        }

    var labelvehicleinrom_8        = "ROM 08";
    var vehicleinRom_8             = datainrom.rom_8;
    $("#labelvehicleinrom_8").html(labelvehicleinrom_8);
    $("#vehicleinRom_8").html(vehicleinRom_8);
      if (vehicleinRom_8 > limittengahrom && vehicleinRom_8 < limitatasrom) {
        $("#labelvehicleinrom_8").addClass('btn btn-warning btn-lg');
        $("#vehicleinRom_8").addClass('btn btn-warning btn-lg dropdown-toggle m-r-20');
      }else if(vehicleinRom_8 > limitatasrom){
        $("#labelvehicleinrom_8").addClass('btn btn-danger btn-lg');
        $("#vehicleinRom_8").addClass('btn btn-danger btn-lg dropdown-toggle m-r-20');
      }else {
        $("#labelvehicleinrom_8").addClass('btn btn-primary btn-lg');
        $("#vehicleinRom_8").addClass('btn btn-primary btn-lg dropdown-toggle m-r-20');
      }

      var jumlahtotalInRom = parseFloat(vehicleinRom_1 + vehicleinRom_1_2_road + vehicleinRom_2 + vehicleinRom_3 + vehicleinRom_3_4_road + vehicleinRom_4 + vehicleinRom_6_road + vehicleinRom_6 + vehicleinRom_7 + vehicleinRom_7_8_road + vehicleinRom_8);
      console.log("jumlahtotalInRom : ", jumlahtotalInRom);

      $("#jumlahtotalInRom").html("Total : " +jumlahtotalInRom);
      $("#jumlahtotalInRom").show();

  },"json");
}

function listVehicleOnRom(id){
  $.post("<?php echo base_url() ?>maps/getlistinrom", {idrom: id}, function(response){
    console.log("response By ROM LIST : ", response);
    var datafix = response.data;
    var htmlpool = "";
      if (datafix.length > 0) {
        var lastcheckpoolws = "Last Check : "+response.data[0].auto_last_update + " WITA";
        $("#modalStateTitle").html(response.romsent);
        $("#lastcheckpoolws").html(lastcheckpoolws);
        htmlpool += '<table class="table table-striped">';
          htmlpool += '<thead>';
            htmlpool += '<tr>';
              htmlpool += '<th>No</th>';
              htmlpool += '<th>Vehicle</th>';
              htmlpool += '<th align="center">Engine</th>';
              htmlpool += '<th align="center">Speed (Kph)</th>';
              htmlpool += '<th>Coord</th>';
            htmlpool += '</tr>';
          htmlpool += '</thead>';
        for (var i = 0; i < datafix.length; i++) {
            htmlpool += '<tr>';
              htmlpool += '<td style="font-size:12px;color:black"><span style="color:black;">'+(i+1)+'</span>';
              htmlpool += '<td style="font-size:12px;color:black"><span style="color:black;">'+datafix[i].vehicle_no+ " " +datafix[i].vehicle_name+'</span>';
              htmlpool += '<td align="center" style="font-size:12px;color:black"><span style="color:black;">'+datafix[i].auto_last_engine+'</span>';
              htmlpool += '<td align="center" style="font-size:12px;color:black"><span style="color:black;">'+datafix[i].auto_last_speed+'</span>';
              htmlpool += '<td style="font-size:12px;color:black"><span style="color:black;">'+datafix[i].auto_last_lat+ "," +datafix[i].auto_last_long+'</span>';
            htmlpool += '</tr>';
        }
        htmlpool += '</table>';
        $("#modalStateContent").html(htmlpool);
        modalPoolFromMasterData('modalState');
      }else {
        alert("Data Tidak Ada");
      }
    },"json");
}

function showTablePort(){
  $("#heatmapbutton").show();
  $("#mapShow").hide();
  $("#realtimealertshowhide").hide();
  $("#tableShowMuatan").hide();
  $("#tableShowKosongan").hide();
  $("#tableShowRom").hide();
  $("#tableShowPool").hide();
  $("#tableShowOutOfHauling").hide();
  $("#tableShowPort").show();
  soundisactive = 0;

  $.post("<?php echo base_url() ?>maps/port_quickcount", {}, function(response){
    console.log("response : ", response);
    var datainport      = response.data;
    var limittengahport = 1000;
    var limitatasport   = 1000;

    var labelvehicleinport_bbc        = "PORT BBC";
    var vehicleinPort_bbc             = datainport.port_bbc;
      $("#labelvehicleinport_bbc").html(labelvehicleinport_bbc);
      $("#vehicleinPort_bbc").html(vehicleinPort_bbc);
        if (vehicleinPort_bbc > limittengahport && vehicleinPort_bbc < limitatasport) {
          $("#labelvehicleinport_bbc").addClass('btn btn-warning btn-lg');
          $("#vehicleinPort_bbc").addClass('btn btn-warning btn-lg dropdown-toggle m-r-20');
        }else if(vehicleinPort_bbc > limitatasport){
          $("#labelvehicleinport_bbc").addClass('btn btn-danger btn-lg');
          $("#vehicleinPort_bbc").addClass('btn btn-danger btn-lg dropdown-toggle m-r-20');
        }else {
          $("#labelvehicleinport_bbc").addClass('btn btn-primary btn-lg');
          $("#vehicleinPort_bbc").addClass('btn btn-primary btn-lg dropdown-toggle m-r-20');
        }

    var labelvehicleinport_bib       = "PORT BIB";
    var vehicleinPort_bib             = datainport.port_bib;
      $("#labelvehicleinport_bib").html(labelvehicleinport_bib);
      $("#vehicleinPort_bib").html(vehicleinPort_bib);
        if (vehicleinPort_bib > limittengahport && vehicleinPort_bib < limitatasport) {
          $("#labelvehicleinport_bib").addClass('btn btn-warning btn-lg');
          $("#vehicleinPort_bib").addClass('btn btn-warning btn-lg dropdown-toggle m-r-20');
        }else if(vehicleinPort_bib > limitatasport){
          $("#labelvehicleinport_bib").addClass('btn btn-danger btn-lg');
          $("#vehicleinPort_bib").addClass('btn btn-danger btn-lg dropdown-toggle m-r-20');
        }else {
          $("#labelvehicleinport_bib").addClass('btn btn-primary btn-lg');
          $("#vehicleinPort_bib").addClass('btn btn-primary btn-lg dropdown-toggle m-r-20');
        }

    var labelvehicleinport_bir = "PORT BIR";
    var vehicleinPort_bir      = datainport.port_bir;
      $("#labelvehicleinport_bir").html(labelvehicleinport_bir);
      $("#vehicleinPort_bir").html(vehicleinPort_bir);
        if (vehicleinPort_bir > limittengahport && vehicleinPort_bir < limitatasport) {
          $("#labelvehicleinport_bir").addClass('btn btn-warning btn-lg');
          $("#vehicleinPort_bir").addClass('btn btn-warning btn-lg dropdown-toggle m-r-20');
        }else if(vehicleinPort_bir > limitatasport){
          $("#labelvehicleinport_bir").addClass('btn btn-danger btn-lg');
          $("#vehicleinPort_bir").addClass('btn btn-danger btn-lg dropdown-toggle m-r-20');
        }else {
          $("#labelvehicleinport_bir").addClass('btn btn-primary btn-lg');
          $("#vehicleinPort_bir").addClass('btn btn-primary btn-lg dropdown-toggle m-r-20');
        }

    var labelvehicleinport_tia = "PORT TIA";
    var vehicleinPort_tia      = datainport.port_tia;
      $("#labelvehicleinport_tia").html(labelvehicleinport_tia);
      $("#vehicleinPort_tia").html(vehicleinPort_tia);
        if (vehicleinPort_tia > limittengahport && vehicleinPort_tia < limitatasport) {
          $("#labelvehicleinport_tia").addClass('btn btn-warning btn-lg');
          $("#vehicleinPort_tia").addClass('btn btn-warning btn-lg dropdown-toggle m-r-20');
        }else if(vehicleinPort_tia > limitatasport){
          $("#labelvehicleinport_tia").addClass('btn btn-danger btn-lg');
          $("#vehicleinPort_tia").addClass('btn btn-danger btn-lg dropdown-toggle m-r-20');
        }else {
          $("#labelvehicleinport_tia").addClass('btn btn-primary btn-lg');
          $("#vehicleinPort_tia").addClass('btn btn-primary btn-lg dropdown-toggle m-r-20');
        }

        var jumlahtotalInPort = parseFloat(vehicleinPort_bbc + vehicleinPort_bib + vehicleinPort_bir + vehicleinPort_tia);
        console.log("jumlahtotalInPort : ", jumlahtotalInPort);

        $("#jumlahtotalInPort").html("Total : " +jumlahtotalInPort);
        $("#jumlahtotalInPort").show();

  },"json");
}

function listVehicleOnPort(id){
  $.post("<?php echo base_url() ?>maps/getlistinport", {idport: id}, function(response){
    console.log("response By PORT LIST : ", response);
    var datafix = response.data;
    var htmlpool = "";
      if (datafix.length > 0) {
        var lastcheckpoolws = "Last Check : "+response.data[0].auto_last_update + " WITA";
        $("#modalStateTitle").html(response.portsent);
        $("#lastcheckpoolws").html(lastcheckpoolws);
        htmlpool += '<table class="table table-striped">';
          htmlpool += '<thead>';
            htmlpool += '<tr>';
              htmlpool += '<th>No</th>';
              htmlpool += '<th>Vehicle</th>';
              htmlpool += '<th align="center">Engine</th>';
              htmlpool += '<th align="center">Speed (Kph)</th>';
              htmlpool += '<th>Coord</th>';
            htmlpool += '</tr>';
          htmlpool += '</thead>';
        for (var i = 0; i < datafix.length; i++) {
            htmlpool += '<tr>';
              htmlpool += '<td style="font-size:12px;color:black"><span style="color:black;">'+(i+1)+'</span>';
              htmlpool += '<td style="font-size:12px;color:black"><span style="color:black;">'+datafix[i].vehicle_no+ " " +datafix[i].vehicle_name+'</span>';
              htmlpool += '<td align="center" style="font-size:12px;color:black"><span style="color:black;">'+datafix[i].auto_last_engine+'</span>';
              htmlpool += '<td align="center" style="font-size:12px;color:black"><span style="color:black;">'+datafix[i].auto_last_speed+'</span>';
              htmlpool += '<td style="font-size:12px;color:black"><span style="color:black;">'+datafix[i].auto_last_lat+ "," +datafix[i].auto_last_long+'</span>';
            htmlpool += '</tr>';
        }
        htmlpool += '</table>';
        $("#modalStateContent").html(htmlpool);
        modalPoolFromMasterData('modalState');
      }else {
        alert("Data Tidak Ada");
      }
    },"json");
}

function showTablePool(){
  $("#heatmapbutton").hide();
  $("#mapShow").hide();
  $("#realtimealertshowhide").hide();
  $("#tableShowMuatan").hide();
  $("#tableShowKosongan").hide();
  $("#tableShowRom").hide();
  $("#tableShowPort").hide();
  $("#tableShowOutOfHauling").hide();
  $("#tableShowPool").show();
  soundisactive = 0;

  $.post("<?php echo base_url() ?>maps/poolws_quickcount", {}, function(response){
    console.log("response : ", response);
    // var datainpool      = JSON.parse(response.data);//JSON.parse(response.data[0].minidashboard_json);
    var arraydatainpool = response.data;
    var size            = arraydatainpool.length;

    var labelvehicleinpool_bbs        = "POOL BBS";
    var vehicleinpool_bbs             = arraydatainpool.pool_bbs;
      $("#labelvehicleinpool_bbs").html(labelvehicleinpool_bbs);
      $("#vehicleinpool_bbs").html(vehicleinpool_bbs);
        if (vehicleinpool_bbs > 100 && vehicleinpool_bbs < 100) {
          $("#labelvehicleinpool_bbs").addClass('btn btn-warning btn-lg');
          $("#vehicleinpool_bbs").addClass('btn btn-warning btn-lg dropdown-toggle m-r-20');
        }else if(vehicleinpool_bbs > 50){
          $("#labelvehicleinpool_bbs").addClass('btn btn-danger btn-lg');
          $("#vehicleinpool_bbs").addClass('btn btn-danger btn-lg dropdown-toggle m-r-20');
        }else {
          $("#labelvehicleinpool_bbs").addClass('btn btn-primary btn-lg');
          $("#vehicleinpool_bbs").addClass('btn btn-primary btn-lg dropdown-toggle m-r-20');
        }

      var labelvehicleinpool_bka        = "POOL BKA";
      var vehicleinpool_bka             = arraydatainpool.pool_bka;
        $("#labelvehicleinpool_bka").html(labelvehicleinpool_bka);
        $("#vehicleinpool_bka").html(vehicleinpool_bka);
          if (vehicleinpool_bka > 100 && vehicleinpool_bka < 100) {
            $("#labelvehicleinpool_bka").addClass('btn btn-warning btn-lg');
            $("#vehicleinpool_bka").addClass('btn btn-warning btn-lg dropdown-toggle m-r-20');
          }else if(vehicleinpool_bka > 50){
            $("#labelvehicleinpool_bka").addClass('btn btn-danger btn-lg');
            $("#vehicleinpool_bka").addClass('btn btn-danger btn-lg dropdown-toggle m-r-20');
          }else {
            $("#labelvehicleinpool_bka").addClass('btn btn-primary btn-lg');
            $("#vehicleinpool_bka").addClass('btn btn-primary btn-lg dropdown-toggle m-r-20');
          }

      var labelvehicleinpool_bsl        = "POOL BSL";
      var vehicleinpool_bsl             = arraydatainpool.pool_bsl;
        $("#labelvehicleinpool_bsl").html(labelvehicleinpool_bsl);
        $("#vehicleinpool_bsl").html(vehicleinpool_bsl);
          if (vehicleinpool_bsl > 100 && vehicleinpool_bsl < 100) {
            $("#labelvehicleinpool_bsl").addClass('btn btn-warning btn-lg');
            $("#vehicleinpool_bsl").addClass('btn btn-warning btn-lg dropdown-toggle m-r-20');
          }else if(vehicleinpool_bsl > 50){
            $("#labelvehicleinpool_bsl").addClass('btn btn-danger btn-lg');
            $("#vehicleinpool_bsl").addClass('btn btn-danger btn-lg dropdown-toggle m-r-20');
          }else {
            $("#labelvehicleinpool_bsl").addClass('btn btn-primary btn-lg');
            $("#vehicleinpool_bsl").addClass('btn btn-primary btn-lg dropdown-toggle m-r-20');
          }

      var labelvehicleinpool_gecl        = "POOL GECL";
      var vehicleinpool_gecl             = arraydatainpool.pool_gecl;
        $("#labelvehicleinpool_gecl").html(labelvehicleinpool_gecl);
        $("#vehicleinpool_gecl").html(vehicleinpool_gecl);
          if (vehicleinpool_gecl > 100 && vehicleinpool_gecl < 100) {
            $("#labelvehicleinpool_gecl").addClass('btn btn-warning btn-lg');
            $("#vehicleinpool_gecl").addClass('btn btn-warning btn-lg dropdown-toggle m-r-20');
          }else if(vehicleinpool_gecl > 50){
            $("#labelvehicleinpool_gecl").addClass('btn btn-danger btn-lg');
            $("#vehicleinpool_gecl").addClass('btn btn-danger btn-lg dropdown-toggle m-r-20');
          }else {
            $("#labelvehicleinpool_gecl").addClass('btn btn-primary btn-lg');
            $("#vehicleinpool_gecl").addClass('btn btn-primary btn-lg dropdown-toggle m-r-20');
          }

      var labelvehicleinpool_kusan_bawah        = "POOL KUSAN BAWAH";
      var vehicleinpool_kusan_bawah             = arraydatainpool.pool_kusan_bawah;
        $("#labelvehicleinpool_kusan_bawah").html(labelvehicleinpool_kusan_bawah);
        $("#vehicleinpool_kusan_bawah").html(vehicleinpool_kusan_bawah);
          if (vehicleinpool_kusan_bawah > 100 && vehicleinpool_kusan_bawah < 100) {
            $("#labelvehicleinpool_kusan_bawah").addClass('btn btn-warning btn-lg');
            $("#vehicleinpool_kusan_bawah").addClass('btn btn-warning btn-lg dropdown-toggle m-r-20');
          }else if(vehicleinpool_kusan_bawah > 50){
            $("#labelvehicleinpool_kusan_bawah").addClass('btn btn-danger btn-lg');
            $("#vehicleinpool_kusan_bawah").addClass('btn btn-danger btn-lg dropdown-toggle m-r-20');
          }else {
            $("#labelvehicleinpool_kusan_bawah").addClass('btn btn-primary btn-lg');
            $("#vehicleinpool_kusan_bawah").addClass('btn btn-primary btn-lg dropdown-toggle m-r-20');
          }

      var labelvehicleinpool_kusan        = "POOL KUSAN";
      var vehicleinpool_kusan             = arraydatainpool.pool_kusan;
        $("#labelvehicleinpool_kusan").html(labelvehicleinpool_kusan);
        $("#vehicleinpool_kusan").html(vehicleinpool_kusan);
          if (vehicleinpool_kusan > 100 && vehicleinpool_kusan < 100) {
            $("#labelvehicleinpool_kusan").addClass('btn btn-warning btn-lg');
            $("#vehicleinpool_kusan").addClass('btn btn-warning btn-lg dropdown-toggle m-r-20');
          }else if(vehicleinpool_kusan > 50){
            $("#labelvehicleinpool_kusan").addClass('btn btn-danger btn-lg');
            $("#vehicleinpool_kusan").addClass('btn btn-danger btn-lg dropdown-toggle m-r-20');
          }else {
            $("#labelvehicleinpool_kusan").addClass('btn btn-primary btn-lg');
            $("#vehicleinpool_kusan").addClass('btn btn-primary btn-lg dropdown-toggle m-r-20');
          }

      var labelvehicleinpool_mks        = "POOL MKS";
      var vehicleinpool_mks             = arraydatainpool.pool_mks;
        $("#labelvehicleinpool_mks").html(labelvehicleinpool_mks);
        $("#vehicleinpool_mks").html(vehicleinpool_mks);
          if (vehicleinpool_mks > 100 && vehicleinpool_mks < 100) {
            $("#labelvehicleinpool_mks").addClass('btn btn-warning btn-lg');
            $("#vehicleinpool_mks").addClass('btn btn-warning btn-lg dropdown-toggle m-r-20');
          }else if(vehicleinpool_mks > 50){
            $("#labelvehicleinpool_mks").addClass('btn btn-danger btn-lg');
            $("#vehicleinpool_mks").addClass('btn btn-danger btn-lg dropdown-toggle m-r-20');
          }else {
            $("#labelvehicleinpool_mks").addClass('btn btn-primary btn-lg');
            $("#vehicleinpool_mks").addClass('btn btn-primary btn-lg dropdown-toggle m-r-20');
          }

      var labelvehicleinpool_ram        = "POOL RAM";
      var vehicleinpool_ram             = arraydatainpool.pool_ram;
        $("#labelvehicleinpool_ram").html(labelvehicleinpool_ram);
        $("#vehicleinpool_ram").html(vehicleinpool_ram);
          if (vehicleinpool_ram > 100 && vehicleinpool_ram < 100) {
            $("#labelvehicleinpool_ram").addClass('btn btn-warning btn-lg');
            $("#vehicleinpool_ram").addClass('btn btn-warning btn-lg dropdown-toggle m-r-20');
          }else if(vehicleinpool_ram > 50){
            $("#labelvehicleinpool_ram").addClass('btn btn-danger btn-lg');
            $("#vehicleinpool_ram").addClass('btn btn-danger btn-lg dropdown-toggle m-r-20');
          }else {
            $("#labelvehicleinpool_ram").addClass('btn btn-primary btn-lg');
            $("#vehicleinpool_ram").addClass('btn btn-primary btn-lg dropdown-toggle m-r-20');
          }

      var labelvehicleinpool_rbt        = "POOL RBT";
      var vehicleinpool_rbt             = arraydatainpool.pool_rbt;
        $("#labelvehicleinpool_rbt").html(labelvehicleinpool_rbt);
        $("#vehicleinpool_rbt").html(vehicleinpool_rbt);
          if (vehicleinpool_rbt > 100 && vehicleinpool_rbt < 100) {
            $("#labelvehicleinpool_rbt").addClass('btn btn-warning btn-lg');
            $("#vehicleinpool_rbt").addClass('btn btn-warning btn-lg dropdown-toggle m-r-20');
          }else if(vehicleinpool_rbt > 50){
            $("#labelvehicleinpool_rbt").addClass('btn btn-danger btn-lg');
            $("#vehicleinpool_rbt").addClass('btn btn-danger btn-lg dropdown-toggle m-r-20');
          }else {
            $("#labelvehicleinpool_rbt").addClass('btn btn-primary btn-lg');
            $("#vehicleinpool_rbt").addClass('btn btn-primary btn-lg dropdown-toggle m-r-20');
          }

      var labelvehicleinpool_stli        = "POOL STLI";
      var vehicleinpool_stli             = arraydatainpool.pool_stli;
        $("#labelvehicleinpool_stli").html(labelvehicleinpool_stli);
        $("#vehicleinpool_stli").html(vehicleinpool_stli);
          if (vehicleinpool_stli > 100 && vehicleinpool_stli < 100) {
            $("#labelvehicleinpool_stli").addClass('btn btn-warning btn-lg');
            $("#vehicleinpool_stli").addClass('btn btn-warning btn-lg dropdown-toggle m-r-20');
          }else if(vehicleinpool_stli > 50){
            $("#labelvehicleinpool_stli").addClass('btn btn-danger btn-lg');
            $("#vehicleinpool_stli").addClass('btn btn-danger btn-lg dropdown-toggle m-r-20');
          }else {
            $("#labelvehicleinpool_stli").addClass('btn btn-primary btn-lg');
            $("#vehicleinpool_stli").addClass('btn btn-primary btn-lg dropdown-toggle m-r-20');
          }

      var labelvehicleinws_gecl        = "WS GECL";
      var vehicleinws_gecl             = arraydatainpool.ws_gecl;
        $("#labelvehicleinws_gecl").html(labelvehicleinws_gecl);
        $("#vehicleinws_gecl").html(vehicleinws_gecl);
          if (vehicleinws_gecl > 100 && vehicleinws_gecl < 100) {
            $("#labelvehicleinws_gecl").addClass('btn btn-warning btn-lg');
            $("#vehicleinws_gecl").addClass('btn btn-warning btn-lg dropdown-toggle m-r-20');
          }else if(vehicleinws_gecl > 50){
            $("#labelvehicleinws_gecl").addClass('btn btn-danger btn-lg');
            $("#vehicleinws_gecl").addClass('btn btn-danger btn-lg dropdown-toggle m-r-20');
          }else {
            $("#labelvehicleinws_gecl").addClass('btn btn-primary btn-lg');
            $("#vehicleinws_gecl").addClass('btn btn-primary btn-lg dropdown-toggle m-r-20');
          }

      var labelvehicleinws_kmb        = "WS KMB";
      var vehicleinws_kmb             = arraydatainpool.ws_kmb;
        $("#labelvehicleinws_kmb").html(labelvehicleinws_kmb);
        $("#vehicleinws_kmb").html(vehicleinws_kmb);
          if (vehicleinws_kmb > 100 && vehicleinws_kmb < 100) {
            $("#labelvehicleinws_kmb").addClass('btn btn-warning btn-lg');
            $("#vehicleinws_kmb").addClass('btn btn-warning btn-lg dropdown-toggle m-r-20');
          }else if(vehicleinws_kmb > 50){
            $("#labelvehicleinws_kmb").addClass('btn btn-danger btn-lg');
            $("#vehicleinws_kmb").addClass('btn btn-danger btn-lg dropdown-toggle m-r-20');
          }else {
            $("#labelvehicleinws_kmb").addClass('btn btn-primary btn-lg');
            $("#vehicleinws_kmb").addClass('btn btn-primary btn-lg dropdown-toggle m-r-20');
          }

      var labelvehicleinws_mks        = "WS MKS";
      var vehicleinws_mks             = arraydatainpool.ws_mks;
        $("#labelvehicleinws_mks").html(labelvehicleinws_mks);
        $("#vehicleinws_mks").html(vehicleinws_mks);
          if (vehicleinws_mks > 100 && vehicleinws_mks < 100) {
            $("#labelvehicleinws_mks").addClass('btn btn-warning btn-lg');
            $("#vehicleinws_mks").addClass('btn btn-warning btn-lg dropdown-toggle m-r-20');
          }else if(vehicleinws_mks > 50){
            $("#labelvehicleinws_mks").addClass('btn btn-danger btn-lg');
            $("#vehicleinws_mks").addClass('btn btn-danger btn-lg dropdown-toggle m-r-20');
          }else {
            $("#labelvehicleinws_mks").addClass('btn btn-primary btn-lg');
            $("#vehicleinws_mks").addClass('btn btn-primary btn-lg dropdown-toggle m-r-20');
          }

          var labelvehicleinws_rbt        = "WS RBT";
          var vehicleinws_rbt             = arraydatainpool.ws_rbt;
            $("#labelvehicleinws_rbt").html(labelvehicleinws_rbt);
            $("#vehicleinws_rbt").html(vehicleinws_rbt);
              if (vehicleinws_rbt > 100 && vehicleinws_rbt < 100) {
                $("#labelvehicleinws_rbt").addClass('btn btn-warning btn-lg');
                $("#vehicleinws_rbt").addClass('btn btn-warning btn-lg dropdown-toggle m-r-20');
              }else if(vehicleinws_rbt > 50){
                $("#labelvehicleinws_rbt").addClass('btn btn-danger btn-lg');
                $("#vehicleinws_rbt").addClass('btn btn-danger btn-lg dropdown-toggle m-r-20');
              }else {
                $("#labelvehicleinws_rbt").addClass('btn btn-primary btn-lg');
                $("#vehicleinws_rbt").addClass('btn btn-primary btn-lg dropdown-toggle m-r-20');
              }

          var jumlahtotalinpool = parseFloat(vehicleinpool_bbs + vehicleinpool_bka + vehicleinpool_bsl + vehicleinpool_gecl + vehicleinpool_kusan_bawah + vehicleinpool_kusan + vehicleinpool_mks + vehicleinpool_ram + vehicleinpool_rbt + vehicleinpool_stli + vehicleinws_gecl + vehicleinws_kmb + vehicleinws_mks + vehicleinws_rbt);
          console.log("jumlahtotalinpool : ", jumlahtotalinpool);

          $("#jumlahtotalinpool").html("Total : " +jumlahtotalinpool);
          $("#jumlahtotalinpool").show();

  },"json");
}

function showOutOfHauling(){
  $("#heatmapbutton").show();
  $("#kosonganbutton").show();
  $("#mapShow").hide();
  $("#realtimealertshowhide").hide();
  $("#tableShowKosongan").hide();
  $("#tableShowPort").hide();
  $("#tableShowRom").hide();
  $("#tableShowPool").hide();
  $("#tableShowMuatan").hide();
  $("#tableShowOutOfHauling").show();
  soundisactive = 0;

  $.post("<?php echo base_url() ?>maps/outOfHauling_quickcount", {}, function(response){
    console.log("response : ", response);
    var dataoutofhauling        = response.data.outofhauling;

    // console.log("arraydataoutofhauling : ", arraydataoutofhauling[0]);
    // console.log("sizekosongan : ", sizekosongan);

    var labelvehicleoutofhauling        = "Out Of Hauling";
    var vehicleoutofhauling             = dataoutofhauling;
      $("#labelvehicleoutofhauling").html(labelvehicleoutofhauling);
      $("#vehicleoutofhauling").html(vehicleoutofhauling);
        if (vehicleoutofhauling > 500 && vehicleoutofhauling < 500) {
          $("#labelvehicleoutofhauling").addClass('btn btn-warning btn-lg');
          $("#vehicleoutofhauling").addClass('btn btn-warning btn-lg dropdown-toggle m-r-20');
        }else if(vehicleoutofhauling > 500){
          $("#labelvehicleoutofhauling").addClass('btn btn-danger btn-lg');
          $("#vehicleoutofhauling").addClass('btn btn-danger btn-lg dropdown-toggle m-r-20');
        }else {
          $("#labelvehicleoutofhauling").addClass('btn btn-primary btn-lg');
          $("#vehicleoutofhauling").addClass('btn btn-primary btn-lg dropdown-toggle m-r-20');
        }
        $("#vehicleoutofhauling").html(vehicleoutofhauling);


    // $("#jumlahtotaloutofhauling").html("Total : " +vehicleoutofhauling);
    $("#jumlahtotaloutofhauling").show();
  },"json");
}

function listOutOfHauling(){
  $.post("<?php echo base_url() ?>maps/getlistoutofhauling", {}, function(response){
    console.log("response By Pool : ", response);
    var datafix = response.data;
    var htmlpool = "";
      if (datafix.length > 0) {
        var lastcheckpoolws = "Last Check : "+response.data[0].auto_last_update + " WITA";
        $("#modalStateTitle").html("Out Of Hauling");
        $("#lastcheckpoolws").html(lastcheckpoolws);
        htmlpool += '<table class="table table-striped">';
          htmlpool += '<thead>';
            htmlpool += '<tr>';
              htmlpool += '<th>No</th>';
              htmlpool += '<th>Vehicle</th>';
              htmlpool += '<th align="center">Engine</th>';
              htmlpool += '<th align="center">Speed (Kph)</th>';
              htmlpool += '<th>Coord</th>';
            htmlpool += '</tr>';
          htmlpool += '</thead>';
        for (var i = 0; i < datafix.length; i++) {
            htmlpool += '<tr>';
              htmlpool += '<td style="font-size:12px;color:black"><span style="color:black;">'+(i+1)+'</span>';
              htmlpool += '<td style="font-size:12px;color:black"><span style="color:black;">'+datafix[i].vehicle_no+ " " +datafix[i].vehicle_name+'</span>';
              htmlpool += '<td align="center" style="font-size:12px;color:black"><span style="color:black;">'+datafix[i].auto_last_engine+'</span>';
              htmlpool += '<td align="center" style="font-size:12px;color:black"><span style="color:black;">'+datafix[i].auto_last_speed+'</span>';
              htmlpool += '<td style="font-size:12px;color:black"><span style="color:black;">'+datafix[i].auto_last_lat+ "," +datafix[i].auto_last_long+'</span>';
            htmlpool += '</tr>';
        }
        htmlpool += '</table>';
        $("#modalStateContent").html(htmlpool);
        modalPoolFromMasterData('modalState');
      }else {
        alert("Data Tidak Ada");
      }
    },"json");
}

function getVehicleByPool(id){
  $.post("<?php echo base_url() ?>maps/vehicleOnPool", {idpool : id}, function(response){
    console.log("response By Pool : ", response);
    var datafix = response.data;
    var htmlpool = "";
      if (datafix.length > 0) {
        var lastcheckpoolws = "Last Check : "+response.lastcheck + " WITA";
        $("#modalStateTitle").html(response.statesent);
        $("#lastcheckpoolws").html(lastcheckpoolws);
        htmlpool += '<table class="table table-striped">';
          htmlpool += '<thead>';
            htmlpool += '<tr>';
            htmlpool += '<th>No</th>';
              htmlpool += '<th>Vehicle</th>';
              htmlpool += '<th align="center">Engine</th>';
              htmlpool += '<th align="center">Speed (Kph)</th>';
              htmlpool += '<th>Coord</th>';
            htmlpool += '</tr>';
          htmlpool += '</thead>';
        for (var i = 0; i < datafix.length; i++) {
            htmlpool += '<tr>';
              htmlpool += '<td style="font-size:12px;color:black"><span style="color:black;">'+(i+1)+'</span>';
              htmlpool += '<td style="font-size:12px;color:black"><span style="color:black;">'+datafix[i].vehicle_no+ " " +datafix[i].vehicle_name+'</span>';
              htmlpool += '<td align="center" style="font-size:12px;color:black"><span style="color:black;">'+datafix[i].auto_last_engine+'</span>';
              htmlpool += '<td align="center" style="font-size:12px;color:black"><span style="color:black;">'+datafix[i].auto_last_speed+'</span>';
              htmlpool += '<td style="font-size:12px;color:black"><span style="color:black;">'+datafix[i].auto_last_lat+ "," +datafix[i].auto_last_long+'</span>';
            htmlpool += '</tr>';
        }
        htmlpool += '</table>';
        $("#modalStateContent").html(htmlpool);
        modalPoolFromMasterData('modalState');
      }else {
        alert("Data Tidak Ada");
      }

  }, "json");
}

function listVehicleOnKm(idkm){
  console.log("idkm : ", idkm);
  $.post("<?php echo base_url() ?>maps/getlistinkm", {idkm : idkm}, function(response){
    console.log("response By KM Quick Count : ", response);
    var dataKosonganfix = response.dataKosongan;
    var dataMuatanfix   = response.dataMuatan;

    console.log("data kosongan : ", dataKosonganfix.length);
    console.log("data muatan : ", dataMuatanfix.length);

    var htmlkosongan = "";
    var htmlmuatan   = "";
    if (dataKosonganfix.length == 0) {
      $("#modalStateContentKosongan").html("Tidak ada data");
    }else {
      var lastcheckKmListQuickCount = "Last Check : "+dataKosonganfix[0].auto_last_update + " WITA";
      $("#modalKmListQuickCountTitle").html(response.kmsent);
      $("#lastcheckKmListQuickCount").html(lastcheckKmListQuickCount);
      htmlkosongan += '<table class="table table-striped">';
        htmlkosongan += '<thead>';
          htmlkosongan += '<tr>';
          htmlkosongan += '<th>No</th>';
            htmlkosongan += '<th>Vehicle</th>';
            htmlkosongan += '<th align="center">Engine</th>';
            htmlkosongan += '<th align="center">Speed (Kph)</th>';
            htmlkosongan += '<th>Coord</th>';
          htmlkosongan += '</tr>';
        htmlkosongan += '</thead>';
      for (var i = 0; i < dataKosonganfix.length; i++) {
          htmlkosongan += '<tr>';
            htmlkosongan += '<td style="font-size:12px;color:black"><span style="color:black;">'+(i+1)+'</span>';
            htmlkosongan += '<td style="font-size:12px;color:black"><span style="color:black;">'+dataKosonganfix[i].vehicle_no+ " " +dataKosonganfix[i].vehicle_name+'</span>';
            htmlkosongan += '<td align="center" style="font-size:12px;color:black"><span style="color:black;">'+dataKosonganfix[i].auto_last_engine+'</span>';
            htmlkosongan += '<td align="center" style="font-size:12px;color:black"><span style="color:black;">'+dataKosonganfix[i].auto_last_speed+'</span>';
            htmlkosongan += '<td style="font-size:12px;color:black"><span style="color:black;">'+dataKosonganfix[i].auto_last_lat+ "," +dataKosonganfix[i].auto_last_long+'</span>';
          htmlkosongan += '</tr>';
      }
      htmlkosongan += '</table>';
      $("#modalStateContentKosongan").html(htmlkosongan);
    }

    if (dataMuatanfix.length == 0) {
      $("#modalStateContentMuatan").html("Tidak ada data");
    }else {
      var lastcheckKmListQuickCount = "Last Check : "+dataMuatanfix[0].auto_last_update + " WITA";
      $("#modalKmListQuickCountTitle").html(response.kmsent);
      $("#lastcheckKmListQuickCount").html(lastcheckKmListQuickCount);
      htmlmuatan += '<table class="table table-striped">';
        htmlmuatan += '<thead>';
          htmlmuatan += '<tr>';
          htmlmuatan += '<th>No</th>';
            htmlmuatan += '<th>Vehicle</th>';
            htmlmuatan += '<th align="center">Engine</th>';
            htmlmuatan += '<th align="center">Speed (Kph)</th>';
            htmlmuatan += '<th>Coord</th>';
          htmlmuatan += '</tr>';
        htmlmuatan += '</thead>';
      for (var i = 0; i < dataMuatanfix.length; i++) {
          htmlmuatan += '<tr>';
            htmlmuatan += '<td style="font-size:12px;color:black"><span style="color:black;">'+(i+1)+'</span>';
            htmlmuatan += '<td style="font-size:12px;color:black"><span style="color:black;">'+dataMuatanfix[i].vehicle_no+ " " +dataMuatanfix[i].vehicle_name+'</span>';
            htmlmuatan += '<td align="center" style="font-size:12px;color:black"><span style="color:black;">'+dataMuatanfix[i].auto_last_engine+'</span>';
            htmlmuatan += '<td align="center" style="font-size:12px;color:black"><span style="color:black;">'+dataMuatanfix[i].auto_last_speed+'</span>';
            htmlmuatan += '<td style="font-size:12px;color:black"><span style="color:black;">'+dataMuatanfix[i].auto_last_lat+ "," +dataMuatanfix[i].auto_last_long+'</span>';
          htmlmuatan += '</tr>';
      }
      htmlmuatan += '</table>';
      $("#modalStateContentMuatan").html(htmlmuatan);
    }

      modalKmFromMasterData('modalKmListQuickCount');

  }, "json");
}






</script>

<?php
$key = $this->config->item("GOOGLE_MAP_API_KEY");
//$key = "AIzaSyAYe-6_UE3rUgSHelcU1piLI7DIBnZMid4";
// echo "key nya : ". $key;

if(isset($key) && $key != "") { ?>
  <script async defer src="https://maps.googleapis.com/maps/api/js?key=<?php echo $key;?>&libraries=visualization&callback=initMap" type="text/javascript" async></script>
  <?php } else { ?>
    <script src="http://maps.google.com/maps/api/js?sensor=false"></script>
    <?php } ?>
