<script>
function page(p)
{
  if(p==undefined){
    p=0;
  }
  jQuery("#offset").val(p);
  jQuery("#result").html('<img src="<?php echo base_url();?>assets/transporter/images/loader2.gif">');
  jQuery("#loader").show();
  jQuery.post("<?=base_url();?>devicealert/searchreport", jQuery("#frmsearch").serialize(),
    function(r)
    {
      console.log("response : ", r);
      jQuery("#loader").hide();
      jQuery("#result").html(r.html);
    }
    , "json"
  );
}

function frmsearch_onsubmit()
{
  jQuery("#loader").show();
  page(0);
  return false;
}
</script>
  <div class="sidebar-container">
    <?=$sidebar;?>
  </div>
<div class="page-content-wrapper">
  <div class="page-content">
  <div class="row">
		<div class="col-md-12 col-sm-12">
      <div class="panel" id="panel_form">
        <header class="panel-heading panel-heading-blue">Overspeed Device</header>
          <div class="panel-body" id="bar-parent10">
            <form class="block-content form" name="frmsearch" id="frmsearch" onsubmit="javascript:return frmsearch_onsubmit()">
              <table width="100%" cellpadding="3" class="table" style="font-size: 12px;">
                <tr>
                  <td>Vehicle</td>
                  <td>
                    <div class="input-group  col-md-6">
                      <select id="vehicle" name="vehicle" class="form-control select2">
                        <!-- <option value="All">All</option> -->
                        <?php for($i=0; $i < count($vehicle); $i++) { ?>
                          <option value="<?php echo $vehicle[$i]->vehicle_device; ?>">
                            <?php echo $vehicle[$i]->vehicle_no." ".$vehicle[$i]->vehicle_name; //.' '.$vehicle[$i]->vehicle_device; ?>
                          </option>
                        <?php } ?>
                      </select>
                    </div>
                  </td>
                </tr>

                <tr>
                  <td>Jalur</td>
                  <td>
                    <div class="input-group  col-md-6">
                      <select id="jalurmuatankosongan" name="jalurmuatankosongan" class="form-control select2">
                        <option value="All">All</option>
                        <option value="kosongan">Kosongan</option>
                        <option value="muatan">Muatan</option>
                      </select>
                    </div>
                  </td>
                </tr>

                <tr>
                  <td>Geofence</td>
                  <td>
                    <div class="input-group  col-md-6">
                      <select id="geofencefilter" name="geofencefilter" class="form-control select2">
                        <option value="All">All</option>
                        <?php
                        foreach ($geofence as $rowgeofence) {?>
                          <option value="<?php echo $rowgeofence['geofence_name'] ?>"><?php echo $rowgeofence['geofence_name'] ?></option>
                        <?php } ?>
                      </select>
                    </div>
                  </td>
                </tr>

                <tr id="filterdatestartend">
                  <td width="10%">Date</td>
                  <td>
                    <div class="input-group date form_date col-md-6" data-date="" data-date-format="dd-mm-yyyy"  data-link-format="yyyy-mm-dd">
                        <input class="form-control" size="5" type="text" readonly name="sdate" id="sdate" value="<?=date('d-m-Y')?>">
                        <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                    </div>
                    <div class="input-group date form_time col-md-2" data-date="" data-date-format="hh:ii" data-link-field="dtp_input3" data-link-format="hh:ii">
                      <input class="form-control" size="5" type="text" readonly id="shour" name="shour" value="<?=date(" H:i ",strtotime("00:00:00 "))?>" onclick="houronclick();">
                      <span class="input-group-addon"><span class="fa fa-clock-o"></span></span>
                    </div>
                    to
                    <div class="input-group date form_date col-md-6" data-date="" data-date-format="dd-mm-yyyy"  data-link-format="yyyy-mm-dd">
                        <input class="form-control" size="5" type="text" readonly name="enddate" id="enddate" value="<?=date('d-m-Y')?>">
                        <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                    </div>
                    <div class="input-group date form_time col-md-2" data-date="" data-date-format="hh:ii" data-link-field="dtp_input3" data-link-format="hh:ii">
                      <input class="form-control" size="5" type="text" readonly id="ehour" name="ehour" value="<?=date(" H:i ",strtotime("23:59:00 "))?>" onclick="houronclick();">
                      <span class="input-group-addon"><span class="fa fa-clock-o"></span></span>
                    </div>
                  </td>
                </tr>
                <tr>
                  <td></td>
                  <td>
                    <button class="btn btn-success btn-circle" id="btnsearchreport" type="submit" />Search</button>
                    <img id="loader2" style="display: none;" src="<?php echo base_url();?>assets/images/ajax-loader.gif" />
                  </td>
                </tr>
              </table>
            </form>
          </div>
			</div>
    </div>
  </div>
  <div id="loader2" class="mdl-progress mdl-js-progress mdl-progress__indeterminate"></div>
		<div id="result"></div>
  </div>
</div>

<script type="text/javascript">
  function houronclick() {
    $(".switch").html("<?php echo date("Y F d ")?>");
  }
</script>
