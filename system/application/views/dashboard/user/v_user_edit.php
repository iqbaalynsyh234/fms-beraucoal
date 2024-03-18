<div class="sidebar-container">
  <?=$sidebar;?>
</div>

<div class="page-content-wrapper">
  <div class="page-content" id="page-content-new">
    <div class="row">
      <div class="col-md-12 col-sm-12" id="reportfilter">
        <div class="panel" id="panel_form">
          <header class="panel-heading panel-heading-blue">Job Order</header>
          <div class="panel-body" id="bar-parent10">
            <div class="formjoborder">
              <div class="card">
                <div class="card-body">
                  <h3>Form Job Order</h3>
                  <form class="form-horizontal" name="frmjoborder" id="frmjoborder" onsubmit="frmjoborder_onsubmit();">
                    <div class="form-group row" id="mn_vehicle">
                      <label class="col-lg-3 col-md-3 control-label">Vehicle</label>
                      <div class="col-lg-4 col-md-4">
                        <select id="vehicle" name="vehicle" class="select2">
                          <option value="all">--All Vehicle--</option>
                          <?php for ($i=0; $i < sizeof($vehicle); $i++) {?>
                            <option value="<?php echo $vehicle[$i]['vehicle_device']?>"><?php echo $vehicle[$i]['vehicle_no'].' '.$vehicle[$i]['vehicle_name']?></option>
                          <?php } ?>
                        </select>
                      </div>
                    </div>

                    <div class="form-group row" id="mn_vehicle">
                      <label class="col-lg-3 col-md-3 control-label">Order Date Time</label>
                      <div class="col-lg-4 col-md-4">
                        <div class="input-group date form_date col-md-12" data-date="" data-date-format="dd-mm-yyyy" data-link-field="dtp_input2" data-link-format="yyyy-mm-dd">
                          <input class="form-control" size="5" type="text" readonly name="orderdate" id="orderdate" value="<?=date('d-m-Y')?>">
                          <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                        </div>
                        <input type="hidden" id="dtp_input2" value=""/>
                        <div class="input-group date form_time col-md-8" data-date="" data-date-format="hh:ii" data-link-field="dtp_input3" data-link-format="hh:ii" onclick="houronclick();">
                          <input class="form-control" size="5" type="text" readonly id="ordertime" name="ordertime" value="<?=date("H:i",strtotime("00:00:00"))?>">
                          <span class="input-group-addon"><span class="fa fa-clock-o"></span></span>
                        </div>
                        <input type="hidden" id="dtp_input3" value=""/>
                      </div>
                    </div>

                    <div class="form-group row" id="mn_vehicle">
                      <label class="col-lg-3 col-md-3 control-label">ROM</label>
                      <div class="col-lg-4 col-md-4">
                        <select id="romorder" name="romorder" class="select2">
                          <option value="all">--ROM--</option>
                          <?php for ($i=0; $i < sizeof($rom); $i++) {?>
                            <option value="<?php echo $rom[$i]?>"><?php echo $rom[$i]?></option>
                          <?php } ?>
                        </select>
                      </div>
                    </div>

                    <div class="form-group row" id="mn_vehicle">
                      <label class="col-lg-3 col-md-3 control-label">ETA To ROM</label>
                      <div class="col-lg-4 col-md-4">
                        <div class="input-group date form_time col-md-6" data-date="" data-date-format="hh:ii" data-link-field="dtp_input4" data-link-format="hh:ii" onclick="houronclick();">
                          <input class="form-control" size="5" type="text" readonly id="etatorom" name="etatorom" value="<?=date("H:i",strtotime("00:00:00"))?>">
                          <span class="input-group-addon"><span class="fa fa-clock-o" onclick="houronclick();"></span></span>
                        </div>
                        <input type="hidden" id="dtp_input4" value=""/>
                      </div>
                    </div>

                    <div class="form-group row" id="mn_vehicle">
                      <label class="col-lg-3 col-md-3 control-label">PORT</label>
                      <div class="col-lg-4 col-md-4">
                        <select id="portorder" name="portorder" class="select2">
                          <option value="all">--PORT--</option>
                          <?php for ($i=0; $i < sizeof($port); $i++) {?>
                            <option value="<?php echo $port[$i]?>"><?php echo $port[$i]?></option>
                          <?php } ?>
                        </select>
                      </div>
                    </div>

                    <div class="form-group row" id="mn_vehicle">
                      <label class="col-lg-3 col-md-3 control-label">ETA To PORT</label>
                      <div class="col-lg-4 col-md-4">
                        <div class="input-group date form_time col-md-6" data-date="" data-date-format="hh:ii" data-link-field="dtp_input4" data-link-format="hh:ii" onclick="houronclick();">
                          <input class="form-control" size="5" type="text" readonly id="etatoport" name="etatoport" value="<?=date("H:i",strtotime("00:00:00"))?>">
                          <span class="input-group-addon"><span class="fa fa-clock-o" onclick="houronclick();"></span></span>
                        </div>
                        <input type="hidden" id="dtp_input4" value=""/>
                      </div>
                    </div>
                  </form>
                    <div class="col-md-8">
                      <div class="text-right">
                        <button class="btn btn-circle btn-warning" id="btncancel" type="button" onclick="cancelformjoborder();"/>Cancel</button>
                        <button class="btn btn-circle btn-success" id="btnsavejoborder" type="button" onclick="frmjoborder_onsubmit();"/>Save</button>
                        <img src="<?php echo base_url();?>assets/transporter/images/loader2.gif" style="display: none;" id="loadernya">
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
