<!-- <a href="<?php echo $htmllivemonitoring; ?>" target="_blank">Stream Now</a> -->
<input type="text" id="urlstream" value="<?php echo $htmllivemonitoring; ?>" hidden>
<!-- <div class="btn btn-success btn-md" href="<?php echo $htmllivemonitoring; ?>" onclick="openstreamnow()">Stream Now</div> -->
<a class="btn btn-success btn-md" href="<?php echo $htmllivemonitoring; ?>" target="_blank">Stream Now</a>

<div id="result_stream" style="display:none; width:100%; height:300px;">

</div>
<!-- http://live.abditrack.com/attachmentview/livemonitoring/ -->
<script type="text/javascript">
  function openstreamnow(){
    var urlvideo = $("#urlstream").val();
    console.log("urlvideo : ", urlvideo);
    $.post("http://live.abditrack.com/attachmentview/livemonitoring", {urlvideo:urlvideo}, function(response){
      $("#result_stream").html(response.html);
      $("#result_stream").show();
    },"json");
  }
</script>
