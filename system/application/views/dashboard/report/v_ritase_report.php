<script>
jQuery(document).ready(
		function()
		{
			jQuery("#export_xcel").click(function()
			{
				window.open('data:application/vnd.ms-excel,' + encodeURIComponent(jQuery('#isexport_xcel').html()));
			});
		}
	);

function page(p)
{
  if(p==undefined){
    p=0;
  }
  jQuery("#offset").val(p);
  jQuery("#result").html('<img src="<?php echo base_url();?>assets/transporter/images/loader2.gif">');
  jQuery("#loader").show();
  jQuery.post("<?=base_url();?>tripreport/ritase_report", jQuery("#frmsearch").serialize(),
    function(r)
    {
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
                            <header class="panel-heading panel-heading-blue">RITASE REPORT</header>
                            <div class="panel-body" id="bar-parent10">
                              <form class="block-content form" name="frmsearch" id="frmsearch" onsubmit="javascript:return frmsearch_onsubmit()">
                                <table width="100%" cellpadding="3" class="tablelist" style="font-size: 12px;">
                                  <tr>
                                    <td>Vehicle</td>
                                    <td>
                                      <select id="vehicle" name="vehicle" class="form-control select2">
                                        <?php for($i=0; $i < count($vehicle); $i++) { ?>
                                        <option value="<?php echo $vehicle[$i]->vehicle_device; ?>">
                                                      <?php echo $vehicle[$i]->vehicle_name." ".$vehicle[$i]->vehicle_no; ?>
                                                      </option>
                                        <?php } ?>
                                      </select>
                                    </td>
                                  </tr>
                                  <tr>
                                    <td>Ritase</td>
                                    <td>
                                      <select name="ritase" id="ritase" class="form-control select2">
                                        <?php
                                          if (isset($ritase) && count($ritase)>0)
                                          {
                                            for ($i=0;$i<count($ritase);$i++)
                                            {
                                        ?>
                                              <option value="<?php echo $ritase[$i]->ritase_id.",".$ritase[$i]->ritase_geofence_name;?>"><?php echo $ritase[$i]->ritase_geofence_name;?></option>
                                        <?php
                                            }
                                          }
                                        ?>
                                      </select>
                                    </td>
                                    <td></td>
                                  </tr>
                                  <tr><td>&nbsp;</td></tr>
                                  <tr id="filterdatestartend">
                                    <td width="10%">Date</td>
                                    <td>
                                      <div class="input-group date form_date col-md-6" data-date="" data-date-format="dd-mm-yyyy"  data-link-format="yyyy-mm-dd">
                                          <input class="form-control" size="5" type="text" readonly name="sdate" id="sdate" value="<?=date('d-m-Y')?>">
                                          <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                                      </div>
                                      to
                                      <div class="input-group date form_date col-md-6" data-date="" data-date-format="dd-mm-yyyy"  data-link-format="yyyy-mm-dd">
                                          <input class="form-control" size="5" type="text" readonly name="enddate" id="enddate" value="<?=date('d-m-Y')?>">
                                          <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                                      </div>
                                    </td>
                                  </tr>
                                  <tr><td>&nbsp;</td></tr>
                                  <tr>
                                    <td style="border: 0px;">&nbsp;</td>
                                    <td style="border: 0px;"><button class="btn btn-success btn-circle" id="btnsearchreport" type="submit" />Search</button>
                                    <!-- <button class="btn btn-danger btn-circle" type="button" name="excel" id="export_xcel" onclick="javascript:return excel_onsubmit()"/>Export To Excel</button> -->
                                    <img id="loader2" style="display: none;" src="<?php echo base_url();?>assets/images/ajax-loader.gif" />
                                    <!--input type="button" name="pdf" value="Export To PDF" onclick="javascript:return pdf_onsubmit()" /-->
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
