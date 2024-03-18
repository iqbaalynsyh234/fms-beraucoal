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
            <!-- <h5>Maps</h5> -->
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
                  <option value="showTableMuatan2">Consolidated Data</option>
                  <option value="showTableRom">ROM</option>
                  <option value="showTablePort">PORT</option>
                </select>
              </div>

              <div class="col-md-3">
                <button type="button" name="button" class="btn btn-danger btn-sm" id="mapSetting" style="margin-left:2%;" onclick="customMymodal('modalMapSetting');">Pengaturan Map</button>
              </div>

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

            <div class="row">
              <div class="col-md-12">
                <div id="tableShowMuatan" style="width: 100%; max-height: 450px; display:none;">
                  <!-- display:none; -->

                  <div class="row">
                    <div class="col-md-6">
                      <p style="margin-left: 1%;">Kosongan || Muatan</p>
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
                            <div class="" id="labelvehicleonmuatan<?php echo $i ?>">KM <?php echo $i; ?></div>
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
                            <div class="" id="labelvehicleonmuatan<?php echo $i ?>">KM <?php echo $i; ?></div>
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
                            <div class="" id="labelvehicleonmuatan<?php echo $i ?>">KM <?php echo $i; ?></div>
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
                            <div class="" id="labelvehicleonmuatan<?php echo $i ?>">KM <?php echo $i; ?></div>
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
                            <div class="" id="labelvehicleonmuatan<?php echo $i ?>">KM <?php echo $i; ?></div>
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
                            <div class="" id="labelvehicleonmuatan<?php echo $i ?>">KM <?php echo $i; ?></div>
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

              <div class="col-md-12">
                <div id="tableShowRom" style="width: 100%; max-height: 400px; display:none;">
                  <div class="row">
                    <div class="col-md-2">
                      <table class="table table-bordered" style="font-size:12px;">
                          <tr>
                            <td>
                              <div class="btn-group">
                                <div type="button" class="" id="labelvehicleinrom_1"></div>
                                <div type="button" class="" id="vehicleinRom_1"></div>
                              </div>
                            </td>

                            <td>
                              <div class="btn-group">
                                <div type="button" class="btn btn-primary btn-lg" id="labelvehicleinrom_1_2_road"></div>
                                <div type="button" class="btn btn-primary btn-lg dropdown-toggle m-r-20" id="vehicleinRom_1_2_road"></div>
                              </div>
                            </td>

                            <td>
                              <div class="btn-group">
                                <div type="button" class="btn btn-primary btn-lg" id="labelvehicleinrom_2"></div>
                                <div type="button" class="btn btn-primary btn-lg dropdown-toggle m-r-20" id="vehicleinRom_2"></div>
                              </div>
                            </td>

                            <td>
                              <div class="btn-group">
                                <div type="button" class="btn btn-primary btn-lg" id="labelvehicleinrom_3"></div>
                                <div type="button" class="btn btn-primary btn-lg dropdown-toggle m-r-20" id="vehicleinRom_3"></div>
                              </div>
                            </td>
                          </tr>

                          <tr>
                            <td>
                              <div class="btn-group">
                                <div type="button" class="btn btn-primary btn-lg" id="labelvehicleinrom_3_4_road"></div>
                                <div type="button" class="btn btn-primary btn-lg dropdown-toggle m-r-20" id="vehicleinRom_3_4_road"></div>
                              </div>
                            </td>

                            <td>
                              <div class="btn-group">
                                <div type="button" class="btn btn-primary btn-lg" id="labelvehicleinrom_4"></div>
                                <div type="button" class="btn btn-primary btn-lg dropdown-toggle m-r-20" id="vehicleinRom_4"></div>
                              </div>
                            </td>

                            <td>
                              <div class="btn-group">
                                <div type="button" class="btn btn-primary btn-lg" id="labelvehicleinrom_6_road"></div>
                                <div type="button" class="btn btn-primary btn-lg dropdown-toggle m-r-20" id="vehicleinRom_6_road"></div>
                              </div>
                            </td>

                            <td>
                              <div class="btn-group">
                                <div type="button" class="btn btn-primary btn-lg" id="labelvehicleinrom_6"></div>
                                <div type="button" class="btn btn-primary btn-lg dropdown-toggle m-r-20" id="vehicleinRom_6"></div>
                              </div>
                            </td>
                          </tr>

                          <tr>
                            <td>
                              <div class="btn-group">
                                <div type="button" class="btn btn-primary btn-lg" id="labelvehicleinrom_7"></div>
                                <div type="button" class="btn btn-primary btn-lg dropdown-toggle m-r-20" id="vehicleinRom_7"></div>
                              </div>
                            </td>

                            <td>
                              <div class="btn-group">
                                <div type="button" class="btn btn-primary btn-lg" id="labelvehicleinrom_7_8_road"></div>
                                <div type="button" class="btn btn-primary btn-lg dropdown-toggle m-r-20" id="vehicleinRom_7_8_road"></div>
                              </div>
                            </td>

                            <td>
                              <div class="btn-group">
                                <div type="button" class="btn btn-primary btn-lg" id="labelvehicleinrom_8"></div>
                                <div type="button" class="btn btn-primary btn-lg dropdown-toggle m-r-20" id="vehicleinRom_8"></div>
                              </div>
                            </td>
                          </tr>
                      </table>
                    </div>
                  </div>
                </div>
              </div>

              <div class="col-md-12">
                <div id="tableShowPort" style="width: 100%; max-height: 400px; display:none;">
                  <div class="row">
                    <div class="col-md-2">
                      <table class="table table-bordered" style="font-size:12px;">
                          <tr>
                            <td>
                              <div class="btn-group">
                                <div type="button" class="btn btn-primary" id="labelvehicleinport_bbc"></div>
                                <div type="button" class="btn btn-primary dropdown-toggle m-r-20" id="vehicleinPort_bbc"></div>
                              </div>
                            </td>

                            <td>
                              <div class="btn-group">
                                <div type="button" class="btn btn-primary" id="labelvehicleinport_bib"></div>
                                <div type="button" class="btn btn-primary dropdown-toggle m-r-20" id="vehicleinPort_bib"></div>
                              </div>
                            </td>

                            <td>
                              <div class="btn-group">
                                <div type="button" class="btn btn-primary" id="labelvehicleinport_bir"></div>
                                <div type="button" class="btn btn-primary dropdown-toggle m-r-20" id="vehicleinPort_bir"></div>
                              </div>
                            </td>

                            <td>
                              <div class="btn-group">
                                <div type="button" class="btn btn-primary" id="labelvehicleinport_tia"></div>
                                <div type="button" class="btn btn-primary dropdown-toggle m-r-20" id="vehicleinPort_tia"></div>
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

                    <button type="button" name="button" id="activatesound" class="btn btn-flat btn-sm" title="Sound" onclick="activatesound();">
                      <span class="fa fa-volume-up"></span>
                    </button>

                    <button type="button" name="button" id="activatesound2" class="btn btn-flat btn-sm" title="Sound" onclick="activatesound2();" style="display:none;">
                      <span class="fa fa-volume-off"></span>
                    </button>
                  </header>
                  <div class="panel-body" id="bar-parent10">
                    <div id="realtimealertcontent"></div>
                      <table class="table">
                        <div id="summaryalertcontent" style="margin-top: 1%; overflow-x: auto; height: 270px; max-height: 270px;"></div>
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
        console.log("response : ", response);
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
    console.log("masuk gan");
    var companyid = $("#contractor").val();
    $.post("<?php echo base_url() ?>maps/getvehiclebycontractor", {companyid : companyid}, function(response){
      console.log("response : ", response);
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
  var car = "M17.402,0H5.643C2.526,0,0,3.467,0,6.584v34.804c0,3.116,2.526,5.644,5.643,5.644h11.759c3.116,0,5.644-2.527,5.644-5.644 V6.584C23.044,3.467,20.518,0,17.402,0z M22.057,14.188v11.665l-2.729,0.351v-4.806L22.057,14.188z M20.625,10.773 c-1.016,3.9-2.219,8.51-2.219,8.51H4.638l-2.222-8.51C2.417,10.773,11.3,7.755,20.625,10.773z M3.748,21.713v4.492l-2.73-0.349 V14.502L3.748,21.713z M1.018,37.938V27.579l2.73,0.343v8.196L1.018,37.938z M2.575,40.882l2.218-3.336h13.771l2.219,3.336H2.575z M19.328,35.805v-7.872l2.729-0.355v10.048L19.328,35.805z";
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

    // console.log("arraypointheatmap : ", arraypointheatmap);

    // for (var i = 0; i < obj.length; i++) {
    //   var vehiclePosition = new google.maps.LatLng(parseFloat(obj[i].auto_last_lat), parseFloat(obj[i].auto_last_long));
    //   dataposition.push(vehiclePosition);
    // }

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
    center: { lat: parseFloat(-3.7288), lng: parseFloat(115.6452)},
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

    var changegradient = document.createElement("button");
    changegradient.textContent = "Change Gradient";
    changegradient.classList.add("custom-map-control-button");
    map.controls[google.maps.ControlPosition.TOP_CENTER].push(changegradient);

     changegradient.addEventListener("click", () => {
      changeGradient();
    });

    intervalstart = setInterval(simultango, 10000);
}

  function simultango() {
    console.log("simultan Started");
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
    var latlngoverlay1                = new google.maps.LatLng(-3.752973, 115.627142);
    var latlngoverlay2                = new google.maps.LatLng(-3.741050, 115.651995);
    var jalanpcs1latlng1              = new google.maps.LatLng(-3.742245, 115.634781);
    var jalanpcs1latlng2              = new google.maps.LatLng(-3.731234, 115.658683);
    var jalanpcs2latlng1                = new google.maps.LatLng(-3.731293, 115.635119);
    var jalanpcs2latlng2                = new google.maps.LatLng(-3.719973, 115.658875);
    var jalanpcs3latlng1                = new google.maps.LatLng(-3.720716, 115.627392);
    var jalanpcs3latlng2                = new google.maps.LatLng(-3.709085, 115.650982);
    var jalanpcs4latlng1                = new google.maps.LatLng(-3.709082, 115.626623);
    var jalanpcs4latlng2                = new google.maps.LatLng(-3.696086, 115.650399);
    var jalanpcs5latlng1                = new google.maps.LatLng(-3.698382, 115.628647);
    var jalanpcs5latlng2                = new google.maps.LatLng(-3.686875, 115.650329);
    var jalanpcs5nikunglatlng1          = new google.maps.LatLng(-3.690207, 115.640326);
    var jalanpcs5nikunglatlng2          = new google.maps.LatLng(-3.684617, 115.651178);
    var jalanpcs5nikungpcs3barulatlng1  = new google.maps.LatLng(-3.685194, 115.642723);
    var jalanpcs5nikungpcs3barulatlng2  = new google.maps.LatLng(-3.679627, 115.653479);

    var jalanpcs5nikungpcs4barulatlng1  = new google.maps.LatLng(-3.679623, 115.643087);
    var jalanpcs5nikungpcs4barulatlng2  = new google.maps.LatLng(-3.672130, 115.658138);

    var jalanpcspatahancroplatlng1      = new google.maps.LatLng(-3.680263, 115.646667);
    var jalanpcspatahancroplatlng2      = new google.maps.LatLng(-3.675736, 115.651107);

    var jalanpcs5nikungpcs5barulatlng1  = new google.maps.LatLng(-3.672140, 115.643943);
    var jalanpcs5nikungpcs5barulatlng2  = new google.maps.LatLng(-3.665922, 115.657922);



    var jalanpcs6latlng1                = new google.maps.LatLng(-3.666083, 115.644821);
    var jalanpcs6latlng2                = new google.maps.LatLng(-3.661015, 115.656904);
    var jalanpcs7latlng1                = new google.maps.LatLng(-3.661060, 115.644165);
    var jalanpcs7latlng2                = new google.maps.LatLng(-3.653857, 115.655901);
    var jalanpcs8latlng1                = new google.maps.LatLng(-3.654903, 115.641802);
    var jalanpcs8latlng2                = new google.maps.LatLng(-3.648403, 115.653267);
    var jalanpcs9latlng1                = new google.maps.LatLng(-3.648949, 115.642282);
    var jalanpcs9latlng2                = new google.maps.LatLng(-3.642311, 115.653412);
    var jalanpcs10latlng1               = new google.maps.LatLng(-3.642658, 115.646624);
    var jalanpcs10latlng2               = new google.maps.LatLng(-3.636349, 115.655885);
    var jalanpcstekukankananataslatlng1 = new google.maps.LatLng(-3.642287, 115.647807);
    var jalanpcstekukankananataslatlng2 = new google.maps.LatLng(-3.624354, 115.659467);
    var jalanpcs15latlng1               = new google.maps.LatLng(-3.624377, 115.645157);
    var jalanpcs15latlng2               = new google.maps.LatLng(-3.613030, 115.657190);
    var jalanpcs16latlng1               = new google.maps.LatLng(-3.615390, 115.640438);
    var jalanpcs16latlng2               = new google.maps.LatLng(-3.604488, 115.664935);
    var jalanpcs17latlng1               = new google.maps.LatLng(-3.605948, 115.650892);
    var jalanpcs17latlng2               = new google.maps.LatLng(-3.591340, 115.675094);
    var jalanpcs18latlng1               = new google.maps.LatLng(-3.591364, 115.651680);
    var jalanpcs18latlng2               = new google.maps.LatLng(-3.579234, 115.666260);
    var jalanpcs19latlng1               = new google.maps.LatLng(-3.579299, 115.652807);
    var jalanpcs19latlng2               = new google.maps.LatLng(-3.574495, 115.659479);
    var jalanpcs20latlng1               = new google.maps.LatLng(-3.574582, 115.653795);
    var jalanpcs20latlng2               = new google.maps.LatLng(-3.571493, 115.657894);
    var jalanpcs21latlng1               = new google.maps.LatLng(-3.571526, 115.652658);
    var jalanpcs21latlng2               = new google.maps.LatLng(-3.568056, 115.656361);
    var jalanpcs22latlng1               = new google.maps.LatLng(-3.572094, 115.649348);
    var jalanpcs22latlng2               = new google.maps.LatLng(-3.566102, 115.657244);
    var jalanpcs23latlng1               = new google.maps.LatLng(-3.566103, 115.645197);
    var jalanpcs23latlng2               = new google.maps.LatLng(-3.558789, 115.661364);
    var jalanpcs24latlng1               = new google.maps.LatLng(-3.559233, 115.645378);
    var jalanpcs24latlng2               = new google.maps.LatLng(-3.554506, 115.656520);
    var jalanpcs25latlng1               = new google.maps.LatLng(-3.554939, 115.642562);
    var jalanpcs25latlng2               = new google.maps.LatLng(-3.547679, 115.657653);
    var jalanpcs26latlng1               = new google.maps.LatLng(-3.547848, 115.643332);
    var jalanpcs26latlng2               = new google.maps.LatLng(-3.540213, 115.657087);
    var jalanpcs27latlng1               = new google.maps.LatLng(-3.540904, 115.643003);
    var jalanpcs27latlng2               = new google.maps.LatLng(-3.535258, 115.656237);
    var jalanpcs28latlng1               = new google.maps.LatLng(-3.535323, 115.643353);
    var jalanpcs28latlng2               = new google.maps.LatLng(-3.527523, 115.656180);
    var jalanpcs29latlng1               = new google.maps.LatLng(-3.527815, 115.644625);
    var jalanpcs29latlng2               = new google.maps.LatLng(-3.523184, 115.654230);
    var jalanpcs30latlng1               = new google.maps.LatLng(-3.523197, 115.640709);
    var jalanpcs30latlng2               = new google.maps.LatLng(-3.516298, 115.655688);
    var jalanpcs32latlng1               = new google.maps.LatLng(-3.521122, 115.629392);
    var jalanpcs32latlng2               = new google.maps.LatLng(-3.514110, 115.644582);
    var jalanpcs33latlng1               = new google.maps.LatLng(-3.521091, 115.615832);
    var jalanpcs33latlng2               = new google.maps.LatLng(-3.514375, 115.629674);
    var jalanpcs34latlng1               = new google.maps.LatLng(-3.521108, 115.606516);
    var jalanpcs34latlng2               = new google.maps.LatLng(-3.515529, 115.620556);
    var jalanpcs36latlng1               = new google.maps.LatLng(-3.517714, 115.633010);
    var jalanpcs36latlng2               = new google.maps.LatLng(-3.506656, 115.648205);

    var imageportfix                    = "<?php echo base_url()?>assets/images/portbunati3_small.png";
    var imagejalanpcs1fix               = "<?php echo base_url()?>assets/images/pcs_1_new.png";
    var imagejalanpcs2fix              = "<?php echo base_url()?>assets/images/pcs_2_new.png";
    var imagejalanpcs3fix               = "<?php echo base_url()?>assets/images/pcs_3_new.png";
    var imagejalanpcs4fix               = "<?php echo base_url()?>assets/images/pcs_4_new.png";
    var imagejalanpcs5fix               = "<?php echo base_url()?>assets/images/pcs_5_new.png";
    var imagejalanpcs5nikungfix         = "<?php echo base_url()?>assets/images/pcs_2_baru.png";
    var imagejalanpcs5nikungpcs3barufix = "<?php echo base_url()?>assets/images/pcs_3_baru.png";
    var imagejalanpcs5nikungpcs4barufix = "<?php echo base_url()?>assets/images/pcs_4_baru.png";
    var imagejalanpcs5nikungpcs5barufix = "<?php echo base_url()?>assets/images/pcs_5_baru.png";

    var imagejalanpcspatahancrop5barufix = "<?php echo base_url()?>assets/images/patahan_tikungan5_baru_nobackground.png";

    var imagejalanpcs6fix                = "<?php echo base_url()?>assets/images/pcs_6_baru.png";
    var imagejalanpcs7fix                = "<?php echo base_url()?>assets/images/pcs_7_barufix.png";
    var imagejalanpcs8fix                = "<?php echo base_url()?>assets/images/pcs_8_baru.png";
    var imagejalanpcs9fix                = "<?php echo base_url()?>assets/images/pcs_9_baru.png";
    var imagejalanpcs10fix               = "<?php echo base_url()?>assets/images/pcs_10_baru.png";
    var imagejalanpcstekukankananatasfix = "<?php echo base_url()?>assets/images/pcs_tekukan_kanan_atas.png";
    var imagejalanpcs15fix               = "<?php echo base_url()?>assets/images/pcs_15_baru.png";
    var imagejalanpcs16fix               = "<?php echo base_url()?>assets/images/pcs_16_baru.png";
    var imagejalanpcs17fix               = "<?php echo base_url()?>assets/images/pcs_17_baru.png";
    var imagejalanpcs18fix               = "<?php echo base_url()?>assets/images/pcs_18_baru.png";
    var imagejalanpcs19fix               = "<?php echo base_url()?>assets/images/pcs_19_baru.png";
    var imagejalanpcs20fix               = "<?php echo base_url()?>assets/images/pcs_20_baru.png";
    var imagejalanpcs21fix               = "<?php echo base_url()?>assets/images/pcs_21_baru.png";
    var imagejalanpcs22fix               = "<?php echo base_url()?>assets/images/pcs_22_baru.png";
    var imagejalanpcs23fix               = "<?php echo base_url()?>assets/images/pcs_23_baru.png";
    var imagejalanpcs24fix               = "<?php echo base_url()?>assets/images/pcs_24_baru.png";
    var imagejalanpcs25fix               = "<?php echo base_url()?>assets/images/pcs_25_baru.png";
    var imagejalanpcs26fix               = "<?php echo base_url()?>assets/images/pcs_26_baru.png";
    var imagejalanpcs27fix               = "<?php echo base_url()?>assets/images/pcs_27_baru2.png";
    var imagejalanpcs28fix               = "<?php echo base_url()?>assets/images/pcs_28_baru.png";
    var imagejalanpcs29fix               = "<?php echo base_url()?>assets/images/pcs_29_baru.png";
    var imagejalanpcs30fix               = "<?php echo base_url()?>assets/images/pcs_30_baru.png";
    var imagejalanpcs32fix               = "<?php echo base_url()?>assets/images/pcs_32_baru.png";
    var imagejalanpcs33fix               = "<?php echo base_url()?>assets/images/pcs_33_baru.png";
    var imagejalanpcs34fix               = "<?php echo base_url()?>assets/images/pcs_34_baru.png";
    var imagejalanpcs36fix               = "<?php echo base_url()?>assets/images/pcs_36_baru.png";

    var imageport                  = new google.maps.LatLngBounds(latlngoverlay1, latlngoverlay2);
    var imagejalanpcs1             = new google.maps.LatLngBounds(jalanpcs1latlng1, jalanpcs1latlng2);
    var imagejalanpcs2               = new google.maps.LatLngBounds(jalanpcs2latlng1, jalanpcs2latlng2);
    var imagejalanpcs3               = new google.maps.LatLngBounds(jalanpcs3latlng1, jalanpcs3latlng2);
    var imagejalanpcs4               = new google.maps.LatLngBounds(jalanpcs4latlng1, jalanpcs4latlng2);
    var imagejalanpcs5               = new google.maps.LatLngBounds(jalanpcs5latlng1, jalanpcs5latlng2);
    var imagejalanpcs5nikung         = new google.maps.LatLngBounds(jalanpcs5nikunglatlng1, jalanpcs5nikunglatlng2);
    var imagejalanpcs5nikungpcs3baru = new google.maps.LatLngBounds(jalanpcs5nikungpcs3barulatlng1, jalanpcs5nikungpcs3barulatlng2);
    var imagejalanpcs5nikungpcs4baru = new google.maps.LatLngBounds(jalanpcs5nikungpcs4barulatlng1, jalanpcs5nikungpcs4barulatlng2);
    var imagejalanpcs5nikungpcs5baru = new google.maps.LatLngBounds(jalanpcs5nikungpcs5barulatlng1, jalanpcs5nikungpcs5barulatlng2);

    var imagejalanpcspatahancrop    = new google.maps.LatLngBounds(jalanpcspatahancroplatlng1, jalanpcspatahancroplatlng2);


    var imagejalanpcs6                = new google.maps.LatLngBounds(jalanpcs6latlng1, jalanpcs6latlng2);
    var imagejalanpcs7                = new google.maps.LatLngBounds(jalanpcs7latlng1, jalanpcs7latlng2);
    var imagejalanpcs8                = new google.maps.LatLngBounds(jalanpcs8latlng1, jalanpcs8latlng2);
    var imagejalanpcs9                = new google.maps.LatLngBounds(jalanpcs9latlng1, jalanpcs9latlng2);
    var imagejalanpcs10               = new google.maps.LatLngBounds(jalanpcs10latlng1, jalanpcs10latlng2);
    var imagejalanpcstekukankananatas = new google.maps.LatLngBounds(jalanpcstekukankananataslatlng1, jalanpcstekukankananataslatlng2);
    var imagejalanpcs15               = new google.maps.LatLngBounds(jalanpcs15latlng1, jalanpcs15latlng2);
    var imagejalanpcs16               = new google.maps.LatLngBounds(jalanpcs16latlng1, jalanpcs16latlng2);
    var imagejalanpcs17               = new google.maps.LatLngBounds(jalanpcs17latlng1, jalanpcs17latlng2);
    var imagejalanpcs18               = new google.maps.LatLngBounds(jalanpcs18latlng1, jalanpcs18latlng2);
    var imagejalanpcs19               = new google.maps.LatLngBounds(jalanpcs19latlng1, jalanpcs19latlng2);
    var imagejalanpcs20               = new google.maps.LatLngBounds(jalanpcs20latlng1, jalanpcs20latlng2);
    var imagejalanpcs21               = new google.maps.LatLngBounds(jalanpcs21latlng1, jalanpcs21latlng2);
    var imagejalanpcs22               = new google.maps.LatLngBounds(jalanpcs22latlng1, jalanpcs22latlng2);
    var imagejalanpcs23               = new google.maps.LatLngBounds(jalanpcs23latlng1, jalanpcs23latlng2);
    var imagejalanpcs24               = new google.maps.LatLngBounds(jalanpcs24latlng1, jalanpcs24latlng2);
    var imagejalanpcs25               = new google.maps.LatLngBounds(jalanpcs25latlng1, jalanpcs25latlng2);
    var imagejalanpcs26               = new google.maps.LatLngBounds(jalanpcs26latlng1, jalanpcs26latlng2);
    var imagejalanpcs27               = new google.maps.LatLngBounds(jalanpcs27latlng1, jalanpcs27latlng2);
    var imagejalanpcs28               = new google.maps.LatLngBounds(jalanpcs28latlng1, jalanpcs28latlng2);
    var imagejalanpcs29               = new google.maps.LatLngBounds(jalanpcs29latlng1, jalanpcs29latlng2);
    var imagejalanpcs30               = new google.maps.LatLngBounds(jalanpcs30latlng1, jalanpcs30latlng2);
    var imagejalanpcs32               = new google.maps.LatLngBounds(jalanpcs32latlng1, jalanpcs32latlng2);
    var imagejalanpcs33               = new google.maps.LatLngBounds(jalanpcs33latlng1, jalanpcs33latlng2);
    var imagejalanpcs34               = new google.maps.LatLngBounds(jalanpcs34latlng1, jalanpcs34latlng2);
    var imagejalanpcs36               = new google.maps.LatLngBounds(jalanpcs36latlng1, jalanpcs36latlng2);

    Overlay                       = new google.maps.GroundOverlay(imageportfix, imageport);
    Overlayjalanpcs_1             = new google.maps.GroundOverlay(imagejalanpcs1fix, imagejalanpcs1);
    Overlayjalanpcs_2               = new google.maps.GroundOverlay(imagejalanpcs2fix, imagejalanpcs2);
    Overlayjalanpcs_3               = new google.maps.GroundOverlay(imagejalanpcs3fix, imagejalanpcs3);
    Overlayjalanpcs_4               = new google.maps.GroundOverlay(imagejalanpcs4fix, imagejalanpcs4);
    Overlayjalanpcs_5               = new google.maps.GroundOverlay(imagejalanpcs5fix, imagejalanpcs5);
    Overlayjalanpcs_5nikung         = new google.maps.GroundOverlay(imagejalanpcs5nikungfix, imagejalanpcs5nikung);
    Overlayjalanpcs_5nikungpcs3baru = new google.maps.GroundOverlay(imagejalanpcs5nikungpcs3barufix, imagejalanpcs5nikungpcs3baru);
    Overlayjalanpcs_5nikungpcs4baru = new google.maps.GroundOverlay(imagejalanpcs5nikungpcs4barufix, imagejalanpcs5nikungpcs4baru);
    Overlayjalanpcs_5nikungpcs5baru = new google.maps.GroundOverlay(imagejalanpcs5nikungpcs5barufix, imagejalanpcs5nikungpcs5baru);

    Overlayjalanpcs_patahancrop = new google.maps.GroundOverlay(imagejalanpcspatahancrop5barufix, imagejalanpcspatahancrop);


    Overlayjalanpcs_6                = new google.maps.GroundOverlay(imagejalanpcs6fix, imagejalanpcs6);
    Overlayjalanpcs_7                = new google.maps.GroundOverlay(imagejalanpcs7fix, imagejalanpcs7);
    Overlayjalanpcs_8                = new google.maps.GroundOverlay(imagejalanpcs8fix, imagejalanpcs8);
    Overlayjalanpcs_9                = new google.maps.GroundOverlay(imagejalanpcs9fix, imagejalanpcs9);
    Overlayjalanpcs_10               = new google.maps.GroundOverlay(imagejalanpcs10fix, imagejalanpcs10);
    // ini nanti Overlayjalanpcs_11               = new google.maps.GroundOverlay(imagejalanpcs11fix, imagejalanpcs11);
    // ini nanti Overlayjalanpcs_12               = new google.maps.GroundOverlay(imagejalanpcs12fix, imagejalanpcs12);
    Overlayjalanpcs_tekukankananatas = new google.maps.GroundOverlay(imagejalanpcstekukankananatasfix, imagejalanpcstekukankananatas);
    Overlayjalanpcs_15               = new google.maps.GroundOverlay(imagejalanpcs15fix, imagejalanpcs15);
    Overlayjalanpcs_16               = new google.maps.GroundOverlay(imagejalanpcs16fix, imagejalanpcs16);
    Overlayjalanpcs_17               = new google.maps.GroundOverlay(imagejalanpcs17fix, imagejalanpcs17);
    Overlayjalanpcs_18               = new google.maps.GroundOverlay(imagejalanpcs18fix, imagejalanpcs18);
    Overlayjalanpcs_19               = new google.maps.GroundOverlay(imagejalanpcs19fix, imagejalanpcs19);
    Overlayjalanpcs_20               = new google.maps.GroundOverlay(imagejalanpcs20fix, imagejalanpcs20);
    Overlayjalanpcs_21               = new google.maps.GroundOverlay(imagejalanpcs21fix, imagejalanpcs21);
    Overlayjalanpcs_22               = new google.maps.GroundOverlay(imagejalanpcs22fix, imagejalanpcs22);
    Overlayjalanpcs_23               = new google.maps.GroundOverlay(imagejalanpcs23fix, imagejalanpcs23);
    Overlayjalanpcs_24               = new google.maps.GroundOverlay(imagejalanpcs24fix, imagejalanpcs24);
    Overlayjalanpcs_25               = new google.maps.GroundOverlay(imagejalanpcs25fix, imagejalanpcs25);
    Overlayjalanpcs_26               = new google.maps.GroundOverlay(imagejalanpcs26fix, imagejalanpcs26);
    Overlayjalanpcs_27               = new google.maps.GroundOverlay(imagejalanpcs27fix, imagejalanpcs27);
    Overlayjalanpcs_28               = new google.maps.GroundOverlay(imagejalanpcs28fix, imagejalanpcs28);
    Overlayjalanpcs_29               = new google.maps.GroundOverlay(imagejalanpcs29fix, imagejalanpcs29);
    Overlayjalanpcs_30               = new google.maps.GroundOverlay(imagejalanpcs30fix, imagejalanpcs30);
    Overlayjalanpcs_32               = new google.maps.GroundOverlay(imagejalanpcs32fix, imagejalanpcs32);
    Overlayjalanpcs_33               = new google.maps.GroundOverlay(imagejalanpcs33fix, imagejalanpcs33);
    Overlayjalanpcs_34               = new google.maps.GroundOverlay(imagejalanpcs34fix, imagejalanpcs34);
    Overlayjalanpcs_36               = new google.maps.GroundOverlay(imagejalanpcs36fix, imagejalanpcs36);

    Overlay.setMap(map);
    Overlayjalanpcs_1.setMap(map);
    Overlayjalanpcs_2.setMap(map);
    Overlayjalanpcs_3.setMap(map);
    Overlayjalanpcs_4.setMap(map);
    Overlayjalanpcs_5.setMap(map);
    Overlayjalanpcs_5nikung.setMap(map);
    Overlayjalanpcs_5nikungpcs3baru.setMap(map);
    Overlayjalanpcs_5nikungpcs4baru.setMap(map);
    Overlayjalanpcs_5nikungpcs5baru.setMap(map);
    Overlayjalanpcs_patahancrop.setMap(map);
    Overlayjalanpcs_6.setMap(map);
    Overlayjalanpcs_7.setMap(map);
    Overlayjalanpcs_8.setMap(map);
    Overlayjalanpcs_9.setMap(map);
    Overlayjalanpcs_10.setMap(map);
    Overlayjalanpcs_tekukankananatas.setMap(map);
    Overlayjalanpcs_15.setMap(map);
    Overlayjalanpcs_16.setMap(map);
    Overlayjalanpcs_17.setMap(map);
    Overlayjalanpcs_18.setMap(map);
    Overlayjalanpcs_19.setMap(map);
    Overlayjalanpcs_20.setMap(map);
    Overlayjalanpcs_21.setMap(map);
    Overlayjalanpcs_22.setMap(map);
    Overlayjalanpcs_23.setMap(map);
    Overlayjalanpcs_24.setMap(map);
    Overlayjalanpcs_25.setMap(map);
    Overlayjalanpcs_26.setMap(map);
    Overlayjalanpcs_27.setMap(map);
    Overlayjalanpcs_28.setMap(map);
    Overlayjalanpcs_29.setMap(map);
    Overlayjalanpcs_30.setMap(map);
    Overlayjalanpcs_32.setMap(map);
    Overlayjalanpcs_33.setMap(map);
    Overlayjalanpcs_34.setMap(map);
    Overlayjalanpcs_36.setMap(map);

    // ARRAY overlay
    overlaysarray.push(Overlay);
    overlaysarray.push(Overlayjalanpcs_1);
    overlaysarray.push(Overlayjalanpcs_2);
    overlaysarray.push(Overlayjalanpcs_3);
    overlaysarray.push(Overlayjalanpcs_4);
    overlaysarray.push(Overlayjalanpcs_5);
    overlaysarray.push(Overlayjalanpcs_5nikung);
    overlaysarray.push(Overlayjalanpcs_5nikungpcs3baru);
    overlaysarray.push(Overlayjalanpcs_5nikungpcs4baru);
    overlaysarray.push(Overlayjalanpcs_5nikungpcs5baru);
    overlaysarray.push(Overlayjalanpcs_patahancrop);


    overlaysarray.push(Overlayjalanpcs_6);
    overlaysarray.push(Overlayjalanpcs_7);
    overlaysarray.push(Overlayjalanpcs_8);
    overlaysarray.push(Overlayjalanpcs_9);
    overlaysarray.push(Overlayjalanpcs_10);
    overlaysarray.push(Overlayjalanpcs_tekukankananatas);
    overlaysarray.push(Overlayjalanpcs_15);
    overlaysarray.push(Overlayjalanpcs_16);
    overlaysarray.push(Overlayjalanpcs_17);
    overlaysarray.push(Overlayjalanpcs_18);
    overlaysarray.push(Overlayjalanpcs_19);
    overlaysarray.push(Overlayjalanpcs_20);
    overlaysarray.push(Overlayjalanpcs_21);
    overlaysarray.push(Overlayjalanpcs_22);
    overlaysarray.push(Overlayjalanpcs_23);
    overlaysarray.push(Overlayjalanpcs_24);
    overlaysarray.push(Overlayjalanpcs_25);
    overlaysarray.push(Overlayjalanpcs_26);
    overlaysarray.push(Overlayjalanpcs_27);
    overlaysarray.push(Overlayjalanpcs_28);
    overlaysarray.push(Overlayjalanpcs_29);
    overlaysarray.push(Overlayjalanpcs_30);
    overlaysarray.push(Overlayjalanpcs_32);
    overlaysarray.push(Overlayjalanpcs_33);
    overlaysarray.push(Overlayjalanpcs_34);
    overlaysarray.push(Overlayjalanpcs_36);
  }else {
    console.log(map.getMapTypeId());
    map.setMapTypeId((map.getMapTypeId() === 'hidden') ? google.maps.MapTypeId.satellite : 'hidden');

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
  soundisactive = 1;
}

function showTableMuatan(sikon){
  $("#heatmapbutton").show();
  $("#kosonganbutton").show();
  $("#mapShow").hide();
  $("#realtimealertshowhide").hide();
  $("#tableShowKosongan").hide();
  $("#tableShowPort").hide();
  $("#tableShowRom").hide();
  $("#tableShowMuatan").show();
  soundisactive = 0;

  $.post("<?php echo base_url() ?>maps/dataconsolidated", {}, function(response){
    console.log("response : ", response);
    var lastupdate        = response.datamuatan[0].minidashboard_created_date;
    if (sikon == 1) {
      $("#lastupdateconsolidated").html("Last Update : "+lastupdate);
      $("#lastupdateconsolidated").show();
    }else {
      $("#lastupdateconsolidated").hide();
    }

    var datamuatan        = JSON.parse(response.datamuatan[0].minidashboard_json);
    var datakosongan      = JSON.parse(response.datakosongan[0].minidashboard_json);
    var arraydatamuatan   = Object.keys(datamuatan).map((key) => [String(key), datamuatan[key]]);
    var arraydatakosongan = Object.keys(datakosongan).map((key) => [String(key), datakosongan[key]]);
    var sizemuatan        = arraydatamuatan.length;
    var sizekosongan      = arraydatakosongan.length;
    // console.log("datamuatan : ", datamuatan);
    // console.log("arraydatamuatan : ", arraydatamuatan[0][1]);
      for (var i = 0; i < sizekosongan; i++) {
        var jumlahfix = arraydatakosongan[i][1]; //arraydatamuatan[i][1]; //30+i; //arraydatamuatan[i][1];
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
  },"json");
}

function showTableRom(){
  $("#heatmapbutton").show();
  $("#mapShow").hide();
  $("#realtimealertshowhide").hide();
  $("#tableShowMuatan").hide();
  $("#tableShowKosongan").hide();
  $("#tableShowPort").hide();
  $("#tableShowRom").show();
  soundisactive = 0;

  $.post("<?php echo base_url() ?>maps/vehicleonrom", {}, function(response){
    console.log("response : ", response);
    var datainrom      = JSON.parse(response.data[0].minidashboard_json);
    var arraydatainrom = Object.keys(datainrom).map((key) => [String(key), datainrom[key]]);
    var size           = arraydatainrom.length;

    var labelvehicleinrom_1        = "ROM 01";
    var vehicleinRom_1             = arraydatainrom[0][1];
      $("#labelvehicleinrom_1").html(labelvehicleinrom_1);
      $("#vehicleinRom_1").html(vehicleinRom_1);
        if (vehicleinRom_1 > 30 && vehicleinRom_1 < 51) {
          $("#labelvehicleinrom_1").addClass('btn btn-warning btn-lg');
          $("#vehicleinRom_1").addClass('btn btn-warning btn-lg dropdown-toggle m-r-20');
        }else if(vehicleinRom_1 > 50){
          $("#labelvehicleinrom_1").addClass('btn btn-danger btn-lg');
          $("#vehicleinRom_1").addClass('btn btn-danger btn-lg dropdown-toggle m-r-20');
        }else {
          $("#labelvehicleinrom_1").addClass('btn btn-primary btn-lg');
          $("#vehicleinRom_1").addClass('btn btn-primary btn-lg dropdown-toggle m-r-20');
        }

    var labelvehicleinrom_1_2_road = "ROM 01/02 ROAD";
    var vehicleinRom_1_2_road      = arraydatainrom[1][1];
      $("#labelvehicleinrom_1_2_road").html(labelvehicleinrom_1_2_road);
      $("#vehicleinRom_1_2_road").html(vehicleinRom_1_2_road);
        if (vehicleinRom_1_2_road > 30 && vehicleinRom_1_2_road < 51) {
          $("#labelvehicleinrom_1_2_road").addClass('btn btn-warning btn-lg');
          $("#vehicleinRom_1_2_road").addClass('btn btn-warning btn-lg dropdown-toggle m-r-20');
        }else if(vehicleinRom_1_2_road > 50){
          $("#labelvehicleinrom_1_2_road").addClass('btn btn-danger btn-lg');
          $("#vehicleinRom_1_2_road").addClass('btn btn-danger btn-lg dropdown-toggle m-r-20');
        }else {
          $("#labelvehicleinrom_1_2_road").addClass('btn btn-primary btn-lg');
          $("#vehicleinRom_1_2_road").addClass('btn btn-primary btn-lg dropdown-toggle m-r-20');
        }

    var labelvehicleinrom_2        = "ROM 02";
    var vehicleinRom_2             = arraydatainrom[2][1];
      $("#labelvehicleinrom_2").html(labelvehicleinrom_2);
      $("#vehicleinRom_2").html(vehicleinRom_2);
        if (vehicleinRom_2 > 30 && vehicleinRom_2 < 51) {
          $("#labelvehicleinrom_2").addClass('btn btn-warning btn-lg');
          $("#vehicleinRom_2").addClass('btn btn-warning btn-lg dropdown-toggle m-r-20');
        }else if(vehicleinRom_2 > 50){
          $("#labelvehicleinrom_2").addClass('btn btn-danger btn-lg');
          $("#vehicleinRom_2").addClass('btn btn-danger btn-lg dropdown-toggle m-r-20');
        }else {
          $("#labelvehicleinrom_2").addClass('btn btn-primary btn-lg');
          $("#vehicleinRom_2").addClass('btn btn-primary btn-lg dropdown-toggle m-r-20');
        }

    var labelvehicleinrom_3        = "ROM 03";
    var vehicleinRom_3             = arraydatainrom[3][1];
      $("#labelvehicleinrom_3").html(labelvehicleinrom_3);
      $("#vehicleinRom_3").html(vehicleinRom_3);
        if (vehicleinRom_3 > 30 && vehicleinRom_3 < 51) {
          $("#labelvehicleinrom_3").addClass('btn btn-warning btn-lg');
          $("#vehicleinRom_3").addClass('btn btn-warning btn-lg dropdown-toggle m-r-20');
        }else if(vehicleinRom_3 > 50){
          $("#labelvehicleinrom_3").addClass('btn btn-danger btn-lg');
          $("#vehicleinRom_3").addClass('btn btn-danger btn-lg dropdown-toggle m-r-20');
        }else {
          $("#labelvehicleinrom_3").addClass('btn btn-primary btn-lg');
          $("#vehicleinRom_3").addClass('btn btn-primary btn-lg dropdown-toggle m-r-20');
        }

    var labelvehicleinrom_3_4_road = "ROM 03/04 ROAD";
    var vehicleinRom_3_4_road      = arraydatainrom[4][1];
      $("#labelvehicleinrom_3_4_road").html(labelvehicleinrom_3_4_road);
      $("#vehicleinRom_3_4_road").html(vehicleinRom_3_4_road);
        if (vehicleinRom_3_4_road > 30 && vehicleinRom_3_4_road < 51) {
          $("#labelvehicleinrom_3_4_road").addClass('btn btn-warning btn-lg');
          $("#vehicleinRom_3_4_road").addClass('btn btn-warning btn-lg dropdown-toggle m-r-20');
        }else if(vehicleinRom_3_4_road > 50){
          $("#labelvehicleinrom_3_4_road").addClass('btn btn-danger btn-lg');
          $("#vehicleinRom_3_4_road").addClass('btn btn-danger btn-lg dropdown-toggle m-r-20');
        }else {
          $("#labelvehicleinrom_3_4_road").addClass('btn btn-primary btn-lg');
          $("#vehicleinRom_3_4_road").addClass('btn btn-primary btn-lg dropdown-toggle m-r-20');
        }

    var labelvehicleinrom_4        = "ROM 04";
    var vehicleinRom_4             = arraydatainrom[5][1];
      $("#labelvehicleinrom_4").html(labelvehicleinrom_4);
      $("#vehicleinRom_4").html(vehicleinRom_4);
        if (vehicleinRom_4 > 30 && vehicleinRom_4 < 51) {
          $("#labelvehicleinrom_4").addClass('btn btn-warning btn-lg');
          $("#vehicleinRom_4").addClass('btn btn-warning btn-lg dropdown-toggle m-r-20');
        }else if(vehicleinRom_4 > 50){
          $("#labelvehicleinrom_4").addClass('btn btn-danger btn-lg');
          $("#vehicleinRom_4").addClass('btn btn-danger btn-lg dropdown-toggle m-r-20');
        }else {
          $("#labelvehicleinrom_4").addClass('btn btn-primary btn-lg');
          $("#vehicleinRom_4").addClass('btn btn-primary btn-lg dropdown-toggle m-r-20');
        }

    var labelvehicleinrom_6_road   = "ROM 06 ROAD";
    var vehicleinRom_6_road        = arraydatainrom[6][1];
      $("#labelvehicleinrom_6_road").html(labelvehicleinrom_6_road);
      $("#vehicleinRom_6_road").html(vehicleinRom_6_road);
        if (vehicleinRom_6_road > 30 && vehicleinRom_6_road < 51) {
          $("#labelvehicleinrom_6_road").addClass('btn btn-warning btn-lg');
          $("#vehicleinRom_6_road").addClass('btn btn-warning btn-lg dropdown-toggle m-r-20');
        }else if(vehicleinRom_6_road > 50){
          $("#labelvehicleinrom_6_road").addClass('btn btn-danger btn-lg');
          $("#vehicleinRom_6_road").addClass('btn btn-danger btn-lg dropdown-toggle m-r-20');
        }else {
          $("#labelvehicleinrom_6_road").addClass('btn btn-primary btn-lg');
          $("#vehicleinRom_6_road").addClass('btn btn-primary btn-lg dropdown-toggle m-r-20');
        }

    var labelvehicleinrom_6        = "ROM 06";
    var vehicleinRom_6             = arraydatainrom[7][1];
      $("#labelvehicleinrom_6").html(labelvehicleinrom_6);
      $("#vehicleinRom_6").html(vehicleinRom_6);
        if (vehicleinRom_6 > 30 && vehicleinRom_6 < 51) {
          $("#labelvehicleinrom_6").addClass('btn btn-warning btn-lg');
          $("#vehicleinRom_6").addClass('btn btn-warning btn-lg dropdown-toggle m-r-20');
        }else if(vehicleinRom_6 > 50){
          $("#labelvehicleinrom_6").addClass('btn btn-danger btn-lg');
          $("#vehicleinRom_6").addClass('btn btn-danger btn-lg dropdown-toggle m-r-20');
        }else {
          $("#labelvehicleinrom_6").addClass('btn btn-primary btn-lg');
          $("#vehicleinRom_6").addClass('btn btn-primary btn-lg dropdown-toggle m-r-20');
        }

    var labelvehicleinrom_7        = "ROM 07";
    var vehicleinRom_7             = arraydatainrom[8][1];
      $("#labelvehicleinrom_7").html(labelvehicleinrom_7);
      $("#vehicleinRom_7").html(vehicleinRom_7);
        if (vehicleinRom_7 > 30 && vehicleinRom_7 < 51) {
          $("#labelvehicleinrom_7").addClass('btn btn-warning btn-lg');
          $("#vehicleinRom_7").addClass('btn btn-warning btn-lg dropdown-toggle m-r-20');
        }else if(vehicleinRom_7 > 50){
          $("#labelvehicleinrom_7").addClass('btn btn-danger btn-lg');
          $("#vehicleinRom_7").addClass('btn btn-danger btn-lg dropdown-toggle m-r-20');
        }else {
          $("#labelvehicleinrom_7").addClass('btn btn-primary btn-lg');
          $("#vehicleinRom_7").addClass('btn btn-primary btn-lg dropdown-toggle m-r-20');
        }

    var labelvehicleinrom_7_8_road = "ROM 07/08 ROAD";
    var vehicleinRom_7_8_road      = arraydatainrom[9][1];
      $("#labelvehicleinrom_7_8_road").html(labelvehicleinrom_7_8_road);
      $("#vehicleinRom_7_8_road").html(vehicleinRom_7_8_road);
        if (vehicleinRom_7_8_road > 30 && vehicleinRom_7_8_road < 51) {
          $("#labelvehicleinrom_7_8_road").addClass('btn btn-warning btn-lg');
          $("#vehicleinRom_7_8_road").addClass('btn btn-warning btn-lg dropdown-toggle m-r-20');
        }else if(vehicleinRom_7_8_road > 50){
          $("#labelvehicleinrom_7_8_road").addClass('btn btn-danger btn-lg');
          $("#vehicleinRom_7_8_road").addClass('btn btn-danger btn-lg dropdown-toggle m-r-20');
        }else {
          $("#labelvehicleinrom_7_8_road").addClass('btn btn-primary btn-lg');
          $("#vehicleinRom_7_8_road").addClass('btn btn-primary btn-lg dropdown-toggle m-r-20');
        }

    var labelvehicleinrom_8        = "ROM 08";
    var vehicleinRom_8             = arraydatainrom[10][1];
    $("#labelvehicleinrom_8").html(labelvehicleinrom_8);
    $("#vehicleinRom_8").html(vehicleinRom_8);
      if (vehicleinRom_8 > 30 && vehicleinRom_8 < 51) {
        $("#labelvehicleinrom_8").addClass('btn btn-warning btn-lg');
        $("#vehicleinRom_8").addClass('btn btn-warning btn-lg dropdown-toggle m-r-20');
      }else if(vehicleinRom_8 > 50){
        $("#labelvehicleinrom_8").addClass('btn btn-danger btn-lg');
        $("#vehicleinRom_8").addClass('btn btn-danger btn-lg dropdown-toggle m-r-20');
      }else {
        $("#labelvehicleinrom_8").addClass('btn btn-primary btn-lg');
        $("#vehicleinRom_8").addClass('btn btn-primary btn-lg dropdown-toggle m-r-20');
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
  $("#tableShowPort").show();
  soundisactive = 0;

  $.post("<?php echo base_url() ?>maps/vehicleonport", {}, function(response){
    console.log("response : ", response);
    var datainport      = JSON.parse(response.data[0].minidashboard_json);
    var arraydatainport = Object.keys(datainport).map((key) => [String(key), datainport[key]]);
    var size            = arraydatainport.length;

    var labelvehicleinport_bbc        = "PORT BBC";
    var vehicleinPort_bbc             = arraydatainport[0][1];
      $("#labelvehicleinport_bbc").html(labelvehicleinport_bbc);
      $("#vehicleinPort_bbc").html(vehicleinPort_bbc);
        if (vehicleinPort_bbc > 30 && vehicleinPort_bbc < 51) {
          $("#labelvehicleinport_bbc").addClass('btn btn-warning btn-lg');
          $("#vehicleinPort_bbc").addClass('btn btn-warning btn-lg dropdown-toggle m-r-20');
        }else if(vehicleinPort_bbc > 50){
          $("#labelvehicleinport_bbc").addClass('btn btn-danger btn-lg');
          $("#vehicleinPort_bbc").addClass('btn btn-danger btn-lg dropdown-toggle m-r-20');
        }else {
          $("#labelvehicleinport_bbc").addClass('btn btn-primary btn-lg');
          $("#vehicleinPort_bbc").addClass('btn btn-primary btn-lg dropdown-toggle m-r-20');
        }

    var labelvehicleinport_bib       = "PORT BIB";
    var vehicleinPort_bib             = arraydatainport[1][1];
      $("#labelvehicleinport_bib").html(labelvehicleinport_bib);
      $("#vehicleinPort_bib").html(vehicleinPort_bib);
        if (vehicleinPort_bib > 30 && vehicleinPort_bib < 51) {
          $("#labelvehicleinport_bib").addClass('btn btn-warning btn-lg');
          $("#vehicleinPort_bib").addClass('btn btn-warning btn-lg dropdown-toggle m-r-20');
        }else if(vehicleinPort_bib > 50){
          $("#labelvehicleinport_bib").addClass('btn btn-danger btn-lg');
          $("#vehicleinPort_bib").addClass('btn btn-danger btn-lg dropdown-toggle m-r-20');
        }else {
          $("#labelvehicleinport_bib").addClass('btn btn-primary btn-lg');
          $("#vehicleinPort_bib").addClass('btn btn-primary btn-lg dropdown-toggle m-r-20');
        }

    var labelvehicleinport_bir = "PORT BIR";
    var vehicleinPort_bir      = arraydatainport[2][1];
      $("#labelvehicleinport_bir").html(labelvehicleinport_bir);
      $("#vehicleinPort_bir").html(vehicleinPort_bir);
        if (vehicleinPort_bir > 30 && vehicleinPort_bir < 51) {
          $("#labelvehicleinport_bir").addClass('btn btn-warning btn-lg');
          $("#vehicleinPort_bir").addClass('btn btn-warning btn-lg dropdown-toggle m-r-20');
        }else if(vehicleinPort_bir > 50){
          $("#labelvehicleinport_bir").addClass('btn btn-danger btn-lg');
          $("#vehicleinPort_bir").addClass('btn btn-danger btn-lg dropdown-toggle m-r-20');
        }else {
          $("#labelvehicleinport_bir").addClass('btn btn-primary btn-lg');
          $("#vehicleinPort_bir").addClass('btn btn-primary btn-lg dropdown-toggle m-r-20');
        }

    var labelvehicleinport_tia = "PORT TIA";
    var vehicleinPort_tia      = arraydatainport[3][1];
      $("#labelvehicleinport_tia").html(labelvehicleinport_tia);
      $("#vehicleinPort_tia").html(vehicleinPort_tia);
        if (vehicleinPort_tia > 30 && vehicleinPort_tia < 51) {
          $("#labelvehicleinport_tia").addClass('btn btn-warning btn-lg');
          $("#vehicleinPort_tia").addClass('btn btn-warning btn-lg dropdown-toggle m-r-20');
        }else if(vehicleinPort_tia > 50){
          $("#labelvehicleinport_tia").addClass('btn btn-danger btn-lg');
          $("#vehicleinPort_tia").addClass('btn btn-danger btn-lg dropdown-toggle m-r-20');
        }else {
          $("#labelvehicleinport_tia").addClass('btn btn-primary btn-lg');
          $("#vehicleinPort_tia").addClass('btn btn-primary btn-lg dropdown-toggle m-r-20');
        }

  },"json");
}

// REALTIME ALERT START
var realtimealertarray        = [];
var realtimealertarraysummary = [];
var userid                    = '<?php echo $this->sess->user_id?>';
var intervalalert2, intervalalert1, intervalalert;
var soundisactive             = 0; // VALUE DEFAULTNYA == 1. GANTI JADI 1 KALAU MAU DIBUNYIKAN
var alertloop                 = 0;

var intervalalert = setInterval(dataalert, 1000);

function dataalert(){
  // console.log("jalan");
  alertloop = alertloop + 1;
  if (realtimealertarray.length >= 1) {
    realtimealertarray = [];
  }
  var vehicle = '<?php echo json_encode($vehicledata); ?>';
  var obj     = JSON.parse(vehicle);
  // console.log("obj dataalert : ", obj);
  // console.log("alertloop : ", alertloop);
  var vehicledevice = obj[alertloop].vehicle_mv03;
  // console.log("vehicledevice : ", vehicledevice);
    // if (vehicledevice != "0000") {
      getalertnow(vehicledevice); //HIDUPKAN UNTUK MENGHIDUPKAN REALTIME ALERT
    // }
    if (alertloop == obj.length-1) {
      alertloop = 0;
    }
}

var limitalert = 10;
function getalertnow(devid){
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
                titlemarker += 'Speed : ' + rounded + ' kph </br>';
                titlemarker +=  devicestatus;
                titlemarker += 'Ritase : ' + response[0].auto_last_ritase + '</br>';
                titlemarker += '<a href="<?php echo base_url()?>maps/tracking/"' + response[0].vehicle_id + '"target="_blank">Tracking</a> </br>';
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
      $("#tableShow").hide();
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
