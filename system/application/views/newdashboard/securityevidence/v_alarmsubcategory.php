  <div class="form-group row">
      <label class="col-lg-3 col-md-4 control-label">Alarm Sub Category</label>
      <div class="col-lg-4 col-md-4">
          <select id="alarmsubcategory" name="alarmsubcategory" class="form-control select2" onchange="getalarmchild();">
            <option value="All">All</option>
              <?php for ($i=0; $i < sizeof($alarmsubcategory); $i++) {?>
                <option value="<?php echo $alarmsubcategory[$i]['webtracking_alarmsubcategory_id'] ?>"><?php echo $alarmsubcategory[$i]['webtracking_alarmsubcategory_name'] ?></option>
              <?php } ?>
          </select>
      </div>
  </div>
