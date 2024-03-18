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

    <div class="page-content">
      <div class="row">
        <div style="width:100%;">
          <div style="margin-left:2%;">
            <h3>Tracking Board (Today) SIMULASI</h3>
          </div>
          <div class="col-md-12" style="margin-left:1%;">
            <button type="button" name="btncommandcenter" id="btncommandcenter" class="btn btn-circle btn-success" onclick="showmodalcommandcenter();">
              <span class="fa fa-microphone"></span>
              Command Center
            </button>

            <button type="button" name="btnstartsimulation" id="btnstartsimulation" class="btn btn-circle btn-info" onclick="startsimulation();">
              <span class="fa fa-check"></span>
              Start Simulation
            </button>
            <div id="notifsimulasi" style="display: none;">

            </div>
            <!-- <input type="text" name="commandtextfix" id="commandtextfix"> -->
             <br><br>
        </div>
        </div>
      </div>

      <div class="row" style="margin-left:1%; width:98%; background-color: white;">
        <div class="col-md-12">
          <div class="text-center">
            <h3>ROM</h3>
          </div>
        </div>
            <?php
              for ($i=0; $i < sizeof($vehicleinrom); $i++) {?>
                <div class="col-xl-2 col-md-6 col-12" style="cursor:pointer;">
                  <?php if ($vehicleinrom[$i]['jumlahinrom'] >= 6) {?>
                    <div class="info-box bg-danger">
                  <?php }else {?>
                    <div class="info-box bg-blue">
                  <?php } ?>
                    <span class="info-box-icon push-bottom">
                      <i class="material-icons">my_location</i>
                    </span>
                    <div class="info-box-content">
                      <span class="info-box-text"><?php echo $vehicleinrom[$i]['rom'] ?></span>
                        <span class="progress-description" style="font-size:15px;">
                          <?php echo $vehicleinrom[$i]['jumlahinrom'] ?>
                        </span>
                    </div>
                  </div>
                </div>
              <?php }
             ?>
      </div>
    </div>

    <div class="row" style="margin-left:1%; width:100%; background-color: white;">
      <div class="col-md-12">
        <div class="text-center">
          <h3>HAULING</h3>
        </div>
      </div>

      <div class="col-md-6" style="cursor:pointer;">
        <div class="info-box bg-success">
          <span class="info-box-icon push-bottom">
            <i class="material-icons">layers</i>
          </span>
          <div class="info-box-content">
            <span class="info-box-text">Kosongan</span>
              <span class="progress-description" style="font-size:15px;">
                <?php echo sizeof($arrayvehicleinkosongan); ?>
              </span>
          </div>
        </div>
      </div>

      <div class="col-md-6" style="cursor:pointer;">
        <div class="info-box bg-success">
          <span class="info-box-icon push-bottom">
            <i class="material-icons">layers</i>
          </span>
          <div class="info-box-content">
            <span class="info-box-text">Kosongan</span>
              <span class="progress-description" style="font-size:15px;">
                <?php echo sizeof($arrayvehicleinmuatan); ?>
              </span>
          </div>
        </div>
      </div>

    </div>


</div>







  <script type="text/javascript" src="js/script.js"></script>
  <script src="<?php echo base_url()?>assets/dashboard/assets/js/jquery-1.7.1.min.js" type="text/javascript"></script>

  <script type="text/javascript">
    var dailytarget                               = 100000;
    var totalmobil                                = 700;
    var totalpjo                                  = 10;
    //IDEAL MOBIL UNTUK TARGET
    var idealmobiluntuktarget                     = idealmobiluntuktargetpershift/totalpjo;
    var idealmobiluntuktargetnums                 = Number(idealmobiluntuktarget);
    var idealmobiluntuktargetroundstrings         = idealmobiluntuktargetnums.toFixed(0);
    var idealmobiluntuktargetfix                  = Number(idealmobiluntuktargetroundstrings);

    //TARGET PER SHIRFT
    var idealmobiluntuktargetpershift             = idealmobiluntuktargetfix/2;
    var idealmobiluntuktargetpershiftnums         = Number(idealmobiluntuktargetpershift);
    var idealmobiluntuktargetpershiftroundstrings = idealmobiluntuktargetpershiftnums.toFixed(0);
    var idealmobiluntuktargetpershiftfix          = Number(idealmobiluntuktargetpershiftroundstrings);

    //JUMLAH MOBIL PER PJO
    var jumlahperpjo = idealmobiluntuktargetpershiftfix/totalpjo;

    // PARAMETER 2
    var jumlahrom 	                  = 7;
    var maxcapinrom                   = 50;
    var jumlahidealperrom             = maxcapinrom/jumlahrom;
    var jumlahidealperromnums         = Number(jumlahidealperrom);
    var jumlahidealperromroundstrings = jumlahidealperromnums.toFixed(0);
    var jumlahidealperromfix          = Number(jumlahidealperromroundstrings);

    //LIST DATA
    var rom_list                  = '<?php echo json_encode($rom_list)?>';
    var rom_listobj               = JSON.parse(rom_list);

    //VEHICLE IN ROM
		var arrayvehicleinrom         = '<?php echo json_encode($arrayvehicleinrom)?>';
    var arrayvehicleinromobj      = JSON.parse(arrayvehicleinrom);

    //VEHICLE IN HAULING KOSONGAN
		var arrayvehicleinkosongan    = '<?php echo json_encode($arrayvehicleinkosongan)?>';
    var arrayvehicleinkosonganobj = JSON.parse(arrayvehicleinkosongan);

    function startsimulation(){
      // console.log("rom_listobj : ", rom_listobj.length);
      // console.log("arrayvehicleinromobj : ", arrayvehicleinromobj.length);
      // console.log("arrayvehicleinkosonganobj : ", arrayvehicleinkosonganobj.length);
      for (i=0; i < arrayvehicleinkosonganobj.length; i++) {
  			var tujuan                      = arrayvehicleinkosonganobj[i].tujuan;
  			var mobilname                   = arrayvehicleinkosonganobj[i].name;
        console.log(tujuan+"-"+mobilname);
  			for (j=0; j < arrayvehicleinromobj.length; j++) {
  				if (arrayvehicleinromobj[j].rom == tujuan) {
  					// echo $arrayvehicleinrom[$j]['rom'].'-'.$tujuan.' '.$arrayvehicleinrom[$j]['jumlahinrom'].'<br>';
  					jumlahcurrentinrom           = arrayvehicleinromobj[j].jumlahinrom;
  					jikaditambahsatumobil 			 = jumlahcurrentinrom+1;

  					if (jikaditambahsatumobil >= jumlahidealperromfix) {
              // console.log("jumlahcurrentinrom : ", jumlahcurrentinrom);
              // console.log("jikaditambahsatumobil : ", jikaditambahsatumobil);
              // console.log("jumlahidealperrom : ", jumlahidealperromfix);
  						// alert(tujuan + " Full. Sudah tercatat "+jumlahcurrentinrom+" Kendaraan");
              var data = {
                text : tujuan + ", Full. Sudah tercatat "+jumlahcurrentinrom+" Kendaraan"
              };
              $("#commandtextfix").val(tujuan + " Full. Sudah tercatat "+jumlahcurrentinrom+" Kendaraan");
              $.post("<?php echo base_url() ?>trackingboard/sendcommand", JSON.stringify(data), function(response){
                console.log("response : ", response);
                  $("#loadernya").hide();
                  var code   = response.code;
                  var msg    = response.msg;
                    if (code == 400) {
                      $("#notifsimulasi").html("Error");
                      $("#notifsimulasi").fadeIn(1000);
                      $("#notifsimulasi").fadeOut(3000);
                    }else {
                      $("#notifsimulasi").html("Command Berhasil Dikirim");
                      $("#notifsimulasi").fadeIn(1000);
                      $("#notifsimulasi").fadeOut(3000);
                    }
              }, "json");
  					}else {
              var data = {
                text : mobilname+". Bisa Memasuki "+tujuan + " sekarang"
              };
              $("#commandtextfix").val(tujuan + " Full. Sudah tercatat "+jumlahcurrentinrom+" Kendaraan");
              $.post("<?php echo base_url() ?>trackingboard/sendcommand", JSON.stringify(data), function(response){
                console.log("response : ", response);
                  $("#loadernya").hide();
                  var code   = response.code;
                  var msg    = response.msg;
                    if (code == 400) {
                      $("#notifsimulasi").html("Error");
                      $("#notifsimulasi").fadeIn(1000);
                      $("#notifsimulasi").fadeOut(3000);
                    }else {
                      $("#notifsimulasi").html("Command Berhasil Dikirim");
                      $("#notifsimulasi").fadeIn(1000);
                      $("#notifsimulasi").fadeOut(3000);
                    }
              }, "json");
  					}
  				}
  			}
  		}
    }

  </script>
