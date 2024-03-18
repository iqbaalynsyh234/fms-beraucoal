


							<div class="col-lg-6 col-sm-6">	
								<input id="btn_hide_form" class="btn btn-circle btn-danger" title="" type="button" value="Hide Form" onclick="javascript:return option_form('hide')" />
								<input id="btn_show_form" class="btn btn-circle btn-success" title="" type="button" value="Show Form" onClick="javascript:return option_form('show')" style="display:none"/>
							</div>
							<div class="col-lg-2 col-sm-2">	
							</div>
							<br />
							
<div class="row">
  <div class="col-md-12 col-sm-12">
    <div class="panel">

      <header class="panel-heading panel-heading-red">RESULT</header>
      <div class="panel-body" id="bar-parent10">
        <div class="row">
          <?php if (count($data) == 0) {
				echo "<p>No Data</p>";
			}else{ ?>
            <div class="col-md-12 col-sm-12">
				<table class="table table-striped custom-table table-hover">
                  <thead>
                    <tr>
                      <th style="text-align:center;" width="3%">No</th>
					  <th style="text-align:center;" width="10%">Imei</th>
					  <th style="text-align:center;" width="10%">Start Time</th>
					  <th style="text-align:center;" width="10%">End Time</th>
                      <th style="text-align:center;" width="10%">PlaybackUrl</th>
                    </tr>
                  </thead>
                  <tbody>


                    <?php
		if(isset($data) && (count($data) > 0)){
		
				for ($i=0;$i<count($data);$i++)
				{
					
					$DownTaskUrl_ex = explode("&", $data[$i]['DownTaskUrl']);
					
					
					$sdate = $DownTaskUrl_ex[2];
					$edate = $DownTaskUrl_ex[3];
					
					$stime_ex = explode("=", $sdate);
					$etime_ex = explode("=", $edate);
					
					$stime = $stime_ex[1];
					$etime = $etime_ex[1];
					
					//print_r($stime_ex);exit();
					
					?>
                   
                      <tr>
                        <td style="text-align:center;font-size:12px;">
                          <?php echo $i+1;?>
                        </td>
						<td style="text-align:center;font-size:12px;">
                         <?php echo $data[$i]['devIdno'];?>
                        </td>
						<td style="text-align:center;font-size:12px;">
							<?php echo $stime;?>
                        </td>
						<td style="text-align:center;font-size:12px;">
							<?php echo $etime;?>
                        </td>
                       
						<td style="text-align:center;font-size:12px;">
							<!--Link <a href="<?php echo $data[$i]['PlaybackUrl'];?>" target="_blank">PlaybackUrl</a>-->
							
							<a href="<?php echo $data[$i]['PlaybackUrl'].'mp4';?>" download="record.mp4">
							  <img src="<?=base_url();?>assets/images/down.png" alt="Download" width="20" height="20">
							</a>
                        </td>
						
                      </tr>
                 <?php } ?>
                 <?php }else{ ?>
                          <tr>
                            <td colspan="10">No Available Data</td>
                          </tr>
                          <?php
		}
	?>
                  </tbody>

                </table>
            </div>
            

            <?php } ?>

        </div>
      </div>
    </div>
  </div>
</div>
