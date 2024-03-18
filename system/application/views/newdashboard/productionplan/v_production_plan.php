<style media="screen">
#production{
  background-color: #221f1f;
  color: white;
}
</style>

<div class="sidebar-container">
  <?= $sidebar; ?>
</div>

<div class="page-content-wrapper">
  <div class="page-content">
    <br>
    <?php if ($this->session->flashdata('notif')) { ?>
      <div class="alert alert-success" id="notifnya" style="display: none;"><?php echo $this->session->flashdata('notif'); ?></div>
    <?php } ?>
    <!--<div class="alert alert-success" id="notifnya2" style="display: none;"></div>-->
    <div class="col-md-12">
      <div class="panel" id="panel_form">
        <header class="panel-heading panel-heading-red" id="production">Production Plan</header>
        <div class="panel-body" id="bar-parent10">

          <div class="row">
            <div class="col-md-2">
              <p>Select date</p>
            </div>
            <div class="col-md-1">
              <p> : </p>
            </div>
            <div class="input-group date form_date col-md-4" data-date="" data-date-format="dd-mm-yyyy" data-link-field="dtp_input2" data-link-format="yyyy-mm-dd">
              <input class="form-control" size="5" type="text" readonly name="date" id="startdate" value="" placeholder="Select date here" style="width:80%;" onchange="productionplandatechange()">
              <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
            </div>
          </div>

          <div class="row">
            <div class="col-md-12">
              <form class="block-content form" name="frmadd" id="frmadd" onsubmit="javascript: return frmadd_onsubmit()">

                <input type="hidden" name="plan_date" id="plan_date" value="">
                <input type="hidden" name="query_update" id="query_update" value="0">

                <table class="table" id="productionplan_content"></table>

                <div class="row" id="productionplan_btnsave" style="display:none;">
                  <div class="col-md-12">
                    <div class="text-right">
                      <input class="btn btn-warning" type="button" name="btncancel" id="btncancel" value=" Cancel " onclick="location='<?= base_url() ?>maps/heatmap'" />
                      <input class="btn btn-success" type="submit" name="btnsave" id="btnsave" value=" Save " />
                    </div>
                  </div>
                </div>
                <div class="row" id="loader" style="display: none;">
                  <div class="col-sm-9"></div>
                  <div class="col-sm-3">

                    <img src="<?= base_url(); ?>assets/images/ajax-loader.gif" border="0" />
                  </div>
                </div>
              </form>
            </div>
          </div>

        </div>
      </div>
    </div>
  </div>


  <script type="text/javascript" src="js/script.js"></script>
  <script src="<?php echo base_url() ?>assets/dashboard/assets/js/jquery-1.7.1.min.js" type="text/javascript"></script>

  <script type="text/javascript">
    $("#notifnya").fadeIn(1000);
    $("#notifnya").fadeOut(5000);

    function productionplandatechange() {
      var date = $("#startdate").val();
      if (date == "") {
        alert("Silahkan pilih tanggal yang diinginkan");
      } else {
        console.log("date selected : ", date);
        var data = {
          date: date
        };
        $("#productionplan_content").html("");
        jQuery.post("<?php echo base_url() ?>productionplan/searchbydate", data, function(response) {
          console.log("response : ", response);
          var data = response.data;
          var html = "";
          $("#plan_date").val(response.date);
          html += '<tr>';
          html += '<th>Hour</th>';
          html += '<th>Tonase</th>';
          html += '<th>Ritase</th>';
          html += '</tr>';
          if (data.length > 0) {
            $("#query_update").val(1);
            for (var i = 0; i < data.length; i++) {
              html += '<tr>';
              html += '<td>' + data[i].plan_name + '</td>';
              html += '<td><input type="text" class="form-control" name="plan_ton' + i + '" value="' + data[i].plan_ton + '" onchange="fieldchange()" onkeypress="return goodchars(event,\'0123456789\',this)"></td>';
              html += '<td><input type="text" class="form-control" name="plan_rit' + i + '" value="' + data[i].plan_rit + '" onchange="fieldchange()" onkeypress="return goodchars(event,\'0123456789\',this)"></td>';
              html += '<input type="hidden" name="plan_id' + i + '" value="' + data[i].plan_id + '"></tr>';
            }
          } else {
            $("#query_update").val(0);
            for (var i = 0; i < 24; i++) {
              html += '<tr>';
              if (i > 9) {
                html += '<td>' + i + '<input type="hidden" name="plan_name' + i + '" value="' + i + '"> </td>';
              } else {
                html += '<td>0' + i + '<input type="hidden" name="plan_name' + i + '" value="0' + i + '"> </td>';
              }
              html += '<td><input type="text" class="form-control" name="plan_ton' + i + '" id="" value="" onchange="fieldchange()" onkeypress="return goodchars(event,\'0123456789\',this)"></td>';
              html += '<td><input type="text" class="form-control" name="plan_rit' + i + '" id="" value="" onchange="fieldchange()" onkeypress="return goodchars(event,\'0123456789\',this)"></td>';
              html += '<input type="hidden" name="plan_value' + i + '" value="' + i + '"></tr>';
            }
          }
          $("#productionplan_content").html(html);
          // $("#productionplan_btnsave").show();
        }, "json");
      }
    }

    function frmadd_onsubmit() {
      $("#loader").show();
      // var data = JSON.stringify($("#frmadd").serializeArray());
      var data = jQuery("#frmadd").serialize();
      $("#productionplan_btnsave").hide();
      jQuery.post("<?php echo base_url(); ?>productionplan/save", data, function(response) {
        console.log("response : ", response);
        if (response.code == 200) {
          alert("Pengaturan production plan berhasil disimpan");
          data = 1;
          if (data == 1) {
            window.location = '<?php echo base_url() ?>productionplan';
          }
        }
      }, "json");
      return false;
    }

    function fieldchange() {
      $("#productionplan_btnsave").show();
      return false;
    }


    function getkey(e) {
      if (window.event)
        return window.event.keyCode;
      else if (e)
        return e.which;
      else
        return null;
    }

    function goodchars(e, goods, field) {
      var key, keychar;
      key = getkey(e);
      if (key == null) return true;

      keychar = String.fromCharCode(key);
      keychar = keychar.toLowerCase();
      goods = goods.toLowerCase();

      // check goodkeys
      if (goods.indexOf(keychar) != -1)
        return true;
      // control keys
      if (key == null || key == 0 || key == 8 || key == 9 || key == 27)
        return true;

      if (key == 13) {
        var i;
        for (i = 0; i < field.form.elements.length; i++)
          if (field == field.form.elements[i])
            break;
        i = (i + 1) % field.form.elements.length;
        field.form.elements[i].focus();
        return false;
      };
      // else return false
      return false;
    }


    // FOR DISABLE SUBMIT FORM
    $(window).keydown(function(event) {
      if (event.keyCode == 13) {
        event.preventDefault();
        return false;
      }
    });
  </script>