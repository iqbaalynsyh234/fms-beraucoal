<br>
<!-- <div class="row" style="margin-left:4px; margin-right:4px; margin-bottom:12px; background-color:#f2f2f2; padding-top:10px; padding-bottom:10px;"> -->

<!-- <div class="row justify-content-center" style="margin-left:4px; margin-right:4px; margin-bottom:12px; background-color:#f2f2f2; padding-top:10px; padding-bottom:10px;"> -->
<div>
    <div>

        <?php
      for ($i=0; $i < sizeof($data_streaming); $i++) {?>
        <?php if ($data_streaming[$i]['isonline'] == 1) {?>
        <label for=""><?php echo $data_streaming[$i]['vehicle_no'].' - Online' ?></label>
        <iframe src="<?php echo $data_streaming[$i]['htmllivemonitoring'] ?>" width="100%" height="400px"></iframe>
        <?php }else {?>
        <label for=""><?php echo $data_streaming[$i]['vehicle_no'].' - Offline' ?></label>
        <iframe src="<?php echo $data_streaming[$i]['htmllivemonitoring'] ?>" width="100%" height="400px"></iframe>
        <?php } ?>
        <br>
        <?php } ?>
        <!-- <video controls style="padding-bottom: 2px; margin-bottom:2px; width:100%; height:auto; ">
      <source src='<?= $htmllivemonitoring; ?>' type='video/mp4'>
    </video> -->
    </div>
</div>