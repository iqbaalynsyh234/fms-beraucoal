<br>
<!-- <div class="row" style="margin-left:4px; margin-right:4px; margin-bottom:12px; background-color:#f2f2f2; padding-top:10px; padding-bottom:10px;"> -->

<!-- <div class="row justify-content-center" style="margin-left:4px; margin-right:4px; margin-bottom:12px; background-color:#f2f2f2; padding-top:10px; padding-bottom:10px;"> -->
<div>
  <div>
    <div class="row">
    <?php if (sizeof($data_stream_mitra) > 0) {
      for ($i=0; $i < sizeof($data_stream_mitra); $i++) {?>
        <?php if ($chanel == 1) {?>
          <div class="col-md-4">
        <?php }else { ?>
          <div class="col-md-6">
        <?php } ?>
          <br>
            <?php if ($data_stream_mitra[$i]['isonline'] == 1) {?>
              <label for=""><?php echo $data_stream_mitra[$i]['vehicle_no'].' - Online' ?></label>
              <iframe src="<?php echo $data_stream_mitra[$i]['url'] ?>" width="100%" height="400px"></iframe>
            <?php }else {?>
              <label for=""><?php echo $data_stream_mitra[$i]['vehicle_no'].' - Offline' ?></label>
              <iframe src="<?php echo $data_stream_mitra[$i]['url'] ?>" width="100%" height="400px"></iframe>
            <?php } ?>
          </div>
      <?php }
    }else {
      echo "Data is empty";
    } ?>
    </div>
  </div>
</div>
