<style media="screen">
.material-icons{
  font-size: 50px;
  padding: 10px;
}

.info-box-icon.push-bottom {
    margin-top: 8px;
}

div#modalarming{
  width: 26.6%;
  margin-top: 3%;
  z-index: 100;
  position: fixed;
  margin-left: 35%;
  display: none;
}

div#modaldisarming{
  width: 26.6%;
  margin-top: 3%;
  z-index: 100;
  position: fixed;
  margin-left: 35%;
  display: none;
}

div#modalrfidreg{
  width: 26.6%;
  margin-top: 3%;
  z-index: 100;
  position: fixed;
  margin-left: 35%;
  display: none;
}

div#modalrfidcheck{
  width: 26.6%;
  margin-top: 3%;
  z-index: 100;
  position: fixed;
  margin-left: 35%;
  display: none;
}

div#modalspeedsetting{
  width: 26.6%;
  margin-top: 3%;
  z-index: 100;
  position: fixed;
  margin-left: 35%;
  display: none;
}

div#modalsnap{
  width: 26.6%;
  margin-top: 3%;
  z-index: 100;
  position: fixed;
  margin-left: 35%;
  display: none;
}

div#modalodometer{
  width: 26.6%;
  margin-top: 3%;
  z-index: 100;
  position: fixed;
  margin-left: 35%;
  display: none;
}

div#modalreboot{
  width: 26.6%;
  margin-top: 3%;
  z-index: 100;
  position: fixed;
  margin-left: 35%;
  display: none;
}

div#modalintervalonoff{
  width: 26.6%;
  margin-top: 3%;
  z-index: 100;
  position: fixed;
  margin-left: 35%;
  display: none;
}

div#modalpositioning{
  width: 26.6%;
  margin-top: 3%;
  z-index: 100;
  position: fixed;
  margin-left: 35%;
  display: none;
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
    <br>
    <?php if ($this->session->flashdata('notif')) {?>
      <div class="alert alert-success" id="notifnya" style="display: none;"><?php echo $this->session->flashdata('notif');?></div>
    <?php }?>
      <div class="alert alert-success" id="notifnya2" style="display: none;"></div>
            <div class="row">
			        <div class="col-xl-3 col-md-6 col-12" style="cursor:pointer;" onclick="armingfeature();">
			          <div class="info-box bg-success">
			            <span class="info-box-icon push-bottom"><i class="material-icons">alarm_on</i></span>
			            <div class="info-box-content">
			              <span class="info-box-text">ARMING</span>
			              <span class="info-box-number" id="totalarmingdevice" style="font-size:12px;">&nbsp</span>
			              <!-- <div class="progress">
			                <div class="progress-bar width-60"></div>
			              </div> -->
  		              <span class="progress-description" style="font-size:12px;">
                      Turn On Alarm
                    </span>
			            </div>
			          </div>
			        </div>
			        <div class="col-xl-3 col-md-6 col-12" style="cursor:pointer;" onclick="disarmingfeature();">
			          <div class="info-box bg-danger">
			            <span class="info-box-icon push-bottom"><i class="material-icons">alarm_off</i></span>
			            <div class="info-box-content">
			              <span class="info-box-text">DISARMING</span>
			              <span class="info-box-number" id="totaldisarmingdevice" style="font-size:12px;">&nbsp</span>
			              <!-- <div class="progress">
			                <div class="progress-bar width-40"></div>
			              </div> -->
			              <span class="progress-description" style="font-size:12px;">
	                    Turn Off Alarm
	                  </span>
			            </div>
			          </div>
			        </div>
			        <div class="col-xl-3 col-md-6 col-12" style="cursor:pointer;" onclick="rfidregfeature();">
			          <div class="info-box bg-blue">
			            <span class="info-box-icon push-bottom"><i class="material-icons">add_box</i></span>
			            <div class="info-box-content">
			              <span class="info-box-text">RFID REGISTER</span>
			              <span class="info-box-number" id="totalrfidregdevice" style="font-size:12px;">&nbsp</span>
			              <!-- <div class="progress">
			                <div class="progress-bar width-80"></div>
			              </div> -->
			              <span class="progress-description" style="font-size:12px;">
	                     Register your RFID
	                  </span>
			            </div>
			          </div>
			        </div>
			        <div class="col-xl-3 col-md-6 col-12" style="cursor:pointer;" onclick="rfidcheckfeature();">
			          <div class="info-box bg-warning">
			            <span class="info-box-icon push-bottom"><i class="material-icons">view_list</i></span>
			            <div class="info-box-content">
			              <span class="info-box-text">RFID CHECK</span>
			              <span class="info-box-number" style="font-size:12px;">&nbsp</span>
			              <!-- <div class="progress">
			                <div class="progress-bar width-60"></div>
			              </div> -->
			              <span class="progress-description" style="font-size:12px;">
	                    Check your RFID
	                  </span>
			            </div>
			          </div>
			        </div>
              <div class="col-xl-3 col-md-6 col-12" style="cursor:pointer;" onclick="speedsettingfeature();">
			          <div class="info-box bg-orange">
			            <span class="info-box-icon push-bottom"><i class="material-icons">warning</i></span>
			            <div class="info-box-content">
			              <span class="info-box-text">SPEED SETTING</span>
			              <span class="info-box-number" style="font-size:12px;">&nbsp</span>
			              <!-- <div class="progress">
			                <div class="progress-bar width-80"></div>
			              </div> -->
			              <span class="progress-description" style="font-size:12px;">
	                    Device limit setting
	                  </span>
			            </div>
			          </div>
			        </div>
              <div class="col-xl-3 col-md-6 col-12" style="cursor:pointer;" onclick="snapfeature();">
			          <div class="info-box bg-blue">
			            <span class="info-box-icon push-bottom"><i class="material-icons">photo_camera</i></span>
			            <div class="info-box-content">
			              <span class="info-box-text">SNAP</span>
			              <span class="info-box-number" style="font-size:12px;">&nbsp</span>
			              <!-- <div class="progress">
			                <div class="progress-bar width-60"></div>
			              </div> -->
			              <span class="progress-description" style="font-size:12px;">
	                    Image Capture
	                  </span>
			            </div>
			          </div>
			        </div>
              <div class="col-xl-3 col-md-6 col-12" style="cursor:pointer;" onclick="odometerfeature();">
			          <div class="info-box bg-success">
			            <span class="info-box-icon push-bottom"><i class="material-icons">directions_car</i></span>
			            <div class="info-box-content">
			              <span class="info-box-text">Odometer</span>
			              <span class="info-box-number" style="font-size:12px;">&nbsp</span>
			              <!-- <div class="progress">
			                <div class="progress-bar width-60"></div>
			              </div> -->
			              <span class="progress-description" style="font-size:12px;">
	                    Odometer Setting
	                  </span>
			            </div>
			          </div>
			        </div>
              <div class="col-xl-3 col-md-6 col-12" style="cursor:pointer;" onclick="intervalonofffeature();">
			          <div class="info-box bg-blue">
			            <span class="info-box-icon push-bottom"><i class="material-icons">schedule</i></span>
			            <div class="info-box-content">
			              <span class="info-box-text">Interval</span>
			              <span class="info-box-number" style="font-size:12px;">On / Off</span>
			              <!-- <div class="progress">
			                <div class="progress-bar width-60"></div>
			              </div> -->
			              <span class="progress-description" style="font-size:12px;">
	                    Setting
	                  </span>
			            </div>
			          </div>
			        </div>
              <div class="col-xl-3 col-md-6 col-12" style="cursor:pointer;" onclick="positioningfeature();">
			          <div class="info-box bg-success">
			            <span class="info-box-icon push-bottom"><i class="material-icons">place</i></span>
			            <div class="info-box-content">
			              <span class="info-box-text">Positioning</span>
			              <span class="info-box-number" style="font-size:12px;">&nbsp</span>
			              <!-- <div class="progress">
			                <div class="progress-bar width-60"></div>
			              </div> -->
			              <span class="progress-description" style="font-size:12px;">
	                    Last Position
	                  </span>
			            </div>
			          </div>
			        </div>
              <div class="col-xl-3 col-md-6 col-12" style="cursor:pointer;" onclick="rebootfeature();">
			          <div class="info-box bg-danger">
			            <span class="info-box-icon push-bottom"><i class="material-icons">refresh</i></span>
			            <div class="info-box-content">
			              <span class="info-box-text">REBOOT</span>
			              <span class="info-box-number" style="font-size:12px;">&nbsp</span>
			              <!-- <div class="progress">
			                <div class="progress-bar width-60"></div>
			              </div> -->
			              <span class="progress-description" style="font-size:12px;">
	                    Reboot your device
	                  </span>
			            </div>
			          </div>
			        </div>
			      </div>
          </div>
  </div>

<!-- MODAL ARMING -->
  <div id="modalarming" style="display: none;">
    <div id="modaldetailpopup1"></div>
    <div class="row">
      <div class="col-md-12">
        <div class="card card-topline-yellow">
          <div class="card-head">
            <header id="headerarming"></header>
            <div class="tools">
              <a class="fa fa-repeat btn-color box-refresh" href="javascript:;"></a>
              <a class="t-collapse btn-color fa fa-chevron-down" href="javascript:;"></a>
              <button type="button" class="btn btn-danger" name="button" onclick="closemodalarming();">X</button>
            </div>
          </div>
          <div class="card-body" id="detailpopupbody">
            <form class="form-horiontal" id="formarming" action="javascript:armingsubmit()">
              <table class="table">
                <tr>
                  <td>Vehicle</td>
                  <td>:</td>
                  <td>
                    <select class="select2" name="armingvehicle" id="armingvehicle" onchange="armingonchange();">
                      <option value="">Choose Vehicle</option>
                      <?php foreach ($vehicle as $rowvehicle) {?>
                        <option value="<?php echo $rowvehicle['vehicle_device'].'.'.$rowvehicle['vehicle_type'] ?>"><?php echo $rowvehicle['vehicle_no'].' '.$rowvehicle['vehicle_name'] ?></option>
                      <?php } ?>
                    </select>
                    <span id="armingnotif" style="display:none; color:red; font-size:10px;"></span>
                  </td>
                </tr>
              </table>
              <div class="text-right">
                <button type="submit" class="btn btn-success" id="btnarmingsave" disabled>Arming This Device</button>
                <img id="loaderarming" src="<?=base_url();?>assets/images/ajax-loader.gif" border="0" alt="" title="" style="display:none;">
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

<!-- MODAL DISARMING -->
  <div id="modaldisarming" style="display: none;">
    <div id="modaldetailpopup1"></div>
    <div class="row">
      <div class="col-md-12">
        <div class="card card-topline-yellow">
          <div class="card-head">
            <header id="headerdisarming"></header>
            <div class="tools">
              <a class="fa fa-repeat btn-color box-refresh" href="javascript:;"></a>
              <a class="t-collapse btn-color fa fa-chevron-down" href="javascript:;"></a>
              <button type="button" class="btn btn-danger" name="button" onclick="closemodaldisarming();">X</button>
            </div>
          </div>
          <div class="card-body" id="detailpopupbody">
            <form class="form-horiontal" id="formdisarming" action="javascript:disarmingsubmit()">
              <table class="table">
                <tr>
                  <td>Vehicle</td>
                  <td>:</td>
                  <td>
                    <select class="select2" name="disarmingvehicle" id="disarmingvehicle" onchange="disarmingonchange();">
                      <option value="">Choose Vehicle</option>
                      <?php foreach ($vehicle as $rowvehicle) {?>
                        <option value="<?php echo $rowvehicle['vehicle_device'].'.'.$rowvehicle['vehicle_type'] ?>"><?php echo $rowvehicle['vehicle_no'].' '.$rowvehicle['vehicle_name'] ?></option>
                      <?php } ?>
                    </select>
                    <span id="disarmingnotif" style="display:none; color:red; font-size:10px;"></span>
                  </td>
                </tr>
              </table>
              <div class="text-right">
                <button type="submit" class="btn btn-success" id="btndisarmingsave" disabled>Disarming This Device</button>
                <img id="loaderdisarming" src="<?=base_url();?>assets/images/ajax-loader.gif" border="0" alt="" title="" style="display:none;">
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

<!-- MODAL RFID REGISTER -->
  <div id="modalrfidreg" style="display: none;">
    <div id="modaldetailpopup1"></div>
    <div class="row">
      <div class="col-md-12">
        <div class="card card-topline-yellow">
          <div class="card-head">
            <header id="headerrfidreg"></header>
            <div class="tools">
              <a class="fa fa-repeat btn-color box-refresh" href="javascript:;"></a>
              <a class="t-collapse btn-color fa fa-chevron-down" href="javascript:;"></a>
              <button type="button" class="btn btn-danger" name="button" onclick="closemodalrfidreg();">X</button>
            </div>
          </div>
          <div class="card-body" id="detailpopupbody">
            <form class="form-horiontal" id="formrfidreg" action="javascript:rfidregonsubmit()">
              <table class="table">
                <tr>
                  <td>Vehicle</td>
                  <td>:</td>
                  <td>
                    <select class="select2" name="rfidregvehicle" id="rfidregvehicle" onchange="rfidregonchange();">
                      <option value="">Choose Vehicle</option>
                      <?php foreach ($vehicle as $rowvehicle) {?>
                        <option value="<?php echo $rowvehicle['vehicle_device'].'.'.$rowvehicle['vehicle_type'] ?>"><?php echo $rowvehicle['vehicle_no'].' '.$rowvehicle['vehicle_name'] ?></option>
                      <?php } ?>
                    </select>
                    <span id="rfidregnotif" style="display:none; color:red; font-size:10px;"></span>
                  </td>
                </tr>
                <tr>
                  <td>RFID</td>
                  <td>:</td>
                  <td>
                    <input type="text" name="rfidnumber" id="rfidnumber" class="form-control" placeholder="Separated By Comma">
                  </td>
                </tr>
              </table>
              <div class="text-right">
                <button type="submit" class="btn btn-success" id="btnrfidregsave" disabled>Register</button>
                <img id="loaderrfidreg" src="<?=base_url();?>assets/images/ajax-loader.gif" border="0" alt="" title="" style="display:none;">
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- MODAL RFID CHECK -->
  <div id="modalrfidcheck" style="display: none;">
    <div id="modaldetailpopup1"></div>
    <div class="row">
      <div class="col-md-12">
        <div class="card card-topline-yellow">
          <div class="card-head">
            <header id="headerrfidcheck"></header>
            <div class="tools">
              <a class="fa fa-repeat btn-color box-refresh" href="javascript:;"></a>
              <a class="t-collapse btn-color fa fa-chevron-down" href="javascript:;"></a>
              <button type="button" class="btn btn-danger" name="button" onclick="closemodalrfidcheck();">X</button>
            </div>
          </div>
          <div class="card-body" id="detailpopupbody">
            <form class="form-horiontal" id="formrfidcheck" action="javascript:rfidcheckonsubmit()">
              <table class="table">
                <tr>
                  <td>Vehicle</td>
                  <td>:</td>
                  <td>
                    <select class="select2" name="rfidcheckvehicle" id="rfidcheckvehicle" onchange="rfidcheckonchange();">
                      <option value="">Choose Vehicle</option>
                      <?php foreach ($vehicle as $rowvehicle) {?>
                        <option value="<?php echo $rowvehicle['vehicle_device'].'.'.$rowvehicle['vehicle_type'] ?>"><?php echo $rowvehicle['vehicle_no'].' '.$rowvehicle['vehicle_name'] ?></option>
                      <?php } ?>
                    </select>
                    <span id="rfidchecknotif" style="display:none; color:red; font-size:10px;"></span>
                  </td>
                </tr>
              </table>
              <div class="text-right">
                <button type="submit" class="btn btn-success" id="btnrfidchecksave" disabled>Check</button>
                <img id="loaderrfidcheck" src="<?=base_url();?>assets/images/ajax-loader.gif" border="0" alt="" title="" style="display:none;">
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- MODAL SPEED SETTING -->
  <div id="modalspeedsetting" style="display: none;">
    <div id="modaldetailpopup1"></div>
    <div class="row">
      <div class="col-md-12">
        <div class="card card-topline-yellow">
          <div class="card-head">
            <header id="headerspeedsetting"></header>
            <div class="tools">
              <a class="fa fa-repeat btn-color box-refresh" href="javascript:;"></a>
              <a class="t-collapse btn-color fa fa-chevron-down" href="javascript:;"></a>
              <button type="button" class="btn btn-danger" name="button" onclick="closemodalspeedsetting();">X</button>
            </div>
          </div>
          <div class="card-body" id="detailpopupbody">
            <form class="form-horiontal" id="formspeedsetting" action="javascript:speedsettingonsubmit()">
              <table class="table">
                <tr>
                  <td>Vehicle</td>
                  <td>:</td>
                  <td>
                    <select class="select2" name="speedsettingvehicle" id="speedsettingvehicle" onchange="speedsettingonchange();">
                      <option value="">Choose Vehicle</option>
                      <?php foreach ($vehicle as $rowvehicle) {?>
                        <option value="<?php echo $rowvehicle['vehicle_device'].'.'.$rowvehicle['vehicle_type'] ?>"><?php echo $rowvehicle['vehicle_no'].' '.$rowvehicle['vehicle_name'] ?></option>
                      <?php } ?>
                    </select>
                    <span id="speedsettingnotif" style="display:none; color:red; font-size:10px;"></span>
                  </td>
                </tr>
                <tr>
                  <td>Second</td>
                  <td>:</td>
                  <td>
                    <input type="text" name="speedsettingsecond" id="speedsettingsecond" class="form-control" placeholder="60, 70, 80 Kph">
                  </td>
                </tr>
              </table>
              <div class="text-right">
                <button type="submit" class="btn btn-success" id="btnspeedsettingsave" disabled>Set Speed</button>
                <img id="loaderspeedsetting" src="<?=base_url();?>assets/images/ajax-loader.gif" border="0" alt="" title="" style="display:none;">
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- MODAL SNAP SETTING -->
  <div id="modalsnap" style="display: none;">
    <div id="modaldetailpopup1"></div>
    <div class="row">
      <div class="col-md-12">
        <div class="card card-topline-yellow">
          <div class="card-head">
            <header id="headersnap"></header>
            <div class="tools">
              <a class="fa fa-repeat btn-color box-refresh" href="javascript:;"></a>
              <a class="t-collapse btn-color fa fa-chevron-down" href="javascript:;"></a>
              <button type="button" class="btn btn-danger" name="button" onclick="closemodalsnap();">X</button>
            </div>
          </div>
          <div class="card-body" id="detailpopupbody">
            <form class="form-horiontal" id="formsnap" action="javascript:snaponsubmit()">
              <table class="table">
                <tr>
                  <td>Vehicle</td>
                  <td>:</td>
                  <td>
                    <select class="select2" name="snapvehicle" id="snapvehicle" onchange="snaponchange();">
                      <option value="">Choose Vehicle</option>
                      <?php foreach ($vehicle as $rowvehicle) {?>
                        <option value="<?php echo $rowvehicle['vehicle_device'].'.'.$rowvehicle['vehicle_type'] ?>"><?php echo $rowvehicle['vehicle_no'].' '.$rowvehicle['vehicle_name'] ?></option>
                      <?php } ?>
                    </select>
                    <span id="snapnotif" style="display:none; color:red; font-size:10px;"></span>
                  </td>
                </tr>
              </table>
              <div class="text-right">
                <button type="submit" class="btn btn-success" id="btnsnapsave" disabled>Save</button>
                <img id="loadersnap" src="<?=base_url();?>assets/images/ajax-loader.gif" border="0" alt="" title="" style="display:none;">
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- MODAL ODOMETER SETTING -->
  <div id="modalodometer" style="display: none;">
    <div id="modaldetailpopup1"></div>
    <div class="row">
      <div class="col-md-12">
        <div class="card card-topline-yellow">
          <div class="card-head">
            <header id="headerodometer"></header>
            <div class="tools">
              <a class="fa fa-repeat btn-color box-refresh" href="javascript:;"></a>
              <a class="t-collapse btn-color fa fa-chevron-down" href="javascript:;"></a>
              <button type="button" class="btn btn-danger" name="button" onclick="closemodalodometer();">X</button>
            </div>
          </div>
          <div class="card-body" id="detailpopupbody">
            <form class="form-horiontal" id="formodometer" action="javascript:odometeronsubmit()">
              <table class="table">
                <tr>
                  <td>Vehicle</td>
                  <td>:</td>
                  <td>
                    <select class="select2" name="odometervehicle" id="odometervehicle" onchange="odometeronchange();">
                      <option value="">Choose Vehicle</option>
                      <?php foreach ($vehicle as $rowvehicle) {?>
                        <option value="<?php echo $rowvehicle['vehicle_device'].'.'.$rowvehicle['vehicle_type'] ?>"><?php echo $rowvehicle['vehicle_no'].' '.$rowvehicle['vehicle_name'] ?></option>
                      <?php } ?>
                    </select>
                    <span id="odometernotif" style="display:none; color:red; font-size:10px;"></span>
                  </td>
                </tr>
                <tr>
                  <td>Odometer</td>
                  <td>:</td>
                  <td>
                    <input type="number" name="odometervalue" class="form-control">
                  </td>
                </tr>
              </table>
              <div class="text-right">
                <button type="submit" class="btn btn-success" id="btnodometersave" disabled>Save</button>
                <img id="loaderodometer" src="<?=base_url();?>assets/images/ajax-loader.gif" border="0" alt="" title="" style="display:none;">
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- MODAL REBOOT SETTING -->
  <div id="modalreboot" style="display: none;">
    <div id="modaldetailpopup1"></div>
    <div class="row">
      <div class="col-md-12">
        <div class="card card-topline-yellow">
          <div class="card-head">
            <header id="headerreboot"></header>
            <div class="tools">
              <a class="fa fa-repeat btn-color box-refresh" href="javascript:;"></a>
              <a class="t-collapse btn-color fa fa-chevron-down" href="javascript:;"></a>
              <button type="button" class="btn btn-danger" name="button" onclick="closemodalreboot();">X</button>
            </div>
          </div>
          <div class="card-body" id="detailpopupbody">
            <form class="form-horiontal" id="formreboot" action="javascript:rebootonsubmit()">
              <table class="table">
                <tr>
                  <td>Vehicle</td>
                  <td>:</td>
                  <td>
                    <select class="select2" name="rebootvehicle" id="rebootvehicle" onchange="rebootonchange();">
                      <option value="">Choose Vehicle</option>
                      <?php foreach ($vehicle as $rowvehicle) {?>
                        <option value="<?php echo $rowvehicle['vehicle_device'].'.'.$rowvehicle['vehicle_type'] ?>"><?php echo $rowvehicle['vehicle_no'].' '.$rowvehicle['vehicle_name'] ?></option>
                      <?php } ?>
                    </select>
                    <span id="rebootnotif" style="display:none; color:red; font-size:10px;"></span>
                  </td>
                </tr>
              </table>
              <div class="text-right">
                <button type="submit" class="btn btn-success" id="btnrebootsave" disabled>Save</button>
                <img id="loaderreboot" src="<?=base_url();?>assets/images/ajax-loader.gif" border="0" alt="" title="" style="display:none;">
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- MODAL INTERVAL SETTING -->
  <div id="modalintervalonoff" style="display: none;">
    <div id="modalintervalonoff"></div>
    <div class="row">
      <div class="col-md-12">
        <div class="card card-topline-yellow">
          <div class="card-head">
            <header id="headerintervalonoff"></header>
            <div class="tools">
              <a class="fa fa-repeat btn-color box-refresh" href="javascript:;"></a>
              <a class="t-collapse btn-color fa fa-chevron-down" href="javascript:;"></a>
              <button type="button" class="btn btn-danger" name="button" onclick="closemodalintervalonoff();">X</button>
            </div>
          </div>
          <div class="card-body" id="detailpopupbody">
            <form class="form-horiontal" id="formintervalonoff" action="javascript:intervalonoffonsubmit()">
              <table class="table">
                <tr>
                  <td>Vehicle</td>
                  <td>:</td>
                  <td>
                    <select class="select2" name="intervalonoffvehicle" id="intervalonoffvehicle" onchange="intervalonoffonchange();">
                      <option value="">Choose Vehicle</option>
                      <?php foreach ($vehicle as $rowvehicle) {?>
                        <option value="<?php echo $rowvehicle['vehicle_device'].'.'.$rowvehicle['vehicle_type'] ?>"><?php echo $rowvehicle['vehicle_no'].' '.$rowvehicle['vehicle_name'] ?></option>
                      <?php } ?>
                    </select>
                    <span id="intervalonoffnotif" style="display:none; color:red; font-size:10px;"></span>
                  </td>
                </tr>

                <tr>
                  <td>Interval Type</td>
                  <td>:</td>
                  <td>
                    <select class="select2" name="intervalonofftype" id="intervalonofftype">
                      <option value="">Interval Type</option>
                      <option value="INTVON">On</option>
                      <option value="INTVOFF">Off</option>
                    </select>
                  </td>
                </tr>

                <tr>
                  <td>Interval Value</td>
                  <td>:</td>
                  <td>
                    <select class="select2" name="intervalonoffvalue" id="intervalonoffvalue">
                      <option value="30">30 Second</option>
                      <option value="60">1 Minute</option>
                      <option value="120">2 Minute</option>
                      <option value="180">3 Minute</option>
                      <option value="240">4 Minute</option>
                      <option value="300">5 Minute</option>
                    </select>
                  </td>
                </tr>
              </table>
              <div class="text-right">
                <button type="submit" class="btn btn-success" id="btnintervalonoffsave" disabled>Save</button>
                <img id="loaderintervalonoff" src="<?=base_url();?>assets/images/ajax-loader.gif" border="0" alt="" title="" style="display:none;">
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- MODAL POSITIONING -->
  <div id="modalpositioning" style="display: none;">
    <div id="modalinpositioning"></div>
    <div class="row">
      <div class="col-md-12">
        <div class="card card-topline-yellow">
          <div class="card-head">
            <header id="headerpositioning"></header>
            <div class="tools">
              <a class="fa fa-repeat btn-color box-refresh" href="javascript:;"></a>
              <a class="t-collapse btn-color fa fa-chevron-down" href="javascript:;"></a>
              <button type="button" class="btn btn-danger" name="button" onclick="closemodalpositioning();">X</button>
            </div>
          </div>
          <div class="card-body" id="detailpopupbody">
            <form class="form-horiontal" id="formpositioning" action="javascript:positioningonsubmit()">
              <table class="table">
                <tr>
                  <td>Vehicle</td>
                  <td>:</td>
                  <td>
                    <select class="select2" name="positioningvehicle" id="positioningvehicle" onchange="positioningonchange();">
                      <option value="">Choose Vehicle</option>
                      <?php foreach ($vehicle as $rowvehicle) {?>
                        <option value="<?php echo $rowvehicle['vehicle_device'].'.'.$rowvehicle['vehicle_type'] ?>"><?php echo $rowvehicle['vehicle_no'].' '.$rowvehicle['vehicle_name'] ?></option>
                      <?php } ?>
                    </select>
                    <span id="positioningotif" style="display:none; color:red; font-size:10px;"></span>
                  </td>
                </tr>
              </table>
              <div class="text-right">
                <button type="submit" class="btn btn-success" id="btnpositioningsave" disabled>Save</button>
                <img id="loaderintervalonoff" src="<?=base_url();?>assets/images/ajax-loader.gif" border="0" alt="" title="" style="display:none;">
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>




<script type="text/javascript">
  var vehicle_type     = ["GT08S", "GT08SRFID", "GT08SRFIDDOOR", "GT08SFAN", "GT08SPTO",
                          "GT08SDOOR", "GT08", "GT08PTO", "GT08FAN", "GT08CAM", "GT08DOOR",
                          "GT08CAMDOOR", "LT02", "LT03", "TK510", "TK510FAN", "TK510PTO",
                          "TK510DOOR", "TK510CAM", "TK510CAMDOOR"]; // INI UNTUK SEMUA

  var vehicletype_rfid = ["GT08SRFID"]; // UNTUK FITURE RFID REGISTER & RFID CHECKING
  var vehicletype_snap = ["GT08CAMDOOR", "TK510CAM", "TK510CAMDOOR", "GT08CAM"]; // UNTUK SNAP

// ARMING
  function armingfeature(){
    $("#armingvehicle").val("");
    $("#armingnotif").hide();
    $("#headerarming").html("Arming Device");
    $("#modalarming").fadeIn(1000);
  }

  function closemodalarming(){
    $("#modalarming").fadeOut(1000);
  }

  function armingonchange(){
    var thisarming    = $("#armingvehicle").val();
    var splitarming   = thisarming.split(".");
    var thisarmingfix = splitarming[1];
    // alert(splitarming);
    if (vehicle_type.includes(thisarmingfix)) {
      $("#btnarmingsave").prop('disabled', false);
    }else {
      $("#armingnotif").html("This feature is not support for your device");
      $("#armingnotif").show();
      $("#btnarmingsave").prop('disabled', true);
    }
  }

  function armingsubmit(){
    $("#loaderarming").show();
    $.post("<?php echo base_url() ?>device/armingsave", $("#formarming").serialize(), function(response){
      $("#loaderarming").hide();
      console.log("response : ", response);
      var status = response.msg;
        if (status == "success") {
          if (confirm("Success Arming this device")) {
            window.location = '<?php echo base_url() ?>device';
          }
        }else {
          if (confirm("Failed Arming this device")) {
            window.location = '<?php echo base_url() ?>device';
          }
        }
    },"json");
  }

//DISARMING
function disarmingfeature(){
  $("#disarmingvehicle").val("");
  $("#disarmingnotif").hide();
  $("#headerdisarming").html("Disarming Device");
  $("#modaldisarming").fadeIn(1000);
}

function closemodaldisarming(){
  $("#modaldisarming").fadeOut(1000);
}

function disarmingonchange(){
  var dissarming     = $("#disarmingvehicle").val();
  var splitdisarming = dissarming.split(".");
  var disarmingfix   = splitdisarming[1];
  // alert(splitarming);
  if (vehicle_type.includes(disarmingfix)) {
    $("#btndisarmingsave").prop('disabled', false);
  }else {
    $("#disarmingnotif").html("This feature is not support for your device");
    $("#disarmingnotif").show();
    $("#btndisarmingsave").prop('disabled', true);
  }
}

function disarmingsubmit(){
  $("#loaderdisarming").show();
  $.post("<?php echo base_url() ?>device/disarmingsave", $("#formdisarming").serialize(), function(response){
    $("#loaderdisarming").hide();
    console.log("response : ", response);
    var status = response.msg;
      if (status == "success") {
        if (confirm("Success Disarming device")) {
          window.location = '<?php echo base_url() ?>device';
        }
      }else {
        if (confirm("Failed Disarming device")) {
          window.location = '<?php echo base_url() ?>device';
        }
      }
  },"json");
}

// RFID REGISTER
function rfidregfeature(){
  $("#rfidregvehicle").val("");
  $("#rfidregnotif").hide();
  $("#headerrfidreg").html("RFID Register");
  $("#modalrfidreg").fadeIn(1000);
}

function closemodalrfidreg(){
  $("#modalrfidreg").fadeOut(1000);
}

function rfidregonchange(){
  var rfidregvehicle      = $("#rfidregvehicle").val();
  var splitrfidregvehicle = rfidregvehicle.split(".");
  var rfidregvehiclefix   = splitrfidregvehicle[1];
  // alert(splitarming);
  if (vehicletype_rfid.includes(rfidregvehiclefix)) {
    $("#btnrfidregsave").prop('disabled', false);
  }else {
    $("#rfidregnotif").html("This feature is not support for your device");
    $("#rfidregnotif").show();
    $("#btnrfidregsave").prop('disabled', true);
  }
}

function rfidregonsubmit(){
  $("#loaderrfidreg").show();
  $.post("<?php echo base_url() ?>device/rfidregister", $("#formrfidreg").serialize(), function(response){
    $("#loaderrfidreg").hide();
    console.log("response : ", response);
    var status = response.msg;
      if (status == "success") {
        if (confirm("Success Register device")) {
          window.location = '<?php echo base_url() ?>device';
        }
      }else {
        if (confirm("Failed Register device")) {
          window.location = '<?php echo base_url() ?>device';
        }
      }
  },"json");
}

// RFID CHECK
function rfidcheckfeature(){
  $("#rfidcheckvehicle").val("");
  $("#rfidchecknotif").hide();
  $("#headerrfidcheck").html("RFID Check");
  $("#modalrfidcheck").fadeIn(1000);
}

function closemodalrfidcheck(){
  $("#modalrfidcheck").fadeOut(1000);
}

function rfidcheckonchange(){
  var rfidcheckvehicle      = $("#rfidcheckvehicle").val();
  var splitrfidcheckvehicle = rfidcheckvehicle.split(".");
  var rfidcheckvehiclefix   = splitrfidcheckvehicle[1];
  // alert(splitarming);
  if (vehicletype_rfid.includes(rfidcheckvehiclefix)) {
    $("#btnrfidchecksave").prop('disabled', false);
  }else {
    $("#rfidchecknotif").html("This feature is not support for your device");
    $("#rfidchecknotif").show();
    $("#btnrfidchecksave").prop('disabled', true);
  }
}

function rfidcheckonsubmit(){
  $("#loaderrfidcheck").show();
  $.post("<?php echo base_url() ?>device/rfidcheck", $("#formrfidcheck").serialize(), function(response){
    $("#loaderrfidcheck").hide();
    console.log("response : ", response);
    var status = response.msg;
      if (status == "success") {
        if (confirm("Checking Device Success")) {
          window.location = '<?php echo base_url() ?>device';
        }
      }else {
        if (confirm("Checking Device Failed")) {
          window.location = '<?php echo base_url() ?>device';
        }
      }
  },"json");
}

// SPEED SETTING
function speedsettingfeature(){
  $("#speedsettingvehicle").val("");
  $("#speedsettingnotif").hide();
  $("#headerspeedsetting").html("Speed Setting");
  $("#modalspeedsetting").fadeIn(1000);
}

function closemodalspeedsetting(){
  $("#modalspeedsetting").fadeOut(1000);
}

function speedsettingonchange(){
  var speedsettingvehicle = $("#speedsettingvehicle").val();
  var splitsettingvehicle = speedsettingvehicle.split(".");
  var settingvehiclefix   = splitsettingvehicle[1];
  // console.log("settingvehiclefix : ", settingvehiclefix);
  if (vehicle_type.includes(settingvehiclefix)) {
    $("#btnspeedsettingsave").prop('disabled', false);
  }else {
    $("#speedsettingnotif").html("This feature is not support for your device");
    $("#speedsettingnotif").show();
    $("#btnspeedsettingsave").prop('disabled', true);
  }
}

function speedsettingonsubmit(){
  $("#loaderspeedsetting").show();
  $.post("<?php echo base_url() ?>device/speedsetting", $("#formspeedsetting").serialize(), function(response){
    $("#loaderspeedsetting").hide();
    console.log("response : ", response);
    var status = response.msg;
      if (status == "success") {
        if (confirm("Speed Setting Success")) {
          window.location = '<?php echo base_url() ?>device';
        }
      }else {
        if (confirm("Speed Setting Failed")) {
          window.location = '<?php echo base_url() ?>device';
        }
      }
  },"json");
}

// SNAP SETTING
function snapfeature(){
  $("#snapvehicle").val("");
  $("#snapnotif").hide();
  $("#headersnap").html("Snap Setting");
  $("#modalsnap").fadeIn(1000);
}

function closemodalsnap(){
  $("#modalsnap").fadeOut(1000);
}

function snaponchange(){
  var snapvehicle      = $("#snapvehicle").val();
  var splitsnapvehicle = snapvehicle.split(".");
  var snapvehiclefix   = splitsnapvehicle[1];
  // console.log("settingvehiclefix : ", settingvehiclefix);
  if (vehicletype_snap.includes(snapvehiclefix)) {
    $("#btnsnapsave").prop('disabled', false);
  }else {
    $("#snapnotif").html("This feature is not support for your device");
    $("#snapnotif").show();
    $("#btnsnapsave").prop('disabled', true);
  }
}

function snaponsubmit(){
  $("#loadersnap").show();
  $.post("<?php echo base_url() ?>device/snap", $("#formsnap").serialize(), function(response){
    $("#loadersnap").hide();
    console.log("response : ", response);
    var status = response.msg;
      if (status == "success") {
        if (confirm("Snap Setting Success")) {
          window.location = '<?php echo base_url() ?>device';
        }
      }else {
        if (confirm("Snap Setting Failed")) {
          window.location = '<?php echo base_url() ?>device';
        }
      }
  },"json");
}

// ODOMETER SETTING
function odometerfeature(){
  $("#odometervehicle").val("");
  $("#odometernotif").hide();
  $("#headerodometer").html("Odometer Setting");
  $("#modalodometer").fadeIn(1000);
}

function closemodalodometer(){
  $("#modalodometer").fadeOut(1000);
}

function odometeronchange(){
  var odometervehicle      = $("#odometervehicle").val();
  var splitodometervehicle = odometervehicle.split(".");
  var odometervehiclefix   = splitodometervehicle[1];
  // console.log("settingvehiclefix : ", settingvehiclefix);
  if (vehicle_type.includes(odometervehiclefix)) {
    $("#btnodometersave").prop('disabled', false);
  }else {
    $("#odometernotif").html("This feature is not support for your device");
    $("#odometernotif").show();
    $("#btnodometersave").prop('disabled', true);
  }
}

function odometeronsubmit(){
  $("#loaderodometer").show();
  $.post("<?php echo base_url() ?>device/odometer", $("#formodometer").serialize(), function(response){
    $("#loaderodometer").hide();
    console.log("response : ", response);
    var status = response.msg;
      if (status == "success") {
        if (confirm("Odometer Setting Success")) {
          window.location = '<?php echo base_url() ?>device';
        }
      }else {
        if (confirm("Odometer Setting Failed")) {
          window.location = '<?php echo base_url() ?>device';
        }
      }
  },"json");
}

// REBOOT SETTING
function rebootfeature(){
  $("#rebootvehicle").val("");
  $("#rebootnotif").hide();
  $("#headerreboot").html("Reboot Device");
  $("#modalreboot").fadeIn(1000);
}

function closemodalreboot(){
  $("#modalreboot").fadeOut(1000);
}

function rebootonchange(){
  var rebootvehicle      = $("#rebootvehicle").val();
  var splitrebootvehicle = rebootvehicle.split(".");
  var rebootvehiclefix   = splitrebootvehicle[1];
  // console.log("settingvehiclefix : ", settingvehiclefix);
  if (vehicle_type.includes(rebootvehiclefix)) {
    $("#btnrebootsave").prop('disabled', false);
  }else {
    $("#rebootnotif").html("This feature is not support for your device");
    $("#rebootnotif").show();
    $("#btnrebootsave").prop('disabled', true);
  }
}

function rebootonsubmit(){
  $("#loaderreboot").show();
  $.post("<?php echo base_url() ?>device/reboot", $("#formreboot").serialize(), function(response){
    $("#loaderreboot").hide();
    console.log("response : ", response);
    var status = response.msg;
      if (status == "success") {
        if (confirm("Reboot Success")) {
          window.location = '<?php echo base_url() ?>device';
        }
      }else {
        if (confirm("Reboot Failed")) {
          window.location = '<?php echo base_url() ?>device';
        }
      }
  },"json");
}

// INTERVAL ON OFF SETTING
function intervalonofffeature(){
  $("#intervalonoffvehicle").val("");
  $("#intervalonoffnotif").hide();
  $("#headerintervalonoff").html("Setting Interval");
  $("#modalintervalonoff").fadeIn(1000);
}

function closemodalintervalonoff(){
  $("#modalintervalonoff").fadeOut(1000);
}

function intervalonoffonchange(){
  var intervalonoffvehicle      = $("#intervalonoffvehicle").val();
  var splitintervalonoffvehicle = intervalonoffvehicle.split(".");
  var intervalonoffvehiclefix   = splitintervalonoffvehicle[1];
  // console.log("settingvehiclefix : ", settingvehiclefix);
  if (vehicle_type.includes(intervalonoffvehiclefix)) {
    $("#btnintervalonoffsave").prop('disabled', false);
  }else {
    $("#intervalonoffnotif").html("This feature is not support for your device");
    $("#intervalonoffnotif").show();
    $("#btnintervalonoffsave").prop('disabled', true);
  }
}

function intervalonoffonsubmit(){
  $("#loaderintervalonoff").show();
  $.post("<?php echo base_url() ?>device/intervalonoff", $("#formintervalonoff").serialize(), function(response){
    $("#loaderintervalonoff").hide();
    console.log("response : ", response);
    var status = response.msg;
      if (status == "success") {
        if (confirm("Interval Setting Success")) {
          window.location = '<?php echo base_url() ?>device';
        }
      }else {
        if (confirm("Interval Setting Failed")) {
          window.location = '<?php echo base_url() ?>device';
        }
      }
  },"json");
}

// POSITIONING
function positioningfeature(){
  $("#positioningvehicle").val("");
  $("#positioningnotif").hide();
  $("#headerpositioning").html("Device Positioning");
  $("#modalpositioning").fadeIn(1000);
}

function closemodalpositioning(){
  $("#modalpositioning").fadeOut(1000);
}

function positioningonchange(){
  var positioningvehicle      = $("#positioningvehicle").val();
  var splitpositioningvehicle = positioningvehicle.split(".");
  var positioningvehiclefix   = splitpositioningvehicle[1];
  // console.log("settingvehiclefix : ", settingvehiclefix);
  if (vehicle_type.includes(positioningvehiclefix)) {
    $("#btnpositioningsave").prop('disabled', false);
  }else {
    $("#positioningnotif").html("This feature is not support for your device");
    $("#positioningnotif").show();
    $("#btnpositioningsave").prop('disabled', true);
  }
}

function positioningonsubmit(){
  $("#loaderpositioning").show();
  $.post("<?php echo base_url() ?>device/positioning", $("#formpositioning").serialize(), function(response){
    $("#loaderpositioning").hide();
    console.log("response : ", response);
    var status = response.msg;
      if (status == "success") {
        if (confirm("Positioning Success")) {
          window.location = '<?php echo base_url() ?>device';
        }
      }else {
        if (confirm("Positioning Failed")) {
          window.location = '<?php echo base_url() ?>device';
        }
      }
  },"json");
}
</script>
