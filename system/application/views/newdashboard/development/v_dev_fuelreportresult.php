<script src="<?php echo base_url();?>assets/js/jsblong/jquery.table2excel.js"></script>
<script>
jQuery(document).ready(
		function()
		{
			jQuery("#export_xcel").click(function()
			{
        var myBlob = new Blob([jQuery('#isexport_xcel').html()], {
            type: 'application/vnd.ms-excel'
        });
        var url    = window.URL.createObjectURL(myBlob);
        var a      = document.createElement("a");
        document.body.appendChild(a);
        a.href     = url;
        a.download = "Fuel Report.xls";
        a.click();
			});
		}
	);
</script>

					<div class="row">
					<?php if (sizeof($data) == 0) {?>
							<p>Data is empty</p>
					<?php }else{ ?>
						<div class="col-md-12 col-sm-12">

							<div class="col-lg-4 col-sm-4">
								<a href="javascript:void(0);" id="export_xcel" type="button" class="btn btn-md btn-primary">Export Excel</a>
							</div>

							<div id="isexport_xcel">
                <!-- <h5>
                  <b>
                    <?php echo "TITLE"; ?>
                  </b>
                </h5> -->
                <table width="100%" class="table table-striped" style="font-size:11px;">
                  <thead>
                    <tr>
                      <th>No.</th>
                      <th>Date</th>
                      <th>Time</th>
                      <th>Position</th>
                      <th>Fuel</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php for ($i=0; $i < sizeof($data); $i++) {?>
                        <tr>
                          <td><?php echo $i+1; ?></td>
                          <td><?php echo $data[$i]['gps_date']; ?></td>
                          <td><?php echo $data[$i]['gps_time']; ?></td>
                          <td>
                            <?php
                              $positionfix = $data[$i]['position'];
                                if ($positionfix == "") {?>
                                  <?php $positionfix = $data[$i]['gps_latitude_real'].','.$data[$i]['gps_longitude_real'];?>
                                <?php }
                            ?>
                            <a href="https://maps.google.com/?q=<?php echo $data[$i]['gps_latitude_real'].','.$data[$i]['gps_longitude_real'] ?>"><?php echo $positionfix ?></a>
                          </td>
                          <td><?php echo $data[$i]['gps_mvd']; ?></td>
                        </tr>
                      <?php } ?>
                  </tbody>
                </table>
							</div>
						</div>
					<?php } ?>

					</div>
