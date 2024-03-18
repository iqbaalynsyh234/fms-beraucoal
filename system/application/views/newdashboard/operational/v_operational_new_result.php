<style media="screen">
  #report-result{
    background-color: #221f1f;
    color: white;
  }
</style>

<script src="<?php echo base_url(); ?>assets/js/jsblong/jquery.table2excel.js"></script>
<script>
  jQuery(document).ready(
    function() {
      // jQuery("#export_xcel").click(function() {
      //   window.open('data:application/vnd.ms-excel,' + encodeURIComponent(jQuery('#isexport_xcel').html()));
      // });


      //export excel
      jQuery("#export_xcel").click(function() {
        var myBlob = new Blob([$("#isexport_xcel").html()], {
          type: 'application/vnd.ms-excel'
        });
        var url = window.URL.createObjectURL(myBlob);
        var a = document.createElement("a");
        document.body.appendChild(a);
        a.href = url;
        a.download = "operational_report.xls";
        a.click();
        setTimeout(function() {
          window.URL.revokeObjectURL(url);
        }, 0);

      });
    }



  );
</script>



<div class="col-lg-6 col-sm-6">
  <input id="btn_hide_form" class="btn btn-circle btn-danger" title="" type="button" value="Hide Form" onclick="javascript:return option_form('hide')" />
  <input id="btn_show_form" class="btn btn-circle btn-success" title="" type="button" value="Show Form" onClick="javascript:return option_form('show')" style="display:none" />
</div>
<div class="col-lg-2 col-sm-2"></div>
<br />

<div class="panel" id="panel_form">
  <header class="panel-heading" id="report-result">
    Report Result
    <button type="button" name="button" id="export_xcel" class="btn btn-primary btn-sm">Export Excel</button>
  </header>
  <div class="panel-body" id="bar-parent10">
    <?php if (sizeof($data) < 1) { ?>
      <?php echo "Data is Empty" ?>
    <?php } else { ?>
      <div id="isexport_xcel" style="overflow-y:auto;">
        <table class="table table-striped table-bordered" style="font-size: 12px; overflow-y:auto;">
          <thead>
            <tr>
              <th rowspan="2" style="text-align:center; vertical-align: middle;">No</th>
              <th rowspan="2" style="text-align:center; vertical-align: middle;">Date</th>
              <th rowspan="2" style="text-align:center; vertical-align: middle;">Contractor</th>
              <th rowspan="2" style="text-align:center; vertical-align: middle;">Unit</th>
              <th rowspan="2" style="text-align:center; vertical-align: middle;">WH</th>
              <th colspan="4" style="text-align:center; vertical-align: middle;">Idle</th>
              <th rowspan="2" style="text-align:center; vertical-align: middle;">Total Idle</th>
			  <th rowspan="2" style="text-align:center; vertical-align: middle;">BD</th>
              <th rowspan="2" style="text-align:center; vertical-align: middle;">Stndby</th>

              <th rowspan="2" style="text-align:center; vertical-align: middle;">Rit</th>
              <th style="text-align:center;">Prod</th>
              <th style="text-align:center;">Dist-Odo</th>
              <th colspan="6" style="text-align:center;">
                Fuel
              </th>
			

            </tr>
            <tr>
              
              <th style="text-align:center;">ROM</th>
              <th style="text-align:center;">Port</th>
              <th style="text-align:center;">Hauling</th>
              <th style="text-align:center;">Others</th>
             
              <th style="text-align:center;">Ton</th>
              <th style="text-align:center;">Km</th>
              <th style="text-align:center;">Ltr</th>
			  
			  <!-- <th style="text-align:center;">Ltr Pagi</th>
			  <th style="text-align:center;">Time</th>
              <th style="text-align:center;">Ltr Malam</th>
			  <th style="text-align:center;">Time</th> -->
			  
			 <th style="text-align:center;">Lt/Jam</th>
             <th style="text-align:center;">Lt/Km</th>
			 
	
			 
            </tr>
          </thead>
          <tbody>
            <?php if (sizeof($data) > 0) {
              for ($i = 0; $i < sizeof($data); $i++) {
                $j = 0;
                $it_rom = 0;
                $it_port = 0;
                $it_hauling = 0;
                $it_others = 0;
                $bd = 0;
                $wh = 0;

                /* if($data[$i]['kepmen_idle_rom_time'] > 3600){
					$it_rom = number_format($data[$i]['kepmen_idle_rom_time'] / 3600) ;
				}
				if($data[$i]['kepmen_idle_port_time'] > 3600){
					$it_port = number_format($data[$i]['kepmen_idle_port_time'] / 3600) ;
				}
				if($data[$i]['kepmen_idle_hauling_time'] > 3600){
					$it_hauling = number_format($data[$i]['kepmen_idle_hauling_time'] / 3600) ;
				}
				if($data[$i]['kepmen_idle_others_time'] > 3600){
					$it_others = number_format($data[$i]['kepmen_idle_others_time'] / 3600) ;
				}
				if($data[$i]['kepmen_breakdown_time'] > 3600){
					$bd = number_format($data[$i]['kepmen_breakdown_time'] / 3600) ;
				}
				if($data[$i]['kepmen_working_time'] > 3600){
					$wh = number_format($data[$i]['kepmen_working_time'] / 3600) ;
				} */

                $it_rom = gmdate("H:i", $data[$i]['kepmen_idle_rom_time']);
                $it_port = gmdate("H:i", $data[$i]['kepmen_idle_port_time']);
                $it_hauling = gmdate("H:i", $data[$i]['kepmen_idle_hauling_time']);
                $it_others = gmdate("H:i", $data[$i]['kepmen_idle_others_time']);
                $bd = gmdate("H:i", $data[$i]['kepmen_breakdown_time']);
                $wh = gmdate("H:i", $data[$i]['kepmen_working_time']);

                $all_opr = $data[$i]['kepmen_working_time'] + $data[$i]['kepmen_idle_rom_time'] + $data[$i]['kepmen_idle_port_time'] + $data[$i]['kepmen_idle_hauling_time'] + $data[$i]['kepmen_idle_others_time'] + $data[$i]['kepmen_breakdown_time'];
				$all_it = $data[$i]['kepmen_idle_rom_time'] + $data[$i]['kepmen_idle_port_time'] + $data[$i]['kepmen_idle_hauling_time'] + $data[$i]['kepmen_idle_others_time'];
                if ($data[$i]['kepmen_standby_time'] < 0) {
                  $j_sec = 0;
                } else {
                  $j_sec = 86399 - $all_opr;
                }
				$it_total = gmdate("H:i", $all_it);
                $j = gmdate("H:i", $j_sec);
				$time_isi_pagi = "";
				$time_isi_malam = "";
				$time_isi_pagi_avg = "";
				$time_isi_malam_avg = "";
				if($data[$i]['kepmen_fuel_time_isi_pagi'] != ""){
					$time_isi_pagi = date("H:i", strtotime($data[$i]['kepmen_fuel_time_isi_pagi']));
				}
				
				if($data[$i]['kepmen_fuel_time_isi_malam'] != ""){
					$time_isi_malam = date("H:i", strtotime($data[$i]['kepmen_fuel_time_isi_malam']));
				}
				
				if($data[$i]['kepmen_fuel_time_isi_pagi_avg'] != ""){
					$time_isi_pagi_avg = date("H:i", strtotime($data[$i]['kepmen_fuel_time_isi_pagi_avg']));
				}
				
				if($data[$i]['kepmen_fuel_time_isi_malam_avg'] != ""){
					$time_isi_malam_avg = date("H:i", strtotime($data[$i]['kepmen_fuel_time_isi_malam_avg']));
				}

            ?>
                <tr>
                  <td style="text-align:center;"><?php echo $i + 1 ?></td>
                  <td style="text-align:center;"><?php echo date_format(date_create($data[$i]['kepmen_date']), "d-m-Y") ?></td>
                  <td style="text-align:center;"><?php echo $data[$i]['kepmen_company_name'] ?></td>
                  <td style="text-align:center;"><?php echo $data[$i]['kepmen_vehicle_no'] ?></td>
                  <td style="text-align:center;"><?php echo $wh; ?></td>

                  <td style="text-align:center;"><?php echo $it_rom; ?></td>
                  <td style="text-align:center;"><?php echo $it_port; ?></td>
                  <td style="text-align:center;"><?php echo $it_hauling; ?></td>
                  <td style="text-align:center;"><?php echo $it_others; ?></td>
				  <td style="text-align:center;"><?php echo $it_total; ?></td>
                  <td style="text-align:center;"><?php echo $bd; ?></td>
                  <td style="text-align:center;"><?php echo $j; ?></td>

                  <td style="text-align:center;"><?php echo $data[$i]['kepmen_total_rit'] ?></td>
                  <td style="text-align:center;"><?php echo $data[$i]['kepmen_total_ton'] ?></td>
                  <td style="text-align:center;"><?php echo number_format($data[$i]['kepmen_total_distance'] / 1000) ?></td>
				  <td style="text-align:center;"><?php echo $data[$i]['kepmen_fuel_cons'] ?></td>
				  <td style="text-align:center;"><?php echo $data[$i]['kepmen_fuel_liter_jam'] ?></td>
				  <td style="text-align:center;"><?php echo $data[$i]['kepmen_fuel_liter_km'] ?></td>

                </tr>
            <?php }
            } else {
              echo "Data is empty";
            } ?>
          </tbody>
        </table>
      <?php } ?>
      </div>
  </div>
</div>